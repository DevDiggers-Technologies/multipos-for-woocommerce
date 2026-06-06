<?php
/**
 * PWA Configuration template class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Templates\Admin\Configuration;

use DevDiggers\Framework\Includes\DDFW_Layout;

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
			$icons_path = DDWCPOS_PLUGIN_URL . 'assets/images/';

			$locked_attributes = [
				'disabled' => 'disabled',
			];

			$args = [
				[
					'class'  => 'ddfw-upgrade-to-pro-tag-wrapper',
					'header' => [
						'heading'     => esc_html__( 'App Details', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Set app name and details used when POS is installed as PWA.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'              => 'text',
							'label'             => esc_html__( 'Full Application Name', 'devdiggers-multipos-for-woocommerce' ),
							'description'       => esc_html__( 'Shown on splash screen and install prompts.', 'devdiggers-multipos-for-woocommerce' ),
							'id'                => 'ddwcpos-pwa-name',
							'value'             => $ddwcpos_configuration['pwa_name'],
							'field_class'       => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => $locked_attributes,
						],
						[
							'type'              => 'text',
							'label'             => esc_html__( 'Interface Short Name', 'devdiggers-multipos-for-woocommerce' ),
							'description'       => esc_html__( 'Short name shown under app icon.', 'devdiggers-multipos-for-woocommerce' ),
							'id'                => 'ddwcpos-pwa-short-name',
							'value'             => $ddwcpos_configuration['pwa_short_name'],
							'field_class'       => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => $locked_attributes,
						],
						[
							'type'              => 'text',
							'label'             => esc_html__( 'App Description', 'devdiggers-multipos-for-woocommerce' ),
							'description'       => esc_html__( 'Short description of your POS app.', 'devdiggers-multipos-for-woocommerce' ),
							'id'                => 'ddwcpos-pwa-description',
							'value'             => $ddwcpos_configuration['pwa_description'],
							'field_class'       => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => $locked_attributes,
						],
					],
				],
				[
					'class'  => 'ddfw-upgrade-to-pro-tag-wrapper',
					'header' => [
						'heading'     => esc_html__( 'App Colors', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Choose colors used by device when PWA opens.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'              => 'colorpicker',
							'label'             => esc_html__( 'OS Theme Color', 'devdiggers-multipos-for-woocommerce' ),
							'description'       => esc_html__( 'Color used for browser and device bars.', 'devdiggers-multipos-for-woocommerce' ),
							'id'                => 'ddwcpos-pwa-theme-color',
							'value'             => $ddwcpos_configuration['pwa_theme_color'],
							'field_class'       => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => $locked_attributes,
						],
						[
							'type'              => 'colorpicker',
							'label'             => esc_html__( 'Splash Background', 'devdiggers-multipos-for-woocommerce' ),
							'description'       => esc_html__( 'Background color for splash screen.', 'devdiggers-multipos-for-woocommerce' ),
							'id'                => 'ddwcpos-pwa-background-color',
							'value'             => $ddwcpos_configuration['pwa_background_color'],
							'field_class'       => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => $locked_attributes,
						],
					],
				],
				[
					'class'  => 'ddfw-upgrade-to-pro-tag-wrapper',
					'header' => [
						'heading'     => esc_html__( 'App Icons', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Upload app icons for different screen sizes.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'          => 'image',
							'label'         => esc_html__( 'Standard UI Asset (48x48)', 'devdiggers-multipos-for-woocommerce' ),
							'description'   => esc_html__( 'Small icon for simple UI use.', 'devdiggers-multipos-for-woocommerce' ),
							'id'            => 'ddwcpos-pwa-icon48',
							'default_image' => $icons_path . 'pwa48.png',
							'field_class'   => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
						],
						[
							'type'          => 'image',
							'label'         => esc_html__( 'Desktop Asset (96x96)', 'devdiggers-multipos-for-woocommerce' ),
							'description'   => esc_html__( 'Used in browser tabs and desktop shortcuts.', 'devdiggers-multipos-for-woocommerce' ),
							'id'            => 'ddwcpos-pwa-icon96',
							'default_image' => $icons_path . 'pwa96.png',
							'field_class'   => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
						],
						[
							'type'          => 'image',
							'label'         => esc_html__( 'High-Density Asset (144x144)', 'devdiggers-multipos-for-woocommerce' ),
							'description'   => esc_html__( 'Used on sharper mobile and laptop screens.', 'devdiggers-multipos-for-woocommerce' ),
							'id'            => 'ddwcpos-pwa-icon144',
							'default_image' => $icons_path . 'pwa144.png',
							'field_class'   => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
						],
						[
							'type'          => 'image',
							'label'         => esc_html__( 'Enhanced Resolution Asset (196x196)', 'devdiggers-multipos-for-woocommerce' ),
							'description'   => esc_html__( 'Used where larger app icon is needed.', 'devdiggers-multipos-for-woocommerce' ),
							'id'            => 'ddwcpos-pwa-icon196',
							'default_image' => $icons_path . 'pwa196.png',
							'field_class'   => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
						],
						[
							'type'          => 'image',
							'label'         => esc_html__( 'Master Branding Asset (512x512)', 'devdiggers-multipos-for-woocommerce' ),
							'description'   => esc_html__( 'Largest app icon used for splash and install screens.', 'devdiggers-multipos-for-woocommerce' ),
							'id'            => 'ddwcpos-pwa-icon512',
							'default_image' => $icons_path . 'pwa512.png',
							'field_class'   => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
						],
					],
				],
			];

			$layout = new DDFW_Layout();
			$layout->get_form_section_layout( $args );
		}
	}
}
