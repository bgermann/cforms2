<?php
### supporting WP2.6 wp-load & custom wp-content / plugin dir
if ( file_exists('../../abspath.php') )
	include_once('../../abspath.php');
else
	$abspath='../../../../../';

if ( file_exists( $abspath . 'wp-load.php') )
	require_once( $abspath . 'wp-load.php' );
else
	require_once( $abspath . 'wp-config.php' );

load_plugin_textdomain( 'cforms' );
?>


<form method="post" id="html5formfields">

	<label for="cf_edit_label"><?php _e('Field label', 'cforms'); ?></label>
	<input type="text" id="cf_edit_label" name="cf_edit_label" value="">

	<?php if( in_array($_POST['type'], array('html5color','html5range','html5date','html5datetime','html5datetime-local','html5time','html5week','html5month','html5email','html5tel','html5url','html5search','html5number')) ) : ?>
		<table class="cf_multi_data1" border=0>
			<tr>
				<td><label for="cf_edit_checked_autocomplete"><?php _e('Autocomplete flag', 'cforms'); ?></label></td>
				<td><label for="cf_edit_checked_autofocus"><?php _e('Autofocus flag', 'cforms'); ?></label></td>
			</tr>
			<tr>
				<td><input type="checkbox" id="cf_edit_checked_autocomplete" name="cf_edit_checked_autocomplete" class="allchk chkBox"></td>
				<td><input type="checkbox" id="cf_edit_checked_autofocus" name="cf_edit_checked_autofocus" class="allchk chkBox"></td>
			</tr>		
		</table>
	<?php endif; ?>

	<?php if( in_array($_POST['type'], array('html5range','html5date','html5datetime','html5datetime-local','html5time','html5week','html5month','html5number')) ) : ?>
		<table class="cf_multi_data2" border=0>
			<tr>
				<td><label for="cf_edit_min"><?php _e('Min value', 'cforms'); ?></label></td>
				<td><label for="cf_edit_max"><?php _e('Max value', 'cforms'); ?></label></td>
				<td><label for="cf_edit_step"><?php _e('Step value', 'cforms'); ?></label></td>
			</tr>
			<tr>
				<td><input type="text" id="cf_edit_min" name="cf_edit_min" class=""></td>
				<td><input type="text" id="cf_edit_max" name="cf_edit_max" class=""></td>
				<td><input type="text" id="cf_edit_step" name="cf_edit_step" value=""></td>
			</tr>		
		</table>
	<?php endif; ?>	
	
	<?php if( in_array($_POST['type'], array('html5email','html5tel','html5url','html5search','html5number')) ) : ?>
		<label for="cf_edit_pattern"><?php _e('Pattern attribute', 'cforms'); ?></label>
		<input type="text" id="cf_edit_pattern" name="cf_edit_pattern" class="">	
	<?php endif; ?>
	
	<?php if( in_array($_POST['type'], array('html5email','html5tel','html5url','html5search','html5number')) ) : ?>
		<label for="cf_edit_placeholder"><?php _e('Placeholder attribute', 'cforms'); ?></label>
		<input type="text" id="cf_edit_placeholder" name="cf_edit_placeholder" value="">
	<?php endif; ?>	
	

		
	<label for="cf_edit_title"><?php _e('Input field title (displayed when mouse hovers over field)', 'cforms'); ?></label>
	<input type="text" id="cf_edit_title" name="cf_edit_title" value="">
	
	<label for="cf_edit_customerr"><?php _e('Custom error message (make sure to enable custom, per field err messages!)', 'cforms'); ?></label>
	<input type="text" id="cf_edit_customerr" name="cf_edit_customerr" value="">

</form>