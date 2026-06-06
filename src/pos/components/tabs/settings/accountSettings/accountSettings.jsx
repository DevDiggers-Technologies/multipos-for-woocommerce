import React, { Component } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import { __ } from '@wordpress/i18n';
import { saveCashier } from './../../../../actions/settings';
import { SaveOutlined } from '@ant-design/icons';
import { isInternetConnected } from '../../../../services/connectivity';
import { notify } from '../../../../services/notifications';
import { getCurrentPosUser } from '../../../../services/runtime';

const hasValidPasswordChange = state => {
    return state.new_password && state.confirm_password && state.new_password === state.confirm_password;
};

const showAccountSettingsOfflineError = () => notify({
    title: __('Error', 'devdiggers-multipos-for-woocommerce'),
    message: __('You need to be online to update account details.', 'devdiggers-multipos-for-woocommerce'),
    type: 'danger',
});

const showAccountSettingsPasswordError = () => notify({
    title: __('Error', 'devdiggers-multipos-for-woocommerce'),
    message: __('Check password fields and try again.', 'devdiggers-multipos-for-woocommerce'),
    type: 'danger',
});
const getAccountSettingsState = () => ({
    id: getCurrentPosUser().ID || '',
    first_name: getCurrentPosUser().first_name || '',
    last_name: getCurrentPosUser().last_name || '',
    email: getCurrentPosUser().user_email || '',
    current_password: '',
    new_password: '',
    confirm_password: '',
});
const getInputStateChange = (field, value) => ({
    [field]: value,
});

class AccountSettings extends Component {
    constructor(props) {
        super(props);

        this.state = getAccountSettingsState();
    }

    handleInput = (e, field) => {
        this.setState(getInputStateChange(field, e.target.value));
    }

    handleAccountInput = field => e => this.handleInput(e, field)

    handleFormSubmit = e => {
        e.preventDefault();

        if (!isInternetConnected()) {
            showAccountSettingsOfflineError();
            return;
        }

        if (this.state.current_password && !hasValidPasswordChange(this.state)) {
            showAccountSettingsPasswordError();
            return;
        }

        this.props.saveCashier(this.state, this);
    }

    render() {
        return (
            <div className="ddwcpos-account-settings-wrapper ddwcpos-settings-panel">
                <h2>{__('Account Settings', 'devdiggers-multipos-for-woocommerce')}</h2>
                <p className="ddwcpos-settings-copy">{__('Update cashier details for this account.', 'devdiggers-multipos-for-woocommerce')}</p>

                <form className="ddwcpos-settings-form" onSubmit={this.handleFormSubmit}>
                    <div className="ddwcpos-form-group-column-2">
                        <label className="ddwcpos-settings-field">
                            <span>{__('First Name', 'devdiggers-multipos-for-woocommerce')}</span>
                            <input type="text" value={this.state.first_name} onChange={this.handleAccountInput('first_name')} placeholder={__('First name', 'devdiggers-multipos-for-woocommerce')} />
                        </label>
                        <label className="ddwcpos-settings-field">
                            <span>{__('Last Name', 'devdiggers-multipos-for-woocommerce')}</span>
                            <input type="text" value={this.state.last_name} onChange={this.handleAccountInput('last_name')} placeholder={__('Last name', 'devdiggers-multipos-for-woocommerce')} />
                        </label>
                    </div>
                    <label className="ddwcpos-settings-field">
                        <span>{__('Email', 'devdiggers-multipos-for-woocommerce')}</span>
                        <input type="email" value={this.state.email} onChange={this.handleAccountInput('email')} placeholder={__('Email address', 'devdiggers-multipos-for-woocommerce')} disabled />
                        <small>{__('Email address cannot be changed here.', 'devdiggers-multipos-for-woocommerce')}</small>
                    </label>
                    <div className="ddwcpos-settings-subsection">
                        <h3>{__('Password', 'devdiggers-multipos-for-woocommerce')}</h3>
                        <p className="ddwcpos-settings-copy">{__('Leave password fields empty if you do not want to change password.', 'devdiggers-multipos-for-woocommerce')}</p>
                        <label className="ddwcpos-settings-field">
                            <span>{__('Current Password', 'devdiggers-multipos-for-woocommerce')}</span>
                            <input type="password" value={this.state.current_password} onChange={this.handleAccountInput('current_password')} placeholder={__('Current password', 'devdiggers-multipos-for-woocommerce')} autoComplete="off" />
                        </label>

                        <label className="ddwcpos-settings-field">
                            <span>{__('New Password', 'devdiggers-multipos-for-woocommerce')}</span>
                            <input type="password" value={this.state.new_password} onChange={this.handleAccountInput('new_password')} placeholder={__('New password', 'devdiggers-multipos-for-woocommerce')} autoComplete="off" />
                        </label>

                        <label className="ddwcpos-settings-field">
                            <span>{__('Confirm New Password', 'devdiggers-multipos-for-woocommerce')}</span>
                            <input type="password" value={this.state.confirm_password} onChange={this.handleAccountInput('confirm_password')} placeholder={__('Confirm new password', 'devdiggers-multipos-for-woocommerce')} autoComplete="off" />
                        </label>
                    </div>

                    <button type="submit" className="ddwcpos-button ddwcpos-settings-save-button">
                        <SaveOutlined />
                        {__('Save Changes', 'devdiggers-multipos-for-woocommerce')}
                    </button>
                </form>
            </div>
        );
    }
}

const mapDispatchToProps = dispatch => bindActionCreators({ saveCashier }, dispatch);

export default connect(null, mapDispatchToProps)(AccountSettings);
