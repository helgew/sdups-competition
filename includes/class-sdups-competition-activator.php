<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/helgew/sdups-competition
 * @since      1.0.0
 *
 * @package    SDUPS_Competition
 * @subpackage SDUPS_Competition/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    SDUPS_Competition
 * @subpackage SDUPS_Competition/includes
 * @author     Helge Weissig <helgew@grajagan.org>
 */
class SDUPS_Competition_Activator {

	/**
	 * Plugin activation hook.
	 *
	 * Registers the post_type.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Trigger our function that registers the custom post type plugin.
		sdups_competition_setup_post_type();
		// Clear the permalinks after the post type has been registered.
		flush_rewrite_rules();
	}

	/**
	 * Register the "sdups-competition" custom post type
	 */
	function sdups_competition_setup_post_type() {
		register_post_type( SDUPS_COMPETITION_POST_TYPE, ['public' => true ] );
	}

}
