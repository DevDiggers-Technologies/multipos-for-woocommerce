import React, { Component, Fragment } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import { __, sprintf } from '@wordpress/i18n';
import { PrinterOutlined, UserOutlined, WarningFilled, ShoppingCartOutlined, DeleteOutlined, TableOutlined } from '@ant-design/icons';
import ReactHtmlParser from 'react-html-parser';
import { formatPrice } from '../../../../utils/currency-format';
import { addHoldCartToCurrentCart, deleteHoldCart } from './../../../../actions/hold-carts';
import { updateDefaultCustomer } from './../../../../actions/customers';
import { printInvoice } from './../../../../actions/invoice';
import { updateDefaultTable } from './../../../../actions/tables';
import { getPosRoute } from '../../../../services/routes';
import { notify } from '../../../../services/notifications';
import { getPosConfig } from '../../../../services/runtime';
import { getActiveCartRecordFromState } from '../../../../services/cart-session';
import { getOrderDetailsPanel, isMobileViewport, pushRoute } from '../../../../utils/navigation';
import { getHoldOrderRecord, getOrderCurrencySymbol } from '../../../../utils/orders';
import { hasEntries } from '../../../../utils/value';

const getFallbackOrderImage = () => `<img width="150" height="150" src="${getPosConfig()['placeholder_image'] || ''}" class="attachment-thumbnail size-thumbnail" />`;

const getOrderProductImage = product => product && product.image ? product.image : getFallbackOrderImage();

const getOrderProductMeta = product => (
    product && Array.isArray(product.meta_data) && product.meta_data.length
        ? product.meta_data.map(meta => (
            <p key={meta.key}><strong>{meta.key}: </strong>{meta.value}</p>
        ))
        : []
);
const getEmptyOrderDetailsMarkup = isVisible => (
    <div className="ddwcpos-order-details-wrapper" style={getOrderDetailsWrapperStyle(isVisible)}>
        <div className="ddwcpos-no-results">
            <WarningFilled />
            <p>{__('No Order Details Found', 'devdiggers-multipos-for-woocommerce')}</p>
        </div>
    </div>
);

const getOrderCouponsMarkup = (order, orderType) => {
    const couponAmountKey = orderType === 'online' ? 'amount' : 'price';
    const orderCoupons = Array.isArray(order && order.coupons) ? order.coupons : [];
    const orderCurrencySymbol = order && order.order_currency_symbol ? order.order_currency_symbol : '';

    return orderCoupons.map((coupon, key) => (
        <span className="ddwcpos-order-applied-coupons" key={key}>
            {coupon.code}
            {` (${formatPrice(coupon[couponAmountKey], orderCurrencySymbol)})`}
        </span>
    ));
};

const getOrderFeesMarkup = order => {
    if (!order || !Array.isArray(order.fees) || !order.fees.length) {
        return null;
    }

    return order.fees.map(fee => (
        <div key={fee.name}>
            <p>{fee.name}</p>
            <strong>{formatPrice(fee.amount)}</strong>
        </div>
    ));
};

const getTenderedMarkup = order => {
    if (Array.isArray(order.payment_methods) && order.payment_methods.length) {
        return order.payment_methods.map((paymentMethod, key) => (
            <div key={key}>
                <p>{paymentMethod.name}</p>
                <strong>{formatPrice(paymentMethod.amount, order.order_currency_symbol)}</strong>
            </div>
        ));
    }

    return (
        <div>
            <p>{order.payment_method_title}</p>
            <strong>{formatPrice(order.tendered, order.order_currency_symbol)}</strong>
        </div>
    );
};

const getOrderDiscountAmount = (order, orderType, totalTax) => {
    if (orderType === 'online') {
        return order.discount;
    }

    if (orderType === 'offline' && hasEntries(order.discount)) {
        if (order.discount.type === 'fixed') {
            return -order.discount.amount;
        }

        return -order.discount.amount * (order.order_subtotal + totalTax) / 100;
    }

    return 0;
};

const getProductKey = product => product.item_id || product.id || product.product_id || product.name;

const getOrderDetailsWrapperStyle = isVisible => (isVisible ? {} : { display: 'none' });
const hasHoldOrderShape = order => Boolean(order) && Array.isArray(order.list) && order.list.some(Boolean);
const hasSalesOrderShape = order => Boolean(order) && Array.isArray(order.products);

