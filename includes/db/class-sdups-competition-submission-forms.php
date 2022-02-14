<?php

class SDUPS_Competition_Submission_Forms extends SDUPS_Competition_DB {

	private static $TABLE = 'submission_forms';

	private static $instance = null;

	private $table_name;
	private $data;

	private function __construct() {
		$this->table_name = self::get_table_prefix() . self::$TABLE;
		$this->data       = $this->load_data();
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
				            name_field int NOT NULL COMMENT 'Name Field ID',
				            email_field int NOT NULL COMMENT 'Email Field ID',
				            division_field int NOT NULL COMMENT 'Division Field ID',
				            category_field int NOT NULL COMMENT 'Category Field ID',
				            upload_field int NOT NULL COMMENT 'Upload Field ID',
				            last_modified datetime NOT NULL COMMENT 'Last Modified',
				            last_parsed datetime DEFAULT '0000-00-00 00:00:00' COMMENT 'Last Analysed',
				            is_active tinyint NOT NULL DEFAULT 0 COMMENT 'Active',
				            PRIMARY KEY  (id)
				       ) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			dbDelta( $sql );
		}
	}

	public static function get_field_attributes(): array {
		return [ 'name', 'email', 'division', 'category', 'upload' ];
	}

	public static function get_active_form(): ?SDUPS_Competition_Submission_Form {
		$instance = self::instance();
		foreach ( $instance->get_data()['entries'] as $entry ) {
			if ( $entry->is_active() ) {
				return $entry;
			}
		}

		return null;
	}

	public static function save_entries( array $forms ): void {
		foreach ( $forms as $form ) {
			self::save( $form );
		}
	}

	public static function save( SDUPS_Competition_Submission_Form $form ) {
		global $wpdb;
		$table_name = self::get_table_prefix() . self::$TABLE;

		if ( $form->is_active() ) {
			$wpdb->query( 'UPDATE ' . $table_name . ' SET is_active = 0' );
		}

		$data                    = array();
		$data['wpforms_form_id'] = $form->get_wpforms_form_id();
		$data['wpforms_title']   = $form->get_wpforms_title();
		$data['name_field']      = $form->get_name_field();
		$data['email_field']     = $form->get_email_field();
		$data['division_field']  = $form->get_division_field();
		$data['category_field']  = $form->get_category_field();
		$data['upload_field']    = $form->get_upload_field();
		$data['last_modified']   = $form->get_last_modified();
		$data['last_parsed']     = $form->get_last_parsed();
		$data['is_active']       = $form->is_active() ? 1 : 0;

		if ( $form->get_id() ) {
			$data['id'] = $form->get_id();
			$wpdb->replace( $table_name, $data );
		} else {
			$wpdb->insert( $table_name, $data );
			$form->set_id( $wpdb->insert_id );
		}

	}

	protected function convert_rows_to_objects( array $rows ): array {
		$objects = array();

		foreach ( $rows as $row ) {
			$objects[] = SDUPS_Competition_Submission_Form::from_db_row( $row );
		}

		return $objects;
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

class SDUPS_Competition_Submission_Form {
	private int $id;
	private int $wpforms_form_id;
	private string $wpforms_title;
	private int $name_field;
	private int $email_field;
	private int $division_field;
	private int $category_field;
	private int $upload_field;
	private string $last_modified;
	private string $last_parsed;
	private int $is_active;

	public static function from_preview_form( $form_data ): SDUPS_Competition_Submission_Form {
		$instance                  = new self();
		$fields                    = self::get_fields();
		$instance->wpforms_form_id = $form_data->form_id;
		$instance->is_active       = 1;
		foreach ( $fields as $field ) {
			$instance->$field = $form_data->$field;
		}
		$instance->parse();

		return $instance;
	}

	public static function from_db_row( $row ): SDUPS_Competition_Submission_Form {
		$instance = new self();
		$vars     = get_class_vars( self::class );
		foreach ( $vars as $property => $value ) {
			if ( property_exists( $row, $property ) ) {
				$instance->$property = $row->$property;
			}
		}

		return $instance;
	}

	private static function get_fields(): array {
		return preg_filter( '/$/', '_field', SDUPS_Competition_Submission_Forms::get_field_attributes() );
	}

	private function parse(): void {
		$post         = get_post( $this->wpforms_form_id );
		$form         = json_decode( $post->post_content );
		$my_fields    = self::get_fields();
		$my_field_ids = array();
		foreach ( $my_fields as $field ) {
			$my_field_ids[] = $this->$field;
		}
		foreach ( $form->fields as $field ) {
			$my_field_ids = array_diff( $my_field_ids, [ $field->id ] );
		}

		if ( sizeof( $my_field_ids ) ) {
			wp_die( new WP_Error( self::class . '_parse',
				'Error parsing the form ' . $this->wpforms_form_id .
				': form does not contain the expected fields!' ) );
		}

		$this->wpforms_title = $post->post_title;
		$this->last_modified = $post->post_modified;
		$this->last_parsed   = current_time( 'mysql' );
	}

	public function save(): void {
		SDUPS_Competition_Submission_Forms::save( $this );
	}

	public function get_submissions(): array {
		global $wpdb;

		$ps = $wpdb->prepare(
			"SELECT entry_id," .
			"    MAX(CASE WHEN field_id = %d THEN value END) AS name," .
			"    MAX(CASE WHEN field_id = %d THEN value END) AS email," .
			"    MAX(CASE WHEN field_id = %d THEN value END) AS division," .
			"    MAX(CASE WHEN field_id = %d THEN value END) AS category," .
			"    MAX(CASE WHEN field_id = %d THEN value END) AS upload," .
			"    MAX(date) AS date" .
			" FROM " . $wpdb->prefix . 'wpforms_entry_fields' .
			" WHERE form_id = %d" .
			" GROUP BY entry_id",
			$this->name_field, $this->email_field, $this->division_field,
			$this->category_field, $this->upload_field, $this->wpforms_form_id );

		$rows    = $wpdb->get_results( $ps );
		$results = array();
		foreach ( $rows as $row ) {
			$divs    = explode( "\n", $row->division );
			$uploads = explode( "\n", $row->upload );
			if ( sizeof( $divs ) === 2 && sizeof( $uploads ) === 2 && $divs[1] === 'Video' ) {
				$row_clone           = clone $row;
				$row->division       = $divs[0];
				$row_clone->division = $divs[1];
				if ( str_ends_with( strtolower( $uploads[0] ), '.mp4' ) ) {
					$row->upload       = $uploads[1];
					$row_clone->upload = $uploads[0];
				} else {
					$row->upload       = $uploads[0];
					$row_clone->upload = $uploads[1];
				}
				$results[] = $row_clone;
			} elseif (sizeof( $divs ) === 1 && sizeof( $uploads ) === 2 ) {
				$row_clone           = clone $row;
				$row_clone->upload = $uploads[0];
				$row->upload = $uploads[1];
				$results[] = $row_clone;
			} elseif ( sizeof( $divs ) === 2 && sizeof( $uploads ) === 1 && $divs[1] === 'Video' ) {
				$row->division = $divs[1];
			}

			$results[] = $row;
		}

		return $results;
	}

	public function set_id( int $id ): void {
		$this->id = $id;
	}

	public function get_id(): int {
		return $this->id ?? 0;
	}

	public function get_wpforms_form_id(): int {
		return $this->wpforms_form_id ?? 0;
	}

	public function get_wpforms_title(): string {
		return $this->wpforms_title ?? '';
	}

	public function get_name_field(): int {
		return $this->name_field ?? 0;
	}

	public function get_email_field(): int {
		return $this->email_field ?? 0;
	}

	public function get_division_field(): int {
		return $this->division_field ?? 0;
	}

	public function get_category_field(): int {
		return $this->category_field ?? 0;
	}

	public function get_upload_field(): int {
		return $this->upload_field ?? 0;
	}

	public function get_last_modified(): string {
		return $this->last_modified ?? '';
	}

	public function get_last_parsed(): string {
		return $this->last_parsed ?? '';
	}

	public function is_active(): bool {
		return $this->is_active === 1;
	}
}