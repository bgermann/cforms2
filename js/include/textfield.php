<?php add_action( 'wp_ajax_cforms2_field_textfield', 'cforms2_field_textfield' );

function cforms2_field_textfield() {
check_admin_referer( 'cforms2_field_textfield' );

?>


<form method="post">

	<label for="cf_edit_label"><?php _e('Field label', 'cforms'); ?></label>
	<input type="text" id="cf_edit_label" name="cf_edit_label" value="">

	<label for="cf_edit_default"><?php _e('Default value', 'cforms'); ?></label>
	<input type="text" id="cf_edit_default" name="cf_edit_default" value="">

	<label for="cf_edit_regexp"><?php echo sprintf(__('Regular expression for field validation (e.g. %s). See Help! for more examples.', 'cforms'),'^[A-Za-z ]+$'); ?></label>
	<input type="text" id="cf_edit_regexp" name="cf_edit_regexp" value="">

	<label for="cf_edit_title"><?php _e('Input field title (displayed when mouse hovers over field)', 'cforms'); ?></label>
	<input type="text" id="cf_edit_title" name="cf_edit_title" value="">

	<label for="cf_edit_customerr"><?php _e('Custom error message (make sure to enable custom, per field err messages!)', 'cforms'); ?></label>
	<input type="text" id="cf_edit_customerr" name="cf_edit_customerr" value="">

</form>
<?php die();
}
