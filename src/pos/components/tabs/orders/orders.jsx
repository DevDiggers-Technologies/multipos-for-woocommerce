import React, { Component, Fragment } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import { sprintf, __ } from '@wordpress/i18n';
import { SearchOutlined, WarningFilled, FieldTimeOutlined, UserOutlined, RightOutlined, TableOutlined } from '@ant-design/icons';
import { FixedSizeList as List } from "react-window";
import { Link } from 'react-router-dom';
import { getOrders, loadSearchedOrders } from './../../../actions/orders';
import { formatPrice } from '../../../utils/currency-format';
import { getHoldCarts } from './../../../actions/hold-carts';
import { getCoupons } from './../../../actions/coupon';
import { getFees } from '../../../actions/fees';
import { getCart } from './../../../actions/cart';
import { getSettings } from './../../../actions/settings';
import OrderDetails from './orderDetails/orderDetails.jsx';
import { getPosRoute } from '../../../services/routes';
import { getPosConfig } from '../../../services/runtime';
import { getOrderDetailsPanel, getRouteType, isMobileViewport } from '../../../utils/navigation';
import { getHoldOrderRecord, getOrderCurrencySymbol, getOrderProducts } from '../../../utils/orders';
import { getOutletId, getOutletMode, isSameId, parseWholeNumber } from '../../../utils/value';

const hasHoldCartShape = order => Boolean(order) && Array.isArray(order.list) && order.list.length > 0;
const hasOrderShape = order => Boolean(order) && order.order_id !== undefined && order.order_id !== null;
const getSelectedHoldOrder = (selectedOrder, holdCarts) => (
    hasHoldCartShape(selectedOrder) ? selectedOrder : (holdCarts.length ? holdCarts[0] : null)
);
const getSelectedSalesOrder = (selectedOrder, orders) => (
    hasOrderShape(selectedOrder) ? selectedOrder : (orders.length ? orders[0] : null)
);
const getOrderTabClassName = (currentOrderType, targetOrderType) => currentOrderType === targetOrderType ? 'ddwcpos-active' : '';
const getSelectedRowClassName = isSelected => isSelected ? 'ddwcpos-list-details ddwcpos-list-details-active' : 'ddwcpos-list-details';
const getOrdersResultsLabel = records => `${records.length} ${__('Results', 'devdiggers-multipos-for-woocommerce')}`;
const getHoldCartId = holdCart => {
    const holdCartRecord = getHoldOrderRecord(holdCart);
    return holdCartRecord ? holdCartRecord.id : null;
};
const isSelectedHoldCart = (selectedOrder, cart, index) => (
    (!selectedOrder && index === 0)
    || (hasHoldCartShape(selectedOrder) && hasHoldCartShape(cart) && isSameId(getHoldCartId(selectedOrder), getHoldCartId(cart)))
);
const isSelectedOrder = (selectedOrder, order, index) => (
    (!selectedOrder && index === 0)
    || (hasOrderShape(selectedOrder) && hasOrderShape(order) && isSameId(selectedOrder.order_id, order.order_id))
);
const getOrderSearchPlaceholder = outletMode => outletMode === 'restaurant' ? __('Search By Customer or Table...', 'devdiggers-multipos-for-woocommerce') : __('Search By Customer...', 'devdiggers-multipos-for-woocommerce');
const getSafeOrderList = records => Array.isArray(records) ? records : [];
const getOrdersState = orders => orders || {};
const getSearchedOrders = orders => getSafeOrderList(getOrdersState(orders).list);
const getSalesOrders = orders => getSafeOrderList(getOrdersState(orders).sorder);
const sortHoldCartsDescending = holdCarts => [...holdCarts]
    .filter(hasHoldCartShape)
    .sort((firstCart, secondCart) => (
        getHoldCartId(firstCart) < getHoldCartId(secondCart) ? 1 : (getHoldCartId(secondCart) < getHoldCartId(firstCart) ? -1 : 0)
    ));
