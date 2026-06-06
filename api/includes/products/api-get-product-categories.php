<?php
/**
 * API Get Categories class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\API\Includes\Products;

use DDWCMultiPOS\API\Includes\Common\DDWCPOS_API_Base_Controller;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_API_GET_Product_Categories' ) ) {
	/**
	 * API Get Categories class
	 * 
	 * Handles product categories retrieval for POS system with proper validation,
	 * error handling, and standardized responses.
	 */
	class DDWCPOS_API_GET_Product_Categories extends DDWCPOS_API_Base_Controller {
		/**
		 * Base Name.
		 *
		 * @var string the route base
		 */
		public $base = 'get-product-categories';

		/**
		 * Required parameters for validation
		 *
		 * @var array
		 */
		protected $required_params = [ 'cashier_id', 'outlet_id' ];

		/**
		 * Execute the specific API logic for getting product categories.
		 * 
		 * @param array $request Sanitized request data.
		 * @return array|WP_Error Categories data or error.
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

			// Check for custom approach filter
			if ( apply_filters( 'ddwcpos_api_product_categories_different_approach', false, $request ) ) {
				return apply_filters( 'ddwcpos_api_product_categories_different_approach_response', [], $request );
			}

			// Get categories
			return $this->get_product_categories( $request );
		}

		/**
		 * Get product categories.
		 *
		 * @param array $request Request data.
		 * @return array|WP_Error Categories data or error.
		 */
		protected function get_product_categories( $request ) {
			try {
				$categories = [];

				$args = [
					'taxonomy'     => 'product_cat',
					'orderby'      => 'name',
					'show_count'   => 1,
					'pad_counts'   => 1,
					'hierarchical' => 1,
					'title_li'     => '',
					'hide_empty'   => 0,
				];

				$args = apply_filters( 'ddwcpos_modify_api_product_categories_args', $args, $request );
				$all_categories = get_categories( $args );

				foreach ( $all_categories as $cat ) {
					$cat_thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
					$cat_image_url    = wp_get_attachment_url( $cat_thumbnail_id );

					if ( apply_filters( 'ddwcpos_modify_api_add_category_in_response', true, $cat->term_id, $request ) ) {
						$categories[] = [
							'id'     => $cat->term_id,
							'name'   => $cat->name,
							'image'  => $cat_image_url,
							'parent' => $cat->category_parent,
							'count'  => $cat->count,
							'slug'   => $cat->slug,
						];
					}
				}

				return apply_filters( 'ddwcpos_modify_api_get_product_categories_response', $categories, $request );

			} catch ( \Exception $e ) {
				return $this->error_response(
					$e->getMessage(),
				'categories_retrieval_error',
					500
				);
			}
		}
	}
}
