<?php

/**
 * The plugin bootstrap file
 *
 * The plugin architecture was adopted from the WordPress plugin boilerplate
 * https://github.com/DevinVinson/WordPress-Plugin-Boilerplate.git
 *
 * @link              https://sdups.org
 * @since             1.0.0
 * @package           SDUPS_Competition
 *
 * @wordpress-plugin
 * Plugin Name: SDUPS Competition
 * Plugin URI: https://sdups.org
 * Description: This plugin generates voting screens and tabulates results for the SDUPS monthly
 * competition.
 * Version: 1.0.0
 * Author: helgew@grajagan.org
 * Author URI: https://www.grajagan.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sdups-competiton
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'SDUPS_COMPETITION_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sdups-competiton-activator.php
 */
function activate_sdups_competiton() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sdups-competiton-activator.php';
	SDUPS_Competition_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sdups-competiton-deactivator.php
 */
function deactivate_sdups_competiton() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sdups-competiton-deactivator.php';
	SDUPS_Competition_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_sdups_competiton' );
register_deactivation_hook( __FILE__, 'deactivate_sdups_competiton' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sdups-competiton.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sdups_competiton() {

	$plugin = new SDUPS_Competition();
	$plugin->run();

}
run_sdups_competiton();
