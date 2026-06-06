import { __ } from '@wordpress/i18n';
import { store } from 'react-notifications-component';
import database from '../services/database';
import { fetchRequest } from '../services/request';
import { saveTransactionsToStorage, getTransactions } from './transactions';
import { isInternetConnected } from '../services/connectivity';
import { notify } from '../services/notifications';
import { getPosApi, getPosConfigValue } from '../services/runtime';
import { getActiveCartRecordFromState, getFirstCartScopedRecord, hasRecordId } from '../services/cart-session';
import { getOrderBilling } from '../utils/orders';
import { getOutletId, normalizeRecordId, toDecimal } from '../utils/value';

import { ORDERS } from '../state/action-types';
export { ORDERS };
const ORDERS_TABLE = 'orders';
const getOrdersTable = () => database.table(ORDERS_TABLE);
const getProductsTable = () => database.products;
const getCouponsTable = () => database.coupon;
const getFeesTable = () => database.fees;
const getCartTable = () => database.cart;
const getPrimaryCartRecord = getActiveCartRecordFromState;
const getFirstStateRecord = stateSlice => (
    Array.isArray(stateSlice) && stateSlice.length ? stateSlice[0] : null
);
const getScopedStateRecord = (stateSlice, activeCartId) => (
    hasRecordId(activeCartId)
        ? getFirstCartScopedRecord(stateSlice, activeCartId)
        : getFirstStateRecord(stateSlice)
);
const buildOrdersRequest = (outletId, currentPage = 0, perPage = -1) => ({
    outlet_id: normalizeRecordId(outletId),
    per_page: perPage,
    current_page: currentPage,
});
const isPaymentAmountPresent = paymentMethod => Boolean(paymentMethod.amount);
const mergePaymentMethodAmount = (existingMethod, amount) => {
    existingMethod.amount = parseFloat(existingMethod.amount) + parseFloat(amount);
    return existingMethod;
};
const createMergedPaymentMethod = paymentMethod => ({ ...paymentMethod });
const normalizeOrderProducts = products => Array.isArray(products) ? products : [];
const createStockUpdateRequest = (productData, quantityDelta, guardPositiveStock) => Promise.all([
    updateProductStock(productData, quantityDelta, guardPositiveStock),
]);
const getPaymentMethodsInput = paymentMethods => Array.isArray(paymentMethods) ? paymentMethods : [];
const getDefaultCustomerFromState = state => (state && state.customers && state.customers.defaultCustomer) || {};
const getActiveCartProducts = cartState => {
    const activeCartRecord = getPrimaryCartRecord({ cart: cartState });
    return activeCartRecord && Array.isArray(activeCartRecord.cart) ? activeCartRecord.cart : [];
};
const getStateDefaultTable = state => state && state.tables ? state.tables.defaultTable : {};
const getOrderActiveCartId = state => {
    const activeCartRecord = getPrimaryCartRecord(state);

    return activeCartRecord ? activeCartRecord.id : null;
};
const getOrderAdjustmentListFromState = (state, stateKey, propertyName) => {
    const adjustmentRecord = getScopedStateRecord(state && state[stateKey], getOrderActiveCartId(state));

    return adjustmentRecord && Array.isArray(adjustmentRecord[propertyName]) ? adjustmentRecord[propertyName] : [];
};
const getOrderCouponsFromState = state => getOrderAdjustmentListFromState(state, 'coupon', 'coupon');
const getOrderFeesFromState = state => getOrderAdjustmentListFromState(state, 'fees', 'fees');
const getOrderBillingField = (billing, field) => billing && billing[field] ? billing[field] : '';
const getOrderProductQuantity = productData => parseFloat(productData && productData.quantity) || 0;
const toStockNumber = value => {
    const stockValue = parseFloat(value);

    return Number.isNaN(stockValue) ? 0 : stockValue;
};

export const setOrders = (orders) => {
    return {
        type: ORDERS,
        orders
    }
};

const createOrdersState = orders => ({
    list: orders,
    s: '',
    sorder: orders,
});
const createSearchedOrdersState = (orders, searchTerm) => ({
    list: orders,
    s: searchTerm,
    sorder: searchTerm ? orders.filter(order => isOrderMatchingSearch(order, searchTerm)) : orders,
});

export const getOrders = outletId => dispatch => {
    return loadOnlineOrdersFromStorage().then(res => {
        if (res && res.length) {
            return loadOrdersFromStorage().then(reslt => {
                dispatch(setOrders(createOrdersState(reslt)));
                return reslt;
            });
        }

        notify({
            title: __('Loading Orders', 'devdiggers-multipos-for-woocommerce'),
            message: __('Loading Orders in the POS', 'devdiggers-multipos-for-woocommerce'),
            type: 'info'
        });

        return getOrdersRecursive(buildOrdersRequest(outletId), dispatch);
    });
}

const getOrdersFromStorage = () => getOrdersTable().toArray();

const loadOrdersFromStorage = () => getOrdersFromStorage().then(data => data ? data : false);

const loadOnlineOrdersFromStorage = () => getOrdersTable().where('order_type').equals('online').toArray().then(data => data);

