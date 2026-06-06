import { __ } from '@wordpress/i18n';
import { fetchRequest } from '../services/request';
import database from '../services/database';
import { notify } from '../services/notifications';
import { getPosApi, getPosObject } from '../services/runtime';
import { getFirstCartScopedRecord, hasRecordId } from '../services/cart-session';
import { normalizeRecordId } from '../utils/value';
import { convertRound, getCartTotal, getEmptyCartTotal } from './cart-totals';

import { CART, COUPON } from '../state/action-types';
export { CART };
const getCartTable = () => database.table('cart');
const getProductsTable = () => database.table('products');
const getCouponTable = () => database.table('coupon');
const getFeesTable = () => database.table('fees');
const getPluginUrl = () => getPosObject().plugin_url || '';

export const setCart = (cart) => {
    return {
        type: CART,
        cart
    }
};

const notifyStockError = () => notify({
    title: __('Stock Error', 'devdiggers-multipos-for-woocommerce'),
    message: __('No stock available for this product.', 'devdiggers-multipos-for-woocommerce'),
    type: 'danger',
});

const shouldPlaySounds = state => {
    const settings = state.settings;

    return !settings.sounds || settings.sounds === 'enabled';
};

const playPosSound = name => {
    const audio = new Audio(`${getPluginUrl()}/assets/audio/${name}.mp3`);

    audio.play();
};

const buildCartState = (dbcart, state, dispatch) => {
    if (dbcart && dbcart.length) {
        return {
            list: dbcart,
            total: getCartTotal(dbcart, state, dispatch),
        };
    }

    return {
        list: [],
        total: getEmptyCartTotal(),
    };
};
const getPrimaryCartRecord = cartRecords => Array.isArray(cartRecords) && cartRecords.length ? cartRecords[0] : null;
const getActiveCartRecordId = cartRecords => {
    const activeCartRecord = getPrimaryCartRecord(cartRecords);

    return activeCartRecord ? activeCartRecord.id : 0;
};

const refreshCart = (dispatch, getState) => (
    loadActiveCartProducts().then(dbcart => {
        const cart = buildCartState(dbcart, getState(), dispatch);

        dispatch(setCart(cart));

        return cart;
    })
);

export const validateProductStock = cart_list => (dispatch) => {
    const postData = {
        cart_data: JSON.stringify(cart_list)
    };

    return fetchRequest(getPosApi().CHECK_CENTRALIZED_STOCK_ENDPOINT, postData).then(json => (
        json.out_of_stock_products !== undefined ? json : false
    ));
};

export const deleteNotValidProductsFromCart = cartData => (dispatch, getState) => {
    return updateActiveCart(cartData).then(updated => {
        if (updated) {
            return refreshCart(dispatch, getState);
        }
        return false;
    })
}

export const resetActiveCartStorage = () => (dispatch, getState) => {
    const cart = getState().cart;
    const activeCartRecord = getPrimaryCartRecord(cart.list);

    if (!activeCartRecord) {
        return Promise.resolve(false);
    }

    const activeCartId = activeCartRecord.id;
    return Promise.all([
        getCartTable().where('id').equals(activeCartId).delete(),
        getCouponTable().where('cart_id').equals(activeCartId).delete(),
        getFeesTable().where('cart_id').equals(activeCartId).delete(),
    ]).then(() => true);
}

export const getCart = () => (dispatch, getState) => {
    return refreshCart(dispatch, getState);
};

export const getProductViaBarcode = barcode => (dispatch, getState) => {
    return getProductsTable().where('barcode_init').equals(barcode).toArray().then(response => {
        if (response.length) {
            return response[0];
        }

        if (shouldPlaySounds(getState())) {
            playPosSound('error-sound');
        }

        return {};
    });
}

const findSellableProduct = productId => (
    getProductsTable().where("product_id").equals(productId).and(value => value.stock > 0 || value.stock === -1).toArray().then(dbproducts => (
        dbproducts.length ? dbproducts[0] : false
    ))
);

const dispatchAddedCartProduct = ({ cart, productData, actionProduct, quantity, dispatch, getState }) => {
    dispatch(setCart(cart));

    if (shouldPlaySounds(getState())) {
        playPosSound('add-to-cart');
    }
};

const prepareProductForCart = productId => findSellableProduct(productId);

