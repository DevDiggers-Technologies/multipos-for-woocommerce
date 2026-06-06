import React, { Component } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import { __ } from '@wordpress/i18n';
import { EditOutlined, MailOutlined, PhoneOutlined, SearchOutlined, WarningFilled, DeleteOutlined, CheckOutlined } from '@ant-design/icons';
import { FixedSizeList as List } from "react-window";
import { getCountriesAndStates } from '../../../actions/countries-and-states';
import { getCustomers, loadSearchCustomers, updateDefaultCustomer, deleteCustomer, saveCustomer } from './../../../actions/customers';
import ManageCustomer from './manageCustomer/manageCustomer.jsx';
import { isInternetConnected } from '../../../services/connectivity';
import { getPosRoute } from '../../../services/routes';
import { notify } from '../../../services/notifications';
import { isMobileViewport, pushRoute } from '../../../utils/navigation';
import { getOutletId, isSameId } from '../../../utils/value';
import { isEnterKey } from '../../../utils/event';

const getCustomerName = customer => {
    const customerName = `${customer && customer.first_name ? customer.first_name : ''} ${customer && customer.last_name ? customer.last_name : ''}`;

    return customerName !== ' ' ? customerName : (customer && customer.username ? customer.username : '');
};

const getManageCustomerPanel = () => document.querySelector('.ddwcpos-manage-customer-wrapper');
const getCustomersResultsLabel = customers => `${customers.length} ${__('Results', 'devdiggers-multipos-for-woocommerce')}`;
const getSingleSearchedCustomerId = customersState => (
    Array.isArray(customersState && customersState.searchedCustomers) && customersState.searchedCustomers.length === 1 ? customersState.searchedCustomers[0].id : null
);
const getCustomersState = customers => customers || {};

const toggleManageCustomerPanel = shouldShow => {
    const customerPanel = getManageCustomerPanel();

    if (!customerPanel || !isMobileViewport()) {
        return;
    }

    customerPanel.style.position = shouldShow ? 'fixed' : 'initial';
    customerPanel.style.display = shouldShow ? 'block' : 'none';
};

class Customers extends Component {
    constructor(props) {
        super(props);

        this.state = {
            search: '',
            editCustomer: {}
        }
    }

    componentDidMount() {
        const outletId = getOutletId(this.props.outlet);
        this.props.getCustomers(outletId);
        this.props.getCountriesAndStates(outletId);
    }

    handleCustomerSearch = e => {
        this.setState({
            search: e.target.value
        });

        const customersState = getCustomersState(this.props.customers);
        this.props.loadSearchCustomers(e.target.value, customersState, customersState.defaultCustomer);
    }

    handleCustomerSearchKeyDown = e => {
        if (isEnterKey(e)) {
            const singleCustomerId = getSingleSearchedCustomerId(getCustomersState(this.props.customers));

            if (singleCustomerId) {
                this.handleSetCustomer(singleCustomerId);
            } else { }
        }
    }

    handleEditCustomer = customer => {
        this.setState({
            editCustomer: customer
        });

        toggleManageCustomerPanel(true);
    }

    handleDeleteCustomer = customer => {
        if (isInternetConnected()) {
            if (confirm(__('Are you sure you want to delete this customer?', 'devdiggers-multipos-for-woocommerce'))) {
                this.props.deleteCustomer(customer.id);
            }
        } else {
            notify({
                title: __('Error', 'devdiggers-multipos-for-woocommerce'),
                message: __('Sorry, customer cannot be deleted in offline mode.', 'devdiggers-multipos-for-woocommerce'),
                type: 'danger',
            });
        }
    }

    handleResetEditCustomer = () => {
        this.setState({
            editCustomer: {}
        });

        toggleManageCustomerPanel(false);
    }

    handleSetCustomer = customerId => {
        this.props.updateDefaultCustomer(customerId);
        pushRoute(this.props.history, getPosRoute())
    }

    handleAddNewCustomerMobile = () => {
        toggleManageCustomerPanel(true);
    }

    getRowCustomer = index => (Array.isArray(getCustomersState(this.props.customers).searchedCustomers) ? getCustomersState(this.props.customers).searchedCustomers : [])[index]
    handleCustomerSearchInput = e => this.handleCustomerSearch(e)
    handleCustomerSearchInputKeyDown = e => this.handleCustomerSearchKeyDown(e)
    createEditCustomerHandler = customer => () => this.handleEditCustomer(customer)
    createDeleteCustomerHandler = customer => () => this.handleDeleteCustomer(customer)
    createSetCustomerHandler = customerId => () => this.handleSetCustomer(customerId)

