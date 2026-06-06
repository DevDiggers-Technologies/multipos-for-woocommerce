import React, { Component } from 'react';
import { __, sprintf } from '@wordpress/i18n';
import { Link } from 'react-router-dom';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import OrderDetails from './../orders/orderDetails/orderDetails.jsx';
import { getTables, updateDefaultTable } from './../../../actions/tables';
import { getCart } from './../../../actions/cart';
import { getHoldCarts } from './../../../actions/hold-carts';
import { CheckCircleOutlined, CheckOutlined, ClockCircleOutlined, SearchOutlined, ShoppingCartOutlined, TeamOutlined } from '@ant-design/icons';
import Popup from '../../popup/popup.jsx';
import { getPosRoute } from '../../../services/routes';
import { formatPrice } from '../../../utils/currency-format';
import { getRouteType } from '../../../utils/navigation';
import { getHoldOrderRecord } from '../../../utils/orders';
import { isSameId, parseWholeNumber } from '../../../utils/value';

const getTableFilterClass = (currentType, filterType) => (
    currentType === filterType ? 'ddwcpos-active' : ''
);
const getMatchingTableOrder = (holdCarts, table) => (
    (Array.isArray(holdCarts) ? holdCarts : []).find(holdCart => {
        const holdCartRecord = getHoldOrderRecord(holdCart);

        return Boolean(
            holdCartRecord &&
            holdCartRecord.table &&
            holdCartRecord.table.name &&
            isSameId(holdCartRecord.table.slug, table.slug)
        );
    }) || null
);

const matchesTableFilter = (tableType, status) => tableType === 'all' || status === tableType;

const matchesSeatSearch = (table, search) => (
    !search || parseWholeNumber(table.seats) >= parseWholeNumber(search)
);
const getTablesResultsLabel = tables => `${tables.length} ${__('Results', 'devdiggers-multipos-for-woocommerce')}`;
const getTableCountByType = (tables, getTableStatus, type) => tables.filter(table => getTableStatus(table) === type).length;
const getTableDisplayNumber = (table, index) => {
    const name = table && table.name ? String(table.name) : '';
    const numberMatch = name.match(/\d+/);
    const number = numberMatch ? parseWholeNumber(numberMatch[0]) : index + 1;

    return String(number).padStart(2, '0');
};

const getTablePopupStyle = () => (window.screen.width < 768 ? {} : { width: '50%' });
const getFilteredTables = (tables, tableType, search, getTableStatus) => (Array.isArray(tables) ? tables : []).filter(table => {
    const type = getTableStatus(table);

    return matchesTableFilter(tableType, type) && matchesSeatSearch(table, search);
});
const getTablesState = tables => tables || {};

class Tables extends Component {
    constructor(props) {
        super(props);

        this.state = {
            search: '',
            order: null,
            showTableOrderPopup: false,
        }
    }

    componentDidMount = () => {
        this.props.getTables();
        this.props.getCart();
        this.props.getHoldCarts();
    }

    handleToggleTableOrderPopup = () => {
        this.setState(prevState => ({
            showTableOrderPopup: !prevState.showTableOrderPopup,
            order: null,
        }));
    }

    handleTableSearch = e => {
        this.setState({
            search: e.target.value
        });
    }
    handleTableSearchInput = e => this.handleTableSearch(e)

    handleSetTable = tableSlug => {
        this.props.updateDefaultTable(tableSlug);
    }

    handleViewOrder = order => {
        this.setState(prevState => ({
            order: order,
            showTableOrderPopup: !prevState.showTableOrderPopup,
        }));
    }

    getTableStatus = table => table.tableType ? table.tableType : 'vacant'

    getTableOrder = table => getMatchingTableOrder(this.props.holdCarts, table)
    createSetTableHandler = tableSlug => () => this.handleSetTable(tableSlug)
    createViewOrderHandler = order => () => this.handleViewOrder(order)

    renderTableAction = (type, isCurrent, table, order) => {
        if (isCurrent) {
            return (
                <button type="button" className="ddwcpos-table-btn ddwcpos-table-btn-current" disabled>
                    <CheckOutlined />
                    <span>{__('Current Table', 'devdiggers-multipos-for-woocommerce')}</span>
                </button>
            );
        }
        if (type === 'occupied' && order) {
            return (
                <button type="button" className="ddwcpos-table-btn ddwcpos-table-btn-view" onClick={this.createViewOrderHandler(order)}>
                    <ShoppingCartOutlined />
                    <span>{__('View Order', 'devdiggers-multipos-for-woocommerce')}</span>
                </button>
            );
        }
        if (type === 'vacant') {
            return (
                <button type="button" className="ddwcpos-table-btn ddwcpos-table-btn-set" onClick={this.createSetTableHandler(table.slug)}>
                    <CheckOutlined />
                    <span>{__('Set Table', 'devdiggers-multipos-for-woocommerce')}</span>
                </button>
            );
        }
        return <span className="ddwcpos-table-btn ddwcpos-table-btn-placeholder" aria-hidden="true"></span>;
    }

