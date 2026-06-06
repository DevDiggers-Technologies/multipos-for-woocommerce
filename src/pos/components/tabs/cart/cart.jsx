import React, { Component, Fragment } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import { __ } from '@wordpress/i18n';
import { EditFilled, PauseOutlined, RedoOutlined, ShoppingCartOutlined, UserOutlined, WifiOutlined } from '@ant-design/icons';
import { store } from 'react-notifications-component';
import { Link } from 'react-router-dom';
import CartProduct from './cartProduct/cartProduct.jsx';
import CartTotal from './cartTotal/cartTotal.jsx';
import { getCart, resetActiveCartStorage } from '../../../actions/cart';
import { getCoupons } from '../../../actions/coupon';
import { getFees } from '../../../actions/fees';
import { getHoldCarts } from './../../../actions/hold-carts';
import { getOrders } from './../../../actions/orders';
import { updateDefaultTable } from '../../../actions/tables';
import { getCustomers } from '../../../actions/customers';
import { getTables } from './../../../actions/tables';
import Popup from '../../popup/popup.jsx';
import { isInternetConnected } from '../../../services/connectivity';
import { notify } from '../../../services/notifications';
import { getPosRoute } from '../../../services/routes';
import { getActiveCartRecordFromState } from '../../../services/cart-session';
import { pushRoute } from '../../../utils/navigation';
import { getOutletId, getOutletMode, hasEntries } from '../../../utils/value';
import TableServiceIcon from '../../icons/tableServiceIcon.jsx';

const hasEntity = entity => hasEntries(entity);
const getSafeCartList = cart => Array.isArray(cart && cart.list) ? cart.list : [];
const getSafeHoldCarts = holdCarts => Array.isArray(holdCarts) ? holdCarts : [];
const getCustomersState = customers => customers || {};
const getTablesState = tables => tables || {};

const getDefaultCustomerName = defaultCustomer => {
    if (!hasEntries(defaultCustomer)) {
        return '';
    }

    const customerName = `${defaultCustomer.first_name} ${defaultCustomer.last_name}`;

    return customerName !== ' ' ? customerName : defaultCustomer.username;
};

const getCartHeaderStyle = (outletMode, context) => {
    let style = {};

    if (outletMode === 'restaurant') {
        style.gridTemplateColumns = 'max-content minmax(0, 1fr) minmax(0, 1fr)';
    }

    return style;
};

const TakeAwayModeIcon = () => (
    <svg data-name="Layer 1" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
        <path d="M46.958,42.1712A14.9968,14.9968,0,0,0,33,28.0507V27a1,1,0,0,0-2,0v1.0507A14.9967,14.9967,0,0,0,17.042,42.1712,2.9918,2.9918,0,0,0,18,48H46a2.9918,2.9918,0,0,0,.958-5.8288ZM32,30A13.0086,13.0086,0,0,1,44.9493,42H19.0507A13.0086,13.0086,0,0,1,32,30ZM46,46H18a1,1,0,0,1,0-2H46a1,1,0,0,1,0,2ZM56.9883,19.8481,55.04,7.1864A2.9831,2.9831,0,0,0,56,5V3a3.0033,3.0033,0,0,0-3-3H11A3.0033,3.0033,0,0,0,8,3V5a2.9831,2.9831,0,0,0,.96,2.1864L7.0117,19.8481A.9914.9914,0,0,0,7,20V61a3.0033,3.0033,0,0,0,3,3H54a3.0033,3.0033,0,0,0,3-3V20A.9914.9914,0,0,0,56.9883,19.8481ZM10,3a1.0009,1.0009,0,0,1,1-1H53a1.0009,1.0009,0,0,1,1,1V5a1.0009,1.0009,0,0,1-1,1H35V4a1,1,0,0,0-1-1H30a1,1,0,0,0-1,1V6H11a1.0009,1.0009,0,0,1-1-1ZM33,5v6H31V5ZM55,61a1.0009,1.0009,0,0,1-1,1H10a1.0009,1.0009,0,0,1-1-1V20.0767L10.8584,8H29v4a1,1,0,0,0,1,1h4a1,1,0,0,0,1-1V8H53.1416L55,20.0767Z" />
    </svg>
);

