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
							'label'       => esc_html__( 'Inventory Management', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Choose "Centralized" to sync directly with WooCommerce stock, or "Custom" for manual stock levels independent of your online store.', 'devdiggers-multipos-for-woocommerce' ),
							'options'     => [
								'centralized' => esc_html__( 'Centralized (Sync with WooCommerce)', 'devdiggers-multipos-for-woocommerce' ),
							],
							'id'          => 'ddwcpos-inventory-type',
							'value'       => 'centralized',
							'input_class' => [ 'ddwcpos-select2' ],
							'field_class' => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => [
								'disabled' => 'disabled',
							],
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
							'type'           => 'checkbox',
							'label'          => esc_html__( 'Order Status Visibility', 'devdiggers-multipos-for-woocommerce' ),
							'checkbox_label' => esc_html__( 'Show Current Order Status in Orders', 'devdiggers-multipos-for-woocommerce' ),
							'description'    => esc_html__( 'Display each order status badge in the POS Orders list. Available in Pro.', 'devdiggers-multipos-for-woocommerce' ),
							'id'             => 'ddwcpos-show-order-status-enabled',
							'value'          => $ddwcpos_configuration['show_order_status_enabled'],
							'field_class'    => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => [
								'disabled' => 'disabled',
							],
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
						[
							'type'           => 'checkbox',
							'label'          => esc_html__( 'Multi-Payment Support', 'devdiggers-multipos-for-woocommerce' ),
							'checkbox_label' => esc_html__( 'Enable Split/Multiple Payments', 'devdiggers-multipos-for-woocommerce' ),
							'description'    => esc_html__( 'Let cashiers split payment across multiple methods.', 'devdiggers-multipos-for-woocommerce' ),
							'id'             => 'ddwcpos-multiple-payments-enabled',
							'field_class'    => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => [
								'disabled' => 'disabled',
							],
						],

						[
							'type'           => 'checkbox',
							'label'          => esc_html__( 'Customer & Order Notes', 'devdiggers-multipos-for-woocommerce' ),
							'checkbox_label' => esc_html__( 'Allow Order Notes', 'devdiggers-multipos-for-woocommerce' ),
							'description'    => esc_html__( 'Let cashiers add notes during checkout.', 'devdiggers-multipos-for-woocommerce' ),
							'id'             => 'ddwcpos-order-note-enabled',
							'field_class'    => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => [
								'disabled' => 'disabled',
							],
						],
						[
							'type'           => 'checkbox',
							'label'          => esc_html__( 'Offline Order Resilience', 'devdiggers-multipos-for-woocommerce' ),
							'checkbox_label' => esc_html__( 'Enable Fast Offline-Online Sync', 'devdiggers-multipos-for-woocommerce' ),
							'description'    => esc_html__( 'Let POS save orders offline and sync them when connection comes back.', 'devdiggers-multipos-for-woocommerce' ),
							'id'             => 'ddwcpos-offline-orders-enabled',
							'field_class'    => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => [
								'disabled' => 'disabled',
							],
						],

						[
							'type'           => 'checkbox',
							'label'          => esc_html__( 'Custom Product Entries', 'devdiggers-multipos-for-woocommerce' ),
							'checkbox_label' => esc_html__( 'Allow Adding Custom Products', 'devdiggers-multipos-for-woocommerce' ),
							'description'    => esc_html__( 'Let cashiers add custom items not saved in product catalog.', 'devdiggers-multipos-for-woocommerce' ),
							'id'             => 'ddwcpos-custom-product-enabled',
							'field_class'    => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => [
								'disabled' => 'disabled',
							],
						],
						[
							'type'           => 'checkbox',
							'label'          => esc_html__( 'Cash Management', 'devdiggers-multipos-for-woocommerce' ),
							'checkbox_label' => esc_html__( 'Enable Cash Drawer Popup', 'devdiggers-multipos-for-woocommerce' ),
							'description'    => esc_html__( 'Show prompt when cash drawer opens so staff can record it.', 'devdiggers-multipos-for-woocommerce' ),
							'id'             => 'ddwcpos-opencash-drawer-enabled',
							'field_class'    => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => [
								'disabled' => 'disabled',
							],
						],

						[
							'type'           => 'checkbox',
							'label'          => esc_html__( 'Calculated Unit Pricing', 'devdiggers-multipos-for-woocommerce' ),
							'checkbox_label' => esc_html__( 'Enable Weight-Based Pricing', 'devdiggers-multipos-for-woocommerce' ),
							'description'    => esc_html__( 'Update price by weight while adding weighted items.', 'devdiggers-multipos-for-woocommerce' ),
							'id'             => 'ddwcpos-unit-price-enabled',
							'field_class'    => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => [
								'disabled' => 'disabled',
							],
						],
					],
				],
				[
					'class'  => 'ddfw-upgrade-to-pro-tag-wrapper',
					'header' => [
						'heading'     => esc_html__( 'Kitchen', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Restaurant and kitchen options for food service workflows. Available in Pro.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'           => 'checkbox',
							'label'          => esc_html__( 'Automatic Kitchen Routing', 'devdiggers-multipos-for-woocommerce' ),
							'checkbox_label' => esc_html__( 'Auto-Send Held Orders to Kitchen', 'devdiggers-multipos-for-woocommerce' ),
							'description'    => esc_html__( 'Send held orders to kitchen automatically.', 'devdiggers-multipos-for-woocommerce' ),
							'id'             => 'ddwcpos-automatic-send-kitchen-order-enabled',
							'field_class'    => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => [
								'disabled'    => 'disabled',
							],
						],
						[
							'type'           => 'checkbox',
							'label'          => esc_html__( 'Workflow Defaults', 'devdiggers-multipos-for-woocommerce' ),
							'checkbox_label' => esc_html__( 'Default Print to Kitchen Checked', 'devdiggers-multipos-for-woocommerce' ),
							'description'    => esc_html__( 'Keep "Send to Kitchen" checked by default at payment.', 'devdiggers-multipos-for-woocommerce' ),
							'id'             => 'ddwcpos-order-send-kitchen-checked-enabled',
							'field_class'    => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => [
								'disabled'    => 'disabled',
							],
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
						[
							'type'           => 'checkbox',
							'label'          => esc_html__( 'Restricted Customer Logic', 'devdiggers-multipos-for-woocommerce' ),
							'checkbox_label' => esc_html__( 'Strict Guest-Only Mode', 'devdiggers-multipos-for-woocommerce' ),
							'description'    => esc_html__( 'Use guest account for every order and hide customer search.', 'devdiggers-multipos-for-woocommerce' ),
							'id'             => 'ddwcpos-load-only-guest-customer-enabled',
							'field_class'    => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => [
								'disabled' => 'disabled',
							],
						],
						[
							'type'           => 'checkbox',
							'label'          => esc_html__( 'Post-Checkout Cart Behavior', 'devdiggers-multipos-for-woocommerce' ),
							'checkbox_label' => esc_html__( 'Reset Customer After Checkout', 'devdiggers-multipos-for-woocommerce' ),
							'description'    => esc_html__( 'Clear selected customer after checkout completes.', 'devdiggers-multipos-for-woocommerce' ),
							'id'             => 'ddwcpos-reset-customer-enabled',
							'field_class'    => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => [
								'disabled' => 'disabled',
							],
						],
					],
				],
				[
					'header' => [
						'heading'     => esc_html__( 'Advanced', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Set performance limits and POS URLs.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'        => 'number',
							'label'       => esc_html__( 'Order Sync Threshold', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Load only recent orders to keep POS fast.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-load-orders-only-days-old',
							'field_class' => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => [
								'disabled' => 'disabled',
							],
						],
						[
							'type'        => 'text',
							'label'       => esc_html__( 'POS Terminal URL Path', 'devdiggers-multipos-for-woocommerce' ),
							'description'    => esc_html__( 'Choose URL path for POS screen, like /pos.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-endpoint',
							'value'       => $ddwcpos_configuration['endpoint'],
						],
						[
							'type'        => 'text',
							'label'       => esc_html__( 'Kitchen Display URL Path', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Choose URL path for kitchen screen, like /kitchen.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-kitchen-endpoint',
							'value'       => '',
							'field_class' => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => [
								'disabled' => 'disabled',
							],
						],
					],
				],
			];

			$layout = new DDFW_Layout();
			$layout->get_form_section_layout( $args, 'ddwcpos-general-configuration-fields' );
		}
	}
}
