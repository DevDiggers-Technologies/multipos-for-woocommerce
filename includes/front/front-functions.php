<?php
/**
 * @author DevDiggers
 * @version 1.0.0
 * This file handles all front end action callbacks.
 */

namespace DDWCMultiPOS\Includes\Front;

use DDWCMultiPOS\Templates\Front\POS\DDWCPOS_Layout;
use DDWCMultiPOS\Helper\Outlet\DDWCPOS_Outlet_Helper;
use DDWCMultiPOS\Helper\Invoice\DDWCPOS_Invoice_Helper;


defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Front_Functions' ) ) {    
    /**
     * Front functions class
     */
    class DDWCPOS_Front_Functions {
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
		 * Add Query Vars function
		 *
		 * @param array $vars
		 * @return array
		 */
		public function ddwcpos_add_query_vars( $vars ) {
			$vars[] = $this->ddwcpos_configuration[ 'endpoint' ];
            return $vars;
		}

        /**
         * Parse request function
         *
         * @return void
         */
        public function ddwcpos_parse_request( $wp ) {
            if ( array_key_exists( $this->ddwcpos_configuration[ 'endpoint' ], $wp->query_vars ) ) {
                new DDWCPOS_Layout( $this->ddwcpos_configuration );
                exit();
            }
        }

        /**
         * WP loaded function
         *
         * @return void
         */
        public function ddwcpos_wp_loaded() {
				$request_uri = ! empty( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	            if ( is_user_logged_in() && preg_match( "/\b\/{$this->ddwcpos_configuration[ 'endpoint' ]}\b/", $request_uri ) ) {
                $user = wp_get_current_user();
                if ( ! ( in_array( 'ddwcpos_cashier', $user->roles, true ) || in_array( 'administrator', $user->roles, true ) || in_array( 'shop_manager', $user->roles, true ) || apply_filters( 'ddwcpos_allow_administrator_access_for_pos_to_user', false ) ) ) {
                    wp_safe_redirect( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) );
                    exit();
                }
            }
        }

		/**
		 * Login failed function
		 *
		 * @param object $user
		 * @return void
		 */
		public function ddwcpos_login_failed( $user ) {
			// check what page the login attempt is coming from
				$referrer = ! empty( $_SERVER[ 'HTTP_REFERER' ] ) ? esc_url_raw( wp_unslash( $_SERVER[ 'HTTP_REFERER' ] ) ) : '';

			$error = false;

				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- WordPress login form handles authentication nonce/session flow.
				if ( empty( $_POST[ 'log' ] ) || empty( $_POST[ 'pwd' ] ) ) {
				$error = true;
			}

			if ( strstr( $referrer, '/' . $this->ddwcpos_configuration[ 'endpoint' ] ) !== false && ( $user !== null || $error ) ) {
				$url = site_url( $this->ddwcpos_configuration[ 'endpoint' ] );
				// make sure we don't already have a failed login attempt
				if ( ! strstr( $referrer, '?login=failed' ) ) {
					// Redirect to the login page and append a querystring of login failed
						wp_safe_redirect( $url . '?login=failed' );
				} else {
						wp_safe_redirect( $url );
				}

				exit();
			}
		}

		/**
		 * Stop mails at pos end function
		 *
		 * @param object $order
		 * @return void
		 */
		public function ddwcpos_stop_mails_at_pos_end( $order ) {
			if ( empty( $this->ddwcpos_configuration[ 'order_mails_enabled' ] ) && ! empty( $order->get_meta( '_ddwcpos_outlet_id', true ) ) ) {
				remove_action( 'woocommerce_order_status_pending_to_processing_notification', [ WC()->mailer()->emails[ 'WC_Email_New_Order' ], 'trigger' ] );
				remove_action( 'woocommerce_order_status_pending_to_completed_notification', [ WC()->mailer()->emails[ 'WC_Email_New_Order' ], 'trigger' ] );
				remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', [ WC()->mailer()->emails[ 'WC_Email_New_Order' ], 'trigger' ] );
				remove_action( 'woocommerce_order_status_failed_to_processing_notification', [ WC()->mailer()->emails[ 'WC_Email_New_Order' ], 'trigger' ] );
				remove_action( 'woocommerce_order_status_failed_to_completed_notification', [ WC()->mailer()->emails[ 'WC_Email_New_Order' ], 'trigger' ] );
				remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', [ WC()->mailer()->emails[ 'WC_Email_New_Order' ], 'trigger' ] );
				remove_action( 'woocommerce_order_status_cancelled_to_processing_notification', [ WC()->mailer()->emails[ 'WC_Email_New_Order' ], 'trigger' ] );
				remove_action( 'woocommerce_order_status_cancelled_to_completed_notification', [ WC()->mailer()->emails[ 'WC_Email_New_Order' ], 'trigger' ] );
				remove_action( 'woocommerce_order_status_cancelled_to_on-hold_notification', [ WC()->mailer()->emails[ 'WC_Email_New_Order' ], 'trigger' ] );

				remove_action( 'woocommerce_order_status_pending_to_processing_notification', [ WC()->mailer()->emails[ 'WC_Email_Customer_Processing_Order' ], 'trigger' ] );
				remove_action( 'woocommerce_order_status_completed_notification', [ WC()->mailer()->emails[ 'WC_Email_Customer_Completed_Order' ], 'trigger' ] );
			}
		}

		/**
		 * Front scripts enqueue function
		 *
		 * @return void
		 */
		public function ddwcpos_front_scripts() {
			global $wp;
			if ( array_key_exists( $this->ddwcpos_configuration[ 'endpoint' ], $wp->query_vars ) ) {
				wp_enqueue_style( 'ddwcpos-login-style', DDWCPOS_PLUGIN_URL . 'assets/css/login.css', [], filemtime( DDWCPOS_PLUGIN_FILE . 'assets/css/login.css' ) );
				wp_add_inline_style( 'ddwcpos-login-style', $this->ddwcpos_get_login_css_variables() );

				wp_enqueue_script( 'ddwcpos-login-script', DDWCPOS_PLUGIN_URL . 'assets/js/login.js', [], filemtime( DDWCPOS_PLUGIN_FILE . 'assets/js/login.js' ) );

				wp_localize_script(
					'ddwcpos-login-script',
					'ddwcposLoginObj',
					[
						'siteUrl'        => esc_url( site_url() ),
						'networkSiteUrl' => esc_url( network_site_url() ),
					]
				);

				if ( is_user_logged_in() ) {
					$dependencies = apply_filters( 'ddwcpos_modify_pos_script_dependencies', [ 'wp-element' ] );

					wp_enqueue_style( 'ddwcpos-pos-style', DDWCPOS_PLUGIN_URL . 'assets/css/pos.css', [], filemtime( DDWCPOS_PLUGIN_FILE . 'assets/css/pos.css' ) );
					wp_add_inline_style( 'ddwcpos-pos-style', $this->ddwcpos_get_layout_css_variables() );
					wp_enqueue_script( 'ddwcpos-pos-script', DDWCPOS_PLUGIN_URL . 'assets/js/pos.js', $dependencies, filemtime( DDWCPOS_PLUGIN_FILE . 'assets/js/pos.js' ) );

					wp_set_script_translations( 'ddwcpos-pos-script', 'devdiggers-multipos-for-woocommerce' );

					$ddwcpos_configuration = $this->ddwcpos_configuration;
					$user                  = wp_get_current_user();
					$tax_display_cart      = get_option( 'woocommerce_tax_display_cart' );
					$outlet_helper         = new DDWCPOS_Outlet_Helper();
					$invoice_helper        = new DDWCPOS_Invoice_Helper( $this->ddwcpos_configuration );
					$site_url              = site_url();

					$user_data = (array) $user->data;

					$user_data[ 'roles' ]      = $user->roles;
					$user_data[ 'first_name' ] = $user->first_name;
					$user_data[ 'last_name' ]  = $user->last_name;

					if ( in_array( 'ddwcpos_cashier', $user->roles, true ) ) {
						$assigned_outlets = get_user_meta( $user->ID, '_ddwcpos_assigned_outlets', true );
						$assigned_outlets = $outlet_helper->ddwcpos_get_outlet_details_by_ids( ! empty( $assigned_outlets ) ? $assigned_outlets : [] );
					} else if ( in_array( 'administrator', $user->roles, true ) || in_array( 'shop_manager', $user->roles, true ) || apply_filters( 'ddwcpos_allow_administrator_access_for_pos_to_user', false ) ) {
						$assigned_outlets = $outlet_helper->ddwcpos_get_all_outlets( 999999, 0, '' );
					}

					$assigned_outlets = apply_filters( 'ddwcpos_modify_assigned_outlets_for_pos', $assigned_outlets, $user );

					if ( 'excl' !== $tax_display_cart ) {
						$tax_label = ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
					} else {
						$tax_label = ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
					}

					$order_status = wc_get_order_statuses();

					$ddwcpos_configuration[ 'tax_included_label' ]  = WC()->countries->inc_tax_or_vat();
					$ddwcpos_configuration[ 'tax_label' ]           = $tax_label;
					$ddwcpos_configuration[ 'current_date' ]        = current_time( 'Y-m-d' );
					$ddwcpos_configuration[ 'per_page' ]            = 200;
					$ddwcpos_configuration[ 'invoice_html' ]        = $invoice_helper->ddwcpos_get_invoice_html();
					$ddwcpos_configuration[ 'invoice_css' ]         = $invoice_helper->ddwcpos_get_invoice_css();
					$ddwcpos_configuration[ 'placeholder_image' ]   = wc_placeholder_img_src();
					$ddwcpos_configuration[ 'language_attributes' ] = get_language_attributes( 'html' );
					$ddwcpos_configuration[ 'order_status_label' ]  = $order_status[ $ddwcpos_configuration[ 'order_status' ] ];
					$ddwcpos_configuration[ 'logo_url' ]            = ! empty( $ddwcpos_configuration[ 'logo' ] ) ? wp_get_attachment_url( $ddwcpos_configuration[ 'logo' ] ) : DDWCPOS_PLUGIN_URL . 'assets/images/logo.png';

					$api = [
						'GET_PRODUCTS_ENDPOINT'            => $site_url . '/wp-json/ddwcpos/v1/get-products',
						'GET_CATEGORIES_ENDPOINT'          => $site_url . '/wp-json/ddwcpos/v1/get-product-categories',
						'GET_CUSTOMERS_ENDPOINT'           => $site_url . '/wp-json/ddwcpos/v1/get-customers',
						'GET_COUNTRIES_STATES_ENDPOINT'    => $site_url . '/wp-json/ddwcpos/v1/get-countries-states',
						'CHECK_COUPON_ENDPOINT'            => $site_url . '/wp-json/ddwcpos/v1/check-coupon',
						'CHECK_CENTRALIZED_STOCK_ENDPOINT' => $site_url . '/wp-json/ddwcpos/v1/check-centralized-stock',
						'MANAGE_CUSTOMER_ENDPOINT'         => $site_url . '/wp-json/ddwcpos/v1/manage-customer',
						'DELETE_CUSTOMER_ENDPOINT'         => $site_url . '/wp-json/ddwcpos/v1/delete-customer',
						'CREATE_ORDER_ENDPOINT'            => $site_url . '/wp-json/ddwcpos/v1/create-order',
						'GET_ORDERS_ENDPOINT'              => $site_url . '/wp-json/ddwcpos/v1/get-orders',
						'SAVE_CASHIER_ENDPOINT'            => $site_url . '/wp-json/ddwcpos/v1/save-cashier',
					];

					$parsed_url = wp_parse_url( $site_url, PHP_URL_PATH );

					wp_localize_script(
						'ddwcpos-pos-script',
						'ddwcposPOSObj',
						[
							'ajax'                         => [
								'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
								'ajaxNonce' => wp_create_nonce( 'ddwcpos-nonce' ),
							],
							'restNonce'                    => wp_create_nonce( 'wp_rest' ),
							'site_url'                     => $site_url,
							'siteUrl'                      => $parsed_url ? $parsed_url : '',
							'assignedOutlets'              => ! empty( $assigned_outlets ) ? $assigned_outlets : [],
							'ddwcpos_configuration'        => $ddwcpos_configuration,
							'user'                         => $user_data,
							'API'                          => $api,
							'currency_format_num_decimals' => esc_attr( wc_get_price_decimals() ),
							'currency_format_symbol'       => get_woocommerce_currency_symbol(),
							'currency_code'                => get_woocommerce_currency(),
							'currency_format_decimal_sep'  => esc_attr( wc_get_price_decimal_separator() ),
							'currency_format_thousand_sep' => esc_attr( wc_get_price_thousand_separator() ),
							'currency_format'              => esc_attr( str_replace( [ '%1$s', '%2$s' ], [ '%s', '%v' ], get_woocommerce_price_format() ) ),
							'logout_url'                   => wp_logout_url( site_url( $ddwcpos_configuration[ 'endpoint' ] ) ),
							'tax_enabled'                  => get_option( 'woocommerce_calc_taxes' ),
							'tax_type'                     => get_option( 'woocommerce_prices_include_tax' ),
							'price_num_decimals'           => get_option( 'woocommerce_price_num_decimals', 2 ),
							'tax_round_at_subtotal'        => get_option( 'woocommerce_tax_round_at_subtotal' ),
							'tax_display_cart'             => $tax_display_cart,
							'weight_unit'                  => get_option( 'woocommerce_weight_unit' ),
							'current_date'                 => apply_filters( 'ddwcpos_modify_current_date_for_pos_transactions', current_time( 'Y-m-d' ) ),
							'plugin_url'                   => DDWCPOS_PLUGIN_URL,
							'plugin_file'                  => DDWCPOS_PLUGIN_FILE,
						]
					);
				}
				}
			}

		/**
		 * Get layout CSS variables.
		 *
		 * @return string
		 */
		private function ddwcpos_get_layout_css_variables() {
			$configuration = $this->ddwcpos_configuration;
			$pos_font_family = $this->ddwcpos_get_pos_font_family_stack( $configuration[ 'layout_pos_font_family' ] );

			return sprintf(
				':root{--pos-layout-primary-color:%1$s;--pos-layout-secondary-color:%2$s;--pos-layout-font-color:%3$s;--pos-font-color:%4$s;--pos-secondary-color:%5$s;--pos-grey-background:%6$s;--pos-muted-background-1:%10$s;--pos-muted-background-2:%11$s;--pos-font-family:%12$s;--pos-border-color:%13$s;--pos-layout-font-size:%7$spx;--pos-layout-radius:%8$spx;--pos-layout-success-color:%9$s;}',
				esc_attr( $configuration[ 'layout_primary_color' ] ),
				esc_attr( $configuration[ 'layout_secondary_color' ] ),
				esc_attr( $configuration[ 'layout_button_font_color' ] ),
				esc_attr( $configuration[ 'layout_font_color' ] ),
				esc_attr( $configuration[ 'layout_surface_color' ] ),
				esc_attr( $configuration[ 'layout_muted_background_color_2' ] ),
				esc_attr( $configuration[ 'layout_font_size' ] ),
				esc_attr( $configuration[ 'layout_radius' ] ),
				esc_attr( $configuration[ 'layout_success_color' ] ),
				esc_attr( $configuration[ 'layout_muted_background_color_1' ] ),
				esc_attr( $configuration[ 'layout_muted_background_color_2' ] ),
				$pos_font_family,
				esc_attr( $configuration[ 'layout_border_color' ] )
			);
		}

		/**
		 * Get POS font family stack from saved option.
		 *
		 * @param string $font_family Font family option.
		 * @return string
		 */
		private function ddwcpos_get_pos_font_family_stack( $font_family ) {
			$font_family_map = [
				'open_sans'  => '"Open Sans", sans-serif',
				'poppins'    => 'Poppins, sans-serif',
				'lato'       => 'Lato, sans-serif',
				'montserrat' => 'Montserrat, sans-serif',
				'nunito'     => 'Nunito, sans-serif',
			];

			return isset( $font_family_map[ $font_family ] ) ? $font_family_map[ $font_family ] : $font_family_map['open_sans'];
		}

		/**
		 * Get login CSS variables.
		 *
		 * @return string
		 */
		private function ddwcpos_get_login_css_variables() {
			$configuration = $this->ddwcpos_configuration;

			return sprintf(
				':root{--login-mesh-1:%1$s;--login-mesh-2:%2$s;--login-canvas:%3$s;--login-card-bg:%4$s;--login-text:%5$s;}',
				esc_attr( $configuration[ 'login_bg_primary_color' ] ),
				esc_attr( $configuration[ 'login_bg_secondary_color' ] ),
				esc_attr( $configuration[ 'login_canvas_bg_color' ] ),
				esc_attr( $configuration[ 'login_card_bg_color' ] ),
				esc_attr( $configuration[ 'login_font_color' ] )
			);
		}

		/**
		 * Deregister front scripts function
		 *
		 * @return void
		 */
		public function ddwcpos_deregister_front_scripts() {
			global $wp;
			if ( array_key_exists( $this->ddwcpos_configuration[ 'endpoint' ], $wp->query_vars ) ) {
				global $wp_styles, $wp_scripts;
				$styles = apply_filters( 'ddwcpos_modify_css_handles', [ 'ddwcpos-login-style', 'ddwcpos-pos-style', 'ddwcpos-variables-style' ] );
				foreach ( $wp_styles->queue as $s ) {
					if ( ! in_array( $s, $styles, true ) && ! empty( $wp_styles->registered[ $s ] ) ) {
						wp_dequeue_style( $wp_styles->registered[$s]->handle );
						wp_deregister_style( $wp_styles->registered[$s]->handle );
					}
				}

				$scripts = apply_filters( 'ddwcpos_modify_js_handles', [ 'ddwcpos-login-script', 'ddwcpos-pos-script' ] );
				foreach ( $wp_scripts->queue as $s ) {
					if ( ! in_array( $s, $scripts, true ) && ! empty( $wp_scripts->registered[ $s ] ) ) {
						wp_dequeue_script( $wp_scripts->registered[$s]->handle );
						wp_deregister_script( $wp_scripts->registered[$s]->handle );
					}
				}
			}
		}
	}
}
