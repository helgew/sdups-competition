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
	private string $section;
	private array $content;
	private int $index;

	public function __construct( string $page ) {
		preg_match( '/-([^-]+)$/', $page, $matches );
		if ( sizeof( $matches ) === 2 ) {
			$this->section = $matches[1] . '-help';
		}
		add_action( 'load-' . $page, [ $this, 'add_help_tabs' ] );
	}

	public function add_help_tabs() {
		$screen = get_current_screen();

		foreach ( glob( plugin_dir_path( __FILE__ ) .
		                'partials/' . $this->section . '/*.php' ) as $file ) {
			ob_start();
			include_once $file;
			$this->content[$this->index]['content'] = ob_get_contents();
			ob_end_clean();
		}

		ksort($this->content);
		foreach ($this->content as $tab) {
			$screen->add_help_tab(
				array(
					'id'      => $tab['id'],
					'title'   => $tab['title'],
					'content' => $tab['content']
				)
			);
		}
	}
}
