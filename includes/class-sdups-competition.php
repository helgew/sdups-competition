<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/helgew/sdups-competition
 * @since      1.0.0
 *
 * @package    SDUPS_Competition
 * @subpackage SDUPS_Competition/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    SDUPS_Competition
 * @subpackage SDUPS_Competition/includes
 * @author     Helge Weissig <helgew@grajagan.org>
 */
class SDUPS_Competition {

	static $LOGGER;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      SDUPS_Competition_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the sdups competition and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SDUPS_COMPETITION_VERSION' ) ) {
			$this->version = SDUPS_COMPETITION_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = SDUPS_COMPETITION_PLUGIN_NAME;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		self::$LOGGER->debug( "Initialized Plugin version " . $this->version );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - SDUPS_Competition_Loader. Orchestrates the hooks of the plugin.
	 * - SDUPS_Competition_i18n. Defines internationalization functionality.
	 * - SDUPS_Competition_Admin. Defines all hooks for the admin area.
	 * - SDUPS_Competition_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * Load our vendor packages automatically.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'vendor/autoload.php';

		/**
		 * The font-awesome package for awesome fonts.
		 */
		require_once plugin_dir_path( __DIR__ ) .
		             'vendor/fortawesome/wordpress-fontawesome/index.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-' .
		             $this->get_plugin_name() . '-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-' .
		             $this->get_plugin_name() . '-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-' .
		             $this->get_plugin_name() . '-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'public/class-' .
		             $this->get_plugin_name() . '-public.php';

		/**
		 * The class that handles all its subclasses and the DB schema.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-' .
		             $this->get_plugin_name() . '-db.php';

		/**
		 * The class that handles access to GMail via the Google Client API.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-' .
		             $this->get_plugin_name() . '-gmail.php';

		SDUPS_Competition_DB::load_dependencies();

		$this->loader = new SDUPS_Competition_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the SDUPS_Competition_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new SDUPS_Competition_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new SDUPS_Competition_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_sdups_admin_menus' );
		$this->loader->add_action( 'wp_ajax_process_ajax', $plugin_admin, 'process_ajax' );
		$this->loader->add_action( 'wp_ajax_nopriv_process_ajax', $plugin_admin, 'process_ajax' );


		$this->loader->add_action( 'admin_notices', SDUPS_Competition_Admin_Notices::instance(), 'display' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new SDUPS_Competition_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'send_mail', SDUPS_Competition_GMail::instance(), 'send_mail_delayed' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    SDUPS_Competition_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}
}

use function FortAwesome\fa;

add_action(
	'font_awesome_preferences',
	function () {
		fa()->register(
			array(
				'name' => 'plugin ' . SDUPS_COMPETITION_PLUGIN_NAME
			)
		);
	}
);

/**
 * The logging class. Need to put this here or in the plugin's php file to be able to log in
 * this class.
 */
require_once plugin_dir_path( __DIR__ ) . 'includes/class-sdups-competition-logger.php';

SDUPS_Competition::$LOGGER = new SDUPS_Competition_Logger( SDUPS_Competition::class,
	SDUPS_Competition_Log_Level::DEBUG );