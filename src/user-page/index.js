"use strict";

import './user-page.less';

var ddwcpos = jQuery.noConflict();

document.addEventListener('DOMContentLoaded', () => {
    const userRoleSelectElements = document.querySelectorAll('select[name="role"]');
    if (userRoleSelectElements.length) {
        userRoleSelectElements.forEach(userRoleSelectElement => {
            if (ddwcposUserPageObj.siteReferer) {
                userRoleSelectElement.value = 'ddwcpos_cashier';
            }
            if ('ddwcpos_cashier' === userRoleSelectElement.value) {
                let selectOutletTemplate = wp.template('ddwcpos_assigned_outlets');
                userRoleSelectElement.closest('table').insertAdjacentHTML('afterend', selectOutletTemplate());
                ddwcpos('.ddwcpos-assigned-outlets').select2();
            }
            userRoleSelectElement.addEventListener('change', e => {
                if ('ddwcpos_cashier' === e.target.value) {
                    let selectOutletTemplate = wp.template('ddwcpos_assigned_outlets');
                    e.target.closest('table').insertAdjacentHTML('afterend', selectOutletTemplate());
                    ddwcpos('.ddwcpos-assigned-outlets').select2();
                } else {
                    if (document.querySelector('#ddwcpos-assigned-outlets-row')) {
                        document.querySelector('#ddwcpos-assigned-outlets-row').remove();
                    }
                }
            });
        });
    }
});