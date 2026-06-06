<?php
/**
 * Autoloader.
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 */

namespace DDWCMultiPOS\Autoload;

defined( 'ABSPATH' ) || exit();

spl_autoload_register( __NAMESPACE__ . '\\ddwcpos_namespace_class_autoload' );

/**
 * Register an autoloadable file path for the class name.
 *
 * @param string $class_name Class name to load.
 */
function ddwcpos_namespace_class_autoload( $class_name ) {
	if ( 0 !== strpos( $class_name, 'DDWCMultiPOS\\' ) ) {
		return;
	}

	$relative_class = substr( $class_name, strlen( 'DDWCMultiPOS\\' ) );
	$parts          = explode( '\\', $relative_class );
	$file_name      = array_pop( $parts );

	if ( ddwcpos_autoload_is_interface( $file_name ) ) {
		$file_name = 'interface-' . ddwcpos_autoload_interface_file_name( $file_name ) . '.php';
	} else {
		$file_name = ddwcpos_autoload_sanitize_segment( $file_name ) . '.php';
	}

	$path_parts = array_map( 'DDWCMultiPOS\\Autoload\\ddwcpos_autoload_sanitize_segment', $parts );
	$filepath   = trailingslashit( dirname( dirname( __FILE__ ) ) . '/' . implode( '/', $path_parts ) ) . $file_name;

	if ( file_exists( $filepath ) ) {
		include_once $filepath;
		return;
	}

	wp_die(
		sprintf(
			/* translators: %s: File path. */
			esc_html__( 'The file attempting to be loaded at %s does not exist.', 'devdiggers-multipos-for-woocommerce' ),
			esc_html( $filepath )
		)
	);
}

/**
 * Sanitize a segment of the class path.
 *
 * @param string $segment Namespace segment.
 * @return string
 */
function ddwcpos_autoload_sanitize_segment( $segment ) {
	$segment = strtolower( $segment );
	$segment = str_replace( '_', '-', $segment );
	return str_ireplace( 'ddwcpos-', '', $segment );
}

/**
 * Check whether the class name represents an interface.
 *
 * @param string $class_name Raw class name segment.
 * @return bool
 */
function ddwcpos_autoload_is_interface( $class_name ) {
	return false !== stripos( $class_name, 'interface' );
}

/**
 * Convert an interface class name to its file base name.
 *
 * @param string $interface_name Interface class name.
 * @return string
 */
function ddwcpos_autoload_interface_file_name( $interface_name ) {
	$parts = explode( '_', $interface_name );
	array_pop( $parts );
	return strtolower( implode( '-', $parts ) );
}
