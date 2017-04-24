<?php
/*
 * Copyright (c) 2017 Bastian Germann
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

class cforms2_fieldtype_fieldsetstart extends cforms2_fieldtype {

    public function get_id() {
        return 'fieldsetstart';

    }

    public function get_name() {
        return __('Begin Fieldset', 'cforms2');

    }

    public static function register() {
        $t = new cforms2_fieldtype_fieldsetstart();
        $t->register_at_filter();

    }

    protected function render_additional_settings() {
        return '<!--<label for="cf_edit_checked">' . __('Show this and following fieldsets only if all previous fields were filled out correctly.', 'cforms2')
                . '</label><input type="checkbox" id="cf_edit_checked" name="cf_edit_checked" class="allchk chkBox" />-->';

    }

}
