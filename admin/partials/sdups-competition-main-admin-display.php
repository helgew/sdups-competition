<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

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
        <a href="#" data="submissions" class="nav-tab nav-tab-active">Current Submissions</a>
        <a href="#" data="forms" class="nav-tab">Voting Forms</a>
        <a href="#" data="settings" class="nav-tab">Settings</a>
    </h2>
    <div id="content">
        <div id="submissions" style="display: none">Current Submissions Content</div>
        <div id="forms" style="display: none">Voting Forms</div>
        <div id="settings" style="display: none">Settings Content</div>
    </div>
</div>