const sortOrdersDescending = orders => [...orders].sort((firstOrder, secondOrder) => new Date(secondOrder.order_created) - new Date(firstOrder.order_created));
const getProductsQuantityTotal = products => getSafeOrderList(products).reduce((total, product) => total + parseWholeNumber(product.quantity), 0);
const showMobileOrderDetailsPanel = () => {
    if (!isMobileViewport()) {
        return;
    }

    const detailsWrapper = getOrderDetailsPanel();

    if (detailsWrapper) {
        detailsWrapper.style.position = 'fixed';
        detailsWrapper.style.display = 'block';
    }
};
const getFilteredHoldCarts = (holdCarts, searchValue) => {
    const search = searchValue.toLowerCase();
    const containsSearch = value => value && String(value).toLowerCase().includes(search);
    const getHoldCustomer = holdCartRecord => holdCartRecord && holdCartRecord.customer ? holdCartRecord.customer : {};
    const getHoldTable = holdCartRecord => holdCartRecord && holdCartRecord.table ? holdCartRecord.table : {};

    if (!search) {
        return sortHoldCartsDescending(holdCarts);
    }

    return sortHoldCartsDescending(holdCarts.filter(cart => {
        const holdCartRecord = getHoldOrderRecord(cart);

        if (!holdCartRecord) {
            return false;
        }

        const holdCustomer = getHoldCustomer(holdCartRecord);
        const holdTable = getHoldTable(holdCartRecord);

        return (
            containsSearch(holdCustomer.first_name)
            || containsSearch(holdCustomer.last_name)
            || containsSearch(holdCustomer.email)
            || containsSearch(holdCustomer.phone)
            || containsSearch(holdTable.name)
        );
    }));
};
const getFilteredSalesOrders = (orders, orderType, component) => (
    sortOrdersDescending(
        getSafeOrderList(orders).filter(order => order.order_type === orderType)
    )
);
const getHoldOrderRowLabel = (holdCartsLength, index) => sprintf(__('Cart #%s', 'devdiggers-multipos-for-woocommerce'), holdCartsLength - index);
const getSalesOrderRowLabel = order => sprintf(__('Order #%s', 'devdiggers-multipos-for-woocommerce'), order.order_id);
const getHoldCartTableName = holdCartRecord => holdCartRecord && holdCartRecord.table ? holdCartRecord.table.name : '';
const getHoldCartByIndex = (holdCarts, index) => getSafeOrderList(holdCarts)[index];
const getSalesOrderByIndex = (orders, index) => getSafeOrderList(orders)[index];
const getHoldCartCustomer = holdCartRecord => holdCartRecord && holdCartRecord.customer ? holdCartRecord.customer : {};
const getHoldCartTotal = holdCart => holdCart && holdCart.total ? holdCart.total : {};
const shouldShowOrderStatus = () => Boolean(getPosConfig().show_order_status_enabled);

class Orders extends Component {
    constructor(props) {
        super(props);

        this.state = {
            search: '',
            order: null,
        }
    }

    componentDidMount = () => {
        this._isMounted = true;
        this.props.getCoupons();
        this.props.getFees();
        this.props.getHoldCarts();
        this.props.getCart();
        this.props.getOrders(getOutletId(this.props.outlet));
        this.props.getSettings();

    }

    componentWillUnmount = () => {
        this._isMounted = false;
    }

    getOrderTabLinks = orderType => [
        <Link key="online" className={getOrderTabClassName(orderType, 'online')} to={getPosRoute('/orders/online')}>{__('Online', 'devdiggers-multipos-for-woocommerce')}</Link>,
        <Link key="hold" className={getOrderTabClassName(orderType, 'hold')} to={getPosRoute('/orders/hold')}>{__('Hold', 'devdiggers-multipos-for-woocommerce')}</Link>
    ]

    getOrderStatusMarkup = (order) => {
        if (!order) {
            return '';
        }
        const status = order.order_status || '';
        const label = order.order_status_label || status;
        if (!status) {
            return '';
        }
        const orderIcon = '<svg class="ddwcpos-status-icon" viewBox="0 0 24 24" width="11" height="11" fill="currentColor" aria-hidden="true"><path d="M19 7h-3V6a4 4 0 00-8 0v1H5a1 1 0 00-1 1v11a3 3 0 003 3h10a3 3 0 003-3V8a1 1 0 00-1-1zm-9-1a2 2 0 014 0v1h-4V6zm8 13a1 1 0 01-1 1H7a1 1 0 01-1-1V9h2v1a1 1 0 002 0V9h4v1a1 1 0 002 0V9h2v10z"/></svg>';

        return sprintf('<mark class="ddwcpos-order-status status-%s" title="%s">%s<span>%s</span></mark>',
            status,
            sprintf(__('Order Status: %s', 'devdiggers-multipos-for-woocommerce'), label),
            orderIcon,
            label
        );
    }

