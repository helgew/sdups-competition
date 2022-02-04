<?php

class SDUPS_Competition_Submission_Forms extends SDUPS_Competition_DB {

	private static $TABLE = 'submission_forms';

	private static $instance = null;

	private $table_name;
	private $data;

	private function __construct() {
		$this->table_name = self::get_table_prefix() . self::$TABLE;
		$this->data = $this->load_data();
	}

	/**
	 * Create the table.
	 */
	protected function create_table() {
		global $wpdb;

		$table_name = self::get_table_prefix() . self::$TABLE;

		if ( self::requires_update( $table_name ) ) {

			$charset_collate = $wpdb->get_charset_collate();

			$sql[] = "CREATE TABLE `$table_name` (
				            id mediumint(9) NOT NULL AUTO_INCREMENT COMMENT 'ID',
				            wpforms_form_id mediumint NOT NULL COMMENT 'Form ID',
				            wpforms_title text NOT NULL COMMENT 'Form Title',
				            last_modified datetime NOT NULL COMMENT 'Last Modified',
				            last_parsed datetime DEFAULT '0000-00-00 00:00:00' COMMENT 'Last Analysed',
				            PRIMARY KEY  (id)
				       ) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			dbDelta( $sql );
		}
	}

	public function get_data() {
		return $this->data;
	}

	protected function get_table_name() {
		return $this->table_name;
	}

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new SDUPS_Competition_Submission_Forms();
		}

		return self::$instance;
	}
}