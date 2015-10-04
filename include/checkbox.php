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


<form method="post">

	<label for="cf_edit_label_left"><?php _e('Field label left of the checkbox...', 'cforms2'); ?></label>
	<input type="text" id="cf_edit_label_left" name="cf_edit_label_left" value="">

	<label for="cf_edit_label_right"><?php _e('...or define a field label to the right of the checkbox', 'cforms2'); ?></label>
	<input type="text" id="cf_edit_label_right" name="cf_edit_label_right" value="">

	<label for="cf_edit_checked"><?php _e('Set default state', 'cforms2'); ?></label>
	<input type="checkbox" id="cf_edit_checked" name="cf_edit_checked" class="allchk chkBox">

	<label for="cf_edit_title"><?php _e('Input field title (displayed when mouse hovers over field)', 'cforms2'); ?></label>
	<input type="text" id="cf_edit_title" name="cf_edit_title" value="">

	<label for="cf_edit_customerr"><?php _e('Custom error message (make sure to enable custom, per field err messages!)', 'cforms2'); ?></label>
	<input type="text" id="cf_edit_customerr" name="cf_edit_customerr" value="">

</form>
