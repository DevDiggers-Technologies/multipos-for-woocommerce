<?php
/**
 * API Get Countries & states class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Api\Includes\Misc;

use DDWCMultiPOS\API\Includes\Common\DDWCPOS_API_Base_Controller;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_API_Get_Countries_States' ) ) {
	/**
	 * API Get Countries & states class
	 * 
	 * Handles countries and states retrieval for POS system with proper validation,
	 * error handling, and standardized responses.
	 */
	class DDWCPOS_API_Get_Countries_States extends DDWCPOS_API_Base_Controller {
		/**
		 * Base Name
		 *
		 * @var string $base the route base
		 */
		public $base = 'get-countries-states';

		/**
		 * Required parameters for validation
		 *
		 * @var array
		 */
		protected $required_params = [];

		/**
		 * Execute the specific API logic for getting countries and states.
		 * 
		 * @param array $request Sanitized request data.
		 * @return array|WP_Error Countries and states data or error.
		 */
		protected function execute_api_logic( $request ) {
			try {
				$countries_obj = new \WC_Countries();

				$response = [
					'countries'    => $countries_obj->__get( 'countries' ),
					'base_country' => $countries_obj->get_base_country(),
					'states'       => $countries_obj->get_states(),
				];

				return apply_filters( 'ddwcpos_modify_api_countries_response', $response, $request );
			} catch ( \Exception $e ) {
				return $this->error_response(
					$e->getMessage(),
					'countries_data_error',
					500
				);
			}
		}
	}
}
