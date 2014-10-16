<?php add_action( 'wp_ajax_cforms2_field_textonly', 'cforms2_field_textonly' );

function cforms2_field_textonly() {
check_admin_referer( 'cforms2_field_textonly' );

?>


<form method="post">

	<label for="cf_edit_label"><?php _e('Text (HTML is supported)', 'cforms'); ?></label>
	<input type="text" id="cf_edit_label" name="cf_edit_label" value="">

	<label for="cf_edit_css"><?php _e('CSS (assigns class to this form element)', 'cforms'); ?></label>
	<input type="text" id="cf_edit_css" name="cf_edit_css" value="">

	<label for="cf_edit_style"><?php echo sprintf(__('Inline style (e.g. %s)', 'cforms'),'<strong>color:red; font-size:11px;</strong>'); ?></label>
	<input type="text" id="cf_edit_style" name="cf_edit_style" value="">

</form>
<?php die();
}
