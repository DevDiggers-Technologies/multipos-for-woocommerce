<?php
/**
 * API Manage Customer class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Api\Includes\Customers;

use DDWCMultiPOS\API\Includes\Common\DDWCPOS_API_Base_Controller;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_API_Manage_Customer' ) ) {
	/**
	 * API Manage Customer class
	 * 
	 * Handles customer creation and updates for POS system with proper validation,
	 * error handling, and standardized responses.
	 */
	class DDWCPOS_API_Manage_Customer extends DDWCPOS_API_Base_Controller {
		/**
		 * Base Name
		 *
		 * @var string $base the route base
		 */
		public $base = 'manage-customer';

		/**
		 * Required parameters for validation
		 *
		 * @var array
		 */
		protected $required_params = [ 'cashier_id', 'customer_data' ];

		/**
		 * Execute the specific API logic for managing customers.
		 * 
		 * @param array $request Sanitized request data.
		 * @return array|WP_Error Customer data or error.
		 */
		protected function execute_api_logic( $request ) {
			$cashier_id = intval( $request['cashier_id'] );
			$customer_data = $request['customer_data'];

			// Validate user permissions
			$user_validation = $this->validate_user_permissions( $cashier_id );
			if ( is_wp_error( $user_validation ) ) {
				return $user_validation;
			}

			// Validate customer data
			$data_validation = $this->validate_customer_data( $customer_data );
			if ( is_wp_error( $data_validation ) ) {
				return $data_validation;
			}

			// Handle customer creation or update
			if ( ! empty( $customer_data['id'] ) ) {
				return $this->update_customer( $customer_data, $request );
			} else {
				return $this->create_customer( $customer_data, $request );
			}
		}

		/**
		 * Validate customer data.
		 *
		 * @param array $customer_data Customer data to validate.
		 * @return bool|WP_Error True if valid, WP_Error if invalid.
		 */
		protected function validate_customer_data( $customer_data ) {
			if ( apply_filters( 'ddwcpos_manage_customer_custom_check', false, $customer_data, $request ) ) {
				return $this->error_response(
					apply_filters(
						'ddwcpos_manage_customer_custom_check_error_message',
						esc_html__( 'Manage customer custom check error message.', 'devdiggers-multipos-for-woocommerce' ),
						$customer_data,
						$request
					),
					'manage_customer_custom_check_failed',
					400
				);
			}

			// Validate required fields
			if ( empty( $customer_data['email'] ) ) {
				return $this->error_response(
					esc_html__( 'Email is required', 'devdiggers-multipos-for-woocommerce' ),
					'missing_email',
					400
				);
			}

			// Validate email format
			if ( ! is_email( $customer_data['email'] ) ) {
				return $this->error_response(
					esc_html__( 'Invalid email format', 'devdiggers-multipos-for-woocommerce' ),
					'invalid_email',
					400
				);
			}

			// Check if email already exists (for new customers)
			if ( empty( $customer_data['id'] ) ) {
				$existing_user = get_user_by( 'email', $customer_data['email'] );
				if ( $existing_user ) {
					return $this->error_response(
						esc_html__( 'Email already exists', 'devdiggers-multipos-for-woocommerce' ),
						'email_exists',
						400
					);
				}
			}

			return true;
		}

		/**
		 * Update existing customer.
		 *
		 * @param array $customer_data Customer data.
		 * @param array $request Original request.
		 * @return array|WP_Error Updated customer data or error.
		 */
		protected function update_customer( $customer_data, $request ) {
			try {
				$customer_id = intval( $customer_data['id'] );

				// Check if customer exists
				$existing_customer = get_user_by( 'ID', $customer_id );
				if ( ! $existing_customer ) {
					return $this->error_response(
						esc_html__( 'Customer not found', 'devdiggers-multipos-for-woocommerce' ),
						'customer_not_found',
						404
					);
				}

				// Update user data
				$update_result = wp_update_user( [
					'ID'         => $customer_id,
					'user_email' => sanitize_email( $customer_data['email'] ),
					'nickname'   => sanitize_email( $customer_data['email'] ),
					'first_name' => sanitize_text_field( $customer_data['first_name'] ),
					'last_name'  => sanitize_text_field( $customer_data['last_name'] ),
				] );

				if ( is_wp_error( $update_result ) ) {
					return $this->error_response(
						$update_result->get_error_message(),
						'update_failed',
						500
					);
				}

				// Update WooCommerce customer data
				$customer = new \WC_Customer( $customer_id );

				$customer->set_first_name( sanitize_text_field( $customer_data['first_name'] ) );
				$customer->set_last_name( sanitize_text_field( $customer_data['last_name'] ) );

				// Update billing information
				$customer->set_billing_first_name( sanitize_text_field( $customer_data['first_name'] ) );
				$customer->set_billing_last_name( sanitize_text_field( $customer_data['last_name'] ) );
				$customer->set_billing_address_1( sanitize_text_field( $customer_data['address_1'] ?? '' ) );
				$customer->set_billing_address_2( sanitize_text_field( $customer_data['address_2'] ?? '' ) );
				$customer->set_billing_country( sanitize_text_field( $customer_data['country'] ?? '' ) );
				$customer->set_billing_state( sanitize_text_field( $customer_data['state'] ?? '' ) );
				$customer->set_billing_city( sanitize_text_field( $customer_data['city'] ?? '' ) );
				$customer->set_billing_postcode( sanitize_text_field( $customer_data['postcode'] ?? '' ) );
				$customer->set_billing_email( sanitize_email( $customer_data['email'] ) );
				$customer->set_billing_phone( sanitize_text_field( $customer_data['phone'] ?? '' ) );

				// Update shipping information
				$customer->set_shipping_first_name( sanitize_text_field( $customer_data['first_name'] ) );
				$customer->set_shipping_last_name( sanitize_text_field( $customer_data['last_name'] ) );
				$customer->set_shipping_address_1( sanitize_text_field( $customer_data['address_1'] ?? '' ) );
				$customer->set_shipping_address_2( sanitize_text_field( $customer_data['address_2'] ?? '' ) );
				$customer->set_shipping_country( sanitize_text_field( $customer_data['country'] ?? '' ) );
				$customer->set_shipping_state( sanitize_text_field( $customer_data['state'] ?? '' ) );
				$customer->set_shipping_city( sanitize_text_field( $customer_data['city'] ?? '' ) );
				$customer->set_shipping_postcode( sanitize_text_field( $customer_data['postcode'] ?? '' ) );

					$customer->save();

				do_action( 'ddwcpos_api_after_customer_save', $customer->get_id(), $customer_data, $request );

				$data = $this->get_customer_data( $customer->get_id() );

				return $this->success_response(
					$data,
					esc_html__( 'Customer updated successfully', 'devdiggers-multipos-for-woocommerce' )
				);

			} catch ( \Exception $e ) {
				return $this->error_response(
					$e->getMessage(),
					'update_error',
					500
				);
			}
		}

		/**
		 * Create new customer.
		 *
		 * @param array $customer_data Customer data.
		 * @param array $request Original request.
		 * @return array|WP_Error Created customer data or error.
		 */
		protected function create_customer( $customer_data, $request ) {
			try {
				// Create WooCommerce customer (this automatically assigns 'customer' role)
				$username = wc_create_new_customer_username( $customer_data['email'] );
				$password = wp_generate_password();
				$user_id = wc_create_new_customer( $customer_data['email'], $username, $password );

				if ( is_wp_error( $user_id ) ) {
					return $this->error_response(
						$user_id->get_error_message(),
						'user_creation_failed',
						500
					);
				}

				// Ensure customer role is assigned (backup safety)
				$user = new \WP_User( $user_id );

				// Update user data
				wp_update_user( [
					'ID'         => $user_id,
					'user_email' => sanitize_email( $customer_data['email'] ),
					'nickname'   => sanitize_email( $customer_data['email'] ),
					'first_name' => sanitize_text_field( $customer_data['first_name'] ),
					'last_name'  => sanitize_text_field( $customer_data['last_name'] ),
				] );

				// Create WooCommerce customer
				$customer = new \WC_Customer( $user_id );

				$customer->set_first_name( sanitize_text_field( $customer_data['first_name'] ) );
				$customer->set_last_name( sanitize_text_field( $customer_data['last_name'] ) );

				// Set billing information
				$customer->set_billing_first_name( sanitize_text_field( $customer_data['first_name'] ) );
				$customer->set_billing_last_name( sanitize_text_field( $customer_data['last_name'] ) );
				$customer->set_billing_address_1( sanitize_text_field( $customer_data['address_1'] ?? '' ) );
				$customer->set_billing_address_2( sanitize_text_field( $customer_data['address_2'] ?? '' ) );
				$customer->set_billing_country( sanitize_text_field( $customer_data['country'] ?? '' ) );
				$customer->set_billing_state( sanitize_text_field( $customer_data['state'] ?? '' ) );
				$customer->set_billing_city( sanitize_text_field( $customer_data['city'] ?? '' ) );
				$customer->set_billing_postcode( sanitize_text_field( $customer_data['postcode'] ?? '' ) );
				$customer->set_billing_email( sanitize_email( $customer_data['email'] ) );
				$customer->set_billing_phone( sanitize_text_field( $customer_data['phone'] ?? '' ) );

				// Set shipping information
				$customer->set_shipping_first_name( sanitize_text_field( $customer_data['first_name'] ) );
				$customer->set_shipping_last_name( sanitize_text_field( $customer_data['last_name'] ) );
				$customer->set_shipping_address_1( sanitize_text_field( $customer_data['address_1'] ?? '' ) );
				$customer->set_shipping_address_2( sanitize_text_field( $customer_data['address_2'] ?? '' ) );
				$customer->set_shipping_country( sanitize_text_field( $customer_data['country'] ?? '' ) );
				$customer->set_shipping_state( sanitize_text_field( $customer_data['state'] ?? '' ) );
				$customer->set_shipping_city( sanitize_text_field( $customer_data['city'] ?? '' ) );
				$customer->set_shipping_postcode( sanitize_text_field( $customer_data['postcode'] ?? '' ) );

				$customer->save();

				do_action( 'ddwcpos_api_after_customer_save', $customer->get_id(), $customer_data, $request );

				$data = $this->get_customer_data( $user_id );

				return $this->success_response(
					$data,
					esc_html__( 'Customer created successfully', 'devdiggers-multipos-for-woocommerce' )
				);

			} catch ( \Exception $e ) {
				return $this->error_response(
					$e->getMessage(),
					'creation_error',
					500
				);
			}
		}

		/**
		 * Get customer data using the get-customers API.
		 * 
		 * Reuses the customer data preparation logic from api-get-customers.php
		 * to ensure consistency and avoid code duplication.
		 *
		 * @param int $customer_id Customer ID.
		 * @return array|null Customer data or null if invalid.
		 */
		protected function get_customer_data( $customer_id ) {
			$get_customers_api = new DDWCPOS_API_Get_Customers( $this->ddwcpos_configuration );
			return $get_customers_api->prepare_customer_data( $customer_id );
		}
	}
}
