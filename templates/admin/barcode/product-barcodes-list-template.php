<?php
/**
 * Product Barcode List Template
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Templates\Admin\Barcode;

use DDWCMultiPOS\Helper\Barcode\DDWCPOS_Barcode_Helper;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Product_Barcodes_List_Template' ) ) {
	/**
	 * Product Barcode list class
	 */
	class DDWCPOS_Product_Barcodes_List_Template extends \WP_List_table {
		/**
		 * Barcode Helper variable
		 *
		 * @var object
		 */
		protected $barcode_helper;

		/**
         * Configuration Variable
         *
         * @var array
         */
        protected $ddwcpos_configuration;

		/**
		 * Class constructor
		 *
		 * @param array $ddwcpos_configuration
		 */
		public function __construct( $ddwcpos_configuration ) {
			$this->ddwcpos_configuration = $ddwcpos_configuration;
			$this->barcode_helper        = new DDWCPOS_Barcode_Helper( $this->ddwcpos_configuration );
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
			$per_page     = $this->get_items_per_page( 'barcodes_per_page', 20 );
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
            $data   = [];
            $off    = ( $current_page - 1 ) * $per_page;

			$args = [
				's'                   => $search,
				'ignore_sticky_posts' => 1,
				'post_type'           => [ 'product' ],
				'tax_query'           => [
					[
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => [ 'simple' ],
					],
				],
				'post_status'         => 'publish',
				'order'               => 'DESC',
				'orderby'             => 'ID',
				'posts_per_page'      => $per_page,
				'offset'              => $off,
				'search_columns'      => [ 'post_title' ],
				'fields'              => 'ids',
			];

			$args = apply_filters( 'ddwcpos_modify_products_list_args', $args );

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

					if ( 'simple' === $product_type ) {
						$product_type    = esc_html__( 'Simple', 'devdiggers-multipos-for-woocommerce' );
						$main_product_id = $product_id;

						$barcode_init  = $this->barcode_helper->ddwcpos_get_barcode_init( $product );
						$barcode_final = $this->barcode_helper->ddwcpos_generate_barcode( $barcode_init );

						ob_start();
						?>
						<span class="ddwcpos-barcode ddwcpos-width-60"><?php echo esc_html( $barcode_init ); ?></span>
						<?php
						$barcode_init_html = ob_get_clean();

						$barcode_image_args = [
							'action' => 'ddwcpos_barcode_image',
							'nonce'  => wp_create_nonce( 'ddwcpos_barcode_image' ),
							'code'   => $barcode_final,
							'size'   => intval( $this->ddwcpos_configuration[ 'barcode_height' ] ),
						];

						$barcode_image_url       = add_query_arg( $barcode_image_args, admin_url( 'admin-ajax.php' ) );
						$barcode_image_print_url = $barcode_image_url;

						ob_start();
						?>
						<img class="ddwcpos-barcode-image" src="<?php echo esc_url( $barcode_image_url ); ?>" alt="<?php esc_attr_e( 'Barcode', 'devdiggers-multipos-for-woocommerce' ); ?>" />
						<?php
						$barcode_image = ob_get_clean();

						ob_start();
						?>
						<div style="width:max-content; margin: <?php echo esc_attr( $this->ddwcpos_configuration[ 'barcode_margin' ] ); ?>; display:inline-block; text-align: center; <?php echo esc_attr( 'vertical' === $this->ddwcpos_configuration[ 'barcode_orientation' ] ? 'transform: rotate(90deg);' : '' ); ?>"><img src="<?php echo esc_url( $barcode_image_print_url ); ?>" alt="<?php esc_attr_e( 'Barcode', 'devdiggers-multipos-for-woocommerce' ); ?>" /></div>
						<?php
						$barcode_print_content = apply_filters( 'ddwcpos_modify_barcode_print_content', ob_get_clean(), $product );

						ob_start();
						?>
						<div class="ddwcpos-barcode-print-content">
							<?php echo wp_kses_post( $barcode_print_content ); ?>
						</div>

						<input type="number" min="1" class="ddwcpos-barcode-quantity ddwcpos-width-60" placeholder="<?php esc_attr_e( 'Quantity', 'devdiggers-multipos-for-woocommerce' ); ?>" />
						<button class="button ddwcpos-product-action" data-action="print_barcode" data-product-id="<?php echo esc_attr( $product_id ); ?>"><?php esc_html_e( 'Print', 'devdiggers-multipos-for-woocommerce' ); ?></button>
						<?php
						$barcode_print = ob_get_clean();

						$data[] = apply_filters(
							'ddwcpos_modify_products_list_row_data',
							[
								'id'              => $product_id,
								'main_product_id' => $main_product_id,
								'thumb'           => $product->get_image( 'thumbnail' ),
								'pro_name'        => $product->get_name(),
								'pro_type'        => $product_type,
								'price'           => $product->get_price_html(),
								'barcode'         => $barcode_init_html,
								'barcode_image'   => $barcode_image,
								'barcode_print'   => $barcode_print,
							]
						);
					}
				}
			}

			return apply_filters( 'ddwcpos_products_list_data', $data );
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
				'product'            => esc_html__( 'Product', 'devdiggers-multipos-for-woocommerce' ),
				'barcode_management' => esc_html__( 'Barcode Management', 'devdiggers-multipos-for-woocommerce' ),
			];

			return apply_filters( 'ddwcpos_products_list_columns', $columns );
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
			return apply_filters( 'ddwcpos_products_list_sortable_columns', [
				'product' => [ 'pro_name', true ],
			] );
		}

		/**
		 * Product column
		 *
		 * @param array $item Item.
		 * @return string
		 */
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
						<small>%3$s - %4$s</small>
						%5$s
					</div>
				</div>',
				$item[ 'thumb' ],
				$item[ 'pro_name' ],
				$item[ 'pro_type' ],
				$item[ 'price' ],
				$this->row_actions( apply_filters( 'ddwcpos_products_list_line_actions', $actions ) )
			);
		}

		/**
		 * Barcode management column
		 *
		 * @param array $item Item.
		 * @return string
		 */
		public function column_barcode_management( $item ) {
			return sprintf(
				'<div class="ddwcpos-barcode-management-item">
					<div class="ddwcpos-action-tier">
						<span class="ddwcpos-tier-label">%1$s</span>
						<div class="ddwcpos-field-group">
							%2$s
							<div class="ddwcpos-barcode-preview">
								%3$s
							</div>
						</div>
					</div>
					<div class="ddwcpos-action-tier">
						<span class="ddwcpos-tier-label">%4$s</span>
						<div class="ddwcpos-field-group">
							%5$s
						</div>
					</div>
				</div>',
				esc_html__( 'Barcode Number', 'devdiggers-multipos-for-woocommerce' ),
				$item[ 'barcode' ],
				$item[ 'barcode_image' ],
				esc_html__( 'Print Label', 'devdiggers-multipos-for-woocommerce' ),
				$item[ 'barcode_print' ]
			);
		}
	}
}
