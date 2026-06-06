import React, { Component, Fragment } from 'react';
import { bindActionCreators } from 'redux';
import { Link } from 'react-router-dom';
import { connect } from 'react-redux';
import { addToHold } from '../../../../actions/hold-carts';
import { addNewCart, validateProductStock, deleteNotValidProductsFromCart } from '../../../../actions/cart';
import { applyCoupon, removeCoupon, getCoupons } from '../../../../actions/coupon';
import { getFees } from '../../../../actions/fees';
import database from '../../../../services/database';
import { fetchRequest } from '../../../../services/request';
import { __ } from '@wordpress/i18n';
import { formatPrice } from '../../../../utils/currency-format';
import { store } from 'react-notifications-component';
import { DoubleRightOutlined, PauseOutlined, TagFilled, DeleteFilled } from '@ant-design/icons';
import Popup from './../../../popup/popup';
import { isInternetConnected } from '../../../../services/connectivity';
import { getPosRoute } from '../../../../services/routes';
import { notify } from '../../../../services/notifications';
import { getPosApi, getTaxType } from '../../../../services/runtime';
import { getActiveCartRecordFromState, getFirstCartScopedRecord, hasRecordId } from '../../../../services/cart-session';
import { pushRoute } from '../../../../utils/navigation';
import { getOutletInventoryType, hasEntries, normalizeRecordId, parseWholeNumber } from '../../../../utils/value';
import { isEnterKey } from '../../../../utils/event';


const hasCartProducts = cart => {
    const activeCartRecord = getActiveCartRecordFromState({ cart });

    return Boolean(activeCartRecord && Array.isArray(activeCartRecord.cart) && activeCartRecord.cart.length);
};

const buildCouponTaxAmount = (coupon, taxes) => {
    if (getTaxType() === 'yes') {
        return 0;
    }

    let couponTax = 0;

    taxes.forEach(tax => {
        if (coupon.type === 'fixed_cart') {
            couponTax = (coupon.price * tax.rate) / 100;
        }
    });

    return couponTax;
};

const clearCartAdjustments = (props, activeCartId) => {
    if (!hasRecordId(activeCartId)) {
        return Promise.resolve(false);
    }

    const normalizedCartId = normalizeRecordId(activeCartId);

    return Promise.all([
        database.table('coupon').where('cart_id').equals(normalizedCartId).delete(),
        database.table('fees').where('cart_id').equals(normalizedCartId).delete(),
    ]).then(() => Promise.all([
        props.getCoupons(),
        props.getFees(),
    ]));
};

const getAppliedCouponLabel = coupon => ` (${coupon.type === 'percent' ? coupon.price + '%' : formatPrice(coupon.price)})`;

const getPopupNode = (isVisible, popupProps) => isVisible ? <Popup {...popupProps} /> : null;
const getCartItemsCount = cartItems => cartItems.reduce((total, item) => total + parseWholeNumber(item.quantity), 0);
const getCartTotalItemsLabel = itemsCount => (
    `${itemsCount} ${__(itemsCount > 1 ? 'Items' : 'Item', 'devdiggers-multipos-for-woocommerce')}`
);
const getAppliedCoupons = (couponState, activeCartId) => {
    const couponRecord = getFirstCartScopedRecord(couponState, activeCartId);
    return couponRecord && Array.isArray(couponRecord.coupon) ? couponRecord.coupon : [];
};
const getAppliedFees = (feesState, activeCartId) => {
    const feeRecord = getFirstCartScopedRecord(feesState, activeCartId);
    return feeRecord && Array.isArray(feeRecord.fees) ? feeRecord.fees : [];
};
const getCartSummaryTotal = cartTotal => parseFloat(cartTotal);
const getActiveCartRecord = cart => getActiveCartRecordFromState({ cart });
const getActiveCartId = cart => {
    const activeCartRecord = getActiveCartRecord(cart);
    return activeCartRecord ? activeCartRecord.id : 0;
};
const getTogglePopupState = key => previousState => ({
    [key]: !previousState[key],
});
const getInputValueState = (key, value) => ({
    [key]: value,
});
const getCouponPopupNode = (component, popupContent) => getPopupNode(component.state.showCouponPopup, {
    handleOverlay: component.handleToggleCouponPopup,
    popupContent,
    notDisabled: component.state.couponCode,
    handleSuccess: component.handleApplyCoupon,
    handleCancel: component.handleToggleCouponPopup,
});
const getHoldOrderPopupNode = (component, popupContent) => getPopupNode(component.state.showHoldOrderPopup, {
    handleOverlay: component.handleToggleHoldOrderPopup,
    popupContent,
    notDisabled: true,
    handleSuccess: component.handleSaveHoldOrder,
    handleCancel: component.handleToggleHoldOrderPopup,
});
const createRemoveCouponHandler = (component, couponCode) => () => component.props.removeCoupon(couponCode);
const createKeyUpHandler = (component, handler) => e => component[handler](e);