const getOrdersRecursive = (postData, dispatch, countOrders = '') => (
    fetchRequest(getPosApi().GET_ORDERS_ENDPOINT, postData).then(totalOrders => {
        if (typeof totalOrders === 'number') {
            postData = buildOrdersRequest(
                postData.outlet_id,
                postData.current_page + 1,
                getPosConfigValue('per_page', -1)
            );

            if (totalOrders > 0) {
                return getOrdersRecursive(postData, dispatch, totalOrders);
            }

            return loadOrdersFromStorage().then(res => {
                notify({
                    title: __('No Orders Found', 'devdiggers-multipos-for-woocommerce'),
                    message: __('No orders have been placed yet in this outlet.', 'devdiggers-multipos-for-woocommerce'),
                    type: 'danger'
                });

                dispatch(setOrders(createOrdersState(res)));
                return true;
            });
        }

        if (!Array.isArray(totalOrders) || !totalOrders.length) {
            return true;
        }

        return getOrdersTable().bulkPut(totalOrders).then(() => loadOrdersFromStorage()).then(res => {
            dispatch(setOrders(createOrdersState(res)));

            const remainingOrders = countOrders > 0 ? countOrders - postData.per_page : 0;

            if (remainingOrders > 0) {
                const nextRequest = buildOrdersRequest(postData.outlet_id, postData.current_page + 1, postData.per_page);
                return getOrdersRecursive(nextRequest, dispatch, remainingOrders);
            }

            notify({
                title: __('Orders Loaded', 'devdiggers-multipos-for-woocommerce'),
                message: __('All Orders are loaded successfully.', 'devdiggers-multipos-for-woocommerce'),
                type: 'success'
            });

            return true;
        });
    })
);

const summarizePaymentMethods = paymentMethodsInput => {
    let tendered = 0;
    const paymentMethods = [];
    const sourcePaymentMethods = getPaymentMethodsInput(paymentMethodsInput);

    sourcePaymentMethods.forEach(paymentMethod => {
        if (isPaymentAmountPresent(paymentMethod)) {
            tendered += toDecimal(paymentMethod.amount);
            const existingMethod = paymentMethods.find(method => method.slug === paymentMethod.slug);

            if (existingMethod) {
                mergePaymentMethodAmount(existingMethod, paymentMethod.amount);
            } else {
                paymentMethods.push(createMergedPaymentMethod(paymentMethod));
            }
        }
    });

    return {
        paymentMethods,
        tendered,
    };
};

const buildOrderPayload = (currentState, stateData, paymentMethods) => {
    const defaultCustomer = getDefaultCustomerFromState(currentState);

    return {
        coupons: getOrderCouponsFromState(currentState),
        fees: getOrderFeesFromState(currentState),
        customer_id: defaultCustomer.id,
        products: getActiveCartProducts(currentState.cart),
        payment_methods: paymentMethods,
        table: getStateDefaultTable(currentState),
    };
};

export const createOrder = (state_data, payComponent) => (dispatch, getState) => {
    const currentState = getState();
    const paymentSummary = summarizePaymentMethods(state_data.paymentMethods);

    const postOrderData = buildOrderPayload(currentState, state_data, paymentSummary.paymentMethods);

    if (!postOrderData) {
        return Promise.resolve([]);
    }

    const postData = {
        outlet_id: getOutletId(currentState.outlet),
        order_data: JSON.stringify(postOrderData)
    };

    if (!isInternetConnected()) {
        notify({
            title: __('System is Offline', 'devdiggers-multipos-for-woocommerce'),
            message: __('Orders cannot be placed while the system is offline.', 'devdiggers-multipos-for-woocommerce'),
            type: 'danger',
        });
        return Promise.resolve(false);
    }

    return fetchRequest(getPosApi().CREATE_ORDER_ENDPOINT, postData).then(order_data => {
        return saveTransactionsToStorage(order_data.transactions).then(() => {
            dispatch(getTransactions());
            return saveOrderToStorage(order_data);
        }).then(() => order_data);
    });
}

export const saveOrderToStorage = data => {
    return getOrdersTable().put(data).then(() => data);
}

const updateStockQuantity = (stockQuantity, quantityDelta, guardPositiveStock) => {
    const currentStockQuantity = toStockNumber(stockQuantity);

    if (guardPositiveStock && currentStockQuantity <= 0) {
        return stockQuantity;
    }

    return currentStockQuantity + quantityDelta;
};

const updateProductStock = (productData, quantityDelta, guardPositiveStock = true) => (
    getProductsTable().where('product_id').equals(productData.id).modify(product => {
        product.stock = toStockNumber(product.stock) + quantityDelta;
        product.stock_quantity = updateStockQuantity(product.stock_quantity, quantityDelta, guardPositiveStock);
    })
);

const clearOrderCartState = activeCartId => (
    Promise.all([
        getCouponsTable().where("cart_id").equals(normalizeRecordId(activeCartId)).delete(),
        getFeesTable().where("cart_id").equals(normalizeRecordId(activeCartId)).delete(),
    ])
);

export const removeProductsFromCart = (products, activeCartId) => {
    return getCartTable().where("id").equals(normalizeRecordId(activeCartId)).delete().then(deletedCount => {
        if (!deletedCount) {
            return false;
        }

        const stockUpdates = normalizeOrderProducts(products).map(productData => (
            createStockUpdateRequest(productData, -getOrderProductQuantity(productData), true)
        ));

        return Promise.all(stockUpdates).then(() => clearOrderCartState(activeCartId));
    });
}

const isOrderMatchingSearch = (order, searchTerm) => {
    const normalizedSearch = String(searchTerm);
    const contains = value => value && value.toString().includes(normalizedSearch);
    const billing = getOrderBilling(order);

    return (
        contains(order.order_id) ||
        contains(getOrderBillingField(billing, 'phone')) ||
        contains(order.email) ||
        contains(getOrderBillingField(billing, 'fname')) ||
        contains(getOrderBillingField(billing, 'lname'))
    );
};

export const loadSearchedOrders = (search, orders) => (dispatch) => {
    dispatch(setOrders(createSearchedOrdersState(orders, search)));
}
