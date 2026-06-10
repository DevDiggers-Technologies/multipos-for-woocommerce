<?php
/**
 * Invoices Configuration template class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Templates\Admin\Invoices;

use DevDiggers\Framework\Includes\DDFW_Layout;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Invoices_Template' ) ) {
	/**
	 * Free invoice template list.
	 */
	class DDWCPOS_Invoices_Template {
		/**
		 * Configuration variable.
		 *
		 * @var array
		 */
		protected $ddwcpos_configuration;

		/**
		 * Construct.
		 *
		 * @param array $ddwcpos_configuration Configuration.
		 */
		public function __construct( $ddwcpos_configuration ) {
			$this->ddwcpos_configuration = $ddwcpos_configuration;

			$this->ddwcpos_get_invoice_template();
		}

		/**
		 * Render invoice template list.
		 *
		 * @return void
		 */
		public function ddwcpos_get_invoice_template() {
			ob_start();
			?>
			<div class="ddwcpos-invoices-grid-wrapper">
				<div class="ddwcpos-invoices-grid">
					<?php foreach ( $this->ddwcpos_configuration['invoices'] as $key => $invoice ) : ?>
						<div class="ddwcpos-invoice-card">
							<div class="ddwcpos-invoice-card-header">
								<div class="ddwcpos-card-icon">
									<span class="dashicons dashicons-media-text"></span>
								</div>
								<div class="ddwcpos-card-actions">
									<span class="ddwcpos-status-badge active"><?php esc_html_e( 'Active', 'devdiggers-multipos-for-woocommerce' ); ?></span>
								</div>
							</div>

							<div class="ddwcpos-card-body">
								<input type="hidden" name="_ddwcpos_invoices[<?php echo esc_attr( $key ); ?>][permanent]" value="yes" />
								<div class="ddwcpos-form-group">
									<input type="text" name="_ddwcpos_invoices[<?php echo esc_attr( $key ); ?>][name]" class="regular-text ddwcpos-full-width" autocomplete="off" value="<?php echo esc_attr( $invoice['name'] ); ?>" placeholder="<?php esc_attr_e( 'Template Name', 'devdiggers-multipos-for-woocommerce' ); ?>" />
								</div>
								<div class="ddwcpos-form-group">
									<input type="text" name="_ddwcpos_invoices[<?php echo esc_attr( $key ); ?>][slug]" class="regular-text ddwcpos-full-width readonly-slug" autocomplete="off" value="default-invoice" readonly title="default-invoice" />
								</div>
								<div class="ddwcpos-hide">
									<select class="regular-text" name="_ddwcpos_invoices[<?php echo esc_attr( $key ); ?>][status]">
										<option value="enabled" selected="selected"><?php esc_html_e( 'Active', 'devdiggers-multipos-for-woocommerce' ); ?></option>
									</select>
								</div>
							</div>

						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php
			$custom_html = ob_get_clean();

			$args = [
				[
					'header'            => [
						'heading'     => esc_html__( 'Invoice Templates', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Translatable receipt template. Customize receipt and manage extra templates in Pro.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'after_header_html' => $custom_html,
					'fields'            => [],
				],
			];

			$layout = new DDFW_Layout();
			$layout->get_form_section_layout( $args, 'ddwcpos-invoices-configuration-fields', '', 'ddwcpos-invoices-container' );
		}
	}
}