export const addToCart = (product_id, quantity = 1) => (dispatch, getState) => {
    return prepareProductForCart(product_id).then(product => {
        if (!product) {
            notifyStockError();

            return false;
        }

        return loadActiveCartProducts().then(data => {
            const cartData = data.length ? data : [];

            return addProductToCartStorage(cartData, product, quantity);
        }).then(productData => {
            if (!productData) {
                return false;
            }

            return loadActiveCartProducts().then(cdata => {
                const cart = buildCartState(cdata || [], getState(), dispatch);

                dispatchAddedCartProduct({
                    cart,
                    productData,
                    actionProduct: product_id,
                    quantity,
                    dispatch,
                    getState,
                });

                return productData;
            });
        });
    });
};

const buildCartProductTitle = product => product.title;

const findMatchingCartProduct = (cartProducts, product, title) => (
    cartProducts.find(cartProduct => (
        cartProduct['name'] === title &&
        String(cartProduct['product_id']) === String(product.product_id)
    ))
);

const hasInsufficientStock = (stock, quantity) => stock < quantity && stock !== -1;
const updateExistingCartProduct = (cartProduct, product, quantity) => {
    cartProduct['tax'] = cartProduct['tax'] / cartProduct['quantity'];
    cartProduct['quantity'] += quantity;
    cartProduct['uf'] = cartProduct['uf'];
    cartProduct['tax'] = cartProduct['tax'];
    cartProduct['uf_total'] = parseFloat(cartProduct['quantity'] * cartProduct['uf']);
    cartProduct['tax'] = cartProduct['quantity'] * cartProduct['tax'];
    cartProduct['total'] = cartProduct['uf_total'];

    return cartProduct;
};

const createCartProductEntry = ({ product, quantity, title, stock, productTax, productPrice, unitPrice, specialPrice }) => {
    const productTotal = parseFloat(quantity * unitPrice);

    let productData = {
        key: Math.floor(Math.random() * 10000),
        id: product.product_id,
        sku: product.sku,
        slug: product.slug,
        categories: product.categories,
        image: product.image,
        product_id: product.product_id,
        parent: product.parent,
        original_regular_price: product.original_regular_price,
        name: title,
        stock: stock,
        special: specialPrice,
        quantity: quantity,
        price: productPrice,
        onsale: product.onsale,
        originalTax: product.tax,
        tax: productTax,
        tax_label: product.tax_label,
        total: productTotal,
        uf: unitPrice,
        length: product.length ? product.length : 0,
        width: product.width ? product.width : 0,
        height: product.height ? product.height : 0,
        uf_total: productTotal,
        type: product.type,
    };

    return productData;
};

const persistCartRecord = cartData => (
    getCartTable().put(cartData).then(() => cartData).catch('DataError', e => {
        return false;
    }).catch(Error, e => {
        return false;
    }).catch(e => {
        return false;
    })
);

function addProductToCartStorage(products, product, quantity) {
    let unitPrice = product.regular_price;
    let productTax = product.tax;
    const productPrice = product.regular_price;
    const specialPrice = product.sale_price;
    const stock = product.stock;
    const activeCartId = getActiveCartRecordId(products);
    const productData = products.length ? [...products[0].cart] : [];
    const title = buildCartProductTitle(product);

    const matchingCartProduct = findMatchingCartProduct(productData, product, title);

    if (matchingCartProduct) {
        if (hasInsufficientStock(stock, matchingCartProduct['quantity'] + quantity)) {
            notifyStockError();

            return Promise.resolve(false);
        }

        const productRecord = updateExistingCartProduct(matchingCartProduct, product, quantity);

        return persistCartRecord({
            id: activeCartId,
            active_cart: 1,
            cart: productData,
        }).then(stored => stored ? productRecord : false);
    }

    if (hasInsufficientStock(stock, quantity)) {
        notifyStockError();

        return Promise.resolve(false);
    }

    const productRecord = createCartProductEntry({
        product,
        quantity,
        title,
        stock,
        productTax,
        productPrice,
        unitPrice,
        specialPrice,
    });

    return persistCartRecord({
        id: activeCartId,
        active_cart: 1,
        cart: productData.concat(productRecord),
    }).then(stored => stored ? productRecord : false);
}

const loadActiveCartProducts = () => {
    return getCartTable().where("active_cart").equals(1).toArray();
}

