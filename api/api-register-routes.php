<?php
/**
 * API Register Route class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\API;

use DDWCMultiPOS\API\Includes;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_API_Register_Routes' ) ) {
	/**
	 * Register API Routes Class.
	 * 
	 * Handles registration of all REST API routes for the MultiPOS system.
	 * Provides organized route management with proper validation and error handling.
	 */
	class DDWCPOS_API_Register_Routes {
		/**
		 * Configuration Variable
		 *
		 * @var array
		 */
		protected $ddwcpos_configuration;

		/**
		 * API Namespace
		 *
		 * @var string
		 */
		const API_NAMESPACE = 'ddwcpos/v1';

		/**
		 * Constructor.
		 * 
		 * @param array $ddwcpos_configuration Plugin configuration array.
		 */
		public function __construct( $ddwcpos_configuration ) {
			$this->ddwcpos_configuration = $ddwcpos_configuration;

			$this->ddwcpos_init_api();
		}

		/**
		 * Initialize API functionality.
		 * 
		 * Sets up WooCommerce frontend components and registers API routes.
		 *
		 * @return void
		 */
		public function ddwcpos_init_api() {
			// Initialize WooCommerce frontend for logged-in users
			if ( is_user_logged_in() ) {
				$this->ddwcpos_init_woocommerce_frontend();
				add_action( 'rest_api_init', [ $this, 'ddwcpos_register_api_routes' ] );
			}
		}

		/**
		 * Initialize WooCommerce frontend components.
		 * 
		 * Ensures WooCommerce customer, cart, and session are properly initialized.
		 *
		 * @return void
		 */
		protected function ddwcpos_init_woocommerce_frontend() {
			if ( is_admin() ) {
				return;
			}

			WC()->frontend_includes();

			// Initialize customer if not already set
			if ( empty( WC()->customer ) ) {
				WC()->customer = new \WC_Customer( get_current_user_ID() );
			}

			// Initialize cart if not already set
			if ( empty( WC()->cart ) ) {
				WC()->cart = new \WC_Cart();
			}

			// Initialize session if not already set
			if ( empty( WC()->session ) ) {
				WC()->session = new \WC_Session_Handler();
				WC()->session->init();
			}
		}

		/**
		 * Register main API routes.
		 * 
		 * Registers all POS API routes including products, customers, and orders.
		 *
		 * @return void
		 */
		public function ddwcpos_register_api_routes() {
			do_action( 'ddwcpos_before_register_pos_rest_routes' );

			if ( ! is_admin() ) {
				$this->ddwcpos_register_pos_routes();
			}

			do_action( 'ddwcpos_after_register_pos_rest_routes' );
		}

		/**
		 * Register POS-specific routes.
		 * 
		 * Registers all POS functionality routes (products, customers, orders, etc.).
		 *
		 * @return void
		 */
		protected function ddwcpos_register_pos_routes() {
			$pos_routes = $this->ddwcpos_get_pos_routes();

			foreach ( $pos_routes as $route_key => $route_instance ) {
				$this->ddwcpos_register_single_route( $route_instance, 'POST' );
			}
		}

		/**
		 * Register a single API route.
		 * 
		 * @param object $route_instance Route instance object.
		 * @param string $method HTTP method.
		 * @return void
		 */
		protected function ddwcpos_register_single_route( $route_instance, $method ) {
			register_rest_route(
				self::API_NAMESPACE,
				$route_instance->base,
				[
					'methods'             => $method,
					'permission_callback' => [ $this, 'ddwcpos_permission_check' ],
					'callback'            => [ $route_instance, 'ddwcpos_get_data' ],
				]
			);
		}

		/**
		 * Check whether the current user can access POS REST routes.
		 *
		 * @return bool
		 */
		public function ddwcpos_permission_check( $request = null ) {
			if ( ! is_user_logged_in() ) {
				return false;
			}

			$user  = wp_get_current_user();
			$roles = ! empty( $user->roles ) ? (array) $user->roles : [];

			return current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_options' ) || in_array( 'ddwcpos_cashier', $roles, true );
		}

		/**
		 * Get POS routes configuration.
		 * 
		 * @return array Array of POS route instances.
		 */
		protected function ddwcpos_get_pos_routes() {
			return [
				'get-products'            => new Includes\Products\DDWCPOS_API_Get_Products( $this->ddwcpos_configuration ),
				'get-product-categories'  => new Includes\Products\DDWCPOS_API_GET_Product_Categories( $this->ddwcpos_configuration ),
				'get-customers'           => new Includes\Customers\DDWCPOS_API_Get_Customers( $this->ddwcpos_configuration ),
				'get-countries-states'    => new Includes\Misc\DDWCPOS_API_Get_Countries_States( $this->ddwcpos_configuration ),
				'check-coupon'            => new Includes\Misc\DDWCPOS_API_Coupon_Check( $this->ddwcpos_configuration ),
				'check-centralized-stock' => new Includes\Misc\DDWCPOS_API_Check_Centralized_Stock( $this->ddwcpos_configuration ),
				'manage-customer'         => new Includes\Customers\DDWCPOS_API_Manage_Customer( $this->ddwcpos_configuration ),
				'delete-customer'         => new Includes\Customers\DDWCPOS_API_Delete_Customer( $this->ddwcpos_configuration ),
				'create-order'            => new Includes\Orders\DDWCPOS_API_Create_Order( $this->ddwcpos_configuration ),
				'get-orders'              => new Includes\Orders\DDWCPOS_API_Get_Orders( $this->ddwcpos_configuration ),
				'save-cashier'            => new Includes\Misc\DDWCPOS_API_Save_Cashier( $this->ddwcpos_configuration ),
			];
		}
	}
}
