<?php
/**
 * Login Configuration template class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Templates\Admin\Configuration;

use DevDiggers\Framework\Includes\DDFW_Layout;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Login_Configuration_Template' ) ) {
	/**
	 * Login Configuration template class
	 */
	class DDWCPOS_Login_Configuration_Template {
		/**
		 * Construct
		 * 
		 * @param array $ddwcpos_configuration
		 */
		public function __construct( $ddwcpos_configuration ) {
			$args = [
				[
					'header' => [
						'heading'     => esc_html__( 'Login Text', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Edit text shown on POS login screen.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'        => 'text',
							'label'       => esc_html__( 'Welcome Heading', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Main title at top of login screen.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-login-heading-text',
							'value'       => $ddwcpos_configuration['login_heading_text'],
						],
						[
							'type'        => 'text',
							'label'       => esc_html__( 'Welcome Subtitle', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Short line shown below heading.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-login-subtitle-text',
							'value'       => $ddwcpos_configuration['login_subtitle_text'],
						],
						[
							'type'        => 'text',
							'label'       => esc_html__( 'Footer', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Text shown at bottom of login screen.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-login-footer-text',
							'value'       => $ddwcpos_configuration['login_footer_text'],
						],
						[
							'type'        => 'text',
							'label'       => esc_html__( 'Log in Button Label', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Text shown on main login button.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-login-button-text',
							'value'       => $ddwcpos_configuration['login_button_text'],
						],
						[
							'type'           => 'checkbox',
							'label'          => esc_html__( 'MultiPOS Branding', 'devdiggers-multipos-for-woocommerce' ),
							'checkbox_label' => esc_html__( 'Show MultiPOS branding link', 'devdiggers-multipos-for-woocommerce' ),
							'description'    => esc_html__( 'Display the MultiPOS product link on the POS login screen.', 'devdiggers-multipos-for-woocommerce' ),
							'id'             => 'ddwcpos-login-branding-enabled',
							'value'          => $ddwcpos_configuration['login_branding_enabled'],
							'field_class'    => [ 'ddfw-upgrade-to-pro-tag-wrapper' ],
							'custom_attributes' => [
								'disabled' => 'disabled',
							],
							'after_field_text' => '<input type="hidden" name="_ddwcpos_login_branding_enabled" value="yes" />',
						],
					],
				],
				[
					'header' => [
						'heading'     => esc_html__( 'Login Access', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Choose login and session options for cashiers.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'           => 'checkbox',
							'label'          => esc_html__( 'Extended Session Access', 'devdiggers-multipos-for-woocommerce' ),
							'checkbox_label' => esc_html__( 'Enable "Remember Me" Option', 'devdiggers-multipos-for-woocommerce' ),
							'description'    => esc_html__( 'Let cashiers stay signed in on trusted devices.', 'devdiggers-multipos-for-woocommerce' ),
							'id'             => 'ddwcpos-login-rememberme-enabled',
							'value'          => $ddwcpos_configuration['login_rememberme_enabled'],
						],
						[
							'type'           => 'checkbox',
							'label'          => esc_html__( 'Password Recovery', 'devdiggers-multipos-for-woocommerce' ),
							'checkbox_label' => esc_html__( 'Enable Forgot Password Flow', 'devdiggers-multipos-for-woocommerce' ),
							'description'    => esc_html__( 'Show forgot password link on login screen.', 'devdiggers-multipos-for-woocommerce' ),
							'id'             => 'ddwcpos-login-forgot-enabled',
							'value'          => $ddwcpos_configuration['login_forgot_enabled'],
						],
					],
				],
				[
					'header' => [
						'heading'     => esc_html__( 'Login Colors', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Set colors used on login screen.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'        => 'colorpicker',
							'label'       => esc_html__( 'Mesh Gradient (Color 1)', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Main background accent color.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-login-bg-primary-color',
							'value'       => $ddwcpos_configuration['login_bg_primary_color'],
						],
						[
							'type'        => 'colorpicker',
							'label'       => esc_html__( 'Mesh Gradient (Color 2)', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Second background accent color.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-login-bg-secondary-color',
							'value'       => $ddwcpos_configuration['login_bg_secondary_color'],
						],
						[
							'type'        => 'colorpicker',
							'label'       => esc_html__( 'Canvas Base Color', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Base color for full login page.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-login-canvas-bg-color',
							'value'       => $ddwcpos_configuration['login_canvas_bg_color'],
						],
						[
							'type'        => 'colorpicker',
							'label'       => esc_html__( 'Card Backdrop Color', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Background color for login card.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-login-card-bg-color',
							'value'       => $ddwcpos_configuration['login_card_bg_color'],
						],
						[
							'type'        => 'colorpicker',
							'label'       => esc_html__( 'Font Color', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Text color for headings and labels.', 'devdiggers-multipos-for-woocommerce' ),
							'id'          => 'ddwcpos-login-font-color',
							'value'       => $ddwcpos_configuration['login_font_color'],
						],
					],
				],
			];

			$layout = new DDFW_Layout();
			$layout->get_form_section_layout( $args, 'ddwcpos-login-configuration-fields' );
		}
	}
}
