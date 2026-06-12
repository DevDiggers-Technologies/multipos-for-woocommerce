<?php
/**
 * Manage Outlet template class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Templates\Admin\Outlet;

use DevDiggers\Framework\Includes\DDFW_Layout;
use DDWCMultiPOS\Helper\Error\DDWCPOS_Error_Helper;
use DDWCMultiPOS\Helper\Outlet\DDWCPOS_Outlet_Helper;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'DDWCPOS_Manage_Outlet_Template' ) ) {
	/**
	 * Manage Outlet template class
	 */
	class DDWCPOS_Manage_Outlet_Template {
		/**
		 * Error Helper Trait
		 */
		use DDWCPOS_Error_Helper;

		/**
		 * Configuration Variable
		 *
		 * @var array
		 */
		protected $ddwcpos_configuration;

		/**
		 * Outlet helper variable
		 *
		 * @var object
		 */
		protected $outlet_helper;

		/**
		 * Outlet data variable
		 *
		 * @var array
		 */
		protected $outlet_data;

		/**
		 * Construct
		 */
		public function __construct( $ddwcpos_configuration ) {
			$this->ddwcpos_configuration = $ddwcpos_configuration;
			$this->outlet_helper         = new DDWCPOS_Outlet_Helper();
			$this->ddwcpos_perform_save_outlet();

			$this->outlet_data = [
				'id'             => '',
				'name'           => '',
				'mode'           => 'grocery',
				'inventory_type' => '',
				'address1'       => '',
				'address2'       => '',
				'city'           => '',
				'state'          => '',
				'country'        => '',
				'postcode'       => '',
				'phone'          => '',
				'email'          => '',
				'payments'       => [],
				'invoice'        => '',
				'tables'         => [],
				'status'         => 'enabled',
			];

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Outlet ID is read-only routing input.
			if ( ! empty( $_GET[ 'id' ] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Outlet ID is read-only routing input.
				$outlet_data       = $this->outlet_helper->ddwcpos_get_outlet_details_by_id( absint( wp_unslash( $_GET[ 'id' ] ) ) );
				$this->outlet_data = wp_parse_args( $outlet_data, $this->outlet_data );
			}

			$this->outlet_data[ 'payments' ] = maybe_unserialize( $this->outlet_data[ 'payments' ] );
			$this->outlet_data[ 'tables' ]   = ! empty( $this->outlet_data[ 'tables' ] ) ? maybe_unserialize( $this->outlet_data[ 'tables' ] ) : maybe_unserialize( [] );

			$this->ddwcpos_get_manage_outlet_template();
		}

		/**
		 * Perform Save Outlet function
		 *
		 * @return void
		 */
		public function ddwcpos_perform_save_outlet() {
			if ( ! empty( $_POST[ 'ddwcpos_save_outlet_submit' ] ) && ! empty( $_POST[ 'ddwcpos_save_outlet_submit_nonce' ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ 'ddwcpos_save_outlet_submit_nonce' ] ) ), 'ddwcpos_save_outlet_submit_nonce_action' ) ) {
				if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
					$this->ddwcpos_print_notification( esc_html__( 'Insufficient permissions.', 'devdiggers-multipos-for-woocommerce' ), 'error' );
					return;
				}

				$id             = ! empty( $_POST[ 'ddwcpos_outlet_id' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'ddwcpos_outlet_id' ] ) ) : '';
				$name           = ! empty( $_POST[ 'ddwcpos_outlet_name' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'ddwcpos_outlet_name' ] ) ) : '';
				$mode           = ! empty( $_POST[ 'ddwcpos_outlet_mode' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'ddwcpos_outlet_mode' ] ) ) : '';
				$address1       = ! empty( $_POST[ 'ddwcpos_outlet_address1' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'ddwcpos_outlet_address1' ] ) ) : '';
				$address2       = ! empty( $_POST[ 'ddwcpos_outlet_address2' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'ddwcpos_outlet_address2' ] ) ) : '';
				$city           = ! empty( $_POST[ 'ddwcpos_outlet_city' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'ddwcpos_outlet_city' ] ) ) : '';
				$state          = ! empty( $_POST[ 'ddwcpos_outlet_state' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'ddwcpos_outlet_state' ] ) ) : '';
				$country        = ! empty( $_POST[ 'ddwcpos_outlet_country' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'ddwcpos_outlet_country' ] ) ) : '';
				$postcode       = ! empty( $_POST[ 'ddwcpos_outlet_postcode' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'ddwcpos_outlet_postcode' ] ) ) : '';
				$phone          = ! empty( $_POST[ 'ddwcpos_outlet_phone' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'ddwcpos_outlet_phone' ] ) ) : '';
				$email          = ! empty( $_POST[ 'ddwcpos_outlet_email' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'ddwcpos_outlet_email' ] ) ) : '';
				$payments       = ! empty( $_POST[ 'ddwcpos_outlet_payments' ] ) ? maybe_serialize( array_map( 'sanitize_text_field', wp_unslash( $_POST[ 'ddwcpos_outlet_payments' ] ) ) ) : '';
				$invoice        = ! empty( $_POST[ 'ddwcpos_outlet_invoice' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'ddwcpos_outlet_invoice' ] ) ) : '';
				$tables         = ! empty( $_POST[ 'ddwcpos_outlet_tables' ] ) ? maybe_serialize( array_map( 'sanitize_text_field', wp_unslash( $_POST[ 'ddwcpos_outlet_tables' ] ) ) ) : maybe_serialize( [] );
				$status         = ! empty( $_POST[ 'ddwcpos_outlet_status' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'ddwcpos_outlet_status' ] ) ) : '';

				$inventory_type = 'centralized';
				$tables         = 'restaurant' === $mode ? $tables : maybe_serialize( [] );

				if ( ! empty( $name ) && ! empty( $mode ) && ! empty( $address1 ) && ! empty( $city ) && ! empty( $state ) && ! empty( $country ) && ! empty( $postcode ) && ! empty( $phone ) && ! empty( $email ) && ! empty( $payments ) && ! empty( $invoice ) && ! empty( $status ) ) {
					$data = compact( 'id', 'name', 'mode', 'inventory_type', 'address1', 'address2', 'city', 'state', 'country', 'postcode', 'phone', 'email', 'payments', 'invoice', 'tables', 'status' );

					$outlet_id = $this->outlet_helper->ddwcpos_save_outlet( $data );

					if ( $outlet_id ) {
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Page/menu are read-only routing values.
						$page = ! empty( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Page/menu are read-only routing values.
						$menu = ! empty( $_GET['menu'] ) ? sanitize_text_field( wp_unslash( $_GET['menu'] ) ) : '';

						wp_safe_redirect( admin_url( 'admin.php?page=' . $page . '&menu=' . $menu . '&success=saved' ) );
						exit();
					}
				} else {
					$this->ddwcpos_print_notification( esc_html__( 'Fields are either empty or invalid.', 'devdiggers-multipos-for-woocommerce' ), 'error' );
				}
			}
		}

		/**
		 * Get Manage Outlet Template function
		 *
		 * @return void
		 */
		public function ddwcpos_get_manage_outlet_template() {
			$outlet_modes = $this->outlet_helper->ddwcpos_get_outlet_modes();

			extract( $this->outlet_data );

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Action is read-only routing input.
			if ( ! empty( $_REQUEST[ 'action' ] ) && 'edit' === sanitize_text_field( wp_unslash( $_REQUEST[ 'action' ] ) ) ) {
				$heading = esc_html__( 'Edit Outlet', 'devdiggers-multipos-for-woocommerce' );
			} else {
				$heading = esc_html__( 'Add Outlet', 'devdiggers-multipos-for-woocommerce' );
			}

			// Map Enabled/Disabled to Active/Inactive for internal consistency if needed,
			// but keeping existing values to avoid DB mismatch if they are already stored.
			// Actually, the user asked to sync Status style and Active/Inactive logic.
			// The list page uses Active/Inactive now.

			$status_label = ( 'enabled' === $status ) ? 'active' : 'inactive';

			$payment_options = [];
			foreach ( $this->ddwcpos_configuration[ 'payment_method' ] as $value ) {
				if ( 'enabled' === $value[ 'status' ] ) {
					$payment_options[ $value[ 'slug' ] ] = $value[ 'name' ];
				}
			}

			$invoice_options = [];
			foreach ( $this->ddwcpos_configuration[ 'invoices' ] as $value ) {
				if ( 'enabled' === $value[ 'status' ] ) {
					$invoice_options[ $value[ 'slug' ] ] = $value[ 'name' ];
				}
			}

			$table_options = [];
			foreach ( $this->ddwcpos_configuration[ 'tables' ] as $value ) {
				if ( 'enabled' === $value[ 'status' ] ) {
					$table_options[ $value[ 'slug' ] ] = $value[ 'name' ];
				}
			}

			$args = [
				[
					'header' => [
						'heading'             => $heading,
						'back_button_enabled' => true,
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Page is read-only routing input.
						'back_button_url'     => admin_url( 'admin.php?page=' . ( ! empty( $_GET[ 'page' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'page' ] ) ) : 'ddwcpos-dashboard' ) ),
						'description'         => esc_html__( 'Configure and manage your physical store locations. Each outlet can have its own operating mode, inventory tracking, and payment settings.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'        => 'text',
							'label'       => esc_html__( 'Outlet Name', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'A descriptive name to identify this store location (e.g., Downtown Branch).', 'devdiggers-multipos-for-woocommerce' ),
							'value'       => $name,
							'id'          => 'ddwcpos-outlet-name',
							'name'        => 'ddwcpos_outlet_name',
							'required'    => true,
							'after_field_text'      => '<input type="hidden" name="ddwcpos_outlet_id" value="' . $id . '" />',
							'wrapper'     => 'span',
						],
						[
							'type'        => 'select',
							'label'       => esc_html__( 'Operation Mode', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Select how this outlet operates. "Restaurant/Cafe" mode enables additional features like Table management.', 'devdiggers-multipos-for-woocommerce' ),
							'value'       => $mode,
							'id'          => 'ddwcpos-outlet-mode',
							'name'        => 'ddwcpos_outlet_mode',
							'required'    => true,
							'options'     => $outlet_modes,
							'class'       => 'ddwcpos-select2',
						],
					],
				],
				[
					'header' => [
						'heading'     => esc_html__( 'Location & Contact', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Enter the physical address and contact information for this outlet. These details appear on printed invoices and receipts.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'        => 'text',
							'label'       => esc_html__( 'Address Line 1', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Primary street address. This will appear on printed invoices.', 'devdiggers-multipos-for-woocommerce' ),
							'value'       => $address1,
							'id'          => 'ddwcpos-outlet-address1',
							'name'        => 'ddwcpos_outlet_address1',
							'required'    => true,
						],
						[
							'type'        => 'text',
							'label'       => esc_html__( 'Address Line 2', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Apartment, suite, unit, etc. (optional).', 'devdiggers-multipos-for-woocommerce' ),
							'value'       => $address2,
							'id'          => 'ddwcpos-outlet-address2',
							'name'        => 'ddwcpos_outlet_address2',
						],
						[
							'type'        => 'text',
							'label'       => esc_html__( 'City', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'City where the outlet is physically located.', 'devdiggers-multipos-for-woocommerce' ),
							'value'       => $city,
							'id'          => 'ddwcpos-outlet-city',
							'name'        => 'ddwcpos_outlet_city',
							'required'    => true,
						],
						[
							'type'        => 'country',
							'label'       => esc_html__( 'Country', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'The region or country for tax and shipping calculations.', 'devdiggers-multipos-for-woocommerce' ),
							'value'       => $country,
							'id'          => 'ddwcpos-outlet-country',
							'name'        => 'ddwcpos_outlet_country',
							'required'    => true,
						],
						[
							'type'        => 'text',
							'label'       => esc_html__( 'State/Province', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Specific state, province, or county.', 'devdiggers-multipos-for-woocommerce' ),
							'value'       => $state,
							'id'          => 'ddwcpos-outlet-state',
							'name'        => 'ddwcpos_outlet_state',
							'required'    => true,
						],
						[
							'type'        => 'text',
							'label'       => esc_html__( 'Postcode/ZIP', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Postal code for the store location.', 'devdiggers-multipos-for-woocommerce' ),
							'value'       => $postcode,
							'id'          => 'ddwcpos-outlet-postcode',
							'name'        => 'ddwcpos_outlet_postcode',
							'required'    => true,
						],
						[
							'type'        => 'tel',
							'label'       => esc_html__( 'Phone', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Enter the contact phone number for the outlet.', 'devdiggers-multipos-for-woocommerce' ),
							'value'       => $phone,
							'id'          => 'ddwcpos-outlet-phone',
							'name'        => 'ddwcpos_outlet_phone',
							'required'    => true,
						],
						[
							'type'        => 'email',
							'label'       => esc_html__( 'Email', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Enter the contact email for the outlet.', 'devdiggers-multipos-for-woocommerce' ),
							'value'       => $email,
							'id'          => 'ddwcpos-outlet-email',
							'name'        => 'ddwcpos_outlet_email',
							'required'    => true,
						],
					],
				],
				[
					'header' => [
						'heading'     => esc_html__( 'Configuration', 'devdiggers-multipos-for-woocommerce' ),
						'description' => esc_html__( 'Manage logistical settings for this outlet, including payment options, receipt templates, and dining table assignments.', 'devdiggers-multipos-for-woocommerce' ),
					],
					'fields' => [
						[
							'type'              => 'select',
							'label'             => esc_html__( 'Payment Methods', 'devdiggers-multipos-for-woocommerce' ),
							'description'       => esc_html__( 'Choose the payment options that will be available for customers at this POS outlet.', 'devdiggers-multipos-for-woocommerce' ),
							'value'             => $payments,
							'id'                => 'ddwcpos-outlet-payments',
							'name'              => 'ddwcpos_outlet_payments[]',
							'required'          => true,
							'options'           => $payment_options,
							'custom_attributes' => [
								'multiple' => 'multiple',
							],
						],
						[
							'type'        => 'select',
							'label'       => esc_html__( 'Invoice Template', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'The design layout for printed customer receipts at this location.', 'devdiggers-multipos-for-woocommerce' ),
							'value'       => $invoice,
							'id'          => 'ddwcpos-outlet-invoice',
							'name'        => 'ddwcpos_outlet_invoice',
							'required'    => true,
							'options'     => $invoice_options,
							'class'       => 'ddwcpos-select2',
						],
						[
							'type'              => 'select',
							'label'             => esc_html__( 'Dining Tables', 'devdiggers-multipos-for-woocommerce' ),
							'description'       => esc_html__( 'Select the active tables for this outlet. (Applicable only in "Restaurant/Cafe" mode).', 'devdiggers-multipos-for-woocommerce' ),
							'value'             => $tables,
							'id'                => 'ddwcpos-outlet-tables',
							'name'              => 'ddwcpos_outlet_tables[]',
							'options'           => $table_options,
							'custom_attributes' => [
								'multiple' => 'multiple',
							],
						],
						[
							'type'     => 'select',
							'label'    => esc_html__( 'Outlet Status', 'devdiggers-multipos-for-woocommerce' ),
							'description' => esc_html__( 'Set to "Inactive" to temporarily prevent POS access for this store.', 'devdiggers-multipos-for-woocommerce' ),
							'value'    => $status,
							'id'       => 'ddwcpos-outlet-status',
							'name'     => 'ddwcpos_outlet_status',
							'required' => true,
							'options'  => [
								'enabled'  => esc_html__( 'Active', 'devdiggers-multipos-for-woocommerce' ),
								'disabled' => esc_html__( 'Inactive', 'devdiggers-multipos-for-woocommerce' ),
							],
							'class'    => 'ddwcpos-select2',
						],
					],
					'submit_button' => [
						'name'  => 'ddwcpos_save_outlet_submit',
						'value' => esc_html__( 'Save Outlet', 'devdiggers-multipos-for-woocommerce' ),
					],
				],
			];

			$layout = new DDFW_Layout();
			$layout->get_form_section_layout( $args );
		}
	}
}
