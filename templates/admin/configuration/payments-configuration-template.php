<?php
/**
 * Payments Configuration template class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Templates\Admin\Configuration;

use DevDiggers\Framework\Includes\DDFW_Layout;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Payments_Configuration_Template' ) ) {
	/**
	 * Payments Configuration template class
	 */
	class DDWCPOS_Payments_Configuration_Template {
		/**
		 * Construct
		 * 
		 * @param array $ddwcpos_configuration
		 */
		public function __construct( $ddwcpos_configuration ) {
			ob_start();
			?>
			<div class="ddfw-table-wrapper">
				<table class="widefat fixed striped ddfw-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Payment Method Label', 'devdiggers-multipos-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Unique Slug', 'devdiggers-multipos-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Enable/Disable Status', 'devdiggers-multipos-for-woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						if ( ! empty( $ddwcpos_configuration['payment_method'] ) ) {
							foreach ( $ddwcpos_configuration['payment_method'] as $key => $payment_method ) {
								?>
								<tr valign="top">
									<td class="forminp forminp-text">
										<input type="hidden" name="_ddwcpos_payment_method[<?php echo esc_attr( $key ); ?>][permanent]" value="<?php echo esc_attr( $payment_method['permanent'] ); ?>" />
										<input type="text" name="_ddwcpos_payment_method[<?php echo esc_attr( $key ); ?>][name]" class="regular-text ddwcpos-width-90" autocomplete="off" value="<?php echo esc_attr( $payment_method['name'] ); ?>" />
									</td>
									<td class="forminp forminp-text">
										<input type="text" name="_ddwcpos_payment_method[<?php echo esc_attr( $key ); ?>][slug]" class="regular-text ddwcpos-width-90" autocomplete="off" value="<?php echo esc_attr( $payment_method['slug'] ); ?>" readonly />
									</td>
									<td class="forminp forminp-text ddfw-table-column-flex">
										<select class="regular-text ddwcpos-width-90" name="_ddwcpos_payment_method[<?php echo esc_attr( $key ); ?>][status]" data-placeholder="<?php esc_attr_e( 'Select Status', 'devdiggers-multipos-for-woocommerce' ); ?>">
											<option value="enabled" <?php echo esc_attr( 'enabled' === $payment_method['status'] ? 'selected="selected' : '' ); ?>><?php esc_html_e( 'Active', 'devdiggers-multipos-for-woocommerce' ); ?></option>
											<option value="disabled" <?php echo esc_attr( 'disabled' === $payment_method['status'] ? 'selected="selected' : '' ); ?>><?php esc_html_e( 'Inactive', 'devdiggers-multipos-for-woocommerce' ); ?></option>
										</select>
										<?php
										if ( 'no' === $payment_method['permanent'] ) {
											?>
											<span class="dashicons dashicons-trash ddwcpos-remove-row" title="<?php esc_attr_e( 'Remove', 'devdiggers-multipos-for-woocommerce' ); ?>"></span>
											<?php
										}
										?>
									</td>
								</tr>
								<?php
							}
						}
						?>
						<tr>
							<td colspan="3">
								<a href="javascript:void(0);" class="button ddfw-upgrade-to-pro-tag-wrapper"><?php esc_html_e( 'Register Custom Method', 'devdiggers-multipos-for-woocommerce' ); ?></a>
							</td>
						</tr>
					</tbody>
				</table>

				<input type="hidden" id="ddwcpos-max-index" value="<?php echo esc_attr( isset( $key ) ? $key : 0 ); ?>">

				<!-- Payments Configuration Row Template -->
				<script id="tmpl-ddwcpos-payments-configuration-row" type="text/html">
					<tr valign="top">
						<td class="forminp forminp-text">
							<input type="hidden" name="_ddwcpos_payment_method[{{data.key}}][permanent]" value="no" />
							<input type="text" name="_ddwcpos_payment_method[{{data.key}}][name]" class="regular-text ddwcpos-width-90" autocomplete="off" />
						</td>
						<td class="forminp forminp-text">
							<input type="text" name="_ddwcpos_payment_method[{{data.key}}][slug]" class="regular-text ddwcpos-width-90" autocomplete="off" />
						</td>
						<td class="forminp forminp-text ddfw-table-column-flex">
							<select class="regular-text ddwcpos-width-90" name="_ddwcpos_payment_method[{{data.key}}][status]" data-placeholder="<?php esc_attr_e( 'Select Status', 'devdiggers-multipos-for-woocommerce' ); ?>">
								<option value="enabled"><?php esc_html_e( 'Active', 'devdiggers-multipos-for-woocommerce' ); ?></option>
								<option value="disabled"><?php esc_html_e( 'Inactive', 'devdiggers-multipos-for-woocommerce' ); ?></option>
							</select>
							<span class="dashicons dashicons-trash ddwcpos-remove-row" title="<?php esc_attr_e( 'Remove', 'devdiggers-multipos-for-woocommerce' ); ?>"></span>
						</td>
					</tr>
				</script>

				<!-- Invalid form data Template -->
				<script id="tmpl-ddwcpos_form_data_error" type="text/html">
					<div class='notice notice-error is-dismissible'>
						<p><?php esc_html_e( 'Some fields are missing or contain invalid formats.', 'devdiggers-multipos-for-woocommerce' ); ?></p>
					</div>
				</script>
			</div>
			<?php
			$custom_html = ob_get_clean();

			$args = [
				[
					'header'            => [
						'heading'     => esc_html__( 'Payments', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Add, edit, and enable payment methods available in POS.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'after_header_html' => $custom_html,
					'fields'            => [],
				],
			];

			$layout = new DDFW_Layout();
			$layout->get_form_section_layout( $args, 'ddwcpos-payments-configuration-fields', '', 'ddwcpos-payments-container' );
		}
	}
}
