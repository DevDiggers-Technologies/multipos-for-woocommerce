<?php
/**
 * Outlet Helper class
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 */

namespace DDWCMultiPOS\Helper\Outlet;

defined( 'ABSPATH' ) || exit();

// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Custom table names are built from $wpdb->prefix/static names; values remain prepared by the existing queries.

if ( ! class_exists( 'DDWCPOS_Outlet_Helper' ) ) {
    /**
     * Outlet Helper class
     */
    class DDWCPOS_Outlet_Helper {
        /**
		 * Database Object
		 *
		 * @var object
		 */
        protected $wpdb;

		/**
		 * Outlet table Variable
		 *
		 * @var string
		 */
		protected $outlet_table;

		/**
		 * Outlet Meta table Variable
		 *
		 * @var string
		 */
		protected $outletmeta_table;

		/**
		 * Construct
		 */
		public function __construct() {
			global $wpdb;
            $this->wpdb             = $wpdb;
            $this->outlet_table     = $this->wpdb->prefix . 'ddwcpos_outlets';
            $this->outletmeta_table = $this->wpdb->prefix . 'ddwcpos_outletmeta';
		}

        /**
		 * Save outlet to DB function
		 *
		 * @param array $data
		 * @return int
		 */
		public function ddwcpos_save_outlet( $data ) {
			$default_data = [
                'name'           => '',
                'mode'           => '',
                'inventory_type' => '',
                'address1'       => '',
                'address2'       => '',
                'city'           => '',
                'state'          => '',
                'country'        => '',
                'postcode'       => '',
                'phone'          => '',
                'email'          => '',
                'payments'       => '',
                'invoice'        => '',
                'tables'         => '',
                'status'         => '',
                'created'        => current_time( 'Y-m-d H:i:s' ),
                'updated'        => current_time( 'Y-m-d H:i:s' ),
			];

			$data = wp_parse_args( $data, $default_data );

			$data['mode']           = in_array( $data['mode'], [ 'grocery', 'restaurant' ], true ) ? $data['mode'] : 'grocery';
			$data['inventory_type'] = 'centralized';
			$data['tables']         = 'restaurant' === $data['mode'] ? $data['tables'] : maybe_serialize( [] );

            if ( ! empty( $data[ 'id' ] ) ) {
                $outlet_id = $data[ 'id' ];
                unset( $data[ 'id' ] );
                unset( $data[ 'created' ] );
                $this->wpdb->update(
                    $this->outlet_table,
                    $data,
                    [ 'id' => $outlet_id ],
                    [ '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ],
                    [ '%d' ]
                );
            } else {
				unset( $data[ 'id' ] );

                $this->wpdb->insert(
                    $this->outlet_table,
                    $data,
                    [ '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ]
                );

				$outlet_id = $this->wpdb->insert_id;
            }

            return $outlet_id;
		}

        /**
		 * Get all outlets function
		 *
		 * @param int $per_page
		 * @param int $offset
		 * @param string $search
		 * @return array
		 */
		public function ddwcpos_get_all_outlets( $per_page, $offset, $search ) {
			$primary_outlet_id = $this->ddwcpos_get_primary_outlet_id();
			$data              = $primary_outlet_id ? $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM $this->outlet_table WHERE id=%d AND name LIKE %s LIMIT 1", $primary_outlet_id, '%' . $search . '%' ), ARRAY_A ) : [];

			return apply_filters( 'ddwcpos_modify_outlets_data', $data, 1, 0, $search );
		}

        /**
		 * Get all outlets count function
		 *
		 * @param string $search
		 * @return int|null
		 */
		public function ddwcpos_get_all_outlets_count( $search ) {
			$primary_outlet_id = $this->ddwcpos_get_primary_outlet_id();
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Count on a trusted internal table name; no user input.
			$data              = $primary_outlet_id ? intval( $this->wpdb->get_var( $this->wpdb->prepare( "SELECT count(id) FROM $this->outlet_table WHERE id=%d AND name LIKE %s", $primary_outlet_id, '%' . $search . '%' ), ARRAY_A ) ) : 0;

			return apply_filters( 'ddwcpos_modify_outlets_count_data', min( $data, 1 ), $search );
		}

		/**
		 * Get raw saved outlet count.
		 *
		 * @return int
		 */
		public function ddwcpos_get_saved_outlets_count() {
			return intval( $this->wpdb->get_var( "SELECT count(id) FROM $this->outlet_table" ) );
		}

		/**
		 * Get outlet details by ids function
		 *
		 * @param array $ids
		 * @return array
		 */
        public function ddwcpos_get_outlet_details_by_ids( $ids ) {
			$primary_outlet_id = $this->ddwcpos_get_primary_outlet_id();
			$ids               = $primary_outlet_id && in_array( $primary_outlet_id, array_map( 'intval', (array) $ids ), true ) ? [ $primary_outlet_id ] : [];

			if ( empty( $ids ) ) {
				return apply_filters( 'ddwcpos_modify_outlets_data', [], $ids );
			}

			$ids          = array_map( 'intval', $ids );
			$placeholders = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );
			$params       = array_merge( $ids, [ 'enabled' ] );

			$data = $this->wpdb->get_results(
				$this->wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is a trusted internal property; $placeholders is only %d tokens and all values are bound via prepare().
					"SELECT * FROM {$this->outlet_table} WHERE id IN ($placeholders) AND status = %s",
					$params
				),
				ARRAY_A
			);

			return apply_filters( 'ddwcpos_modify_outlets_data', $data, $ids );
        }

        /**
		 * Get outlet details by id function
		 *
		 * @param int $id
		 * @return array
		 */
        public function ddwcpos_get_outlet_details_by_id( $id ) {
			$primary_outlet_id = $this->ddwcpos_get_primary_outlet_id();

			if ( empty( $primary_outlet_id ) || intval( $id ) !== $primary_outlet_id ) {
				return apply_filters( 'ddwcpos_modify_outlets_data', [], $id );
			}

            $data = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM $this->outlet_table WHERE id=%d", $id ), ARRAY_A );

			return apply_filters( 'ddwcpos_modify_outlets_data', $data, $id );
        }

        /**
		 * Delete outlet function
		 *
		 * @param int $id
		 * @return int|boolean
		 */
        public function ddwcpos_delete_outlet( $id ) {
			$this->wpdb->delete(
				$this->outletmeta_table,
				[
					'outlet_id'  => $id,
				],
				[ '%d' ]
			);

            return $this->wpdb->delete(
				$this->outlet_table,
				[
					'id' => $id
				],
                [ '%d' ]
            );
        }

		/**
		 * Update Outlet Meta function
		 *
		 * @param int $outlet_id
		 * @param string $meta_key
		 * @param string $meta_value
		 * @return void
		 */
		public function ddwcpos_update_outlet_meta( $outlet_id, $meta_key, $meta_value ) {
			$meta_value = is_array( $meta_value ) ? maybe_serialize( $meta_value ) : $meta_value;

			$meta_id = $this->wpdb->get_var( $this->wpdb->prepare( "SELECT id FROM $this->outletmeta_table WHERE outlet_id=%d AND meta_key=%s", $outlet_id, $meta_key ) );

			if ( ! empty( $meta_id ) ) {
				$this->wpdb->update(
					$this->outletmeta_table,
					[
						'meta_value' => $meta_value,
					],
					[
						'id' => $meta_id,
					],
					[ '%s' ],
					[ '%d' ]
				);
			} else {
				$this->wpdb->insert(
					$this->outletmeta_table,
					[
						'outlet_id'  => $outlet_id,
						'meta_key'   => $meta_key,
						'meta_value' => $meta_value,
					],
					[ '%d', '%s', '%s' ]
				);
			}
		}

		/**
		 * Get Outlet Meta function
		 *
		 * @param int $outlet_id
		 * @param string $meta_key
		 * @param boolean $meta_value_single
		 * @return string|array
		 */
		public function ddwcpos_get_outlet_meta( $outlet_id, $meta_key, $meta_value_single = false ) {
			$meta_value = maybe_unserialize( $this->wpdb->get_var( $this->wpdb->prepare( "SELECT meta_value FROM $this->outletmeta_table WHERE outlet_id=%d AND meta_key=%s", $outlet_id, $meta_key ) ) );

			return $meta_value_single ? $meta_value : [ $meta_value ];
		}

		/**
		 * Delete Outlet Meta function
		 *
		 * @param int $outlet_id
		 * @param string $meta_key
		 * @param string $meta_value
		 * @return boolean
		 */
		public function ddwcpos_delete_outlet_meta( $outlet_id, $meta_key, $meta_value = '' ) {
			if ( $meta_value ) {
				return $this->wpdb->delete(
					$this->outletmeta_table,
					[
						'outlet_id'  => $outlet_id,
						'meta_key'   => $meta_key,
						'meta_value' => $meta_value,
					],
					[ '%d', '%s', '%s' ]
				);
			} else {
				return $this->wpdb->delete(
					$this->outletmeta_table,
					[
						'outlet_id' => $outlet_id,
						'meta_key'  => $meta_key,
					],
					[ '%d', '%s' ]
				);
			}
		}

		/**
		 * Get outlet modes function
		 *
		 * @return array
		 */
		public function ddwcpos_get_outlet_modes() {
			$outlet_modes = [
				'grocery' => esc_html__( 'Grocery/Retail', 'devdiggers-multipos-for-woocommerce' ),
				'restaurant' => esc_html__( 'Restaurant/Cafe', 'devdiggers-multipos-for-woocommerce' ),
			];

			return apply_filters( 'ddwcpos_modify_outlet_modes', $outlet_modes );
		}

		/**
		 * Get inventory type function
		 *
		 * @param int $id
		 * @return string
		 */
		public function ddwcpos_get_inventory_type( $id ) {
			return apply_filters( 'ddwcpos_modify_outlet_inventory_type', 'centralized', $id );
		}

		/**
		 * Get the primary outlet exposed by this plugin.
		 *
		 * @return int
		 */
		protected function ddwcpos_get_primary_outlet_id() {
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Trusted internal table name; no user input.
			return intval( $this->wpdb->get_var( "SELECT id FROM $this->outlet_table ORDER BY id ASC LIMIT 1" ) );
		}
    }
}
