<?php
/**
 * Dashboard Helper
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 * @version 1.0.0
 */

namespace DDWCMultiPOS\Helper\Dashboard;

use DDWCMultiPOS\Helper\Error\DDWCPOS_Error_Helper;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DDWCPOS_Dashboard_Helper' ) ) {
	/**
	 * Dashboard Helper Class
	 */
	class DDWCPOS_Dashboard_Helper {
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
		 * Date Range
		 *
		 * @var array
		 */
		protected $date_range;

		/**
		 * Construct
		 *
		 * @param array $ddwcpos_configuration
		 */
		public function __construct( $ddwcpos_configuration ) {
			$this->ddwcpos_configuration = $ddwcpos_configuration;
            $this->date_range            = $this->get_date_range();
		}

        /**
         * Get date range from request
         * 
         * @return array
         */
        protected function get_date_range() {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Date range is read-only dashboard filtering.
            $range_type = ! empty( $_GET['date_range'] ) ? sanitize_text_field( wp_unslash( $_GET['date_range'] ) ) : '30_days';
            $from_date  = '';
            $to_date    = '';
            $label      = '';

            switch ( $range_type ) {
                case 'today':
                    $from_date = current_time( 'Y-m-d' );
                    $to_date   = current_time( 'Y-m-d' );
                    $label     = __( 'Today', 'devdiggers-multipos-for-woocommerce' );
                    break;
                case '7_days':
                    $from_date = gmdate( 'Y-m-d', strtotime( 'monday this week' ) );
                    $to_date   = current_time( 'Y-m-d' );
                    $label     = __( 'This Week', 'devdiggers-multipos-for-woocommerce' );
                    break;
                case 'last_week':
                    $from_date = gmdate( 'Y-m-d', strtotime( 'monday last week' ) );
                    $to_date   = gmdate( 'Y-m-d', strtotime( 'sunday last week' ) );
                    $label     = __( 'Last Week', 'devdiggers-multipos-for-woocommerce' );
                    break;
                case '30_days':
                    $from_date = current_time( 'Y-m-01' );
                    $to_date   = current_time( 'Y-m-d' );
                    $label     = __( 'This Month', 'devdiggers-multipos-for-woocommerce' );
                    break;
                case 'last_month':
                    $from_date = gmdate( 'Y-m-01', strtotime( 'first day of last month' ) );
                    $to_date   = gmdate( 'Y-m-t', strtotime( 'last day of last month' ) );
                    $label     = __( 'Last Month', 'devdiggers-multipos-for-woocommerce' );
                    break;
                case '90_days':
                    $from_date = gmdate( 'Y-m-d', strtotime( '-90 days' ) );
                    $to_date   = current_time( 'Y-m-d' );
                    $label     = __( 'Last 3 Months', 'devdiggers-multipos-for-woocommerce' );
                    break;
                case '180_days':
                    $from_date = gmdate( 'Y-m-d', strtotime( '-180 days' ) );
                    $to_date   = current_time( 'Y-m-d' );
                    $label     = __( 'Last 6 Months', 'devdiggers-multipos-for-woocommerce' );
                    break;
                case 'year_to_date':
                    $from_date = current_time( 'Y-01-01' );
                    $to_date   = current_time( 'Y-m-d' );
                    $label     = __( 'Year to Date', 'devdiggers-multipos-for-woocommerce' );
                    break;
                case 'last_year':
                    $from_date = gmdate( 'Y-01-01', strtotime( 'last year' ) );
                    $to_date   = gmdate( 'Y-12-31', strtotime( 'last year' ) );
                    $label     = __( 'Last Year', 'devdiggers-multipos-for-woocommerce' );
                    break;
                case 'all_time':
                    $from_date = '2020-01-01'; // Fallback start date
                    $to_date   = current_time( 'Y-m-d' );
                    $label     = __( 'All Time', 'devdiggers-multipos-for-woocommerce' );
                    break;
                case 'custom':
                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Date range is read-only dashboard filtering.
                    $from_date = ! empty( $_GET['from_date'] ) ? sanitize_text_field( wp_unslash( $_GET['from_date'] ) ) : current_time( 'Y-m-d', false, strtotime( '-30 days' ) );
                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Date range is read-only dashboard filtering.
                    $to_date   = ! empty( $_GET['to_date'] ) ? sanitize_text_field( wp_unslash( $_GET['to_date'] ) ) : current_time( 'Y-m-d' );
                    $label     = sprintf( '%s to %s', $from_date, $to_date );
                    break;
                default:
                    $from_date = current_time( 'Y-m-d', false, strtotime( '-30 days' ) );
                    $to_date   = current_time( 'Y-m-d' );
                    $label     = __( 'Last 30 Days', 'devdiggers-multipos-for-woocommerce' );
                    break;
            }

            return [
                'from'  => $from_date,
                'to'    => $to_date,
                'label' => $label,
                'type'  => $range_type,
            ];
        }

		/**
		 * Get dashboard data
		 *
		 * @return array
		 */
		public function get_dashboard_data() {
			$data = [
				'summary' => [
					'total_orders'    => $this->get_total_orders(),
					'total_revenue'   => $this->get_total_revenue(),
					'total_outlets'   => $this->get_total_outlets(),
					'total_cashiers'  => $this->get_total_cashiers(),
				],
                'charts' => [
                    'revenue_chart'   => $this->get_revenue_chart_data(),
                    'outlets_chart'   => $this->get_sales_by_outlet(),
                    'payment_methods' => $this->get_payment_methods_breakdown(),
                ],
				'recent_orders' => $this->get_recent_orders(),
                'date_range'    => $this->date_range,
			];

			return $data;
		}

		/**
		 * Get total POS orders
		 *
		 * @return array
		 */
		protected function get_total_orders() {
			$args = [
				'status'       => array_keys( wc_get_order_statuses() ),
				'meta_key'     => '_ddwcpos_outlet_id',
				'meta_compare' => 'EXISTS',
				'date_created' => $this->date_range['from'] . '...' . $this->date_range['to'] . ' 23:59:59',
				'limit'        => -1,
				'return'       => 'ids',
			];

			$current_orders = wc_get_orders( $args );
			$current_count  = count( $current_orders );

			// Get previous period count
			$days_diff = ( strtotime( $this->date_range['to'] ) - strtotime( $this->date_range['from'] ) ) / ( 60 * 60 * 24 );
			$prev_from = gmdate( 'Y-m-d', strtotime( $this->date_range['from'] . ' -' . ceil( $days_diff + 1 ) . ' days' ) );
			$prev_to   = gmdate( 'Y-m-d', strtotime( $this->date_range['from'] . ' -1 day' ) );

			$args['date_created'] = $prev_from . '...' . $prev_to . ' 23:59:59';
			$prev_orders          = wc_get_orders( $args );
			$prev_count           = count( $prev_orders );

			$change_percentage = $prev_count > 0 ? ( ( $current_count - $prev_count ) / $prev_count ) * 100 : 0;

			return [
				'value'       => $current_count,
				'change'      => round( $change_percentage, 1 ),
				'is_positive' => $change_percentage >= 0,
			];
		}

		/**
		 * Get total POS revenue
		 *
		 * @return array
		 */
		protected function get_total_revenue() {
			$args = [
				'status'       => [ 'wc-completed', 'wc-processing' ],
				'meta_key'     => '_ddwcpos_outlet_id',
				'meta_compare' => 'EXISTS',
				'date_created' => $this->date_range['from'] . '...' . $this->date_range['to'] . ' 23:59:59',
				'limit'        => -1,
			];

			$orders          = wc_get_orders( $args );
			$current_revenue = 0;
			if ( ! empty( $orders ) ) {
				foreach ( $orders as $order ) {
					$current_revenue += (float) $order->get_total();
				}
			}

			// Get previous period revenue
			$days_diff = ( strtotime( $this->date_range['to'] ) - strtotime( $this->date_range['from'] ) ) / ( 60 * 60 * 24 );
			$prev_from = gmdate( 'Y-m-d', strtotime( $this->date_range['from'] . ' -' . ceil( $days_diff + 1 ) . ' days' ) );
			$prev_to   = gmdate( 'Y-m-d', strtotime( $this->date_range['from'] . ' -1 day' ) ) . ' 23:59:59';

			$args['date_created'] = $prev_from . '...' . $prev_to;
			$prev_orders          = wc_get_orders( $args );
			$previous_revenue     = 0;
			if ( ! empty( $prev_orders ) ) {
				foreach ( $prev_orders as $order ) {
					$previous_revenue += (float) $order->get_total();
				}
			}

			$change_percentage = $previous_revenue > 0 ? ( ( $current_revenue - $previous_revenue ) / $previous_revenue ) * 100 : 0;

			return [
				'value'       => (float) $current_revenue,
				'change'      => round( $change_percentage, 1 ),
				'is_positive' => $change_percentage >= 0,
			];
		}

		/**
		 * Get total outlets
		 *
		 * @return int
		 */
		protected function get_total_outlets() {
            global $wpdb;
				$outlet_table = $wpdb->prefix . 'ddwcpos_outlets';
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Custom table name is built from $wpdb->prefix and a static suffix.
	            return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$outlet_table} WHERE status = %s", 'enabled' ) );
		}

		/**
		 * Get total cashiers
		 *
		 * @return int
		 */
		protected function get_total_cashiers() {
             $args = [
                'role'    => 'ddwcpos_cashier',
                'fields'  => 'ID',
            ];
            $users = get_users($args);
			return count( $users );
		}

        /**
         * Get recent POS orders
         * 
         * @return array
         */
        protected function get_recent_orders( $limit = 5 ) {
            $args = [
				'status'     => array_keys( wc_get_order_statuses() ),
				'meta_key'     => '_ddwcpos_outlet_id',
				'meta_compare' => 'EXISTS',
				'limit'      => $limit,
                'orderby'    => 'date',
                'order'      => 'DESC',
			];

            $orders = wc_get_orders( $args );
            $data   = [];

            if ( ! empty( $orders ) ) {
                foreach ( $orders as $order ) {
                    $data[] = [
                        'id'       => $order->get_id(),
                        'number'   => $order->get_order_number(),
                        'total'    => $order->get_formatted_order_total(),
                        'status'   => wc_get_order_status_name( $order->get_status() ),
                        'date'     => $order->get_date_created()->date_i18n( get_option( 'date_format' ) ),
                        'customer' => $order->get_formatted_billing_full_name() ?: __( 'Guest', 'devdiggers-multipos-for-woocommerce' ),
                    ];
                }
            }

            return $data;
        }

        /**
         * Get revenue chart data
         * 
         * @return array
         */
        protected function get_revenue_chart_data() {
            $from_date = $this->date_range['from'];
            $to_date   = $this->date_range['to'];
            $days_diff = ( strtotime( $to_date ) - strtotime( $from_date ) ) / ( 60 * 60 * 24 );

            $args = [
                'status'       => [ 'wc-completed', 'wc-processing' ],
                'meta_key'     => '_ddwcpos_outlet_id',
				'meta_compare' => 'EXISTS',
                'date_created' => $from_date . '...' . $to_date . ' 23:59:59',
                'limit'        => -1,
            ];

            $orders = wc_get_orders( $args );
            $raw_data = [];

            if ( ! empty( $orders ) ) {
                foreach ( $orders as $order ) {
                    $date = $order->get_date_created();
                    if ( ! $date ) continue;

                    // Grouping logic
                    if ( $days_diff <= 90 ) {
                        $period = $date->date( 'Y-m-d' );
                    } elseif ( $days_diff <= 365 ) {
                        $period = $date->date( 'Y-m' );
                    } else {
                        $year    = $date->date( 'Y' );
                        $quarter = ceil( $date->date( 'n' ) / 3 );
                        $period  = $year . '-Q' . $quarter;
                    }

                    if ( ! isset( $raw_data[ $period ] ) ) {
                        $raw_data[ $period ] = 0;
                    }
                    $raw_data[ $period ] += (float) $order->get_total();
                }
            }

            // Post-process data
            $data_by_date = [];

            foreach ( $raw_data as $period_value => $revenue ) {
                $display_date = $period_value;

                // For Quarter, convert to first date of quarter for Chart.js label parsing consistency
                if ( $days_diff > 365 ) {
                    $year    = substr( $period_value, 0, 4 );
                    $quarter = substr( $period_value, -1 );
                    $month   = ( $quarter - 1 ) * 3 + 1;
                    $display_date = $year . '-' . str_pad( $month, 2, '0', STR_PAD_LEFT ) . '-01';
                } elseif ( $days_diff > 90 ) {
                    // For month, append -01
                    $display_date = $period_value . '-01';
                }

                $data_by_date[ $display_date ] = [
                    'date'    => $display_date,
                    'period'  => $period_value,
                    'revenue' => (float) $revenue,
                ];
            }

            // Fill empty dates/months for finer charts
            if ( $days_diff <= 90 ) {
                $current = $from_date;
                while ( $current <= $to_date ) {
                    if ( ! isset( $data_by_date[ $current ] ) ) {
                        $data_by_date[ $current ] = [
                            'date'    => $current,
                            'revenue' => 0,
                        ];
                    }
                    $current = gmdate( 'Y-m-d', strtotime( $current . ' +1 day' ) );
                }
            } elseif ( $days_diff <= 365 ) {
                $current = gmdate( 'Y-m-01', strtotime( $from_date ) );
                $end     = gmdate( 'Y-m-01', strtotime( $to_date ) );
                while ( $current <= $end ) {
                    if ( ! isset( $data_by_date[ $current ] ) ) {
                        $data_by_date[ $current ] = [
                            'date'    => $current,
                            'revenue' => 0,
                        ];
                    }
                    $current = gmdate( 'Y-m-d', strtotime( $current . ' +1 month' ) );
                }
            }

            ksort( $data_by_date );
            return array_values( $data_by_date );
        }

        /**
         * Get sales by outlet
         * 
         * @return array
         */
        protected function get_sales_by_outlet() {
            $args = [
                'status'       => [ 'wc-completed', 'wc-processing' ],
                'meta_key'     => '_ddwcpos_outlet_id',
				'meta_compare' => 'EXISTS',
                'date_created' => $this->date_range['from'] . '...' . $this->date_range['to'] . ' 23:59:59',
                'limit'        => -1,
            ];

            $orders   = wc_get_orders( $args );
            $raw_data = [];

            if ( ! empty( $orders ) ) {
                foreach ( $orders as $order ) {
                    $outlet_id = $order->get_meta( '_ddwcpos_outlet_id' );
                    if ( ! $outlet_id ) continue;

                    if ( ! isset( $raw_data[ $outlet_id ] ) ) {
                        $raw_data[ $outlet_id ] = 0;
                    }
                    $raw_data[ $outlet_id ] += (float) $order->get_total();
                }
            }

            arsort( $raw_data );

            global $wpdb;
            $outlet_table = $wpdb->prefix . 'ddwcpos_outlets';
            $data         = [];

            foreach ( $raw_data as $outlet_id => $revenue ) {
                // Fetch outlet name from custom table.
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Custom table name is built from $wpdb->prefix and a static suffix.
                $outlet_name = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$outlet_table} WHERE id = %d", $outlet_id ) );
                
                $data[] = [
                    /* translators: %d: Outlet ID. */
                    'outlet'  => $outlet_name ? $outlet_name : sprintf( __( 'Outlet #%d', 'devdiggers-multipos-for-woocommerce' ), $outlet_id ),
                    'revenue' => (float) $revenue,
                ];
            }

            return $data;
        }

        /**
         * Get payment methods breakdown
         * 
         * @return array
         */
        protected function get_payment_methods_breakdown() {
            $args = [
                'status'       => [ 'wc-completed', 'wc-processing' ],
                'meta_key'     => '_ddwcpos_outlet_id',
				'meta_compare' => 'EXISTS',
                'date_created' => $this->date_range['from'] . '...' . $this->date_range['to'] . ' 23:59:59',
                'limit'        => -1,
            ];

            $orders   = wc_get_orders( $args );
            $raw_data = [];

            if ( ! empty( $orders ) ) {
                foreach ( $orders as $order ) {
                    $payment_method = $order->get_payment_method_title();
                    if ( ! $payment_method ) {
                        $payment_method = __( 'Other', 'devdiggers-multipos-for-woocommerce' );
                    }

                    if ( ! isset( $raw_data[ $payment_method ] ) ) {
                        $raw_data[ $payment_method ] = 0;
                    }
                    $raw_data[ $payment_method ] += (float) $order->get_total();
                }
            }

            arsort( $raw_data );

            $data = [];
            foreach ( $raw_data as $method => $amount ) {
                $data[] = [
                    'payment_method' => $method,
                    'amount'         => (float) $amount,
                ];
            }

            return $data;
        }
	}
}
