<?php
/**
 * Invoice Helper class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 */

namespace DDWCMultiPOS\Helper\Invoice;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Invoice_Helper' ) ) {
	/**
	 * Invoice Helper class
	 */
	class DDWCPOS_Invoice_Helper {
		/*
		 * Configuration Variable
		 *
		 * @var array
		 */
		protected $ddwcpos_configuration;

		/**
		 * Construct
		 *
		 * @param array $ddwcpos_configuration
		 */
		public function __construct( $ddwcpos_configuration ) {
			$this->ddwcpos_configuration = $ddwcpos_configuration;
		}

		/**
		 * Get invoice html function
		 *
		 * @return void
		 */
		public function ddwcpos_get_invoice_html() {
			$logo_url = ! empty( $this->ddwcpos_configuration['logo'] ) ? wp_get_attachment_url( $this->ddwcpos_configuration['logo'] ) : DDWCPOS_PLUGIN_URL . 'assets/images/logo.png';

			$labels = [
				'logo_alt'        => esc_attr__( 'Store Logo', 'devdiggers-multipos-for-woocommerce' ),
				'sales_receipt'   => esc_html__( 'SALES RECEIPT', 'devdiggers-multipos-for-woocommerce' ),
				'order'           => esc_html__( 'Order', 'devdiggers-multipos-for-woocommerce' ),
				'date'            => esc_html__( 'Date', 'devdiggers-multipos-for-woocommerce' ),
				'cashier'         => esc_html__( 'Cashier', 'devdiggers-multipos-for-woocommerce' ),
				'customer'        => esc_html__( 'Customer', 'devdiggers-multipos-for-woocommerce' ),
				'item_name'       => esc_html__( 'Item Name', 'devdiggers-multipos-for-woocommerce' ),
				'unit_price'      => esc_html__( 'Unit Price', 'devdiggers-multipos-for-woocommerce' ),
				'quantity'        => esc_html__( 'Quantity', 'devdiggers-multipos-for-woocommerce' ),
				'total_price'     => esc_html__( 'Total Price', 'devdiggers-multipos-for-woocommerce' ),
				'items_sold'      => esc_html__( '%s Item(s) Sold', 'devdiggers-multipos-for-woocommerce' ),
				'sub_total'       => esc_html__( 'Sub Total', 'devdiggers-multipos-for-woocommerce' ),
				'total'           => esc_html__( 'Total', 'devdiggers-multipos-for-woocommerce' ),
				'refunded'        => esc_html__( 'Refunded', 'devdiggers-multipos-for-woocommerce' ),
				'total_tendered'  => esc_html__( 'Total Tendered', 'devdiggers-multipos-for-woocommerce' ),
				'change'          => esc_html__( 'Change', 'devdiggers-multipos-for-woocommerce' ),
				'thank_you'       => esc_html__( 'THANK YOU, HAVE A NICE DAY.', 'devdiggers-multipos-for-woocommerce' ),
				'phone'           => esc_html__( 'Phone', 'devdiggers-multipos-for-woocommerce' ),
				'email'           => esc_html__( 'Email', 'devdiggers-multipos-for-woocommerce' ),
			];

			return '<div class="ddwcpos-invoice-container">
				<div class="invoice-header">
					<img src="' . esc_url( $logo_url ) . '" alt="' . $labels['logo_alt'] . '" />
					<hr />
					<h3>' . $labels['sales_receipt'] . '</h3>
					<hr />
				</div>
				<div class="invoice-details">
					<p><strong>' . $labels['order'] . ':</strong> ${order_id}</p>
					<p><strong>' . $labels['date'] . ':</strong> ${order_date}</p>
					<p><strong>' . $labels['cashier'] . ':</strong> ${cashier_name}</p>
					<p><strong>' . $labels['customer'] . ':</strong> ${customer_fname} ${customer_lname}</p>
				</div>
				<div class="product-details">
					<table>
						<thead>
							<tr>
								<th>' . $labels['item_name'] . '</th>
								<th>' . $labels['unit_price'] . '</th>
								<th>' . $labels['quantity'] . '</th>
								<th>' . $labels['total_price'] . '</th>
							</tr>
						</thead>
					</table>
					${product_row}
					<h3 style="text-align: center; margin: 12px 0 5px;">' . sprintf( $labels['items_sold'], '${total_quantity}' ) . '</h3>
					<hr />
					<div class="invoice-details">
						<div class="left-details">
							<p style="font-weight: 600;">' . $labels['sub_total'] . '</p>
						</div>
						<div class="right-details">
							<p style="font-weight: 600;">${sub_total}</p>
						</div>
					</div>
					<hr />
					<div class="invoice-details">
						<div class="left-details">
							<p>${tax_label}</p>
							${coupon_name}
							${fee_name}
						</div>
						<div class="right-details">
							<p>${order_tax}</p>
							${coupon_amount}
							${fee_amount}
						</div>
					</div>
					<hr />
					<div class="invoice-details">
						<div class="left-details">
							<p style="font-size: 22px; font-weight: 600;">' . $labels['total'] . '</p>
							<p>' . $labels['refunded'] . '</p>
							${tendered_payment_name}
						</div>
						<div class="right-details">
							<p style="font-size: 21px; font-weight: 600;">${order_total}</p>
							<p>${order_refunded}</p>
							${tendered_payment_amount}
						</div>
					</div>
					<hr />
					<div class="invoice-details">
						<div class="left-details">
							<p>' . $labels['total_tendered'] . '</p>
							<p>' . $labels['change'] . '</p>
						</div>
						<div class="right-details">
							<p>${tendered_total}</p>
							<p>${order_change}</p>
						</div>
					</div>
					<hr />
				</div>
				<div class="invoice-footer">
					<p style="margin: 10px 0; text-align: center;">' . $labels['thank_you'] . '</p>
					<h3 style="text-align: center;">${outlet_name}</h3>
					<p>${outlet_address1} ${outlet_address2}</p>
					<p>${outlet_city} ${outlet_state}</p>
					<p><strong>' . $labels['phone'] . ':</strong> ${outlet_phone}</p>
					<p><strong>' . $labels['email'] . ':</strong> ${outlet_email}</p>
				</div>
			</div>';
		}

		/**
		 * Get invoice css function
		 *
		 * @return void
		 */
		public function ddwcpos_get_invoice_css() {
			return str_replace(
				'                ',
				'',
				'
				@font-face {
					font-family: "OpenSans";
					font-style: normal;
					src: url(' . esc_url( DDWCPOS_PLUGIN_URL . 'assets/fonts/OpenSans-Regular.ttf' ) . ') format("truetype");
				}
				.ddwcpos-invoice-container {
					padding: 10px;
					border-radius: 2px;
					font-family: "OpenSans", sans-serif;
					word-break: break-word;
				}
				.ddwcpos-invoice-container * {
					padding: 0;
					margin: 0;
				}
				p {
					line-height: 1.5;
				}
				.invoice-header, .invoice-footer {
					text-align: center;
				}
				.invoice-header h3 {
					margin: 10px 0;
					text-align: center;
				}
				.invoice-header img {
					width: 50px;
					height: auto;
					margin: 10px 0;
				}
				.invoice-details {
					width: 100%;
					display: inline-block;
					padding: 8px 0;
				}
				.left-details, .right-details {
					width: 50%;
				}
				.invoice-details .left-details {
					float: left;
				}
				.invoice-details .right-details {
					float: right;
					text-align: right;
				}
				.product-details table {
					border-collapse: collapse;
					width: 100%;
					text-align: center;
				}
				.product-details table thead th {
					padding: 12px 0;
				}
				.product-details table tr th:nth-child(1), .product-details table tr td:nth-child(1) {
					text-align: left;
				}
				.product-details table tr th:nth-child(4), .product-details table tr td:nth-child(4) {
					text-align: right;
				}
				.product-details table th, .product-details table td {
					padding: 3px 0;
				}
				.product-details table th:first-child,
				.product-details table td:first-child {
					width: 35%;
				}
				.product-details table th, .product-details table td p {
					padding: 3px 0;
				}
				.product-details table thead {
					border-style: dashed;
					border-width: 3px 0 3px;
					border-color: #ddd;
				}
				hr {
					margin: 0 auto;
					border-style: dashed;
					border-width: 3px 0;
					border-top-color: #ddd;
					border-bottom-color: #fafafa;
					clear: both;
				}'
			);
		}
	}
}
