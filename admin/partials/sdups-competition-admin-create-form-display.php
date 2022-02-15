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
    <h2>Create a New Voting Form</h2>
    <div id="categories-form-container">
        <form action="" method="post" enctype="multipart/form-data" id="categories-form">
            <h4>STEP 1: Category Selection</h4>
            <p>Select the category from the drop-down menu to start creating a voting form.</p>
            <div class="error-message"></div>
            <label>
                <select id="category" name="category">
					<?php if ( sizeof( $this->context['categories'] ) == 0 ): ?>
                        <option value="-1">-- No Categories Found --</option>
					<?php endif; ?>
					<?php foreach ( $this->context['categories'] as $category ): ?>
                        <option value="<?= $category ?>"><?= $category ?></option>
					<?php endforeach; ?>
                </select>
                <input type="hidden" name="action" value="get_submissions_by_category">
                <input type="submit" class="submitbtn" value="submit">
            </label>
        </form>
    </div>
    <div id="submissions-table-container" style="margin-top:50px; display:none;">
        <form action="" method="post" enctype="multipart/form-data" id="submissions-form">
            <h4>STEP 2: Entry Selection</h4>
            <p>In the table below, unselect any entries you do not want to include in the voting form before clicking
                the button.</p>
            <label>
                <input type="hidden" name="action" value="save_voting_form">
                <input type="submit" class="submitbtn" value="Create Form">
            </label>
            <div style="margin-top: 20px;">
                <table id="submissions" class="stripe" style="width: 100%;">
                    <thead>
                    <tr>
                        <th>Use</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date</th>
                        <th>Division</th>
                        <th>Upload</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>Use</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date</th>
                        <th>Division</th>
                        <th>Upload</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </form>
    </div>
</div>