class CartTotal extends Component {

    constructor(props) {
        super(props);

        this.state = {
            showCouponPopup: false,
            showHoldOrderPopup: false,
            couponCode: '',
            holdOrderInfo: '',
        };
    }

    handleToggleCouponPopup = () => {
        this.setState(getTogglePopupState('showCouponPopup'));
    }

    handleToggleHoldOrderPopup = () => {
        const cart = this.props.cart;

        if (hasCartProducts(cart)) {
            this.setState(getTogglePopupState('showHoldOrderPopup'));
        } else {
            notify({
                title: __('Empty Cart', 'devdiggers-multipos-for-woocommerce'),
                message: __('Please add products in the cart first to hold it.', 'devdiggers-multipos-for-woocommerce'),
                type: 'danger',
            });
        }
    }

    handleCouponInput = e => {
        if (isEnterKey(e)) {
            this.handleApplyCoupon();
        } else {
            this.setState(getInputValueState('couponCode', e.target.value));
        }
    }

    handleHoldOrderInfoInput = e => {
        if (isEnterKey(e)) {
            this.handleSaveHoldOrder();
        } else {
            this.setState(getInputValueState('holdOrderInfo', e.target.value));
        }
    }

    handleApplyCoupon = () => {
        if (!isInternetConnected()) {
            notify({
                title: __('System is Offline', 'devdiggers-multipos-for-woocommerce'),
                message: __('Coupon cannot be applied in offline mode, please apply after getting online.', 'devdiggers-multipos-for-woocommerce'),
                type: 'danger',
            });
            return;
        }

        const cart = this.props.cart;

        if (hasCartProducts(cart)) {
            this.handleToggleCouponPopup();

            const couponCode = this.state.couponCode;

            if (couponCode) {
                const customers = this.props.customers || {};

                const postData = {
                    coupon_code: couponCode,
                    customer: customers.defaultCustomer || {}
                };

                fetchRequest(getPosApi().CHECK_COUPON_ENDPOINT, postData).then(response => {
                    if (response.success && response.coupon) {
                        response.coupon.coup_tax = buildCouponTaxAmount(response.coupon, this.props.taxes);

                        response.coupon = response.coupon;
                        this.props.applyCoupon(response.coupon);
                    } else {
                        notify({
                            title: __('Coupon Error', 'devdiggers-multipos-for-woocommerce'),
                            message: response.message,
                            type: 'danger',
                        });
                    }
                });
            } else {
                notify({
                    title: __('Empty Field', 'devdiggers-multipos-for-woocommerce'),
                    message: __('Please enter a coupon code first.', 'devdiggers-multipos-for-woocommerce'),
                    type: 'danger',
                });
            }
        } else {
            notify({
                title: __('Empty Cart', 'devdiggers-multipos-for-woocommerce'),
                message: __('Please add products in the cart to apply coupon.', 'devdiggers-multipos-for-woocommerce'),
                type: 'danger',
            });
        }
    }

    getActiveCartProducts = () => {
        const activeCartRecord = getActiveCartRecordFromState({ cart: this.props.cart });

        return activeCartRecord && Array.isArray(activeCartRecord.cart) ? activeCartRecord.cart : [];
    }

