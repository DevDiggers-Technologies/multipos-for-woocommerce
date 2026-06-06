import React, { Component } from 'react';
import { connect } from 'react-redux';
import { __ } from '@wordpress/i18n';
import ReactNotification from 'react-notifications-component';
import { bindActionCreators } from 'redux';
import 'react-notifications-component/dist/theme.css';
import Menu from './components/menu/menu.jsx';
import Tabs from './components/tabs/tabs.jsx';
import { setCurrentOutlet } from './actions/outlet';
import { store } from 'react-notifications-component';
import { getSettings } from './actions/settings';
import { getKeyboardShortcutRoute } from './services/keyboard-shortcuts';
import { getAssignedOutlets, getPosConfig, getPosConfigValue } from './services/runtime';
import { hasEntries } from './utils/value';

import './pos.less';

const hasOutletSelection = outlet => hasEntries(outlet);
const shouldShowBranding = () => Boolean(getPosConfig()['login_branding_enabled']);
const hasOutletOptions = outletList => Array.isArray(outletList) && outletList.length > 0;

const getLoginViewStyle = () => ({
    '--login-mesh-1': getPosConfigValue('login_bg_primary_color'),
    '--login-mesh-2': getPosConfigValue('login_bg_secondary_color'),
    '--login-canvas': getPosConfigValue('login_canvas_bg_color'),
    '--login-card-bg': getPosConfigValue('login_card_bg_color'),
    '--login-text': getPosConfigValue('login_font_color'),
});

class App extends Component {

    constructor(props) {
        super(props);
        this.state = {
            outletFetched: false,
        };
        this.isMountedFlag = false;
    }

    componentDidMount() {
        this.isMountedFlag = true;
        this.props.setCurrentOutlet().then(() => {
            if (this.isMountedFlag) {
                this.setState({
                    outletFetched: true,
                });
            }
        });

        this.props.getSettings();
        document.addEventListener('keydown', this.handleKeyboardShortcut);
    }

    componentWillUnmount() {
        this.isMountedFlag = false;
        document.removeEventListener('keydown', this.handleKeyboardShortcut);
    }

    handleKeyboardShortcut = event => {
        if (this.props.settings && this.props.settings.keyboard_shortcuts === 'disabled') {
            return;
        }

        const route = getKeyboardShortcutRoute(event, this.props.settings);

        if (route && this.props.history && this.props.history.push) {
            event.preventDefault();
            this.props.history.push(route);
        }
    }

    changeCurrentOutlet = outletId => {
        this.props.setCurrentOutlet(outletId).then(() => {
            if (this.isMountedFlag) {
                this.setState({
                    outletFetched: true,
                });
            }
        });
    }

    renderBranding = () => {
        if (!shouldShowBranding()) {
            return null;
        }

        return (
            <p className="ddwcpos-login-branding">
                {__('Powered by ', 'devdiggers-multipos-for-woocommerce')}<a href="https://devdiggers.com/product/multipos-point-of-sale-for-woocommerce/" target="_blank" rel="noreferrer">{__('MultiPOS', 'devdiggers-multipos-for-woocommerce')}</a>
            </p>
        );
    }

    renderLoadingView = () => (
        <div className="ddwcpos-container">
            <div className="ddwcpos-progress-bar"></div>
        </div>
    )

    renderPosView = () => (
        <div className="ddwcpos-container">
            <ReactNotification />
            <div className="ddwcpos-progress-bar"></div>
            <Menu {...this.props} />
            <Tabs {...this.props} notification={store}></Tabs>
        </div>
    )

    createOutletSelectHandler = outletId => () => this.changeCurrentOutlet(outletId)

    renderOutletOption = outlet => {
        if (!outlet || outlet.id === undefined || outlet.id === null) {
            return null;
        }

        return <li key={outlet.id} onClick={this.createOutletSelectHandler(outlet.id)}>{outlet.name || outlet.id}</li>;
    }

    renderOutletList = () => (
        getAssignedOutlets().map(this.renderOutletOption)
    )

    renderSelectOutletView = () => {
        const outletList = this.renderOutletList();

        return (
            <div className="ddwcpos-login-wrapper" style={getLoginViewStyle()}>
                <div className="ddwcpos-mesh-background"></div>
                <div className="ddwcpos-login-card">
                    <div className="ddwcpos-login-header">
                        <h1>{__('Select Outlet', 'devdiggers-multipos-for-woocommerce')}</h1>
                        <p className="ddwcpos-login-subtitle">{__('Please select a terminal to begin your session.', 'devdiggers-multipos-for-woocommerce')}</p>
                    </div>
                    <div className="ddwcpos-outlets-container">
                        {hasOutletOptions(outletList) ?
                            <ul>
                                {outletList}
                            </ul>
                            : <p>{__('You do not have any assigned outlet!!', 'devdiggers-multipos-for-woocommerce')}</p>
                        }
                    </div>
                    <div className="ddwcpos-login-footer">
                        <p>{getPosConfigValue('login_footer_text')}</p>
                        {this.renderBranding()}
                    </div>
                </div>
            </div>
        );
    }

    render() {
        if (!this.state.outletFetched) {
            return this.renderLoadingView();
        }

        if (hasOutletSelection(this.props.outlet)) {
            return this.renderPosView();
        }

        return this.renderSelectOutletView();
    }
}

const mapStateToProps = state => ({
    outlet: state.outlet,
    settings: state.settings,
});

const mapDispatchToProps = dispatch => bindActionCreators({
    setCurrentOutlet,
    getSettings,
}, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(App);
