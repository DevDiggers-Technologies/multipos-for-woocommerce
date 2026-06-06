<?php
/**
 * Dashboard Template
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Templates\Admin\Dashboard;

use DDWCMultiPOS\Helper\Dashboard\DDWCPOS_Dashboard_Helper;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Dashboard_Template' ) ) {
	/**
	 * Dashboard template class
	 */
	class DDWCPOS_Dashboard_Template {
		/**
		 * Configuration Variable
		 *
		 * @var array
		 */
		protected $ddwcpos_configuration;

		/**
		 * Dashboard Helper Variable
		 *
		 * @var object
		 */
		protected $dashboard_helper;

		/**
		 * Dashboard Data Variable
		 *
		 * @var array
		 */
		protected $dashboard_data;

		/**
		 * Construct
		 * 
		 * @param array $ddwcpos_configuration
		 */
		public function __construct( $ddwcpos_configuration ) {
			$this->ddwcpos_configuration = $ddwcpos_configuration;
			$this->dashboard_helper      = new DDWCPOS_Dashboard_Helper( $ddwcpos_configuration );
			$this->dashboard_data        = $this->dashboard_helper->get_dashboard_data();

			$this->render();
		}

		/**
		 * Render dashboard
		 *
		 * @return void
		 */

		/**
		 * Render dashboard
		 *
		 * @return void
		 */
        protected function render() {
            // Enqueue dashboard specific scripts/styles
            wp_enqueue_script( 'ddwcpos-dashboard-script' );
            
            wp_localize_script(
                'ddwcpos-dashboard-script',
                'ddwcposDashboardData',
                [
                    'summary'             => $this->dashboard_data['summary'],
                    'recent_orders'       => $this->dashboard_data['recent_orders'],
                    'revenueChart'        => $this->dashboard_data['charts']['revenue_chart'],
                    'outletsChart'        => $this->dashboard_data['charts']['outlets_chart'],
                    'paymentMethodsChart' => $this->dashboard_data['charts']['payment_methods'],
                    'dateRange'           => $this->dashboard_data['date_range'],
                    'currency'            => get_woocommerce_currency_symbol(),
                    'i18n'                => [
                        'revenue'             => esc_html__( 'Revenue', 'devdiggers-multipos-for-woocommerce' ),
                        'orders'              => esc_html__( 'Orders', 'devdiggers-multipos-for-woocommerce' ),
                        'outlets'             => esc_html__( 'Outlets Breakdown', 'devdiggers-multipos-for-woocommerce' ),
                        'paymentMethods'      => esc_html__( 'Payment Methods', 'devdiggers-multipos-for-woocommerce' ),
                        'noData'              => esc_html__( 'No data available', 'devdiggers-multipos-for-woocommerce' ),
                    ]
                ]
            );
            
			$current_user = wp_get_current_user();

			?>
			<div class="ddwcpos-dashboard">
				<!-- Enhanced Dashboard Header -->
				<div class="ddwcpos-dashboard-header">
					<!-- Header Top Section -->
					<div class="ddwcpos-header-top">
						<div class="ddwcpos-header-left">
							<div class="ddwcpos-welcome-section">
								<div class="ddwcpos-welcome-content">
									<div class="ddwcpos-admin-avatar">
                                        <img src="<?php echo esc_url( get_avatar_url( $current_user->ID, [ 'size' => 48 ] ) ); ?>" alt="<?php echo esc_attr( $current_user->display_name ); ?>" class="ddwcpos-avatar-image" />
									</div>
									<div class="ddwcpos-welcome-message">
											<h1>
												<?php
												/* translators: %s: Current user's display name. */
												printf( esc_html__( 'Welcome back, %s! 👋🏻', 'devdiggers-multipos-for-woocommerce' ), esc_html( $current_user->display_name ) );
												?>
											</h1>
										<p class="ddwcpos-welcome-subtitle"><?php esc_html_e( 'Here\'s what\'s happening with your POS stores.', 'devdiggers-multipos-for-woocommerce' ); ?></p>
									</div>
								</div>
							</div>
						</div>

                        <!-- Date Filter -->
						<div class="ddwcpos-header-right">
                            <div class="ddwcpos-dashboard-filters">
                                <form method="get" class="ddwcpos-date-filter-form">
	                                    <?php
	                                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Page slug is read-only form context.
	                                    $page = ! empty( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	                                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Menu slug is read-only form context.
	                                    $menu = ! empty( $_GET['menu'] ) ? sanitize_text_field( wp_unslash( $_GET['menu'] ) ) : '';
	                                    ?>
	                                    <input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>" />
	                                    <input type="hidden" name="menu" value="<?php echo esc_attr( $menu ); ?>" />

                                    <div class="ddwcpos-date-range-container">
										<input type="text"
											id="ddwcpos-date-range-picker"
											class="ddwcpos-date-range-picker"
											value="<?php echo esc_attr( $this->dashboard_data['date_range']['label'] ); ?>"
											readonly />

                                        <div class="ddwcpos-date-range-dropdown" id="ddwcpos-date-range-dropdown">
                                            <div class="ddwcpos-dropdown-content">
                                                <div class="ddwcpos-date-presets">
                                                    <div class="ddwcpos-presets-header">
                                                        <h4><?php esc_html_e( 'Quick Select', 'devdiggers-multipos-for-woocommerce' ); ?></h4>
                                                    </div>
                                                    <button type="button" class="ddwcpos-date-preset" data-range="today"><?php esc_html_e( 'Today', 'devdiggers-multipos-for-woocommerce' ); ?></button>
                                                    <button type="button" class="ddwcpos-date-preset" data-range="7_days"><?php esc_html_e( 'This Week', 'devdiggers-multipos-for-woocommerce' ); ?></button>
                                                    <button type="button" class="ddwcpos-date-preset" data-range="last_week"><?php esc_html_e( 'Last Week', 'devdiggers-multipos-for-woocommerce' ); ?></button>
                                                    <button type="button" class="ddwcpos-date-preset" data-range="30_days"><?php esc_html_e( 'This Month', 'devdiggers-multipos-for-woocommerce' ); ?></button>
                                                    <button type="button" class="ddwcpos-date-preset" data-range="last_month"><?php esc_html_e( 'Last Month', 'devdiggers-multipos-for-woocommerce' ); ?></button>
                                                    <button type="button" class="ddwcpos-date-preset" data-range="90_days"><?php esc_html_e( 'Last 3 Months', 'devdiggers-multipos-for-woocommerce' ); ?></button>
                                                    <button type="button" class="ddwcpos-date-preset" data-range="180_days"><?php esc_html_e( 'Last 6 Months', 'devdiggers-multipos-for-woocommerce' ); ?></button>
                                                    <button type="button" class="ddwcpos-date-preset" data-range="year_to_date"><?php esc_html_e( 'Year to Date', 'devdiggers-multipos-for-woocommerce' ); ?></button>
                                                    <button type="button" class="ddwcpos-date-preset" data-range="last_year"><?php esc_html_e( 'Last Year', 'devdiggers-multipos-for-woocommerce' ); ?></button>
                                                    <button type="button" class="ddwcpos-date-preset" data-range="all_time"><?php esc_html_e( 'All Time', 'devdiggers-multipos-for-woocommerce' ); ?></button>
                                                </div>

                                                <div class="ddwcpos-custom-date-range">
                                                    <div class="ddwcpos-custom-header">
                                                        <h4><?php esc_html_e( 'Custom Range', 'devdiggers-multipos-for-woocommerce' ); ?></h4>
                                                    </div>
                                                    <div class="ddwcpos-date-inputs">
                                                        <div class="ddwcpos-date-input-group">
                                                            <label for="ddwcpos-from-date"><?php esc_html_e( 'From', 'devdiggers-multipos-for-woocommerce' ); ?></label>
                                                            <input type="date" name="from_date" id="ddwcpos-from-date" value="<?php echo esc_attr( $this->dashboard_data['date_range']['from'] ); ?>" />
                                                        </div>
                                                        <div class="ddwcpos-date-input-group">
                                                            <label for="ddwcpos-to-date"><?php esc_html_e( 'To', 'devdiggers-multipos-for-woocommerce' ); ?></label>
                                                            <input type="date" name="to_date" id="ddwcpos-to-date" value="<?php echo esc_attr( $this->dashboard_data['date_range']['to'] ); ?>" />
                                                        </div>
                                                    </div>
                                                    <button type="button" class="ddwcpos-apply-custom-range button button-primary"><?php esc_html_e( 'Apply', 'devdiggers-multipos-for-woocommerce' ); ?></button>
                                                </div>
                                            </div>
                                        </div>

                                        <input type="hidden" name="date_range" id="ddwcpos-selected-range" value="<?php echo esc_attr( $this->dashboard_data['date_range']['type'] ); ?>" />
                                    </div>
                                </form>
                            </div>
						</div>
					</div>
				</div>

				<div class="ddwcpos-dashboard-content">
					<!-- Summary Cards -->
					<div class="ddwcpos-summary-cards">
						<?php
						$this->render_summary_card(
							esc_html__( 'Total Orders', 'devdiggers-multipos-for-woocommerce' ),
							$this->dashboard_data['summary']['total_orders']['value'],
							$this->dashboard_data['summary']['total_orders']['change'],
							$this->dashboard_data['summary']['total_orders']['is_positive'],
                            '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4H6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 6h18M16 10a4 4 0 0 1-8 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
						);

						$this->render_summary_card(
							esc_html__( 'Total Revenue', 'devdiggers-multipos-for-woocommerce' ),
							wc_price( $this->dashboard_data['summary']['total_revenue']['value'] ),
							$this->dashboard_data['summary']['total_revenue']['change'],
							$this->dashboard_data['summary']['total_revenue']['is_positive'],
                            '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                            'html'
						);

						$this->render_summary_card(
							esc_html__( 'Active Outlets', 'devdiggers-multipos-for-woocommerce' ),
							$this->dashboard_data['summary']['total_outlets'],
							0,
							true,
                            '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 7L1 11H23L22 7H2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M4 11V19C4 20.1046 4.89543 21 6 21H18C19.1046 21 20 20.1046 20 19V11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M9 21V15H15V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M2 7L4 3H20L22 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
						);

						$this->render_summary_card(
							esc_html__( 'Cashiers', 'devdiggers-multipos-for-woocommerce' ),
							$this->dashboard_data['summary']['total_cashiers'],
							0,
							true,
                            '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
						);

                        // Top Payment Method Calculation
                        $top_payment = !empty($this->dashboard_data['charts']['payment_methods']) ? $this->dashboard_data['charts']['payment_methods'][0]['payment_method'] : __('N/A', 'devdiggers-multipos-for-woocommerce');
                        $this->render_summary_card(
							esc_html__( 'Top Payment', 'devdiggers-multipos-for-woocommerce' ),
							$top_payment,
							0,
							true,
                            '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M2 10h20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                            'text'
						);
						?>
					</div>

                    <!-- Charts Section -->
                    <div class="ddwcpos-dashboard-charts">
                        <!-- Revenue Chart -->
                        <div class="ddwcpos-chart-container">
                            <h3>
                                <?php esc_html_e( 'Revenue Overview', 'devdiggers-multipos-for-woocommerce' ); ?>
                                <span class="ddwcpos-chart-subtitle"><?php echo esc_html( $this->dashboard_data['date_range']['label'] ); ?></span>
                            </h3>
                            <div class="ddwcpos-chart-placeholder">
                                <canvas id="ddwcpos-revenue-chart"></canvas>
                            </div>
                        </div>

                        <!-- Secondary Charts Grid -->
                        <div class="ddwcpos-charts-grid">
                            <div class="ddwcpos-chart-container">
                                <h3>
                                    <?php esc_html_e( 'Sales by Outlet', 'devdiggers-multipos-for-woocommerce' ); ?>
                                    <span class="ddwcpos-chart-subtitle"><?php echo esc_html( $this->dashboard_data['date_range']['label'] ); ?></span>
                                </h3>
                                <div class="ddwcpos-chart-placeholder">
                                    <canvas id="ddwcpos-outlets-chart"></canvas>
                                </div>
                            </div>
                            <div class="ddwcpos-chart-container">
                                <h3>
                                    <?php esc_html_e( 'Payment Methods', 'devdiggers-multipos-for-woocommerce' ); ?>
                                    <span class="ddwcpos-chart-subtitle"><?php echo esc_html( $this->dashboard_data['date_range']['label'] ); ?></span>
                                </h3>
                                <div class="ddwcpos-chart-placeholder">
                                    <canvas id="ddwcpos-payment-methods-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="ddwcpos-dashboard-tables">
                        <div class="ddwcpos-table-widget">
                            <h3><?php esc_html_e( 'Recent POS Orders', 'devdiggers-multipos-for-woocommerce' ); ?></h3>
                            <table class="ddwcpos-table">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e( 'Order', 'devdiggers-multipos-for-woocommerce' ); ?></th>
                                        <th><?php esc_html_e( 'Date', 'devdiggers-multipos-for-woocommerce' ); ?></th>
                                        <th><?php esc_html_e( 'Customer', 'devdiggers-multipos-for-woocommerce' ); ?></th>
                                        <th><?php esc_html_e( 'Status', 'devdiggers-multipos-for-woocommerce' ); ?></th>
                                        <th><?php esc_html_e( 'Total', 'devdiggers-multipos-for-woocommerce' ); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ( ! empty( $this->dashboard_data['recent_orders'] ) ) : ?>
                                        <?php foreach ( $this->dashboard_data['recent_orders'] as $order ) : ?>
                                            <tr>
                                                <td><a href="<?php echo esc_url( admin_url( 'post.php?post=' . $order['id'] . '&action=edit' ) ); ?>">#<?php echo esc_html( $order['number'] ); ?></a></td>
                                                <td><?php echo esc_html( $order['date'] ); ?></td>
                                                <td><?php echo esc_html( $order['customer'] ); ?></td>
                                                <td><span class="ddwcpos-status-badge status-<?php echo esc_attr( strtolower( $order['status'] ) ); ?>"><?php echo esc_html( $order['status'] ); ?></span></td>
                                                <td><?php echo wp_kses_post( $order['total'] ); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="5" class="ddwcpos-no-data"><?php esc_html_e( 'No recent orders found.', 'devdiggers-multipos-for-woocommerce' ); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
				</div>
			</div>
			<?php
		}

		/**
		 * Render summary card
		 *
		 * @param string $title
		 * @param mixed $value
         * @param float $change
         * @param bool $is_positive
		 * @param string $icon
         * @param string $value_type
		 * @return void
		 */
		protected function render_summary_card( $title, $value, $change, $is_positive, $icon, $value_type = 'number' ) {
			?>
			<div class="ddwcpos-summary-card">
				<div class="ddwcpos-card-header">
					<div class="ddwcpos-card-icon"><?php echo wp_kses( $icon, $this->get_allowed_svg_tags() ); ?></div>
                    <?php if ( $change != 0 ) : ?>
                        <div class="ddwcpos-change-indicator <?php echo esc_attr( $is_positive ? 'positive' : 'negative' ); ?>">
                            <?php if ( $is_positive ) : ?>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M23 6l-9.5 9.5-5-5L1 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M17 6h6v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            <?php else : ?>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M23 18l-9.5-9.5-5 5L1 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M17 18h6v-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            <?php endif; ?>
                            <span><?php echo esc_html( abs( $change ) ); ?>%</span>
                        </div>
                    <?php endif; ?>
				</div>
				<div class="ddwcpos-card-content">
					<h4><?php echo esc_html( $title ); ?></h4>
					<div class="ddwcpos-card-value">
                        <?php if ( 'html' === $value_type ) : ?>
						    <span class="ddwcpos-value-text"><?php echo wp_kses_post( $value ); ?></span>
                        <?php else : ?>
                            <span class="ddwcpos-value-number"><?php echo esc_html( is_numeric( $value ) ? number_format( $value ) : $value ); ?></span>
                        <?php endif; ?>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Allowed SVG tags for internal dashboard icons.
		 *
		 * @return array
		 */
		protected function get_allowed_svg_tags() {
			return [
				'svg'    => [
					'width'   => true,
					'height'  => true,
					'viewbox' => true,
					'fill'    => true,
					'xmlns'   => true,
				],
				'path'   => [
					'd'               => true,
					'stroke'          => true,
					'stroke-width'    => true,
					'stroke-linecap'  => true,
					'stroke-linejoin' => true,
					'fill'            => true,
				],
				'circle' => [
					'cx'           => true,
					'cy'           => true,
					'r'            => true,
					'stroke'       => true,
					'stroke-width' => true,
					'fill'         => true,
				],
				'rect'   => [
					'x'            => true,
					'y'            => true,
					'width'        => true,
					'height'       => true,
					'rx'           => true,
					'stroke'       => true,
					'stroke-width' => true,
					'fill'         => true,
				],
			];
		}
	}
}