    getOutOfStockProducts = cartList => this.props.validateProductStock(cartList).then(notValidProducts => {
        if (!notValidProducts || !notValidProducts.out_of_stock_products) {
            return [];
        }

        const outOfStockProducts = Array.isArray(notValidProducts.out_of_stock_products) ? notValidProducts.out_of_stock_products : [];

        return (Array.isArray(cartList) ? cartList : []).filter(cartData => outOfStockProducts.includes(cartData.product_id));
    })

    getStockWarningMessage = notValidProductsList => {
        const names = (Array.isArray(notValidProductsList) ? notValidProductsList : []).map(product => product.name);
        let namesString = names.join(', ');

        if (names.length > 1) {
            return `${namesString} ${__('are out of stock now, please remove them from cart', 'devdiggers-multipos-for-woocommerce')}`;
        }

        return `${namesString} ${__('is out of stock now, kindly remove them from cart', 'devdiggers-multipos-for-woocommerce')}`;
    }

    notifyEmptyCartOnPay = () => notify({
        title: __('Empty Cart', 'devdiggers-multipos-for-woocommerce'),
        message: __('Cart is empty right now, kindly add products to checkout.', 'devdiggers-multipos-for-woocommerce'),
        type: 'danger',
    })

    notifySelectCustomerOnPay = () => notify({
        title: __('Select Customer First', 'devdiggers-multipos-for-woocommerce'),
        message: __('Customer is not selected, kindly select the customer to checkout.', 'devdiggers-multipos-for-woocommerce'),
        type: 'danger',
    })

    notifyCentralizedOfflineError = () => notify({
        title: __('Error', 'devdiggers-multipos-for-woocommerce'),
        message: __('Sorry, orders cannot be processed with centralized inventory in offline mode.', 'devdiggers-multipos-for-woocommerce'),
        type: 'danger',
    })

    proceedToPay = () => {
        pushRoute(this.props.history, getPosRoute('/pay'));
    }

    handleCentralizedInventoryPay = async (e, cartList) => {
        e.preventDefault();

        if (!isInternetConnected()) {
            this.notifyCentralizedOfflineError();
            return;
        }

        const activeCartRecord = getActiveCartRecordFromState({ cart: this.props.cart });
        const activeCartId = activeCartRecord ? activeCartRecord.id : null;
        const notValidProductsList = await this.getOutOfStockProducts(cartList);

        if (!notValidProductsList.length) {
            this.proceedToPay();
            return;
        }

        const validProductsList = cartList.filter(cartData => !notValidProductsList.includes(cartData));
        const stockWarning = this.getStockWarningMessage(notValidProductsList);

        if (confirm(stockWarning)) {
            this.props.deleteNotValidProductsFromCart(validProductsList, activeCartId);
        }
    }

    handlePayClick = async e => {
        const customers = this.props.customers || {};
        const cartList = this.getActiveCartProducts();

        if (!hasEntries(customers.defaultCustomer)) {
            e.preventDefault();
            this.notifySelectCustomerOnPay();
            return;
        }

        if (!cartList.length) {
            e.preventDefault();
            this.notifyEmptyCartOnPay();
            return;
        }

        if (getOutletInventoryType(this.props.outlet) === 'centralized') {
            await this.handleCentralizedInventoryPay(e, cartList);
        }
    }

    handleSaveHoldOrder = () => {
        const activeCartId = getActiveCartId(this.props.cart);

        this.handleToggleHoldOrderPopup();

        this.props.addToHold(this.state.holdOrderInfo).then(holdSaved => {
            if (holdSaved) {
                clearCartAdjustments(this.props, activeCartId).then(() => {
                    this.props.addNewCart();
                });
            }
        });
    }

