import React, { Component } from 'react';
import { __ } from '@wordpress/i18n';
import AccountSettings from './accountSettings/accountSettings.jsx';
import OutletSettings from './outletSettings/outletSettings.jsx';
import KeyboardShortcuts from './keyboardShortcuts/keyboardShortcuts.jsx';

const getSettingsSections = props => ([
    { key: 'outlet', label: __('Outlet', 'devdiggers-multipos-for-woocommerce'), component: OutletSettings, props },
    { key: 'account', label: __('Account', 'devdiggers-multipos-for-woocommerce'), component: AccountSettings, props },
    { key: 'shortcuts', label: __('Shortcuts', 'devdiggers-multipos-for-woocommerce'), component: KeyboardShortcuts, props },
]);

class Settings extends Component {
    constructor(props) {
        super(props);
        this.state = {
            activeSection: 'outlet',
        };
    }

    setActiveSection = activeSection => this.setState({ activeSection })

    createSectionChangeHandler = sectionKey => e => {
        e.preventDefault();
        this.setActiveSection(sectionKey);
    }

    renderSectionTab = section => (
        <a
            href={`#${section.key}`}
            key={section.key}
            className={this.state.activeSection === section.key ? 'ddwcpos-active' : ''}
            onClick={this.createSectionChangeHandler(section.key)}
        >
            {section.label}
        </a>
    )

    renderActiveSection = sections => {
        const activeSection = sections.find(section => section.key === this.state.activeSection) || sections[0];
        const SectionComponent = activeSection.component;

        return <SectionComponent {...activeSection.props} />;
    }

    render() {
        const sections = getSettingsSections(this.props);

        return (
            <div className="ddwcpos-settings-tab-wrapper">
                <div className="ddwcpos-settings-shell">
                    <header className="ddwcpos-settings-header">
                        <h1>{__('Settings', 'devdiggers-multipos-for-woocommerce')}</h1>
                        <p className="ddwcpos-settings-copy">{__('Review POS preferences, account details, and shortcuts in one place.', 'devdiggers-multipos-for-woocommerce')}</p>
                        <nav className="ddwcpos-tab-changer ddwcpos-segmented-tab-changer" aria-label={__('Settings Sections', 'devdiggers-multipos-for-woocommerce')}>
                            {sections.map(this.renderSectionTab)}
                        </nav>
                    </header>
                    <div className="ddwcpos-settings-content">
                        {this.renderActiveSection(sections)}
                    </div>
                </div>
            </div>
        );
    }
}

export default Settings;
