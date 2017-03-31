<?php
/*
 * Copyright (c) 2006-2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
 * Copyright (c) 2014-2017 Bastian Germann
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

class cforms2_fieldtype_textonly extends cforms2_fieldtype {

    public function get_id() {
        return 'textonly';

    }

    public function get_name() {
        return __('Text only (no input)', 'cforms2');

    }

    protected function get_text_inputs() {
        return array(
            "cf_edit_label" => __('Text (HTML is supported)', 'cforms2'),
            "cf_edit_css" => __('CSS (assigns class to this form element)', 'cforms2'),
            "cf_edit_style" => sprintf(__('Inline style (e.g. %s)', 'cforms2'), '<strong>color:red; font-size:11px;</strong>')
        );

    }

    public static function register() {
        $t = new cforms2_fieldtype_textonly();
        $t->register_at_filter();

    }

}
