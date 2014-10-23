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

add_action( 'wp_ajax_cforms2_field_fieldsetstart', 'cforms2_field_fieldsetstart' );

function cforms2_field_fieldsetstart() {
check_admin_referer( 'cforms2_field_fieldsetstart' );

?>


<form method="post">

	<label for="cf_edit_label"><?php _e('Name of Fieldset ( only required for <em>New Fieldset</em> )', 'cforms'); ?></label>
	<input type="text" id="cf_edit_label" name="cf_edit_label" value="">

</form>
<?php die();
}
