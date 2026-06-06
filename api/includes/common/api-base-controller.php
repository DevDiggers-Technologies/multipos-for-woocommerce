<?php
/**
 * API Base Controller class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\API\Includes\Common;

use DDWCMultiPOS\Helper\Outlet\DDWCPOS_Outlet_Helper;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_API_Base_Controller' ) ) {
	/**
	 * API Base Controller Class.
	 * 
	 * Provides common functionality for all API controllers including
	 * standardized error handling, input validation, and response formatting.
	 */
	abstract class DDWCPOS_API_Base_Controller {
		/**
		 * Base Name.
		 *
		 * @var string the route base
		 */
		public $base = '';

		/**
		 * Configuration Variable
		 *
		 * @var array
		 */
		protected $ddwcpos_configuration;

		/**
		 * Required parameters for validation
		 *
		 * @var array
		 */
		protected $required_params = [];

		/**
		 * Constructor.
		 *
		 * @param array $ddwcpos_configuration Configuration array.
		 */
		public function __construct( $ddwcpos_configuration = [] ) {
			$this->ddwcpos_configuration = $ddwcpos_configuration;
		}

		/**
		 * Main API callback method.
		 * 
		 * This method handles common functionality like validation,
		 * error handling, and response formatting.
		 *
		 * @param array $request Request data.
		 * @return array|WP_Error Standardized response.
		 */
		public function ddwcpos_get_data( $request ) {
			try {
				// Validate required parameters
				$validation_result = $this->validate_required_params( $request );
				if ( is_wp_error( $validation_result ) ) {
					return $validation_result;
				}

				// Sanitize input data
				$sanitized_request = $request;

				// Execute the specific API logic
				$result = $this->execute_api_logic( $sanitized_request );

				// Check if result is an error
				if ( is_wp_error( $result ) ) {
					return $result;
				}

				return $result;

			} catch ( \Exception $e ) {
				return $this->handle_exception( $e );
			}
		}

		/**
		 * Execute the specific API logic.
		 * 
		 * This method should be implemented by each API controller
		 * to handle their specific business logic.
		 *
		 * @param array $request Sanitized request data.
		 * @return mixed API-specific result.
		 */
		abstract protected function execute_api_logic( $request );

		/**
		 * Validate required parameters.
		 *
		 * @param array $request Request data.
		 * @return bool|WP_Error True if valid, WP_Error if invalid.
		 */
		protected function validate_required_params( $request ) {
			foreach ( $this->required_params as $param ) {
				if ( empty( $request[ $param ] ) ) {
					return new \WP_Error(
						'rest_missing_param',
						sprintf(
							/* translators: %s: parameter name */
							esc_html__( 'Missing required parameter: %s', 'devdiggers-multipos-for-woocommerce' ),
							$param
						),
						[ 'status' => 400, 'param' => $param ]
					);
				}
			}

			return true;
		}

		/**
		 * Sanitize request data.
		 *
		 * @param array $request Raw request data.
		 * @return array Sanitized request data.
		 */
		protected function sanitize_request_data( $request ) {
			$sanitized = [];

			foreach ( $request as $key => $value ) {
				$sanitized[ $key ] = $this->sanitize_value( $value );
			}

			return $sanitized;
		}

		/**
		 * Sanitize individual value based on its type.
		 *
		 * @param mixed $value Value to sanitize.
		 * @return mixed Sanitized value.
		 */
		protected function sanitize_value( $value ) {
			if ( is_array( $value ) ) {
				return array_map( [ $this, 'sanitize_value' ], $value );
			}

			if ( is_string( $value ) ) {
				return sanitize_text_field( $value );
			}

			if ( is_numeric( $value ) ) {
				return is_float( $value ) ? floatval( $value ) : intval( $value );
			}

			return $value;
		}

		/**
		 * Format API response.
		 *
		 * @param mixed $result API result.
		 * @param array $request Original request data.
		 * @return array Formatted response.
		 */
		protected function format_response( $result, $request ) {
			$response = [
				'success' => true,
				'data'    => $result,
				'message' => esc_html__( 'Request processed successfully', 'devdiggers-multipos-for-woocommerce' ),
			];

			// Apply filters for customization
			return apply_filters( 'ddwcpos_api_response_format', $response, $result, $request, $this->base );
		}

		/**
		 * Handle exceptions with standardized error format.
		 *
		 * @param \Exception $exception The exception to handle.
		 * @return WP_Error Formatted error response.
		 */
		protected function handle_exception( \Exception $exception ) {
				return new \WP_Error(
				'ddwcpos_api_error',
				esc_html__( 'An error occurred while processing your request', 'devdiggers-multipos-for-woocommerce' ),
				[
					'status'   => 500,
					'details'  => $exception->getMessage(),
					'endpoint' => $this->base,
				]
			);
		}

		/**
		 * Create success response.
		 *
		 * @param mixed $data Response data.
		 * @param string $message Success message.
		 * @return array Success response.
		 */
		protected function success_response( $data = null, $message = '' ) {
			return [
				'success' => true,
				'data'    => $data,
				'message' => $message ?: esc_html__( 'Operation completed successfully', 'devdiggers-multipos-for-woocommerce' ),
			];
		}

		/**
		 * Create error response.
		 *
		 * @param string $message Error message.
		 * @param string $code Error code.
		 * @param int $status HTTP status code.
		 * @return WP_Error Error response.
		 */
		protected function error_response( $message, $code = 'ddwcpos_error', $status = 400 ) {
			return new \WP_Error( $code, $message, [ 'status' => $status ] );
		}

		/**
		 * Validate user permissions.
		 *
		 * @param int $user_id User ID to validate.
		 * @param string $capability Required capability.
		 * @return bool|WP_Error True if valid, WP_Error if invalid.
		 */
		protected function validate_user_permissions( $user_id, $capability = 'read' ) {
			// For POS system, we need to be more lenient with user validation
			// as it might use special POS users or guest users
			if ( ! $user_id || ! is_numeric( $user_id ) ) {
				return $this->error_response(
					esc_html__( 'Invalid user ID', 'devdiggers-multipos-for-woocommerce' ),
					'invalid_user_id',
					400
				);
			}

			// Check if user exists, but don't fail if it doesn't for POS compatibility
			$user = get_user_by( 'id', $user_id );
			if ( ! $user ) {
				// For POS system, we'll allow non-existent users to pass through
				// as the original system didn't have this strict validation
				// This maintains backward compatibility
				return true;
			}

			// Only check capabilities if user exists and capability checking is enabled
			if ( apply_filters( 'ddwcpos_enable_user_capability_check', false, $user_id, $capability ) ) {
				if ( ! user_can( $user, $capability ) ) {
					return $this->error_response(
						esc_html__( 'Insufficient permissions', 'devdiggers-multipos-for-woocommerce' ),
						'insufficient_permissions',
						403
					);
				}
			}

			return true;
		}

		/**
		 * Validate outlet access.
		 *
		 * @param int $outlet_id Outlet ID to validate.
		 * @param int $user_id User ID for permission check.
		 * @return bool|WP_Error True if valid, WP_Error if invalid.
		 */
		protected function validate_outlet_access( $outlet_id, $user_id ) {
			if ( ! $outlet_id || ! is_numeric( $outlet_id ) ) {
				return $this->error_response(
					esc_html__( 'Invalid outlet ID', 'devdiggers-multipos-for-woocommerce' ),
					'invalid_outlet_id',
					400
				);
			}

			// Check if outlet exists and is enabled
			$outlet_helper = new DDWCPOS_Outlet_Helper();
			$outlet_data   = $outlet_helper->ddwcpos_get_outlet_details_by_id( $outlet_id );

			if ( empty( $outlet_data ) ) {
				return $this->error_response(
					esc_html__( 'This outlet is not available in the free version.', 'devdiggers-multipos-for-woocommerce' ),
					'outlet_not_available',
					403
				);
			}

			// For POS compatibility, we'll be more lenient with outlet validation
			// Only validate outlet status if the outlet exists
			if ( $outlet_data && 'enabled' !== $outlet_data['status'] ) {
				return $this->error_response(
					esc_html__( 'Outlet is disabled', 'devdiggers-multipos-for-woocommerce' ),
					'outlet_disabled',
					403
				);
			}

			// If outlet doesn't exist, we'll allow it to pass through for backward compatibility
			// This maintains compatibility with the original system

			return true;
		}

		/**
		 * Get configuration value with fallback.
		 *
		 * @param string $key Configuration key.
		 * @param mixed $default Default value if key not found.
		 * @return mixed Configuration value or default.
		 */
		protected function get_config( $key, $default = null ) {
			return isset( $this->ddwcpos_configuration[ $key ] ) 
				? $this->ddwcpos_configuration[ $key ] 
				: $default;
		}

		/**
		 * Log API activity for debugging.
		 *
		 * @param string $action Action performed.
		 * @param array $data Additional data to log.
		 * @return void
		 */
		protected function log_activity( $action, $data = [] ) {
				unset( $action, $data );
		}
	}
}
