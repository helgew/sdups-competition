<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/helgew/sdups-competition
 * @since      1.0.0
 *
 * @package    SDUPS_Competition
 * @subpackage SDUPS_Competition/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the sdups competition, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    SDUPS_Competition
 * @subpackage SDUPS_Competition/admin
 * @author     Helge Weissig <helgew@grajagan.org>
 */
class SDUPS_Competition_Admin {

	static $LOGGER;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private string $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private string $version;

	/**
	 * The submission forms we are working with.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var object SDUPS_Competition_Submission_Forms
	 */
	private object $submission_forms;

	/**
	 * The context of this class. Used to store stuff for the different screens.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var array
	 */
	private $context = array();

	/**
	 * The submission page URL option name saved as a plugin option.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var string $submission_url_option The submission page URL option name
	 */
	private string $submission_url_option;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name           = $plugin_name;
		$this->version               = $version;
		$this->submission_forms      = SDUPS_Competition_Submission_Forms::instance();
		$this->submission_url_option = SDUPS_COMPETITION_PLUGIN_NAME . '-submission-url';

		include_once plugin_dir_path( __FILE__ ) . 'class-' . $this->plugin_name . '-admin-help.php';
		include_once plugin_dir_path( __FILE__ ) . 'class-' . $this->plugin_name . '-admin-notices.php';
	}

	public static function get_main_menu_slug() {
		return self::get_admin_slug() . '-overview';
	}

	public static function get_admin_slug() {
		return SDUPS_COMPETITION_PLUGIN_NAME . '-admin';
	}

	private function init_context() {
		$submission_url                  = get_option( $this->submission_url_option, site_url() . '/submit-photo-and-or-video/' );
		$this->context['submission_url'] = $submission_url;
		$this->context['possible_forms'] = $this->parse_submission_page( $submission_url );
		$this->context['field_attrs']    = SDUPS_Competition_Submission_Forms::get_field_attributes();
		if ( sizeof( $this->context['possible_forms'] ) == 1 ) {
			$this->context['formId']          = $this->context['possible_forms'][0]['value'];
			$this->context['possible_fields'] = $this->parse_submission_form( $this->context['formId'] );
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'css/sdups-competition-admin.css', array(), $this->version, 'all' );
		// wp_register_style( 'dataTables', '//cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css' );
		// we're using a modified version without styling of form fields
		wp_enqueue_style( $this->plugin_name . '-datatables', plugin_dir_url( __FILE__ ) . 'css/jquery.dataTables.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$handle = $this->plugin_name . '-admin-script';

		wp_register_script( $handle, plugin_dir_url( __FILE__ ) . 'js/sdups-competition-admin.js', array( 'jquery' ), $this->version, false );

		$translation_array = array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		);
		wp_localize_script( $handle, 'cpm_object', $translation_array );

		wp_enqueue_script( $handle );

		wp_register_script( 'dataTables', '//cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js' );
		wp_enqueue_script( 'dataTables' );

		add_thickbox();
	}

	public function add_sdups_admin_menus() {
		self::$LOGGER->debug( "Generating admin menu" );

		$admin_slug = self::get_admin_slug();
		$main_menu  = self::get_main_menu_slug();

		add_menu_page(
			'SDUPS Competition',
			'Competition',
			SDUPS_COMPETITION_USER_CAPABILITY,
			$main_menu,
			array( $this, 'admin_overview_page' ),
			null,
			4
		);

		$main_page = add_submenu_page( $main_menu,
			'SDUPS Competition',
			'Overview',
			SDUPS_COMPETITION_USER_CAPABILITY,
			$main_menu,
			array( $this, 'admin_overview_page' )
		);

		$help = new SDUPS_Competition_Admin_Help( $main_page, $this->plugin_name );

		add_submenu_page( $main_menu,
			'SDUPS Competition',
			'Create Form',
			SDUPS_COMPETITION_USER_CAPABILITY,
			$admin_slug . '-create-form',
			array( $this, 'admin_create_form_page' )
		);

		add_submenu_page( $main_menu,
			'SDUPS Competition',
			'Voting Forms',
			SDUPS_COMPETITION_USER_CAPABILITY,
			$admin_slug . '-forms-admin',
			array( $this, 'admin_voting_forms_page' )
		);

		$this->check_config();
	}

	private function check_config() {
		if ( sizeof( $this->submission_forms->get_data()['entries'] ) == 0 ) {
			SDUPS_Competition_Admin_Notices::error( 'Please complete the initial configuration in the <b>Overview -> Configuration</b> section.', 'initial-config' );
		}
	}

	public function admin_overview_page() {
		self::$LOGGER->debug( 'Generating overview page' );
		if ( current_user_can( SDUPS_COMPETITION_USER_CAPABILITY ) ) {
			$this->init_context();
			require_once plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-admin-overview-display.php';
		}
	}

	public function admin_create_form_page() {
		self::$LOGGER->debug( 'Generating form creation page' );
		if ( current_user_can( SDUPS_COMPETITION_USER_CAPABILITY ) ) {
			$subtitle = 'test';
			require_once plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-admin-create-form-display.php';
		}
	}

	public function admin_voting_forms_page() {
		self::$LOGGER->debug( 'Generating voting forms page' );
		if ( current_user_can( SDUPS_COMPETITION_USER_CAPABILITY ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-admin-forms-display.php';
		}
	}

	public function process_ajax() {
		$data = $_POST['data'] ?? null;
		if ( empty( $data ) ) {
			wp_send_json_error( [ 'message' => 'The browser request included no data!' ], 400 );
		}

		$data = json_decode( stripslashes( $data ) );

		$data->action = sanitize_text_field( $data->action );

		switch ( $data->action ) {
			case 'set_submission_url':
				$data->url = sanitize_url( $data->url );
				delete_option( $this->submission_url_option );
				add_option( $this->submission_url_option, $data->url );
				$forms    = $this->parse_submission_page( $data->url );
				$response = [
					[
						'select' => [
							'id'      => 'submission-form',
							'name'    => 'submission_form',
							'options' => $forms
						]
					]
				];
				break;
			case 'set_submission_form_id':
				$data->submission_form = sanitize_text_field( $data->submission_form );
				$fields                = $this->parse_submission_form( $data->submission_form );
				$response              = $this->get_field_choices( $fields );
				break;
			case 'get_submissions':
				$form     = SDUPS_Competition_Submission_Forms::get_active_form();
				$response = $this->get_submissions_for_form( $form, $data );
				break;
			case 'get_submission_preview':
				$form     = SDUPS_Competition_Submission_Form::from_preview_form( $data );
				$response = $this->get_submissions_for_form( $form, $data );
				break;
			case 'save_submission_form':
				$form = SDUPS_Competition_Submission_Form::from_preview_form( $data );
				$form->save();
				$url      = admin_url( 'admin.php' ) . '?page=' . self::get_main_menu_slug();
				$response = [ 'status' => 302, 'url' => $url ];
				break;
			default:
				wp_send_json_error( [ 'message' => 'The browser request did not include the required information!' ], 400 );
		}

		wp_send_json( $response );
	}

	private function parse_submission_page( $url ): array {
		if ( str_starts_with( $url, '/' ) ) {
			$url = site_url() . $url;
		}

		$postId = url_to_postid( $url );
		$return = array();
		if ( $postId != 0 ) {
			$post = get_post( $postId );
			if ( $post != null ) {
				$content = $post->post_content;

				$pattern = '<!-- wp:wpforms/form-selector {"formId":"(?<formId>\d+)"} /-->';
				preg_match_all( $pattern, $content, $matches );
				foreach ( $matches['formId'] as $formId ) {
					$post = get_post( $formId );
					if ( $post != null ) {
						$return[] = array(
							'value' => $formId,
							'name'  => $post->post_title
						);
					}
				}
			}
		}

		return $return;
	}

	private function parse_submission_form( $id ): array {
		$post   = get_post( $id );
		$form   = json_decode( $post->post_content );
		$return = array();
		foreach ( $form->fields as $field ) {
			$return[] = array(
				'value' => $field->id,
				'name'  => $field->label
			);
		}

		return $return;
	}

	private function get_field_choices( array $fields ): string {
		$choices = array();
		foreach ( SDUPS_Competition_Submission_Forms::get_field_attributes() as $attr ) {
			$choices[] = [
				'select' => [
					'id'      => $attr . '-field',
					'name'    => $attr . '_field',
					'options' => $fields
				]
			];
		}

		return __( json_encode( $choices ) );
	}

	private function get_submissions_for_form( ?SDUPS_Competition_Submission_Form $form, $data ): array {
		$submissions = $form !== null ? $form->get_submissions() : [];

		return [
			'data' => $submissions,
			'meta' => $data
		];
	}
}

SDUPS_Competition_Admin::$LOGGER = new SDUPS_Competition_Logger( SDUPS_Competition_Admin::class,
	SDUPS_Competition_Log_Level::DEBUG );