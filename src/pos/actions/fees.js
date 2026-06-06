import { __ } from '@wordpress/i18n';
import { getCart } from './cart';
import database from '../services/database';
import { notify } from '../services/notifications';
import { getCartScopedRecords, getFirstCartScopedRecord, hasRecordId, resolveActiveCartId } from '../services/cart-session';

import { FEES } from '../state/action-types';
export { FEES };

export const setFees = fees => {
    return {
        type: FEES,
        fees
    }
};

const buildFeeState = (feeRecord, feeList) => [{ ...feeRecord, fees: feeList }];
const getFeeEntries = feeRecord => Array.isArray(feeRecord && feeRecord.fees) ? feeRecord.fees : [];
const createFeeRecord = (activeCartId, fees) => ({
    id: activeCartId,
    cart_id: activeCartId,
    fees,
});

export const getFees = () => (dispatch, getState) => {
    return Promise.all([resolveActiveCartId(getState), loadFeesFromStorage()]).then(([activeCartId, result]) => {
        const activeFees = hasRecordId(activeCartId) ? getCartScopedRecords(result, activeCartId) : [];
        dispatch(setFees(activeFees));
        return activeFees;
    });
};

const refreshFeesAndCartState = (dispatch, fees) => {
    dispatch(setFees(fees));
    dispatch(getCart());
};

const notifyFeeChange = (title, message, showMessage) => {
    if (showMessage) {
        notify({
            title,
            message,
            type: 'success',
        });
    }
};

export const addFees = (fee, showMessage = true) => (dispatch, getState) => {
    return resolveActiveCartId(getState).then(activeCartId => {
        if (!hasRecordId(activeCartId)) {
            return false;
        }

        return loadFeesFromStorage().then(res => {
            const feeRecord = getFirstCartScopedRecord(res, activeCartId);
            if (feeRecord) {
                const updatedFees = getFeeEntries(feeRecord).concat(fee);
                return database.table('fees').where("cart_id").equals(activeCartId).modify({
                    fees: updatedFees
                }).then(data => {
                    if (data) {
                        notifyFeeChange(__('Fee Added', 'devdiggers-multipos-for-woocommerce'), __('Fee is added successfully in the cart.', 'devdiggers-multipos-for-woocommerce'), showMessage);

                        refreshFeesAndCartState(dispatch, buildFeeState(feeRecord, updatedFees));

                        return true;
                    }

                    return false;
                });
            }

            const newFeeRecord = createFeeRecord(activeCartId, [fee]);

            return database.table('fees').put(newFeeRecord).then(() => {
                notifyFeeChange(__('Fee Added', 'devdiggers-multipos-for-woocommerce'), __('Fee is added successfully in the cart.', 'devdiggers-multipos-for-woocommerce'), showMessage);

                refreshFeesAndCartState(dispatch, [newFeeRecord]);

                return true;
            });
        });
    });
}

export const updateFees = (fees, showMessage = true) => (dispatch, getState) => {
    return resolveActiveCartId(getState).then(activeCartId => {
        if (!hasRecordId(activeCartId)) {
            return false;
        }

        return loadFeesFromStorage().then(res => {
            const feeRecord = getFirstCartScopedRecord(res, activeCartId);
            if (feeRecord) {
                return database.table('fees').where("cart_id").equals(activeCartId).modify({
                    fees: fees
                }).then(data => {
                    if (data) {
                        notifyFeeChange(__('Fee Updated', 'devdiggers-multipos-for-woocommerce'), __('Fee is updated successfully in the cart.', 'devdiggers-multipos-for-woocommerce'), showMessage);

                        refreshFeesAndCartState(dispatch, buildFeeState(feeRecord, fees));

                        return true;
                    }

                    return false;
                });
            }

            return false;
        });
    });
}

export const removeFees = (feeId, activeCartId = null, showMessage = true) => (dispatch, getState) => {
    const activeCartPromise = activeCartId === null
        ? resolveActiveCartId(getState)
        : Promise.resolve(activeCartId);

    return activeCartPromise.then(resolvedActiveCartId => {
        if (!hasRecordId(resolvedActiveCartId)) {
            return false;
        }

        return loadFeesFromStorage().then(res => {
            activeCartId = resolvedActiveCartId;
            const feeRecord = getFirstCartScopedRecord(res, activeCartId);
            if (feeRecord && Object.prototype.hasOwnProperty.call(getFeeEntries(feeRecord), feeId)) {
                const updatedFees = getFeeEntries(feeRecord).filter((fee, index) => index !== feeId);

                return database.table('fees').where("cart_id").equals(activeCartId).modify({
                    fees: updatedFees
                }).then(data => {
                    if (data) {
                        notifyFeeChange(__('Fee Removed', 'devdiggers-multipos-for-woocommerce'), __('Fee is removed successfully in the cart.', 'devdiggers-multipos-for-woocommerce'), showMessage);

                        refreshFeesAndCartState(dispatch, buildFeeState(feeRecord, updatedFees));

                        return true;
                    }

                    return false;
                });
            }

            return false;
        });
    });
}

const loadFeesFromStorage = () => database.table('fees').toArray();
