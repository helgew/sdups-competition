<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Provide a admin area's voting forms tab view for the plugin
 *
 * @link       https://github.com/helgew/sdups-competition
 * @since      1.0.0
 *
 * @package    SDUPS_Competition
 * @subpackage SDUPS_Competition/admin/partials
 */
?>
<div style="margin-top: 40px;">
    <table id="submissions" class="stripe" style="width: 100%;">
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Date</th>
            <th>Division</th>
            <th>Category</th>
            <th>Upload</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Date</th>
            <th>Division</th>
            <th>Category</th>
            <th>Upload</th>
        </tr>
        </tfoot>
    </table>
</div>