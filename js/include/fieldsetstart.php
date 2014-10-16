<?php add_action( 'wp_ajax_cforms2_field_fieldsetstart', 'cforms2_field_fieldsetstart' );

function cforms2_field_fieldsetstart() {
check_admin_referer( 'cforms2_field_fieldsetstart' );

?>


<form method="post">

	<label for="cf_edit_label"><?php _e('Name of Fieldset ( only required for <em>New Fieldset</em> )', 'cforms'); ?></label>
	<input type="text" id="cf_edit_label" name="cf_edit_label" value="">

</form>
<?php die();
}
