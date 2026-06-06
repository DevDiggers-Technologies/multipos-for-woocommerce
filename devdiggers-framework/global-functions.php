<?php
/**
 * File for handling global functions in the DevDiggers Plugin Framework.
 *
 * @author  DevDiggers
 * @category Framework
 * @package DevDiggers\Framework
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'ddfw_get_parent_menu_slug' ) ) {
	/**
	 * Get the parent menu slug for the DevDiggers Plugins menu.
	 *
	 * @return string
	 */
	function ddfw_get_parent_menu_slug() {
		return apply_filters( 'ddfw_modify_parent_menu_slug', 'devdiggers-plugins' );
	}
}

if ( ! function_exists( 'ddfw_get_menu_capability' ) ) {
	/**
	 * Get the capability required to access the dashboard menu.
	 *
	 * @return string
	 */
	function ddfw_get_menu_capability() {
		return apply_filters( 'ddfw_modify_admin_menu_capability', class_exists( 'WooCommerce' ) ? 'manage_woocommerce' : 'manage_options' );
	}
}

if ( ! function_exists( 'ddfw_get_placeholder_image_src' ) ) {
	/**
	 * Get placeholder image src function
	 *
	 * @return string
	 */
	function ddfw_get_placeholder_image_src() {
		return DDFW_URL . 'assets/images/placeholder.png';
	}
}

if ( ! function_exists( 'ddfw_print_notification' ) ) {
	/**
	 * Print a notification message.
	 *
	 * @param string $message The message to display.
	 * @param string $type    The type of notification (e.g., 'success', 'error').
	 * @param bool   $dismissible Whether the notification is dismissible.
	 */
	function ddfw_print_notification( $message, $type = 'success', $dismissible = true ) {
		include DDFW_FILE . 'templates/global/notice.php';
	}
}

if ( ! function_exists( 'ddfw_kses_allowed_svg_tags' ) ) {
	/**
	 * Get allowed SVG tags for KSES filtering.
	 *
	 * @return array
	 */
	function ddfw_kses_allowed_svg_tags() {
		return [
			'svg'      => [
				'class'           => true,
				'data-*'          => true,
				'aria-*'          => true,
				'role'            => true,
				'xmlns'           => true,
				'width'           => true,
				'height'          => true,
				'viewbox'         => true,
				'version'         => true,
				'x'               => true,
				'y'               => true,
				'style'           => true,
				'fill'            => true,
				'stroke'          => true,
				'stroke-width'    => true,
				'stroke-linecap'  => true,
				'stroke-linejoin' => true,
				'fill-opacity'    => true,
			],
			'circle'   => [
				'class'           => true,
				'cx'              => true,
				'cy'              => true,
				'r'               => true,
				'fill'            => true,
				'style'           => true,
				'stroke'          => true,
				'stroke-width'    => true,
				'stroke-linecap'  => true,
				'stroke-linejoin' => true,
				'fill-opacity'    => true,
			],
			'g'        => [ 'fill' => true, 'fill-opacity' => true ],
			'polyline' => [
				'class'  => true,
				'points' => true,
				'd'               => true,
				'fill'            => true,
				'clip-rule'       => true,
				'fill-rule'       => true,
				'stroke'          => true,
				'stroke-width'    => true,
				'stroke-linecap'  => true,
				'stroke-linejoin' => true,
				'fill-opacity'    => true,
			],
			'polygon'  => [
				'class'  => true,
				'points' => true,
				'd'               => true,
				'fill'            => true,
				'clip-rule'       => true,
				'fill-rule'       => true,
				'stroke'          => true,
				'stroke-width'    => true,
				'stroke-linecap'  => true,
				'stroke-linejoin' => true,
				'fill-opacity'    => true,
			],
			'line'     => [
				'class' => true,
				'x1'    => true,
				'x2'    => true,
				'y1'    => true,
				'y2'    => true,
			],
			'title'    => [ 'title' => true ],
			'path'     => [
				'class'           => true,
				'd'               => true,
				'fill'            => true,
				'clip-rule'       => true,
				'fill-rule'       => true,
				'stroke'          => true,
				'stroke-width'    => true,
				'stroke-linecap'  => true,
				'stroke-linejoin' => true,
				'fill-opacity'    => true,
			],
			'rect'     => [
				'class'           => true,
				'x'               => true,
				'y'               => true,
				'rx'              => true,
				'ry'              => true,
				'fill'            => true,
				'width'           => true,
				'height'          => true,
				'clip-rule'       => true,
				'fill-rule'       => true,
				'stroke'          => true,
				'stroke-width'    => true,
				'stroke-linecap'  => true,
				'stroke-linejoin' => true,
				'fill-opacity'    => true,
			],
		];
	}
}

if ( ! function_exists( 'ddfw_upgrade_to_pro_section' ) ) {
	/**
	 * Upgrade to Pro section function
	 *
	 * @param array $args
	 * @return void
	 */
	function ddfw_upgrade_to_pro_section( $args ) {
		include DDFW_FILE . 'templates/layout/upgrade-to-pro.php';
	}
}

if ( ! function_exists( 'ddfw_pro_tag' ) ) {
	/**
	 * Pro tag function
	 *
	 * @return void
	 */
	function ddfw_pro_tag() {
		?>
		<span class="ddfw-pro-tag"><span class="dashicons dashicons-lock"></span> <?php esc_html_e( 'PRO', 'devdiggers-multipos-for-woocommerce' ); ?></span>
		<?php
	}
}

if ( ! function_exists( 'ddfw_fields_heading' ) ) {
	/**
	 * Fields heading function
	 *
	 * @param array $args
	 * @return void
	 */
	function ddfw_fields_heading( $args ) {
		include DDFW_FILE . 'templates/layout/field-section-header.php';
	}
}

if ( ! function_exists( 'ddfw_get_devdiggers_plugin_menu_icon_src' ) ) {
	/**
	 * Get the DevDiggers plugin menu icon src.
	 *
	 * @return string
	 */
	function ddfw_get_devdiggers_plugin_menu_icon_src() {
		return DDFW_URL . 'assets/images/devdiggers-logo.svg';
	}
}

