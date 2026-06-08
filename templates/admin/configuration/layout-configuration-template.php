<?php
/**
 * Layout Configuration template class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Templates\Admin\Configuration;

use DevDiggers\Framework\Includes\DDFW_Layout;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Layout_Configuration_Template' ) ) {
	/**
	 * Layout Configuration template class
	 */
	class DDWCPOS_Layout_Configuration_Template {
		/**
		 * Construct
		 * 
		 * @param array $ddwcpos_configuration
		 */
		public function __construct( $ddwcpos_configuration ) {
			$args = [
				[
					'header' => [
						'heading'     => esc_html__( 'Colors', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Choose colors used across POS screens and buttons.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'        => 'colorpicker',
							'label'       => esc_html__( 'Primary Brand Color', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Main color for active states and primary buttons.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-layout-primary-color',
							'value'       => $ddwcpos_configuration['layout_primary_color'],
						],
						[
							'type'        => 'colorpicker',
							'label'       => esc_html__( 'Secondary Brand Color', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Secondary color used in gradients and highlights.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-layout-secondary-color',
							'value'       => $ddwcpos_configuration['layout_secondary_color'],
						],
						[
							'type'        => 'colorpicker',
							'label'       => esc_html__( 'Primary Text Color', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Main text color across POS.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-layout-font-color',
							'value'       => $ddwcpos_configuration['layout_font_color'],
						],
						[
							'type'        => 'colorpicker',
							'label'       => esc_html__( 'Surface Color', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Background color for cards, panels, and inputs.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-layout-surface-color',
							'value'       => $ddwcpos_configuration['layout_surface_color'],
						],
						[
							'type'        => 'colorpicker',
							'label'       => esc_html__( 'Muted Background Color 1', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Muted background for smaller secondary elements.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-layout-muted-background-color-1',
							'value'       => $ddwcpos_configuration['layout_muted_background_color_1'],
						],
						[
							'type'        => 'colorpicker',
							'label'       => esc_html__( 'Muted Background Color 2', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Muted background for larger secondary sections.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-layout-muted-background-color-2',
							'value'       => $ddwcpos_configuration['layout_muted_background_color_2'],
						],
						[
							'type'        => 'colorpicker',
							'label'       => esc_html__( 'Button Text Color', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Text and icon color on primary buttons.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-layout-button-font-color',
							'value'       => $ddwcpos_configuration['layout_button_font_color'],
						],
						[
							'type'        => 'colorpicker',
							'label'       => esc_html__( 'Border Color', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Default border color for cards, inputs, and sections.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-layout-border-color',
							'value'       => $ddwcpos_configuration['layout_border_color'],
						],
						[
							'type'        => 'colorpicker',
							'label'       => esc_html__( 'Success Color', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Color used for success states and positive amounts.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-layout-success-color',
							'value'       => $ddwcpos_configuration['layout_success_color'],
						],
					],
				],
				[
					'header' => [
						'heading'     => esc_html__( 'Typography', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Set font style and size for POS screens.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'        => 'select',
							'label'       => esc_html__( 'POS Font Family', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Choose font used in POS only.', 'devdiggers-multipos-for-woocommerce' ),
							'options'     => [
								'open_sans'  => esc_html__( 'Open Sans', 'devdiggers-multipos-for-woocommerce' ),
								'poppins'    => esc_html__( 'Poppins', 'devdiggers-multipos-for-woocommerce' ),
								'lato'       => esc_html__( 'Lato', 'devdiggers-multipos-for-woocommerce' ),
								'montserrat' => esc_html__( 'Montserrat', 'devdiggers-multipos-for-woocommerce' ),
								'nunito'     => esc_html__( 'Nunito', 'devdiggers-multipos-for-woocommerce' ),
							],
							'id'          => 'ddwcpos-layout-pos-font-family',
							'value'       => $ddwcpos_configuration['layout_pos_font_family'],
							'input_class' => [ 'ddwcpos-select2' ],
						],
						[
							'type'        => 'number',
							'label'       => esc_html__( 'Base Font Size', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Base font size in pixels.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-layout-font-size',
							'value'       => $ddwcpos_configuration['layout_font_size'],
						],
						[
							'type'        => 'number',
							'label'       => esc_html__( 'Global Corner Radius', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Corner radius in pixels for buttons, inputs, and cards.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-layout-radius',
							'value'       => $ddwcpos_configuration['layout_radius'],
						],
					],
				],
				[
					'header' => [
						'heading'     => esc_html__( 'Product Display', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Choose how products and variations appear to cashiers.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'        => 'select',
							'label'       => esc_html__( 'Product Card Orientation', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Choose whether product image sits left or top.', 'devdiggers-multipos-for-woocommerce' ),
							'options'     => [
								'image_left' => esc_html__( 'Horizontal (Image Left, Text Right)', 'devdiggers-multipos-for-woocommerce' ),
								'image_top'  => esc_html__( 'Vertical (Image Top, Text Bottom)', 'devdiggers-multipos-for-woocommerce' ),
							],
							'id'          => 'ddwcpos-product-layout',
							'value'       => $ddwcpos_configuration['product_layout'],
							'input_class' => [ 'ddwcpos-select2' ],
						],
					],
				],
				[
					'header' => [
						'heading'     => esc_html__( 'Product Details', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Choose which product details stay visible in grid.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'           => 'checkbox',
							'label'          => esc_html__( 'Real-time Stock Indicators', 'devdiggers-multipos-for-woocommerce' ),
							'checkbox_label' => esc_html__( 'Display Current Stock Count', 'devdiggers-multipos-for-woocommerce' ),
							'description'    => esc_html__( 'Show stock count on each product card.', 'devdiggers-multipos-for-woocommerce' ),
							'id'             => 'ddwcpos-show-product-stock-enabled',
							'value'          => $ddwcpos_configuration['show_product_stock_enabled'],
						],
					],
				],
			];

			$layout = new DDFW_Layout();
			$layout->get_form_section_layout( $args, 'ddwcpos-layout-configuration-fields' );
		}
	}
}
