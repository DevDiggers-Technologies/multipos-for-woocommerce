<?php
/**
 * Orders List Template
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Templates\Admin\Order;

use DDWCMultiPOS\Helper\Outlet\DDWCPOS_Outlet_Helper;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Orders_List_Template' ) ) {

    /**
     * Orders List class
     */
    class DDWCPOS_Orders_List_Template extends \WP_List_Table {

        /**
		 * Outlet Helper Variable
		 *
		 * @var object
		 */
		protected $outlet_helper;

        /**
         * Construct
         */
        function __construct() {
            $this->outlet_helper = new DDWCPOS_Outlet_Helper();

            parent::__construct( [
                'singular' => esc_html__( 'Order', 'devdiggers-multipos-for-woocommerce' ),
                'plural'   => esc_html__( 'Orders', 'devdiggers-multipos-for-woocommerce' ),
                'ajax'     => false
            ] );
        }

        /**
         * Prepare the items for the table to process
         */
        public function prepare_items() {
			$this->_column_headers = $this->get_column_info();

				$http_host   = ! empty( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
				$request_uri = ! empty( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
				$current_url = set_url_scheme( 'http://' . $http_host . $request_uri );

			if ( strpos( $current_url, '_wp_http_referer' ) !== false ) {
				$new_url = remove_query_arg( [ '_wp_http_referer', '_wpnonce' ], stripslashes( $current_url ) );
				wp_safe_redirect( $new_url );
				exit();
			}

				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Search is read-only list-table input.
				$search       = ! empty( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Outlet filter is read-only list-table input.
				$outlet_id    = ! empty( $_GET['outlet-id'] ) ? intval( $_GET['outlet-id'] ) : 0;
			$per_page     = $this->get_items_per_page( 'orders_per_page', 20 );
			$current_page = $this->get_pagenum();
			$total_items  = $this->ddwcpos_get_orders_count( $search, $outlet_id );

			$this->set_pagination_args( [
				'total_items' => $total_items,
				'per_page'    => $per_page,
			] );

			$data = $this->ddwcpos_get_orders( $per_page, $current_page, $search, $outlet_id );

			usort( $data, [ $this, 'usort_reorder' ] );

			$this->items = $data;
		}

		/**
		 * Get Orders Count function
		 *
		 * @param string $search
		 * @param int $outlet_id
		 * @return int
		 */
		public function ddwcpos_get_orders_count( $search, $outlet_id ) {
			if ( ! empty( $outlet_id ) ) {

				$args = [
					'p'            => esc_attr( $search ),
					'paginate'     => true,
					'limit'        => 1,
					'return'       => 'ids',
					'meta_key'     => '_ddwcpos_outlet_id',
					'meta_compare' => '=',
					'meta_value'   => $outlet_id,
				];
			} else {

				$args = [
					'p'            => esc_attr( $search ),
					'paginate'     => true,
					'limit'        => 1,
					'return'       => 'ids',
					'meta_key'     => '_ddwcpos_outlet_id',
					'meta_compare' => 'EXISTS',
				];
			}

			$result = wc_get_orders( $args );

			return $result->total;
		}

		/**
		 * Fetch Orders Data Function
		 *
		 * @param int $per_page Per Page.
		 * @param int $current_page Page.
		 * @param string $search
		 * @param int $outlet_id
		 * @return array $data
		 */
		public function ddwcpos_get_orders( $per_page, $current_page, $search, $outlet_id ) {
			$data   = [];
			$offset = ( $current_page - 1 ) * $per_page;

			if ( ! empty( $outlet_id ) ) {

				$args = [
					'p'                   => esc_attr( $search ),
					'ignore_sticky_posts' => 1,
					'posts_per_page'      => $per_page,
					'offset'              => $offset,
					'order'               => 'DESC',
					'orderby'             => 'ID',
					'return'              => 'ids',
					'meta_key'            => '_ddwcpos_outlet_id',
					'meta_compare'        => '=',
					'meta_value'          => $outlet_id,
				];
			} else {

				$args = [
					'p'                   => esc_attr( $search ),
					'ignore_sticky_posts' => 1,
					'posts_per_page'      => $per_page,
					'offset'              => $offset,
					'order'               => 'DESC',
					'orderby'             => 'ID',
					'return'              => 'ids',
					'meta_key'            => '_ddwcpos_outlet_id',
					'meta_compare'        => 'EXISTS',
				];
			}

			$order_ids = wc_get_orders( $args );

			if ( ! empty( $order_ids ) ) {
				foreach ( $order_ids as $order_id ) {
					$order = wc_get_order( $order_id );
					$buyer = '';

					if ( $order->get_billing_first_name() || $order->get_billing_last_name() ) {
						/* translators: 1: first name 2: last name */
						$buyer = trim( sprintf( _x( '%1$s %2$s', 'full name', 'devdiggers-multipos-for-woocommerce' ), $order->get_billing_first_name(), $order->get_billing_last_name() ) );
					} elseif ( $order->get_billing_company() ) {
						$buyer = trim( $order->get_billing_company() );
					} elseif ( $order->get_customer_id() ) {
						$user  = get_user_by( 'ID', $order->get_customer_id() );
						$buyer = ucwords( $user->display_name );
					}

					/**
					 * Filter buyer name in list table orders.
					 *
					 * @since 3.7.0
					 * @param string   $buyer Buyer name.
					 * @param WC_Order $order Order data.
					 */
					$buyer = apply_filters( 'woocommerce_admin_order_buyer_name', $buyer, $order );

					if ( 'trash' === $order->get_status() ) {
						$order_html = '<strong>#' . esc_attr( $order->get_order_number() ) . ' ' . esc_html( $buyer ) . '</strong>';
					} else {
						$order_html = '<a href="' . esc_url( admin_url( 'post.php?post=' . absint( $order->get_id() ) ) . '&action=edit' ) . '" class="order-view"><strong>#' . esc_attr( $order->get_order_number() ) . ' ' . esc_html( $buyer ) . '</strong></a>';
					}

					$tooltip                 = '';
					$comment_count           = get_comment_count( $order_id );
					$approved_comments_count = absint( $comment_count[ 'approved' ] );

					if ( $approved_comments_count ) {
						$latest_notes = wc_get_order_notes(
							[
								'order_id' => $order_id,
								'limit'    => 1,
								'orderby'  => 'date_created_gmt',
							]
						);

						$latest_note = current( $latest_notes );

						if ( isset( $latest_note->content ) && 1 === $approved_comments_count ) {
							$tooltip = wc_sanitize_tooltip( $latest_note->content );
						} elseif ( isset( $latest_note->content ) ) {
							/* translators: %d: notes count */
							$tooltip = wc_sanitize_tooltip( $latest_note->content . '<br/><small style="display:block">' . sprintf( _n( 'Plus %d other note', 'Plus %d other notes', ( $approved_comments_count - 1 ), 'devdiggers-multipos-for-woocommerce' ), $approved_comments_count - 1 ) . '</small>' );
						} else {
							/* translators: %d: notes count */
							$tooltip = wc_sanitize_tooltip( sprintf( _n( '%d note', '%d notes', $approved_comments_count, 'devdiggers-multipos-for-woocommerce' ), $approved_comments_count ) );
						}
					}

					if ( $tooltip ) {
						$status = sprintf( '<mark class="order-status %s tips" data-tip="%s"><span>%s</span></mark>', esc_attr( sanitize_html_class( 'status-' . $order->get_status() ) ), wp_kses_post( $tooltip ), esc_html( wc_get_order_status_name( $order->get_status() ) ) );
					} else {
						$status = sprintf( '<mark class="order-status %s"><span>%s</span></mark>', esc_attr( sanitize_html_class( 'status-' . $order->get_status() ) ), esc_html( wc_get_order_status_name( $order->get_status() ) ) );
					}

					$order_timestamp = $order->get_date_created() ? $order->get_date_created()->getTimestamp() : '';

					if ( ! $order_timestamp ) {
						echo '&ndash;';
						return;
					}

					// Check if the order was created within the last 24 hours, and not in the future.
					if ( $order_timestamp > strtotime( '-1 day', time() ) && $order_timestamp <= time() ) {
						$show_date = sprintf(
							/* translators: %s: human-readable time difference */
							_x( '%s ago', '%s = human-readable time difference', 'devdiggers-multipos-for-woocommerce' ),
							human_time_diff( $order->get_date_created()->getTimestamp(), time() )
						);
					} else {
						$show_date = $order->get_date_created()->date_i18n( apply_filters( 'woocommerce_admin_order_date_format', __( 'M j, Y', 'devdiggers-multipos-for-woocommerce' ) ) );
					}

					$date = sprintf(
						'<time datetime="%1$s" title="%2$s">%3$s</time>',
						esc_attr( $order->get_date_created()->date( 'c' ) ),
						esc_html( $order->get_date_created()->date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) ),
						esc_html( $show_date )
					);

					if ( $order->get_payment_method_title() ) {
						/* translators: %s: method */
						$total = '<span class="tips" data-tip="' . esc_attr( sprintf( __( 'via %s', 'devdiggers-multipos-for-woocommerce' ), $order->get_payment_method_title() ) ) . '">' . wp_kses_post( $order->get_formatted_order_total() ) . '</span>';
					} else {
						$total = wp_kses_post( $order->get_formatted_order_total() );
					}

					$outlet_id   = $order->get_meta( '_ddwcpos_outlet_id', true );
					$outlet      = $this->outlet_helper->ddwcpos_get_outlet_details_by_id( $outlet_id );

					if ( ! $outlet ) {
						continue;
					}

					$cashier_id  = $order->get_meta( '_ddwcpos_cashier_id', true );
					$offline_id  = $order->get_meta( '_ddwcpos_offline_id', true );
					$cashier     = '-';
					$cashier_obj = get_user_by( 'ID', $cashier_id );

					if ( $cashier_obj ) {
						$cashier_name = ! empty( $cashier_obj->display_name ) ? $cashier_obj->display_name : $cashier_obj->user_login;
						$cashier      = '<a href="' . esc_url( admin_url( 'user-edit.php?user_id=' . $cashier_id ) ) . '">' . esc_html( $cashier_name ) . '</a>';
					}

					$data[] = [
						'id'               => $order_id,
						'offline_order_id' => ! empty( $offline_id ) ? '#' . $offline_id : '-',
						'order'            => $order_html,
						'date'             => $date,
						'status'           => $status,
						'total'            => $total,
						'outlet'           => $outlet[ 'name' ],
						'cashier'          => $cashier,
						'payment_method'   => $order->get_payment_method_title(),
					];

				}
			}

			wp_reset_postdata();

			return apply_filters( 'ddwcpos_orders_list_data', $data );
		}

        /**
		 * Associative array of columns
		 *
		 * @return array
		 */
		public function get_columns() {
			return apply_filters( 'ddwcpos_orders_list_columns', [
				'order'       => esc_html__( 'Order', 'devdiggers-multipos-for-woocommerce' ),
				'status_date' => esc_html__( 'Status & Date', 'devdiggers-multipos-for-woocommerce' ),
				'revenue'     => esc_html__( 'Revenue', 'devdiggers-multipos-for-woocommerce' ),
				'source'      => esc_html__( 'Staff Attribution', 'devdiggers-multipos-for-woocommerce' ),
			] );
		}

        /**
		 * No items
		 *
		 * @return void
		 */
		public function no_items() {
			esc_html_e( 'No orders avaliable.', 'devdiggers-multipos-for-woocommerce' );
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
		 * Usort
		 *
		 * @param int $first First value.
		 * @param int $second Second value.
		 * @return $result
		 */
		public function usort_reorder( $first, $second ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Sorting is read-only list-table input.
				$orderby = ! empty( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'id';
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Sorting is read-only list-table input.
				$order   = ! empty( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'desc';

			$result = strnatcmp( $first[ $orderby ], $second[ $orderby ] );
			return $order === 'asc' ? $result : -$result;
        }

        /**
		 * Columns to make sortable.
		 *
		 * @return array
		 */
		public function get_sortable_columns() {
			$sortable_columns = [
				'order'       => [ 'order', true ],
				'status_date' => [ 'status', true ],
				'revenue'     => [ 'total', true ],
			];

			return apply_filters( 'ddwcpos_orders_list_sortable_columns', $sortable_columns );
		}

		/**
		 * Order Column
		 *
		 * @param array $item Item.
		 * @return string
		 */
		public function column_order( $item ) {
			return sprintf(
				'<div class="ddwcpos-order-id-column">
					<div class="ddwcpos-order-main">
						%1$s
					</div>
					<div class="ddwcpos-offline-id-label">
						%2$s %3$s
					</div>
				</div>',
				$item[ 'order' ],
				esc_html__( 'Offline ID:', 'devdiggers-multipos-for-woocommerce' ),
				$item[ 'offline_order_id' ]
			);
		}

		/**
		 * Status & Date Column
		 *
		 * @param array $item Item.
		 * @return string
		 */
		public function column_status_date( $item ) {
			return sprintf(
				'<div class="ddwcpos-status-timeline-column">
					<div class="ddwcpos-status-tier">
						%1$s
					</div>
					<div class="ddwcpos-date-tier">
						%2$s
					</div>
				</div>',
				$item[ 'status' ],
				$item[ 'date' ]
			);
		}

		/**
		 * Revenue Column
		 *
		 * @param array $item Item.
		 * @return string
		 */
		public function column_revenue( $item ) {
			return sprintf(
				'<div class="ddwcpos-revenue-column">
					<div class="ddwcpos-total-amount">
						%1$s
					</div>
					<div class="ddwcpos-payment-method">
						%2$s
					</div>
				</div>',
				$item[ 'total' ],
				! empty( $item[ 'payment_method' ] ) ? 'via ' . $item[ 'payment_method' ] : ''
			);
		}

		/**
		 * Source Column
		 *
		 * @param array $item Item.
		 * @return string
		 */
		public function column_source( $item ) {
			return sprintf(
				'<div class="ddwcpos-attribution-path">
					<span class="dashicons dashicons-store"></span>
					<span class="ddwcpos-path-outlet">%1$s</span>
					<span class="dashicons dashicons-arrow-right-alt2"></span>
					<span class="ddwcpos-path-cashier">%2$s</span>
				</div>',
				$item[ 'outlet' ],
				$item[ 'cashier' ]
			);
		}

		/**
		 * Product Stocks List Filters
		 *
		 * @param string $which Position of filter.
		 */
		public function extra_tablenav( $which ) {
			if ( 'top' === $which ) {
				$outlets   = $this->outlet_helper->ddwcpos_get_all_outlets( 999999, 0, '' );
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Outlet filter is read-only list-table input.
					$outlet_id = ! empty( $_GET[ 'outlet-id' ] ) ? intval( $_GET[ 'outlet-id' ] ) : 0;
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

					<input type="submit" value="<?php esc_attr_e( 'Filter', 'devdiggers-multipos-for-woocommerce' ); ?>" name="ddwcpos_filter_submit" class="button" />

					<?php
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Filter submit flag controls reset button visibility only.
						if ( ! empty( $_GET['ddwcpos_filter_submit'] ) ) {
							// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Page/menu are read-only routing values.
							$page = ! empty( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
							// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Page/menu are read-only routing values.
							$menu = ! empty( $_GET['menu'] ) ? sanitize_text_field( wp_unslash( $_GET['menu'] ) ) : '';
							?>
							<a href="<?php echo esc_url( admin_url( "admin.php?page={$page}&menu={$menu}" ) ); ?>" name="ddwcpos_filter_reset" class="button"><?php esc_html_e( 'Reset', 'devdiggers-multipos-for-woocommerce' ); ?></a>
						<?php
					}
					?>
				</div>
				<?php
			}
		}
    }
}