    render() {
        const customersState = getCustomersState(this.props.customers);
        const customers = Array.isArray(customersState.searchedCustomers) ? customersState.searchedCustomers : [];
        const defaultCustomer = customersState.defaultCustomer || {};
        const isMobile = isMobileViewport();

        const Row = ({ index, style }) => {
            const customer = this.getRowCustomer(index);
            if (!customer) {
                return null;
            }

            const customerName = getCustomerName(customer);
            return (
                <div className="ddwcpos-list-row" style={style}>
                    <div className="ddwcpos-list-details">
                        <img src={customer.avatar_url} alt={customer.username} />
                        <div>
                            <h3>{customerName}</h3>
                            <p>
                                <MailOutlined />
                                {customer.email}
                            </p>
                            {customer.phone ?
                                <p>
                                    <PhoneOutlined />
                                    {customer.phone}
                                </p>
                                : null}
                            {''}
                        </div>
                        <div>
                            <span className="ddwcpos-icon-card" onClick={this.createEditCustomerHandler(customer)} title={__('Edit Customer', 'devdiggers-multipos-for-woocommerce')}>
                                <EditOutlined />
                            </span>
                            <span className="ddwcpos-icon-card" onClick={this.createDeleteCustomerHandler(customer)} title={__('Delete Customer', 'devdiggers-multipos-for-woocommerce')}>
                                <DeleteOutlined />
                            </span>
                            {defaultCustomer && isSameId(defaultCustomer.id, customer.id) ?
                                <span className="ddwcpos-icon-card" title={__('Current Customer', 'devdiggers-multipos-for-woocommerce')}>
                                    <CheckOutlined />
                                    {__('Current Customer', 'devdiggers-multipos-for-woocommerce')}
                                </span>
                                :
                                <span className="ddwcpos-icon-card" onClick={this.createSetCustomerHandler(customer.id)} title={__('Set Customer', 'devdiggers-multipos-for-woocommerce')}>
                                    <CheckOutlined />
                                    {__('Set Customer', 'devdiggers-multipos-for-woocommerce')}
                                </span>
                            }
                        </div>
                    </div>
                </div>
            );
        }

        const rowHeight = isMobile ? 128 : 80;

        return (
            <div className="ddwcpos-customers-tab-wrapper">
                <div className="ddwcpos-list-wrapper">
                    <div className="ddwcpos-search-wrapper">
                        <h2>{__('Customers', 'devdiggers-multipos-for-woocommerce')}</h2>

                        {isMobile ?
                            <button className="ddwcpos-button" onClick={this.handleAddNewCustomerMobile}>{__('Add New', 'devdiggers-multipos-for-woocommerce')}</button>
                            : null}

                        <div className="ddwcpos-search-input-wrapper">
                            <div className="ddwcpos-search-input">
                                <SearchOutlined />
                                <input type="text" className="ddwcpos-form-control" value={this.state.search} placeholder={__('Search Customer by name, email or phone...', 'devdiggers-multipos-for-woocommerce')} onChange={this.handleCustomerSearchInput} onKeyDown={this.handleCustomerSearchInputKeyDown} autoFocus autoComplete="off" />
                            </div>
                            <span>{getCustomersResultsLabel(customers)}</span>
                        </div>
                    </div>
                    {
                        customers.length > 0 ?
                            <List
                                className="ddwcpos-list"
                                height={800}
                                itemCount={customers.length}
                                itemSize={rowHeight}
                            >
                                {Row}
                            </List>
                            :
                            <div className="ddwcpos-no-results">
                                <WarningFilled />
                                <p>{__('No Customers Found', 'devdiggers-multipos-for-woocommerce')}</p>
                            </div>
                    }
                </div>
                <ManageCustomer customer={this.state.editCustomer} {...this.props} handleResetEditCustomer={this.handleResetEditCustomer} />
            </div>
        );
    }
}

const mapStateToProps = state => ({
    customers: state.customers,
    countries_and_states: state.countries_and_states,
});

const mapDispatchToProps = dispatch => bindActionCreators({
    getCustomers,
    getCountriesAndStates,
    loadSearchCustomers,
    updateDefaultCustomer,
    deleteCustomer,
    saveCustomer
}, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(Customers);
