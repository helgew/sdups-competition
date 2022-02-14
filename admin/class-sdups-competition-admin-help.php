<?php

/**
 * Adds the help sections for our screens.
 *
 * @since      1.0.0
 * @package    SDUPS_Competition
 * @subpackage SDUPS_Competition/includes
 * @author     Helge Weissig <helgew@grajagan.org>
 */
class SDUPS_Competition_Admin_Help {
	private $plugin_name;

	public function __construct( $page, $plugin_name ) {
		$this->plugin_name = $plugin_name;
		add_action( 'load-' . $page, [$this, 'add_help_tabs']);
	}

	public function add_help_tabs() {
		$screen = get_current_screen();
		$screen->add_help_tab(
			array(
				'id' => 'main-screen-overview',
				'title' => 'Overview',
				'content' => $this->content('admin-overview'),
			)
		);

		$screen->add_help_tab(
			array(
				'id' => 'main-screen-submissions',
				'title' => 'Current Submissions',
				'content' => $this->content('admin-overview-submissions'),
			)
		);

		$screen->add_help_tab(
			array(
				'id' => 'main-screen-forms',
				'title' => 'Voting Forms',
				'content' => $this->content('admin-overview-forms'),
			)
		);

		$screen->add_help_tab(
			array(
				'id' => 'main-screen-settings',
				'title' => 'Configuration',
				'content' => $this->content('admin-overview-config'),
			)
		);
	}

	private function content($tab, $data = null) {
		( $data ) ? extract( $data ) : null;
		ob_start();
		include plugin_dir_path(__FILE__) . 'partials/' . $this->plugin_name . '-' . $tab . '-help.php';
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
}
