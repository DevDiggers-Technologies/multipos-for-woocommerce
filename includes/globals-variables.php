<?php
/**
 * Global Variables
 *
 * @author DevDiggers
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
*/

defined( 'ABSPATH' ) || exit();

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- This file builds the legacy plugin configuration array from local variables.
global $ddwcpos_configuration;

$order_status             = get_option( '_ddwcpos_order_status' );
$default_barcode          = get_option( '_ddwcpos_default_barcode' );
$endpoint                 = get_option( '_ddwcpos_endpoint' );
$payment_method           = get_option( '_ddwcpos_payment_method' );
$tables                   = get_option( '_ddwcpos_tables' );
$invoices                 = get_option( '_ddwcpos_invoices' );
$login_heading_text       = get_option( '_ddwcpos_login_heading_text' );
$login_subtitle_text      = get_option( '_ddwcpos_login_subtitle_text' );
$login_footer_text        = get_option( '_ddwcpos_login_footer_text' );
$login_button_text        = get_option( '_ddwcpos_login_button_text' );
$login_bg_primary_color   = get_option( '_ddwcpos_login_bg_primary_color' );
$login_bg_secondary_color = get_option( '_ddwcpos_login_bg_secondary_color' );
$login_canvas_bg_color    = get_option( '_ddwcpos_login_canvas_bg_color' );
$login_card_bg_color      = get_option( '_ddwcpos_login_card_bg_color' );
$login_font_color         = get_option( '_ddwcpos_login_font_color' );
$barcode_printer_width    = get_option( '_ddwcpos_barcode_printer_width' );
$barcode_printer_height   = get_option( '_ddwcpos_barcode_printer_height' );
$barcode_printer_margin   = get_option( '_ddwcpos_barcode_printer_margin' );
$barcode_height           = get_option( '_ddwcpos_barcode_height' );
$barcode_margin           = get_option( '_ddwcpos_barcode_margin' );
$barcode_orientation      = get_option( '_ddwcpos_barcode_orientation' );
$printer_width            = get_option( '_ddwcpos_printer_width' );
$printer_height           = get_option( '_ddwcpos_printer_height' );
$printer_margin           = get_option( '_ddwcpos_printer_margin' );
$layout_primary_color     = get_option( '_ddwcpos_layout_primary_color' );
$layout_secondary_color   = get_option( '_ddwcpos_layout_secondary_color' );
$layout_font_color        = get_option( '_ddwcpos_layout_font_color' );
$layout_surface_color     = get_option( '_ddwcpos_layout_surface_color' );
$layout_muted_bg_color_1  = get_option( '_ddwcpos_layout_muted_background_color_1' );
$layout_muted_bg_color_2  = get_option( '_ddwcpos_layout_muted_background_color_2' );
$layout_button_font_color = get_option( '_ddwcpos_layout_button_font_color' );
$layout_success_color     = get_option( '_ddwcpos_layout_success_color' );
$layout_border_color      = get_option( '_ddwcpos_layout_border_color' );
$layout_pos_font_family   = get_option( '_ddwcpos_layout_pos_font_family' );
$layout_radius            = get_option( '_ddwcpos_layout_radius' );
$layout_font_size         = get_option( '_ddwcpos_layout_font_size' );

if ( empty( $payment_method ) ) {
    $payment_method = [];
    $payment_method[] = [
        'name'      => esc_html__( 'Cash', 'devdiggers-multipos-for-woocommerce' ),
        'slug'      => 'cash',
        'permanent' => 'yes',
        'status'    => 'enabled',
    ];
}

$cash_payment_method = [
    'name'      => esc_html__( 'Cash', 'devdiggers-multipos-for-woocommerce' ),
    'slug'      => 'cash',
    'permanent' => 'yes',
    'status'    => 'enabled',
];

foreach ( (array) $payment_method as $configured_payment_method ) {
    if ( ! empty( $configured_payment_method['slug'] ) && 'cash' === $configured_payment_method['slug'] ) {
        $cash_payment_method['name']   = ! empty( $configured_payment_method['name'] ) ? $configured_payment_method['name'] : $cash_payment_method['name'];
        $cash_payment_method['status'] = 'enabled';
        break;
    }
}

$payment_method = [ $cash_payment_method ];

if ( empty( $invoices ) ) {
    $invoices = [];
    $invoices[] = [
        'name'      => esc_html__( 'Default Invoice', 'devdiggers-multipos-for-woocommerce' ),
        'slug'      => 'default-invoice',
        'permanent' => 'yes',
        'status'    => 'enabled',
    ];
} else {
    $invoice = is_array( $invoices ) ? reset( $invoices ) : [];
    $invoices = [
        [
            'name'      => ! empty( $invoice['name'] ) ? $invoice['name'] : esc_html__( 'Default Invoice', 'devdiggers-multipos-for-woocommerce' ),
            'slug'      => 'default-invoice',
            'permanent' => 'yes',
            'status'    => 'enabled',
        ],
    ];
}

