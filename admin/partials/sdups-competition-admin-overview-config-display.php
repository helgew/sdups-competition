<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Provide a admin area's configuration tab view for the plugin
 *
 * @link       https://github.com/helgew/sdups-competition
 * @since      1.0.0
 *
 * @package    SDUPS_Competition
 * @subpackage SDUPS_Competition/admin/partials
 */
?>
<div class="forms-container">
    <div id="submission-form-form-container">
        <form action="" method="post" class="ajax" enctype="multipart/form-data" id="submission-form-form">
            <h3>STEP 1: Submission Page</h3>
            <p>The URL for the submission form is used to determine other configuration parameters. If this
                URL should ever change, it will have to be changed here.</p>
            <div class="error-message"></div>
            <label>
                <b>URL: </b>
                <input type="text" placeholder="Please enter the submission form URL" name="url" required
                       class="url" size="50" value="<?php echo __( $this->context['submission_url'] ) ?>">
                <input type="hidden" name="action" value="set_submission_url">
                <input type="submit" class="submitbtn" value="submit">
            </label>
        </form>
    </div>
    <div id="wpform-picker-form-container">
        <form action="" method="post" class="ajax" enctype="multipart/form-data" id="wpform-picker-form">
            <h3>STEP 2: Submission Form </h3>
            <p>The submission page may contain more than one form. Select the correct from from the drop-down.</p>
            <div class="error-message"></div>
            <label>
                <b>Form: </b>
                <div class="form-content inline">
                    <select id="submission-form" name="submission_form">
						<?php if ( sizeof( $this->context['possible_forms'] ) == 0 ): ?>
                            <option value="-1">-- No Forms Found --</option>
						<?php endif; ?>
						<?php foreach ( $this->context['possible_forms'] as $form ): ?>
                            <option value="<?= $form['value'] ?>"<?= array_key_exists( 'selected', $form ) ? ' selected' : '' ?>><?= $form['name'] ?></option>
						<?php endforeach; ?>
                    </select>
                </div>
                <input type="hidden" name="action" value="set_submission_form_id">
                <input type="submit" class="submitbtn" value="submit">
            </label>
        </form>
    </div>
    <div id="wpform-fields-form-container">
        <form action="" method="post" enctype="multipart/form-data" id="wpform-fields-form">
            <h3>STEP 3: Form Fields</h3>
            <p>Select the appropriate form fields for the attributes listed below.</p>
            <div class="error-message"></div>
            <label>
                <table class="responsive">
					<?php foreach ( $this->context['field_attrs'] as $attr ): ?>
                        <tr>
                            <td>
                                <b><?= ucfirst( $attr ) ?>: </b>
                            </td>
                            <td>
                                <select id="<?= $attr ?>-field" name="<?= $attr ?>_field">
									<?php if ( sizeof( $this->context['possible_fields'] ) == 0 ): ?>
                                        <option value="-1">-- No Fields Found --</option>
									<?php endif; ?>
									<?php foreach ( $this->context['possible_fields'] as $field ): ?>
                                        <option value="<?= $field['value'] ?>"><?= $field['name'] ?></option>
									<?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
					<?php endforeach; ?>
                </table>
                <input type="hidden" name="form_id" value="<?= $this->context['formId'] ?>">
                <input type="hidden" name="action" value="get_submission_preview">
                <input type="submit" class="submitbtn" value="submit">
            </label>
        </form>
    </div>
    <div id="confirmation-form-container" style="width: 80%; display: none">
        <form action="" method="post" class="ajax" enctype="multipart/form-data" id="confirmation-form">
            <h3>STEP 4: Confirmation</h3>
            <p>Confirm that the data shown below is what would be expected. Note that if the data above reflects
                recent changes, there might not be any data displayed below. In that case, please wait until data
                has been submitted with the new form.</p>
            <label>
                <input type="hidden" name="action" value="save_submission_form">
                <input type="submit" class="submitbtn" value="Looks Good!">
            </label>
        </form>
        <table id="submission-data-preview" class="stripe" style="width: 100%; margin-top: 20px;">
            <caption>Form Submissions</caption>
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
    <div id="confirm_popup" style="display:none">
        <h1>Hello World!</h1>
    </div>
</div>
