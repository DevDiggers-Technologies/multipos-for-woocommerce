<?php
/**
 * API Get Orders class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\API\Includes\Orders;

use DDWCMultiPOS\API\Includes\Common\DDWCPOS_API_Base_Controller;
use DDWCMultiPOS\Helper\Outlet\DDWCPOS_Outlet_Helper;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_API_Get_Orders' ) ) {
	/**
	 * API Get Orders Class.
	 * 
	 * Handles order retrieval for POS system with proper validation,
	 * error handling, and standardized responses.
	 */
	class DDWCPOS_API_Get_Orders extends DDWCPOS_API_Base_Controller {
		/**
		 * Base Name.
		 *
		 * @var string the route base
		 */
		public $base = 'get-orders';

		/**
		 * Required parameters for validation
		 *
		 * @var array
		 */
		protected $required_params = [ 'cashier_id', 'outlet_id' ];

		/**
		 * DB variable
		 *
		 * @var object
		 */
		protected $wpdb;

		/**
		 * Outlet Helper Variable
		 *
		 * @var object
		 */
		protected $outlet_helper;

		/**
		 * Constructor.
		 * 
		 * @param array $ddwcpos_configuration Configuration array.
		 */
		public function __construct( $ddwcpos_configuration ) {
			parent::__construct( $ddwcpos_configuration );
			global $wpdb;
			$this->wpdb = $wpdb;
			$this->outlet_helper = new DDWCPOS_Outlet_Helper();
		}

		/**
		 * Execute the specific API logic for getting orders.
		 * 
		 * @param array $request Sanitized request data.
		 * @return array|WP_Error Order data or error.
		 */
		protected function execute_api_logic( $request ) {
			$cashier_id   = intval( $request['cashier_id'] );
			$outlet_id    = intval( $request['outlet_id'] );
			$per_page     = isset( $request['per_page'] ) ? intval( $request['per_page'] ) : 10;
			$current_page = isset( $request['current_page'] ) ? intval( $request['current_page'] ) : 1;

			// Validate user permissions
			$user_validation = $this->validate_user_permissions( $cashier_id );
			if ( is_wp_error( $user_validation ) ) {
				return $user_validation;
			}

			// Validate outlet access
			$outlet_validation = $this->validate_outlet_access( $outlet_id, $cashier_id );
			if ( is_wp_error( $outlet_validation ) ) {
				return $outlet_validation;
			}

			// Handle count request
			if ( -1 === $per_page ) {
				return $this->get_orders_count( $request, $outlet_id );
			}

			// Handle orders list request
			return $this->get_orders_list( $request, $outlet_id, $per_page, $current_page );
		}

		/**
		 * Get total orders count.
		 *
		 * @param array $request Request data.
		 * @param int $outlet_id Outlet ID.
		 * @return int Total orders count.
		 */
		protected function get_orders_count( $request, $outlet_id ) {
			$args = [
				'paginate'     => true,
				'limit'        => 1,
				'return'       => 'ids',
				'meta_key'     => '_ddwcpos_outlet_id',
				'meta_compare' => '=',
				'meta_value'   => $outlet_id,
			];

			$args = apply_filters( 'ddwcpos_modify_api_get_orders_count_args', $args, $request );
			$result = wc_get_orders( $args );

			return apply_filters( 'ddwcpos_modify_api_total_orders_count', $result->total, $request );
		}

		/**
		 * Get orders list with pagination.
		 *
		 * @param array $request Request data.
		 * @param int $outlet_id Outlet ID.
		 * @param int $per_page Items per page.
		 * @param int $current_page Current page.
		 * @return array Orders data.
		 */
		protected function get_orders_list( $request, $outlet_id, $per_page, $current_page ) {
			$data = [];
			$off  = ( $current_page - 1 ) * $per_page;

			$args = [
				'posts_per_page' => $per_page,
				'offset'         => $off,
				'order'          => 'DESC',
				'orderby'        => 'ID',
				'return'         => 'ids',
				'meta_key'       => '_ddwcpos_outlet_id',
				'meta_compare'   => '=',
				'meta_value'     => $outlet_id,
			];

			$args = apply_filters( 'ddwcpos_modify_api_get_orders_args', $args, $request );
			$order_ids = wc_get_orders( $args );

			if ( ! empty( $order_ids ) ) {
				foreach ( $order_ids as $order_id ) {
					$order = wc_get_order( $order_id );
					$order_response = $this->ddwcpos_prepare_order_data( $order_id );
					$data[] = apply_filters( 'ddwcpos_modify_api_order_response', $order_response, $order, $request );
				}
			}

			return apply_filters( 'ddwcpos_modify_api_orders_response', $data, $request );
		}

		/**
		 * Prepare order data function
		 *
		 * @param int $order_id
		 * @return array
		 */
		public function ddwcpos_prepare_order_data( $order_id ) {
			$order_response  = [];
			$order           = wc_get_order( $order_id );
			$order_items     = $order->get_items();
			$tendered        = floatval( $order->get_meta( '_ddwcpos_tendered_amount', true ) );
			$table           = $order->get_meta( '_ddwcpos_table', true );
			$payment_methods = $order->get_meta( '_ddwcpos_payment_methods', true );

			$order_response[ 'order_status' ]          = $order->get_status();
			$order_response[ 'order_status_label' ]    = wc_get_order_status_name( $order_response[ 'order_status' ] );
			$order_response[ 'order_currency' ]        = $order->get_currency();
			$order_response[ 'order_currency_symbol' ] = html_entity_decode( get_woocommerce_currency_symbol( $order->get_currency() ) );
			$order_response[ 'id' ]                    = $order_id;
			$order_response[ 'order_id' ]              = $order->get_order_number();
			$order_response[ 'customer_id' ]           = $order->get_customer_id();

			$id = 0;

			$order_response[ 'products' ] = [];

			// order items loop
			foreach ( $order_items as $key => $value ) {
				$product_id = $value->get_product_id();
				$value_data = $value->get_data();
				$meta       = apply_filters( 'ddwcpos_get_order_item_meta_data', [], $value_data );
				$image      = null;
				$product    = wc_get_product( $product_id );
				$image      = $product->get_image( 'thumbnail' );
				$type       = $product->get_type();

				$value_data[ 'quantity' ] = ! empty( $value_data[ 'quantity' ] ) ? $value_data[ 'quantity' ] : 1;

				$product_total_price = apply_filters( 'ddwcpos_modify_order_product_total_price', $value_data[ 'subtotal' ], $value );
				$product_unit_price  = $product_total_price / $value_data[ 'quantity' ];
				$taxes = $value_data[ 'taxes' ][ 'subtotal' ];

				if ( ! empty( $taxes ) ) {
					foreach ( $taxes as $k => $v ) {
						$taxes[ $k ] = wc_format_decimal( floatval( $taxes[ $k ] ) / $value_data[ 'quantity' ] );
					}
				}

				$order_response[ 'products' ][] = [
					'item_id'           => $key,
					'id'                => $product_id,
					'parent'            => 0,
					'product_id'        => $product_id,
					'type'              => $type,
					'name'              => $value[ 'name' ],
					'quantity'          => $value_data[ 'quantity' ],
					'image'             => $image,
					'uf'                => $product_unit_price,
					'uf_total'          => $product_total_price,
					'meta_data'         => $meta,
					'taxes'             => $taxes,
				];
			}

			$order_response[ 'tax_lines' ] = [];

			// order tax
			foreach ( $order->get_tax_totals() as $tax_code => $tax ) {
				$order_response[ 'tax_lines' ][] = [
					'id'       => $tax->id,
					'rate_id'  => $tax->rate_id,
					'code'     => $tax_code,
					'label'    => $tax->label,
					'total'    => wc_format_decimal( $tax->amount, 2 ),
					'compound' => (bool) $tax->is_compound,
				];
			}

			$order_response[ 'billing' ] = [
				'fname'    => $order->get_billing_first_name(),
				'lname'    => $order->get_billing_last_name(),
				'address1' => $order->get_billing_address_1(),
				'address2' => $order->get_billing_address_2(),
				'phone'    => $order->get_billing_phone(),
				'city'     => $order->get_billing_city(),
				'state'    => $order->get_billing_state(),
				'country'  => WC()->countries->countries[ $order->get_billing_country() ],
				'postcode' => $order->get_billing_postcode(),
			];

			$order_response[ 'shipping' ] = [
				'fname'    => $order->get_shipping_first_name(),
				'lname'    => $order->get_shipping_last_name(),
				'address1' => $order->get_shipping_address_1(),
				'address2' => $order->get_shipping_address_2(),
				'city'     => $order->get_shipping_city(),
				'state'    => $order->get_shipping_state(),
				'country'  => WC()->countries->countries[ $order->get_shipping_country() ],
				'postcode' => $order->get_shipping_postcode(),
			];

			$order_date  = $order->get_date_created();

			$order_response[ 'order_created' ] = $order_date->date( 'Y-m-d H:i:s' );

			$order_date  = $order_date->date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) );
			$order_total = floatval( $order->get_total() );

			$order_response[ 'tendered' ]             = $tendered;
			$order_response[ 'table' ]                = $table;
			$order_response[ 'change' ]               = ( $tendered - $order_total ) > 0 ? $tendered - $order_total : 0;
			$order_response[ 'email' ]                = $order->get_billing_email();
			$order_response[ 'order_date' ]           = $order_date;
			$order_response[ 'payment_method' ]       = $order->get_payment_method();
			$order_response[ 'payment_method_title' ] = $order->get_payment_method_title();
			$order_response[ 'payment_methods' ]      = ! empty( $payment_methods ) ? $payment_methods : [];

			$coupons = [];

			$order_coupons = $order->get_items( 'coupon' );

			if ( ! empty( $order_coupons ) ) {
				foreach ( $order_coupons as $order_coupon ) {
					$coupons[] = [
						'code'   => $order_coupon->get_code(),
						'amount' => $order_coupon->get_discount(),
					];
				}
			}

			$fees = [];

			foreach ( $order->get_fees() as $order_fees ) {
				$fees[] = [
					'name'   => $order_fees->get_name(),
					'amount' => $order_fees->get_total(),
				];
			}

			$order_response[ 'coupons' ]        = $coupons;
			$order_response[ 'fees' ]           = $fees;
			$order_response[ 'order_subtotal' ] = $order->get_subtotal();
			$order_response[ 'discount' ]       = 0;

			$order_response[ 'order_type' ]     = 'online';
			$order_response[ 'order_total' ]    = $order_total;

			return apply_filters( 'ddwcpos_modify_api_single_order_response', $order_response, $order );
		}
	}
}
