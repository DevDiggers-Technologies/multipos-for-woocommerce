import React, { Component } from 'react';
import { __ } from '@wordpress/i18n';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import { WarningFilled, SearchOutlined } from '@ant-design/icons';
import { FixedSizeList as List } from "react-window";
import { getTransactions } from './../../../actions/transactions';
import { formatPrice } from '../../../utils/currency-format';
import { getOrders } from './../../../actions/orders';
import { getSettings } from './../../../actions/settings';
import { getCurrentPosDate, getPosConfig } from '../../../services/runtime';
import { getOutletId } from '../../../utils/value';

const getTransactionMethodName = transactionMethod => {
    if (transactionMethod === 'manual') {
        return __('Manual', 'devdiggers-multipos-for-woocommerce');
    }

    const paymentMethods = Object.values(getPosConfig().payment_method || {});
    const transactionPaymentMethod = paymentMethods.find(paymentMethod => paymentMethod.slug === transactionMethod);

    return transactionPaymentMethod ? transactionPaymentMethod.name : '-';
};

const getTransactionRowClassName = index => (
    index % 2 !== 0 ? 'ddwcpos-list-row ddwcpos-odd-list-row' : 'ddwcpos-list-row'
);

const getTransactionTotals = transactions => {
    let cashAmount = 0;
    let expectedDrawerAmount = 0;

    (Array.isArray(transactions) ? transactions : []).forEach(transaction => {
        if (transaction.method === 'cash') {
            if (transaction.in) {
                cashAmount += parseFloat(transaction.in);
            }
            if (transaction.out) {
                cashAmount -= parseFloat(transaction.out);
            }
        } else if (transaction.method === 'manual') {
            if (transaction.in) {
                expectedDrawerAmount += parseFloat(transaction.in);
            }
            if (transaction.out) {
                expectedDrawerAmount -= parseFloat(transaction.out);
            }
        }
    });

    return {
        cashAmount,
        expectedDrawerAmount: expectedDrawerAmount + cashAmount,
    };
};

const matchesTransactionSearch = (transaction, search) => {
    const contains = value => String(value || '').toLowerCase().includes(search);

    return (
        contains(transaction.id) ||
        contains(transaction.order_id) ||
        contains(transaction.method) ||
        contains(transaction.reference)
    );
};

const sortTransactionsDescending = transactions => (
    [...(Array.isArray(transactions) ? transactions : [])].sort((firstTransaction, secondTransaction) => (
        firstTransaction.id < secondTransaction.id ? 1 : (secondTransaction.id < firstTransaction.id ? -1 : 0)
    ))
);
const getFilteredTransactions = (transactions, search) => (
    search ? (Array.isArray(transactions) ? transactions : []).filter(transaction => matchesTransactionSearch(transaction, search)) : (Array.isArray(transactions) ? transactions : [])
);
const getTransactionResultLabel = transactions => `${transactions.length} ${__('Results', 'devdiggers-multipos-for-woocommerce')}`;
const getTransactionOrderLabel = transaction => transaction.order_id ? `#${transaction.order_id}` : '-';
const getTransactionReferenceLabel = transaction => transaction.reference || '-';
const getOrdersList = ordersState => Array.isArray(ordersState && ordersState.list) ? ordersState.list : [];
const createTransactionRowRenderer = (transactions, context) => ({ index, style }) => {
    const transaction = transactions[index];
    const paymentMethodName = getTransactionMethodName(transaction.method);

    return (
        <div className={getTransactionRowClassName(index)} style={style}>
            <div className="ddwcpos-list-details">
                <p>#{transaction.id}</p>
                <p>{getTransactionOrderLabel(transaction)}</p>
                <p><mark className="instock">+ {formatPrice(transaction.in)}</mark></p>
                <p><mark className="required">- {formatPrice(transaction.out)}</mark></p>
                <p>{paymentMethodName}</p>
                <p>{getTransactionReferenceLabel(transaction)}</p>
                <p>{transaction.date}</p>
                {''}
            </div>
        </div>
    );
};
const getTodayOrders = orders => getOrdersList(orders).filter(order => {
    const orderCreatedAt = new Date(order.order_created);
    const currentDate = new Date(getCurrentPosDate());

    return orderCreatedAt.getTime() >= currentDate.getTime();
});
const getTodaySalesTotal = (orders, context) => (Array.isArray(orders) ? orders : []).reduce((total, order) => total + (parseFloat(order.order_total) || 0), 0);

