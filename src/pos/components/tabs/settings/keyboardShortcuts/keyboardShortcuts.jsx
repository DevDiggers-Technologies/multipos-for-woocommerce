import React, { Component } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import { __ } from '@wordpress/i18n';
import { saveSettings } from '../../../../actions/settings';
import {
    POS_KEYBOARD_SHORTCUTS,
    KEYBOARD_SHORTCUT_OPTIONS,
    getDefaultKeyboardShortcutMap,
    getKeyboardShortcutDisplay,
    getKeyboardShortcutMap,
} from '../../../../services/keyboard-shortcuts';

const getKeyboardShortcutsValue = settings => settings && settings.keyboard_shortcuts === 'disabled' ? 'disabled' : 'enabled';

class KeyboardShortcuts extends Component {
    handleKeyboardShortcutsChange = e => {
        this.props.saveSettings({
            ...this.props.settings,
            keyboard_shortcuts: e.target.value,
        });
    }

    handleShortcutChange = actionId => e => {
        const shortcut = e.target.value;
        const shortcutMap = getKeyboardShortcutMap(this.props.settings);
        const nextShortcutMap = Object.keys(shortcutMap).reduce((map, id) => ({
            ...map,
            [id]: shortcut && shortcutMap[id] === shortcut ? '' : shortcutMap[id],
        }), {});

        nextShortcutMap[actionId] = shortcut;

        this.props.saveSettings({
            ...this.props.settings,
            keyboard_shortcuts_map: nextShortcutMap,
        });
    }

    resetShortcuts = () => {
        this.props.saveSettings({
            ...this.props.settings,
            keyboard_shortcuts_map: getDefaultKeyboardShortcutMap(),
        });
    }

    renderShortcut = shortcut => (
        <div className="ddwcpos-shortcut-row" key={shortcut.id}>
            <label htmlFor={`ddwcpos-shortcut-${shortcut.id}`}>{shortcut.label}</label>
            <select
                id={`ddwcpos-shortcut-${shortcut.id}`}
                value={getKeyboardShortcutMap(this.props.settings)[shortcut.id]}
                onChange={this.handleShortcutChange(shortcut.id)}
            >
                <option value="">{__('Unassigned', 'devdiggers-multipos-for-woocommerce')}</option>
                {KEYBOARD_SHORTCUT_OPTIONS.map(option => (
                    <option value={option} key={option}>{getKeyboardShortcutDisplay(option)}</option>
                ))}
            </select>
        </div>
    )

    render() {
        return (
            <div className="ddwcpos-account-settings-wrapper ddwcpos-settings-panel">
                <div className="ddwcpos-settings-section-heading">
                    <h2>{__('Keyboard Shortcuts', 'devdiggers-multipos-for-woocommerce')}</h2>
                    <button type="button" className="ddwcpos-button ddwcpos-shortcuts-reset" onClick={this.resetShortcuts}>
                        {__('Reset Defaults', 'devdiggers-multipos-for-woocommerce')}
                    </button>
                </div>
                <p className="ddwcpos-settings-copy">{__('Choose shortcuts that feel natural for your keyboard. Each shortcut can be used only once.', 'devdiggers-multipos-for-woocommerce')}</p>

                <form className="ddwcpos-settings-form">
                    <label className="ddwcpos-settings-field">
                        <span>{__('Keyboard Shortcuts', 'devdiggers-multipos-for-woocommerce')}</span>
                        <select value={getKeyboardShortcutsValue(this.props.settings)} onChange={this.handleKeyboardShortcutsChange}>
                            <option value="enabled">{__('Enabled', 'devdiggers-multipos-for-woocommerce')}</option>
                            <option value="disabled">{__('Disabled', 'devdiggers-multipos-for-woocommerce')}</option>
                        </select>
                        <small>{__('Turn shortcuts on or off for this POS screen.', 'devdiggers-multipos-for-woocommerce')}</small>
                    </label>

                    <div className="ddwcpos-shortcuts-list">
                        {POS_KEYBOARD_SHORTCUTS.map(this.renderShortcut)}
                    </div>
                </form>
            </div>
        );
    }
}

const mapStateToProps = state => ({
    settings: state.settings,
});

const mapDispatchToProps = dispatch => bindActionCreators({ saveSettings }, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(KeyboardShortcuts);
