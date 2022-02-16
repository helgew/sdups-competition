<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

$menu_slug = SDUPS_Competition_Admin::get_manage_menu_slug();
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
    <h1><?php echo esc_html( get_admin_page_title() ); ?> - Manage</h1>
    <h2 class="nav-tab-wrapper">
        <a href="?page=<?= $menu_slug ?>" data="submissions"
           class="nav-tab <?php if ( $tab === null ): ?>nav-tab-active<?php endif; ?>">Current Submissions</a>
        <a href="?page=<?= $menu_slug ?>&tab=create-form" data="create-form"
           class="nav-tab <?php if ( $tab === 'create-form' ): ?>nav-tab-active<?php endif; ?>">Create Voting Form</a>
        <a href="?page=<?= $menu_slug ?>&tab=forms" data="forms"
           class="nav-tab <?php if ( $tab === 'forms' ): ?>nav-tab-active<?php endif; ?>">Voting Forms</a>
    </h2>
    <div id="content">
		<?php switch ( $tab ):
			case 'create-form':
				include_once plugin_dir_path( __FILE__ ) . SDUPS_COMPETITION_PLUGIN_NAME . '-admin-create-form-display.php';
                break;
			case 'forms':
				include_once plugin_dir_path( __FILE__ ) . SDUPS_COMPETITION_PLUGIN_NAME . '-admin-forms-display.php';
				break;
            default:
                include_once plugin_dir_path(__FILE__ ) . SDUPS_COMPETITION_PLUGIN_NAME . '-admin-submissions-display.php';
                break;
		endswitch; ?>
    </div>
</div>
