import { __ } from '@wordpress/i18n';
import database from '../services/database';
import { notify } from '../services/notifications';
import { getPosObject, getTaxType, isTaxEnabled } from '../services/runtime';
import { getFirstCartScopedRecord } from '../services/cart-session';
import { hasEntries, normalizeRecordId, parseWholeNumber, toDecimal } from '../utils/value';
import { COUPON } from '../state/action-types';

const getCouponTable = () => database.table('coupon');
const getPriceDecimalCount = () => getPosObject().price_num_decimals || 0;
const getTaxRoundAtSubtotal = () => getPosObject().tax_round_at_subtotal || '';
const convertRound = (number, decimalPlaces = getPriceDecimalCount()) => {
    number = parseFloat(number);
    const factor = Math.pow(10, decimalPlaces);
    const epsilon = 1 / (factor * 1e9);

    return Math.round((number + epsilon) * factor) / factor;
};
const getPrimaryCartRecord = cartRecords => Array.isArray(cartRecords) && cartRecords.length ? cartRecords[0] : null;
const getPrimaryCouponList = couponRecords => {
    const couponRecord = Array.isArray(couponRecords) && couponRecords.length ? couponRecords[0] : null;

    return couponRecord && Array.isArray(couponRecord.coupon) ? couponRecord.coupon : [];
};
const getActiveCartProducts = cartRecords => {
    const activeCartRecord = getPrimaryCartRecord(cartRecords);

    return activeCartRecord && Array.isArray(activeCartRecord.cart) ? activeCartRecord.cart : [];
};
const sumProductQuantities = cartProducts => (
    cartProducts.reduce((total, cartProduct) => total + parseWholeNumber(cartProduct.quantity), 0)
);
const getTotalCartQuantity = cartProducts => (
    cartProducts.length > 1 ? sumProductQuantities(cartProducts) : cartProducts.length
);
const getCartScopedCoupons = (coupons, activeCartId) => {
    const couponRecord = getFirstCartScopedRecord(coupons, activeCartId);

    return couponRecord ? [couponRecord] : [];
};
const getCartScopedFees = (fees, activeCartId) => {
    const feeRecord = getFirstCartScopedRecord(fees, activeCartId);

    return feeRecord && Array.isArray(feeRecord.fees) ? feeRecord.fees : [];
};
const getStateCollections = state => ({
    taxes: Array.isArray(state.taxes) ? state.taxes : [],
    coupons: Array.isArray(state.coupon) ? state.coupon : [],
    fees: Array.isArray(state.fees) ? state.fees : [],
    products: state.products && Array.isArray(state.products.list) ? state.products.list : [],
});
const getStateDefaultCustomer = state => (state && state.customers && state.customers.defaultCustomer) ? state.customers.defaultCustomer : {};
const getStateCustomerEmail = state => getStateDefaultCustomer(state).email || '';
const resolveCartSourceProducts = (dbcart, state) => (
    Array.isArray(dbcart) && dbcart.length ? dbcart : (state && state.cart && Array.isArray(state.cart.list) ? state.cart.list : [])
);
const calculateCartSubtotal = cartProducts => cartProducts.reduce((sum, product) => (
    toDecimal(sum) + toDecimal(product.uf_total)
), 0);
const calculateCartProductTaxTotal = cartProducts => cartProducts.reduce((sum, product) => {
    if (getTaxRoundAtSubtotal() === 'yes') {
        return toDecimal(sum) + toDecimal(product.tax);
    }

    return convertRound(sum) + convertRound(product.tax);
}, 0);
const calculateCartFeesTotal = cartFees => cartFees.reduce((sum, fee) => toDecimal(sum) + toDecimal(fee.amount), 0);
const createEmptyRestrictionEvaluation = () => ({
    restrictionCheck: false,
    calculatedAmount: 0,
});
const buildCartTotals = (cartSubtotal, taxTotal, totalDiscount, cartTotal) => ({
    cart_subtotal: convertRound(cartSubtotal),
    tax_total: convertRound(taxTotal),
    total_discount: convertRound(totalDiscount),
    cart_total: convertRound(cartTotal),
});
const calcExclusiveTax = (price, rates) => {
    let taxes = 0;

    if (rates.length) {
        rates.forEach(rate => {
            if ('yes' !== rate.compound) {
                taxes += price * (rate.rate / 100);
            }
        });

        let preCompoundTotal = taxes;

        rates.forEach(rate => {
            if ('no' !== rate.compound) {
                const priceWithTax = price + preCompoundTotal;
                taxes += priceWithTax * (rate.rate / 100);
                preCompoundTotal = taxes;
            }
        });
    }

    return taxes;
};
const calculateExclusiveTaxWithFilters = (amount, taxes, couponAmount, product, coupon, posCart) => {
    if (!isTaxEnabled() || getTaxType() === 'yes') {
        return 0;
    }

    const baseAmount = amount;
    const taxAmount = calcExclusiveTax(baseAmount, taxes);

    return taxAmount;
};
const calculateTax = (taxes, stateDiscount, stateCoupon, cartSubtotal, posCart) => {
    let totalTax = 0;
    let productTotal = 0;
    const totalCartQuantity = getTotalCartQuantity(posCart);
    const posCoupon = getPrimaryCouponList(stateCoupon);

    posCart.forEach(product => {
        if (posCoupon.length > 0) {
            productTotal += product.uf_total;

            posCoupon.forEach(value => {
                if (value.price !== undefined) {
                    if (value.type === 'percent') {
                        productTotal = 0;
                        totalTax = 0;

                        const couponAmount = cartSubtotal * (value.price / 100);
                        const discountedProductPrice = cartSubtotal - couponAmount;
                        productTotal += discountedProductPrice;
                        totalTax += calculateExclusiveTaxWithFilters(discountedProductPrice, taxes, couponAmount, product, value, posCart);
                    } else if (value.type === 'fixed_cart') {
                        let couponAmount = value.price;

                        if (totalCartQuantity > 1) {
                            couponAmount = value.price / totalCartQuantity * product.quantity;
                        }

                        productTotal -= couponAmount;
                        totalTax = calculateExclusiveTaxWithFilters(productTotal, taxes, couponAmount, product, value, posCart);
                    } else if (value.type === 'fixed_product') {
                        let couponAmount = value.price;

                        if (totalCartQuantity > 1) {
                            couponAmount = value.price / totalCartQuantity * product.quantity;
                        }

                        const discountedProductPrice = product.uf_total - couponAmount;
                        productTotal += discountedProductPrice;
                        totalTax += calculateExclusiveTaxWithFilters(discountedProductPrice, taxes, couponAmount, product, value, posCart);
                    }
                }
            });
        }
    });

    return {
        totalTax: totalTax < 0 ? 0 : totalTax,
        product_total: productTotal < 0 ? 0 : productTotal,
    };
};
const toRestrictionAmount = amount => {
    const numericAmount = parseFloat(amount);

    return Number.isNaN(numericAmount) ? 0 : numericAmount;
};
const isCouponAllowedForCustomerAndAmount = (restrictions, state, cartSubtotal) => {
    const emailRestrictions = Array.isArray(restrictions.email_restrictions) ? restrictions.email_restrictions : [];
    const minimumAmount = toRestrictionAmount(restrictions.minimum_amount);
    const maximumAmount = toRestrictionAmount(restrictions.maximum_amount);

    if (emailRestrictions.length > 0 && !emailRestrictions.includes(getStateCustomerEmail(state))) {
        return false;
    }

    if (minimumAmount > 0 && minimumAmount > cartSubtotal) {
        return false;
    }

    if (maximumAmount > 0 && maximumAmount < cartSubtotal) {
        return false;
    }

    return true;
};
const getCartProductCategories = (productId, products) => {
    const matchedProduct = products.find(product => String(product.product_id) === String(productId));

    return matchedProduct && Array.isArray(matchedProduct.categories) ? matchedProduct.categories : [];
};
const couponHasPrice = coupon => coupon.price !== undefined;
const getCouponItemAmount = (coupon, amountBase) => {
    if (!couponHasPrice(coupon)) {
        return 0;
    }

    if (coupon.type === 'percent') {
        return parseFloat((coupon.price * amountBase) / 100);
    }

    return parseFloat(coupon.price);
};
const evaluateRestrictedCouponAmount = (coupon, cartProducts, catalogProducts, cartSubtotal) => {
    let restrictionCheck = false;
    let calculatedAmount = 0;
    const restrictions = coupon.restrictions || {};
    const includedProducts = Array.isArray(restrictions.product_ids) ? restrictions.product_ids : [];
    const excludedProducts = Array.isArray(restrictions.excluded_product_ids) ? restrictions.excluded_product_ids : [];
    const includedCategories = Array.isArray(restrictions.product_categories) ? restrictions.product_categories : [];
    const excludedCategories = Array.isArray(restrictions.excluded_product_categories) ? restrictions.excluded_product_categories : [];

    for (let i = 0; i < cartProducts.length; i++) {
        const cartProduct = cartProducts[i];
        let oneTime = true;

        if (restrictions.exclude_sale_items && cartProduct.onsale) {
            restrictionCheck = true;
            oneTime = false;
            calculatedAmount = 0;
            continue;
        }

        if (includedProducts.length > 0) {
            restrictionCheck = true;

            includedProducts.forEach(productId => {
                if (String(productId) === String(cartProduct.product_id)) {
                    calculatedAmount += getCouponItemAmount(coupon, cartSubtotal);
                    oneTime = false;
                }
            });
        } else if (excludedProducts.length > 0) {
            restrictionCheck = true;

            excludedProducts.forEach(productId => {
                if (String(productId) === String(cartProduct.product_id)) {
                    calculatedAmount = 0;
                    oneTime = false;
                }
            });
        } else if (includedCategories.length > 0 || excludedCategories.length > 0) {
            const productCategories = getCartProductCategories(cartProduct.product_id, catalogProducts);
            restrictionCheck = true;

            includedCategories.forEach(categoryId => {
                productCategories.forEach(productCategoryId => {
                    if (String(categoryId) === String(productCategoryId) && oneTime && couponHasPrice(coupon)) {
                        if (coupon.type === 'percent') {
                            oneTime = false;
                            calculatedAmount += parseFloat((coupon.price * cartProduct.uf_total) / 100);
                        } else {
                            oneTime = false;
                            calculatedAmount = parseFloat(coupon.price);
                        }
                    }
                });
            });

            excludedCategories.forEach(categoryId => {
                productCategories.forEach(productCategoryId => {
                    if (String(categoryId) === String(productCategoryId)) {
                        oneTime = false;
                        calculatedAmount = false;
                    }
                });
            });
        }
    }

    return {
        restrictionCheck,
        calculatedAmount,
    };
};
const addGeneralCouponAmount = (coupon, posCart, totalCartQuantity, cartSubtotal) => {
    if (!couponHasPrice(coupon)) {
        return 0;
    }

    if (coupon.type === 'percent') {
        return parseFloat((coupon.price * cartSubtotal) / 100);
    }

    return posCart.reduce((sum, product) => (
        sum + convertRound(coupon.price / totalCartQuantity * product.quantity)
    ), 0);
};
const removeRestrictedCoupons = activeCartId => (
    getCouponTable().where('cart_id').equals(normalizeRecordId(activeCartId)).delete()
);
const getCouponAmount = (coupons, posCart, activeCartId, cartSubtotal, posProducts, dispatch, state) => {
    let couponAmount = 0;
    const totalCartQuantity = getTotalCartQuantity(posCart);
    const couponRecord = Array.isArray(coupons) && coupons.length ? coupons[0] : null;

    if (!couponRecord || String(couponRecord.cart_id) !== String(activeCartId)) {
        return couponAmount;
    }

    const posCoupon = getPrimaryCouponList(coupons);

    posCoupon.forEach(value => {
        if (!value.restrictions) {
            return;
        }

        let allowedForOtherRestrictions = isCouponAllowedForCustomerAndAmount(value.restrictions, state, cartSubtotal);

        if (value.restrictions.individual_use && posCoupon.length > 1) {
            allowedForOtherRestrictions = false;
        }

        const { restrictionCheck, calculatedAmount } = allowedForOtherRestrictions
            ? evaluateRestrictedCouponAmount(value, posCart, posProducts, cartSubtotal)
            : createEmptyRestrictionEvaluation();

        if (!allowedForOtherRestrictions || (restrictionCheck && calculatedAmount === 0)) {
            removeRestrictedCoupons(activeCartId);
            dispatch({ type: COUPON, coupon: [] });
            notify({
                title: __('Coupon Error', 'devdiggers-multipos-for-woocommerce'),
                message: __('This coupon is not applicable as per restrictions.', 'devdiggers-multipos-for-woocommerce'),
                type: 'danger',
            });
        } else {
            couponAmount += addGeneralCouponAmount(value, posCart, totalCartQuantity, cartSubtotal);
        }

        couponAmount += calculatedAmount;
    });

    return couponAmount;
};

