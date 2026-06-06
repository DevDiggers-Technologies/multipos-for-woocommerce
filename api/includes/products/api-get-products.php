<?php
/**
 * API Get Products class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\API\Includes\Products;

use DDWCMultiPOS\API\Includes\Common\DDWCPOS_API_Base_Controller;
use DDWCMultiPOS\Helper\Outlet\DDWCPOS_Outlet_Helper;
use DDWCMultiPOS\Helper\Barcode\DDWCPOS_Barcode_Helper;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_API_Get_Products' ) ) {
	/**
	 * API Get Products Class.
	 * 
	 * Handles product retrieval for POS system with proper validation,
	 * error handling, and standardized responses.
	 */
	class DDWCPOS_API_Get_Products extends DDWCPOS_API_Base_Controller {
		/**
		 * Base Name.
		 *
		 * @var string the route base
		 */
		public $base = 'get-products';

		/**
		 * Required parameters for validation
		 *
		 * @var array
		 */
		protected $required_params = [ 'cashier_id', 'outlet_id' ];

		/**
		 * Outlet Helper Variable
		 *
		 * @var object
		 */
		protected $outlet_helper;

		/**
		 * Barcode Helper Variable
		 *
		 * @var object
		 */
		protected $barcode_helper;

		/**
		 * Constructor.
		 *
		 * @param array $ddwcpos_configuration Configuration array.
		 */
		public function __construct( $ddwcpos_configuration ) {
			parent::__construct( $ddwcpos_configuration );
			$this->outlet_helper  = new DDWCPOS_Outlet_Helper();
			$this->barcode_helper = new DDWCPOS_Barcode_Helper( $ddwcpos_configuration );
		}

		/**
		 * Execute the specific API logic for getting products.
		 * 
		 * @param array $request Sanitized request data.
		 * @return array|WP_Error Product data or error.
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

			// Get outlet data
			$outlet_data = $this->outlet_helper->ddwcpos_get_outlet_details_by_id( $outlet_id );
			if ( ! $outlet_data ) {
				return $this->error_response(
					esc_html__( 'Outlet data not found', 'devdiggers-multipos-for-woocommerce' ),
					'outlet_data_not_found',
					404
				);
			}

			// Get tax rates for the outlet location
			$tax_rates = $this->get_tax_rates_for_outlet( $outlet_data );

			// Handle count request
			if ( -1 === $per_page ) {
				return $this->get_products_count( $request, $tax_rates );
			}

			// Handle products list request
			return $this->get_products_list( $request, $outlet_data, $tax_rates );
		}

		/**
		 * Get tax rates for outlet location.
		 *
		 * @param array $outlet_data Outlet data.
		 * @return array Tax rates.
		 */
		protected function get_tax_rates_for_outlet( $outlet_data ) {
			$tax = new \WC_Tax();
			$states_list = WC()->countries->get_states( $outlet_data['country'] );

			$state_code = $outlet_data['state'];
			if ( is_array( $states_list ) ) {
				$found_state_code = array_search( $outlet_data['state'], $states_list );
				if ( $found_state_code ) {
					$state_code = $found_state_code;
				}
			}

			return $tax->find_rates( [
				'country'  => $outlet_data['country'],
				'city'     => $outlet_data['city'],
				'state'    => $state_code,
				'postcode' => $outlet_data['postcode'],
			] );
		}

		/**
		 * Get total products count.
		 *
		 * @param array $request Request data.
		 * @param array $tax_rates Tax rates.
		 * @return array Response with count and tax rates.
		 */
		protected function get_products_count( $request, $tax_rates ) {
			$args = apply_filters( 'ddwcpos_modify_api_total_products_count_args', [
				'ignore_sticky_posts' => 1,
				'post_type'           => [ 'product' ],
				'tax_query'           => [
					[
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => [ 'simple' ],
					],
				],
				'order'               => 'DESC',
				'orderby'             => 'ID',
				'post_status'         => 'publish',
				'posts_per_page'      => -1,
				'fields'              => 'ids',
			], $request );

			$search_results = new \WP_Query( $args );
			wp_reset_postdata();

			$response = [
				'taxes'          => $tax_rates,
				'total_products' => apply_filters( 'ddwcpos_modify_api_total_products_count', $search_results->post_count, $request ),
			];

			return apply_filters( 'ddwcpos_modify_api_total_products_count_response', $response, $request );
		}

		/**
		 * Get products list with pagination.
		 *
		 * @param array $request Request data.
		 * @param array $outlet_data Outlet data.
		 * @param array $tax_rates Tax rates.
		 * @return array Products list.
		 */
		protected function get_products_list( $request, $outlet_data, $tax_rates ) {
			$cashier_id = intval( $request['cashier_id'] );
			$outlet_id  = intval( $request['outlet_id'] );
			$per_page   = intval( $request['per_page'] );
			$current_page = intval( $request['current_page'] );

			// Set up WooCommerce customer for tax calculations
			$this->setup_customer_for_tax_calculation( $cashier_id, $outlet_data );

			$offset = ( $current_page - 1 ) * $per_page;

			$args = [
				'post_type'           => [ 'product' ],
				'tax_query'           => [
					[
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => [ 'simple' ],
					],
				],
				'post_status'         => 'publish',
				'ignore_sticky_posts' => 1,
				'posts_per_page'      => $per_page,
				'offset'              => $offset,
				'order'               => 'DESC',
				'orderby'             => 'ID',
				'fields'              => 'ids',
			];

			$args = apply_filters( 'ddwcpos_modify_api_products_list_args', $args, $request );

			$search_results = new \WP_Query( $args );
			wp_reset_postdata();

			$products = [];
			if ( $search_results->have_posts() ) {
				$inventory_type = $this->outlet_helper->ddwcpos_get_inventory_type( $outlet_id );

				while ( $search_results->have_posts() ) {
					$search_results->the_post();
					$product_id = $search_results->post;

					// Skip excluded products
					if ( apply_filters( 'ddwcpos_exclude_product_in_pos', false, $product_id ) ) {
						continue;
					}

					$product_data = $this->ddwcpos_prepare_product_data( $product_id, $outlet_id, $inventory_type, $request );

					if ( $product_data ) {
						$products[] = $product_data;
					}
				}
			}

			return apply_filters( 'ddwcpos_modify_api_get_products_response', $products, $request );
		}

		/**
		 * Set up WooCommerce customer for tax calculations.
		 *
		 * @param int $cashier_id Cashier ID.
		 * @param array $outlet_data Outlet data.
		 * @return void
		 */
		protected function setup_customer_for_tax_calculation( $cashier_id, $outlet_data ) {
			WC()->customer = new \WC_Customer( $cashier_id );

			// Set shipping address
			WC()->customer->set_shipping_country( $outlet_data['country'] );
			WC()->customer->set_shipping_city( $outlet_data['city'] );
			WC()->customer->set_shipping_state( $outlet_data['state'] );
			WC()->customer->set_shipping_postcode( $outlet_data['postcode'] );

			// Set billing address
			WC()->customer->set_billing_country( $outlet_data['country'] );
			WC()->customer->set_billing_city( $outlet_data['city'] );
			WC()->customer->set_billing_state( $outlet_data['state'] );
			WC()->customer->set_billing_postcode( $outlet_data['postcode'] );
		}

		/**
		 * Check parent category function
		 *
		 * @param array $categories
		 * @param int $parent_id
		 * @return array
		 */
		public function ddwcpos_check_parent_category( $categories, $parent_id ) {
			$parent_category = get_term( $parent_id );
			$parent_id       = $parent_category->parent;

			if ( ! empty( $parent_id ) ) {
				$categories[] = $parent_id;
				$this->ddwcpos_check_parent_category( $categories, $parent_id );
			}

			return $categories;
		}

		/**
		 * Prepare product data function
		 *
		 * @param int $product_id
		 * @param int $outlet_id
		 * @param string $inventory_type
		 * @param array $request
		 * @return array
		 */
		public function ddwcpos_prepare_product_data( $product_id, $outlet_id, $inventory_type, $request ) {
			$product = wc_get_product( $product_id );

			if ( ! $product ) {
				return false;
			}

			$product_type = $product->get_type();

			if ( 'simple' !== $product_type ) {
				return false;
			}

			$is_include_tax = get_option( 'woocommerce_prices_include_tax' );
			$shop_display   = get_option( 'woocommerce_tax_display_shop' );
			$cart_display   = get_option( 'woocommerce_tax_display_cart' );

			ob_start();
			if ( 'custom' === $inventory_type ) {
				$stock = $product->get_meta( '_ddwcpos_outlet_stock_' . $outlet_id, true );
				?>
				<mark class="instock">
					<?php
					/* translators: %s: Product stock quantity. */
					echo esc_html( sprintf( __( 'In Stock(%s)', 'devdiggers-multipos-for-woocommerce' ), $stock ) );
					?>
				</mark>
				<?php
			} else {
				$stock_status       = $product->get_stock_status();
				$backorders_allowed = $product->backorders_allowed();
				$stock_quantity     = $product->get_stock_quantity();

				if ( $backorders_allowed ) {
					$stock = 999999999;
					if ( $stock_quantity > 0 ) {
						?>
						<mark class="instock">
							<?php
							/* translators: %s: Product stock quantity. */
							echo esc_html( sprintf( __( 'On Backorder(%s)', 'devdiggers-multipos-for-woocommerce' ), $stock_quantity ) );
							?>
						</mark>
						<?php
					} else {
						?>
						<mark class="instock"><?php esc_html_e( 'On Backorder', 'devdiggers-multipos-for-woocommerce' ); ?></mark>
						<?php
					}
				} else if ( 'instock' === $stock_status ) {
					$stock = $stock_quantity > 0 ? $stock_quantity : 999999999;
					if ( $stock_quantity > 0 ) {
						?>
						<mark class="instock">
							<?php
							/* translators: %s: Product stock quantity. */
							echo esc_html( sprintf( __( 'In Stock(%s)', 'devdiggers-multipos-for-woocommerce' ), $stock_quantity ) );
							?>
						</mark>
						<?php
					} else {
						?>
						<mark class="instock"><?php esc_html_e( 'In Stock', 'devdiggers-multipos-for-woocommerce' ); ?></mark>
						<?php
					}
				} else {
					$stock = 0;
					?>
					<mark class="outofstock"><?php esc_html_e( 'Out of Stock', 'devdiggers-multipos-for-woocommerce' ); ?></mark>
					<?php
				}
			}

			$stock_html = ob_get_clean();

			if ( $stock <= 0 ) {
				return false;
			}

			$barcode_init  = $this->barcode_helper->ddwcpos_get_barcode_init( $product );
			$barcode_final = $this->barcode_helper->ddwcpos_generate_barcode( $barcode_init );

			add_filter( 'wc_get_price_decimals', function( $decimals ) {
				return 10;
			} );

			if ( 'yes' === $is_include_tax ) {
				if ( 'excl' === $shop_display && 'excl' === $cart_display ) {
					$regular_price = floatval( $product->get_regular_price() );
					$sale_price    = floatval( wc_get_price_excluding_tax( $product ) );
					$product_tax   = floatval( wc_get_price_including_tax( $product ) ) - $sale_price;
				} elseif ( 'incl' === $shop_display && 'excl' === $cart_display ) {
					$regular_price = floatval( $product->get_regular_price() );
					$sale_price    = floatval( wc_get_price_excluding_tax( $product ) );
					$product_tax   = floatval( wc_get_price_including_tax( $product ) ) - $sale_price;
				} elseif ( 'excl' === $shop_display && 'incl' === $cart_display ) {
					$regular_price = $product->get_regular_price();
					$sale_price    = $product->get_price();
					$product_tax   = 0;
				} else {
					$regular_price = $product->get_regular_price();
					$sale_price    = $product->get_price();
					$product_tax   = 0;
				}
			} else {
				if ( 'excl' === $shop_display && 'excl' === $cart_display ) {
					$regular_price = floatval( $product->get_regular_price() );
					$sale_price    = floatval( wc_get_price_excluding_tax( $product ) );
					$product_tax   = floatval( wc_get_price_including_tax( $product ) ) - $sale_price;
				} elseif ( 'incl' === $shop_display && 'excl' === $cart_display ) {
					$regular_price = floatval( $product->get_regular_price() );
					$sale_price    = floatval( wc_get_price_excluding_tax( $product ) );
					$product_tax   = floatval( wc_get_price_including_tax( $product ) ) - $sale_price;
				} elseif ( 'excl' === $shop_display && 'incl' === $cart_display ) {
					$regular_price = $product->get_regular_price();
					$sale_price    = wc_get_price_including_tax( $product );
					$product_tax   = 0;
				} else {
					$regular_price = $product->get_regular_price();
					$sale_price    = wc_get_price_including_tax( $product );
					$product_tax   = 0;
				}
			}

			add_filter( 'wc_get_price_decimals', function( $decimals ) {
				return absint( get_option( 'woocommerce_price_num_decimals', 2 ) );
			} );

			$categories = [];

			$terms = get_the_terms( $product_id, 'product_cat' );

			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$categories[] = $term->term_id;

					if ( ! empty( $term->parent ) ) {
						$categories[] = $term->parent;
						$categories   = $this->ddwcpos_check_parent_category( $categories, $term->parent );
					}
				}
			}

			return apply_filters(
				'ddwcpos_modify_api_products_data',
				[
					'product_id'             => $product_id,
					'sku'                    => $product->get_sku(),
					'slug'                   => $product->get_slug(),
					'title'                  => $product->get_name( 'edit' ),
					'parent'                 => $product->get_parent_id(),
					'price_html'             => $product->get_price_html(),
					'original_regular_price' => $regular_price,
					'regular_price'          => $sale_price,
					'sale_price'             => $sale_price,
					'onsale'                 => $product->is_on_sale(),
					'stock'                  => $stock,
					'stock_html'             => $stock_html,
					'stock_status'           => $backorders_allowed ? 'onbackorder' : $product->get_stock_status(),
					'stock_quantity'         => 'custom' === $inventory_type ? ( $backorders_allowed ? $stock_quantity : $stock ) : $product->get_stock_quantity(),
					'image'                  => $product->get_image( 'thumbnail' ),
					'categories'             => $categories,
					'tax'                    => $product_tax,
					'originalTax'            => $product_tax,
					'available_variations'   => [],
					'attributes'             => '',
					'selected_attributes'    => '',
					'attribute_keys'         => [],
					'attribute_values'       => [],
					'type'                   => $product_type,
					'weight'                 => floatval( $product->get_weight() ),
					'length'                 => floatval( $product->get_length() ),
					'width'                  => floatval( $product->get_width() ),
					'height'                 => floatval( $product->get_height() ),
					'barcode_init'           => $barcode_init,
					'barcode_final'          => $barcode_final,
				],
				$request
			);
		}
	}
}
