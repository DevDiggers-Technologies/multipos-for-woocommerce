# DevDiggers MultiPOS for WooCommerce

A WooCommerce Point of Sale terminal for your store. Create in-person orders from a browser, assign a cashier, scan barcodes, take payment, and print receipts — all saved as real WooCommerce orders.

**Contributors:** DevDiggers
**Author:** [DevDiggers](https://devdiggers.com/)
**Plugin URI:** https://devdiggers.com/product/multipos-point-of-sale-for-woocommerce/
**Live Demo:** https://demo.devdiggers.com/multipos-point-of-sale-for-woocommerce-free/
**Version:** 1.0.0
**License:** GPL-3.0 — http://www.gnu.org/licenses/gpl-3.0.html

| Requirement | Minimum | Tested up to |
| --- | --- | --- |
| WordPress | 6.0 | 7.0 |
| WooCommerce | 9.0.0 | 10.8.1 |
| PHP | 7.4 | — |

> This repository is the public, human-readable source for the plugin published on WordPress.org, including the un-minified JavaScript/CSS source and the build tooling used to compile the shipped assets.

---

## Building from source

The compiled assets in `assets/js/` and `assets/css/` are generated from the source in `src/` (and `devdiggers-framework/src/`) with webpack + Babel.

```bash
# 1. Install dependencies (Node.js 16+)
npm install

# 2. Build production assets into assets/js and assets/css
npm run build

# Development build with watch
npm run watch
```

Build a distributable plugin zip (honours `.distignore`, strips dev/source files):

```bash
npm run build      # compile assets first
wp dist-archive .  # or the project's bin/ build script
```

**Build tools:** webpack, Babel, and the `@wordpress/*` script packages declared in `package.json`.

---

## Repository structure

```text
.
├── functions.php            # Plugin bootstrap (headers, init, HPOS, framework load)
├── readme.txt               # WordPress.org readme
├── src/                     # Un-minified JS/LESS source (POS app, admin, login, dashboard)
├── assets/                  # Compiled JS/CSS + images shipped to users
├── api/                     # REST controllers (ddwcpos/v1)
├── includes/                # Bootstrap, admin, front, globals, install
├── helper/                  # Domain/database helpers (outlet, invoice, transaction, dashboard)
├── templates/               # Admin list/config templates + front POS templates
├── devdiggers-framework/    # Bundled DevDiggers admin framework
├── i18n/                    # Translation files (.pot)
├── webpack.config.js        # Asset build config
└── package.json             # Build dependencies and scripts
```

---

## Description

Cashiers log in at your POS URL, choose their assigned outlet, search products, scan barcodes, add customers, apply coupons, take payment, and print a receipt. Orders are saved as WooCommerce orders, so stock, tax, customer, coupon, and order records all stay in one place.

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
- Product ID or SKU as the default barcode source
- Custom barcode values assignable to products
- Barcode label printing with size and margin settings
- Default receipt template editor with HTML and CSS
- Receipt roll size and margin settings

### Customers and Coupons
- Search, add, edit, and delete customers from the POS
- Set a default guest customer account
- Apply WooCommerce coupons during checkout

### Dashboard and Logs
- Summary cards for orders, revenue, outlets, cashiers, and top payment method
- Revenue, outlet, and payment method charts
- Recent POS orders and POS transactions list

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
3. Enable the POS module and set the POS terminal URL path.
4. Create an outlet from **MultiPOS > Outlets**.
5. Create a cashier from **MultiPOS > Cashiers** and assign the outlet.
6. Open the POS from the **Visit POS** link.

If you change the POS URL path and the screen does not load, save your permalinks once from **Settings > Permalinks**.

---

## REST API

POS routes are registered under `/wp-json/ddwcpos/v1/`:

`get-products`, `get-product-categories`, `get-customers`, `get-countries-states`, `check-coupon`, `check-centralized-stock`, `manage-customer`, `delete-customer`, `create-order`, `get-orders`, `save-cashier`.

Routes require a logged-in user with WooCommerce management access, site management access, or the `ddwcpos_cashier` role.

---

## External services

This plugin connects only to DevDiggers (https://devdiggers.com) and only inside the WordPress admin:

- **Extensions directory** — `https://devdiggers.com/wp-json/ddwcs/v1/plugins`, read-only, loads the DevDiggers extensions list on the Extensions admin page (cached 24h). No personal or store data sent.
- **Newsletter (optional)** — only when an admin submits the newsletter form; sends the entered email + site URL.

See the readme [Terms](https://devdiggers.com/terms-and-conditions/) and [Privacy Policy](https://devdiggers.com/privacy-policy/).

---

## Free and Pro

**Free:** one outlet, one cashier, WooCommerce stock, cash payment, simple product sales, customer management, coupons, barcode labels, default receipt editing, POS orders and transactions.

**Pro:** multiple outlets and cashiers, custom/split payments, offline orders and sync, outlet-specific stock, variable products, custom product entries, weight/unit pricing, cash drawer, kitchen display, dine-in/takeaway, advanced reports, PWA settings, multiple invoice templates.

[Upgrade to MultiPOS Pro](https://devdiggers.com/product/multipos-point-of-sale-for-woocommerce/)

---

## Support

- **Documentation:** https://devdiggers.com/multipos-point-of-sale-for-woocommerce/
- **Support:** https://devdiggers.com/contact/
- WordPress.org support forum for free-version questions.

---

## Changelog

### 1.0.0
- Initial free version release.
- POS terminal for WooCommerce orders, setup wizard, admin dashboard.
- Outlet management, POS cashier role, barcode assignment and label printing.
- Receipt template editing, POS orders and transactions screens.
- WooCommerce HPOS compatibility.

---

## License

GPL-3.0 — see [LICENSE](http://www.gnu.org/licenses/gpl-3.0.html). Built by [DevDiggers](https://devdiggers.com/).
