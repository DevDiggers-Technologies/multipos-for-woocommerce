<?php
/**
 * Product Stock List Template
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Templates\Admin\Stock;

use DDWCMultiPOS\Helper\Outlet\DDWCPOS_Outlet_Helper;
use DDWCMultiPOS\Helper\Barcode\DDWCPOS_Barcode_Helper;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Product_Stocks_List_Template' ) ) {
	/**
	 * Product Stock list class
	 */
	class DDWCPOS_Product_Stocks_List_Template extends \WP_List_table {
		/**
         * Configuration Variable
         *
         * @var array
         */
        protected $ddwcpos_configuration;

		/**
		 * Outlet helper variable
		 *
		 * @var object
		 */
		protected $outlet_helper;

		/**
		 * Barcode Helper variable
		 *
		 * @var object
		 */
		protected $barcode_helper;

		/**
		 * Outlet id variable
		 *
		 * @var int
		 */
		protected $outlet_id;

		/**
		 * Class constructor
		 *
		 * @param array $ddwcpos_configuration
		 */
		public function __construct( $ddwcpos_configuration ) {
			$this->ddwcpos_configuration = $ddwcpos_configuration;
			$this->outlet_helper         = new DDWCPOS_Outlet_Helper();
			$this->barcode_helper        = new DDWCPOS_Barcode_Helper( $this->ddwcpos_configuration );
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Outlet filter is read-only list-table input.
				$this->outlet_id             = ! empty( $_GET[ 'outlet-id' ] ) ? intval( $_GET[ 'outlet-id' ] ) : '';

			parent::__construct( [
				'singular' => esc_html__( 'Product List', 'devdiggers-multipos-for-woocommerce' ),
				'plural'   => esc_html__( 'Products List', 'devdiggers-multipos-for-woocommerce' ),
				'ajax'     => false,
			] );
		}

		/**
		 * Prepare Items
		 *
		 * @return void
		 */
		public function prepare_items() {
			$this->_column_headers = $this->get_column_info();

				$http_host   = ! empty( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
				$request_uri = ! empty( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
				$current_url = set_url_scheme( 'http://' . $http_host . $request_uri );

			if ( strpos( $current_url, '_wp_http_referer' ) !== false ) {
				$new_url = remove_query_arg( [ '_wp_http_referer', '_wpnonce' ], stripslashes( $current_url ) );
				wp_safe_redirect( $new_url );
				exit();
			}

				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Search is read-only list-table input.
				$search       = ! empty( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
			$per_page     = $this->get_items_per_page( 'stocks_per_page', 20 );
			$current_page = $this->get_pagenum();
			$total_items  = $this->ddwcpos_get_products_count( $search );

			$this->set_pagination_args( [
				'total_items' => $total_items,
				'per_page'    => $per_page,
			] );

			$data = $this->ddwcpos_get_products( $per_page, $current_page, $search );

			usort( $data, [ $this, 'usort_reorder' ] );

			$this->items = $data;
		}

		/**
		 * Get Products Count function
		 *
		 * @param string $search
		 * @return int
		 */
		public function ddwcpos_get_products_count( $search ) {
			$search_results = new \WP_Query( [
				's'                   => $search,
				'ignore_sticky_posts' => 1,
				'post_type'           => [ 'product', 'product_variation' ],
				'post_status'         => 'publish',
				'order'               => 'DESC',
				'orderby'             => 'ID',
				'posts_per_page'      => -1,
				'fields'              => 'ids',
				'search_columns'      => [ 'post_title' ],
			] );

			wp_reset_postdata();

			return $search_results->post_count;
		}

		/**
		 * Usort function
		 *
		 * @param int $first First value.
		 * @param int $second Second value.
		 * @return $result
		 */
		public function usort_reorder( $first, $second ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Sorting is read-only list-table input.
				$orderby = ! empty( $_GET[ 'orderby' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'orderby' ] ) ) : 'id';
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Sorting is read-only list-table input.
				$order   = ! empty( $_GET[ 'order' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'order' ] ) ) : 'desc';

			$result = strnatcmp( $first[ $orderby ], $second[ $orderby ] );
			return $order === 'asc' ? $result : -$result;
		}

		/**
		 * Fetch Products
		 *
		 * @param int $per_page Per Page.
		 * @param int $current_page Page.
		 * @param string $search
		 * @return array $data
		 */
		public function ddwcpos_get_products( $per_page, $current_page, $search ) {
            $data = [];
            $off  = ( $current_page - 1 ) * $per_page;

			$args = [
				's'              => $search,
				'post_type'      => [ 'product', 'product_variation' ],
				'post_status'    => 'publish',
				'posts_per_page' => $per_page,
				'offset'         => $off,
				'search_columns' => [ 'post_title' ],
				'order'          => 'DESC',
				'orderby'        => 'ID',
				'fields'         => 'ids',
			];

			$args = apply_filters( 'ddwcpos_modify_outlet_products_list_args', $args );

			$search_results = new \WP_Query( $args );

			wp_reset_postdata();

			if ( $search_results->have_posts() ) {
				while ( $search_results->have_posts() ) {
					$search_results->the_post();

					$product_id   = $search_results->post;
					$product      = wc_get_product( $product_id );

					if ( ! $product ) {
						continue;
					}

					$product_type = $product->get_type();

					if ( 'simple' === $product_type || 'variation' === $product_type ) {
						if ( $product_type == 'simple' ) {
							$product_type    = esc_html__( 'Simple', 'devdiggers-multipos-for-woocommerce' );
							$main_product_id = $product_id;
						} else {
							$product_type    = esc_html__( 'Variation', 'devdiggers-multipos-for-woocommerce' );
							$main_product_id = $product->get_parent_id();
						}

						$barcode_init = $this->barcode_helper->ddwcpos_get_barcode_init( $product );
						$stock_status = $product->get_stock_status();

						ob_start();
						if ( 'instock' === $stock_status ) {
							$stock_quantity = $product->get_stock_quantity();
							if ( $stock_quantity > 0 ) {
								?>
								<mark class="instock">
									<?php
									/* translators: %s for product stock quantity */
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
							?>
							<mark class="outofstock"><?php esc_html_e( 'Out of Stock', 'devdiggers-multipos-for-woocommerce' ); ?></mark>
							<?php
						}
						$stock = ob_get_clean();

						if ( ! empty( $this->outlet_id ) ) {
							$custom_stock = $product->get_meta( '_ddwcpos_outlet_stock_' . $this->outlet_id, true );

							ob_start();
							?>
							<input type="number" min="0" class="ddwcpos-custom-stock ddwcpos-width-60" placeholder="<?php esc_attr_e( 'Quantity', 'devdiggers-multipos-for-woocommerce' ); ?>" value="<?php echo esc_attr( ! empty( $custom_stock ) ? $custom_stock : 0 ); ?>" />
							<button class="button ddwcpos-product-action" data-action="update_custom_stock" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-outlet-id="<?php echo esc_attr( $this->outlet_id ); ?>"><?php esc_html_e( 'Update', 'devdiggers-multipos-for-woocommerce' ); ?></button>
							<?php
							$custom_stock = ob_get_clean();
						} else {
							$custom_stock = esc_html__( 'Select Outlet First!', 'devdiggers-multipos-for-woocommerce' );
						}

						$data[] = apply_filters(
							'ddwcpos_modify_outlet_products_list_row_data',
							[
								'id'                => $product_id,
								'main_product_id'   => $main_product_id,
								'thumb'             => $product->get_image( 'thumbnail' ),
								'pro_name'          => $product->get_name(),
								'pro_type'          => $product_type,
								'barcode'           => $barcode_init,
								'price'             => $product->get_price_html(),
								'centralized_stock' => $stock,
								'custom_stock'      => $custom_stock,
							]
						);
					}
				}
			}

			return apply_filters( 'ddwcpos_outlet_products_list_data', $data );
		}

		/**
		 * No items
		 *
		 * @return void
		 */
		public function no_items() {
			esc_html_e( 'No products avaliable.', 'devdiggers-multipos-for-woocommerce' );
		}

		/**
		 * Hidden Columns
		 *
		 * @return array
		 */
		public function get_hidden_columns() {
			return [];
		}

		/**
		 *  Associative array of columns
		 *
		 * @return array
		 */
		public function get_columns() {
			$columns = [
				'product'          => esc_html__( 'Product', 'devdiggers-multipos-for-woocommerce' ),
				'stock_management' => esc_html__( 'Stock Management', 'devdiggers-multipos-for-woocommerce' ),
			];

			return apply_filters( 'ddwcpos_outlet_products_list_columns', $columns );
		}

		/**
		 * Render a column when no column specific method exists.
		 *
		 * @param array  $item Items.
		 * @param string $column_name Name.
		 * @return mixed
		 */
		public function column_default( $item, $column_name ) {
			if ( array_key_exists( $column_name, $item ) ) {
				return $item[ $column_name ];
			}

				return '';
		}

		/**
		 * Columns to make sortable.
		 *
		 * @return array
		 */
		public function get_sortable_columns() {
			return apply_filters( 'ddwcpos_outlet_products_list_sortable_columns', [
				'product' => [ 'pro_name', true ],
			] );
		}

		public function column_product( $item ) {
			$actions = [
				'edit' => sprintf( '<a href="%s">%s</a>', esc_url( admin_url( "post.php?post={$item[ 'main_product_id' ]}&action=edit" ) ), esc_html__( 'Edit', 'devdiggers-multipos-for-woocommerce' ) ),
				'view' => sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( get_permalink( $item[ 'id' ] ) ), esc_html__( 'View', 'devdiggers-multipos-for-woocommerce' ) ),
			];

			return sprintf(
				'<div class="ddwcpos-product-list-item">
					<div class="ddwcpos-product-thumb">%1$s</div>
					<div class="ddwcpos-product-details">
						<strong>%2$s</strong><br />
						<small>%3$s - %4$s</small><br />
						<code class="ddwcpos-barcode-label">%5$s</code>
						%6$s
					</div>
				</div>',
				$item[ 'thumb' ],
				$item[ 'pro_name' ],
				$item[ 'pro_type' ],
				$item[ 'price' ],
				$item[ 'barcode' ],
				$this->row_actions( apply_filters( 'ddwcpos_outlet_products_list_line_actions', $actions ) )
			);
		}

		/**
		 * Stock Management Column
		 *
		 * @param array $item Item.
		 * @return string
		 */
		public function column_stock_management( $item ) {
			return sprintf(
				'<div class="ddwcpos-barcode-management-item ddwcpos-stock-management-item">
					<div class="ddwcpos-action-tier">
						<span class="ddwcpos-tier-label">%1$s</span>
						<div class="ddwcpos-field-group">
							%2$s
						</div>
					</div>
					<div class="ddwcpos-action-tier">
						<span class="ddwcpos-tier-label">%3$s</span>
						<div class="ddwcpos-field-group">
							%4$s
						</div>
					</div>
				</div>',
				esc_html__( 'WooCommerce Stock', 'devdiggers-multipos-for-woocommerce' ),
				$item[ 'centralized_stock' ],
				esc_html__( 'Manual POS Stock', 'devdiggers-multipos-for-woocommerce' ),
				$item[ 'custom_stock' ]
			);
		}

		/**
		 * Product Stocks List Filters
		 *
		 * @param string $which Position of filter.
		 */
		public function extra_tablenav( $which ) {
			if ( 'top' === $which ) {
				$outlets = $this->outlet_helper->ddwcpos_get_all_outlets( 999999, 0, '' );
				?>
				<div class="alignleft actions bulkactions">
					<select name="outlet-id" data-placeholder="<?php esc_attr_e( 'Select Outlet', 'devdiggers-multipos-for-woocommerce' ); ?>">
						<option value=""><?php esc_html_e( 'Select Outlet', 'devdiggers-multipos-for-woocommerce' ); ?></option>
						<?php
						if ( ! empty( $outlets ) ) {
							foreach ( $outlets as $key => $outlet ) {
								?>
								<option value="<?php echo esc_attr( $outlet[ 'id' ] ); ?>" <?php echo esc_attr( $outlet[ 'id' ] == $this->outlet_id ? 'selected="selected"' : '' ); ?>><?php echo esc_html( $outlet[ 'name' ] ); ?></option>
								<?php
							}
						}
						?>
					</select>

					<input type="submit" value="<?php esc_attr_e( 'Filter', 'devdiggers-multipos-for-woocommerce' ); ?>" name="ddwcpos_filter_submit" class="button" />

					<?php
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Filter submit flag controls reset button visibility only.
						if ( ! empty( $_GET['ddwcpos_filter_submit'] ) ) {
							// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Page/menu are read-only routing values.
							$page = ! empty( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
							// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Page/menu are read-only routing values.
							$menu = ! empty( $_GET['menu'] ) ? sanitize_text_field( wp_unslash( $_GET['menu'] ) ) : '';
							?>
							<a href="<?php echo esc_url( admin_url( "admin.php?page={$page}&menu={$menu}" ) ); ?>" name="ddwcpos_filter_reset" class="button"><?php esc_html_e( 'Reset', 'devdiggers-multipos-for-woocommerce' ); ?></a>
						<?php
					}
					?>
				</div>
				<?php
			}
		}
	}
}
