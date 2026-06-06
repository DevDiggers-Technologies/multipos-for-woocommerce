import React, { Component, Fragment } from 'react';
import { __ } from '@wordpress/i18n';
import { CloseOutlined, PlusOutlined } from '@ant-design/icons';
import { hasEntries } from '../../utils/value';

const isEscapeKey = event => event.key === 'Escape' || event.which === 27;
const hasPopupStyle = popupStyle => hasEntries(popupStyle);
const getPopupActionsStyle = isSingleButton => (isSingleButton ? { gridTemplateColumns: '1fr' } : {});
const getPopupClassName = additionalPopupClass => (
    additionalPopupClass ? `ddwcpos-popup ${additionalPopupClass}` : 'ddwcpos-popup'
);

class Popup extends Component {
    componentDidMount = () => {
        document.addEventListener('keydown', this.handleKeyDown, false);
    }

    componentWillUnmount = () => {
        document.removeEventListener('keydown', this.handleKeyDown, false);
    }

    handleKeyDown = e => {
        if (isEscapeKey(e)) {
            this.props.handleCancel();
        }
    }

    handleSuccessClick = e => {
        const successArgs = this.props.handleSuccessArgs ? this.props.handleSuccessArgs : [];
        this.props.handleSuccess(e, ...successArgs);
    }

    render() {
        const style = getPopupActionsStyle(this.props.singleButton);
        const popupClass = getPopupClassName(this.props.additionalPopupClass);

        return (
            <Fragment>
                <div className="ddwcpos-popup-overlay" onClick={this.props.handleOverlay}></div>
                <div className={popupClass} style={hasPopupStyle(this.props.popupStyle) ? this.props.popupStyle : {}}>
                    <div className="ddwcpos-popup-content">
                        {this.props.popupContent}

                        {this.props.hideActions ?
                            null
                            :
                            <div className="ddwcpos-actions-wrapper" style={style}>
                                {
                                    !this.props.hideSuccessButton &&
                                    <button disabled={!this.props.notDisabled} onClick={this.handleSuccessClick}>
                                        {this.props.successButtonText ?
                                            this.props.successButtonText
                                            :
                                            <Fragment>
                                                <PlusOutlined />
                                                {__('Add', 'devdiggers-multipos-for-woocommerce')}
                                            </Fragment>
                                        }
                                    </button>
                                }
                                {
                                    !this.props.hideCancelButton &&
                                    <button className="ddwcpos-button-secondary" onClick={this.props.handleCancel}>
                                        {this.props.cancelButtonText ?
                                            this.props.cancelButtonText
                                            :
                                            <Fragment>
                                                <CloseOutlined />
                                                {__('Cancel', 'devdiggers-multipos-for-woocommerce')}
                                            </Fragment>
                                        }
                                    </button>
                                }
                            </div>
                        }
                    </div>
                </div>
            </Fragment>
        );
    }
}

export default Popup;
