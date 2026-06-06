<?php
/**
 * Printer Configuration template class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Templates\Admin\Configuration;

use DevDiggers\Framework\Includes\DDFW_Layout;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Printer_Configuration_Template' ) ) {
	/**
	 * Printer Configuration template class
	 */
	class DDWCPOS_Printer_Configuration_Template {
		/**
		 * Construct
		 * 
		 * @param array $ddwcpos_configuration
		 */
		public function __construct( $ddwcpos_configuration ) {
			$args = [
				[
					'header' => [
						'heading'     => esc_html__( 'Barcode Paper', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Set paper size for barcode labels.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'        => 'text',
							'label'       => esc_html__( 'Page Width', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Label width in millimeters.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-barcode-printer-width',
							'value'       => $ddwcpos_configuration['barcode_printer_width'],
						],
						[
							'type'        => 'text',
							'label'       => esc_html__( 'Page Height', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Label height in millimeters. Use 0 for continuous rolls.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-barcode-printer-height',
							'value'       => $ddwcpos_configuration['barcode_printer_height'],
						],
						[
							'type'        => 'text',
							'label'       => esc_html__( 'Page Margins', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Label margins in millimeters.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-barcode-printer-margin',
							'value'       => $ddwcpos_configuration['barcode_printer_margin'],
						],
					],
				],
				[
					'header' => [
						'heading'     => esc_html__( 'Barcode Layout', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Set barcode size and spacing.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'        => 'text',
							'label'       => esc_html__( 'Generated Barcode Height', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Barcode height in millimeters.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-barcode-height',
							'value'       => $ddwcpos_configuration['barcode_height'],
						],
						[
							'type'        => 'text',
							'label'       => esc_html__( 'Unit Spacing Offset', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Space between barcodes in millimeters.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-barcode-margin',
							'value'       => $ddwcpos_configuration['barcode_margin'],
						],
						[
							'type'        => 'select',
							'label'       => esc_html__( 'Print Output Orientation', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Choose barcode print direction.', 'devdiggers-multipos-for-woocommerce' ),
							'options'     => [
								'vertical'   => esc_html__( 'Vertical (Portrait)', 'devdiggers-multipos-for-woocommerce' ),
								'horizontal' => esc_html__( 'Horizontal (Landscape)', 'devdiggers-multipos-for-woocommerce' ),
							],
							'id'          => 'ddwcpos-barcode-orientation',
							'value'       => $ddwcpos_configuration['barcode_orientation'],
							'input_class' => [ 'ddwcpos-select2' ],
						],
					],
				],
				[
					'header' => [
						'heading'     => esc_html__( 'Receipt Size', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Set default size for printed receipts.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'        => 'text',
							'label'       => esc_html__( 'Thermal Roll Width', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Receipt width in millimeters, like 80 or 58.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-printer-width',
							'value'       => $ddwcpos_configuration['printer_width'],
						],
						[
							'type'        => 'text',
							'label'       => esc_html__( 'Thermal Content Height', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Receipt height in millimeters. Leave empty for automatic height.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-printer-height',
							'value'       => $ddwcpos_configuration['printer_height'],
						],
						[
							'type'        => 'text',
							'label'       => esc_html__( 'Safe Content Margins', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Receipt margins in millimeters to keep text from clipping.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-printer-margin',
							'value'       => $ddwcpos_configuration['printer_margin'],
						],
					],
				],
			];

			$layout = new DDFW_Layout();
			$layout->get_form_section_layout( $args, 'ddwcpos-printer-configuration-fields' );
		}
	}
}