    componentDidUpdate = prevProps => {
        if (getRouteType(prevProps, 'online') !== getRouteType(this.props, 'online')) {
            this.setState({
                order: null
            });
            return;
        }

        const currentOrderType = getRouteType(this.props, 'online');

        if (currentOrderType === 'hold' && this.state.order && !hasHoldCartShape(this.state.order)) {
            this.setState({
                order: null
            });
            return;
        }

        if (currentOrderType !== 'hold' && this.state.order && !hasOrderShape(this.state.order)) {
            this.setState({
                order: null
            });
        }
    }

    handleOrderSearch = e => {
        this.setState({
            search: e.target.value
        });

        this.props.loadSearchedOrders(e.target.value, getSearchedOrders(this.props.orders));
    }

    handleChangeOrderDetails = order => {
        this.setState({
            order: order
        });

        showMobileOrderDetailsPanel();
    }

    handleHoldOrderSearch = e => {
        this.setState({
            search: e.target.value
        });
    }

    createOrderDetailsHandler = order => () => this.handleChangeOrderDetails(order)
    handleOrderSearchChange = e => this.handleOrderSearch(e)

    render() {
        const routeOrderType = getRouteType(this.props, 'online');
        const orderType = routeOrderType === 'hold' ? 'hold' : 'online';
        const rowHeight = isMobileViewport() ? 92 : 80;
        const holdCartsState = getSafeOrderList(this.props.holdCarts);
        const outletMode = getOutletMode(this.props.outlet);

        if (orderType === 'hold' && holdCartsState.length) {
            const holdCarts = getFilteredHoldCarts(holdCartsState, this.state.search);

            const Row = ({ index, style }) => {
                const holdCart = getHoldCartByIndex(holdCarts, index);
                const holdCartRecord = getHoldOrderRecord(holdCart);

                if (!holdCartRecord) {
                    return null;
                }

                let orderId = getHoldOrderRowLabel(holdCarts.length, index);
                const holdCustomer = getHoldCartCustomer(holdCartRecord);
                const holdTotal = getHoldCartTotal(holdCart);

                orderId = orderId;

                const totalQuantity = getProductsQuantityTotal(holdCartRecord.cart);

                const className = getSelectedRowClassName(isSelectedHoldCart(this.state.order, holdCart, index));

                return (
                    <div className="ddwcpos-list-row" style={style}>
                        <div className={className} onClick={this.createOrderDetailsHandler(holdCart)}>
                            <RightOutlined />
                            <div>
                                <h3>{orderId}</h3>
                                <p>
                                    <FieldTimeOutlined />
                                    {holdCartRecord.date}
                                </p>
                                <p>
                                    <UserOutlined />
                                    {holdCustomer.email}
                                    {getHoldCartTableName(holdCartRecord) ?
                                        <Fragment>
                                            <TableOutlined />
                                            {getHoldCartTableName(holdCartRecord)}
                                        </Fragment>
                                        : null}
                                </p>
                            </div>
                            <div className="ddwcpos-order-total-cell">
                                <h3>{formatPrice(holdTotal.cart_total)}</h3>
                                <p>{sprintf(__('%d Item(s)', 'devdiggers-multipos-for-woocommerce'), totalQuantity)}</p>
                            </div>
                        </div>
                    </div>
                );
            }

            return (
                <div className="ddwcpos-orders-tab-wrapper">
                    <div className="ddwcpos-list-wrapper">
                        <div className="ddwcpos-tab-changer">
                            {this.getOrderTabLinks(orderType)}
                            {''}
                        </div>
                        <div className="ddwcpos-search-wrapper">
                            <div className="ddwcpos-search-input-wrapper">
                                <div className="ddwcpos-search-input">
                                    <SearchOutlined />
                                    <input type="text" className="ddwcpos-form-control" value={this.state.search} placeholder={getOrderSearchPlaceholder(outletMode)} onChange={this.handleHoldOrderSearch} autoComplete="off" />
                                </div>
                                <span>{getOrdersResultsLabel(holdCarts)}</span>
                            </div>
                        </div>
                        {
                            holdCarts.length ?
                                <List
                                    className="ddwcpos-list"
                                    height={800}
                                    itemCount={holdCarts.length}
                                    itemSize={rowHeight}
                                >
                                    {Row}
                                </List>
                                :
                                <div className="ddwcpos-no-results">
                                    <WarningFilled />
                                    <p>{__('No Orders Found', 'devdiggers-multipos-for-woocommerce')}</p>
                                </div>
                        }
                    </div>

                    <OrderDetails order={getSelectedHoldOrder(this.state.order, holdCarts)} handleChangeOrderDetails={this.handleChangeOrderDetails} orderType={orderType} {...this.props} />
                </div>
            );
        } else {
            const orders = getFilteredSalesOrders(getSalesOrders(this.props.orders), orderType, this);

            const Row = ({ index, style }) => {
                const orderRecord = getSalesOrderByIndex(orders, index);
                if (!orderRecord) {
                    return null;
                }
                let orderId = getSalesOrderRowLabel(orderRecord);

                const totalQuantity = getProductsQuantityTotal(getOrderProducts(orderRecord));

                const className = getSelectedRowClassName(isSelectedOrder(this.state.order, orderRecord, index));
                const orderStatus = shouldShowOrderStatus() ? this.getOrderStatusMarkup(orderRecord) : '';

                return (
                    <div className="ddwcpos-list-row" style={style}>
                        <div className={className} onClick={this.createOrderDetailsHandler(orderRecord)}>
                            <RightOutlined />
                            <div>
                                <h3>{orderId}</h3>
                                <p>
                                    <FieldTimeOutlined />
                                    {orderRecord.order_date}
                                </p>
                                <p>
                                    <UserOutlined />
                                    {orderRecord.email}
                                </p>
                            </div>
                            {'' || null}
                            {orderStatus ?
                                <div className="ddwcpos-order-status-cell" dangerouslySetInnerHTML={{ __html: orderStatus }}></div>
                                : null}
                            <div className="ddwcpos-order-total-cell">
                                <h3>{formatPrice(orderRecord.order_total, getOrderCurrencySymbol(orderRecord))}</h3>
                                <p>{sprintf(__('%d Item(s)', 'devdiggers-multipos-for-woocommerce'), totalQuantity)}</p>
                            </div>
                        </div>
                    </div>
                );
            }

            return (
                <div className="ddwcpos-orders-tab-wrapper">
                    <div className="ddwcpos-list-wrapper">
                        <div className="ddwcpos-tab-changer">
                            {this.getOrderTabLinks(orderType)}
                            {''}
                        </div>
                        <div className="ddwcpos-search-wrapper">
                            <div className="ddwcpos-search-input-wrapper">
                                <div className="ddwcpos-search-input">
                                    <SearchOutlined />
                                    <input type="text" className="ddwcpos-form-control" value={this.state.search} placeholder={__('Search Order...', 'devdiggers-multipos-for-woocommerce')} onChange={this.handleOrderSearchChange} autoComplete="off" />
                                </div>
                                <span>{getOrdersResultsLabel(orders)}</span>
                            </div>
                        </div>
                        {
                            orders.length ?
                                <List
                                    className="ddwcpos-list"
                                    height={800}
                                    itemCount={orders.length}
                                    itemSize={rowHeight}
                                >
                                    {Row}
                                </List>
                                :
                                <div className="ddwcpos-no-results">
                                    <WarningFilled />
                                    <p>{__('No Orders Found', 'devdiggers-multipos-for-woocommerce')}</p>
                                </div>
                        }
                    </div>

                    <OrderDetails order={getSelectedSalesOrder(this.state.order, orders)} handleChangeOrderDetails={this.handleChangeOrderDetails} orderType={orderType} {...this.props} />
                </div>
            );
        }
    }
}

const mapStateToProps = state => ({
    orders: state.orders,
    cart: state.cart,
    holdCarts: state.holdCarts,
});

const mapDispatchToProps = dispatch => bindActionCreators({
    getOrders,
    loadSearchedOrders,
    getHoldCarts,
    getCoupons,
    getFees,
    getCart,
    getSettings
}, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(Orders);
