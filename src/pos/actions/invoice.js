
import { formatPrice } from '../utils/currency-format';
import { __ } from '@wordpress/i18n';
import { getCurrentPosUser, getPosConfig, getPosLogoUrl, getTaxType } from '../services/runtime';
import { getOrderBilling, getOrderCurrencySymbol, getOrderProducts } from '../utils/orders';
import { hasEntries, parseWholeNumber } from '../utils/value';

const getLanguageAttributes = () => getPosConfig().language_attributes || '';
const isMobilePrintDevice = () => /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
const getSelectedInvoice = outletInvoiceSlug => {
    if (!outletInvoiceSlug) {
        return null;
    }

    const invoices = getPosConfig().invoices ? Object.values(getPosConfig().invoices) : [];

    return invoices.find(invoice => invoice.slug === outletInvoiceSlug) || null;
};

const getInvoiceTemplate = (selectedInvoice, pageWidth, pageHeight, pageMargin) => {
    if (selectedInvoice && selectedInvoice.html) {
        return `<style>@page { size: ${pageWidth} ${pageHeight}; margin: ${pageMargin}; } ${selectedInvoice.css}</style>${selectedInvoice.html}`;
    }

    return `<style>@page { size: ${pageWidth} ${pageHeight}; margin: ${pageMargin}; } ${getPosConfig().invoice_css || ''}</style>${getPosConfig().invoice_html || ''}`;
};

const hasRecord = hasEntries;
const getInvoiceTableName = order => order.table && hasRecord(order.table) ? order.table.name : 'N/A';
const hasOrderDiscount = discount => hasRecord(discount);
const getOrderTaxLines = order => order && Array.isArray(order.tax_lines) ? order.tax_lines : [];
const getOrderCoupons = order => order && Array.isArray(order.coupons) ? order.coupons : [];
const getOrderPaymentMethods = order => order && Array.isArray(order.payment_methods) ? order.payment_methods : [];
const getOrderFees = order => order && Array.isArray(order.fees) ? order.fees : [];
const getBillingField = (billing, primaryKey, fallbackKey = primaryKey) => billing[primaryKey] || billing[fallbackKey] || '';
const createEmptyInvoiceTokens = () => ({
    logo_invoice: '${logo_invoice}',
    outlet_name: '${outlet_name}',
    order_id: '${order_id}',
    order_date: '${order_date}',
    customer_fname: '${customer_fname}',
    customer_lname: '${customer_lname}',
    customer_phone: '${customer_phone}',
    outlet_address1: '${outlet_address1}',
    outlet_address2: '${outlet_address2}',
    outlet_city: '${outlet_city}',
    outlet_state: '${outlet_state}',
    outlet_country: '${outlet_country}',
    outlet_postcode: '${outlet_postcode}',
    outlet_phone: '${outlet_phone}',
    outlet_email: '${outlet_email}',
    pro_name: '${pro_name}',
    pro_quantity: '${pro_quantity}',
    pro_unit_price: '${pro_unit_price}',
    pro_total: '${pro_total}',
    product_row: '${product_row}',
    sub_total: '${sub_total}',
    tax_label: '${tax_label}',
    order_tax: '${order_tax}',
    coupon_name: '${coupon_name}',
    coupon_amount: '${coupon_amount}',
    fee_name: '${fee_name}',
    fee_amount: '${fee_amount}',
    order_discount: '${order_discount}',
    order_total: '${order_total}',
    tendered_total: '${tendered_total}',
    order_change: '${order_change}',
    tendered_payment_name: '${tendered_payment_name}',
    tendered_payment_amount: '${tendered_payment_amount}',
    table_name: '${table_name}',
    cashier_name: '${cashier_name}',
    total_quantity: '${total_quantity}',
});
const getOfflineOrderDiscountAmount = (discount, order) => {
    if (!hasOrderDiscount(discount)) {
        return formatPrice(parseFloat(0));
    }

    if (discount.type === 'fixed') {
        return formatPrice(-parseFloat(discount.amount));
    }

    return formatPrice(-parseFloat(discount.amount * order.order_total / 100));
};

