<?php
/**
 * @author DevDiggers
 * @version 1.0.0
 * This file handles all admin end ajax action hooks.
 */

namespace DDWCMultiPOS\Includes\Admin;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Admin_Ajax_Hooks' ) ) {
    /**
     * Admin end hook handler class
     */
    class DDWCPOS_Admin_Ajax_Hooks extends DDWCPOS_Admin_Ajax_Functions {
        /**
         * Construct
         */
		public function __construct( $ddwcpos_configuration ) {
			parent::__construct( $ddwcpos_configuration );

			add_action( 'wp_ajax_ddwcpos_barcode_image', [ $this, 'ddwcpos_barcode_image' ] );
		}
	}
}
