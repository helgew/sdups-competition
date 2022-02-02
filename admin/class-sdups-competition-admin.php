<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
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
 * @author     Your Name <email@example.com>
 */
class SDUPS_Competition_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $sdups_competition    The ID of this plugin.
	 */
	private $sdups_competition;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $sdups_competition       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $sdups_competition, $version ) {

		$this->sdups_competition = $sdups_competition;
		$this->version = $version;

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

		wp_enqueue_style( $this->sdups_competition, plugin_dir_url( __FILE__ ) . 'css/sdups-competition-admin.css', array(), $this->version, 'all' );

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

		wp_enqueue_script( $this->sdups_competition, plugin_dir_url( __FILE__ ) . 'js/sdups-competition-admin.js', array( 'jquery' ), $this->version, false );

	}

}