const getTenderedPaymentMarkup = order => {
    const paymentMethods = getOrderPaymentMethods(order);

    if (paymentMethods.length) {
        return paymentMethods.reduce((paymentMarkup, paymentMethod) => ({
            names: `${paymentMarkup.names}<p>${paymentMethod.name}</p>`,
            amounts: `${paymentMarkup.amounts}<p>${formatPrice(paymentMethod.amount, order.order_currency_symbol)}</p>`,
        }), { names: '', amounts: '' });
    }

    return {
        names: `<p>${order.payment_method_title}</p>`,
        amounts: `<p>${formatPrice(order.tendered, order.order_currency_symbol)}</p>`,
    };
};

const getInvoiceProductRows = order => {
    let totalQuantity = 0;
    const orderProducts = getOrderProducts(order);

    const productRow = orderProducts.reduce((rowsMarkup, product) => {
        totalQuantity += parseWholeNumber(product.quantity);

        return `${rowsMarkup}<tr>
                <td><p>${product.name}</p></td>
                <td><p>${formatPrice(product.uf, order.order_currency_symbol)}</p></td>
                <td><p>${product.quantity}</p></td>
                <td><p>${formatPrice(product.uf_total, order.order_currency_symbol)}</p></td>
            </tr>`;
    }, '<table><tbody>');

    return {
        productRow: `${productRow}</tbody></table>`,
        totalQuantity,
    };
};
const getOnlineOrderTaxMarkup = order => getOrderTaxLines(order).reduce((markup, tax) => ({
    tax_label: `${markup.tax_label}<p>${__('Tax', 'devdiggers-multipos-for-woocommerce')} (${tax.label})</p>`,
    order_tax: `${markup.order_tax}<p>${formatPrice(tax.total, order.order_currency_symbol)}</p>`,
}), { tax_label: '', order_tax: '' });
const getOfflineOrderTaxMarkup = order => getOrderTaxLines(order).reduce((markup, tax) => {
    const taxAmount = getTaxType() === 'yes' ? formatPrice(0) : formatPrice(parseFloat(tax.rate * order.order_subtotal / 100));

    return {
        tax_label: `${markup.tax_label}<p>${__('Tax', 'devdiggers-multipos-for-woocommerce')} (${tax.label})</p>`,
        order_tax: `${markup.order_tax}<p>${taxAmount}</p>`,
    };
}, { tax_label: '', order_tax: '' });
const getOnlineCouponMarkup = order => getOrderCoupons(order).reduce((markup, coupon) => ({
    coupon_name: `${markup.coupon_name}<p>${__('Coupon', 'devdiggers-multipos-for-woocommerce')} (${coupon.code})</p>`,
    coupon_amount: `${markup.coupon_amount}<p>${formatPrice(-coupon.amount, getOrderCurrencySymbol(order))}</p>`,
}), { coupon_name: '', coupon_amount: '' });
const getOfflineCouponAmountMarkup = coupon => {
    if (coupon.type === 'percent') {
        return `<p>${-coupon.price}%</p>`;
    }

    return `<p>${formatPrice(-coupon.price)}</p>`;
};
const getOfflineCouponMarkup = order => getOrderCoupons(order).reduce((markup, coupon) => ({
    coupon_name: `${markup.coupon_name}<p>${__('Coupon', 'devdiggers-multipos-for-woocommerce')} (${coupon.code})</p>`,
    coupon_amount: `${markup.coupon_amount}${getOfflineCouponAmountMarkup(coupon)}`,
}), { coupon_name: '', coupon_amount: '' });
const getOrderCustomerDetails = order => {
    const billing = getOrderBilling(order);

    if (order.order_type === 'online') {
        return {
            customer_fname: getBillingField(billing, 'fname', 'first_name'),
            customer_lname: getBillingField(billing, 'lname', 'last_name'),
            customer_phone: getBillingField(billing, 'phone'),
        };
    }

    return {
        customer_fname: getBillingField(billing, 'first_name', 'fname'),
        customer_lname: getBillingField(billing, 'last_name', 'lname'),
        customer_phone: getBillingField(billing, 'phone'),
    };
};
const getOrderDiscountMarkup = order => (
    order.order_type === 'online'
        ? formatPrice(order.discount, order.order_currency_symbol)
        : getOfflineOrderDiscountAmount(order.discount, order)
);
const getOrderTaxAndCouponMarkup = order => (
    order.order_type === 'online'
        ? {
            ...getOnlineOrderTaxMarkup(order),
            ...getOnlineCouponMarkup(order),
        }
        : {
            ...getOfflineOrderTaxMarkup(order),
            ...getOfflineCouponMarkup(order),
        }
);
const getFeeMarkup = order => getOrderFees(order).reduce((markup, fee) => ({
    fee_name: `${markup.fee_name}<p>${fee.name}</p>`,
    fee_amount: `${markup.fee_amount}<p>${formatPrice(fee.amount, getOrderCurrencySymbol(order))}</p>`,
}), { fee_name: '', fee_amount: '' });
const getBaseInvoiceDetails = (outlet, order) => {
    const baseDetails = {
        logo_invoice: getPosLogoUrl(),
        outlet_name: outlet.name || '',
        outlet_address1: outlet.address1 || '',
        outlet_address2: outlet.address2 || '',
        outlet_city: outlet.city || '',
        outlet_state: outlet.state || '',
        outlet_country: outlet.country || '',
        outlet_postcode: outlet.postcode || '',
        outlet_phone: outlet.phone || '',
        outlet_email: outlet.email || '',
        cashier_name: getCurrentPosUser().display_name || '',
        total_quantity: 0,
    };

    if (!order) {
        return {
            invoiceDetails: baseDetails,
            totalQuantity: 0,
        };
    }

    const tenderedMarkup = getTenderedPaymentMarkup(order);
    const invoiceProductRows = getInvoiceProductRows(order);

    return {
        totalQuantity: invoiceProductRows.totalQuantity,
        invoiceDetails: {
            ...baseDetails,
            table_name: getInvoiceTableName(order),
            tendered_payment_name: tenderedMarkup.names,
            tendered_payment_amount: tenderedMarkup.amounts,
            order_id: `#${order.order_id}`,
            order_date: order.order_date,
            sub_total: formatPrice(order.order_subtotal, order.order_currency_symbol),
            order_total: formatPrice(order.order_total, order.order_currency_symbol),
            order_change: formatPrice(order.change, order.order_currency_symbol),
            tendered_total: formatPrice(order.tendered, order.order_currency_symbol),
            product_row: invoiceProductRows.productRow,
            order_discount: getOrderDiscountMarkup(order),
            total_quantity: invoiceProductRows.totalQuantity,
            ...getOrderCustomerDetails(order),
            ...getOrderTaxAndCouponMarkup(order),
            ...getFeeMarkup(order),
        },
    };
};

