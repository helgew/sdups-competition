<?php

abstract class SDUPS_Competition_DB {

	private static $DB_VERSION = '1.0';
	static $LOGGER;

	abstract protected function get_table_name();

	abstract protected function create_table();

	protected function load_data() {
		global $wpdb;

		$sql             = "SELECT `column_name`, `column_comment` " .
		                   "FROM `information_schema`.`COLUMNS`  " .
		                   "WHERE `table_name` = '" . $this->get_table_name() . "'  " .
		                   "AND `table_schema` = '$wpdb->dbname';";
		$results         = $wpdb->get_results( $sql, ARRAY_A );
		$data            = array();
		$data['headers'] = array();
		foreach ( $results as $row ) {
			$data['headers'][ $row['column_name'] ] = $row['column_comment'];
		}

		$sql             = "SELECT * FROM " . $this->get_table_name();
		$data['entries'] = $this->convert_rows_to_objects( $wpdb->get_results( $sql ) );

		return $data;
	}

	/**
	 * Convert the rows retrieved from the table to objects. Override in the child class to do
	 * more than just returning the rows.
	 *
	 * @param array $rows the rows loaded from the database
	 *
	 * @return array an array of objects
	 */
	protected function convert_rows_to_objects( array $rows ): array {
		return $rows;
	}

	public static function load_dependencies() {
		foreach ( glob( plugin_dir_path( __FILE__ ) . 'db/*.php' ) as $file ) {
			require_once $file;
		}

		SDUPS_Competition_Submission_Forms::instance();
	}

	/**
	 * Create our database schema.
	 */
	public static function create_db() {
		foreach ( get_declared_classes() as $class ) {
			if ( is_subclass_of( $class, self::class ) ) {
				self::$LOGGER->debug( "Calling create_table in $class" );
				if ( method_exists( $class, 'instance' ) ) {
					$instance = $class::instance();
				} else {
					$instance = new $class;
				}
				$instance->create_table();
			}
		}

		delete_option( self::get_version_option() );
		add_option( self::get_version_option(), self::$DB_VERSION );
	}

	protected static function requires_update( $table_name ) {
		global $wpdb;

		$current_version = get_option( self::get_version_option(), self::$DB_VERSION );

		return $wpdb->get_var( "show tables like '{$table_name}'" ) != $table_name ||
		       version_compare( $current_version, self::$DB_VERSION ) < 0;
	}

	private static function get_version_option() {
		return SDUPS_COMPETITION_PLUGIN_NAME . '_db_version';
	}

	/**
	 * Returns the table prefix to use.
	 *
	 * @return string the full table prefix to use, including the one configured for WP.
	 */
	protected static function get_table_prefix() {
		global $wpdb;

		return $wpdb->prefix . str_replace( '-', '_', SDUPS_COMPETITION_PLUGIN_NAME ) . '_';
	}
}

SDUPS_Competition_DB::$LOGGER = new SDUPS_Competition_Logger( SDUPS_Competition_DB::class,
	SDUPS_Competition_Log_Level::DEBUG );