const canAssignTable = table => table && table.slug;
const getSalesOrderProducts = order => hasSalesOrderShape(order) ? order.products : [];
const getSalesOrderTaxLines = order => Array.isArray(order.tax_lines) ? order.tax_lines : [];
const getHoldCartProducts = cart => cart && Array.isArray(cart.cart) ? cart.cart : [];
const getOrderEmail = order => order && order.email ? order.email : '';
const getOrderTaxRate = tax => parseFloat(tax && tax.rate) || 0;
const getActiveCartRecord = cartState => getActiveCartRecordFromState({ cart: cartState });
const hideMobileOrderDetailsPanel = () => {
    if (!isMobileViewport()) {
        return;
    }

    const detailsWrapper = getOrderDetailsPanel();

    if (detailsWrapper) {
        detailsWrapper.style.position = 'initial';
        detailsWrapper.style.display = 'none';
    }
};

const getOrderTaxMarkup = (order, orderType) => {
    let totalTax = 0;
    const orderTaxLines = getSalesOrderTaxLines(order);
    const orderCurrencySymbol = getOrderCurrencySymbol(order);

    const taxHTML = orderTaxLines.map(tax => {
        if (orderType === 'online') {
            return (
                <div key={tax.label}>
                    <p>{tax.label}</p>
                    <strong>{formatPrice(tax.total, orderCurrencySymbol)}</strong>
                </div>
            );
        }

        if (orderType === 'offline') {
            const taxAmount = getOrderTaxRate(tax) * (parseFloat(order.order_subtotal) || 0) / 100;
            totalTax += taxAmount;

            return (
                <div key={tax.label}>
                    <p>{tax.label}</p>
                    <strong>{formatPrice(taxAmount, orderCurrencySymbol)}</strong>
                </div>
            );
        }

        return null;
    });

    return {
        taxHTML,
        totalTax,
    };
};
const getHoldOrderCustomerEmail = cart => (cart && cart.customer && cart.customer.email) || '';
const getHoldOrderTableName = cart => (cart && cart.table && cart.table.name) || '';

class OrderDetails extends Component {
    constructor(props) {
        super(props);

        this.state = {
        };
    }

    moveHoldCartToCart = holdCart => {
        const holdCartId = holdCart && holdCart.id;
        const customer = holdCart && holdCart.customer ? holdCart.customer : {};
        const table = holdCart && holdCart.table ? holdCart.table : {};

        this.props.addHoldCartToCurrentCart(holdCartId);
        if (customer.id) {
            this.props.updateDefaultCustomer(customer.id);
        }

        if (canAssignTable(table)) {
            this.props.updateDefaultTable(table.slug);
        }

        pushRoute(this.props.history, getPosRoute());
    }

    handlePrintInvoice = order => {
        this.props.printInvoice(order);
    }

    handleAddHoldCart = holdCart => {
        const activeCartRecord = getActiveCartRecord(this.props.cart);

        if (activeCartRecord && Array.isArray(activeCartRecord.cart) && activeCartRecord.cart.length) {
            if (confirm(__('There are items present in the cart, proceeding will replace these items. Please confirm'))) {
                this.moveHoldCartToCart(holdCart);
            }
        } else {
            this.moveHoldCartToCart(holdCart);
        }
    }

    handleDeleteHoldCart = holdCartId => {
        this.props.deleteHoldCart(holdCartId);
        notify({
            title: __('Delete Success', 'devdiggers-multipos-for-woocommerce'),
            message: __('Hold order is deleted successfully.', 'devdiggers-multipos-for-woocommerce'),
            type: 'success',
        });
        if (this.props.handleToggleTableOrderPopup) {
            this.props.handleToggleTableOrderPopup();
        }

        hideMobileOrderDetailsPanel();
    }

    handleBackOrderDetailsMobile = () => {
        hideMobileOrderDetailsPanel();
    }

    createPrintInvoiceHandler = order => () => this.handlePrintInvoice(order)
    createAddHoldCartHandler = cart => () => this.handleAddHoldCart(cart)
    createDeleteHoldCartHandler = cartId => () => this.handleDeleteHoldCart(cartId)

