<?php
/**
 * API Create Order class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\API\Includes\Orders;

use DDWCMultiPOS\API\Includes\Common\DDWCPOS_API_Base_Controller;
use DDWCMultiPOS\Helper\Outlet\DDWCPOS_Outlet_Helper;
use DDWCMultiPOS\Helper\Transaction\DDWCPOS_Transaction_Helper;
use Automattic\WooCommerce\Utilities\NumberUtil;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_API_Create_Order' ) ) {
	/**
	 * API Create Order Class.
	 * 
	 * Handles order creation for POS system with proper validation,
	 * error handling, and standardized responses.
	 */
	class DDWCPOS_API_Create_Order extends DDWCPOS_API_Base_Controller {
		/**
		 * Base Name.
		 *
		 * @var string the route base
		 */
		public $base = 'create-order';

		/**
		 * Required parameters for validation
		 *
		 * @var array
		 */
		protected $required_params = [ 'cashier_id', 'outlet_id' ];

		/**
		 * DB Variable
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
		 * Transaction Helper Variable
		 *
		 * @var object
		 */
		protected $transaction_helper;

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
			$this->transaction_helper = new DDWCPOS_Transaction_Helper();
		}

		/**
		 * Execute the specific API logic for creating orders.
		 * 
		 * @param array $request Sanitized request data.
		 * @return array|WP_Error Order data or error.
		 */
		protected function execute_api_logic( $request ) {
			$cashier_id = intval( $request['cashier_id'] );
			$outlet_id  = intval( $request['outlet_id'] );

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

			// Handle single order creation
			if ( ! empty( $request['order_data'] ) ) {
				$order_data = json_decode( $request['order_data'], true );
				if ( json_last_error() !== JSON_ERROR_NONE ) {
					return $this->error_response(
						esc_html__( 'Invalid JSON data in order_data', 'devdiggers-multipos-for-woocommerce' ),
						'invalid_json_data',
						400
					);
				}
				return $this->create_single_order( $order_data, $request );
			}

			if ( ! empty( $request['offline_orders'] ) ) {
				return $this->error_response(
					esc_html__( 'Offline order sync is not available in the free version.', 'devdiggers-multipos-for-woocommerce' ),
					'offline_orders_disabled',
					403
				);
			}

			return $this->error_response(
				esc_html__( 'No order data provided', 'devdiggers-multipos-for-woocommerce' ),
				'no_order_data',
				400
			);
		}

		/**
		 * Create a single order.
		 *
		 * @param array $order_data Order data.
		 * @param array $request Original request.
		 * @return array|WP_Error Order response or error.
		 */
		protected function create_single_order( $order_data, $request ) {
			try {
				$order = $this->ddwcpos_create_order( $order_data, $request );
				return $order;
			} catch ( \Exception $e ) {
				return $this->error_response(
					$e->getMessage(),
					'order_creation_failed',
					500
				);
			}
		}

		/**
		 * Create Order.
		 * 
		 * @param array $order_data
		 * @param object $request
		 * @return array
		 */
		public function ddwcpos_create_order( $order_data, $request ) {
			$order_response     = [];
			$outlet_id          = $request[ 'outlet_id' ];
			$cashier_id         = $request[ 'cashier_id' ];
			$outlet_helper      = new DDWCPOS_Outlet_Helper();
			$outlet_data        = $outlet_helper->ddwcpos_get_outlet_details_by_id( $outlet_id );
			$inventory_type     = 'centralized';
			$customer_id        = $order_data[ 'customer_id' ];
			$coupons            = ! empty( $order_data[ 'coupons' ] ) ? $order_data[ 'coupons' ] : [];
			$fees               = ! empty( $order_data[ 'fees' ] ) ? $order_data[ 'fees' ] : [];
			$payment_methods    = $order_data[ 'payment_methods' ];
			$table              = $order_data[ 'table' ];
			$tax                = new \WC_Tax();
			$tendered           = 0;
			$customer           = new \WC_Customer( $customer_id );

			$states_list = WC()->countries->get_states( $outlet_data[ 'country' ] );

			if ( is_array( $states_list ) ) {
				$state_code = array_search( $outlet_data[ 'state' ], $states_list );
			}

			$state_code = ! empty( $state_code ) ? $state_code : $outlet_data[ 'state' ];

			$billing_address = [
				'first_name' => $customer->get_billing_first_name() ? $customer->get_billing_first_name() : $customer->get_first_name(),
				'last_name'  => $customer->get_billing_last_name() ? $customer->get_billing_last_name() : $customer->get_last_name(),
				'company'    => $customer->get_billing_company(),
				'address_1'  => $outlet_data[ 'address1' ],
				'address_2'  => $outlet_data[ 'address2' ],
				'city'       => $outlet_data[ 'city' ],
				'state'      => $state_code,
				'postcode'   => $outlet_data[ 'postcode' ],
				'country'    => $outlet_data[ 'country' ],
				'email'      => $customer->get_billing_email(),
				'phone'      => $customer->get_billing_phone(),
			];

			$shipping_address = [
				'first_name' => $customer->get_shipping_first_name(),
				'last_name'  => $customer->get_shipping_last_name(),
				'company'    => $customer->get_shipping_company(),
				'address_1'  => $outlet_data[ 'address1' ],
				'address_2'  => $outlet_data[ 'address2' ],
				'city'       => $outlet_data[ 'city' ],
				'state'      => $state_code,
				'postcode'   => $outlet_data[ 'postcode' ],
				'country'    => $outlet_data[ 'country' ],
			];

			$order = wc_create_order( [ 'customer_id' => apply_filters( 'ddwcpos_modify_customer_id_in_creating_order', $customer_id, $request ) ] );

			if ( empty( $this->ddwcpos_configuration[ 'order_mails_enabled' ] ) ) {
				remove_action( 'woocommerce_order_status_pending_to_processing_notification', [ WC()->mailer()->emails[ 'WC_Email_New_Order' ], 'trigger' ] );
				remove_action( 'woocommerce_order_status_pending_to_processing_notification', [ WC()->mailer()->emails[ 'WC_Email_Customer_Processing_Order' ], 'trigger' ] );
				remove_action( 'woocommerce_order_status_pending_to_completed_notification', [ WC()->mailer()->emails[ 'WC_Email_New_Order' ], 'trigger' ] );
				remove_action( 'woocommerce_order_status_completed_notification', [ WC()->mailer()->emails[ 'WC_Email_Customer_Completed_Order' ], 'trigger' ] );
			}

			$order_id = $order->get_id();

			if ( count( $payment_methods ) > 1 ) {
				throw new \Exception( esc_html__( 'Multiple payments are not available in the free version.', 'devdiggers-multipos-for-woocommerce' ) );
			}

			$enabled_payment_methods = ! empty( $this->ddwcpos_configuration[ 'payment_method' ] ) && is_array( $this->ddwcpos_configuration[ 'payment_method' ] ) ? $this->ddwcpos_configuration[ 'payment_method' ] : [];
			$enabled_payment_slugs   = [];

			foreach ( $enabled_payment_methods as $configured_method ) {
				if ( ! empty( $configured_method[ 'slug' ] ) && ! empty( $configured_method[ 'status' ] ) && 'enabled' === $configured_method[ 'status' ] ) {
					$enabled_payment_slugs[] = $configured_method[ 'slug' ];
				}
			}

			if ( empty( $payment_methods[0][ 'slug' ] ) || ! in_array( $payment_methods[0][ 'slug' ], $enabled_payment_slugs, true ) ) {
				throw new \Exception( esc_html__( 'Selected payment method is not available.', 'devdiggers-multipos-for-woocommerce' ) );
			}

			$payment_method       = $payment_methods[0][ 'slug' ];
			$payment_method_title = $payment_methods[0][ 'name' ];
			$tendered             = $payment_methods[0][ 'amount' ];

			$order->update_meta_data( '_ddwcpos_tendered_amount', $tendered );
			$order->update_meta_data( '_ddwcpos_table', $table );

			$order->update_meta_data( '_ddwcpos_payment_methods', $payment_methods );

			$tax_display_cart = get_option( 'woocommerce_tax_display_cart' );
			$global_tax_rate  = 0;

			if ( wc_tax_enabled() ) {
				$rates = $tax->find_rates( [
					'country'  => $outlet_data[ 'country' ],
					'city'     => $outlet_data[ 'city' ],
					'state'    => $state_code,
					'postcode' => $outlet_data[ 'postcode' ],
				] );

				foreach ( $rates as $key => $rate ) {
					$global_tax_rate += $rate[ 'rate' ];
				}
			}

			foreach ( $order_data[ 'products' ] as $item ) {
				if ( $item[ 'custom' ] ) {
					throw new \Exception( esc_html__( 'Custom products are not available in the free version.', 'devdiggers-multipos-for-woocommerce' ) );
				}
			}

			foreach ( $order_data[ 'products' ] as $item ) {
				if ( ! $item[ 'custom' ] ) {
					$product = wc_get_product( $item[ 'product_id' ] );

					if ( $product ) {
						if ( 'simple' !== $product->get_type() ) {
							throw new \Exception( esc_html__( 'Only simple products are supported in the free version.', 'devdiggers-multipos-for-woocommerce' ) );
						}

						$product_id    = $item[ 'product_id' ];
						$product_title = $item[ 'name' ];
						$tax_status    = $product->get_tax_status();
						$tax_class     = $product->get_tax_class();
						$tax_rate      = 0;

						if ( wc_tax_enabled() ) {
							if ( ! empty( $tax_class ) ) {
								$rates    = $tax->get_rates_for_tax_class( $tax_class );
								$priority = 1;

								foreach ( $rates as $key => $rate ) {
									if ( $rate->tax_rate_priority == $priority ) {
										if ( empty( $rate->tax_rate_country ) || ( $outlet_data[ 'country' ] === $rate->tax_rate_country && ( empty( $rate->tax_rate_state ) || $state_code === $rate->tax_rate_state ) && ( empty( $rate->city_count ) || $outlet_data[ 'city' ] === $rate->city_count ) && ( empty( $rate->postcode_count ) || $outlet_data[ 'postcode' ] === $rate->postcode_count ) ) ) {
											++$priority;

											$tax_rate += floatval( $rate->tax_rate );
										}
									}
								}
							} else {
								$tax_rate = $global_tax_rate;
							}
						}

						$quantity = absint( $item['quantity'] );

						if ( 'incl' === $tax_display_cart && 'none' !== $tax_status ) {
							$line_price         = wc_get_price_including_tax( $product, [ 'qty' => $quantity ] );
							$tax_excluded_price = $line_price / ( ( $tax_rate / 100 ) + 1 );

							$item_id = $order->add_product( $product, $quantity, [
								'subtotal' => $tax_excluded_price,
								'total'    => $tax_excluded_price,
								'name'     => $product_title,
							] );
						} else {
							$line_price = wc_get_price_excluding_tax( $product, [ 'qty' => $quantity ] );

							$item_id = $order->add_product( $product, $quantity, [
								'subtotal' => $line_price,
								'total'    => $line_price,
								'name'     => $product_title,
							] );
						}

						do_action( 'ddwcpos_after_adding_product_in_order', $item_id, $item, $order, $request );

						if ( 'custom' === $inventory_type ) {
							$custom_stock = get_post_meta( $product_id, '_ddwcpos_outlet_stock_' . $outlet_id, true );

							update_post_meta( $product_id, '_ddwcpos_outlet_stock_' . $outlet_id, $custom_stock - $quantity );

							$product_stock = $product->get_stock_quantity();

							wc_update_product_stock( $product, $product_stock + $quantity );
						}
					}
				}
			}

			if ( apply_filters( 'ddwcpos_set_address_in_order', true, $order, $request ) ) {
				$order->set_address( $billing_address, 'billing' );
				$order->set_address( $shipping_address, 'shipping' );
			}

			$order->set_payment_method( $payment_method );
			$order->set_payment_method_title( $payment_method_title );

			$order_items = $order->get_items();

			$order_discount = 0;

			if ( ! empty( $coupons ) ) {
				$coupon_amount = 0;

				foreach ( $coupons as $coupon_key => $coupon_val ) {
					$coupon_code = ! empty( $coupon_val['code'] ) ? sanitize_text_field( $coupon_val['code'] ) : '';
					$coupon      = ! empty( $coupon_code ) ? new \WC_Coupon( $coupon_code ) : false;

					if ( ! $coupon || 0 === $coupon->get_id() ) {
						continue;
					}

					$coupon_val = [
						'code'  => $coupon->get_code(),
						'price' => wc_format_decimal( $coupon->get_amount(), 2 ),
						'type'  => $coupon->get_discount_type(),
					];

					if ( 'percent' === $coupon_val[ 'type' ] ) {
						foreach ( $order_items as $order_item ) {
							$total          = $order_item->get_total();
							$discount_total = $order_item->get_subtotal() * floatval( $coupon_val[ 'price' ] ) / 100;
							$order_item->set_total( $total - $discount_total );
							$order_item->save();
						}

						$coupon_amount = $order->get_subtotal() * $coupon_val[ 'price' ] / 100;
					} else {
						$items = [];

						if ( 'fixed_product' === $coupon_val[ 'type' ] ) {
							$this->ddwcpos_apply_coupon_fixed_product( $coupon_val, $order_items );
						} elseif ( 'fixed_cart' === $coupon_val[ 'type' ] ) {
							$total_discount = $this->ddwcpos_apply_coupon_fixed_cart( $coupon_val, $order_items );
						}

						$coupon_amount = $coupon_val[ 'price' ];
					}

					$coupon_amount = apply_filters( 'ddwcpos_modify_coupon_coupon_in_create_order', $coupon_amount, $coupon_val, $order, $request );

					$item = new \WC_Order_Item_Coupon();

					$item->set_props(
						[
							'code'         => $coupon_val[ 'code' ],
							'discount'     => floatval( $coupon_amount ),
							'discount_tax' => apply_filters( 'ddwcpos_modify_coupon_discount_tax_in_create_order', 0, $coupon_amount, $coupon_val, $order, $request ),
							'order_id'     => $order_id,
						]
					);

					$order->add_item( $item );
				}
			}

			// Adding Fees
			if ( ! empty( $fees ) ) {
				foreach ( $fees as $fee ) {
					if ( ! empty( $fee[ 'name' ] ) && ! empty( $fee[ 'amount' ] ) ) {
						$fee_item = new \WC_Order_Item_Fee();

						$fee_item->set_props(
							array(
								'name'      => sanitize_text_field( $fee[ 'name' ] ),
								'tax_class' => 0,
								'amount'    => floatval( $fee[ 'amount' ] ),
								'total'     => floatval( $fee[ 'amount' ] ),
							)
						);

						$order->add_item( $fee_item );
					}
				}
			}

			$order->calculate_totals();

			$order_total = $order->get_total();

			$order->update_meta_data( '_ddwcpos_outlet_id', $outlet_id );
			$order->update_meta_data( '_ddwcpos_cashier_id', $cashier_id );

			do_action( 'ddwcpos_before_payment_complete_in_order_at_pos', $order, $order_data, $request );

			if ( apply_filters( 'ddwcpos_process_order_payment_complete', true, $order, $order_data, $request ) ) {
				$order->payment_complete();
			}

			$order = apply_filters( 'ddwcpos_modify_creating_pos_order', $order, $order_data, $request );

			do_action( 'ddwcpos_after_creating_order', $order, $order_data, $request );

			if ( apply_filters( 'ddwcpos_process_order_update_status', true, $order, $order_data, $request ) ) {
				$order->update_status( str_replace( 'wc-', '', $this->ddwcpos_configuration[ 'order_status' ] ) );
			}

			$order->save();

			$api_get_orders = new DDWCPOS_API_Get_Orders( $this->ddwcpos_configuration );
			$order_response = $api_get_orders->ddwcpos_prepare_order_data( $order_id );

			$order_response[ 'transactions' ] = [];

			foreach ( $payment_methods as $value ) {
				$transaction_data = [
					'cashier_id' => $cashier_id,
					'outlet_id'  => $outlet_id,
					'order_id'   => $order_id,
					'in'         => $value[ 'amount' ],
					'out'        => 'cash' === $value[ 'slug' ] ? $order_response[ 'change' ] : 0,
					'method'     => $value[ 'slug' ],
					'reference'  => '',
					'date'       => current_time( 'Y-m-d H:i:s' ),
				];

				$transaction_data['id']           = strval( $this->transaction_helper->ddwcpos_save_transaction( $transaction_data ) );
				$order_response['transactions'][] = $transaction_data;
			}

			$order_response = apply_filters( 'ddwcpos_modify_api_order_response', $order_response, $order, $request );

			return apply_filters( 'ddwcpos_modify_api_create_order_response', $order_response, $order, $request );
		}

		/**
		 * Apply fixed cart discount to items.
		 *
		 * @param  array $coupon Coupon array.
		 * @param  array $items_to_apply Array of items to apply the coupon to.
		 * @param  int $amount Fixed discount amount to apply in cents. Leave blank to pull from coupon.
		 * @return int Total discounted.
		 */
		protected function ddwcpos_apply_coupon_fixed_cart( $coupon, $items_to_apply, $amount = null ) {
			$total_discount = 0;
			$amount         = $amount ? $amount : wc_add_number_precision( $coupon[ 'price' ] );
			$items_to_apply = array_filter( $items_to_apply, array( $this, 'ddwcpos_filter_products_with_price' ) );

			$item_count = 0;

			foreach ( $items_to_apply as $key => $item ) {
				$item_count += $item->get_quantity();
			}

			if ( ! $item_count ) {
				return $total_discount;
			}

			if ( ! $amount ) {
				// If there is no amount we still send it through so filters are fired.
				$total_discount = $this->ddwcpos_apply_coupon_fixed_product( $coupon, $items_to_apply, 0 );
			} else {
				$per_item_discount = absint( $amount / $item_count ); // round it down to the nearest cent.

				if ( $per_item_discount > 0 ) {
					$total_discount = $this->ddwcpos_apply_coupon_fixed_product( $coupon, $items_to_apply, $per_item_discount );

					/**
					 * If there is still discount remaining, repeat the process.
					 */
					if ( $total_discount > 0 && $total_discount < $amount ) {
						$total_discount += $this->ddwcpos_apply_coupon_fixed_cart( $coupon, $items_to_apply, $amount - $total_discount );
					}
				} elseif ( $amount > 0 ) {
					$total_discount += $this->ddwcpos_apply_coupon_remainder( $coupon, $items_to_apply, $amount );
				}
			}

			return $total_discount;
		}

		/**
		 * Apply fixed product discount to items.
		 *
		 * @param  array $coupon Coupon array.
		 * @param  array     $items_to_apply Array of items to apply the coupon to.
		 * @param  int       $amount Fixed discount amount to apply in cents. Leave blank to pull from coupon.
		 * @return int Total discounted.
		 */
		protected function ddwcpos_apply_coupon_fixed_product( $coupon, $items_to_apply, $amount = null ) {
			$total_discount  = 0;
			$amount          = $amount ? $amount : wc_add_number_precision( $coupon[ 'price' ] );

			foreach ( $items_to_apply as $item ) {
				// Find out how much price is available to discount for the item.
				$discounted_price = $this->ddwcpos_get_discounted_price_in_cents( $item );

				// Get the price we actually want to discount, based on settings.
				$price_to_discount = ( 'yes' === get_option( 'woocommerce_calc_discounts_sequentially', 'no' ) ) ? $discounted_price : $item->get_total();

				$apply_quantity = $item->get_quantity();
				$discount       = $amount * $apply_quantity;

				$discount       = min( $discounted_price, $discount );
				$total_discount = $total_discount + $discount;

				$item->set_total( wc_remove_number_precision_deep( $discounted_price - $discount ) );
			}
			return $total_discount;
		}

		/**
		 * Deal with remaining fractional discounts by splitting it over items
		 * until the amount is expired, discounting 1 cent at a time.
		 *
		 * @since 3.2.0
		 * @param  array $coupon Coupon array.
		 * @param  array     $items_to_apply Array of items to apply the coupon to.
		 * @param  int       $amount Fixed discount amount to apply.
		 * @return int Total discounted.
		 */
		protected function ddwcpos_apply_coupon_remainder( $coupon, $items_to_apply, $amount ) {
			$total_discount = 0;

			foreach ( $items_to_apply as $item ) {
				for ( $i = 0; $i < $item->get_quantity(); $i ++ ) {
					// Find out how much price is available to discount for the item.
					$price_to_discount = $this->ddwcpos_get_discounted_price_in_cents( $item );

					// Run coupon calculations.
					$discount = min( $price_to_discount, 1 );

					// Store totals.
					$total_discount += $discount;

					$item->set_total( wc_remove_number_precision_deep( $price_to_discount - $discount ) );

					if ( $total_discount >= $amount ) {
						break 2;
					}
				}
				if ( $total_discount >= $amount ) {
					break;
				}
			}
			return $total_discount;
		}

		/**
		 * Filter out all products which have been fully discounted to 0.
		 * Used as array_filter callback.
		 *
		 * @since  3.2.0
		 * @param  object $item Get data for this item.
		 * @return bool
		 */
		protected function ddwcpos_filter_products_with_price( $item ) {
			return $this->ddwcpos_get_discounted_price_in_cents( $item ) > 0;
		}

		/**
		 * Get discounted price of an item to precision (in cents).
		 *
		 * @param  object $item Get data for this item.
		 * @return int
		 */
		public function ddwcpos_get_discounted_price_in_cents( $item ) {
			return absint( NumberUtil::round( wc_add_number_precision( $item->get_total() ) ) );
		}
	}
}