$ddwcpos_configuration = [
    'enabled'                              => get_option( '_ddwcpos_enabled' ),
    'inventory_type'                       => 'centralized',
    'order_status'                         => $order_status ?: 'wc-completed',
    'default_barcode'                      => $default_barcode ?: 'id',
    'order_mails_enabled'                  => get_option( '_ddwcpos_order_mails_enabled' ),
    'show_order_status_enabled'            => '',
    'logo'                                 => get_option( '_ddwcpos_logo' ),
    'default_customer'                     => get_option( '_ddwcpos_default_customer' ),
    'endpoint'                             => ! empty( $endpoint ) ? $endpoint : 'pos',
    'payment_method'                       => $payment_method,
    'tables'                               => ! empty( $tables ) ? $tables : [],
    'invoices'                             => $invoices,
    'pwa_name'                             => esc_html__( 'Point of Sale', 'devdiggers-multipos-for-woocommerce' ),
    'pwa_short_name'                       => esc_html__( 'POS', 'devdiggers-multipos-for-woocommerce' ),
    'pwa_description'                      => esc_html__( 'A Progressive Web App for Point of Sale', 'devdiggers-multipos-for-woocommerce' ),
    'pwa_theme_color'                      => '#0892fd',
    'pwa_background_color'                 => '#ffffff',
    'login_heading_text'                   => ! empty( $login_heading_text ) ? $login_heading_text : esc_html__( 'Welcome to Point of Sale', 'devdiggers-multipos-for-woocommerce' ),
    'login_subtitle_text'                  => ! empty( $login_subtitle_text ) ? $login_subtitle_text : esc_html__( 'Access your point of sale terminal to manage orders and retail operations.', 'devdiggers-multipos-for-woocommerce' ),
    'login_footer_text'                    => ! empty( $login_footer_text ) ? $login_footer_text : esc_html__( 'Thanks for using the Point of Sale', 'devdiggers-multipos-for-woocommerce' ),
    'login_button_text'                    => ! empty( $login_button_text ) ? $login_button_text : esc_html__( 'Log in', 'devdiggers-multipos-for-woocommerce' ),
    'login_branding_enabled'               => 'yes',
    'login_rememberme_enabled'             => get_option( '_ddwcpos_login_rememberme_enabled' ),
    'login_forgot_enabled'                 => get_option( '_ddwcpos_login_forgot_enabled' ),
    'login_bg_primary_color'               => ! empty( $login_bg_primary_color ) ? $login_bg_primary_color : '#f0f7ff',
    'login_bg_secondary_color'             => ! empty( $login_bg_secondary_color ) ? $login_bg_secondary_color : '#add8e6',
    'login_canvas_bg_color'                => ! empty( $login_canvas_bg_color ) ? $login_canvas_bg_color : '#f0f7ff',
    'login_card_bg_color'                  => ! empty( $login_card_bg_color ) ? $login_card_bg_color : 'rgba(255, 255, 255, 0.4)',
    'login_font_color'                     => ! empty( $login_font_color ) ? $login_font_color : '#1a1a1a',
    'barcode_printer_width'                => ! empty( $barcode_printer_width ) ? $barcode_printer_width : '28mm',
    'barcode_printer_height'               => ! empty( $barcode_printer_height ) ? $barcode_printer_height : '89mm',
    'barcode_printer_margin'               => ! empty( $barcode_printer_margin ) ? $barcode_printer_margin : '0mm',
    'barcode_height'                       => ! empty( $barcode_height ) ? $barcode_height : '40mm',
    'barcode_margin'                       => ! empty( $barcode_margin ) ? $barcode_margin : '50mm -3mm',
    'barcode_orientation'                  => ! empty( $barcode_orientation ) ? $barcode_orientation : 'vertical',
    'printer_width'                        => ! empty( $printer_width ) ? $printer_width : '150mm',
    'printer_height'                       => ! empty( $printer_height ) ? $printer_height : '300mm',
    'printer_margin'                       => ! empty( $printer_margin ) ? $printer_margin : '10mm',
    'barcode_print_orientation'            => 'veritical',
    'layout_primary_color'                 => ! empty( $layout_primary_color ) ? $layout_primary_color : '#0256ff',
    'layout_secondary_color'               => ! empty( $layout_secondary_color ) ? $layout_secondary_color : '#0256ff',
    'layout_font_color'                    => ! empty( $layout_font_color ) ? $layout_font_color : '#555',
    'layout_surface_color'                 => ! empty( $layout_surface_color ) ? $layout_surface_color : '#fff',
    'layout_muted_background_color_1'      => ! empty( $layout_muted_bg_color_1 ) ? $layout_muted_bg_color_1 : '#f1f3f7',
    'layout_muted_background_color_2'      => ! empty( $layout_muted_bg_color_2 ) ? $layout_muted_bg_color_2 : '#f9fafb',
    'layout_button_font_color'             => ! empty( $layout_button_font_color ) ? $layout_button_font_color : '#fff',
    'layout_success_color'                 => ! empty( $layout_success_color ) ? $layout_success_color : '#15b71a',
    'layout_border_color'                  => ! empty( $layout_border_color ) ? $layout_border_color : '#e1e4e8',
    'layout_pos_font_family'               => ! empty( $layout_pos_font_family ) ? $layout_pos_font_family : 'open_sans',
    'layout_radius'                        => ! empty( $layout_radius ) ? $layout_radius : '6',
    'layout_font_size'                     => ! empty( $layout_font_size ) ? $layout_font_size : '14',
    'product_layout'                       => get_option( '_ddwcpos_product_layout', 'image_top' ),
    'product_variation_layout'             => 'grid',
    'show_product_stock_enabled'           => get_option( '_ddwcpos_show_product_stock_enabled', 'yes' ),
];
