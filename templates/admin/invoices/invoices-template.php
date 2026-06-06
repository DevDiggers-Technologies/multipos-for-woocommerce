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

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Slug is read-only routing input.
			if ( ! empty( $_GET['slug'] ) ) {
				$this->ddwcpos_invoice_composer_locked();
			} else {
				$this->ddwcpos_get_invoice_template();
			}
		}

		/**
		 * Render locked invoice composer page.
		 *
		 * @return void
		 */
		public function ddwcpos_invoice_composer_locked() {
			ddfw_upgrade_to_pro_section(
				[
					'image_url'     => DDWCPOS_PLUGIN_URL . 'assets/images/pro-pages/customize-invoice.webp',
					'heading'       => esc_html__( 'Customize Invoice Templates', 'devdiggers-multipos-for-woocommerce' ),
					'description'   => esc_html__( 'Upgrade to Pro to edit receipt HTML, style custom CSS, restore factory templates, and create multiple invoice layouts.', 'devdiggers-multipos-for-woocommerce' ),
					'list_features' => [
						esc_html__( 'Visual receipt editor with HTML source mode', 'devdiggers-multipos-for-woocommerce' ),
						esc_html__( 'Custom CSS for branded receipt styling', 'devdiggers-multipos-for-woocommerce' ),
						esc_html__( 'Multiple invoice templates for different outlets', 'devdiggers-multipos-for-woocommerce' ),
						esc_html__( 'Restore factory template controls', 'devdiggers-multipos-for-woocommerce' ),
					],
					'upgrade_url'   => '//devdiggers.com/product/multipos-point-of-sale-for-woocommerce/',
				]
			);
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

							<div class="ddwcpos-card-footer">
								<?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Page is read-only routing input. ?>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . ( ! empty( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : 'ddwcpos-dashboard' ) . '&menu=invoices&slug=' . $invoice['slug'] ) ); ?>" class="button ddfw-upgrade-to-pro-tag-wrapper"><?php esc_html_e( 'Customize', 'devdiggers-multipos-for-woocommerce' ); ?></a>
							</div>
						</div>
					<?php endforeach; ?>

					<div class="ddwcpos-invoice-card create-new ddfw-upgrade-to-pro-tag-wrapper">
						<div class="ddwcpos-create-content">
							<span class="dashicons dashicons-plus"></span>
							<h3><?php esc_html_e( 'Create New Template', 'devdiggers-multipos-for-woocommerce' ); ?></h3>
						</div>
					</div>
				</div>
			</div>
			<?php
			$custom_html = ob_get_clean();

			$args = [
				[
					'header'            => [
						'heading'     => esc_html__( 'Invoice Templates', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Free uses one fixed, translatable receipt template. Receipt customization and extra templates are available in Pro.', 'devdiggers-multipos-for-woocommerce' ),
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
