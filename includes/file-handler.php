<?php
/**
 * File handler
 *
 * @author DevDiggers
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Includes;

use DDWCMultiPOS\Includes\Admin;
use DDWCMultiPOS\Includes\Front;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_File_Handler' ) ) {
	/**
	 * File handler class
	 */
	class DDWCPOS_File_Handler {

		/**
		 * Construct
		 */
		public function __construct() {
			global $ddwcpos_configuration;

			add_rewrite_endpoint( $ddwcpos_configuration['endpoint'], EP_ROOT | EP_PAGES );

			new Common\DDWCPOS_Common_Hooks( $ddwcpos_configuration );

			if ( is_admin() ) {
				new DDWCPOS_Admin_Dashboard( $ddwcpos_configuration );
				new Admin\DDWCPOS_Admin_Hooks( $ddwcpos_configuration );
				new Admin\DDWCPOS_Admin_Ajax_Hooks( $ddwcpos_configuration );
				new Admin\DDWCPOS_Setup_Wizard();
			} elseif ( ! empty( $ddwcpos_configuration['enabled'] ) ) {
				new Front\DDWCPOS_Front_Hooks( $ddwcpos_configuration );
			}
		}
	}
}
