<?php
/**
 * Cashiers List Template
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Templates\Admin\Cashier;

use DDWCMultiPOS\Helper\Error\DDWCPOS_Error_Helper;
use DDWCMultiPOS\Helper\Outlet\DDWCPOS_Outlet_Helper;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Cashiers_List_Template' ) ) {
	/**
	 * Cashiers list class
	 */
	class DDWCPOS_Cashiers_List_Template extends \WP_List_table {
		/**
		 * Error Helper Trait
		 */
		use DDWCPOS_Error_Helper;

		/**
		 * Class constructor
		 */
		public function __construct() {
			parent::__construct( [
				'singular' => esc_html__( 'Cashier List', 'devdiggers-multipos-for-woocommerce' ),
				'plural'   => esc_html__( 'Cashiers List', 'devdiggers-multipos-for-woocommerce' ),
				'ajax'     => false,
			] );
		}

		/**
		 * Prepare Items
		 *
		 * @return void
		 */
		public function prepare_items() {
			$this->_column_headers = $this->get_column_info();

			$http_host   = ! empty( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
			$request_uri = ! empty( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
			$current_url = set_url_scheme( 'http://' . $http_host . $request_uri );

			if ( strpos( $current_url, '_wp_http_referer' ) !== false ) {
				$new_url = remove_query_arg( [ '_wp_http_referer', '_wpnonce' ], stripslashes( $current_url ) );
				wp_safe_redirect( $new_url );
				exit();
			}

			$this->process_bulk_action();
			$this->process_row_action();

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- List table search is read-only.
			$search = ! empty( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

			$per_page     = $this->get_items_per_page( 'cashiers_per_page', 20 );
			$current_page = $this->get_pagenum();
			$total_items  = $this->ddwcpos_cashiers_count( $search );

			$this->set_pagination_args( [
				'total_items' => $total_items,
				'per_page'    => $per_page,
			] );

			$data = $this->ddwcpos_get_cashiers( $per_page, $current_page, $search );

			usort( $data, [ $this, 'usort_reorder' ] );

			$this->items = $data;
		}

		/**
		 * Usort
		 *
		 * @param int $first First value.
		 * @param int $second Second value.
		 * @return $result
		 */
		public function usort_reorder( $first, $second ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- List table sorting is read-only.
			$orderby = ! empty( $_GET[ 'orderby' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'orderby' ] ) ) : 'id';
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- List table sorting is read-only.
			$order   = ! empty( $_GET[ 'order' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'order' ] ) ) : 'desc';
			$result  = strnatcmp( $first[ $orderby ], $second[ $orderby ] );

			return 'asc' === $order ? $result : -$result;
		}

		/**
		 * Verify Nonce
		 *
		 * @return boolean
		 */
		protected function ddwcpos_verify_nonce() {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce is being verified here.
			return ! empty( $_GET[ 'ddwcpos_nonce' ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET[ 'ddwcpos_nonce' ] ) ), 'ddwcpos_nonce_action' );
		}

		/**
		 * Record count
		 *
		 * @param string $search Search.
		 * @return $count
		 */
		public function ddwcpos_cashiers_count( $search = '' ) {
			$user_query = new \WP_User_Query( [
                'role__in'       => apply_filters( 'ddwcpos_allowed_roles_for_pos', [ 'ddwcpos_cashier', 'administrator', 'shop_manager' ] ),
                'search'         => '*' . esc_attr( $search ) . '*',
                'search_columns' => [ 'user_nicename', 'ID', 'user_login', 'user_email' ],
			] );

			wp_reset_postdata();

            return $user_query->get_total();
		}

		/**
		 * Fetch Users
		 *
		 * @param int $per_page Per Page.
		 * @param int $current_page Page.
		 * @param string $search Search.
		 * @return array $users
		 */
		public function ddwcpos_get_cashiers( $per_page, $current_page, $search = '' ) {
            $data = [];

            $off = ( $current_page - 1 ) * $per_page;

			$query = new \WP_User_Query( [
                'role__in'       => apply_filters( 'ddwcpos_allowed_roles_for_pos', [ 'ddwcpos_cashier', 'administrator', 'shop_manager' ] ),
                'number'         => $per_page,
                'offset'         => $off,
                'order'          => 'DESC',
                'orderby'        => 'ID',
                'search'         => '*' . esc_attr( $search ) . '*',
                'search_columns' => [ 'user_nicename', 'ID', 'user_login', 'user_email' ],
                'fields'         => [ 'ID', 'user_login', 'user_email' ],
			] );

			wp_reset_postdata();

            $cashiers = $query->get_results();

            if ( ! empty( $cashiers ) ) {
				global $wp_roles;
				$outlet_helper = new DDWCPOS_Outlet_Helper();
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Page slug is read-only link context.
				$page          = ! empty( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

                foreach ( $cashiers as $cashier ) {
					$cashier_id       = $cashier->ID;
					$assigned_outlets = get_user_meta( $cashier_id, '_ddwcpos_assigned_outlets', true );
					$user_data        = get_userdata( $cashier_id );

					if ( ! empty( $assigned_outlets ) ) {
						$assigned_outlets = array_map( function( $assigned_outlet ) use ( $outlet_helper, $page ) {
							$outlet_details = $outlet_helper->ddwcpos_get_outlet_details_by_id( $assigned_outlet );
							ob_start();
							?>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $page . '&menu=product-stocks&outlet-id=' . $assigned_outlet ) ) ?>" class="button button-primary" target="_blank"><?php echo esc_html( $outlet_details[ 'name' ] ); ?></a>
							<?php
							return ob_get_clean();
						}, $assigned_outlets );

						$assigned_outlets = implode( '', $assigned_outlets );
					} else {
						if ( in_array( 'administrator', $user_data->roles, true ) || in_array( 'shop_manager', $user_data->roles, true ) || apply_filters( 'ddwcpos_allow_administrator_access_for_pos_to_user', false ) ) {
							$assigned_outlets = esc_html__( 'All Outlets', 'devdiggers-multipos-for-woocommerce' );
						} else {
							$assigned_outlets = 'N/A';
						}
					}

					$roles = array_map( function( $role ) use ( $wp_roles ) {
						return $wp_roles->roles[ $role ][ 'name' ];
					}, $user_data->roles );

                    $data[] = [
                        'id'               => $cashier_id,
                        'username'         => $cashier->user_login,
                        'email'            => $cashier->user_email,
                        'role'             => implode( ', ', $roles ),
                        'assigned_outlets' => $assigned_outlets,
					];
                }
            }

			return apply_filters( 'ddwcpos_cashiers_list_data', $data );
		}

		/**
		 * Process bulk actions
		 *
		 * @return void
		 */
		public function process_bulk_action() {
			if ( $this->ddwcpos_verify_nonce() ) {
				$action = $this->current_action();

				if ( in_array( $action, [ 'delete' ], true ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verified by ddwcpos_verify_nonce().
					if ( ! empty( $_GET[ 'ddwcpos-id' ] ) ) {
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verified by ddwcpos_verify_nonce().
						if ( is_array( $_GET[ 'ddwcpos-id' ] ) ) {
							// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verified by ddwcpos_verify_nonce().
							$ids     = array_map( 'absint', wp_unslash( $_GET[ 'ddwcpos-id' ] ) );
							$success = $error = 0;

							foreach ( $ids as $id ) {
								if ( ! current_user_can( 'delete_user', $id ) ) {
									$error++;
									continue;
								}

								$response = wp_delete_user( $id );
								if ( $response ) {
									$success++;
								} else {
									$error++;
								}
							}

							if ( $success ) {
								/* translators: %d: Number of cashiers deleted. */
								$message = sprintf( esc_html__( '%d cashier(s) deleted successfully.', 'devdiggers-multipos-for-woocommerce' ), $success );
								$this->ddwcpos_print_notification( $message );
							}

							if ( $error ) {
								/* translators: %d: Number of cashiers not found. */
								$message = sprintf( esc_html__( '%d cashier(s) not exists.', 'devdiggers-multipos-for-woocommerce' ), $error );
								$this->ddwcpos_print_notification( $message, 'error' );
							}
						}
					} else {
						$message = esc_html__( 'Select cashier(s) to delete.', 'devdiggers-multipos-for-woocommerce' );
						$this->ddwcpos_print_notification( $message, 'error' );
					}
				}
			}
		}

		/**
		 * Process row actions
		 *
		 * @return void
		 */
		public function process_row_action() {
			if ( $this->ddwcpos_verify_nonce() ) {
				$action = $this->current_action();

				if ( in_array( $action, [ 'delete' ], true ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verified by ddwcpos_verify_nonce().
					if ( ! empty( $_GET[ 'ddwcpos-id' ] ) && ! is_array( $_GET[ 'ddwcpos-id' ] ) ) {
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verified by ddwcpos_verify_nonce().
						$id       = absint( wp_unslash( $_GET[ 'ddwcpos-id' ] ) );
						if ( ! current_user_can( 'delete_user', $id ) ) {
							$this->ddwcpos_print_notification( esc_html__( 'Insufficient permissions.', 'devdiggers-multipos-for-woocommerce' ), 'error' );
							return;
						}

						$response = wp_delete_user( $id );

						if ( $response ) {
							$message = esc_html__( 'Cashier is deleted successfully.', 'devdiggers-multipos-for-woocommerce' );
							$this->ddwcpos_print_notification( $message );
						} else {
							$message = esc_html__( 'Cashier not exists.', 'devdiggers-multipos-for-woocommerce' );
							$this->ddwcpos_print_notification( $message, 'error' );
						}
					}
				}
			}
		}

		/**
		 *  No items
		 *
		 * @return void
		 */
		public function no_items() {
			esc_html_e( 'No cashiers avaliable.', 'devdiggers-multipos-for-woocommerce' );
		}

		/**
		 * Hidden Columns
		 *
		 * @return array
		 */
		public function get_hidden_columns() {
			return [];
		}

		/**
		 *  Associative array of columns
		 *
		 * @return array
		 */
		public function get_columns() {
			$columns = [
				'cb'               => '<input type="checkbox" />',
				'id'               => esc_html__( 'ID', 'devdiggers-multipos-for-woocommerce' ),
				'username'         => esc_html__( 'Username', 'devdiggers-multipos-for-woocommerce' ),
				'role'             => esc_html__( 'Role', 'devdiggers-multipos-for-woocommerce' ),
				'assigned_outlets' => esc_html__( 'Assigned Outlets', 'devdiggers-multipos-for-woocommerce' ),
			];

			return apply_filters( 'ddwcpos_cashiers_list_columns', $columns );
		}

		/**
		 * Render a column when no column specific method exists.
		 *
		 * @param array  $item Items.
		 * @param string $column_name Name.
		 *
		 * @return mixed
		 */
		public function column_default( $item, $column_name ) {
			if ( array_key_exists( $column_name, $item ) ) {
				return $item[ $column_name ];
			}

			return '';
		}

		/**
		 * Columns to make sortable.
		 *
		 * @return array
		 */
		public function get_sortable_columns() {
			return apply_filters( 'ddwcpos_cashiers_list_sortable_columns', [
				'id'       => [ 'id', true ],
				'username' => [ 'username', true ],
				'role'     => [ 'role', true ],
			] );
		}

		/**
		 * Render the bulk edit checkbox
		 *
		 * @param array $item Item.
		 * @return string
		 */
		public function column_cb( $item ) {
			return sprintf( '<input type="checkbox" name="ddwcpos-id[]" value="%d" />', esc_attr( $item[ 'id' ] ) );
		}

		public function column_username( $item ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Values are used only to build admin action links.
			$page         = ! empty( $_GET[ 'page' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'page' ] ) ) : '';
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Values are used only to build admin action links.
			$menu         = ! empty( $_GET[ 'menu' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'menu' ] ) ) : '';
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Values are used only to build admin action links.
			$search       = ! empty( $_GET[ 's' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 's' ] ) ) : '';
			$current_page = $this->get_pagenum();

			$actions = [
				'edit'   => sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'user-edit.php?user_id=' . $item[ 'id' ] ) ), esc_html__( 'Edit', 'devdiggers-multipos-for-woocommerce' ) ),
				'delete' => sprintf( '<a href="%s">%s</a>', wp_nonce_url( admin_url( 'admin.php?page=' . $page . '&menu=' . $menu . '&action=delete&s=' . $search . '&paged=' . $current_page . '&ddwcpos-id=' . $item[ 'id' ] ), 'ddwcpos_nonce_action', 'ddwcpos_nonce' ), esc_html__( 'Delete', 'devdiggers-multipos-for-woocommerce' ) ),
			];

			return sprintf( '<strong>%1$s</strong><br /><small>%2$s</small>%3$s', $item[ 'username' ], $item[ 'email' ], $this->row_actions( apply_filters( 'ddwcpos_cashiers_list_line_actions', $actions ) ) );
		}

		/**
		 * Column ID
		 *
		 * @param array $item Item.
		 * @return string
		 */
		public function column_id( $item ) {
			return $item[ 'id' ];
		}

		/**
         * Bulk actions on list.
		 *
		 * @return array
         */
        public function get_bulk_actions() {
            return apply_filters( 'ddwcpos_modify_bulk_actions_in_cashiers', [
                'delete'  => esc_html__( 'Delete', 'devdiggers-multipos-for-woocommerce' ),
			] );
		}
	}
}
