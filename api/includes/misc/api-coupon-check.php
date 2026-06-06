<?php
/**
 * API Coupon Check class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\API\Includes\Misc;

use DDWCMultiPOS\API\Includes\Common\DDWCPOS_API_Base_Controller;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_API_Coupon_Check' ) ) {
	/**
	 * API Coupon Check class
	 *
	 * Handles coupon validation for POS system with proper validation,
	 * error handling, and standardized responses.
	 */
	class DDWCPOS_API_Coupon_Check extends DDWCPOS_API_Base_Controller {
		/**
		 * Base Name
		 *
		 * @var string $base the route base
		 */
		public $base = 'check-coupon';

		/**
		 * Required parameters for validation
		 *
		 * @var array
		 */
		protected $required_params = [ 'coupon_code', 'customer' ];

		/**
		 * Constructor.
		 *
		 * @param array $ddwcpos_configuration Configuration array.
		 */
		public function __construct( $ddwcpos_configuration = [] ) {
			parent::__construct( $ddwcpos_configuration );
		}

		/**
		 * Execute the specific API logic for checking coupons.
		 * 
		 * @param array $request Sanitized request data.
		 * @return array|WP_Error Coupon validation result or error.
		 */
		protected function execute_api_logic( $request ) {
			$coupon_code = sanitize_text_field( $request['coupon_code'] );
			$customer_data = $request['customer'];

			// Validate customer data
			if ( empty( $customer_data['id'] ) || ! is_numeric( $customer_data['id'] ) ) {
				return $this->error_response(
					esc_html__( 'Invalid customer data', 'devdiggers-multipos-for-woocommerce' ),
					'invalid_customer_data',
					400
				);
			}

			// Validate coupon code
			if ( empty( $coupon_code ) ) {
				return $this->error_response(
					esc_html__( 'Coupon code is required', 'devdiggers-multipos-for-woocommerce' ),
					'missing_coupon_code',
					400
				);
			}

			// Check if coupon exists
			$coupon = new \WC_Coupon( $coupon_code );
			if ( 0 === $coupon->get_id() ) {
				return $this->error_response(
					esc_html__( 'Invalid coupon code', 'devdiggers-multipos-for-woocommerce' ),
					'invalid_coupon_code',
					404
				);
			}

			// Validate coupon expiration
			$expiration_validation = $this->validate_coupon_expiration( $coupon );
			if ( is_wp_error( $expiration_validation ) ) {
				return $expiration_validation;
			}

			// Prepare coupon data
			$coupon_data = $this->prepare_coupon_data( $coupon );

			return apply_filters( 'ddwcpos_modify_api_coupon_response', [
				'success' => true,
				'coupon'  => $coupon_data,
				'message' => esc_html__( 'Coupon Applied Successfully', 'devdiggers-multipos-for-woocommerce' ),
			], $coupon_code );
		}

		/**
		 * Validate coupon expiration.
		 *
		 * @param \WC_Coupon $coupon Coupon object.
		 * @return bool|WP_Error True if valid, WP_Error if invalid.
		 */
		protected function validate_coupon_expiration( $coupon ) {
			$date_expires = $coupon->get_date_expires();

			if ( ! empty( $date_expires ) ) {
				$current_date = current_time( 'Y-m-d' );
				$expiry_date = $date_expires->date( 'Y-m-d' );

				if ( strtotime( $current_date ) > strtotime( $expiry_date ) ) {
					return $this->error_response(
						esc_html__( 'Sorry, Coupon Expired. Try another one!', 'devdiggers-multipos-for-woocommerce' ),
						'coupon_expired',
					400
					);
				}
			}

			return true;
		}

		/**
		 * Prepare coupon data for API response.
		 *
		 * @param \WC_Coupon $coupon Coupon object.
		 * @return array Coupon data.
		 */
		protected function prepare_coupon_data( $coupon ) {
			return [
				'price'        => wc_format_decimal( $coupon->get_amount(), 2 ),
				'code'         => $coupon->get_code(),
				'type'         => $coupon->get_discount_type(),
				'date_expires' => $coupon->get_date_expires(),
				'restrictions' => [],
			];
		}
	}
}
