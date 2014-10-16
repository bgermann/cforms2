<?php add_action( 'wp_ajax_cforms2_field_checkbox', 'cforms2_field_checkbox' );

function cforms2_field_checkbox() {
check_admin_referer( 'cforms2_field_checkbox' );

?>


<form method="post">

	<label for="cf_edit_label_left"><?php _e('Field label left of the checkbox...', 'cforms'); ?></label>
	<input type="text" id="cf_edit_label_left" name="cf_edit_label_left" value="">

	<label for="cf_edit_label_right"><?php _e('...or define a field label to the right of the checkbox', 'cforms'); ?></label>
	<input type="text" id="cf_edit_label_right" name="cf_edit_label_right" value="">

	<label for="cf_edit_checked"><?php _e('Set default state', 'cforms'); ?></label>
	<input type="checkbox" id="cf_edit_checked" name="cf_edit_checked" class="allchk chkBox">

	<label for="cf_edit_title"><?php _e('Input field title (displayed when mouse hovers over field)', 'cforms'); ?></label>
	<input type="text" id="cf_edit_title" name="cf_edit_title" value="">

	<label for="cf_edit_customerr"><?php _e('Custom error message (make sure to enable custom, per field err messages!)', 'cforms'); ?></label>
	<input type="text" id="cf_edit_customerr" name="cf_edit_customerr" value="">

</form>
<?php die();
}