class Cart extends Component {

    constructor(props) {
        super(props);

        this.state = {
            showSelectTablePopup: false,
            online: isInternetConnected()
        }
    }

    componentDidMount = () => {
        this.props.getCoupons();
        this.props.getFees();
        this.props.getCart();
        this.props.getHoldCarts();
        this.props.getOrders(getOutletId(this.props.outlet));
        this.props.getTables();

        window.addEventListener('online', this.handleBrowserOnline);
        window.addEventListener('offline', this.handleBrowserOffline);
    }

    componentWillUnmount = () => {
        window.removeEventListener('online', this.handleBrowserOnline);
        window.removeEventListener('offline', this.handleBrowserOffline);
    }

    handleBrowserOnline = () => this.setState({ online: true });

    handleBrowserOffline = () => this.setState({ online: false });

    handleResetCartButton = () => {
        const cartList = getSafeCartList(this.props.cart);
        if (cartList.length) {
            this.props.resetActiveCartStorage().then(() => {
                this.props.getCoupons();
                this.props.getFees();
                this.props.getCart();
            });
        } else {
            notify({
                title: __('Empty Cart', 'devdiggers-multipos-for-woocommerce'),
                message: __('Cart is already empty.', 'devdiggers-multipos-for-woocommerce'),
                type: 'info',
            });
        }
    }

    handleToggleShowSelectTablePopup = () => {
        this.setState(prevState => ({
            showSelectTablePopup: !prevState.showSelectTablePopup
        }));
    }

    handleSelectMode = mode => {
        this.handleToggleShowSelectTablePopup();

        if (mode === 'dine_in') {
            pushRoute(this.props.history, getPosRoute('/tables/all'));
        } else if (mode === 'take_away') {
            this.props.updateDefaultTable();
        }
    }

    handleSelectDineInMode = () => this.handleSelectMode('dine_in')
    handleSelectTakeAwayMode = () => this.handleSelectMode('take_away')

