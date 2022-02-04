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
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The context of this class. Used to store stuff for the different screens.
	 * @var array
	 */
	private $context = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		include_once plugin_dir_path( __FILE__ ) . 'class-' . $this->plugin_name . '-admin-help.php';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in SDUPS_Competition_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The SDUPS_Competition_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sdups-competition-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in SDUPS_Competition_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The SDUPS_Competition_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sdups-competition-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function add_sdups_admin_menus() {
		self::$LOGGER->debug( "Generating admin menu" );

		add_menu_page(
			'SDUPS Competition',
			'Competition',
			SDUPS_COMPETITION_USER_CAPABILITY,
			$this->plugin_name . '-admin',
			array( $this, 'main_admin_page' ),
			null,
			4
		);

		$main_page = add_submenu_page( $this->plugin_name . '-admin',
			'SDUPS Competition',
			'Overview',
			SDUPS_COMPETITION_USER_CAPABILITY,
			$this->plugin_name . '-admin',
			array( $this, 'main_admin_page' )
		);

		$help = new SDUPS_Competition_Admin_Help( $main_page, $this->plugin_name );

		add_submenu_page( $this->plugin_name . '-admin',
			'SDUPS Competition',
			'Create Form',
			SDUPS_COMPETITION_USER_CAPABILITY,
			$this->plugin_name . '-create-form',
			array( $this, 'create_form_page' )
		);

		add_submenu_page( $this->plugin_name . '-admin',
			'SDUPS Competition',
			'Voting Forms',
			SDUPS_COMPETITION_USER_CAPABILITY,
			$this->plugin_name . '-forms-admin',
			array( $this, 'forms_admin_page' )
		);
	}

	public function main_admin_page() {
		self::$LOGGER->debug( "Generating main admin page" );
		if ( current_user_can( SDUPS_COMPETITION_USER_CAPABILITY ) ) {
			$submission_forms  = SDUPS_Competition_Submission_Forms::instance();
			require_once plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-main-admin-display.php';
		}
	}

	public function create_form_page() {
		self::$LOGGER->debug( "Generating form creation page" );
		if ( current_user_can( SDUPS_COMPETITION_USER_CAPABILITY ) ) {
			$subtitle = 'test';
			require_once plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-create-form-display.php';
		}
	}

	public function forms_admin_page() {
		self::$LOGGER->debug( "Generating forms admin page" );
		if ( current_user_can( SDUPS_COMPETITION_USER_CAPABILITY ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-forms-admin-display.php';
		}
	}
}

SDUPS_Competition_Admin::$LOGGER = new SDUPS_Competition_Logger( SDUPS_Competition_Admin::class,
	SDUPS_Competition_Log_Level::DEBUG );