<?php
/**
 * API Get Customers class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\API\Includes\Customers;

use DDWCMultiPOS\API\Includes\Common\DDWCPOS_API_Base_Controller;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_API_Get_Customers' ) ) {
	/**
	 * API Get Customers class
	 * 
	 * Handles customer retrieval for POS system with proper validation,
	 * error handling, and standardized responses.
	 */
	class DDWCPOS_API_Get_Customers extends DDWCPOS_API_Base_Controller {
		/**
		 * Base Name.
		 *
		 * @var string the route base
		 */
		public $base = 'get-customers';

		/**
		 * Required parameters for validation
		 *
		 * @var array
		 */
		protected $required_params = [ 'cashier_id', 'outlet_id' ];

		/**
		 * Constructor.
		 * 
		 * @param array $ddwcpos_configuration Configuration array.
		 */
		public function __construct( $ddwcpos_configuration ) {
			parent::__construct( $ddwcpos_configuration );
		}

		/**
		 * Execute the specific API logic for getting customers.
		 * 
		 * @param array $request Sanitized request data.
		 * @return array|WP_Error Customer data or error.
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
				return $this->get_customers_count( $request );
			}

			// Handle customers list request
			return $this->get_customers_list( $request );
		}

		/**
		 * Get total customers count.
		 *
		 * @param array $request Request data.
		 * @return int Total customers count.
		 */
		protected function get_customers_count( $request ) {
			$args = [
				'role'   => 'customer',
				'fields' => 'ids',
			];

			$args = apply_filters( 'ddwcpos_modify_api_total_customers_count_args', $args, $request );

			$user_query = new \WP_User_Query( $args );
			wp_reset_postdata();

			return apply_filters( 'ddwcpos_modify_api_total_customers_count', $user_query->get_total(), $request );
		}

		/**
		 * Get customers list with pagination.
		 *
		 * @param array $request Request data.
		 * @return array Customers list.
		 */
		protected function get_customers_list( $request ) {
			$per_page = intval( $request['per_page'] );
			$current_page = intval( $request['current_page'] );
			$offset = ( $current_page - 1 ) * $per_page;

			$args = [
				'role'    => 'customer',
				'number'  => $per_page,
				'offset'  => $offset,
				'order'   => 'DESC',
				'orderby' => 'ID',
				'fields'  => 'ids',
			];

			$args = apply_filters( 'ddwcpos_modify_api_customers_args', $args, $request );

			$query = new \WP_User_Query( $args );
			wp_reset_postdata();

			$customer_ids = $query->get_results();
			$customers = [];

			if ( ! empty( $customer_ids ) ) {
				foreach ( $customer_ids as $customer_id ) {
					$customer_data = $this->prepare_customer_data( $customer_id );
					if ( $customer_data ) {
						$customers[] = $customer_data;
					}
				}
			}

			return apply_filters( 'ddwcpos_modify_api_get_customers_response', $customers, $request );
		}

		/**
		 * Prepare customer data for API response.
		 *
		 * @param int $customer_id Customer ID.
		 * @return array|null Customer data or null if invalid.
		 */
		public function prepare_customer_data( $customer_id ) {
			$customer = new \WC_Customer( $customer_id );
			
			if ( ! $customer->get_id() ) {
				return null;
			}

			$phone               = $customer->get_billing_phone();
			$default_customer_id = $this->get_config( 'default_customer' );

			return apply_filters( 'ddwcpos_api_customer_data', [
				'id'           => intval( $customer_id ),
				'email'        => $customer->get_email(),
				'first_name'   => $customer->get_first_name(),
				'last_name'    => $customer->get_last_name(),
				'display_name' => $customer->get_display_name(),
				'username'     => $customer->get_username(),
				'avatar_url'   => $customer->get_avatar_url(),
				'coupons'      => [],
				'phone'        => $phone,
				'default'      => $customer_id == $default_customer_id ? 1 : 0,
				'billing'      => [
					'first_name' => $customer->get_billing_first_name(),
					'last_name'  => $customer->get_billing_last_name(),
					'company'    => $customer->get_billing_company(),
					'address_1'  => $customer->get_billing_address_1(),
					'address_2'  => $customer->get_billing_address_2(),
					'city'       => $customer->get_billing_city(),
					'state'      => $customer->get_billing_state(),
					'postcode'   => $customer->get_billing_postcode(),
					'country'    => $customer->get_billing_country(),
					'email'      => $customer->get_billing_email(),
					'phone'      => $phone,
				],
				'shipping'  => [
					'first_name' => $customer->get_shipping_first_name(),
					'last_name'  => $customer->get_shipping_last_name(),
					'company'    => $customer->get_shipping_company(),
					'address_1'  => $customer->get_shipping_address_1(),
					'address_2'  => $customer->get_shipping_address_2(),
					'city'       => $customer->get_shipping_city(),
					'state'      => $customer->get_shipping_state(),
					'postcode'   => $customer->get_shipping_postcode(),
					'country'    => $customer->get_shipping_country(),
				],
			] );
		}
	}
}
