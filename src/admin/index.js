"use strict";

import './admin.less';

const ADMIN_OBJECT = window.ddwcposAdminObj || {};
const AJAX_CONFIG = ADMIN_OBJECT.ajax || {};
const I18N = ADMIN_OBJECT.i18n || {};
const CONFIG = ADMIN_OBJECT.ddwcpos_configuration || {};
const INVALID_HTML_PATTERN = /<\s?[^>]*\/?\s?>/i;
const FORM_SELECTOR = 'form#ddwcpos-payments-container, form#ddwcpos-tables-container';
const PRODUCT_ACTION_SELECTOR = '.ddwcpos-product-action';
const BARCODE_INPUT_SELECTOR = '.ddwcpos-barcode, .ddwcpos-barcode-quantity, .ddwcpos-custom-stock';

const onReady = callback => {
    if ('loading' === document.readyState) {
        document.addEventListener('DOMContentLoaded', callback);
        return;
    }

    callback();
};

const removeElements = elements => {
    [...elements].forEach(element => element.remove());
};

const getTemplate = templateId => (
    window.wp && wp.template && templateId ? wp.template(templateId) : null
);

const showValidationNotice = form => {
    const invalidFormFields = getTemplate('ddwcpos_form_data_error');

    if (invalidFormFields) {
        form.insertAdjacentHTML('beforeBegin', invalidFormFields());
    }
};

const shouldValidateInput = input => (
    input
    && !input.disabled
    && !['hidden', 'submit', 'button'].includes(input.type)
    && !input.classList.contains('ddwcpos-hide')
);

const setInputInvalid = input => {
    input.style.borderColor = 'red';
};

const resetInputState = input => {
    input.style.borderColor = '';
};

const isInvalidInputValue = input => {
    const inputValue = String(input.value || '').trim();

    return !inputValue || '-1' === inputValue || INVALID_HTML_PATTERN.test(inputValue);
};

const validateForm = (form, event) => {
    let hasError = false;

    [...form.elements].forEach(input => {
        resetInputState(input);

        if (!shouldValidateInput(input) || !isInvalidInputValue(input)) {
            return;
        }

        event.preventDefault();
        setInputInvalid(input);
        hasError = true;
    });

    if (hasError) {
        showValidationNotice(form);
    }
};

const bindConfigurationForms = () => {
    const forms = document.querySelectorAll(FORM_SELECTOR);

    if (!forms.length) {
        return;
    }

    forms.forEach(form => {
        form.addEventListener('submit', event => {
            removeElements(document.querySelectorAll('.notice'));
            validateForm(form, event);
        });
    });
};

const getRepeaterTarget = button => {
    const invoiceCard = button.closest('.ddwcpos-invoice-card');

    if (invoiceCard) {
        return invoiceCard;
    }

    return button.closest('tr');
};

const addRepeaterRow = button => {
    const form = button.closest('form');
    const templateId = button.getAttribute('data-template');
    const template = getTemplate(templateId);
    const maxIndexElement = form ? form.querySelector('#ddwcpos-max-index') : null;
    const target = getRepeaterTarget(button);

    if (!template || !maxIndexElement || !target) {
        return;
    }

    const rowIndex = Number.parseInt(maxIndexElement.value, 10) || 0;
    const nextIndex = rowIndex + 1;

    maxIndexElement.value = nextIndex;
    target.insertAdjacentHTML('beforeBegin', template({ key: nextIndex }));
};

const removeRepeaterRow = button => {
    const target = getRepeaterTarget(button);

    if (target) {
        target.remove();
    }
};

const bindRepeaterRows = () => {
    document.addEventListener('click', event => {
        const addRowButton = event.target.closest('.ddwcpos-add-row');
        const removeRowButton = event.target.closest('.ddwcpos-remove-row');

        if (addRowButton) {
            event.preventDefault();
            addRepeaterRow(addRowButton);
            return;
        }

        if (removeRowButton) {
            event.preventDefault();
            removeRepeaterRow(removeRowButton);
        }
    });
};

const getBarcodePrintStyle = () => `<style type="text/css">
    @page {
        size: ${CONFIG.barcode_printer_width} ${CONFIG.barcode_printer_height};
        margin: ${CONFIG.barcode_printer_margin};
        text-align: center;
    }
</style>`;

const getBarcodePrintContent = (row, quantity) => {
    const printContentElement = row.querySelector('.ddwcpos-barcode-print-content');
    const printContent = printContentElement ? printContentElement.innerHTML : '';

    return Array.from({ length: quantity }, () => printContent).join('');
};

const isMobileDevice = () => /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

const printInPopup = html => {
    const printWindow = window.open('', 'PRINT', 'height=400,width=600');

    if (!printWindow) {
        return;
    }

    printWindow.document.open();
    printWindow.document.clear();
    printWindow.document.writeln(html);
    printWindow.document.close();

    printWindow.addEventListener('load', () => {
        setTimeout(() => {
            printWindow.focus();
            printWindow.print();
        }, 700);
    }, true);
};

const printInFrame = html => {
    const frame = document.createElement('iframe');
    frame.name = 'ddwcpos-barcode-print-frame';
    document.body.appendChild(frame);

    const frameDocument = frame.contentWindow ? frame.contentWindow.document : frame.contentDocument;

    frameDocument.open();
    frameDocument.write(html);
    frameDocument.close();

    setTimeout(() => {
        frame.contentWindow.focus();
        frame.contentWindow.print();
        document.body.removeChild(frame);
    }, 700);
};

const handleBarcodePrint = row => {
    const quantityElement = row.querySelector('.ddwcpos-barcode-quantity');
    const quantity = quantityElement ? Number.parseInt(quantityElement.value, 10) : 0;

    if (!quantity) {
        alert(I18N.barcodeQuantityError);
        return;
    }

    quantityElement.value = '';

    const html = `<html><head><title>Barcode</title>${getBarcodePrintStyle()}</head><body>${getBarcodePrintContent(row, quantity)}</body></html>`;

    if (isMobileDevice()) {
        printInPopup(html);
        return;
    }

    printInFrame(html);
};

const bindProductActions = () => {
    document.addEventListener('click', event => {
        const button = event.target.closest(PRODUCT_ACTION_SELECTOR);

        if (!button) {
            return;
        }

        event.preventDefault();

        const row = button.closest('tr');
        const productAction = button.getAttribute('data-action');

        if (!row) {
            return;
        }

        if ('print_barcode' === productAction) {
            handleBarcodePrint(row);
            return;
        }
    });
};

const bindBarcodeEnterKey = () => {
    document.addEventListener('keydown', event => {
        if (!event.target.matches(BARCODE_INPUT_SELECTOR) || ('Enter' !== event.key && 13 !== event.keyCode)) {
            return;
        }

        const nextElement = event.target.nextElementSibling;

        event.preventDefault();

        if (nextElement) {
            nextElement.click();
        }
    });
};

onReady(() => {
    bindConfigurationForms();
    bindRepeaterRows();
    bindProductActions();
    bindBarcodeEnterKey();
});