    renderTableCard = (table, index) => {
        const defaultTable = getTablesState(this.props.tables).defaultTable || {};
        const type = this.getTableStatus(table);
        const order = this.getTableOrder(table);
        const isCurrent = defaultTable && isSameId(defaultTable.slug, table.slug);
        const statusIcon = type === 'occupied' ? <ClockCircleOutlined /> : <CheckCircleOutlined />;
        const orderTotal = order && order.total && typeof order.total === 'object' ? order.total.cart_total : null;
        const amountLabel = orderTotal != null && orderTotal !== '' ? formatPrice(orderTotal) : '';
        const tableNumber = getTableDisplayNumber(table, index);
        const bottomLabel = type === 'vacant'
            ? __('Vacant', 'devdiggers-multipos-for-woocommerce')
            : (amountLabel || table.name || __('Occupied', 'devdiggers-multipos-for-woocommerce'));
        const wrapperClass = `ddwcpos-table-card-wrapper ddwcpos-table-card-wrapper-${type}${isCurrent ? ' ddwcpos-table-card-wrapper-current' : ''}`;
        const blockClass = `ddwcpos-table-block ddwcpos-${type}${isCurrent ? ' ddwcpos-current' : ''}`;

        return (
            <div className={wrapperClass}>
                <div className="ddwcpos-table-card">
                    <div className={blockClass}>
                        <i className="ddwcpos-chair ddwcpos-chair-top" aria-hidden="true"></i>
                        <i className="ddwcpos-chair ddwcpos-chair-right" aria-hidden="true"></i>
                        <i className="ddwcpos-chair ddwcpos-chair-bottom" aria-hidden="true"></i>
                        <i className="ddwcpos-chair ddwcpos-chair-left" aria-hidden="true"></i>
                        <div className="ddwcpos-table-block-meta">
                            <span className="ddwcpos-table-seats"><TeamOutlined /> {table.seats}</span>
                            <span className={`ddwcpos-table-statusicon ddwcpos-table-statusicon-${type}`}>{statusIcon}</span>
                        </div>
                        <div className="ddwcpos-table-number">
                            <h3>{tableNumber}</h3>
                            <p className={`ddwcpos-table-bottom ddwcpos-table-bottom-${type}`}>{bottomLabel}</p>
                        </div>
                    </div>
                </div>
                {this.renderTableAction(type, isCurrent, table, order)}
            </div>
        );
    }

    render() {
        const tableType = getRouteType(this.props, 'all');

        const search = this.state.search;
        const tableState = getTablesState(this.props.tables);
        const tables = getFilteredTables(tableState.list, tableType, search, this.getTableStatus);
        const allTables = Array.isArray(tableState.list) ? tableState.list : [];
        const occupiedCount = getTableCountByType(allTables, this.getTableStatus, 'occupied');
        const vacantCount = getTableCountByType(allTables, this.getTableStatus, 'vacant');
        const currentTable = tableState.defaultTable && tableState.defaultTable.name ? tableState.defaultTable.name : __('Not Selected', 'devdiggers-multipos-for-woocommerce');

        const tablesHTML = tables.map((table, index) => this.renderTableCard(table, index));

        let tableOrderPopupHTML = null;

        if (this.state.showTableOrderPopup) {
            const popupContent = (
                <OrderDetails order={this.state.order} {...this.props} orderType="hold" tableView={true} handleToggleTableOrderPopup={this.handleToggleTableOrderPopup} />
            );

            const popupProps = {
                popupStyle: getTablePopupStyle(),
                handleOverlay: this.handleToggleTableOrderPopup,
                popupContent: popupContent,
                hideSuccessButton: true,
                hideCancelButton: true,
                handleCancel: this.handleToggleTableOrderPopup,
            };

            tableOrderPopupHTML = <Popup {...popupProps} />
        }

        return (
            <div className="ddwcpos-tables-tab-wrapper">
                <div className="ddwcpos-tables-header">
                    <div>
                        <h2>{__('Floor Plan', 'devdiggers-multipos-for-woocommerce')}</h2>
                        <p>{sprintf(__('Main Dining Room • %s Active Tables', 'devdiggers-multipos-for-woocommerce'), occupiedCount)}</p>
                    </div>
                    <div className="ddwcpos-tables-summary">
                        <span className="ddwcpos-current-table-chip" title={__('Current Table', 'devdiggers-multipos-for-woocommerce')}>
                            <CheckOutlined /> {sprintf(__('Current: %s', 'devdiggers-multipos-for-woocommerce'), currentTable)}
                        </span>
                    </div>
                </div>
                <div className="ddwcpos-table-toolbar">
                    <div className="ddwcpos-search-wrapper">
                        <div className="ddwcpos-search-input-wrapper">
                            <div className="ddwcpos-search-input">
                                <SearchOutlined />
                                <input type="text" className="ddwcpos-form-control" value={this.state.search} placeholder={__('Search by seats (e.g. 4, 6, 8)...', 'devdiggers-multipos-for-woocommerce')} onChange={this.handleTableSearchInput} autoComplete="off" />
                            </div>
                            <span>{getTablesResultsLabel(tablesHTML)}</span>
                        </div>
                    </div>
                    <div className="ddwcpos-tab-changer">
                        <Link className={getTableFilterClass(tableType, 'all')} to={getPosRoute('/tables/all')}>{sprintf(__('All Tables (%s)', 'devdiggers-multipos-for-woocommerce'), allTables.length)}</Link>
                        <Link className={getTableFilterClass(tableType, 'occupied')} to={getPosRoute('/tables/occupied')}>{sprintf(__('Occupied (%s)', 'devdiggers-multipos-for-woocommerce'), occupiedCount)}</Link>
                        <Link className={getTableFilterClass(tableType, 'vacant')} to={getPosRoute('/tables/vacant')}>{sprintf(__('Vacant (%s)', 'devdiggers-multipos-for-woocommerce'), vacantCount)}</Link>
                    </div>
                </div>

                <div className="ddwcpos-table-wrapper">
                    {tablesHTML}
                </div>
                {tableOrderPopupHTML}
            </div>
        );
    }
}

const mapStateToProps = state => ({
    tables: state.tables,
    cart: state.cart,
    holdCarts: state.holdCarts,
});

const mapDispatchToProps = dispatch => bindActionCreators({ getTables, updateDefaultTable, getHoldCarts, getCart }, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(Tables);
