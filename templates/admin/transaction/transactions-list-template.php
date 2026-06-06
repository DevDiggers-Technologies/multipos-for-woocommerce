<?php
/**
 * Transactions List Template
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Templates\Admin\Transaction;

use DDWCMultiPOS\Helper\Error\DDWCPOS_Error_Helper;
use DDWCMultiPOS\Helper\Transaction\DDWCPOS_Transaction_Helper;
use DDWCMultiPOS\Helper\Outlet\DDWCPOS_Outlet_Helper;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Transactions_List_Template' ) ) {
	/**
	 * Transactions list class
	 */
	class DDWCPOS_Transactions_List_Template extends \WP_List_table {
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
		 * Transactions Helper Variable
		 *
		 * @var object
		 */
		protected $transaction_helper;

        /**
		 * Outlet Helper Variable
		 *
		 * @var object
		 */
		protected $outlet_helper;

		/**
		 * Class constructor
		 */
		public function __construct( $ddwcpos_configuration ) {
            $this->ddwcpos_configuration = $ddwcpos_configuration;
            $this->transaction_helper    = new DDWCPOS_Transaction_Helper();
            $this->outlet_helper         = new DDWCPOS_Outlet_Helper();

			parent::__construct( [
				'singular' => esc_html__( 'Transaction List', 'devdiggers-multipos-for-woocommerce' ),
				'plural'   => esc_html__( 'Transactions List', 'devdiggers-multipos-for-woocommerce' ),
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

			$per_page     = $this->get_items_per_page( 'transactions_per_page', 20 );
			$current_page = $this->get_pagenum();
			$total_items  = $this->transaction_helper->ddwcpos_get_all_transactions_count( $search );

			$this->set_pagination_args( [
				'total_items' => $total_items,
				'per_page'    => $per_page,
			] );

			$data = $this->ddwcpos_get_data( $per_page, $current_page, $search );

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
			$orderby = ! empty( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'id';
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- List table sorting is read-only.
			$order   = ! empty( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'desc';
			$result  = strnatcmp( $first[$orderby], $second[$orderby] );

			return 'asc' === $order ? $result : -$result;
		}

		/**
		 * Fetch data
		 *
		 * @param int $per_page Per Page.
		 * @param int $current_page Page.
		 * @param string $search Search.
		 * @return array $data
		 */
		public function ddwcpos_get_data( $per_page, $current_page, $search = '' ) {
            $data = [];

            $offset = ( $current_page - 1 ) * $per_page;

            $transactions = $this->transaction_helper->ddwcpos_get_all_transactions( $per_page, $offset, $search );

            if ( ! empty( $transactions ) ) {
				$date_format = get_option( 'date_format' );
				$time_format = get_option( 'time_format' );

                foreach ( $transactions as $transaction ) {
                    $cashier_id     = $transaction[ 'cashier_id' ];
                    $cashier        = get_user_by( 'ID', $cashier_id );
                    $email          = $cashier->user_email . ' (#' . $cashier_id . ')';
                    $outlet_details = $this->outlet_helper->ddwcpos_get_outlet_details_by_id( $transaction[ 'outlet_id' ] );

					if ( 'opencash' === $transaction[ 'method' ] ) {
						$method = esc_html__( 'Open Cash Drawer Amount', 'devdiggers-multipos-for-woocommerce' );
					} elseif ( 'manual' === $transaction[ 'method' ] ) {
						$method = esc_html__( 'Manual', 'devdiggers-multipos-for-woocommerce' );
					} elseif ( 'split' === $transaction[ 'method' ] ) {
						$method = esc_html__( 'Split', 'devdiggers-multipos-for-woocommerce' );
					} elseif ( 'refund' === $transaction[ 'method' ] ) {
						$method = esc_html__( 'Refund', 'devdiggers-multipos-for-woocommerce' );
					} else {
						$method = array_filter( $this->ddwcpos_configuration[ 'payment_method' ], function( $payment_method ) use ( $transaction ) {
							return $payment_method[ 'slug' ] === $transaction[ 'method' ];
						} );

						$method = array_values( $method );

						$method = ! empty( $method ) ? $method[ 0 ][ 'name' ] : $transaction[ 'method' ];
					}

                    $data[] = [
                        'id'        => $transaction[ 'id' ],
                        'cashier'   => $cashier ? ( ! empty( $cashier->display_name ) ? $cashier->display_name : $cashier->user_login ) : '-',
                        'cashier_link' => $cashier ? admin_url( 'user-edit.php?user_id=' . $cashier_id ) : '#',
                        'outlet'    => ! empty( $outlet_details[ 'name' ] ) ? $outlet_details[ 'name' ] : '',
                        'order_id'  => ! empty( $transaction[ 'order_id' ] ) ? $transaction[ 'order_id' ] : '',
                        'order_link' => ! empty( $transaction[ 'order_id' ] ) ? admin_url( 'post.php?action=edit&post=' . $transaction[ 'order_id' ] ) : '',
                        'in'        => $transaction[ 'in' ],
                        'out'       => $transaction[ 'out' ],
                        'method'    => $method,
                        'reference' => ! empty( $transaction[ 'reference' ] ) ? $transaction[ 'reference' ] : '',
                        'date'      => date_i18n( $date_format . ' ' . $time_format, strtotime( $transaction[ 'date' ] ) ),
					];
                }
            }

			return apply_filters( 'ddwcpos_transactions_list_data', $data );
		}

		/**
		 * Process bulk actions
		 *
		 * @return void
		 */
		public function process_bulk_action() {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce is sanitized and verified below.
			if ( ! empty( $_GET[ 'ddwcpos_nonce' ] ) && wp_unslash( $_GET[ 'ddwcpos_nonce' ] ) ) { // WPCS: CSRF ok. // WPCS: input var ok. // WPCS: sanitization ok.
				$nonce = sanitize_text_field( wp_unslash( $_GET['ddwcpos_nonce'] ) );
				if ( wp_verify_nonce( $nonce, 'ddwcpos_nonce_action' ) ) {
					if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
						$this->ddwcpos_print_notification( esc_html__( 'Insufficient permissions.', 'devdiggers-multipos-for-woocommerce' ), 'error' );
						return;
					}

					$action = $this->current_action();

					if ( in_array( $action, [ 'delete' ] ) ) {
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verified above.
						if ( ! empty( $_GET[ 'ddwcpos-id' ] ) ) { // WPCS: input var ok.
							// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verified above.
							if ( is_array( $_GET[ 'ddwcpos-id' ] ) ) { // WPCS: input var ok.
								// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verified above.
								$ids = array_map( 'sanitize_text_field', wp_unslash( $_GET[ 'ddwcpos-id' ] ) ); // WPCS: input var ok.

								$success = $error = 0;

								foreach ( $ids as $id ) {
                                    $response = $this->transaction_helper->ddwcpos_delete_transaction( $id );
									if ( $response ) {
										$success++;
									} else {
										$error++;
									}
								}

								if ( $success ) {
									/* translators: %d: Number of transactions deleted. */
									$message = sprintf( esc_html__( '%d Transaction(s) deleted successfully.', 'devdiggers-multipos-for-woocommerce' ), $success );
									$this->ddwcpos_print_notification( $message );
								}

								if ( $error ) {
									/* translators: %d: Number of transactions not found. */
									$message = sprintf( esc_html__( '%d Transaction(s) not exits.', 'devdiggers-multipos-for-woocommerce' ), $error );
									$this->ddwcpos_print_notification( $message, 'error' );
								}
							}
						} else {
							$message = esc_html__( 'Select transaction(s) to delete.', 'devdiggers-multipos-for-woocommerce' );
							$this->ddwcpos_print_notification( $message, 'error' );
						}
					}
				} else {
					$message = esc_html__( 'Invalid nonce. Security check failed!!!', 'devdiggers-multipos-for-woocommerce' );
					$this->ddwcpos_print_notification( $message, 'error' );
				}
			}
		}

		/**
		 * Process row actions
		 *
		 * @return void
		 */
		public function process_row_action() {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce is sanitized and verified below.
			if ( ! empty( $_GET[ 'ddwcpos_nonce' ] ) && wp_unslash( $_GET[ 'ddwcpos_nonce' ] ) ) { // WPCS: CSRF ok. // WPCS: input var ok. // WPCS: sanitization ok.
				$nonce = sanitize_text_field( wp_unslash( $_GET['ddwcpos_nonce'] ) );
				if ( wp_verify_nonce( $nonce, 'ddwcpos_nonce_action' ) ) {
					if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
						$this->ddwcpos_print_notification( esc_html__( 'Insufficient permissions.', 'devdiggers-multipos-for-woocommerce' ), 'error' );
						return;
					}

					$action = $this->current_action();

					if ( in_array( $action, [ 'delete' ] ) ) {
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verified above.
						if ( ! empty( $_GET[ 'ddwcpos-id' ] ) && ! is_array( $_GET[ 'ddwcpos-id' ] ) ) { // WPCS: input var ok.
							// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verified above.
							$id       = intval( wp_unslash( $_GET[ 'ddwcpos-id' ] ) );       // WPCS: input var ok.
							$response = $this->transaction_helper->ddwcpos_delete_transaction( $id );

							if ( $response ) {
								$message = esc_html__( 'Transaction is deleted successfully.', 'devdiggers-multipos-for-woocommerce' );
								$this->ddwcpos_print_notification( $message );
							} else {
								$message = esc_html__( 'Transaction not exists.', 'devdiggers-multipos-for-woocommerce' );
								$this->ddwcpos_print_notification( $message, 'error' );
							}
						}
					}
				} else {
					$message = esc_html__( 'Invalid nonce. Security check failed!!!', 'devdiggers-multipos-for-woocommerce' );
					$this->ddwcpos_print_notification( $message, 'error' );
				}
			}
		}

		/**
		 *  No items
		 *
		 * @return void
		 */
		public function no_items() {
			esc_html_e( 'No transactions avaliable.', 'devdiggers-multipos-for-woocommerce' );
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
			return apply_filters( 'ddwcpos_transactions_list_columns', [
				'cb'             => '<input type="checkbox" />',
				'identification' => esc_html__( 'ID & Date', 'devdiggers-multipos-for-woocommerce' ),
				'attribution'    => esc_html__( 'Staff Attribution', 'devdiggers-multipos-for-woocommerce' ),
				'context'        => esc_html__( 'Transaction Context', 'devdiggers-multipos-for-woocommerce' ),
				'amount'         => esc_html__( 'Financial Totals', 'devdiggers-multipos-for-woocommerce' ),
			] );
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
			return apply_filters( 'ddwcpos_transactions_list_sortable_columns', [
				'identification' => [ 'id', true ],
				'amount'         => [ 'in', true ],
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

		public function column_identification( $item ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Value is used only to build admin action links.
			$search       = ! empty( $_GET[ 's' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 's' ] ) ) : '';
			$current_page = $this->get_pagenum();
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Value is used only to build admin action links.
			$page         = ! empty( $_REQUEST[ 'page' ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ 'page' ] ) ) : '';

			$actions = [
				'delete' => sprintf( '<a href="%s">%s</a>', esc_url( wp_nonce_url( admin_url( 'admin.php?page=' . $page . '&action=delete&s=' . $search . '&paged=' . $current_page . '&ddwcpos-id=' . $item[ 'id' ] ), 'ddwcpos_nonce_action', 'ddwcpos_nonce' ) ), esc_html__( 'Delete', 'devdiggers-multipos-for-woocommerce' ) ),
			];

			return sprintf(
				'<div class="ddwcpos-transaction-id-column">
					<div class="ddwcpos-transaction-id">#%1$d</div>
					<div class="ddwcpos-transaction-date">%2$s</div>
					%3$s
				</div>',
				$item[ 'id' ],
				$item[ 'date' ],
				$this->row_actions( apply_filters( 'ddwcpos_transactions_list_line_actions', $actions ) )
			);
		}

		/**
		 * Attribution Column
		 *
		 * @param array $item Item.
		 * @return string
		 */
		public function column_attribution( $item ) {
			return sprintf(
				'<div class="ddwcpos-attribution-path">
					<span class="dashicons dashicons-store"></span>
					<span class="ddwcpos-path-outlet">%1$s</span>
					<span class="dashicons dashicons-arrow-right-alt2"></span>
					<span class="ddwcpos-path-cashier"><a href="%2$s">%3$s</a></span>
				</div>',
				$item[ 'outlet' ],
				esc_url( $item[ 'cashier_link' ] ),
				esc_html( $item[ 'cashier' ] )
			);
		}

		/**
		 * Context Column
		 *
		 * @param array $item Item.
		 * @return string
		 */
		public function column_context( $item ) {
			$order_html = ! empty( $item[ 'order_link' ] ) ? sprintf( '<a href="%1$s">#%2$s</a>', esc_url( $item[ 'order_link' ] ), esc_html( $item[ 'order_id' ] ) ) : '-';

			return sprintf(
				'<div class="ddwcpos-transaction-context-column">
					<div class="ddwcpos-method-tier">
						<mark class="ddwcpos-method-badge method-%1$s">%2$s</mark>
					</div>
					<div class="ddwcpos-order-tier">
						<span class="ddwcpos-label-mini">%3$s</span> %4$s
					</div>
					<div class="ddwcpos-reference-tier">
						<span class="ddwcpos-label-mini">%5$s</span> <em>%6$s</em>
					</div>
				</div>',
				sanitize_title( $item[ 'method' ] ),
				esc_html( $item[ 'method' ] ),
				esc_html__( 'Order:', 'devdiggers-multipos-for-woocommerce' ),
				$order_html,
				esc_html__( 'Ref:', 'devdiggers-multipos-for-woocommerce' ),
				! empty( $item[ 'reference' ] ) ? esc_html( $item[ 'reference' ] ) : '-'
			);
		}

		/**
		 * Amount Column
		 *
		 * @param array $item Item.
		 * @return string
		 */
		public function column_amount( $item ) {
			return sprintf(
				'<div class="ddwcpos-transaction-amount-column">
					<div class="ddwcpos-amount-row amount-in">
						<span class="ddwcpos-label-mini">%1$s</span>
						<strong>%2$s</strong>
					</div>
					<div class="ddwcpos-amount-row amount-out">
						<span class="ddwcpos-label-mini">%3$s</span>
						<span>%4$s</span>
					</div>
				</div>',
				esc_html__( 'In:', 'devdiggers-multipos-for-woocommerce' ),
				wc_price( $item[ 'in' ] ),
				esc_html__( 'Out:', 'devdiggers-multipos-for-woocommerce' ),
				wc_price( $item[ 'out' ] )
			);
		}

		/**
         * Bulk actions on list.
		 *
		 * @return array
         */
        public function get_bulk_actions() {
            return apply_filters( 'ddwcpos_modify_bulk_actions_in_transactions', [
                'delete'  => esc_html__( 'Delete', 'devdiggers-multipos-for-woocommerce' ),
			] );
		}

        /**
		 * Transactions List Filters
		 *
		 * @param string $which Position of filter.
		 */
		public function extra_tablenav( $which ) {
			if ( 'top' === $which ) {
				$outlets               = $this->outlet_helper->ddwcpos_get_all_outlets( 999999, 0, '' );
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Filters are read-only.
				$outlet_id             = ! empty( $_GET[ 'outlet-id' ] ) ? absint( wp_unslash( $_GET[ 'outlet-id' ] ) ) : 0;
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Filters are read-only.
				$transaction_from_date = ! empty( $_GET[ 'transaction-from-date' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'transaction-from-date' ] ) ) : '';
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Filters are read-only.
				$transaction_to_date   = ! empty( $_GET[ 'transaction-to-date' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'transaction-to-date' ] ) ) : '';
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Filters are read-only.
				$cashier_filter_id     = ! empty( $_GET[ 'cashier-id' ] ) ? absint( wp_unslash( $_GET[ 'cashier-id' ] ) ) : 0;
				?>
				<div class="alignleft actions bulkactions">
					<select name="outlet-id" data-placeholder="<?php esc_attr_e( 'Select Outlet', 'devdiggers-multipos-for-woocommerce' ); ?>">
						<option value=""><?php esc_html_e( 'Select Outlet', 'devdiggers-multipos-for-woocommerce' ); ?></option>
						<?php
						if ( ! empty( $outlets ) ) {
							foreach ( $outlets as $key => $outlet ) {
								?>
								<option value="<?php echo esc_attr( $outlet[ 'id' ] ); ?>" <?php echo esc_attr( $outlet[ 'id' ] == $outlet_id ? 'selected="selected"' : '' ); ?>><?php echo esc_html( $outlet[ 'name' ] ); ?></option>
								<?php
							}
						}
						?>
					</select>

					<label for="ddwcpos-transaction-from-date"><?php esc_html_e( 'From:', 'devdiggers-multipos-for-woocommerce' ); ?></label>
					<input type="date" value="<?php echo esc_attr( $transaction_from_date ); ?>" name="transaction-from-date" id="ddwcpos-transaction-from-date" class="ddwcpos-datepicker" placeholder="yyyy-mm-dd" autocomplete="off" />

					<label for="ddwcpos-transaction-to-date"><?php esc_html_e( 'To:', 'devdiggers-multipos-for-woocommerce' ); ?></label>
					<input type="date" value="<?php echo esc_attr( $transaction_to_date ); ?>" name="transaction-to-date" id="ddwcpos-transaction-to-date" class="ddwcpos-datepicker" placeholder="yyyy-mm-dd" autocomplete="off" />

					<select name="cashier-id" data-placeholder="<?php esc_attr_e( 'Select Cashier', 'devdiggers-multipos-for-woocommerce' ); ?>">
                        <option value=""><?php esc_html_e( 'Select Cashier', 'devdiggers-multipos-for-woocommerce' ); ?></option>
						<?php
                        $query = new \WP_User_Query( [
							'role__in'       => apply_filters( 'ddwcpos_allowed_roles_for_pos', [ 'ddwcpos_cashier', 'administrator', 'shop_manager' ] ),
                            'order'          => 'DESC',
                            'orderby'        => 'ID',
                            'search_columns' => [ 'user_nicename', 'ID', 'user_login', 'user_email' ],
                            'fields'         => [ 'ID', 'user_login', 'user_email' ],
                        ] );

                        $cashiers = $query->get_results();

                        if ( ! empty( $cashiers ) ) {
                            foreach ( $cashiers as $cashier ) {
                                $cashier_id = $cashier->ID;
                                $cashier_option_value = "(#{$cashier_id}) {$cashier->user_login} <{$cashier->user_email}>";
                                ?>
                                <option value="<?php echo esc_attr( $cashier_id ); ?>" <?php selected( $cashier_id, $cashier_filter_id ); ?>><?php echo esc_html( $cashier_option_value ); ?></option>
                                <?php
                            }
                        }
						?>
					</select>

					<input type="submit" value="<?php esc_attr_e( 'Filter', 'devdiggers-multipos-for-woocommerce' ); ?>" name="ddwcpos_filter_submit" class="button" />

					<?php
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reset link is shown for read-only filters.
					if ( ! empty( $_GET['ddwcpos_filter_submit'] ) ) {
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Values are used only to build reset URL.
						$page = ! empty( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Values are used only to build reset URL.
						$menu = ! empty( $_GET['menu'] ) ? sanitize_text_field( wp_unslash( $_GET['menu'] ) ) : '';
						?>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $page . '&menu=' . $menu ) ); ?>" name="ddwcpos_filter_reset" class="button"><?php esc_html_e( 'Reset', 'devdiggers-multipos-for-woocommerce' ); ?></a>
						<?php
					}
					?>
				</div>
				<?php
			}
		}
	}
}