export const getEmptyCartTotal = () => ({
    cart_subtotal: 0,
    tax_total: 0,
    total_discount: 0,
    cart_total: 0,
});

export const getCartTotal = (dbcart = '', state, dispatch) => {
    const cartProducts = resolveCartSourceProducts(dbcart, state);
    const {
        taxes: stateTax,
        coupons: stateCoupon,
        fees: stateFees,
        products: stateProducts,
    } = getStateCollections(state);
    const activeCartRecord = getPrimaryCartRecord(cartProducts);

    if (!activeCartRecord || !Array.isArray(activeCartRecord.cart) || !activeCartRecord.cart.length) {
        const total = getEmptyCartTotal();

        return total;
    }

    const activeCartId = activeCartRecord.id;
    const cartCoupons = getCartScopedCoupons(stateCoupon, activeCartId);
    const cartFees = getCartScopedFees(stateFees, activeCartId);
    let cartTotal = 0;
    let taxTotal = 0;
    let totalDiscount = 0;
    let totalFees = 0;
    let couponAmount = 0;
    let finalAmount = 0;

    if (cartProducts.length) {
        const posCart = getActiveCartProducts(cartProducts);
        const cartSubtotal = calculateCartSubtotal(posCart);
        const taxAmount = calculateCartProductTaxTotal(posCart);

        if (cartCoupons.length > 0) {
            couponAmount = getCouponAmount(cartCoupons, posCart, activeCartId, cartSubtotal, stateProducts, dispatch, state);
        }

        finalAmount = couponAmount > 0 && cartSubtotal > 0 ? cartSubtotal - couponAmount : cartSubtotal;
        taxTotal = parseFloat(taxAmount);

        if (couponAmount && getPrimaryCouponList(cartCoupons).length) {
            const result = calculateTax(stateTax, [], cartCoupons, cartSubtotal, posCart);
            taxTotal = result.totalTax;
            finalAmount = result.product_total;
        }

        cartTotal = finalAmount + taxTotal;

        if (finalAmount < 0) {
            cartTotal = 0;
            totalDiscount = 0;
            taxTotal = parseFloat(taxAmount);
        }

        if (cartFees.length) {
            totalFees = calculateCartFeesTotal(cartFees);
            cartTotal += totalFees;
        }

        const total = buildCartTotals(cartSubtotal, taxTotal, totalDiscount, cartTotal);

        return total;
    }

    const total = getEmptyCartTotal();

    return total;
};

export { convertRound };