const getTemplateScopedInvoiceValues = invoiceDetails => {
    let {
        logo_invoice,
        outlet_name,
        order_id,
        order_date,
        customer_fname,
        customer_lname,
        customer_phone,
        outlet_address1,
        outlet_address2,
        outlet_city,
        outlet_state,
        outlet_country,
        outlet_postcode,
        outlet_phone,
        outlet_email,
        pro_name,
        pro_quantity,
        pro_unit_price,
        pro_total,
        product_row,
        sub_total,
        tax_label,
        order_tax,
        coupon_name,
        coupon_amount,
        fee_name,
        fee_amount,
        order_discount,
        order_total,
        tendered_total,
        order_change,
        tendered_payment_name,
        tendered_payment_amount,
        table_name,
        cashier_name,
        total_quantity,
    } = {
        ...createEmptyInvoiceTokens(),
        ...invoiceDetails,
    };

    return {
        logo_invoice,
        outlet_name,
        order_id,
        order_date,
        customer_fname,
        customer_lname,
        customer_phone,
        outlet_address1,
        outlet_address2,
        outlet_city,
        outlet_state,
        outlet_country,
        outlet_postcode,
        outlet_phone,
        outlet_email,
        pro_name,
        pro_quantity,
        pro_unit_price,
        pro_total,
        product_row,
        sub_total,
        tax_label,
        order_tax,
        coupon_name,
        coupon_amount,
        fee_name,
        fee_amount,
        order_discount,
        order_total,
        tendered_total,
        order_change,
        tendered_payment_name,
        tendered_payment_amount,
        table_name,
        cashier_name,
        total_quantity,
    };
};

