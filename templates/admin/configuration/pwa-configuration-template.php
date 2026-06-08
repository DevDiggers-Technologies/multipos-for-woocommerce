<?php
/**
 * PWA Configuration template class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Templates\Admin\Configuration;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_PWA_Configuration_Template' ) ) {
	/**
	 * PWA Configuration template class
	 */
	class DDWCPOS_PWA_Configuration_Template {
		/**
		 * Construct
		 *
		 * @param array $ddwcpos_configuration
		 */
		public function __construct( $ddwcpos_configuration ) {
			ddfw_upgrade_to_pro_section(
				[
					'image_url'     => DDWCPOS_PLUGIN_URL . 'assets/images/pro-pages/pwa.webp',
					'heading'       => esc_html__( 'Installable POS App (PWA)', 'devdiggers-multipos-for-woocommerce' ),
					'description'   => esc_html__( 'Turn the POS into an installable Progressive Web App with its own name, colors, and icons. Available in the Pro version.', 'devdiggers-multipos-for-woocommerce' ),
					'list_features' => [
						esc_html__( 'Custom app name, short name, and description', 'devdiggers-multipos-for-woocommerce' ),
						esc_html__( 'Branded theme and splash background colors', 'devdiggers-multipos-for-woocommerce' ),
						esc_html__( 'Upload app icons for every screen size', 'devdiggers-multipos-for-woocommerce' ),
					],
					'upgrade_url'   => '//devdiggers.com/product/multipos-point-of-sale-for-woocommerce/',
				]
			);
		}
	}
}
