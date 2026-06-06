<?php
/**
 * Common hooks class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Includes\Common;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Common_Hooks' ) ) {
	/**
	 * Common hooks class
	 */
	class DDWCPOS_Common_Hooks extends DDWCPOS_Common_Functions {
		/**
		 * Construct
		 */
		public function __construct( $ddwcpos_configuration ) {
			parent::__construct( $ddwcpos_configuration );

			add_filter( 'ddfw_modify_svg_icons', [ $this, 'ddwcpos_add_svg_icons' ], 10, 2 );
		}
	}
}
