<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

$main_menu_slug = SDUPS_Competition_Admin::get_main_menu_slug();
//Get the active tab from the $_GET param
$default_tab = null;
$tab         = $_GET['tab'] ?? $default_tab;

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/helgew/sdups-competition
 * @since      1.0.0
 *
 * @package    SDUPS_Competition
 * @subpackage SDUPS_Competition/admin/partials
 */
?>
<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <h2>Overview & Settings</h2>
    <h2 class="nav-tab-wrapper">
        <a href="?page=<?= $main_menu_slug ?>" data="submissions"
           class="nav-tab <?php if ( $tab === null ): ?>nav-tab-active<?php endif; ?>">Current Submissions</a>
        <a href="?page=<?= $main_menu_slug ?>&tab=forms" data="forms"
           class="nav-tab <?php if ( $tab === 'forms' ): ?>nav-tab-active<?php endif; ?>">Voting Forms</a>
        <a href="?page=<?= $main_menu_slug ?>&tab=config" data="config"
           class="nav-tab <?php if ( $tab === 'config' ): ?>nav-tab-active<?php endif; ?>">Configuration</a>
    </h2>
    <div id="content">
		<?php switch ( $tab ):
			case 'forms':
				include_once plugin_dir_path( __FILE__ ) . SDUPS_COMPETITION_PLUGIN_NAME . '-admin-overview-forms-display.php';
                break;
			case 'config':
				include_once plugin_dir_path( __FILE__ ) . SDUPS_COMPETITION_PLUGIN_NAME . '-admin-overview-config-display.php';
                break;
            default:
                include_once plugin_dir_path(__FILE__ ) . SDUPS_COMPETITION_PLUGIN_NAME . '-admin-overview-submissions-display.php';
                break;
		endswitch; ?>
    </div>
</div>
