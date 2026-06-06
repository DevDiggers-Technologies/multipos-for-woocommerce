<?php
/**
 * This file handles all front end action hooks.
 *
 * @author DevDiggers
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Includes\Front;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Front_Hooks' ) ) {
	/**
	 * Front end hooks class
	 */
	class DDWCPOS_Front_Hooks extends DDWCPOS_Front_Functions {
		/**
		 * Construct
		 */
		public function __construct( $ddwcpos_configuration ) {
			parent::__construct( $ddwcpos_configuration );

			add_filter( 'query_vars', [ $this, 'ddwcpos_add_query_vars' ] );
			add_action( 'parse_request', [ $this, 'ddwcpos_parse_request' ] );
			add_filter( 'wp_loaded',  [ $this, 'ddwcpos_wp_loaded' ] );
			add_action( 'wp_login_failed', [ $this, 'ddwcpos_login_failed' ], 10, 2 );
			add_action( 'authenticate', [ $this, 'ddwcpos_login_failed' ], 10, 2 );
			add_action( 'woocommerce_checkout_create_order', [ $this, 'ddwcpos_stop_mails_at_pos_end' ], 1, 1 );
			add_action( 'wp_enqueue_scripts', [ $this, 'ddwcpos_front_scripts' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'ddwcpos_deregister_front_scripts' ], 99999 );
		}
	}
}