class Statistics extends Component {
    constructor(props) {
        super(props);

        this.state = {
            search: '',
        };
    }

    componentDidMount = () => {
        const outletId = getOutletId(this.props.outlet);

        this.props.getTransactions(outletId);
        this.props.getOrders(outletId);
        this.props.getSettings();
    }

    handleTransactionSearch = e => {
        this.setState({
            search: e.target.value,
        });
    }
    handleTransactionSearchInput = e => this.handleTransactionSearch(e)

    render() {
        let transactions = this.props.transactions;
        const { cashAmount, expectedDrawerAmount } = getTransactionTotals(transactions);

        const search = this.state.search.toLowerCase();
        transactions = sortTransactionsDescending(getFilteredTransactions(transactions, search));

        const todayOrders = getTodayOrders(this.props.orders);
        const todayTotalSale = getTodaySalesTotal(todayOrders, this);
        const transactionRowRenderer = createTransactionRowRenderer(transactions, this);
        const rowHeight = 52;

        return (
            <div className="ddwcpos-statistics-tab-wrapper">
                <div className="ddwcpos-statistics-summary">
                    <div>
                        <h3>{__('Today\'s Cash Sale', 'devdiggers-multipos-for-woocommerce')}</h3>
                        <span>{formatPrice(cashAmount)}</span>
                    </div>
                    {''}
                    <div>
                        <h3>{__('Today\'s Total Sale', 'devdiggers-multipos-for-woocommerce')}</h3>
                        <span>{formatPrice(todayTotalSale)}</span>
                    </div>
                    <div>
                        <h3>{__('Expected Drawer Amount', 'devdiggers-multipos-for-woocommerce')}</h3>
                        <span>{formatPrice(expectedDrawerAmount)}</span>
                    </div>
                </div>

                <div className="ddwcpos-search-wrapper">
                    <h2>{__('Today\'s Transactions', 'devdiggers-multipos-for-woocommerce')}</h2>

                    <div className="ddwcpos-search-input-wrapper">
                        <div className="ddwcpos-search-input">
                            <SearchOutlined />
                            <input type="text" className="ddwcpos-form-control" value={this.state.search} placeholder={__('Search Transaction by ID or Order ID', 'devdiggers-multipos-for-woocommerce')} onChange={this.handleTransactionSearchInput} autoComplete="off" />
                        </div>
                        <span>{getTransactionResultLabel(transactions)}</span>
                    </div>
                </div>

                <div className="ddwcpos-transactions-list">
                    <div className="ddwcpos-list-heading-row">
                        <div className="ddwcpos-list-details">
                            <p>{__('Transaction ID', 'devdiggers-multipos-for-woocommerce')}</p>
                            <p>{__('Order ID', 'devdiggers-multipos-for-woocommerce')}</p>
                            <p>{__('In', 'devdiggers-multipos-for-woocommerce')}</p>
                            <p>{__('Out', 'devdiggers-multipos-for-woocommerce')}</p>
                            <p>{__('Method', 'devdiggers-multipos-for-woocommerce')}</p>
                            <p>{__('Reference', 'devdiggers-multipos-for-woocommerce')}</p>
                            <p>{__('Date', 'devdiggers-multipos-for-woocommerce')}</p>
                            {''}
                        </div>
                    </div>
                    {
                        transactions.length ?
                            <List
                                className="ddwcpos-list"
                                height={800}
                                itemCount={transactions.length}
                                itemSize={rowHeight}
                            >
                                {transactionRowRenderer}
                            </List>
                            :
                            <div className="ddwcpos-no-results">
                                <WarningFilled />
                                <p>{__('No Transactions Found', 'devdiggers-multipos-for-woocommerce')}</p>
                            </div>
                    }
                </div>
            </div>
        );
    }
}

const mapStateToProps = state => ({
    transactions: state.transactions,
    orders: state.orders,
    settings: state.settings
});

const mapDispatchToProps = dispatch => bindActionCreators({ getTransactions, getOrders, getSettings }, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(Statistics);
