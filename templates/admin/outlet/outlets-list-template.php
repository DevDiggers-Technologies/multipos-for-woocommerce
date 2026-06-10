<?php
/**
 * Outlets List Template
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Templates\Admin\Outlet;

use DDWCMultiPOS\Helper\Error\DDWCPOS_Error_Helper;
use DDWCMultiPOS\Helper\Outlet\DDWCPOS_Outlet_Helper;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Outlets_List_Template' ) ) {
	/**
	 * Outlets list class
	 */
	class DDWCPOS_Outlets_List_Template extends \WP_List_table {
		/**
		 * Error Helper Trait
		 */
		use DDWCPOS_Error_Helper;

		/**
		 * Outlet Helper Variable
		 *
		 * @var object
		 */
		protected $outlet_helper;

		/**
		 * Configuration Variable
		 *
		 * @var array
		 */
		protected $ddwcpos_configuration;

		/**
		 * Class constructor
		 */
		public function __construct( $ddwcpos_configuration ) {
			$this->ddwcpos_configuration = $ddwcpos_configuration;
			$this->outlet_helper         = new DDWCPOS_Outlet_Helper();

			parent::__construct( [
				'singular' => esc_html__( 'Outlet List', 'devdiggers-multipos-for-woocommerce' ),
				'plural'   => esc_html__( 'Outlets List', 'devdiggers-multipos-for-woocommerce' ),
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

			$per_page     = $this->get_items_per_page( 'outlets_per_page', 20 );
			$current_page = $this->get_pagenum();
			$total_items  = $this->outlet_helper->ddwcpos_get_all_outlets_count( $search );


			$this->set_pagination_args( [
				'total_items' => $total_items,
				'per_page'    => $per_page,
			] );

			$data = $this->ddwcpos_get_outlets( $per_page, $current_page, $search );

			usort( $data, [ $this, 'usort_reorder' ] );

			$this->items = $data;
		}

		/**
		 * Usort
		 *
		 * @param int $first First value.
		 * @param int $second Second value.
		 * @return int
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
		 * Fetch Outlets
		 *
		 * @param int $per_page Per Page.
		 * @param int $current_page Page.
		 * @param string $search Search.
		 * @return array $data
		 */
		public function ddwcpos_get_outlets( $per_page, $current_page, $search = '' ) {
            $data    = [];
            $offset  = ( $current_page - 1 ) * $per_page;
            $outlets = $this->outlet_helper->ddwcpos_get_all_outlets( $per_page, $offset, $search );
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Page slug is read-only link context.
			$page    = ! empty( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

            if ( ! empty( $outlets ) ) {
				$date_format  = get_option( 'date_format' );
				$time_format  = get_option( 'time_format' );
				$outlet_modes = $this->outlet_helper->ddwcpos_get_outlet_modes();

                foreach ( $outlets as $outlet ) {
                    $address = sprintf(
						/* translators: 1: Address line 1, 2: Address line 2, 3: City, 4: State, 5: Country, 6: Postcode. */
						esc_html__( 'Address 1: %1$s Address 2: %2$s City: %3$s State: %4$s Country: %5$s Postcode: %6$s', 'devdiggers-multipos-for-woocommerce' ),
						esc_html( $outlet[ 'address1' ] ) . '<br />',
						esc_html( $outlet[ 'address2' ] ) . '<br />',
						esc_html( $outlet[ 'city' ] ) . '<br />',
						esc_html( $outlet[ 'state' ] ) . '<br />',
						esc_html( $outlet[ 'country' ] ) . '<br />',
						esc_html( $outlet[ 'postcode' ] ) . '<br />'
					);

                    $status_class = ( 'enabled' === $outlet[ 'status' ] ) ? 'active' : 'inactive';
                    $status_text  = ( 'enabled' === $outlet[ 'status' ] ) ? esc_html__( 'Active', 'devdiggers-multipos-for-woocommerce' ) : esc_html__( 'Inactive', 'devdiggers-multipos-for-woocommerce' );

                    ob_start();
                    ?>
                    <mark class="ddwcpos-status ddwcpos-status-<?php echo esc_attr( $status_class ); ?>"><?php echo esc_html( $status_text ); ?></mark>
                    <?php
                    $status = ob_get_clean();

					ob_start();
					?>
					<a class="button button-primary" href="<?php echo esc_url( site_url( $this->ddwcpos_configuration['endpoint'] . '?outlet_id=' . $outlet['id'] ) ); ?>" target="_blank"><?php esc_html_e( 'Visit POS', 'devdiggers-multipos-for-woocommerce' ); ?></a>
					<?php
					$action = ob_get_clean();

                    $data[] = [
                        'id'          => $outlet[ 'id' ],
                        'outlet_name' => $outlet[ 'name' ],
                        'mode'        => $outlet_modes[ $outlet[ 'mode' ] ],
                        'address'     => $address,
                        'email'       => $outlet[ 'email' ],
                        'phone'       => $outlet[ 'phone' ],
                        'status'      => $status,
                        'created_at'  => date_i18n( $date_format . ' ' . $time_format, strtotime( $outlet[ 'created' ] ) ),
                        'updated_at'  => date_i18n( $date_format . ' ' . $time_format, strtotime( $outlet[ 'updated' ] ) ),
                        'actions'      => $action,
					];
                }
            }

			return apply_filters( 'ddwcpos_outlets_list_data', $data );
		}

		/**
		 * Process bulk actions
		 *
		 * @return void
		 */
		public function process_bulk_action() {
			$nonce = isset( $_GET['ddwcpos_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['ddwcpos_nonce'] ) ) : '';

			if ( wp_verify_nonce( $nonce, 'ddwcpos_nonce_action' ) ) {
				if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
					$this->ddwcpos_print_notification( esc_html__( 'Insufficient permissions.', 'devdiggers-multipos-for-woocommerce' ), 'error' );
					return;
				}

				$action = $this->current_action();

				if ( in_array( $action, [ 'delete' ], true ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verified by the inline wp_verify_nonce() check above.
					if ( ! empty( $_GET[ 'ddwcpos-id' ] ) ) {
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verified by the inline wp_verify_nonce() check above.
						if ( is_array( $_GET[ 'ddwcpos-id' ] ) ) {
							// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verified by the inline wp_verify_nonce() check above.
							$ids     = array_map( 'absint', wp_unslash( $_GET[ 'ddwcpos-id' ] ) );
							$success = $error = 0;

							foreach ( $ids as $id ) {
								$response = $this->outlet_helper->ddwcpos_delete_outlet( $id );
								if ( $response ) {
									$success++;
								} else {
									$error++;
								}
							}

							if ( $success ) {
								/* translators: %d: Number of outlets deleted. */
								$message = sprintf( esc_html__( '%d outlet(s) deleted successfully.', 'devdiggers-multipos-for-woocommerce' ), $success );
								$this->ddwcpos_print_notification( $message );
							}

							if ( $error ) {
								/* translators: %d: Number of outlets not found. */
								$message = sprintf( esc_html__( '%d outlet(s) not exits.', 'devdiggers-multipos-for-woocommerce' ), $error );
								$this->ddwcpos_print_notification( $message, 'error' );
							}
						}
					} else {
						$message = esc_html__( 'Select outlet(s) to delete.', 'devdiggers-multipos-for-woocommerce' );
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
			$nonce = isset( $_GET['ddwcpos_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['ddwcpos_nonce'] ) ) : '';

			if ( wp_verify_nonce( $nonce, 'ddwcpos_nonce_action' ) ) {
				if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
					$this->ddwcpos_print_notification( esc_html__( 'Insufficient permissions.', 'devdiggers-multipos-for-woocommerce' ), 'error' );
					return;
				}

				$action = $this->current_action();

				if ( in_array( $action, [ 'delete' ], true ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verified by the inline wp_verify_nonce() check above.
					if ( ! empty( $_GET[ 'ddwcpos-id' ] ) && ! is_array( $_GET[ 'ddwcpos-id' ] ) ) {
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verified by the inline wp_verify_nonce() check above.
						$id       = absint( wp_unslash( $_GET[ 'ddwcpos-id' ] ) );
						$response = $this->outlet_helper->ddwcpos_delete_outlet( $id );

						if ( $response ) {
							$message = esc_html__( 'Outlet is deleted successfully.', 'devdiggers-multipos-for-woocommerce' );
							$this->ddwcpos_print_notification( $message );
						} else {
							$message = esc_html__( 'Outlet not exists.', 'devdiggers-multipos-for-woocommerce' );
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
			esc_html_e( 'No outlets avaliable.', 'devdiggers-multipos-for-woocommerce' );
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
				'cb'           => '<input type="checkbox" />',
				'id'           => esc_html__( 'ID', 'devdiggers-multipos-for-woocommerce' ),
				'outlet_name'  => esc_html__( 'Name', 'devdiggers-multipos-for-woocommerce' ),
				'address'      => esc_html__( 'Address', 'devdiggers-multipos-for-woocommerce' ),
				'date'         => esc_html__( 'Date', 'devdiggers-multipos-for-woocommerce' ),
				'status'       => esc_html__( 'Status', 'devdiggers-multipos-for-woocommerce' ),
				'actions'      => esc_html__( 'Actions', 'devdiggers-multipos-for-woocommerce' ),
			];

			return apply_filters( 'ddwcpos_outlets_list_columns', $columns );
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
			return apply_filters( 'ddwcpos_outlets_list_sortable_columns', [
				'id'          => [ 'id', true ],
				'outlet_name' => [ 'outlet_name', true ],
				'status'      => [ 'status', true ],
				'date'        => [ 'created_at', true ],
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

		/**
		 * Column Name Actions
		 *
		 * @param array $item Items.
		 * @return string
		 */
		public function column_outlet_name( $item ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Values are used only to build admin action links.
			$search       = ! empty( $_GET[ 's' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 's' ] ) ) : '';
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Values are used only to build admin action links.
			$page         = ! empty( $_GET[ 'page' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'page' ] ) ) : '';
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Values are used only to build admin action links.
			$menu         = ! empty( $_GET[ 'menu' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'menu' ] ) ) : '';
			$current_page = $this->get_pagenum();

			$actions = [
				'edit'   => sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=' . $page . '&menu=' . $menu . '&action=edit&id=' . $item[ 'id' ] ) ), esc_html__( 'Edit', 'devdiggers-multipos-for-woocommerce' ) ),
				'delete' => sprintf( '<a href="%s">%s</a>', wp_nonce_url( admin_url( 'admin.php?page=' . $page . '&menu=' . $menu . '&action=delete&s=' . $search . '&paged=' . $current_page . '&ddwcpos-id=' . $item[ 'id' ] ), 'ddwcpos_nonce_action', 'ddwcpos_nonce' ), esc_html__( 'Delete', 'devdiggers-multipos-for-woocommerce' ) ),
			];

			return sprintf( '<strong>%1$s</strong><br /><small>%2$s</small>%3$s', $item[ 'outlet_name' ], $item[ 'mode' ], $this->row_actions( apply_filters( 'ddwcpos_outlets_list_line_actions', $actions ) ) );
		}

		/**
		 * Column Address
		 *
		 * @param array $item Item.
		 * @return string
		 */
		public function column_address( $item ) {
			$address_html = $item[ 'address' ];
			if ( ! empty( $item[ 'email' ] ) || ! empty( $item[ 'phone' ] ) ) {
				$address_html .= '<br /><small>';
				if ( ! empty( $item[ 'email' ] ) ) {
					$address_html .= '<strong>' . esc_html__( 'Email: ', 'devdiggers-multipos-for-woocommerce' ) . '</strong>' . esc_html( $item[ 'email' ] ) . '<br />';
				}
				if ( ! empty( $item[ 'phone' ] ) ) {
					$address_html .= '<strong>' . esc_html__( 'Phone: ', 'devdiggers-multipos-for-woocommerce' ) . '</strong>' . esc_html( $item[ 'phone' ] );
				}
				$address_html .= '</small>';
			}
			return $address_html;
		}

		/**
		 * Column Date
		 *
		 * @param array $item Item.
		 * @return string
		 */
		public function column_date( $item ) {
			return sprintf( '<strong>%1$s: </strong>%2$s<br /><strong>%3$s: </strong>%4$s', esc_html__( 'Created', 'devdiggers-multipos-for-woocommerce' ), $item[ 'created_at' ], esc_html__( 'Updated', 'devdiggers-multipos-for-woocommerce' ), $item[ 'updated_at' ] );
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
            return apply_filters( 'ddwcpos_modify_bulk_actions_in_outlets', [
                'delete'  => esc_html__( 'Delete', 'devdiggers-multipos-for-woocommerce' ),
			] );
		}
	}
}
