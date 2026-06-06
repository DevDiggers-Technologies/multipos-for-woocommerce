<?php
/**
 * MultiPOS Setup Wizard Integration
 */

namespace DDWCMultiPOS\Includes\Admin;

defined( 'ABSPATH' ) || exit();

use DevDiggers\Framework\Includes\DDFW_Form_Field;
use DevDiggers\Framework\Includes\DDFW_Setup_Wizard;

if ( ! class_exists( 'DDWCPOS_Setup_Wizard' ) ) {
    /**
     * DDWCPOS_Setup_Wizard class
     */
    class DDWCPOS_Setup_Wizard {
        /**
         * Configuration array
         *
         * @var array
         */
        private $ddwcpos_configuration;

        /**
         * Constructor
         */
        public function __construct() {
            global $ddwcpos_configuration;
            $this->ddwcpos_configuration = $ddwcpos_configuration;

            $slug = 'devdiggers-multipos-for-woocommerce';

            // If the plugin already has existing configuration, mark the wizard as completed.
            if ( ! get_option( 'ddfw_setup_wizard_completed_' . $slug ) ) {
                if ( get_option( '_ddwcpos_enabled' ) ) {
                    update_option( 'ddfw_setup_wizard_completed_' . $slug, true );
                }
            }

            new DDFW_Setup_Wizard( $this->get_wizard_config() );
        }

        /**
         * Get the wizard configuration
         *
         * @return array
         */
        public function get_wizard_config() {
            return [
                'plugin_slug'    => 'devdiggers-multipos-for-woocommerce',
                'plugin_file'    => 'devdiggers-multipos-for-woocommerce/functions.php',
                'dashboard_page' => 'ddwcpos-dashboard',
                'redirect_url'   => admin_url( 'admin.php?page=ddwcpos-dashboard' ),
                'brand'          => [
                    'name'        => esc_html__( 'MultiPOS', 'devdiggers-multipos-for-woocommerce' ),
                    'description' => esc_html__( "Welcome to MultiPOS - Point of Sale for WooCommerce! Let's get your POS system up and running in just a few steps.", 'devdiggers-multipos-for-woocommerce' ),
                    'logo'        => '<svg class="ddfw-success-svg" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="15" fill="var(--ddfw-tab-background-color)"/>
                        <path d="M9 21C9 19.8954 9.89543 19 11 19H21C22.1046 19 23 19.8954 23 21V25C23 25.5523 22.5523 26 22 26H10C9.44772 26 9 25.5523 9 25V21Z" fill="var(--ddfw-primary-color)"/>
                        <path d="M10 11C10 9.89543 10.8954 9 12 9H20C21.1046 9 22 9.89543 22 11V19H10V11Z" fill="var(--ddfw-primary-color)" fill-opacity="0.7"/>
                        <rect x="12" y="11" width="2" height="1.5" rx="0.5" fill="white"/>
                        <rect x="15" y="11" width="2" height="1.5" rx="0.5" fill="white"/>
                        <rect x="18" y="11" width="2" height="1.5" rx="0.5" fill="white"/>
                        <rect x="12" y="14" width="2" height="1.5" rx="0.5" fill="white"/>
                        <rect x="15" y="14" width="2" height="1.5" rx="0.5" fill="white"/>
                        <rect x="18" y="14" width="2" height="1.5" rx="0.5" fill="white"/>
                        <circle cx="16" cy="22.5" r="1.2" fill="white"/>
                        <path d="M18 9V4L19 5L20 4L21 5L22 4V9H18Z" fill="var(--ddfw-primary-color)" fill-opacity="0.5"/>
                    </svg>',
                ],
                'steps'          => [
                    'welcome'     => [
                        'label'         => esc_html__( 'Welcome', 'devdiggers-multipos-for-woocommerce' ),
                        'view_callback' => [ $this, 'welcome_view' ],
                    ],
                    'general'     => [
                        'label'         => esc_html__( 'General', 'devdiggers-multipos-for-woocommerce' ),
                        'title'         => esc_html__( 'Plugin Status & Inventory', 'devdiggers-multipos-for-woocommerce' ),
                        'description'   => esc_html__( 'Manage the core status of your POS system and define how inventory should be tracked across your registers.', 'devdiggers-multipos-for-woocommerce' ),
                        'view_callback' => [ $this, 'general_settings_view' ],
                        'save_callback' => [ $this, 'save_fields' ],
                    ],
                    'checkout'    => [
                        'label'         => esc_html__( 'Checkout', 'devdiggers-multipos-for-woocommerce' ),
                        'title'         => esc_html__( 'Checkout Features', 'devdiggers-multipos-for-woocommerce' ),
                        'description'   => esc_html__( 'Configure specialized functionalities available during the checkout process.', 'devdiggers-multipos-for-woocommerce' ),
                        'view_callback' => [ $this, 'checkout_features_view' ],
                        'save_callback' => [ $this, 'save_fields' ],
                    ],
                    'layout'      => [
                        'label'         => esc_html__( 'Layout', 'devdiggers-multipos-for-woocommerce' ),
                        'title'         => esc_html__( 'Aesthetics & Display', 'devdiggers-multipos-for-woocommerce' ),
                        'description'   => esc_html__( 'Customize the visual identity and product presentation of your POS terminal.', 'devdiggers-multipos-for-woocommerce' ),
                        'view_callback' => [ $this, 'layout_settings_view' ],
                        'save_callback' => [ $this, 'save_fields' ],
                    ],
                    'customers'   => [
                        'label'         => esc_html__( 'Customers', 'devdiggers-multipos-for-woocommerce' ),
                        'title'         => esc_html__( 'Customer Preferences', 'devdiggers-multipos-for-woocommerce' ),
                        'description'   => esc_html__( 'Manage how customers and guest shoppers are handled at the point of sale.', 'devdiggers-multipos-for-woocommerce' ),
                        'view_callback' => [ $this, 'customer_preferences_view' ],
                        'save_callback' => [ $this, 'save_fields' ],
                    ],
                    'ready'       => [
                        'label'             => esc_html__( 'Ready!', 'devdiggers-multipos-for-woocommerce' ),
                        'ready_title'       => esc_html__( 'Your POS system is ready to go!', 'devdiggers-multipos-for-woocommerce' ),
                        'ready_description' => esc_html__( 'You can now create outlets, assign cashiers, and start processing orders. Visit the Configuration page anytime to adjust these settings.', 'devdiggers-multipos-for-woocommerce' ),
                    ],
                ],
            ];
        }

        /**
         * Welcome view
         *
         * @return void
         */
        public function welcome_view() {
            ?>
            <div class="ddfw-setup-wizard-ready ddwcpr-onboarding-welcome">
                <div class="ddfw-success-icon-wrap">
                    <svg class="ddfw-success-svg" width="100" height="100" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="15" fill="var(--ddfw-tab-background-color)"/>
                        <path d="M9 21C9 19.8954 9.89543 19 11 19H21C22.1046 19 23 19.8954 23 21V25C23 25.5523 22.5523 26 22 26H10C9.44772 26 9 25.5523 9 25V21Z" fill="var(--ddfw-primary-color)"/>
                        <path d="M10 11C10 9.89543 10.8954 9 12 9H20C21.1046 9 22 9.89543 22 11V19H10V11Z" fill="var(--ddfw-primary-color)" fill-opacity="0.7"/>
                        <rect x="12" y="11" width="2" height="1.5" rx="0.5" fill="white"/>
                        <rect x="15" y="11" width="2" height="1.5" rx="0.5" fill="white"/>
                        <rect x="18" y="11" width="2" height="1.5" rx="0.5" fill="white"/>
                        <rect x="12" y="14" width="2" height="1.5" rx="0.5" fill="white"/>
                        <rect x="15" y="14" width="2" height="1.5" rx="0.5" fill="white"/>
                        <rect x="18" y="14" width="2" height="1.5" rx="0.5" fill="white"/>
                        <circle cx="16" cy="22.5" r="1.2" fill="white"/>
                        <path d="M18 9V4L19 5L20 4L21 5L22 4V9H18Z" fill="var(--ddfw-primary-color)" fill-opacity="0.5"/>
                    </svg>
                </div>
                <h2 class="ddwcpr-onboarding-welcome-title"><?php esc_html_e( 'Welcome to MultiPOS!', 'devdiggers-multipos-for-woocommerce' ); ?></h2>
                <p class="ddwcpr-onboarding-welcome-desc">
                    <?php esc_html_e( 'Let\'s quickly set up your Point of Sale system so you can start selling in your physical store. This wizard will guide you through the essential settings.', 'devdiggers-multipos-for-woocommerce' ); ?>
                </p>
            </div>
            <?php
        }

        /**
         * General settings view
         *
         * @return void
         */
        public function general_settings_view() {
            ?>
            <div class="ddfw-fields-section">
                <table class="form-table">
                    <tbody>
                        <?php
                        $fields = [
                            [
                                'type'           => 'checkbox',
                                'label'          => esc_html__( 'Plugin Status', 'devdiggers-multipos-for-woocommerce' ),
                                'checkbox_label' => esc_html__( 'Enable Point of Sale System', 'devdiggers-multipos-for-woocommerce' ),
                                'description'    => esc_html__( 'Turn on or off the complete Point of Sale functionality across your entire WordPress site.', 'devdiggers-multipos-for-woocommerce' ),
                                'id'             => 'ddwcpos-enabled',
                                'name'           => '_ddwcpos_enabled',
                                'value'          => $this->ddwcpos_configuration['enabled'],
                            ],
                            [
                                'type'        => 'select',
                                'label'       => esc_html__( 'Initial Order Status', 'devdiggers-multipos-for-woocommerce' ),
                                'description' => esc_html__( 'Select the initial status applied to all orders processed through the POS interface.', 'devdiggers-multipos-for-woocommerce' ),
                                'options'     => wc_get_order_statuses(),
                                'id'          => 'ddwcpos-order-status',
                                'name'        => '_ddwcpos_order_status',
                                'value'       => $this->ddwcpos_configuration['order_status'],
                            ],
                            [
                                'type'        => 'select',
                                'label'       => esc_html__( 'Barcode Synchronization', 'devdiggers-multipos-for-woocommerce' ),
                                'description' => esc_html__( 'Select which product property should be used to synchronize and generate barcodes for your store items.', 'devdiggers-multipos-for-woocommerce' ),
                                'options'     => [
                                    'id'  => esc_html__( 'Use Product ID', 'devdiggers-multipos-for-woocommerce' ),
                                    'sku' => esc_html__( 'Use Product SKU', 'devdiggers-multipos-for-woocommerce' ),
                                ],
                                'id'          => 'ddwcpos-default-barcode',
                                'name'        => '_ddwcpos_default_barcode',
                                'value'       => $this->ddwcpos_configuration['default_barcode'],
                            ],
                            [
                                'type'        => 'text',
                                'label'       => esc_html__( 'POS Terminal URL Path', 'devdiggers-multipos-for-woocommerce' ),
                                'description' => esc_html__( 'Define the custom URL slug for your point of sale front-end terminal (e.g., yoursite.com/pos).', 'devdiggers-multipos-for-woocommerce' ),
                                'id'          => 'ddwcpos-endpoint',
                                'name'        => '_ddwcpos_endpoint',
                                'value'       => $this->ddwcpos_configuration['endpoint'],
                            ],
                        ];

                        foreach ( $fields as $field ) {
                            DDFW_Form_Field::display_form_field( $field );
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
        }

        /**
         * Checkout features view
         *
         * @return void
         */
        public function checkout_features_view() {
            ?>
            <div class="ddfw-fields-section">
                <table class="form-table">
                    <tbody>
                        <?php
                        $fields = [
                            [
                                'type'           => 'checkbox',
                                'label'          => esc_html__( 'Order Notifications', 'devdiggers-multipos-for-woocommerce' ),
                                'checkbox_label' => esc_html__( 'Enable Automated Order Mails', 'devdiggers-multipos-for-woocommerce' ),
                                'description'    => esc_html__( 'Automatically trigger standard WooCommerce order emails for both admin and customers upon POS checkout.', 'devdiggers-multipos-for-woocommerce' ),
                                'id'             => 'ddwcpos-order-mails-enabled',
                                'name'           => '_ddwcpos_order_mails_enabled',
                                'value'          => $this->ddwcpos_configuration['order_mails_enabled'],
                            ],
                        ];

                        foreach ( $fields as $field ) {
                            DDFW_Form_Field::display_form_field( $field );
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
        }

        /**
         * Layout settings view
         *
         * @return void
         */
        public function layout_settings_view() {
            ?>
            <div class="ddfw-fields-section">
                <table class="form-table">
                    <tbody>
                        <?php
                        $fields = [
                            [
                                'type'          => 'image',
                                'label'         => esc_html__( 'POS Terminal Branding Logo', 'devdiggers-multipos-for-woocommerce' ),
                                'description'   => esc_html__( 'This logo will appear on receipts and the administrative dashboard. Ideal size: 200x60 pixels.', 'devdiggers-multipos-for-woocommerce' ),
                                'id'            => 'ddwcpos-logo',
                                'name'          => '_ddwcpos_logo',
                                'value'         => $this->ddwcpos_configuration['logo'],
                                'default_image' => DDWCPOS_PLUGIN_URL . 'assets/images/logo.png',
                            ],
                            [
                                'type'        => 'colorpicker',
                                'label'       => esc_html__( 'Primary Brand Color', 'devdiggers-multipos-for-woocommerce' ),
                                'description' => esc_html__( 'Main color used for active states, buttons, and top-level navigation gradients.', 'devdiggers-multipos-for-woocommerce' ),
                                'id'          => 'ddwcpos-layout-primary-color',
                                'name'        => '_ddwcpos_layout_primary_color',
                                'value'       => $this->ddwcpos_configuration['layout_primary_color'],
                            ],
                            [
                                'type'        => 'select',
                                'label'       => esc_html__( 'Product Card Orientation', 'devdiggers-multipos-for-woocommerce' ),
                                'description' => esc_html__( 'Determine the visual stack of product images and their descriptive text metadata.', 'devdiggers-multipos-for-woocommerce' ),
                                'options'     => [
                                    'image_left' => esc_html__( 'Horizontal (Image Left, Text Right)', 'devdiggers-multipos-for-woocommerce' ),
                                    'image_top'  => esc_html__( 'Vertical (Image Top, Text Bottom)', 'devdiggers-multipos-for-woocommerce' ),
                                ],
                                'id'          => 'ddwcpos-product-layout',
                                'name'        => '_ddwcpos_product_layout',
                                'value'       => $this->ddwcpos_configuration['product_layout'],
                            ],
                            [
                                'type'           => 'checkbox',
                                'label'          => esc_html__( 'Real-time Stock Indicators', 'devdiggers-multipos-for-woocommerce' ),
                                'checkbox_label' => esc_html__( 'Display Current Stock Count', 'devdiggers-multipos-for-woocommerce' ),
                                'description'    => esc_html__( 'If enabled, numeric stock availability indicators will be displayed on every product card.', 'devdiggers-multipos-for-woocommerce' ),
                                'id'             => 'ddwcpos-show-product-stock-enabled',
                                'name'           => '_ddwcpos_show_product_stock_enabled',
                                'value'          => $this->ddwcpos_configuration['show_product_stock_enabled'],
                            ],
                        ];

                        foreach ( $fields as $field ) {
                            DDFW_Form_Field::display_form_field( $field );
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
        }

        /**
         * Customer preferences view
         *
         * @return void
         */
        public function customer_preferences_view() {
            ?>
            <div class="ddfw-fields-section">
                <table class="form-table">
                    <tbody>
                        <?php
                        $fields = [
                            [
                                'type'              => 'users',
                                'label'             => esc_html__( 'Default Guest Account', 'devdiggers-multipos-for-woocommerce' ),
                                'description'       => esc_html__( 'Select a WordPress user account to represent all guest/anonymous transactions.', 'devdiggers-multipos-for-woocommerce' ),
                                'id'                => 'ddwcpos-default-customer',
                                'name'              => '_ddwcpos_default_customer',
                                'value'             => $this->ddwcpos_configuration['default_customer'],
                                'custom_attributes' => [
                                    'data-placeholder' => esc_attr__( 'Search for a customer...', 'devdiggers-multipos-for-woocommerce' ),
                                    'data-role'        => 'customer',
                                ],
                            ],
                        ];

                        foreach ( $fields as $field ) {
                            DDFW_Form_Field::display_form_field( $field );
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
        }

        /**
         * Generic save helper
         *
         * @param array $form_data Form data array.
         * @return bool
         */
        public function save_fields( $form_data ) {
            if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
                return new \WP_Error( 'ddwcpos_insufficient_permissions', esc_html__( 'Insufficient permissions.', 'devdiggers-multipos-for-woocommerce' ) );
            }

            $allowed_options = [
                '_ddwcpos_enabled',
                '_ddwcpos_order_status',
                '_ddwcpos_default_barcode',
                '_ddwcpos_endpoint',
                '_ddwcpos_order_mails_enabled',
                '_ddwcpos_logo',
                '_ddwcpos_layout_primary_color',
                '_ddwcpos_product_layout',
                '_ddwcpos_show_product_stock_enabled',
                '_ddwcpos_default_customer',
            ];

            // The JS now explicitly sends unchecked checkboxes with empty values,
            // so we can simply save all submitted fields directly.
            foreach ( $form_data as $field ) {
                if ( in_array( $field['name'], $allowed_options, true ) ) {
                    update_option( $field['name'], $this->sanitize_wizard_field( $field['name'], $field['value'] ) );
                }
            }

            return true;
        }

        /**
         * Sanitize a setup wizard field according to the target option.
         *
         * @param string $field_name Field option name.
         * @param mixed  $field_value Field value.
         * @return mixed
         */
        protected function sanitize_wizard_field( $field_name, $field_value ) {
            switch ( $field_name ) {
                case '_ddwcpos_logo':
                case '_ddwcpos_default_customer':
                    return absint( $field_value );
                case '_ddwcpos_endpoint':
                    return sanitize_title( $field_value );
                case '_ddwcpos_layout_primary_color':
                    return sanitize_hex_color( $field_value );
                default:
                    return sanitize_text_field( $field_value );
            }
        }
    }
}
