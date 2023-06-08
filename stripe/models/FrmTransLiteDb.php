<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmTransLiteDb {

	/**
	 * @var int
	 */
	public $db_version = 5;

	/**
	 * @var string
	 */
	public $db_opt_name = 'frm_trans_db_version';

	/**
	 * @var string
	 */
	public $table_name = '';

	/**
	 * @var string
	 */
	public $singular = '';

	/**
	 * @param mixed $old_db_version
	 * @return void
	 */
	public function upgrade( $old_db_version = false ) {
		if ( ! $old_db_version ) {
			$old_db_version = get_option( $this->db_opt_name );
		}

		if ( $this->db_version == $old_db_version ) {
			return;
		}

		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$frm_db          = new FrmMigrate();
		$charset_collate = $frm_db->collation();

		/* Create/Upgrade Payments Table */
		$sql = "CREATE TABLE {$wpdb->prefix}frm_payments (
				id bigint(20) NOT NULL auto_increment,
				meta_value longtext default NULL,
				receipt_id varchar(100) default NULL,
				invoice_id varchar(100) default NULL,
				sub_id varchar(100) default NULL,
				item_id bigint(20) NOT NULL,
				action_id bigint(20) NOT NULL,
				amount decimal(12,2) NOT NULL default '0.00',
				status varchar(100) default NULL,
				begin_date date NOT NULL,
				expire_date date default NULL,
				paysys varchar(100) default NULL,
				created_at datetime NOT NULL,
				PRIMARY KEY  (id),
				KEY item_id (item_id)
			) {$charset_collate};";

		dbDelta( $sql );

		/* Create/Upgrade Subscriptions Table */
		$sql = "CREATE TABLE {$wpdb->prefix}frm_subscriptions (
				id bigint(20) NOT NULL auto_increment,
				sub_id varchar(100) default NULL,
				meta_value longtext default NULL,
				item_id bigint(20) NOT NULL,
				action_id bigint(20) NOT NULL,
				amount decimal(12,2) NOT NULL default '0.00',
				first_amount decimal(12,2) NOT NULL default '0.00',
				interval_count bigint(20) default 1,
				time_interval varchar(100) default NULL,
				fail_count bigint(20) default 0,
				end_count bigint(20) default NULL,
				next_bill_date date default NULL,
				status varchar(100) default NULL,
				paysys varchar(100) default NULL,
				created_at datetime NOT NULL,
				PRIMARY KEY  (id),
				KEY item_id (item_id)
			) {$charset_collate};";

		dbDelta( $sql );

		/***** SAVE DB VERSION *****/
		update_option( $this->db_opt_name, $this->db_version );

		$this->migrate_data( $old_db_version );
	}

	/**
	 * @param array $values
	 * @return int
	 */
	public function create( $values ) {
		global $wpdb;

		$values['action'] = 'create';
		$new_values = array();
		$this->fill_values( $values, $new_values );

		$wpdb->insert( $wpdb->prefix . $this->table_name, $new_values );

		return $wpdb->insert_id;
	}

	/**
	 * @param string|int $id
	 * @param array      $values
	 * @return int|false
	 */
	public function update( $id, $values ) {
		global $wpdb;

		$values['action'] = 'update';
		$new_values       = array();
		$this->fill_values( $values, $new_values );

		return $wpdb->update( $wpdb->prefix . $this->table_name, $new_values, compact( 'id' ) );
	}

	/**
	 * @param string|int $id
	 * @return int|bool
	 */
	public function &destroy( $id ) {
		FrmAppHelper::permission_check( 'administrator' );

		global $wpdb;
		$id = absint( $id );

		/**
		 * @param int $id
		 */
		do_action( 'frm_before_destroy_' . $this->singular, $id );

		// @codingStandardsIgnoreStart
		$result = $wpdb->query(
			$wpdb->prepare(
				'DELETE FROM ' . $wpdb->prefix . $this->table_name . ' WHERE id=%d',
				$id
			)
		);
		// @codingStandardsIgnoreEnd

		return $result;
	}

	/**
	 * @param string|int $id
	 * @return array|object|null|void
	 */
	public function get_one( $id ) {
		global $wpdb;
		// @codingStandardsIgnoreStart
		return $wpdb->get_row(
			$wpdb->prepare(
				'SELECT * FROM ' . $wpdb->prefix . $this->table_name . ' WHERE id=%d',
				$id
			)
		);
		// @codingStandardsIgnoreEnd
	}

	/**
	 * @param string|int $id
	 * @param string     $field
	 * @return object|null
	 */
	public function get_one_by( $id, $field = 'receipt_id' ) {
		if ( ! in_array( $field, array( 'receipt_id', 'sub_id' ), true ) ) {
			_doing_it_wrong( __FUNCTION__, 'Items can only be retrieved by receipt id or sub id.', 'x.x' );
			return null;
		}

		global $wpdb;
		// Can this be exploited?
		$field = sanitize_text_field( $field );
		// @codingStandardsIgnoreStart
		return $wpdb->get_row(
			$wpdb->prepare(
				'SELECT * FROM ' . $wpdb->prefix . $this->table_name
				. ' WHERE ' . $field . ' = %s ORDER BY created_at DESC',
				$id
			)
		);
		// @codingStandardsIgnoreEnd
	}

	public function get_all_by( $value, $field = 'item_id' ) {
		$field = sanitize_text_field( $field );

		if ( ! in_array( $field, array( 'item_id', 'sub_id' ), true ) ) {
			_doing_it_wrong( __FUNCTION__, 'Items can only be retrieved by item id or sub id.', 'x.x' );
			return null;
		}

		global $wpdb;
		// @codingStandardsIgnoreStart
		return $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM ' . $wpdb->prefix . $this->table_name
				. ' WHERE ' . $field . ' = %s ORDER BY created_at DESC',
				$value
			)
		);
		// @codingStandardsIgnoreEnd
	}

	public function get_all_for_user( $user_id ) {
		global $wpdb;
		// @codingStandardsIgnoreStart
		return $wpdb->get_results(
			$wpdb->prepare(
				'SELECT
					*,
					e.id as entry_id,
					p.id as id
				FROM ' . $wpdb->prefix . $this->table_name . ' p '
				. 'LEFT JOIN ' . $wpdb->prefix . 'frm_items e ON e.id = p.item_id '
				. 'WHERE e.user_id = %d '
				. 'ORDER BY p.created_at DESC',
				$user_id
			)
		);
		// @codingStandardsIgnoreEnd
	}

	public function get_all_for_entry( $id ) {
		return $this->get_all_by( $id, $field = 'item_id' );
	}

	public function get_count() {
		global $wpdb;
		// @codingStandardsIgnoreStart
		return $wpdb->get_var(
			'SELECT COUNT(*) FROM ' . $wpdb->prefix . $this->table_name
		);
		// @codingStandardsIgnoreEnd
	}

	/**
	 * @return array
	 */
	public function get_defaults() {
		return array();
	}

	/**
	 * @param array $values
	 * @param array $new_values
	 * @return void
	 */
	private function fill_values( $values, &$new_values ) {
		$defaults = $this->get_defaults();
		foreach ( $defaults as $val => $default ) {
			if ( isset( $values[ $val ] ) ) {
				if ( $default['sanitize'] === 'float' ) {
					$new_values[ $val ] = (float) $values[ $val ];
				} elseif ( ! empty( $default['sanitize'] ) ) {
					$new_values[ $val ] = call_user_func( $default['sanitize'], $values[ $val ] );
				}
			} elseif ( $values['action'] === 'create' ) {
				$new_values[ $val ] = $default['default'];
			}
		}
	}

	/**
	 * @return void
	 */
	private function migrate_data( $old_db_version ) {
		$migrations = array( 4 );
		foreach ( $migrations as $migration ) {
			if ( $this->db_version >= $migration && $old_db_version < $migration ) {
				$function_name = 'migrate_to_' . $migration;
				$this->$function_name();
			}
		}
	}

	/**
	 * @return void
	 */
	private function migrate_to_4() {
		global $wpdb;
		$result = $wpdb->get_results( $wpdb->prepare( 'SHOW COLUMNS FROM ' . $wpdb->prefix . 'frm_payments LIKE %s', 'completed' ) );
		if ( empty( $result ) ) {
			return;
		}

		$payments = $wpdb->get_results(
			"SELECT * FROM {$wpdb->prefix}frm_payments WHERE completed is NOT NULL AND status is NULL"
		);
		foreach ( $payments as $payment ) {
			$status = $payment->completed ? 'complete' : 'failed';
			$wpdb->update( $wpdb->prefix . 'frm_payments', compact( 'status' ), array( 'id' => $payment->id ) );
		}
	}
}
