# MultiPOS - Point of Sale for WooCommerce

**Contributors:** DevDiggers  
**Plugin URI:** https://devdiggers.com/product/multipos-point-of-sale-for-woocommerce/  
**Author:** DevDiggers  
**Author URI:** https://devdiggers.com/  
**Version:** 1.0.0  
**License:** GNU General Public License v3.0  
**License URI:** http://www.gnu.org/licenses/gpl-3.0.html

**WordPress Requirements**
- Requires at least: 6.0
- Tested up to: 7.0

**WooCommerce Requirements**
- Requires at least: 9.0.0
- Tested up to: 10.8.1

---

## Description

**MultiPOS - Point of Sale for WooCommerce** adds a POS terminal to your WooCommerce store so you can create in-person orders from a browser.

Cashiers can log in at your POS URL, choose their assigned outlet, search products, scan barcodes, add customers, apply coupons, take payment, and print a receipt. Orders are saved as WooCommerce orders, so your stock, tax, customer, coupon, and order records stay in one place.

The free version is best for a small store starting with one counter: one outlet, one cashier, cash payment, WooCommerce stock, barcode labels, receipt editing, POS orders, and transaction tracking.

---

## Key Features

### POS Terminal
- Custom POS URL, for example `/pos`
- Product search and barcode-based adding
- Cart, customer, coupon, and payment screens
- Hold cart support for unfinished sales
- Receipt printing after checkout
- Layout color, font size, and product card settings

### Outlet and Cashier Management
- One active outlet in the free version
- One POS cashier in the free version
- Outlet address, phone, email, payment, receipt, and table settings
- Cashier assignment through the WordPress user screen
- Administrators and shop managers can access the POS

### WooCommerce Orders
- POS sales are created as WooCommerce orders
- Default order status setting for POS orders
- Optional WooCommerce order emails for POS sales
- POS order type marker in the WooCommerce orders list
- Outlet, cashier, payment, and tendered amount saved with the order
- WooCommerce stock check before checkout

### Barcodes and Receipts
- Product ID or SKU can be used as the default barcode source
- Custom barcode values can be assigned to products
- Barcode label printing with size and margin settings
- Default receipt template editor with HTML and CSS
- Receipt roll size and margin settings

### Customers and Coupons
- Search existing customers from the POS
- Add and edit customer details
- Delete customers where needed
- Set a default guest customer account
- Apply WooCommerce coupons during checkout

### Dashboard and Logs
- Summary cards for orders, revenue, outlets, cashiers, and top payment method
- Revenue, outlet, and payment method charts
- Recent POS orders
- POS transactions list
- Admin lists for outlets, cashiers, barcodes, orders, transactions, and invoices

### Technical Notes
- HPOS compatible
- Translation-ready with `devdiggers-multipos-for-woocommerce` text domain
- REST API namespace: `ddwcpos/v1`
- React-based POS app
- PHP hooks and JavaScript filters for custom work

---

## Setup

1. Install and activate the plugin.
2. Follow the setup wizard, or go to **MultiPOS > Configuration**.
3. Enable the POS module.
4. Set the POS terminal URL path.
5. Create an outlet from **MultiPOS > Outlets**.
6. Create a cashier from **MultiPOS > Cashiers** and assign the outlet.
7. Open the POS from the **Visit POS** link.

If you change the POS URL path and the screen does not load, save your permalinks once from **Settings > Permalinks**.

---

## Configuration Areas

### General
Enable the POS, choose the default order status, choose the barcode source, upload a logo, set a default guest customer, and set the POS URL path.

### Payments
The free version includes the built-in Cash method. Custom payment methods and split payments are available in Pro.

### Login
Change the POS login heading, subtitle, footer, button text, remember me option, forgot password link, and colors.

### Printer
Set barcode label dimensions and receipt roll dimensions.

### Tables
Create table names and seating counts for restaurant/cafe outlet setups. Kitchen display and advanced restaurant routing are Pro features.

### Invoices
Edit the default receipt template and CSS. More invoice templates are available in Pro.

### Layout
Set POS colors, base font size, product card orientation, and product stock visibility.

---

## Free and Pro

The free version covers a practical first POS counter:

- One outlet
- One POS cashier
- WooCommerce stock
- Cash payment
- Simple product sales
- Customer management
- Coupon handling
- Barcode labels
- Default receipt editing
- POS orders and transactions

MultiPOS Pro is for stores that need more room:

- Multiple outlets
- Multiple cashiers
- Custom and split payment methods
- Offline order handling and later sync
- Outlet-specific stock
- Variable product workflows
- Custom product entries
- Weight or unit-based pricing
- Cash drawer tracking
- Kitchen display
- Dine-in and takeaway workflows
- Advanced reports
- PWA app settings
- Multiple invoice templates

[Upgrade to MultiPOS Pro](https://devdiggers.com/product/multipos-point-of-sale-for-woocommerce/)

---

## REST API

MultiPOS registers POS routes under:

```text
/wp-json/ddwcpos/v1/
```

Main routes include:

- `get-products`
- `get-product-categories`
- `get-customers`
- `get-countries-states`
- `check-coupon`
- `check-centralized-stock`
- `manage-customer`
- `delete-customer`
- `create-order`
- `get-orders`
- `save-cashier`

Routes require a logged-in user with WooCommerce management access, site management access, or the `ddwcpos_cashier` role.

---

## Support

- **Documentation:** https://devdiggers.com/multipos-point-of-sale-for-woocommerce/
- **Support:** https://devdiggers.com/contact/
- **Product Page:** https://devdiggers.com/product/multipos-point-of-sale-for-woocommerce/

Free version users can also use the WordPress.org support forum.

---

## Changelog

### Version 1.0.0
- Initial free version release.
- Added POS terminal for WooCommerce orders.
- Added setup wizard and admin dashboard.
- Added outlet management and POS cashier role.
- Added barcode assignment and label printing.
- Added receipt template editing.
- Added POS orders and transactions screens.
- Added WooCommerce HPOS compatibility.

---

## About DevDiggers

DevDiggers builds WordPress and WooCommerce plugins for store owners who need practical tools inside their own site. Visit [devdiggers.com](https://devdiggers.com/) for more extensions.
