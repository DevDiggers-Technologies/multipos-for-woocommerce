<?php
/**
 * General Configuration template class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Templates\Admin\Configuration;

use DevDiggers\Framework\Includes\DDFW_Layout;
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_General_Configuration_Template' ) ) {
	/**
	 * General Configuration template class
	 */
	class DDWCPOS_General_Configuration_Template {
		/**
		 * Construct
		 * 
		 * @param array $ddwcpos_configuration
		 */
		public function __construct( $ddwcpos_configuration ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Core settings-updated flag is read-only.
			if ( ! empty( $_GET['settings-updated'] ) && 'true' === sanitize_text_field( wp_unslash( $_GET['settings-updated'] ) ) ) {
				flush_rewrite_rules();
			}

			$args = [
				[
					'header' => [
						'heading'     => esc_html__( 'General', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Turn POS features on, choose how stock works, and set core behavior for your registers.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'           => 'checkbox',
							'label'          => esc_html__( 'Plugin Status', 'devdiggers-multipos-for-woocommerce' ),
							'checkbox_label' => esc_html__( 'Enable Point of Sale System', 'devdiggers-multipos-for-woocommerce' ),
							'description'    => esc_html__( 'Turn POS on or off for this site.', 'devdiggers-multipos-for-woocommerce' ),
							'id'             => 'ddwcpos-enabled',
							'value'          => $ddwcpos_configuration['enabled'],
						],
						[
							'type'        => 'select',
							'label'       => esc_html__( 'Initial Order Status', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Choose default status for new POS orders.', 'devdiggers-multipos-for-woocommerce' ),
							'options'     => wc_get_order_statuses(),
							'id'          => 'ddwcpos-order-status',
							'value'       => $ddwcpos_configuration['order_status'],
							'input_class' => [ 'ddwcpos-select2' ],
						],
						[
							'type'        => 'select',
							'label'       => esc_html__( 'Barcode Synchronization', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Choose whether barcodes use product ID or SKU.', 'devdiggers-multipos-for-woocommerce' ),
							'options'     => [
								'id'  => esc_html__( 'Use Product ID', 'devdiggers-multipos-for-woocommerce' ),
								'sku' => esc_html__( 'Use Product SKU', 'devdiggers-multipos-for-woocommerce' ),
							],
							'id'          => 'ddwcpos-default-barcode',
							'value'       => $ddwcpos_configuration['default_barcode'],
							'input_class' => [ 'ddwcpos-select2' ],
						],
					],
				],
				[
					'header' => [
						'heading'     => esc_html__( 'Checkout Features', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Control checkout options your cashiers can use in POS.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'           => 'checkbox',
							'label'          => esc_html__( 'Order Notifications', 'devdiggers-multipos-for-woocommerce' ),
							'checkbox_label' => esc_html__( 'Enable Automated Order Mails', 'devdiggers-multipos-for-woocommerce' ),
							'description'    => esc_html__( 'Send WooCommerce order emails after POS checkout.', 'devdiggers-multipos-for-woocommerce' ),
							'id'             => 'ddwcpos-order-mails-enabled',
							'value'          => $ddwcpos_configuration['order_mails_enabled'],
						],
					],
				],
				[
					'header' => [
						'heading'     => esc_html__( 'Branding', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Set logo used across POS admin and receipts.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'          => 'image',
							'label'         => esc_html__( 'POS Terminal Branding Logo', 'devdiggers-multipos-for-woocommerce' ),
							'description'   => esc_html__( 'Shown on receipts and POS admin screens. Recommended size: 200x60 pixels.', 'devdiggers-multipos-for-woocommerce' ),
							'id'            => 'ddwcpos-logo',
							'value'         => $ddwcpos_configuration['logo'],
							'default_image' => DDWCPOS_PLUGIN_URL . 'assets/images/logo.png',
						],
					],
				],
				[
					'header' => [
						'heading'     => esc_html__( 'Customers', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Choose how POS handles guest and saved customers.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'              => 'users',
							'label'             => esc_html__( 'Default Guest Account', 'devdiggers-multipos-for-woocommerce' ),
							'description'       => esc_html__( 'Choose user account used for guest orders.', 'devdiggers-multipos-for-woocommerce' ),
							'id'                => 'ddwcpos-default-customer',
							'value'             => $ddwcpos_configuration['default_customer'],
							'custom_attributes' => [
								'data-placeholder' => esc_attr__( 'Search for a customer...', 'devdiggers-multipos-for-woocommerce' ),
								'data-role'        => 'customer',
							],
						],
					],
				],
				[
					'header' => [
						'heading'     => esc_html__( 'Advanced', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Set the POS terminal URL path.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'        => 'text',
							'label'       => esc_html__( 'POS Terminal URL Path', 'devdiggers-multipos-for-woocommerce' ),
							'description'    => esc_html__( 'Choose URL path for POS screen, like /pos.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-endpoint',
							'value'       => $ddwcpos_configuration['endpoint'],
						],
					],
				],
			];

			$layout = new DDFW_Layout();
			$layout->get_form_section_layout( $args, 'ddwcpos-general-configuration-fields' );
		}
	}
}
