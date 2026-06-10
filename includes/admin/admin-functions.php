<?php
/**
 * This file handles all admin end action callbacks.
 *
 * @author DevDiggers
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Includes\Admin;

use DDWCMultiPOS\Helper\Outlet\DDWCPOS_Outlet_Helper;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Admin_Functions' ) ) {
	/**
	 * Admin Functions Class
	 */
	class DDWCPOS_Admin_Functions {
		/**
		 * Configuration Variable
		 *
		 * @var array
		 */
		protected $ddwcpos_configuration;

		/**
		 * Construct
		 */
		public function __construct( $ddwcpos_configuration ) {
			$this->ddwcpos_configuration = $ddwcpos_configuration;
		}

		/**
		 * Register settings function
		 *
		 * @return settings
		 */
		public function ddwcpos_register_settings() {
				register_setting( 'ddwcpos-general-configuration-fields', '_ddwcpos_enabled', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-general-configuration-fields', '_ddwcpos_order_status', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-general-configuration-fields', '_ddwcpos_default_barcode', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-general-configuration-fields', '_ddwcpos_order_mails_enabled', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-general-configuration-fields', '_ddwcpos_logo', [ 'sanitize_callback' => 'absint' ] );
				register_setting( 'ddwcpos-general-configuration-fields', '_ddwcpos_default_customer', [ 'sanitize_callback' => 'absint' ] );
				register_setting( 'ddwcpos-general-configuration-fields', '_ddwcpos_endpoint', [ 'sanitize_callback' => 'sanitize_title' ] );

			register_setting( 'ddwcpos-payments-configuration-fields', '_ddwcpos_payment_method', [ 'sanitize_callback' => [ $this, 'ddwcpos_sanitize_payment_methods' ] ] );

				register_setting( 'ddwcpos-login-configuration-fields', '_ddwcpos_login_heading_text', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-login-configuration-fields', '_ddwcpos_login_subtitle_text', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-login-configuration-fields', '_ddwcpos_login_footer_text', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-login-configuration-fields', '_ddwcpos_login_button_text', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-login-configuration-fields', '_ddwcpos_login_rememberme_enabled', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-login-configuration-fields', '_ddwcpos_login_forgot_enabled', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-login-configuration-fields', '_ddwcpos_login_branding_enabled', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-login-configuration-fields', '_ddwcpos_login_bg_primary_color', [ 'sanitize_callback' => 'sanitize_hex_color' ] );
				register_setting( 'ddwcpos-login-configuration-fields', '_ddwcpos_login_bg_secondary_color', [ 'sanitize_callback' => 'sanitize_hex_color' ] );
				register_setting( 'ddwcpos-login-configuration-fields', '_ddwcpos_login_canvas_bg_color', [ 'sanitize_callback' => 'sanitize_hex_color' ] );
				register_setting( 'ddwcpos-login-configuration-fields', '_ddwcpos_login_card_bg_color', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-login-configuration-fields', '_ddwcpos_login_font_color', [ 'sanitize_callback' => 'sanitize_hex_color' ] );

				register_setting( 'ddwcpos-printer-configuration-fields', '_ddwcpos_barcode_printer_width', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-printer-configuration-fields', '_ddwcpos_barcode_printer_height', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-printer-configuration-fields', '_ddwcpos_barcode_printer_margin', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-printer-configuration-fields', '_ddwcpos_barcode_height', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-printer-configuration-fields', '_ddwcpos_barcode_margin', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-printer-configuration-fields', '_ddwcpos_barcode_orientation', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-printer-configuration-fields', '_ddwcpos_printer_width', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-printer-configuration-fields', '_ddwcpos_printer_height', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-printer-configuration-fields', '_ddwcpos_printer_margin', [ 'sanitize_callback' => 'sanitize_text_field' ] );

			register_setting( 'ddwcpos-tables-configuration-fields', '_ddwcpos_tables', [ 'sanitize_callback' => [ $this, 'ddwcpos_sanitize_tables' ] ] );
			register_setting( 'ddwcpos-invoices-configuration-fields', '_ddwcpos_invoices', [ 'sanitize_callback' => [ $this, 'ddwcpos_sanitize_invoices' ] ] );

				register_setting( 'ddwcpos-layout-configuration-fields', '_ddwcpos_layout_primary_color', [ 'sanitize_callback' => 'sanitize_hex_color' ] );
				register_setting( 'ddwcpos-layout-configuration-fields', '_ddwcpos_layout_secondary_color', [ 'sanitize_callback' => 'sanitize_hex_color' ] );
				register_setting( 'ddwcpos-layout-configuration-fields', '_ddwcpos_layout_font_color', [ 'sanitize_callback' => 'sanitize_hex_color' ] );
				register_setting( 'ddwcpos-layout-configuration-fields', '_ddwcpos_layout_surface_color', [ 'sanitize_callback' => 'sanitize_hex_color' ] );
				register_setting( 'ddwcpos-layout-configuration-fields', '_ddwcpos_layout_muted_background_color_1', [ 'sanitize_callback' => 'sanitize_hex_color' ] );
				register_setting( 'ddwcpos-layout-configuration-fields', '_ddwcpos_layout_muted_background_color_2', [ 'sanitize_callback' => 'sanitize_hex_color' ] );
				register_setting( 'ddwcpos-layout-configuration-fields', '_ddwcpos_layout_button_font_color', [ 'sanitize_callback' => 'sanitize_hex_color' ] );
				register_setting( 'ddwcpos-layout-configuration-fields', '_ddwcpos_layout_success_color', [ 'sanitize_callback' => 'sanitize_hex_color' ] );
				register_setting( 'ddwcpos-layout-configuration-fields', '_ddwcpos_layout_border_color', [ 'sanitize_callback' => 'sanitize_hex_color' ] );
				register_setting( 'ddwcpos-layout-configuration-fields', '_ddwcpos_layout_pos_font_family', [ 'sanitize_callback' => 'sanitize_key' ] );
				register_setting( 'ddwcpos-layout-configuration-fields', '_ddwcpos_layout_radius', [ 'sanitize_callback' => 'absint' ] );
				register_setting( 'ddwcpos-layout-configuration-fields', '_ddwcpos_layout_font_size', [ 'sanitize_callback' => 'absint' ] );
				register_setting( 'ddwcpos-layout-configuration-fields', '_ddwcpos_product_layout', [ 'sanitize_callback' => 'sanitize_text_field' ] );
				register_setting( 'ddwcpos-layout-configuration-fields', '_ddwcpos_show_product_stock_enabled', [ 'sanitize_callback' => 'sanitize_text_field' ] );
		}

		/**
		 * Sanitize the POS payment methods.
		 *
		 * Keeps every submitted method and guarantees the built-in cash method
		 * always exists. No method count limit is imposed.
		 *
		 * @param mixed $payment_methods Submitted payment methods.
		 * @return array
		 */
		public function ddwcpos_sanitize_payment_methods( $payment_methods ) {
			$sanitized = [];

			foreach ( (array) $payment_methods as $payment_method ) {
				$name = ! empty( $payment_method['name'] ) ? sanitize_text_field( $payment_method['name'] ) : '';
				$slug = ! empty( $payment_method['slug'] ) ? sanitize_key( $payment_method['slug'] ) : sanitize_key( $name );

				if ( '' === $name || '' === $slug ) {
					continue;
				}

				$sanitized[ $slug ] = [
					'name'      => $name,
					'slug'      => $slug,
					'permanent' => ( ! empty( $payment_method['permanent'] ) && 'yes' === $payment_method['permanent'] ) ? 'yes' : 'no',
					'status'    => ( ! empty( $payment_method['status'] ) && 'disabled' === $payment_method['status'] ) ? 'disabled' : 'enabled',
				];
			}

			// The built-in cash method is always available and not removable.
			if ( ! isset( $sanitized['cash'] ) ) {
				$sanitized = array_merge(
					[
						'cash' => [
							'name'      => esc_html__( 'Cash', 'devdiggers-multipos-for-woocommerce' ),
							'slug'      => 'cash',
							'permanent' => 'yes',
							'status'    => 'enabled',
						],
					],
					$sanitized
				);
			} else {
				$sanitized['cash']['permanent'] = 'yes';
			}

			return array_values( $sanitized );
		}

		/**
		 * Sanitize restaurant table definitions.
		 *
		 * @param mixed $tables Submitted table rows.
		 * @return array
		 */
		public function ddwcpos_sanitize_tables( $tables ) {
			$sanitized_tables = [];

			foreach ( (array) $tables as $table ) {
				$name = ! empty( $table['name'] ) ? sanitize_text_field( $table['name'] ) : '';
				$slug = ! empty( $table['slug'] ) ? sanitize_title( $table['slug'] ) : '';

				if ( '' === $name || '' === $slug ) {
					continue;
				}

				$sanitized_tables[] = [
					'name'   => $name,
					'slug'   => $slug,
					'seats'  => max( 1, absint( $table['seats'] ?? 1 ) ),
					'status' => ( ! empty( $table['status'] ) && 'disabled' === $table['status'] ) ? 'disabled' : 'enabled',
				];
			}

			return $sanitized_tables;
		}

		/**
		 * Sanitize the POS invoice templates.
		 *
		 * Keeps every submitted template and guarantees the built-in receipt
		 * template always exists. No template count limit is imposed.
		 *
		 * @param mixed $invoices Submitted invoice templates.
		 * @return array
		 */
		public function ddwcpos_sanitize_invoices( $invoices ) {
			$sanitized = [];

			foreach ( (array) $invoices as $invoice ) {
				$name = ! empty( $invoice['name'] ) ? sanitize_text_field( $invoice['name'] ) : '';
				$slug = ! empty( $invoice['slug'] ) ? sanitize_title( $invoice['slug'] ) : sanitize_title( $name );

				if ( '' === $name || '' === $slug ) {
					continue;
				}

				$sanitized[ $slug ] = [
					'name'      => $name,
					'slug'      => $slug,
					'permanent' => ( ! empty( $invoice['permanent'] ) && 'yes' === $invoice['permanent'] ) ? 'yes' : 'no',
					'status'    => ( ! empty( $invoice['status'] ) && 'disabled' === $invoice['status'] ) ? 'disabled' : 'enabled',
				];
			}

			// The built-in receipt template is always available and not removable.
			if ( ! isset( $sanitized['default-invoice'] ) ) {
				$sanitized = array_merge(
					[
						'default-invoice' => [
							'name'      => esc_html__( 'Default Invoice', 'devdiggers-multipos-for-woocommerce' ),
							'slug'      => 'default-invoice',
							'permanent' => 'yes',
							'status'    => 'enabled',
						],
					],
					$sanitized
				);
			} else {
				$sanitized['default-invoice']['permanent'] = 'yes';
				$sanitized['default-invoice']['status']    = 'enabled';
			}

			return array_values( $sanitized );
		}

		/**
		 * Add user form fields function
		 *
		 * @return void
		 */
		public function ddwcpos_add_user_form_fields() {
			$this->ddwcpos_render_user_outlet_details_template();
		}

		/**
		 * Validate fields
		 *
		 * @param object $errors
		 * @param string $update
		 * @param object $user
		 * @return void
		 */
		public function ddwcpos_validate_user_fields( &$errors, $update = null, &$user = null ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- WordPress user form nonce is verified by core before this validation hook.
				$submitted_role = ! empty( $_POST['role'] ) ? sanitize_key( wp_unslash( $_POST['role'] ) ) : '';

			if ( 'ddwcpos_cashier' === $submitted_role ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Missing -- WordPress user form nonce is verified by core before this validation hook.
					if ( empty( $_POST['ddwcpos_assigned_outlets'] ) ) {
					$errors->add( 'ddwcpos_assigned_outlets_error', esc_html__( 'Please select the assigned outlets.', 'devdiggers-multipos-for-woocommerce' ) );
				}
			}

			return $errors;
		}

		/**
		 * Save user custom data function.
		 *
		 * @param int $user_id
		 * @return void
		 */
			public function ddwcpos_save_user_custom_data( $user_id ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- WordPress user form nonce is verified by core before this save hook.
				if ( ! empty( $_POST[ 'createuser' ] ) && ! empty( $_POST[ 'role' ] ) && 'ddwcpos_cashier' === sanitize_key( wp_unslash( $_POST[ 'role' ] ) ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Missing -- WordPress user form nonce is verified by core before this save hook.
					$assigned_outlets  = ! empty( $_POST[ 'ddwcpos_assigned_outlets' ] ) ? array_map( 'absint', (array) wp_unslash( $_POST[ 'ddwcpos_assigned_outlets' ] ) ) : [];
					// phpcs:ignore WordPress.Security.NonceVerification.Missing -- WordPress user form nonce is verified by core before this save hook.
					$from_cashier_page = ! empty( $_POST[ 'ddwcpos_from_cashier_page' ] ) ? absint( wp_unslash( $_POST[ 'ddwcpos_from_cashier_page' ] ) ) : 0;
				update_user_meta( $user_id, '_ddwcpos_assigned_outlets', $assigned_outlets );

				if ( 0 != $from_cashier_page ) {
					wp_safe_redirect( admin_url( 'admin.php?page=ddwcpos-dashboard&menu=cashiers&success=yes' ) );
					exit();
				}
			}
		}

		/**
		 * Display custom user profile fields function
		 *
		 * @param object $user
		 * @return void
		 */
		public function ddwcpos_display_custom_user_profile_fields( $user ) {
			$assigned_outlets = get_user_meta( $user->ID, '_ddwcpos_assigned_outlets', true );
			$this->ddwcpos_render_user_outlet_details_template( $assigned_outlets );
		}

		/**
		 * Render user outlet details template function
		 *
		 * @param array $assigned_outlets
		 * @return void
		 */
		protected function ddwcpos_render_user_outlet_details_template( $assigned_outlets = [] ) {
			$outlet_helper     = new DDWCPOS_Outlet_Helper();
			$outlets           = $outlet_helper->ddwcpos_get_all_outlets( 999999, 0, '' );
				$referer           = ! empty( $_SERVER[ 'HTTP_REFERER' ] ) ? esc_url_raw( wp_unslash( $_SERVER[ 'HTTP_REFERER' ] ) ) : '';
				$from_cashier_page = ! empty( $referer ) && false !== strpos( $referer, 'menu=cashiers' ) ? 1 : 0;
			?>
			<script id="tmpl-ddwcpos_assigned_outlets" type="text/html">
				<div id="ddwcpos-assigned-outlets-row" class="ddwcpos-user-profile-card">
					<div class="ddwcpos-card-header">
						<h3 class="heading"><?php esc_html_e( 'MultiPOS Outlet Details', 'devdiggers-multipos-for-woocommerce' ); ?></h3>
						<p class="description"><?php esc_html_e( 'Assign specific store locations to this user for POS access.', 'devdiggers-multipos-for-woocommerce' ); ?></p>
					</div>
					<div class="ddwcpos-card-content">
						<table class="form-table" role="presentation">
							<tbody>
								<tr valign="top">
									<th>
										<label for="ddwcpos-assigned-outlets">
											<?php esc_html_e( 'Assigned Outlets', 'devdiggers-multipos-for-woocommerce' ); ?>
											<span class="ddwcpos-required-label"><?php esc_html_e( '(required)', 'devdiggers-multipos-for-woocommerce' ); ?></span>
										</label>
									</th>
									<td>
										<input type="hidden" name="ddwcpos_from_cashier_page" value="<?php echo esc_attr( $from_cashier_page ); ?>" />
										<select id="ddwcpos-assigned-outlets" name="ddwcpos_assigned_outlets[]" class="ddwcpos-assigned-outlets regular-text" data-placeholder="<?php esc_attr_e( 'Select Outlet(s)', 'devdiggers-multipos-for-woocommerce' ); ?>" multiple required>
											<?php
											foreach ( $outlets as $key => $outlet ) {
												?>
												<option value="<?php echo esc_attr( $outlet[ 'id' ] ); ?>" <?php echo esc_attr( in_array( $outlet[ 'id' ], (array) $assigned_outlets, true ) ? 'selected="selected"' : '' ); ?>><?php echo esc_html( $outlet[ 'name' ] ); ?></option>
												<?php
											}
											?>
										</select>
										<p class="description"><?php esc_html_e( 'The user will only be able to log in to the POS of the assigned outlets.', 'devdiggers-multipos-for-woocommerce' ); ?></p>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</script>
			<?php
		}

		/**
		 * Save custom user profile fields function
		 *
		 * @param int $user_id
		 * @return void
		 */
		public function ddwcpos_save_custom_user_profile_fields( $user_id ) {
				$nonce          = ! empty( $_POST[ '_wpnonce' ] ) ? sanitize_text_field( wp_unslash( $_POST[ '_wpnonce' ] ) ) : '';
				$submitted_role = ! empty( $_POST[ 'role' ] ) ? sanitize_key( wp_unslash( $_POST[ 'role' ] ) ) : '';
				if ( ! empty( $nonce ) && wp_verify_nonce( $nonce, 'update-user_' . $user_id ) && current_user_can( 'edit_user', $user_id ) && 'ddwcpos_cashier' === $submitted_role ) {
				$assigned_outlets = ! empty( $_POST[ 'ddwcpos_assigned_outlets' ] ) ? array_map( 'absint', (array) wp_unslash( $_POST[ 'ddwcpos_assigned_outlets' ] ) ) : [];
				update_user_meta( $user_id, '_ddwcpos_assigned_outlets', $assigned_outlets );
			}
		}

		/**
		 * Get saved POS cashier count.
		 *
		 * @return int
		 */
		protected function ddwcpos_get_pos_cashiers_count() {
			$query = new \WP_User_Query(
				[
					'role'   => 'ddwcpos_cashier',
					'fields' => 'ID',
					'number' => 1,
				]
			);

			return intval( $query->get_total() );
		}

		/**
		 * Admin bar menu function
		 *
		 * @param object $wp_admin_bar
		 * @return void
		 */
		public function ddwcpos_admin_bar_menu( $wp_admin_bar ) {
			if ( ! is_user_logged_in() ) {
				return;
			}

			// Show only when the user is a member of this site, or they're a super admin.
			if ( ! is_user_member_of_blog() && ! is_super_admin() ) {
				return;
			}

			// Add an option to visit the store.
			$wp_admin_bar->add_node( [
				'parent' => 'site-name',
				'id'     => 'view-pos',
				'title'  => esc_html__( 'Visit POS', 'devdiggers-multipos-for-woocommerce' ),
				'href'   => site_url( $this->ddwcpos_configuration[ 'endpoint' ] ),
			] );
		}

		/**
		 * Add custom WooCommerce orders column function
		 *
		 * @param array $columns
		 * @return array
		 */
		public function ddwcpos_add_custom_woocommerce_orders_column( $columns ) {
			$new_columns = [];
			foreach ( $columns as $key => $value ) {
				if ( 'order_number' === $key ) {
					ob_start();
					?>
					<span class="order-type tips" data-tip="<?php esc_attr_e( 'Order Type', 'devdiggers-multipos-for-woocommerce' ); ?>"><?php esc_html_e( 'Order Type', 'devdiggers-multipos-for-woocommerce' ); ?></span>
					<?php
					$new_columns[ 'ddwcpos_order_type' ] = ob_get_clean();
				}

				$new_columns[ $key ] = $value;
			}
			return $new_columns;
		}

		/**
		 * Add custom WooCommerce orders column content function
		 *
		 * @param array $column
		 * @param int $order_id
		 * @return string
		 */
		public function ddwcpos_add_custom_woocommerce_orders_column_content( $column, $order_id ) {
			$the_order = wc_get_order( $order_id );

			if ( ! $the_order ) {
				return;
			}

			if ( 'ddwcpos_order_type' === $column ) {
				if ( ! empty( $the_order->get_meta( '_ddwcpos_outlet_id', true ) ) ) {
					/* translators: %1$s opening span tag %2$s closing span tag */
					$order_type = sprintf( _x( '%1$sPOS%2$s', 'POS Order Icon', 'devdiggers-multipos-for-woocommerce' ), '<span class="order-type-pos tips" data-tip="', '"><span>');
				} else {
					/* translators: %1$s opening span tag %2$s closing span tag */
					$order_type = sprintf( _x( '%1$sWebsite%2$s', 'Website Order Icon', 'devdiggers-multipos-for-woocommerce' ), '<span class="order-type-checkout tips" data-tip="', '"><span>');
				}

				echo wp_kses_post( $order_type );
			}
		}

	}
}