    render() {
        const cart = this.props.cart || { list: [], total: {} };
        let totalItems = 0;
        const activeCartRecord = getActiveCartRecord(cart);

        if (activeCartRecord && Array.isArray(activeCartRecord.cart) && activeCartRecord.cart.length) {
            totalItems = getCartItemsCount(activeCartRecord.cart);
            totalItems = getCartTotalItemsLabel(totalItems);
        } else {
            totalItems = getCartTotalItemsLabel(0);
        }

        const activeCartId = getActiveCartId(cart);
        let coupon = getAppliedCoupons(this.props.coupon, activeCartId);
        let fees = getAppliedFees(this.props.fees, activeCartId);
        let total = cart.total || {};

        const couponHTML = coupon.map((val, index) => {
            const removeCouponEligible = true;

            return (
                <span className="ddwcpos-cart-applied-coupons" key={val.code || index}>
                    {removeCouponEligible ?
                        <DeleteFilled className="ddwcpos-remove-discount-icon" onClick={createRemoveCouponHandler(this, val.code)} />
                        : null
                    }
                    {val.code}
                    {getAppliedCouponLabel(val)}
                </span>
            );
        });

        let feesHTML = null;

        if (fees && fees.length) {
            feesHTML = fees.map((fee, feeIndex) => {
                return (
                    <div key={fee.name || feeIndex}>
                        <p>{fee.name}</p>
                        <strong>{formatPrice(fee.amount)}</strong>
                    </div>
                );
            });
        }

        const holdOrderPopupHTML = getHoldOrderPopupNode(this, (
            <Fragment>
                <h2>{__('Hold Order', 'devdiggers-multipos-for-woocommerce')}</h2>
                <input type="text" onKeyUp={createKeyUpHandler(this, 'handleHoldOrderInfoInput')} placeholder={__('Enter Order Info', 'devdiggers-multipos-for-woocommerce')} autoFocus />
            </Fragment>
        ));

        const couponPopupHTML = getCouponPopupNode(this, (
            <Fragment>
                <h2>{__('Apply Coupon', 'devdiggers-multipos-for-woocommerce')}</h2>
                <input type="text" onKeyUp={createKeyUpHandler(this, 'handleCouponInput')} placeholder={__('Enter Coupon Code', 'devdiggers-multipos-for-woocommerce')} autoFocus />
            </Fragment>
        ));

        return (
            <Fragment>
                <div className="ddwcpos-cart-totals">
                    <div>
                        <p>{__('Subtotal', 'devdiggers-multipos-for-woocommerce')}</p>
                        <strong>{formatPrice(total.cart_subtotal)}</strong>
                    </div>
                    {feesHTML}
                    <div>
                        <p>{__('Tax', 'devdiggers-multipos-for-woocommerce')}</p>
                        <strong>{formatPrice(total.tax_total)}</strong>
                    </div>
                    <div>
                        <p>{__('Applied Coupon(s)', 'devdiggers-multipos-for-woocommerce')}</p>
                        <strong>
                            {couponHTML.length ? couponHTML : __('N/A', 'devdiggers-multipos-for-woocommerce')}
                        </strong>
                    </div>
                    <div className="ddwcpos-cart-actions">
                        <span onClick={this.handleToggleCouponPopup}>
                            <TagFilled />
                            {__('Coupon', 'devdiggers-multipos-for-woocommerce')}
                        </span>
                        <span onClick={this.handleToggleHoldOrderPopup}>
                            <PauseOutlined />
                            {__('Hold Order', 'devdiggers-multipos-for-woocommerce')}
                        </span>
                    </div>
                    <Link className="ddwcpos-cart-pay" to={getPosRoute('/pay')} onClick={e => this.handlePayClick(e)}>
                        <p>
                            {__('Proceed to Pay', 'devdiggers-multipos-for-woocommerce')}
                            <br />
                            <i>{totalItems}</i>
                        </p>
                        <p>{formatPrice(getCartSummaryTotal(total.cart_total))}</p>
                        <DoubleRightOutlined />
                    </Link>
                    {null}
                </div>
                {holdOrderPopupHTML}
                {couponPopupHTML}
                {''}
            </Fragment>
        );
    }
}

const mapDispatchToProps = dispatch => bindActionCreators({
    applyCoupon,
    removeCoupon,
    validateProductStock,
    deleteNotValidProductsFromCart,
    getCoupons,
    getFees,
    addToHold,
    addNewCart,
}, dispatch);

export default connect(null, mapDispatchToProps)(CartTotal);
