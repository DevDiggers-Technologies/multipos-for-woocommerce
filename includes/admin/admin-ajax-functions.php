<?php
/**
 * This file handles all admin end ajax action callbacks.
 *
 * @author DevDiggers
 * @version 1.0.0
 * @package MultiPOS - Point of Sale for WooCommerce
 */

namespace DDWCMultiPOS\Includes\Admin;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Admin_Ajax_Functions' ) ) {
	/**
	 * Admin Functions Class
	 */
	class DDWCPOS_Admin_Ajax_Functions {
		/**
		 * Configuration Variable
		 *
		 * @var array
		 */
		protected $ddwcpos_configuration;

		/**
		 * Class constructor
		 *
		 * @param array $ddwcpos_configuration
		 */
		public function __construct( $ddwcpos_configuration ) {
			$this->ddwcpos_configuration = $ddwcpos_configuration;
		}

		/**
		 * Output barcode image.
		 *
		 * @return void
		 */
		public function ddwcpos_barcode_image() {
			if ( ! check_ajax_referer( 'ddwcpos_barcode_image', 'nonce', false ) ) {
				wp_die( esc_html__( 'Security check failed!', 'devdiggers-multipos-for-woocommerce' ) );
			}

			if ( ! $this->ddwcpos_current_user_can_manage_pos() ) {
				wp_die( esc_html__( 'Insufficient permissions.', 'devdiggers-multipos-for-woocommerce' ) );
			}

			$code        = ! empty( $_GET['code'] ) ? preg_replace( '/[^0-9A-Za-z]/', '', sanitize_text_field( wp_unslash( $_GET['code'] ) ) ) : '';
			$orientation = ! empty( $_GET['orientation'] ) ? sanitize_key( wp_unslash( $_GET['orientation'] ) ) : 'horizontal';
			$size        = ! empty( $_GET['size'] ) ? max( 1, absint( wp_unslash( $_GET['size'] ) ) ) : 20;

			if ( empty( $code ) || ! in_array( $orientation, [ 'horizontal', 'vertical' ], true ) ) {
				wp_die( esc_html__( 'Invalid barcode data.', 'devdiggers-multipos-for-woocommerce' ) );
			}

			$this->ddwcpos_output_barcode_image( $code, $orientation, $size );
		}

		/**
		 * Generate and print barcode PNG.
		 *
		 * @param string $code_string Barcode code string.
		 * @param string $orientation Barcode orientation.
		 * @param int    $size Barcode size.
		 * @return void
		 */
		protected function ddwcpos_output_barcode_image( $code_string, $orientation = 'horizontal', $size = 20 ) {
			$print         = false;
			$size_factor   = 1;
			$barcode_width = '';
			$text_height   = $print ? 30 : 0;
			$code_length   = 20;

			for ( $i = 1; $i <= strlen( $code_string ); ++$i ) {
				$code_length += intval( substr( $code_string, $i - 1, 1 ) );
			}

			if ( 'horizontal' === strtolower( $orientation ) ) {
				$barcode_width = empty( $barcode_width ) ? $code_length * $size_factor : $barcode_width;
				$img_width     = $barcode_width;
				$img_height    = $size;
			} else {
				$barcode_width = empty( $barcode_width ) ? $size : $barcode_width;
				$img_width     = $barcode_width;
				$img_height    = $code_length * $size_factor;
			}

			$image = imagecreate( $img_width, $img_height + $text_height );

			if ( ! $image ) {
				wp_die( esc_html__( 'Could not generate barcode image.', 'devdiggers-multipos-for-woocommerce' ) );
			}

			$black = imagecolorallocate( $image, 0, 0, 0 );
			$white = imagecolorallocate( $image, 255, 255, 255 );

			imagefill( $image, 0, 0, $white );

			$location = 10;
			for ( $position = 1; $position <= strlen( $code_string ); ++$position ) {
				$cur_size = $location + intval( substr( $code_string, $position - 1, 1 ) );

				if ( 'horizontal' === strtolower( $orientation ) ) {
					imagefilledrectangle( $image, $location * $size_factor, 0, $cur_size * $size_factor, $img_height, ( 0 === $position % 2 ? $white : $black ) );
				} else {
					imagefilledrectangle( $image, 0, $location * $size_factor, $img_width, $cur_size * $size_factor, ( 0 === $position % 2 ? $white : $black ) );
				}

				$location = $cur_size;
			}

			nocache_headers();
			header( 'Content-Type: image/png' );
			imagepng( $image );
			imagedestroy( $image );
			exit;
		}

		/**
		 * Check whether the current admin user can manage POS data.
		 *
		 * @return bool
		 */
		protected function ddwcpos_current_user_can_manage_pos() {
			return current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_options' );
		}
	}
}
