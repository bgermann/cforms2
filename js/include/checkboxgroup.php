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

add_action( 'wp_ajax_cforms2_field_checkboxgroup', 'cforms2_field_checkboxgroup' );

function cforms2_field_checkboxgroup() {
check_admin_referer( 'cforms2_field_checkboxgroup' );

?>


<form method="post">

	<label for="cf_edit_label_group"><?php _e('Field label', 'cforms'); ?></label>
	<input type="text" id="cf_edit_label_group" name="cf_edit_label_group" value="">

	<div class="cf_edit_groups_header">
		<span class="cf_option"><?php _e('Check box/radio box option (displayed)', 'cforms'); ?></span>
		<span class="cf_optVal"><?php _e('Optional value (transmitted)', 'cforms'); ?></span>
		<span class="cf_chked" title="<?php _e('Set default state', 'cforms'); ?>"></span>
		<span class="cf_br" title="<?php _e('Carriage return / New Line', 'cforms'); ?>"></span>
	</div>

	<div id="cf_edit_groups">
	</div>
	<div class="add_group_item"><a href="#" id="add_group_button" class="cf_edit_plus"></a></div>

	<label style="clear:left; padding-top:5px;" for="cf_edit_title"><?php _e('Input field title (displayed when mouse hovers over field)', 'cforms'); ?></label>
	<input type="text" id="cf_edit_title" name="cf_edit_title" value="">

	<!--label for="cf_edit_customerr"><?php _e('Custom error message (make sure to enable custom, per field err messages!)', 'cforms'); ?></label-->
	<!--input type="text" id="cf_edit_customerr" name="cf_edit_customerr" value=""-->

</form>
<?php die();
}
