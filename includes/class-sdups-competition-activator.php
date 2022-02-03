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

	static $LOGGER;

	/**
	 * @var string the user name authorized to set-up and score competitions.
	 */
	private static $user_name = 'competition';

	/**
	 * @var string the email address for the authorized user.
	 */
	private static $user_email = 'competition@sdups.org';

	/**
	 * Plugin activation hook.
	 *
	 * Registers the post_type.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Create the competition user, if necessary, and assign our capability to them
		self::find_or_create_competition_user();

		// Trigger our function that registers the custom post type plugin.
		self::sdups_competition_setup_post_type();

		// Clear the permalinks after the post type has been registered.
		flush_rewrite_rules();
	}

	/**
	 * Looks for the competition user by username or email and creates the user if they do not
	 * exists. Assigns a custom capability to them and also to the administrator role.
	 *
	 * @return void
	 */
	private static function find_or_create_competition_user() {
		$user_id = username_exists( self::$user_name );
		if ( ! $user_id ) {
			$user_id = email_exists( self::$user_email );
		}

		// check that the email address does not belong to a registered user
		if ( ! $user_id ) {
			self::$LOGGER->info( "Creating the competiton user" );
			// create a random password
			$random_password = wp_generate_password( 12, false );
			// create the user
			$user_id = wp_create_user(
				self::$user_name,
				$random_password,
				self::$user_email
			);
			$user    = new WP_User( $user_id );
			$user->set_role( 'author' );
		} else {
			self::$LOGGER->info( "Using existing user with ID $user_id" );
			$user = new WP_User( $user_id );
		}

		$user->add_cap( SDUPS_COMPETITION_USER_CAPABILITY );
		$administrator = get_role( 'administrator' );
		$administrator->add_cap( SDUPS_COMPETITION_USER_CAPABILITY );

		// for testing
		$user_id = email_exists( 'helge@sdups.org' );
		if ( $user_id ) {
			$user = new WP_User( $user_id );
			$user->for_site( 2 );
			$user->add_cap( SDUPS_COMPETITION_USER_CAPABILITY );
		}
	}

	/**
	 * Register the "sdups-competition" custom post type
	 */
	private static function sdups_competition_setup_post_type() {
		register_post_type( SDUPS_COMPETITION_POST_TYPE );
	}

}

SDUPS_Competition_Activator::$LOGGER = new SDUPS_Competition_Logger( SDUPS_Competition_Activator::class,
	SDUPS_Competition_Log_Level::DEBUG );