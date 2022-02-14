<?php

/**
 * On-the-fly admin notices.
 *
 * @package    SDUPS_Competition
 * @subpackage SDUPS_Competition/includes
 * @author     Helge Weissig <helgew@grajagan.org>
 */
class SDUPS_Competition_Admin_Notices {

	/**
	 * Single instance holder.
	 *
	 * @since 1.0.0
	 * @var mixed
	 */
	private static $instance = null;

	/**
	 * Added notices.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $notices = array();

	/**
	 * Get the instance.
	 *
	 * @return SDUPS_Competition_Admin_Notices
	 * @since 1.0.0
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new SDUPS_Competition_Admin_Notices();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
	}

	/**
	 * Display the notices.
	 *
	 * @since 1.0.0
	 */
	public function display() {
		if ( current_user_can( SDUPS_COMPETITION_USER_CAPABILITY ) &&
		     str_starts_with( get_current_screen()->parent_base, SDUPS_COMPETITION_PLUGIN_NAME ) ) {
			echo implode( ' ', $this->notices );
		}
	}

	/**
	 * Add notice to instance property.
	 *
	 * @param string $message Message to display.
	 * @param string $type Type of the notice (default: '').
	 *
	 * @since 1.0.0
	 *
	 */
	public static function add( $message, $type = '', $id = '' ) {
		$instance = self::instance();
		$id       = SDUPS_COMPETITION_PLUGIN_NAME . '-notice-' . ( $id !== '' ? $id : count( $instance->notices ) + 1 );
		$type     = ! empty( $type ) ? 'notice-' . $type : '';
		$notice   = sprintf( '<div class="notice is-dismissible %s" id="%s">%s</div>', $type, $id, wpautop( $message ) );

		$instance->notices[] = $notice;
	}

	/**
	 * Add Info notice.
	 *
	 * @param string $message Message to display.
	 * @param string $id an optional ID string to use for the div
	 *
	 * @since 1.0.0
	 *
	 */
	public static function info( $message, $id = '' ) {
		self::add( $message, 'info', $id );
	}

	/**
	 * Add Error notice.
	 *
	 * @param string $message Message to display.
	 * @param string $id an optional ID string to use for the div
	 *
	 * @since 1.0.0
	 *
	 */
	public static function error( $message, $id = '' ) {
		self::add( $message, 'error', $id );
	}

	/**
	 * Add Success notice.
	 *
	 * @param string $message Message to display.
	 * @param string $id an optional ID string to use for the div
	 *
	 * @since 1.0.0
	 *
	 */
	public static function success( $message, $id = '' ) {
		self::add( $message, 'success', $id );
	}

	/**
	 * Add Warning notice.
	 *
	 * @param string $message Message to display.
	 * @param string $id an optional ID string to use for the div
	 *
	 * @since 1.0.0
	 *
	 */
	public static function warning( $message, $id = '' ) {
		self::add( $message, 'warning', $id );
	}
}