    render() {
        const activeCartRecord = getActiveCartRecordFromState({ cart: this.props.cart });
        const cart = getSafeCartList(this.props.cart);
        const defaultCustomer = getCustomersState(this.props.customers).defaultCustomer || {};
        const defaultTable = getTablesState(this.props.tables).defaultTable || {};
        const holdCartLength = getSafeHoldCarts(this.props.holdCarts).length;
        let cartProducts = [];

        if (activeCartRecord && Array.isArray(activeCartRecord.cart)) {
            cartProducts = activeCartRecord.cart.map(cartProduct => {
                const cartProductKey = cartProduct.key || `${cartProduct.product_id}-${cartProduct.name}`;

                return <CartProduct key={cartProductKey} cartProduct={cartProduct} {...this.props}></CartProduct>;
            });
        }

        const wifiButtonStyle = {
            color: this.state.online ? 'green' : 'red'
        };

        const defaultCustomerName = getDefaultCustomerName(defaultCustomer);
        const outletMode = getOutletMode(this.props.outlet);
        const style = getCartHeaderStyle(outletMode, this);

        let selectTablePopupHTML = null;

        if (this.state.showSelectTablePopup) {
            const popupContent = (
                <Fragment>
                    <h2>{__('Select Mode', 'devdiggers-multipos-for-woocommerce')}</h2>
                    <div className="ddwcpos-mode-options">
                        <span onClick={this.handleSelectDineInMode}>
                            <TableServiceIcon />
                            <p>{__('Dine in', 'devdiggers-multipos-for-woocommerce')}</p>
                        </span>
                        <span onClick={this.handleSelectTakeAwayMode}>
                            <TakeAwayModeIcon />
                            <p>{__('Take Away', 'devdiggers-multipos-for-woocommerce')}</p>
                        </span>
                    </div>
                </Fragment>
            );

            const selectTablePopupProps = {
                handleOverlay: this.handleToggleShowSelectTablePopup,
                popupContent: popupContent,
                hideActions: true,
            };

            selectTablePopupHTML = <Popup {...selectTablePopupProps} />
        }

        return (
            <div className="ddwcpos-cart-wrapper">
                <div className="ddwcpos-cart-header" style={style}>
                    <div className="ddwcpos-icon-card" style={wifiButtonStyle} title={this.state.online ? __('Online', 'devdiggers-multipos-for-woocommerce') : __('Offline', 'devdiggers-multipos-for-woocommerce')}>
                        <WifiOutlined />
                    </div>
                    <Link className="ddwcpos-button" to={getPosRoute('/customers')}>
                        {hasEntity(defaultCustomer) ?
                            <Fragment>
                                <img src={defaultCustomer.avatar_url || ''} alt={defaultCustomer.username || defaultCustomerName} width="30" height="30" />
                                <p>{defaultCustomerName}</p>
                                <EditFilled />
                            </Fragment>
                            :
                            <Fragment>
                                <UserOutlined />
                                <p>{__('Select Customer', 'devdiggers-multipos-for-woocommerce')}</p>
                            </Fragment>
                        }
                    </Link>
                    {outletMode === 'restaurant' ?
                        <button className="ddwcpos-button ddwcpos-button-green" onClick={this.handleToggleShowSelectTablePopup}>
                            {hasEntity(defaultTable) ?
                                <Fragment>
                                    <span className="ddwcpos-table-icon" role="img">
                                        <TableServiceIcon />
                                    </span>
                                    <p>{defaultTable.name}</p>
                                    <EditFilled />
                                </Fragment>
                                :
                                <Fragment>
                                    <span className="ddwcpos-table-icon" role="img">
                                        <TableServiceIcon />
                                    </span>
                                    <p>{__('Select Table', 'devdiggers-multipos-for-woocommerce')}</p>
                                </Fragment>
                            }
                        </button>
                        : null}
                </div>
                <div className="ddwcpos-cart-products-list">
                    <div className="ddwcpos-cart-header">
                        <h2>
                            <ShoppingCartOutlined />
                            {__('Cart Items', 'devdiggers-multipos-for-woocommerce')}
                        </h2>
                        <Fragment>
                            <Link to={getPosRoute('/orders/hold')}>
                                <div className="ddwcpos-icon-card" onClick={this.handleSyncOrdersButton} title={__('Check Hold Orders', 'devdiggers-multipos-for-woocommerce')}>
                                    {holdCartLength ?
                                        <span className="ddwcpos-card-count">{holdCartLength}</span>
                                        : null}
                                    <PauseOutlined />
                                </div>
                            </Link>
                            <div className="ddwcpos-icon-card" onClick={this.handleResetCartButton} title={__('Reset Cart', 'devdiggers-multipos-for-woocommerce')}>
                                <RedoOutlined />
                            </div>
                        </Fragment>
                    </div>
                    <div className={'ddwcpos-cart-products' + ''}>
                        {cartProducts.length > 0 ? cartProducts : (
                            <div className="ddwcpos-empty-cart">
                                <ShoppingCartOutlined />
                                <p>{__('Your cart is empty', 'devdiggers-multipos-for-woocommerce')}</p>
                            </div>
                        )}
                    </div>
                </div>
                <CartTotal {...this.props} />
                {selectTablePopupHTML}
            </div>
        );
    }
}

const mapStateToProps = state => ({
    cart: state.cart,
    holdCarts: state.holdCarts,
    taxes: state.taxes,
    coupon: state.coupon,
    fees: state.fees,
    customers: state.customers,
    orders: state.orders,
    tables: state.tables
});

const mapDispatchToProps = dispatch => bindActionCreators({
    getCart,
    resetActiveCartStorage,
    getCoupons,
    getFees,
    getHoldCarts,
    getOrders,
    updateDefaultTable,
    getCustomers,
    getTables
}, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(Cart);
