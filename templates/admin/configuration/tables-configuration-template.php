<?php
/**
 * Tables Configuration template class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Templates\Admin\Configuration;

use DevDiggers\Framework\Includes\DDFW_Layout;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Tables_Configuration_Template' ) ) {
	/**
	 * Tables Configuration template class
	 */
	class DDWCPOS_Tables_Configuration_Template {
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
							<th><?php esc_html_e( 'Identification Name', 'devdiggers-multipos-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Unique Slug', 'devdiggers-multipos-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Seating Capacity', 'devdiggers-multipos-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Status', 'devdiggers-multipos-for-woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						if ( ! empty( $ddwcpos_configuration['tables'] ) ) {
							foreach ( $ddwcpos_configuration['tables'] as $key => $tables ) {
								?>
								<tr valign="top">
									<td class="forminp forminp-text">
										<input type="text" name="_ddwcpos_tables[<?php echo esc_attr( $key ); ?>][name]" class="regular-text ddwcpos-width-90" autocomplete="off" value="<?php echo esc_attr( $tables['name'] ); ?>" />
									</td>
									<td class="forminp forminp-text">
										<input type="text" name="_ddwcpos_tables[<?php echo esc_attr( $key ); ?>][slug]" class="regular-text ddwcpos-width-90" autocomplete="off" value="<?php echo esc_attr( $tables['slug'] ); ?>" readonly />
									</td>
									<td class="forminp forminp-text">
										<input type="number" min="1" name="_ddwcpos_tables[<?php echo esc_attr( $key ); ?>][seats]" class="regular-text ddwcpos-width-90" autocomplete="off" value="<?php echo esc_attr( $tables['seats'] ); ?>" />
									</td>
									<td class="forminp forminp-text ddfw-table-column-flex">
										<select class="regular-text ddwcpos-width-90" name="_ddwcpos_tables[<?php echo esc_attr( $key ); ?>][status]" data-placeholder="<?php esc_attr_e( 'Select Status', 'devdiggers-multipos-for-woocommerce' ); ?>">
											<option value="enabled" <?php echo esc_attr( 'enabled' === $tables['status'] ? 'selected="selected' : '' ); ?>><?php esc_html_e( 'Active', 'devdiggers-multipos-for-woocommerce' ); ?></option>
											<option value="disabled" <?php echo esc_attr( 'disabled' === $tables['status'] ? 'selected="selected' : '' ); ?>><?php esc_html_e( 'Inactive', 'devdiggers-multipos-for-woocommerce' ); ?></option>
										</select>
										<span class="dashicons dashicons-trash ddwcpos-remove-row" title="<?php esc_attr_e( 'Remove', 'devdiggers-multipos-for-woocommerce' ); ?>"></span>
									</td>
								</tr>
								<?php
							}
						}
						?>
						<tr>
							<td colspan="4">
								<a href="javascript:void(0);" class="ddwcpos-add-row button" data-template="ddwcpos-tables-configuration-row"><?php esc_html_e( 'Add Guest Table', 'devdiggers-multipos-for-woocommerce' ); ?></a>
							</td>
						</tr>
					</tbody>
				</table>

				<input type="hidden" id="ddwcpos-max-index" value="<?php echo esc_attr( isset( $key ) ? $key : -1 ); ?>">

				</div>
			<?php
			$custom_html = ob_get_clean();

			$args = [
				[
					'header'            => [
						'heading'     => esc_html__( 'Tables', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Add and manage tables available in restaurant POS.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'after_header_html' => $custom_html,
					'fields'            => [],
				],
			];

			$layout = new DDFW_Layout();
			$layout->get_form_section_layout( $args, 'ddwcpos-tables-configuration-fields', '', 'ddwcpos-tables-container' );

			// Inert wp.template markup. Printed raw (static, no variables) so wp_kses does not strip the <script> wrappers.
			?>
			<!-- Tables Configuration Row Template -->
				<script id="tmpl-ddwcpos-tables-configuration-row" type="text/html">
					<tr valign="top">
						<td class="forminp forminp-text">
							<input type="text" name="_ddwcpos_tables[{{data.key}}][name]" class="regular-text ddwcpos-width-90" autocomplete="off" />
						</td>
						<td class="forminp forminp-text">
							<input type="text" name="_ddwcpos_tables[{{data.key}}][slug]" class="regular-text ddwcpos-width-90" autocomplete="off" />
						</td>
						<td class="forminp forminp-text">
							<input type="number" min="1" name="_ddwcpos_tables[{{data.key}}][seats]" class="regular-text ddwcpos-width-90" autocomplete="off" />
						</td>
						<td class="forminp forminp-text ddfw-table-column-flex">
							<select class="regular-text ddwcpos-width-90" name="_ddwcpos_tables[{{data.key}}][status]" data-placeholder="<?php esc_attr_e( 'Select Status', 'devdiggers-multipos-for-woocommerce' ); ?>">
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
						<p><?php esc_html_e( 'Required table attributes are missing or invalid.', 'devdiggers-multipos-for-woocommerce' ); ?></p>
					</div>
				</script>
			<?php
		}
	}
}