export const printInvoice = (order = '', print = true) => (dispatch, getState) => {
    const currentState = getState();
    const outlet = currentState.outlet || {};
    const settings = currentState.settings || {};
    const { invoiceDetails, totalQuantity } = getBaseInvoiceDetails(outlet, order);
    let {
        logo_invoice,
        outlet_name,
        order_id,
        order_date,
        customer_fname,
        customer_lname,
        customer_phone,
        outlet_address1,
        outlet_address2,
        outlet_city,
        outlet_state,
        outlet_country,
        outlet_postcode,
        outlet_phone,
        outlet_email,
        pro_name,
        pro_quantity,
        pro_unit_price,
        pro_total,
        product_row,
        sub_total,
        tax_label,
        order_tax,
        coupon_name,
        coupon_amount,
        fee_name,
        fee_amount,
        order_discount,
        order_total,
        tendered_total,
        order_change,
        tendered_payment_name,
        tendered_payment_amount,
        table_name,
        cashier_name,
        total_quantity,
    } = getTemplateScopedInvoiceValues(invoiceDetails);

    const pageWidth = settings.printer_width;
    const pageHeight = settings.printer_height;
    const pageMargin = settings.printer_margin;

    const selectedInvoice = getSelectedInvoice(outlet.invoice);
    let invoiceData = getInvoiceTemplate(selectedInvoice, pageWidth, pageHeight, pageMargin);

    invoiceData = eval('`' + invoiceData + '`');

    if (print) {
        openPrintWindow(invoiceData);
    }

    return invoiceData;
}

const openPrintWindow = (printContents, style = '') => {
    if (isMobilePrintDevice()) {
        const printWindow = window.open();

        if (!printWindow) {
            return;
        }

        printWindow.document.open();
        printWindow.document.clear();

        printWindow.document.writeln(`<html ${getLanguageAttributes()}><head><title></title>${style}`);
        printWindow.document.writeln('</head><body>');
        printWindow.document.writeln(printContents);
        printWindow.document.writeln('</body></html>');
        printWindow.document.close(); // necessary for IE >= 10

        printWindow.addEventListener('load', function () {
            setTimeout(() => {
                printWindow.focus(); // necessary for IE >= 10*-/
                printWindow.print();
            }, 500);
        }, true);
    } else {
        const iframeElement = document.createElement('iframe');

        iframeElement.name = 'frame1';
        document.body.appendChild(iframeElement);

        const iframeDoc = iframeElement.contentWindow ? iframeElement.contentWindow : iframeElement.contentDocument && iframeElement.contentDocument.document ? iframeElement.contentDocument.document : iframeElement.contentDocument;

        if (!iframeDoc || !iframeDoc.document) {
            document.body.removeChild(iframeElement);
            return;
        }

        iframeDoc.document.open();
        iframeDoc.document.write(`<html ${getLanguageAttributes()}><head><title></title>${style}`);
        iframeDoc.document.write('</head><body>');
        iframeDoc.document.write(printContents);
        iframeDoc.document.write('</body></html>');
        iframeDoc.document.close();

        setTimeout(() => {
            if (window.frames['frame1']) {
                window.frames['frame1'].focus();
                window.frames['frame1'].print();
            }
            document.body.removeChild(iframeElement);
        }, 500);
    }
}
