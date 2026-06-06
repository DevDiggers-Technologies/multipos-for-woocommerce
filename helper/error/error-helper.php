<?php
/**
 * Error Helper trait
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 */

namespace DDWCMultiPOS\Helper\Error;

defined( 'ABSPATH' ) || exit();

if ( ! trait_exists( 'DDWCPOS_Error_Helper' ) ) {
	/**
	 *Error Helper trait
	*/
	trait DDWCPOS_Error_Helper {
		/**
		 * Print Notification function
		 *
		 * @param string $message
		 * @param string $error_type
		 * @return void
		 */
		public function ddwcpos_print_notification( $message, $type = 'success', $dismissible = true ) {
			if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
				?>
				<div class="notice notice-<?php echo esc_attr( $type . ' ' . ( $dismissible ? 'is-dismissible' : '' ) ); ?>">
					<p><?php echo wp_kses_post( $message ); ?></p>
				</div>
				<?php
			} else {
				wc_print_notice( $message, $type );
			}
		}
	}
}
