import React, { Component } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import { __ } from '@wordpress/i18n';
import { RedoOutlined, SwapOutlined } from '@ant-design/icons';
import { getSettings, saveSettings } from '../../../../actions/settings';
import { setCurrentOutlet } from '../../../../actions/outlet';
import { isInternetConnected } from '../../../../services/connectivity';
import { clearPosCache } from '../../../../services/cache';
import { notify } from '../../../../services/notifications';
import { getPosRoute } from '../../../../services/routes';
import { pushRoute } from '../../../../utils/navigation';
import { getOutletId } from '../../../../utils/value';

const notifyOutletResetting = () => notify({
    title: __('Resetting', 'devdiggers-multipos-for-woocommerce'),
    message: __('Clearing saved outlet data for this browser.', 'devdiggers-multipos-for-woocommerce'),
    type: 'success',
});
const notifyOutletSwitching = () => notify({
    title: __('Switching', 'devdiggers-multipos-for-woocommerce'),
    message: __('Returning to outlet selection.', 'devdiggers-multipos-for-woocommerce'),
    type: 'success',
});
const notifyOutletOfflineError = message => notify({
    title: __('Error', 'devdiggers-multipos-for-woocommerce'),
    message,
    type: 'danger',
});
const buildUpdatedOutletSettings = (settings, settingType, value) => ({
    ...settings,
    [settingType]: value,
});
const createSettingsChangeHandler = component => settingType => e => component.handleChangeSettings(e, settingType);
const getSettingValue = (settings, settingKey) => (settings && settings[settingKey]) || '';

class OutletSettings extends Component {
    constructor(props) {
        super(props);

        this.state = {
            page_width: '',
            page_height: '',
            page_margin: '',
        }
    }

    componentDidMount = () => {
        this.props.getSettings();
    }

    handleChangeSettings = (e, settingType) => {
        const updatedSettings = buildUpdatedOutletSettings(this.props.settings, settingType, e.target.value);

        this.props.saveSettings(updatedSettings);
    }

    handleResetOutlet = () => {
        if (isInternetConnected()) {
            notifyOutletResetting();

            this.props.setCurrentOutlet(getOutletId(this.props.outlet));

            clearPosCache('currentOutletData').then(() => {
                pushRoute(this.props.history, getPosRoute());
            });
        } else {
            notifyOutletOfflineError(__('You need to be online to reset outlet data.', 'devdiggers-multipos-for-woocommerce'));
        }
    }

    handleSwitchOutlet = () => {
        if (isInternetConnected()) {
            notifyOutletSwitching();
            clearPosCache('switchOutletData').then(() => {
                pushRoute(this.props.history, getPosRoute());
            });
        } else {
            notifyOutletOfflineError(__('You need to be online to switch outlets.', 'devdiggers-multipos-for-woocommerce'));
        }
    }

    handleDisplayCategoryChange = createSettingsChangeHandler(this)('display_category')
    handleSoundsChange = createSettingsChangeHandler(this)('sounds')
    handlePrinterWidthChange = createSettingsChangeHandler(this)('printer_width')
    handlePrinterHeightChange = createSettingsChangeHandler(this)('printer_height')
    handlePrinterMarginChange = createSettingsChangeHandler(this)('printer_margin')

    render() {
        return (
            <div className="ddwcpos-account-settings-wrapper ddwcpos-settings-panel">
                <h2>{__('Outlet Settings', 'devdiggers-multipos-for-woocommerce')}</h2>
                <p className="ddwcpos-settings-copy">{__('Manage how this outlet looks and prints in POS.', 'devdiggers-multipos-for-woocommerce')}</p>

                <form className="ddwcpos-settings-form">
                    <label className="ddwcpos-settings-field">
                        <span>{__('Show Category Cards', 'devdiggers-multipos-for-woocommerce')}</span>
                        <select value={getSettingValue(this.props.settings, 'display_category')} onChange={this.handleDisplayCategoryChange}>
                            <option value="enabled">{__('Enabled', 'devdiggers-multipos-for-woocommerce')}</option>
                            <option value="disabled">{__('Disabled', 'devdiggers-multipos-for-woocommerce')}</option>
                        </select>
                        <small>{__('Show product categories on home screen for faster browsing.', 'devdiggers-multipos-for-woocommerce')}</small>
                    </label>

                    <label className="ddwcpos-settings-field">
                        <span>{__('Play POS Sounds', 'devdiggers-multipos-for-woocommerce')}</span>
                        <select value={getSettingValue(this.props.settings, 'sounds')} onChange={this.handleSoundsChange}>
                            <option value="enabled">{__('Enabled', 'devdiggers-multipos-for-woocommerce')}</option>
                            <option value="disabled">{__('Disabled', 'devdiggers-multipos-for-woocommerce')}</option>
                        </select>
                        <small>{__('Use sound alerts for actions and updates in POS.', 'devdiggers-multipos-for-woocommerce')}</small>
                    </label>

                    <div className="ddwcpos-settings-subsection">
                        <h3>{__('Receipt Size', 'devdiggers-multipos-for-woocommerce')}</h3>
                        <p className="ddwcpos-settings-copy">{__('These values control default paper size for printed receipts.', 'devdiggers-multipos-for-woocommerce')}</p>
                        <div className="ddwcpos-settings-measurements">
                            <label className="ddwcpos-settings-field">
                                <span>{__('Receipt Width (mm)', 'devdiggers-multipos-for-woocommerce')}</span>
                                <input type="text" value={getSettingValue(this.props.settings, 'printer_width')} onChange={this.handlePrinterWidthChange} />
                            </label>

                            <label className="ddwcpos-settings-field">
                                <span>{__('Receipt Height (mm)', 'devdiggers-multipos-for-woocommerce')}</span>
                                <input type="text" value={getSettingValue(this.props.settings, 'printer_height')} onChange={this.handlePrinterHeightChange} />
                            </label>

                            <label className="ddwcpos-settings-field">
                                <span>{__('Receipt Margin (mm)', 'devdiggers-multipos-for-woocommerce')}</span>
                                <input type="text" value={getSettingValue(this.props.settings, 'printer_margin')} onChange={this.handlePrinterMarginChange} />
                            </label>
                        </div>
                    </div>

                    <div className="ddwcpos-settings-actions">
                        <div className="ddwcpos-settings-action-row">
                            <div>
                                <strong>{__('Reset Outlet Data', 'devdiggers-multipos-for-woocommerce')}</strong>
                                <small>{__('Clear saved outlet data in this browser and reload fresh data.', 'devdiggers-multipos-for-woocommerce')}</small>
                            </div>
                            <button type="button" className="ddwcpos-button-secondary" onClick={this.handleResetOutlet}>
                                <RedoOutlined />
                                {__('Reset', 'devdiggers-multipos-for-woocommerce')}
                            </button>
                        </div>

                        <div className="ddwcpos-settings-action-row">
                            <div>
                                <strong>{__('Switch Outlet', 'devdiggers-multipos-for-woocommerce')}</strong>
                                <small>{__('Go back and start a session in another outlet.', 'devdiggers-multipos-for-woocommerce')}</small>
                            </div>
                            <button type="button" className="ddwcpos-button-secondary" onClick={this.handleSwitchOutlet}>
                                <SwapOutlined />
                                {__('Switch', 'devdiggers-multipos-for-woocommerce')}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        );
    }
}

const mapStateToProps = state => ({
    settings: state.settings,
    outlet: state.outlet,
});

const mapDispatchToProps = dispatch => bindActionCreators({ getSettings, saveSettings, setCurrentOutlet }, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(OutletSettings);
