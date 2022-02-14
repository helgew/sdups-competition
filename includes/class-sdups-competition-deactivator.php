<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/helgew/sdups-competition
 * @since      1.0.0
 *
 * @package    SDUPS_Competition
 * @subpackage SDUPS_Competition/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    SDUPS_Competition
 * @subpackage SDUPS_Competition/includes
 * @author     Helge Weissig <helgew@grajagan.org>
 */
class SDUPS_Competition_Deactivator {

	/**
	 * Deactivate the plugin.
	 *
	 * Unregisters the post_type.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// Unregister the post type, so the rules are no longer in memory.
		unregister_post_type( SDUPS_COMPETITION_POST_TYPE );
		// Clear the permalinks to remove our post type's rules from the database.
		flush_rewrite_rules();

		// Deactivate (maybe!) font-awesome
		FortAwesome\FontAwesome_Loader::maybe_deactivate();
	}

}
