<?php
/**
 * This file handles all admin end action hooks.
 *
 * @author DevDiggers
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Includes\Admin;

use Automattic\WooCommerce\Utilities\OrderUtil;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Admin_Hooks' ) ) {
	/**
	 * Admin end hook handler class
	 */
	class DDWCPOS_Admin_Hooks extends DDWCPOS_Admin_Functions {
		/**
		 * Construct
		 */
		public function __construct( $ddwcpos_configuration ) {
			parent::__construct( $ddwcpos_configuration );

			add_action( 'admin_init', [ $this, 'ddwcpos_register_settings' ] );

			add_action( 'user_new_form', [ $this, 'ddwcpos_add_user_form_fields' ] );

			add_filter( 'user_profile_update_errors', [ $this, 'ddwcpos_validate_user_fields' ] );

			add_action( 'user_register', [ $this, 'ddwcpos_save_user_custom_data' ] );

			add_action( 'show_user_profile', [ $this, 'ddwcpos_display_custom_user_profile_fields' ] );

			add_action( 'edit_user_profile', [ $this, 'ddwcpos_display_custom_user_profile_fields' ] );

			add_action( 'personal_options_update', [ $this, 'ddwcpos_save_custom_user_profile_fields' ] );

			add_action( 'edit_user_profile_update', [ $this, 'ddwcpos_save_custom_user_profile_fields' ] );

			add_action( 'admin_bar_menu', [ $this, 'ddwcpos_admin_bar_menu' ] );

			$order_screen_id = OrderUtil::custom_orders_table_usage_is_enabled() ? 'woocommerce_page_wc-orders' : 'shop_order';

			add_filter( "manage_{$order_screen_id}_columns", [ $this, 'ddwcpos_add_custom_woocommerce_orders_column' ] );

			add_filter( "manage_edit-{$order_screen_id}_columns", [ $this, 'ddwcpos_add_custom_woocommerce_orders_column' ] );

			add_action( "manage_{$order_screen_id}_custom_column", [ $this, 'ddwcpos_add_custom_woocommerce_orders_column_content' ], 10, 2 );

			add_action( "manage_{$order_screen_id}_posts_custom_column", [ $this, 'ddwcpos_add_custom_woocommerce_orders_column_content' ], 10, 2 );
		}
	}
}