    render() {
        const orderType = this.props.orderType;
        const order = this.props.order;

        if (!order) {
            return getEmptyOrderDetailsMarkup(!isMobileViewport());
        } else {
            if (orderType === 'hold') {
                if (!hasHoldOrderShape(order)) {
                    return getEmptyOrderDetailsMarkup(this.props.tableView || !isMobileViewport());
                }

                const cart = getHoldOrderRecord(order);
                const total = order.total || {};
                const holdCartProducts = getHoldCartProducts(cart);

                if (!cart) {
                    return getEmptyOrderDetailsMarkup(this.props.tableView || !isMobileViewport());
                }

                const productsList = holdCartProducts.map(product => {
                    return (
                        <div className="ddwcpos-order-product" key={getProductKey(product)}>
                            <div className="ddwcpos-order-product-info">
                                <div className="ddwcpos-order-product-image" dangerouslySetInnerHTML={{ __html: getOrderProductImage(product) }}></div>
                                <div className="ddwcpos-order-product-details">
                                    <h4 title={ReactHtmlParser(product.name)}>{ReactHtmlParser(product.name)}</h4>
                                    <p>{formatPrice(product.uf)} x {product.quantity}</p>
                                </div>
                                <div className="ddwcpos-order-product-price">
                                    <p>
                                        {formatPrice(product.uf_total)}
                                    </p>
                                </div>
                            </div>
                        </div>
                    );
                });

                return (
                    <div className="ddwcpos-order-details-wrapper" style={getOrderDetailsWrapperStyle(this.props.tableView || !isMobileViewport())}>
                        <div className="ddwcpos-order-details-header">
                            <h2>{this.props.tableView ? __('Table Order', 'devdiggers-multipos-for-woocommerce') : __('Cart Details', 'devdiggers-multipos-for-woocommerce')}</h2>
                            <div>
                                <p>
                                    <UserOutlined />
                                    {getHoldOrderCustomerEmail(cart)}
                                </p>
                                {getHoldOrderTableName(cart) ?
                                    <p>
                                        <TableOutlined />
                                        {getHoldOrderTableName(cart)}
                                    </p>
                                    : null}
                            </div>
                            {!this.props.tableView && isMobileViewport() ?
                                <button className="ddwcpos-button ddwcpos-button-secondary" onClick={this.handleBackOrderDetailsMobile}>{__('X', 'devdiggers-multipos-for-woocommerce')}</button>
                                : null}
                        </div>
                        <div className="ddwcpos-order-products">
                            {productsList}
                        </div>
                        <div className="ddwcpos-order-totals">
                            <div>
                                <p>{__('Subtotal', 'devdiggers-multipos-for-woocommerce')}</p>
                                <strong>{formatPrice(total.cart_subtotal, order.order_currency_symbol)}</strong>
                            </div>
                            <div>
                                <p>{__('Tax', 'devdiggers-multipos-for-woocommerce')}</p>
                                <strong>{formatPrice(total.tax_total)}</strong>
                            </div>
                            <div className="ddwcpos-order-total">
                                <p>{__('Total', 'devdiggers-multipos-for-woocommerce')}</p>
                                <strong>{formatPrice(total.cart_total)}</strong>
                            </div>
                            <div className="ddwcpos-hold-order-info">
                                <p>{sprintf(__('Info: %s', 'devdiggers-multipos-for-woocommerce'), cart.info)}</p>
                            </div>
                            <div className="ddwcpos-hold-order-actions">
                                <button className="ddwcpos-button" onClick={this.createAddHoldCartHandler(cart)}>
                                    <ShoppingCartOutlined />
                                    {__('Add to Cart', 'devdiggers-multipos-for-woocommerce')}
                                </button>
                                <button className="ddwcpos-button" onClick={this.createDeleteHoldCartHandler(cart.id)}>
                                    <DeleteOutlined />
                                    {__('Delete', 'devdiggers-multipos-for-woocommerce')}
                                </button>
                            </div>
                        </div>
                    </div>
                );
            } else {
                if (!hasSalesOrderShape(order)) {
                    return getEmptyOrderDetailsMarkup(!isMobileViewport());
                }

                const productsList = getSalesOrderProducts(order).map(product => {
                    const orderCurrencySymbol = getOrderCurrencySymbol(order);
                    const metaData = getOrderProductMeta(product);

                    return (
                        <div className="ddwcpos-order-product" key={getProductKey(product)}>
                            <div className="ddwcpos-order-product-info">
                                <div className="ddwcpos-order-product-image" dangerouslySetInnerHTML={{ __html: getOrderProductImage(product) }}></div>
                                <div className="ddwcpos-order-product-details">
                                    <h4 title={ReactHtmlParser(product.name)}>{ReactHtmlParser(product.name)}</h4>
                                    <p>
                                        {formatPrice(product.uf, orderCurrencySymbol)} x {product.quantity}
                                    </p>
                                </div>
                                <div className="ddwcpos-order-product-price">
                                    <p>
                                        {formatPrice(product.uf_total, orderCurrencySymbol)}
                                    </p>
                                </div>
                            </div>
                            {metaData.length ? <div className="ddwcpos-order-product-meta-info">{metaData}</div> : null}
                        </div>
                    );
                });

                const { taxHTML, totalTax } = getOrderTaxMarkup(order, orderType);
                const tenderedHTML = getTenderedMarkup(order);
                const couponHTML = getOrderCouponsMarkup(order, orderType);
                const feesHTML = getOrderFeesMarkup(order);
                const orderDiscount = getOrderDiscountAmount(order, orderType, totalTax);
                const orderCurrencySymbol = getOrderCurrencySymbol(order);

                return (
                    <div className="ddwcpos-order-details-wrapper" style={getOrderDetailsWrapperStyle(!isMobileViewport())}>
                        <div className="ddwcpos-order-details-header">
                            <h2>{sprintf(__('Order #%s', 'devdiggers-multipos-for-woocommerce'), order.order_id)}</h2>
                            <p>
                                <UserOutlined />
                                {getOrderEmail(order)}
                            </p>
                            {isMobileViewport() ?
                                <button className="ddwcpos-button ddwcpos-button-secondary" onClick={this.handleBackOrderDetailsMobile}>{__('X', 'devdiggers-multipos-for-woocommerce')}</button>
                                : null}
                        </div>
                        <div className="ddwcpos-order-products">
                            {productsList}
                        </div>
                        <div className="ddwcpos-order-totals">
                            <div>
                                <p>{__('Subtotal', 'devdiggers-multipos-for-woocommerce')}</p>
                                <strong>{formatPrice(order.order_subtotal, orderCurrencySymbol)}</strong>
                            </div>
                            {taxHTML}
                            {feesHTML}
                            <div>
                                <p>{__('Discount', 'devdiggers-multipos-for-woocommerce')}</p>
                                <strong>{formatPrice(orderDiscount, orderCurrencySymbol)}</strong>
                            </div>
                            <div>
                                <p>{__('Applied Coupon(s)', 'devdiggers-multipos-for-woocommerce')}</p>
                                <strong>{couponHTML.length ? couponHTML : __('N/A', 'devdiggers-multipos-for-woocommerce')}</strong>
                            </div>
                            {''}
                            <div className="ddwcpos-order-total">
                                <p>{__('Total', 'devdiggers-multipos-for-woocommerce')}</p>
                                <strong>{formatPrice(order.order_total, orderCurrencySymbol)}</strong>
                            </div>
                            {tenderedHTML}
                            <div>
                                <p>{__('Change', 'devdiggers-multipos-for-woocommerce')}</p>
                                <strong>{formatPrice(order.change, orderCurrencySymbol)}</strong>
                            </div>
                            {''}
                            <button className="ddwcpos-button" onClick={this.createPrintInvoiceHandler(order)}>
                                <PrinterOutlined />
                                {__('Print Invoice', 'devdiggers-multipos-for-woocommerce')}
                            </button>
                        </div>
                        {''}
                    </div>
                );
            }
        }
    }
}

const mapStateToProps = state => ({
    orders: state.orders,
});

const mapDispatchToProps = dispatch => bindActionCreators({
    addHoldCartToCurrentCart,
    deleteHoldCart,
    updateDefaultCustomer,
    printInvoice,
    updateDefaultTable,
}, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(OrderDetails);
