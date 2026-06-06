<?php
/**
 * API Delete Customer class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Api\Includes\Customers;

use DDWCMultiPOS\API\Includes\Common\DDWCPOS_API_Base_Controller;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_API_Delete_Customer' ) ) {
	/**
	 * API Delete Customer class
	 * 
	 * Handles customer deletion for POS system with proper validation,
	 * error handling, and standardized responses.
	 */
	class DDWCPOS_API_Delete_Customer extends DDWCPOS_API_Base_Controller {
		/**
		 * Base Name
		 *
		 * @var string $base the route base
		 */
		public $base = 'delete-customer';

		/**
		 * Required parameters for validation
		 *
		 * @var array
		 */
		protected $required_params = [ 'cashier_id', 'customer_id' ];

		/**
		 * Execute the specific API logic for deleting customers.
		 * 
		 * @param array $request Sanitized request data.
		 * @return array|WP_Error Deletion result or error.
		 */
		protected function execute_api_logic( $request ) {
			$cashier_id = intval( $request['cashier_id'] );
			$customer_id = intval( $request['customer_id'] );

			// Validate user permissions
			$user_validation = $this->validate_user_permissions( $cashier_id );
			if ( is_wp_error( $user_validation ) ) {
				return $user_validation;
			}

			// Validate customer exists
			$customer_validation = $this->validate_customer_exists( $customer_id );
			if ( is_wp_error( $customer_validation ) ) {
				return $customer_validation;
			}

			// Check if customer has orders
			$orders_validation = $this->validate_customer_orders( $customer_id );
			if ( is_wp_error( $orders_validation ) ) {
				return $orders_validation;
			}

			// Delete customer
			return $this->delete_customer( $customer_id, $request );
		}

		/**
		 * Validate customer exists.
		 *
		 * @param int $customer_id Customer ID to validate.
		 * @return bool|WP_Error True if valid, WP_Error if invalid.
		 */
		protected function validate_customer_exists( $customer_id ) {
			$customer = get_user_by( 'ID', $customer_id );

			if ( ! $customer ) {
				return $this->error_response(
					esc_html__( 'Customer not found', 'devdiggers-multipos-for-woocommerce' ),
					'customer_not_found',
					404
				);
			}

			// Never allow deleting yourself or any privileged account through the POS endpoint.
			if ( get_current_user_id() === (int) $customer_id ) {
				return $this->error_response(
					esc_html__( 'You cannot delete your own account.', 'devdiggers-multipos-for-woocommerce' ),
					'cannot_delete_self',
					403
				);
			}

			$roles = (array) $customer->roles;

			if (
				in_array( 'administrator', $roles, true ) ||
				in_array( 'shop_manager', $roles, true ) ||
				user_can( $customer, 'manage_woocommerce' ) ||
				user_can( $customer, 'edit_users' ) ||
				! in_array( 'customer', $roles, true )
			) {
				return $this->error_response(
					esc_html__( 'This user cannot be deleted from the point of sale.', 'devdiggers-multipos-for-woocommerce' ),
					'not_a_customer',
					403
				);
			}

			return true;
		}

		/**
		 * Validate customer has no orders.
		 *
		 * @param int $customer_id Customer ID to validate.
		 * @return bool|WP_Error True if valid, WP_Error if invalid.
		 */
		protected function validate_customer_orders( $customer_id ) {
			$orders = wc_get_orders( [
				'customer' => $customer_id,
				'limit'    => 1,
				'return'   => 'ids',
			] );

			if ( ! empty( $orders ) ) {
				return $this->error_response(
					esc_html__( 'Cannot delete customer with existing orders', 'devdiggers-multipos-for-woocommerce' ),
					'customer_has_orders',
					400
				);
			}

			return true;
		}

		/**
		 * Delete customer.
		 *
		 * @param int $customer_id Customer ID to delete.
		 * @param array $request Original request.
		 * @return array|WP_Error Deletion result or error.
		 */
		protected function delete_customer( $customer_id, $request ) {
			try {
				require_once( ABSPATH . 'wp-admin/includes/user.php' );

				$result = wp_delete_user( $customer_id );

				if ( ! $result ) {
					return $this->error_response(
						esc_html__( 'Failed to delete customer', 'devdiggers-multipos-for-woocommerce' ),
						'deletion_failed',
						500
					);
				}

				return apply_filters( 'ddwcpos_modify_api_delete_customer_response', $customer_id, $request );
			} catch ( \Exception $e ) {
				return $this->error_response(
					$e->getMessage(),
					'deletion_error',
					500
				);
			}
		}
	}
}
