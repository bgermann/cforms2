<?php
/*
 * Copyright (c) 2006-2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
 * Copyright (c) 2014      Bastian Germann
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

add_action( 'wp_ajax_cforms2_field_textfield', 'cforms2_field_textfield' );

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
