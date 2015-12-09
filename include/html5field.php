<?php
/*
 * Copyright (c) 2006-2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
 * Copyright (c) 2014-2015 Bastian Germann
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>


<form method="post" id="html5formfields">

	<label for="cf_edit_label"><?php _e('Field label', 'cforms2'); ?></label>
	<input type="text" id="cf_edit_label" name="cf_edit_label" value="">

	<?php if( in_array($_POST['type'], array('html5color','html5range','html5date','html5datetime','html5datetime-local','html5time','html5week','html5month','html5email','html5tel','html5url','html5search','html5number')) ) : ?>
		<table class="cf_multi_data1" border=0>
			<tr>
				<td><label for="cf_edit_checked_autocomplete"><?php _e('Autocomplete flag', 'cforms2'); ?></label></td>
				<td><label for="cf_edit_checked_autofocus"><?php _e('Autofocus flag', 'cforms2'); ?></label></td>
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
				<td><label for="cf_edit_min"><?php _e('Min value', 'cforms2'); ?></label></td>
				<td><label for="cf_edit_max"><?php _e('Max value', 'cforms2'); ?></label></td>
				<td><label for="cf_edit_step"><?php _e('Step value', 'cforms2'); ?></label></td>
			</tr>
			<tr>
				<td><input type="text" id="cf_edit_min" name="cf_edit_min" class=""></td>
				<td><input type="text" id="cf_edit_max" name="cf_edit_max" class=""></td>
				<td><input type="text" id="cf_edit_step" name="cf_edit_step" value=""></td>
			</tr>		
		</table>
	<?php endif; ?>	
	
	<?php if( in_array($_POST['type'], array('html5email','html5tel','html5url','html5search','html5number')) ) : ?>
		<label for="cf_edit_pattern"><?php _e('Pattern attribute', 'cforms2'); ?></label>
		<input type="text" id="cf_edit_pattern" name="cf_edit_pattern" class="">	
	<?php endif; ?>
	
	<?php if( in_array($_POST['type'], array('html5email','html5tel','html5url','html5search','html5number')) ) : ?>
		<label for="cf_edit_placeholder"><?php _e('Placeholder attribute', 'cforms2'); ?></label>
		<input type="text" id="cf_edit_placeholder" name="cf_edit_placeholder" value="">
	<?php endif; ?>	
	

		
	<label for="cf_edit_title"><?php _e('Input field title (displayed when mouse hovers over field)', 'cforms2'); ?></label>
	<input type="text" id="cf_edit_title" name="cf_edit_title" value="">
	
	<label for="cf_edit_customerr"><?php _e('Custom error message (make sure to enable custom, per field err messages!)', 'cforms2'); ?></label>
	<input type="text" id="cf_edit_customerr" name="cf_edit_customerr" value="">

</form>