export const removeCart = (newCartId) => (dispatch, getState) => {
    const removeActiveCart = getCartTable().where("active_cart").equals(1).delete();

    if (!hasRecordId(newCartId)) {
        return removeActiveCart;
    }

    return removeActiveCart.then(() => (
        getCartTable().where('id').equals(normalizeRecordId(newCartId)).modify({ active_cart: 1 })
    )).then(() => refreshCart(dispatch, getState));
}

const isSameCartProduct = (candidate, cartProduct) => (
    String(candidate.product_id) === String(cartProduct.product_id) &&
    cartProduct.name === candidate.name &&
    (!hasRecordId(cartProduct.key) || String(cartProduct.key) === String(candidate.key))
);

export const removeCartProduct = cartProduct => (dispatch, getState) => {
    return loadActiveCartProducts().then(response => {
        const activeCartRecord = getPrimaryCartRecord(response);
        if (activeCartRecord && activeCartRecord.cart !== undefined) {
            const activeCartId = activeCartRecord.id;
            const remainingProducts = activeCartRecord.cart.filter(item => !isSameCartProduct(item, cartProduct));

            return updateActiveCart(remainingProducts).then(updated => {
                if (!updated) {
                    return false;
                }

                return loadActiveCartProducts().then(dbcart => {
                    const state = getState();
                    const refreshedActiveCartRecord = getPrimaryCartRecord(dbcart);
                    const cart = refreshedActiveCartRecord && refreshedActiveCartRecord.cart.length
                        ? buildCartState(dbcart, state, dispatch)
                        : buildCartState([], state, dispatch);

                    dispatch(setCart(cart));

                    return true;
                });
            });
        }

        return false;
    });
};

const updateActiveCart = cartData => {
    return getCartTable().where("active_cart").equals(1).modify({
        cart: cartData
    });
}

export const updateCartProduct = (product_id, modifiedQuantity, product_name = '', cartProductKey = '') => (dispatch, getState) => {
    return loadActiveCartProducts().then(data => {
        const cartData = data.length ? data : [];

        return updateCartProductInStorage(cartData, product_id, modifiedQuantity, product_name, cartProductKey);
    }).then(updatedCartRecord => {
        if (!updatedCartRecord) {
            return false;
        }

        const cartRecords = [updatedCartRecord];
        const cart = buildCartState(cartRecords, getState(), dispatch);

        dispatch(setCart(cart));

        return updatedCartRecord;
    });
};

function updateCartProductInStorage(cartData, product_id, modifiedQuantity, product_name, cartProductKey) {
    const activeCartRecord = getPrimaryCartRecord(cartData);

    if (!activeCartRecord) {
        return Promise.resolve(false);
    }

    const current_cart_length = activeCartRecord.cart.length;
    const product_data = [...activeCartRecord.cart];

    const activeCartId = activeCartRecord.id;
    const cartRecord = {
        id: activeCartId,
        active_cart: 1,
        cart: product_data
    };

    for (let l = 0; l < current_cart_length; l++) {
        const productRecord = product_data[l];

        if (String(product_id) === String(productRecord.product_id) && product_name === productRecord.name && (!hasRecordId(cartProductKey) || String(cartProductKey) === String(productRecord.key))) {
            const singleTax = parseFloat(productRecord.originalTax);
            productRecord.quantity = modifiedQuantity;
            productRecord.uf_total = parseFloat(productRecord.quantity * productRecord.uf);
            productRecord.tax = productRecord.quantity * singleTax;
            productRecord.total = productRecord.uf_total;

            break;
        }
    }

    return persistCartRecord(cartRecord);
}
const getNextCartId = cart => {
    const lastCartId = Number(cart && cart.id);

    return cart && !Number.isNaN(lastCartId) ? lastCartId + 1 : 0;
};

export const addNewCart = () => dispatch => {
    return getCartTable().orderBy('id').last().then(cart => {
        const newCartId = getNextCartId(cart);
        const cart_obj = {
            id: newCartId,
            active_cart: 1,
            cart: []
        };
        return getCartTable().add(cart_obj).then(addResult => {
            if (addResult) {
                const final_total = getEmptyCartTotal();

                const cart = {
                    list: [],
                    total: final_total
                }

                dispatch(setCart(cart));
            }
            return addResult;
        });
    });
}
