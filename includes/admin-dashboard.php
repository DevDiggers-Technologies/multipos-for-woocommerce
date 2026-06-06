<?php
/**
 * This file handles all admin dashboard functionalities.
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Includes;

use DevDiggers\Framework\Includes\DDFW_Plugin_Dashboard;
use DevDiggers\Framework\Includes\DDFW_Assets;
use DevDiggers\Framework\Includes\DDFW_SVG;
use DDWCMultiPOS\Templates\Admin;

defined( 'ABSPATH' ) || exit();

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Admin routing/filter GET values here are read-only; destructive list-table actions verify their own nonce.

if ( ! class_exists( 'DDWCPOS_Admin_Dashboard' ) ) {
	/**
	 * Admin Dashboard Class
	 */
	class DDWCPOS_Admin_Dashboard {
		/**
		 * Configuration Variable
		 *
		 * @var array
		 */
		protected $ddwcpos_configuration;

		/**
		 * Dashboard Variable
		 *
		 * @var DDFW_Plugin_Dashboard
		 */
		protected $dashboard;

		/**
		 * Construct
		 * 
		 * @param array $ddwcpos_configuration
		 */
		public function __construct( $ddwcpos_configuration ) {
			$this->ddwcpos_configuration = $ddwcpos_configuration;
			$this->ddwcpos_add_dashboard_menu();
			add_action( 'admin_enqueue_scripts', [ $this, 'ddwcpos_enqueue_admin_scripts' ] );
			add_filter( 'admin_footer_text', [ $this, 'ddwcpos_set_admin_footer_text' ], 99 );
		}

		/**
		 * Add Admin menu function
		 *
		 * @return void
		 */
		public function ddwcpos_add_dashboard_menu() {
			ob_start();
			?>
			<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
				<circle cx="16" cy="16" r="15" fill="var(--ddfw-tab-background-color)"/>
				<path d="M9 21C9 19.8954 9.89543 19 11 19H21C22.1046 19 23 19.8954 23 21V25C23 25.5523 22.5523 26 22 26H10C9.44772 26 9 25.5523 9 25V21Z" fill="var(--ddfw-primary-color)"/>
				<path d="M10 11C10 9.89543 10.8954 9 12 9H20C21.1046 9 22 9.89543 22 11V19H10V11Z" fill="var(--ddfw-primary-color)" fill-opacity="0.7"/>
				<rect x="12" y="11" width="2" height="1.5" rx="0.5" fill="white"/>
				<rect x="15" y="11" width="2" height="1.5" rx="0.5" fill="white"/>
				<rect x="18" y="11" width="2" height="1.5" rx="0.5" fill="white"/>
				<rect x="12" y="14" width="2" height="1.5" rx="0.5" fill="white"/>
				<rect x="15" y="14" width="2" height="1.5" rx="0.5" fill="white"/>
				<rect x="18" y="14" width="2" height="1.5" rx="0.5" fill="white"/>
				<circle cx="16" cy="22.5" r="1.2" fill="white"/>
				<path d="M18 9V4L19 5L20 4L21 5L22 4V9H18Z" fill="var(--ddfw-primary-color)" fill-opacity="0.5"/>
			</svg>
			<?php esc_html_e( 'MultiPOS', 'devdiggers-multipos-for-woocommerce' ); ?>
			<?php
			$plugin_name = ob_get_clean();

			$args = [
				'page_title'              => esc_html__( 'MultiPOS - Point of Sale', 'devdiggers-multipos-for-woocommerce' ),
				'menu_title'              => esc_html__( 'MultiPOS', 'devdiggers-multipos-for-woocommerce' ),
				'slug'                    => 'ddwcpos-dashboard',
				'plugin_name'             => $plugin_name,
				'upgrade_url'             => '//devdiggers.com/product/multipos-point-of-sale-for-woocommerce/?utm_source=plugin_header&utm_medium=upgrade_button&utm_campaign=header_upgrade',
				'screen_options_callback' => [ $this, 'add_screen_options' ],
				'menus'                   => [
					'dashboard'         => [
						'label'    => esc_html__( 'Dashboard', 'devdiggers-multipos-for-woocommerce' ),
						'callback' => [ $this, 'ddwcpos_get_dashboard_template' ],
						'layout'   => 'full-width',
					],
					'outlets'           => [
						'label'    => esc_html__( 'Outlets', 'devdiggers-multipos-for-woocommerce' ),
						'callback' => [ $this, 'ddwcpos_get_outlets_template' ],
						'layout'   => 'full-width',
					],
					'cashiers'          => [
						'label'    => esc_html__( 'Cashiers', 'devdiggers-multipos-for-woocommerce' ),
						'callback' => [ $this, 'ddwcpos_get_cashiers_template' ],
						'layout'   => 'full-width',
					],
					'product-barcodes'  => [
						'label'    => esc_html__( 'Assign Barcodes', 'devdiggers-multipos-for-woocommerce' ),
						'callback' => [ $this, 'ddwcpos_get_product_barcodes_template' ],
						'layout'   => 'full-width',
					],
					'product-stocks'    => [
						'label'    => esc_html__( 'Assign Stocks', 'devdiggers-multipos-for-woocommerce' ),
						'callback' => [ $this, 'ddwcpos_get_product_stocks_upgrade_template' ],
						'layout'   => 'full-width',
					],
					'orders'            => [
						'label'    => esc_html__( 'Orders', 'devdiggers-multipos-for-woocommerce' ),
						'callback' => [ $this, 'ddwcpos_get_orders_template' ],
						'layout'   => 'full-width',
					],
					'reports'           => [
						'label'    => esc_html__( 'Reports', 'devdiggers-multipos-for-woocommerce' ),
						'callback' => [ $this, 'ddwcpos_get_reports_upgrade_template' ],
						'layout'   => 'full-width',
					],
					'transactions'      => [
						'label'    => esc_html__( 'Transactions', 'devdiggers-multipos-for-woocommerce' ),
						'callback' => [ $this, 'ddwcpos_get_transactions_template' ],
						'layout'   => 'full-width',
					],
					'invoices'          => [
						'label'    => esc_html__( 'Invoices', 'devdiggers-multipos-for-woocommerce' ),
						'callback' => [ $this, 'ddwcpos_get_invoices_template' ],
						'layout'   => 'full-width',
					],
					'configuration'     => [
						'label'    => esc_html__( 'Configuration', 'devdiggers-multipos-for-woocommerce' ),
						'layout'   => 'sidebar',
						'tabs'     => [
							'general'  => [
								'label'    => esc_html__( 'General', 'devdiggers-multipos-for-woocommerce' ),
								'icon'     => DDFW_SVG::get_svg_icon(
									'general',
									true,
									[ 'size' => 18 ]
								),
								'callback' => [ $this, 'ddwcpos_get_general_configuration_template' ],
							],
							'payments' => [
								'label'    => esc_html__( 'Payments', 'devdiggers-multipos-for-woocommerce' ),
								'icon'     => DDFW_SVG::get_svg_icon(
									'payments',
									true,
									[ 'size' => 18 ]
								),
								'callback' => [ $this, 'ddwcpos_get_payments_configuration_template' ],
							],
							'login'    => [
								'label'    => esc_html__( 'Login', 'devdiggers-multipos-for-woocommerce' ),
								'icon'     => DDFW_SVG::get_svg_icon(
									'login',
									true,
									[ 'size' => 18 ]
								),
								'callback' => [ $this, 'ddwcpos_get_login_configuration_template' ],
							],
							'printer'  => [
								'label'    => esc_html__( 'Printer', 'devdiggers-multipos-for-woocommerce' ),
								'icon'     => DDFW_SVG::get_svg_icon(
									'printer',
									true,
									[ 'size' => 18 ]
								),
								'callback' => [ $this, 'ddwcpos_get_printer_configuration_template' ],
							],
							'pwa'      => [
								'label'    => esc_html__( 'PWA', 'devdiggers-multipos-for-woocommerce' ),
								'icon'     => DDFW_SVG::get_svg_icon(
									'pwa',
									true,
									[ 'size' => 18 ]
								),
								'callback' => [ $this, 'ddwcpos_get_pwa_configuration_template' ],
							],
							'tables'   => [
								'label'    => esc_html__( 'Tables', 'devdiggers-multipos-for-woocommerce' ),
								'icon'     => DDFW_SVG::get_svg_icon(
									'tables',
									true,
									[ 'size' => 18 ]
								),
								'callback' => [ $this, 'ddwcpos_get_tables_configuration_template' ],
							],
							'layout'   => [
								'label'    => esc_html__( 'Layout', 'devdiggers-multipos-for-woocommerce' ),
								'icon'     => DDFW_SVG::get_svg_icon(
									'layout',
									true,
									[ 'size' => 18 ]
								),
								'callback' => [ $this, 'ddwcpos_get_layout_configuration_template' ],
							],
						],
					],
				],
			];

			$this->dashboard = new DDFW_Plugin_Dashboard( $args );
		}

		/**
		 * Add screen options for the admin dashboard
		 *
		 * @return void
		 */
		public function add_screen_options() {
			global $myListTable;

			$current_menu = ! empty( $_GET['menu'] ) ? sanitize_title( wp_unslash( $_GET['menu'] ) ) : 'dashboard';

			$args = [
				'label'    => esc_html__( 'Results Per Page', 'devdiggers-multipos-for-woocommerce' ),
				'default'  => 20,
				'hidden'   => 'id',
				'sanitize' => 'intval',
			];

			// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- WP_List_Table instance name is local to this method.
			switch ( $current_menu ) {
				case 'outlets':
					$args['option'] = 'outlets_per_page';
					$myListTable    = new Admin\Outlet\DDWCPOS_Outlets_List_Template( $this->ddwcpos_configuration );
					break;
				case 'cashiers':
					$args['option'] = 'cashiers_per_page';
					$myListTable    = new Admin\Cashier\DDWCPOS_Cashiers_List_Template();
					break;
				case 'product-barcodes':
					$args['option'] = 'barcodes_per_page';
					$myListTable    = new Admin\Barcode\DDWCPOS_Product_Barcodes_List_Template( $this->ddwcpos_configuration );
					break;
				case 'transactions':
					$args['option'] = 'transactions_per_page';
					$myListTable    = new Admin\Transaction\DDWCPOS_Transactions_List_Template( $this->ddwcpos_configuration );
					break;
				case 'orders':
					$args['option'] = 'orders_per_page';
					$myListTable    = new Admin\Order\DDWCPOS_Orders_List_Template();
					break;
			}

			if ( ! empty( $args['option'] ) ) {
				add_screen_option( 'per_page', $args );
			}
		}

		/**
		 * Dashboard Template
		 *
		 * @return void
		 */
		public function ddwcpos_get_dashboard_template() {
			new Admin\Dashboard\DDWCPOS_Dashboard_Template( $this->ddwcpos_configuration );
		}

		/**
		 * Reports Upgrade Template
		 *
		 * @return void
		 */
		public function ddwcpos_get_reports_upgrade_template() {
			ddfw_upgrade_to_pro_section(
				[
					'image_url'     => DDWCPOS_PLUGIN_URL . 'assets/images/pro-pages/reports.webp',
					'heading'       => esc_html__( 'Advanced POS Reports in One Place', 'devdiggers-multipos-for-woocommerce' ),
					'description'   => esc_html__( 'Upgrade to Pro to unlock easy-to-read sales, order, product, coupon, and tax reports for your store.', 'devdiggers-multipos-for-woocommerce' ),
					'list_features' => [
						esc_html__( 'Sales and revenue summaries with date filters', 'devdiggers-multipos-for-woocommerce' ),
						esc_html__( 'Product performance and stock insights', 'devdiggers-multipos-for-woocommerce' ),
						esc_html__( 'Coupon usage and discount tracking', 'devdiggers-multipos-for-woocommerce' ),
						esc_html__( 'Outlet-level order and sales reports', 'devdiggers-multipos-for-woocommerce' ),
						esc_html__( 'Tax totals and easy summary views', 'devdiggers-multipos-for-woocommerce' ),
						esc_html__( 'POS analytics with outlet breakdowns', 'devdiggers-multipos-for-woocommerce' ),
					],
					'upgrade_url' => '//devdiggers.com/product/multipos-point-of-sale-for-woocommerce/?utm_source=plugin_dashboard&utm_medium=upgrade_notice&utm_campaign=reports_pro_feature',
				]
			);
		}

		/**
		 * Outlets Template
		 *
		 * @return void
		 */
		public function ddwcpos_get_outlets_template() {
			$outlet_helper = new \DDWCMultiPOS\Helper\Outlet\DDWCPOS_Outlet_Helper();
			$outlet_count  = $outlet_helper->ddwcpos_get_saved_outlets_count();
			$action        = ! empty( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

			if ( 'add' === $action && $outlet_count >= 1 ) {
				ddfw_upgrade_to_pro_section(
					[
						'image_url'     => DDWCPOS_PLUGIN_URL . 'assets/images/pro-pages/add-outlet.webp',
						'heading'       => esc_html__( 'Manage Unlimited Outlets', 'devdiggers-multipos-for-woocommerce' ),
						'description'   => esc_html__( 'The free version allows only one outlet. Upgrade to Pro to add more locations, registers, and outlet-specific settings.', 'devdiggers-multipos-for-woocommerce' ),
						'list_features' => [
							esc_html__( 'Create unlimited POS outlets', 'devdiggers-multipos-for-woocommerce' ),
							esc_html__( 'Assign multiple cashiers for multiple outlets', 'devdiggers-multipos-for-woocommerce' ),
							esc_html__( 'Set stock and settings per location', 'devdiggers-multipos-for-woocommerce' ),
							esc_html__( 'Enable offline mode with Custom (Manual POS Stock) management', 'devdiggers-multipos-for-woocommerce' ),
							esc_html__( 'Design different invoices for different outlets.', 'devdiggers-multipos-for-woocommerce' ),
							esc_html__( 'Add more custom payment options for outlets.', 'devdiggers-multipos-for-woocommerce' ),
						],
						'upgrade_url'   => '//devdiggers.com/product/multipos-point-of-sale-for-woocommerce/?utm_source=plugin_dashboard&utm_medium=upgrade_notice&utm_campaign=multiple_outlets_pro_feature',
					]
				);
			} elseif ( ! empty( $action ) && ( 'add' === $action || 'edit' === $action ) ) {
				new Admin\Outlet\DDWCPOS_Manage_Outlet_Template( $this->ddwcpos_configuration );
			} else {
				if ( ! empty( $_GET['success'] ) ) {
					ddfw_print_notification( esc_html__( 'Outlet saved successfully.', 'devdiggers-multipos-for-woocommerce' ), 'success' );
				}
				// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
				$obj = new Admin\Outlet\DDWCPOS_Outlets_List_Template( $this->ddwcpos_configuration );

				$page  = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
				$menu  = isset( $_GET['menu'] ) ? sanitize_text_field( wp_unslash( $_GET['menu'] ) ) : '';
				$paged = isset( $_GET['paged'] ) ? sanitize_text_field( wp_unslash( $_GET['paged'] ) ) : '';
				?>
				<form method="get">
					<hr class="wp-header-end" />
					<h1 class="wp-heading-inline"><?php esc_html_e( 'Outlets', 'devdiggers-multipos-for-woocommerce' ); ?></h1>
					<?php if ( $outlet_count < 1 ) : ?>
						<a href="<?php echo esc_url( admin_url( "admin.php?page={$page}&menu={$menu}&action=add" ) ); ?>" class="page-title-action button-primary button">
							<?php
							DDFW_SVG::get_svg_icon(
								'plus',
								false,
								[ 'size' => 15 ]
							);
							esc_html_e( 'Add New', 'devdiggers-multipos-for-woocommerce' );
							?>
						</a>
					<?php else : ?>
						<a href="<?php echo esc_url( admin_url( "admin.php?page={$page}&menu={$menu}&action=add" ) ); ?>" class="page-title-action button ddfw-upgrade-to-pro-tag-wrapper">
							<?php esc_html_e( 'Add New', 'devdiggers-multipos-for-woocommerce' ); ?>
						</a>
						<?php endif; ?>
					<a href="<?php echo esc_url( site_url( $this->ddwcpos_configuration['endpoint'] ) ); ?>" class="page-title-action button" target="_blank"><?php esc_html_e( 'Visit POS', 'devdiggers-multipos-for-woocommerce' ); ?></a>
					<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>" />
					<input type="hidden" name="menu" value="<?php echo esc_attr( $menu ); ?>" />
					<input type="hidden" name="paged" value="<?php echo esc_attr( $paged ); ?>" />
					<?php
					wp_nonce_field( 'ddwcpos_nonce_action', 'ddwcpos_nonce' );
					$obj->prepare_items();
					$obj->search_box( esc_html__( 'Search', 'devdiggers-multipos-for-woocommerce' ), 'search-id' );
					$obj->display();
					?>
				</form>
				<?php
			}
		}

		/**
		 * Cashiers Template
		 *
		 * @return void
		 */
		public function ddwcpos_get_cashiers_template() {
			if ( ! empty( $_GET['success'] ) ) {
				ddfw_print_notification( esc_html__( 'Cashier created successfully.', 'devdiggers-multipos-for-woocommerce' ), 'success' );
			}
			$obj = new Admin\Cashier\DDWCPOS_Cashiers_List_Template();
			$cashier_query = new \WP_User_Query(
				[
					'role'   => 'ddwcpos_cashier',
					'fields' => 'ID',
					'number' => 1,
				]
			);
			$cashier_count = intval( $cashier_query->get_total() );

			$page  = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
			$menu  = isset( $_GET['menu'] ) ? sanitize_text_field( wp_unslash( $_GET['menu'] ) ) : '';
			$paged = isset( $_GET['paged'] ) ? sanitize_text_field( wp_unslash( $_GET['paged'] ) ) : '';
			?>
			<form method="get">
				<hr class="wp-header-end" />
				<h1 class="wp-heading-inline"><?php esc_html_e( 'Cashiers', 'devdiggers-multipos-for-woocommerce' ); ?></h1>
				<?php if ( $cashier_count < 1 ) : ?>
					<a href="<?php echo esc_url( admin_url( 'user-new.php' ) ); ?>" class="page-title-action button-primary button">
						<?php
						DDFW_SVG::get_svg_icon(
							'plus',
							false,
							[ 'size' => 15 ]
						);
						esc_html_e( 'Add New', 'devdiggers-multipos-for-woocommerce' );
						?>
					</a>
				<?php else : ?>
					<a href="<?php echo esc_url( admin_url( 'user-new.php' ) ); ?>" class="page-title-action button ddfw-upgrade-to-pro-tag-wrapper"><?php esc_html_e( 'Add New', 'devdiggers-multipos-for-woocommerce' ); ?></a>
				<?php endif; ?>
				<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>" />
				<input type="hidden" name="menu" value="<?php echo esc_attr( $menu ); ?>" />
				<input type="hidden" name="paged" value="<?php echo esc_attr( $paged ); ?>" />
				<?php
				wp_nonce_field( 'ddwcpos_nonce_action', 'ddwcpos_nonce' );
				$obj->prepare_items();
				$obj->search_box( esc_html__( 'Search', 'devdiggers-multipos-for-woocommerce' ), 'search-id' );
				$obj->display();
				?>
			</form>
			<?php
		}

		/**
		 * Product Barcodes Template
		 *
		 * @return void
		 */
		public function ddwcpos_get_product_barcodes_template() {
			$obj = new Admin\Barcode\DDWCPOS_Product_Barcodes_List_Template( $this->ddwcpos_configuration );

			$page  = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
			$menu  = isset( $_GET['menu'] ) ? sanitize_text_field( wp_unslash( $_GET['menu'] ) ) : '';
			$paged = isset( $_GET['paged'] ) ? sanitize_text_field( wp_unslash( $_GET['paged'] ) ) : '';
			?>
			<form method="get">
				<hr class="wp-header-end" />
				<h1 class="wp-heading-inline"><?php esc_html_e( 'Products', 'devdiggers-multipos-for-woocommerce' ); ?></h1>
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=product' ) ); ?>" class="page-title-action button-primary button">
					<?php
					DDFW_SVG::get_svg_icon(
						'plus',
						false,
						[ 'size' => 15 ]
					);
					esc_html_e( 'Add New', 'devdiggers-multipos-for-woocommerce' );
					?>
				</a>
				<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>" />
				<input type="hidden" name="menu" value="<?php echo esc_attr( $menu ); ?>" />
				<input type="hidden" name="paged" value="<?php echo esc_attr( $paged ); ?>" />
				<?php
				wp_nonce_field( 'ddwcpos_nonce_action', 'ddwcpos_nonce' );
				$obj->prepare_items();
				$obj->search_box( esc_html__( 'Search', 'devdiggers-multipos-for-woocommerce' ), 'search-id' );
				$obj->display();
				?>
			</form>
			<?php
		}

		/**
		 * Product Stocks Template
		 *
		 * @return void
		 */
		public function ddwcpos_get_product_stocks_template() {
			$obj = new Admin\Stock\DDWCPOS_Product_Stocks_List_Template( $this->ddwcpos_configuration );

			$page      = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
			$menu      = isset( $_GET['menu'] ) ? sanitize_text_field( wp_unslash( $_GET['menu'] ) ) : '';
			$paged     = isset( $_GET['paged'] ) ? sanitize_text_field( wp_unslash( $_GET['paged'] ) ) : '';
			$outlet_id = isset( $_GET['outlet-id'] ) ? sanitize_text_field( wp_unslash( $_GET['outlet-id'] ) ) : '';
			?>
			<form method="get">
				<hr class="wp-header-end" />
				<h1 class="wp-heading-inline"><?php esc_html_e( 'Products', 'devdiggers-multipos-for-woocommerce' ); ?></h1>
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=product' ) ); ?>" class="page-title-action button-primary button">
					<?php
					DDFW_SVG::get_svg_icon(
						'plus',
						false,
						[ 'size' => 15 ]
					);
					esc_html_e( 'Add New', 'devdiggers-multipos-for-woocommerce' );
					?>
				</a>
				<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>" />
				<input type="hidden" name="menu" value="<?php echo esc_attr( $menu ); ?>" />
				<input type="hidden" name="paged" value="<?php echo esc_attr( $paged ); ?>" />
				<input type="hidden" name="outlet-id" value="<?php echo esc_attr( $outlet_id ); ?>" />
				<?php
				wp_nonce_field( 'ddwcpos_nonce_action', 'ddwcpos_nonce' );
				$obj->prepare_items();
				$obj->search_box( esc_html__( 'Search', 'devdiggers-multipos-for-woocommerce' ), 'search-id' );
				$obj->display();
				?>
			</form>
			<?php
		}

		/**
		 * Product Stocks Upgrade Template
		 *
		 * @return void
		 */
		public function ddwcpos_get_product_stocks_upgrade_template() {
			ddfw_upgrade_to_pro_section(
				[
					'image_url'     => DDWCPOS_PLUGIN_URL . 'assets/images/pro-pages/assign-stocks.webp',
					'heading'       => esc_html__( 'Assign/Track Stock by Outlet', 'devdiggers-multipos-for-woocommerce' ),
					'description'   => esc_html__( 'Upgrade to Pro to manage inventory separately for each outlet and keep stock levels accurate across all locations.', 'devdiggers-multipos-for-woocommerce' ),
					'list_features' => [
						esc_html__( 'Set stock levels for each outlet', 'devdiggers-multipos-for-woocommerce' ),
						esc_html__( 'Use custom outlet stock or central WooCommerce inventory', 'devdiggers-multipos-for-woocommerce' ),
						esc_html__( 'Show only available products per location', 'devdiggers-multipos-for-woocommerce' ),
						esc_html__( 'Keep inventory accurate across multiple stores', 'devdiggers-multipos-for-woocommerce' ),
						esc_html__( 'Bulk stock updates using CSV import', 'devdiggers-multipos-for-woocommerce' ),
					],
					'upgrade_url' => '//devdiggers.com/product/multipos-point-of-sale-for-woocommerce/?utm_source=plugin_dashboard&utm_medium=upgrade_notice&utm_campaign=stock_management_pro_feature',
				]
			);
		}

		/**
		 * Orders Template
		 *
		 * @return void
		 */
		public function ddwcpos_get_orders_template() {
			$obj = new Admin\Order\DDWCPOS_Orders_List_Template();

			$page      = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
			$menu      = isset( $_GET['menu'] ) ? sanitize_text_field( wp_unslash( $_GET['menu'] ) ) : '';
			$paged     = isset( $_GET['paged'] ) ? sanitize_text_field( wp_unslash( $_GET['paged'] ) ) : '';
			$outlet_id = isset( $_GET['outlet-id'] ) ? sanitize_text_field( wp_unslash( $_GET['outlet-id'] ) ) : '';
			?>
			<form method="get">
				<hr class="wp-header-end" />
				<h1 class="wp-heading-inline"><?php esc_html_e( 'Orders', 'devdiggers-multipos-for-woocommerce' ); ?></h1>
				<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>" />
				<input type="hidden" name="menu" value="<?php echo esc_attr( $menu ); ?>" />
				<input type="hidden" name="paged" value="<?php echo esc_attr( $paged ); ?>" />
				<input type="hidden" name="outlet-id" value="<?php echo esc_attr( $outlet_id ); ?>" />
				<?php
				wp_nonce_field( 'ddwcpos_nonce_action', 'ddwcpos_nonce' );
				$obj->prepare_items();
				$obj->search_box( esc_html__( 'Search', 'devdiggers-multipos-for-woocommerce' ), 'search-id' );
				$obj->display();
				?>
			</form>
			<?php
		}

		/**
		 * Transactions Template
		 *
		 * @return void
		 */
		public function ddwcpos_get_transactions_template() {
			$obj = new Admin\Transaction\DDWCPOS_Transactions_List_Template( $this->ddwcpos_configuration );

			$page                  = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
			$menu                  = isset( $_GET['menu'] ) ? sanitize_text_field( wp_unslash( $_GET['menu'] ) ) : '';
			$paged                 = isset( $_GET['paged'] ) ? sanitize_text_field( wp_unslash( $_GET['paged'] ) ) : '';
			$outlet_id             = isset( $_GET['outlet-id'] ) ? sanitize_text_field( wp_unslash( $_GET['outlet-id'] ) ) : '';
			$transaction_from_date = isset( $_GET['transaction-from-date'] ) ? sanitize_text_field( wp_unslash( $_GET['transaction-from-date'] ) ) : '';
			$transaction_to_date   = isset( $_GET['transaction-to-date'] ) ? sanitize_text_field( wp_unslash( $_GET['transaction-to-date'] ) ) : '';
			$cashier_id            = isset( $_GET['cashier-id'] ) ? sanitize_text_field( wp_unslash( $_GET['cashier-id'] ) ) : '';
			?>
			<form method="get">
				<hr class="wp-header-end" />
				<h1 class="wp-heading-inline"><?php esc_html_e( 'Transactions', 'devdiggers-multipos-for-woocommerce' ); ?></h1>
				<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>" />
				<input type="hidden" name="menu" value="<?php echo esc_attr( $menu ); ?>" />
				<input type="hidden" name="paged" value="<?php echo esc_attr( $paged ); ?>" />
				<input type="hidden" name="outlet-id" value="<?php echo esc_attr( $outlet_id ); ?>" />
				<input type="hidden" name="transaction-from-date" value="<?php echo esc_attr( $transaction_from_date ); ?>" />
				<input type="hidden" name="transaction-to-date" value="<?php echo esc_attr( $transaction_to_date ); ?>" />
				<input type="hidden" name="cashier-id" value="<?php echo esc_attr( $cashier_id ); ?>" />
				<?php
				wp_nonce_field( 'ddwcpos_nonce_action', 'ddwcpos_nonce' );
				$obj->prepare_items();
				$obj->search_box( esc_html__( 'Search', 'devdiggers-multipos-for-woocommerce' ), 'search-id' );
				$obj->display();
				?>
			</form>
			<?php
		}

		/**
		 * Invoices Template
		 *
		 * @return void
		 */


		/**
		 * General Configuration Template
		 *
		 * @return void
		 */
		public function ddwcpos_get_general_configuration_template() {
			new Admin\Configuration\DDWCPOS_General_Configuration_Template( $this->ddwcpos_configuration );
		}

		/**
		 * Payments Configuration Template
		 *
		 * @return void
		 */
		public function ddwcpos_get_payments_configuration_template() {
			new Admin\Configuration\DDWCPOS_Payments_Configuration_Template( $this->ddwcpos_configuration );
		}

		/**
		 * Login Configuration Template
		 *
		 * @return void
		 */
		public function ddwcpos_get_login_configuration_template() {
			new Admin\Configuration\DDWCPOS_Login_Configuration_Template( $this->ddwcpos_configuration );
		}

		/**
		 * PWA Configuration Template
		 *
		 * @return void
		 */
		public function ddwcpos_get_pwa_configuration_template() {
			new Admin\Configuration\DDWCPOS_PWA_Configuration_Template( $this->ddwcpos_configuration );
		}

		/**
		 * Printer Configuration Template
		 *
		 * @return void
		 */
		public function ddwcpos_get_printer_configuration_template() {
			new Admin\Configuration\DDWCPOS_Printer_Configuration_Template( $this->ddwcpos_configuration );
		}

		/**
		 * Tables Configuration Template
		 *
		 * @return void
		 */
		public function ddwcpos_get_tables_configuration_template() {
			new Admin\Configuration\DDWCPOS_Tables_Configuration_Template( $this->ddwcpos_configuration );
		}

		/**
		 * Invoices Upgrade Template
		 *
		 * @return void
		 */
		public function ddwcpos_get_invoices_template() {
			new Admin\Invoices\DDWCPOS_Invoices_Template( $this->ddwcpos_configuration );
		}

		/**
		 * Layout Configuration Template
		 *
		 * @return void
		 */
		public function ddwcpos_get_layout_configuration_template() {
			new Admin\Configuration\DDWCPOS_Layout_Configuration_Template( $this->ddwcpos_configuration );
		}

		/**
		 * Enqueue admin scripts function
		 *
		 * @return void
		 */
		public function ddwcpos_enqueue_admin_scripts() {
			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';

			if ( 'edit-shop_order' === $screen_id || 'shop_order' === $screen_id || 'woocommerce_page_wc-orders' || $screen_id ) {
				wp_enqueue_style( 'ddwcpos-woocommerce-orders-list-style', DDWCPOS_PLUGIN_URL . 'assets/css/woocommerce-orders-list.css', [], filemtime( DDWCPOS_PLUGIN_FILE . 'assets/css/woocommerce-orders-list.css' ) );
			}

			if ( $this->dashboard->is_a_plugin_page() ) {
				wp_enqueue_media();

				wp_enqueue_style( 'ddwcpos-admin-style', DDWCPOS_PLUGIN_URL . 'assets/css/admin.css', [ DDFW_Assets::$framework_css_handle ], filemtime( DDWCPOS_PLUGIN_FILE . 'assets/css/admin.css' ) );

				wp_enqueue_script( 'ddwcpos-admin-script', DDWCPOS_PLUGIN_URL . 'assets/js/admin.js', [ DDFW_Assets::$framework_js_handle, 'wp-util' ], filemtime( DDWCPOS_PLUGIN_FILE . 'assets/js/admin.js' ) );

				wp_localize_script(
					'ddwcpos-admin-script',
					'ddwcposAdminObj',
					[
						'ajax'                  => [
							'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
							'ajaxNonce' => wp_create_nonce( 'ddwcpos-nonce' ),
						],
						'i18n'                  => [
							'pleaseEnter'          => esc_html__( 'Please enter', 'devdiggers-multipos-for-woocommerce' ),
							'moreCharacter'        => esc_html__( 'or more character', 'devdiggers-multipos-for-woocommerce' ),
							'noResult'             => esc_html__( 'No result Found', 'devdiggers-multipos-for-woocommerce' ),
							'uploadIcon'           => esc_html__( 'Upload Icon', 'devdiggers-multipos-for-woocommerce' ),
							'barcodeError'         => esc_html__( 'Barcode field is empty.', 'devdiggers-multipos-for-woocommerce' ),
							'barcodeQuantityError' => esc_html__( 'Barcode quantity field is empty.', 'devdiggers-multipos-for-woocommerce' ),
							'customStockError'     => esc_html__( 'Custom stock value is invalid.', 'devdiggers-multipos-for-woocommerce' ),
						],
						'ddwcpos_configuration' => $this->ddwcpos_configuration,
						'siteReferer'           => ! empty( $_SERVER['HTTP_REFERER'] ) ? false !== strpos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ), 'ddwcpos-cashiers' ) : false,
					]
				);

				// Register dashboard specific assets.
				$page = ! empty( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
				$menu = ! empty( $_GET['menu'] ) ? sanitize_text_field( wp_unslash( $_GET['menu'] ) ) : '';

				if ( 'ddwcpos-dashboard' === $page && ( empty( $menu ) || 'dashboard' === $menu ) ) {
					wp_register_style( 'ddwcpos-dashboard-style', DDWCPOS_PLUGIN_URL . 'assets/css/dashboard.css', [ DDFW_Assets::$framework_css_handle ], filemtime( DDWCPOS_PLUGIN_FILE . 'assets/css/dashboard.css' ) );
					wp_register_script( 'ddwcpos-dashboard-script', DDWCPOS_PLUGIN_URL . 'assets/js/dashboard.js', [ DDFW_Assets::$framework_js_handle ], filemtime( DDWCPOS_PLUGIN_FILE . 'assets/js/dashboard.js' ) );

					wp_enqueue_style( 'ddwcpos-dashboard-style' );
				}
			} elseif ( ! empty( $_SERVER['REQUEST_URI'] ) && ( false !== strpos( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'user-new.php' ) || false !== strpos( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'user-edit.php' ) ) ) {
				wp_enqueue_style( 'ddwcpos-user-page-style', DDWCPOS_PLUGIN_URL . 'assets/css/user-page.css', [ 'select2' ], filemtime( DDWCPOS_PLUGIN_FILE . 'assets/css/user-page.css' ) );

				wp_enqueue_script( 'ddwcpos-user-page-script', DDWCPOS_PLUGIN_URL . 'assets/js/user-page.js', [ 'select2', 'wp-util' ], filemtime( DDWCPOS_PLUGIN_FILE . 'assets/js/user-page.js' ) );

				wp_localize_script(
					'ddwcpos-user-page-script',
					'ddwcposUserPageObj',
					[
						'siteReferer' => ! empty( $_SERVER['HTTP_REFERER'] ) ? false !== strpos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ), 'ddwcpos-cashiers' ) : false,
					]
				);
			}
		}

		/**
		 * Change the admin footer text function.
		 *
		 * @param  string $footer_text text to be rendered in the footer.
		 * @return string
		 */
		public function ddwcpos_set_admin_footer_text( $footer_text ) {
			if ( ! current_user_can( 'manage_woocommerce' ) || ! function_exists( 'wc_get_screen_ids' ) ) {
				return $footer_text;
			}
			$current_screen = get_current_screen();
			$wc_pages       = wc_get_screen_ids();

			// Set only WC pages.
			$wc_pages = array_diff( $wc_pages, [ 'profile', 'user-edit' ] );

			/**
			 * Check to make sure we're on a plugin page.
			 * 
			 * @since 1.0.0
			 */
			if ( isset( $current_screen->base ) && 'devdiggers-plugins_page_ddwcpos-dashboard' === $current_screen->base ) {
				// Change the footer text.
				$footer_text = sprintf(
					/* translators: %s for a tag */
					esc_html__( 'If you really like our plugin, please leave us a %s rating, we\'ll really appreciate it.', 'devdiggers-multipos-for-woocommerce' ), '<a href="//wordpress.org/support/plugin/devdiggers-multipos-for-woocommerce/reviews/#new-post" target="_blank" title="' . esc_attr__( 'Review', 'devdiggers-multipos-for-woocommerce' ) . '" aria-label="' . esc_attr__( 'Review', 'devdiggers-multipos-for-woocommerce' ) . '"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 32" height="10"><path d="M16 26.534L6.111 32 8 20.422l-8-8.2 11.056-1.688L16 0l4.944 10.534L32 12.223l-8 8.2L25.889 32zm40 0L46.111 32 48 20.422l-8-8.2 11.056-1.688L56 0l4.944 10.534L72 12.223l-8 8.2L65.889 32zm40 0L86.111 32 88 20.422l-8-8.2 11.056-1.688L96 0l4.944 10.534L112 12.223l-8 8.2L105.889 32zm40 0L126.111 32 128 20.422l-8-8.2 11.056-1.688L136 0l4.944 10.534L152 12.223l-8 8.2L145.889 32zm40 0L166.111 32 168 20.422l-8-8.2 11.056-1.688L176 0l4.944 10.534L192 12.223l-8 8.2L185.889 32z" fill="#F5A623" fill-rule="evenodd"/></svg></a>'
				);
			}

			return $footer_text;
		}
	}
}
