=== DevDiggers MultiPOS for WooCommerce ===
Contributors: DevDiggers
Plugin URI: https://devdiggers.com/product/multipos-point-of-sale-for-woocommerce/
Author: DevDiggers
Author URI: https://devdiggers.com/
Tags: woocommerce pos, point of sale, pos system, barcode scanner, cashier
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.4
WC requires at least: 9.0.0
WC tested up to: 10.7.0
Stable tag: 1.0.0
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add a WooCommerce point of sale screen to your store. Create in-person orders, assign a cashier, print receipts, and keep stock tied to WooCommerce.

== Description ==

MultiPOS is a **[WooCommerce point of sale plugin](https://devdiggers.com/product/multipos-point-of-sale-for-woocommerce/)** for store owners who sell both online and in person.

It gives your store a dedicated POS terminal at a custom URL such as `yoursite.com/pos`. A cashier can log in, choose an assigned outlet, search products, scan barcodes, add customers, apply coupons, take payment, and create a WooCommerce order from the same screen.

The free version is made for a simple first POS setup: one outlet, one cashier, WooCommerce stock, cash payment, barcode management, receipt printing, and basic POS order tracking. It is a good fit for small retail counters, pickup desks, market stalls, pop-up shops, and stores that want their in-person sales recorded inside WooCommerce.

Because orders are created as real WooCommerce orders, you can still use your normal order list, taxes, coupons, customer data, and stock handling.

= Quick Links =

* [View Product Page](https://devdiggers.com/product/multipos-point-of-sale-for-woocommerce/)
* [Full Documentation](https://devdiggers.com/multipos-point-of-sale-for-woocommerce/)
* [Contact Support](https://devdiggers.com/contact/)
* [Upgrade to Pro](https://devdiggers.com/product/multipos-point-of-sale-for-woocommerce/)

---

= Key Features =

* Dedicated POS terminal with a custom URL
* Setup wizard for the first configuration
* One active outlet in the free version
* One POS cashier role in the free version
* Product search and barcode-based product adding
* WooCommerce stock checking before checkout
* Cash payment method
* POS orders saved as WooCommerce orders
* Order type marker in the WooCommerce orders list
* Customer search, add, edit, and delete from the POS
* Coupon support during POS checkout
* Hold cart support for unfinished sales
* Receipt template editor with HTML and CSS fields
* Barcode labels for products
* POS dashboard with orders, revenue, outlets, cashiers, and payment method summaries
* Transaction log for POS activity
* Login screen text and color settings
* Receipt and barcode printer size settings
* POS layout colors, font size, and product card layout settings
* Translation-ready with `.pot` file included
* WooCommerce High-Performance Order Storage (HPOS) compatible

= How It Works =

After activation, MultiPOS adds a new **MultiPOS** panel inside your WordPress admin.

1. Run the setup wizard or open **MultiPOS > Configuration**
2. Enable the POS module
3. Choose the POS URL path, for example `pos`
4. Create your outlet and enter its address
5. Create a POS cashier and assign the outlet
6. Visit the POS terminal and start creating in-person orders

Cashiers do not need full WordPress admin access. They log in from the POS screen and only see the outlet assigned to them.

---

== WooCommerce POS - Free Version Features ==

=== POS Terminal ===

The POS terminal is a clean sales screen for your in-person counter.

* Open the POS from a custom URL such as `/pos`
* Allow administrators, shop managers, and assigned POS cashiers to access the terminal
* Search products from your WooCommerce catalog
* Add products by clicking or by entering/scanning a barcode
* Show product stock on product cards
* Choose product card layout from the admin
* Use WooCommerce prices, tax settings, and coupons
* Print the order receipt after checkout

=== Outlet Management ===

An outlet is the physical place where sales happen.

* Create one active outlet in the free version
* Add outlet name, address, phone, and email
* Choose grocery/retail or restaurant/cafe mode
* Assign payment methods and receipt template to the outlet
* Mark an outlet active or inactive
* Use outlet address for POS tax and receipt details

=== Cashier Management ===

MultiPOS creates a dedicated `POS Cashier` role.

* Create one POS cashier in the free version
* Assign the cashier to the outlet
* Let administrators and shop managers access the POS when needed
* Cashiers can update their basic profile details from the POS
* Cashier and outlet details are attached to POS orders

=== Orders and WooCommerce Integration ===

POS sales are stored as WooCommerce orders, not separate records.

* Create WooCommerce orders from the POS
* Set the default order status for POS orders
* Choose whether WooCommerce order emails should be sent for POS sales
* Add POS order source details to the WooCommerce orders list
* View POS orders from the MultiPOS admin orders screen
* Save outlet, cashier, payment, tendered amount, and table data as order meta
* Check WooCommerce stock before placing an order

=== Customers and Coupons ===

Cashiers can work with customer data without leaving the POS.

* Search existing customers
* Add a new customer from the POS screen
* Edit customer name, email, phone, and address details
* Delete customers when needed
* Set a default guest account for walk-in sales
* Apply WooCommerce coupon codes during POS checkout

=== Barcode Management ===

MultiPOS can use product IDs or SKUs as the base for barcode labels.

* Choose product ID or SKU as the default barcode source
* Assign a custom barcode value to products
* Print barcode labels from the admin product barcode screen
* Configure barcode page width, height, margins, barcode height, spacing, and orientation
* Add products to the POS cart by scanning or entering the barcode value

=== Receipts and Printing ===

You can adjust how printed POS receipts and labels behave.

* Edit the default invoice/receipt template
* Use HTML and CSS fields for receipt layout changes
* Configure receipt roll width, height, and margin
* Configure barcode label size and print orientation
* Use outlet details, order details, customer details, totals, taxes, and payment data in printed receipts

=== Dashboard and Transactions ===

The admin dashboard gives a quick view of POS activity.

* Summary cards for total orders, revenue, active outlets, cashiers, and top payment method
* Revenue chart with date range filters
* Sales by outlet and payment method charts
* Recent POS orders table
* POS transaction list with outlet, cashier, method, amount, and date
* Search and filter admin lists where available

=== Setup and Branding ===

The plugin includes simple settings for the first setup and daily use.

* Setup wizard shown after activation
* Enable or disable the POS module
* Customize POS login heading, subtitle, footer, button text, and colors
* Upload a POS logo
* Change POS layout colors and base font size
* Choose whether the login screen shows remember me and forgot password links

=== For Developers ===

MultiPOS is built with WordPress and WooCommerce hooks in mind.

* REST API namespace: `ddwcpos/v1`
* Routes for products, categories, customers, countries/states, coupons, stock checks, orders, and cashier saving
* PHP hooks for order creation, POS access, outlet data, products, customers, transactions, and API responses
* JavaScript filters and actions inside the React POS app
* HPOS compatibility declared for WooCommerce custom order tables
* Translation-ready text domain: `devdiggers-multipos-for-woocommerce`

---

== MultiPOS Pro - What You Get On Top ==

The Pro version is built for stores with more than one counter, branch, cashier, payment flow, or restaurant workflow.

=== Multi-Outlet and Staff Workflows ===

* Add unlimited outlets
* Assign different cashiers to different outlets
* Manage outlet-specific workflows
* Use outlet stock controls for multi-location inventory

=== Advanced Checkout Tools ===

* Multiple and split payment methods
* Custom payment methods
* Order notes during POS checkout
* Custom product entries from the POS
* Weight or unit-based pricing
* Cash drawer tracking
* Faster offline order handling and later sync

=== Restaurant and Kitchen Tools ===

* Kitchen display URL
* Send held orders to the kitchen
* Restaurant table workflows
* Dine-in and takeaway handling
* Kitchen preparation status tracking

=== Reports and Inventory ===

* Advanced POS reports
* Revenue stats with date range filtering
* Product performance reports
* Coupon usage reports
* Order statistics by outlet
* Tax reports and summaries
* Outlet-specific stock editing

=== App and Layout Options ===

* Progressive Web App configuration
* Custom app name, icon, splash color, and theme color
* More product and variation display options
* More receipt and invoice templates

[Upgrade to MultiPOS Pro](https://devdiggers.com/product/multipos-point-of-sale-for-woocommerce/)

== Installation ==

**Automatic Installation**

1. Go to **Plugins > Add New** in your WordPress admin.
2. Search for **MultiPOS - Point of Sale for WooCommerce**.
3. Click **Install Now**, then **Activate**.

**Manual Installation**

1. Download the plugin zip file.
2. Go to **Plugins > Add New > Upload Plugin**.
3. Upload the zip file, click **Install Now**, then **Activate**.

You can also upload the `devdiggers-multipos-for-woocommerce` folder to `/wp-content/plugins/` using FTP, then activate it from the Plugins menu.

**After Activation**

1. Follow the setup wizard, or go to **MultiPOS > Configuration > General**.
2. Enable the Point of Sale module.
3. Set your POS terminal URL path.
4. Create one outlet from **MultiPOS > Outlets**.
5. Create one cashier from **MultiPOS > Cashiers** and assign the outlet.
6. Open the POS using the **Visit POS** link.

== Frequently Asked Questions ==

= Does MultiPOS create normal WooCommerce orders? =

Yes. POS sales are created as WooCommerce orders. The plugin adds POS details such as outlet, cashier, payment method, tendered amount, and offline ID where available, but the order still lives in WooCommerce.

= Can I use MultiPOS with one physical store? =

Yes. The free version is designed for one outlet and one cashier. That is enough for a small counter, pickup desk, pop-up shop, or a store that wants to test POS selling inside WooCommerce.

= Can I add more than one outlet? =

The free version supports one outlet. MultiPOS Pro adds multiple outlets for stores with more than one branch, register, or sales location.

= Can I add more than one cashier? =

The free version supports one POS cashier role user. Administrators and shop managers can also access the POS. MultiPOS Pro is needed when you want multiple cashier accounts.

= Does it work with WooCommerce stock? =

Yes. The free version uses centralized WooCommerce stock. Before checkout, the POS can check whether the products in the cart are still available.

= Can I use custom stock per outlet? =

Outlet-specific stock management is a Pro feature. In the free version, stock comes from WooCommerce.

= Which product types are supported in the free version? =

The free version is focused on simple WooCommerce products. Variable product and more advanced product workflows are handled in Pro.

= Can cashiers scan barcodes? =

Yes. Products can be added by barcode value. You can use the product ID or SKU as the default barcode source, or assign a custom barcode value to products.

= Can I print barcode labels? =

Yes. The admin barcode screen lets you print labels and configure label size, barcode size, margins, and orientation.

= Can customers be created from the POS? =

Yes. Cashiers can add and edit customers from the POS screen. Customer data is saved to WooCommerce customer records.

= Can customers use coupons at the POS? =

Yes. WooCommerce coupon codes can be checked and applied during POS checkout.

= Does the POS work offline? =

The codebase includes order sync handling, but the free version keeps the offline order setting locked. Use Pro if offline selling and later sync are part of your daily workflow.

= Can I change the POS URL? =

Yes. Go to **MultiPOS > Configuration > General** and update the POS terminal URL path. If the page does not load after changing it, save your WordPress permalinks once from **Settings > Permalinks**.

= Does it support restaurant tables? =

You can define tables and choose restaurant/cafe mode for an outlet. Full kitchen display and advanced restaurant order routing are Pro features.

= Can I customize the receipt? =

Yes. The free version lets you edit the default receipt template and CSS. Pro adds more invoice template options.

= Is MultiPOS compatible with HPOS? =

Yes. The plugin declares compatibility with WooCommerce High-Performance Order Storage.

= Where can I get help? =

Free version questions can be asked through the WordPress.org support forum. You can also read the documentation at [devdiggers.com/multipos-point-of-sale-for-woocommerce/](https://devdiggers.com/multipos-point-of-sale-for-woocommerce/) or contact DevDiggers from [devdiggers.com/contact/](https://devdiggers.com/contact/).

== Screenshots ==

1. MultiPOS admin dashboard showing orders, revenue, outlets, cashiers, and payment method summaries.
2. POS login screen with configurable heading, subtitle, logo, colors, and login button text.
3. POS outlet selection screen for users assigned to a store location.
4. POS product grid where cashiers search products and add items to the cart.
5. POS cart screen with customer selection, coupon handling, totals, and checkout actions.
6. Customer management screen inside the POS.
7. Payment screen for completing a POS order.
8. Printed receipt preview from a completed POS order.
9. Outlets list in the MultiPOS admin panel.
10. Add/edit outlet screen with address, payment, invoice, and table settings.
11. Cashiers list showing users and assigned outlets.
12. Product barcode list with barcode assignment and print label action.
13. Orders list showing POS orders with outlet and cashier attribution.
14. Transactions list showing POS cash and order activity.
15. Invoice editor for customizing the default receipt template.
16. Configuration screens for general, payment, login, printer, table, and layout settings.
17. Setup wizard for first-time configuration.

== Changelog ==

= 1.0.0 =
* Initial free version release.
* Added POS terminal for WooCommerce orders.
* Added setup wizard, outlet management, cashier role, barcode labels, receipt editing, POS dashboard, and transaction log.
* Added WooCommerce HPOS compatibility.
