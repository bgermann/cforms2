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

class cforms2_fieldtype_html5 extends cforms2_fieldtype_multi_id {

    protected function __construct($id, $name) {
        parent::__construct($id, $name, true);

    }

    protected function get_text_inputs() {
        return array(
            "cf_edit_label" => __('Field label', 'cforms2'),
            "cf_edit_title" => __('Input field title (displayed when mouse hovers over field)', 'cforms2'),
            "cf_edit_customerr" => __('Custom error message (make sure to enable custom, per field err messages!)', 'cforms2')
        );

    }

    protected function render_additional_settings() {
        $out = '';

        if (in_array($_POST['type'], array('html5color', 'html5range', 'html5date', 'html5datetime', 'html5datetime-local', 'html5time', 'html5week', 'html5month', 'html5email', 'html5tel', 'html5url', 'html5search', 'html5number'))) {
            $out .= '<table class="cf_multi_data1" border="0"><tr>';
            $out .= '<td><label for="cf_edit_checked_autocomplete">'
                    . __('Autocomplete flag', 'cforms2')
                    . '</label></td>';
            $out .= '<td><label for="cf_edit_checked_autofocus">'
                    . __('Autofocus flag', 'cforms2')
                    . '</label></td>';
            $out .= '</tr><tr>';
            $out .= '<td><input type="checkbox" id="cf_edit_checked_autocomplete" name="cf_edit_checked_autocomplete" class="allchk chkBox" /></td>';
            $out .= '<td><input type="checkbox" id="cf_edit_checked_autofocus" name="cf_edit_checked_autofocus" class="allchk chkBox" /></td>';
            $out .= '</tr></table>';
        }

        if (in_array($_POST['type'], array('html5range', 'html5date', 'html5datetime', 'html5datetime-local', 'html5time', 'html5week', 'html5month', 'html5number'))) {
            $out .= '<table class="cf_multi_data2" border="0"><tr>'
                    . '<td><label for="cf_edit_min">' . __('Min value', 'cforms2') . '</label></td>'
                    . '<td><label for="cf_edit_max">' . __('Max value', 'cforms2') . '</label></td>'
                    . '<td><label for="cf_edit_step">' . __('Step value', 'cforms2') . '</label></td>'
                    . '</tr><tr>';
            $out .= '<td><input type="text" id="cf_edit_min" name="cf_edit_min" /></td>'
                    . '<td><input type="text" id="cf_edit_max" name="cf_edit_max" /></td>'
                    . '<td><input type="text" id="cf_edit_step" name="cf_edit_step" value="" /></td>';
            $out .= '</tr></table>';
        }

        if (in_array($_POST['type'], array('html5email', 'html5tel', 'html5url', 'html5search', 'html5number'))) {
            $out .= '<label for="cf_edit_pattern">' . __('Pattern attribute', 'cforms2') . '</label>'
                    . '<input type="text" id="cf_edit_pattern" name="cf_edit_pattern" />';
        }

        if (in_array($_POST['type'], array('html5email', 'html5tel', 'html5url', 'html5search', 'html5number'))) {
            $out .= '<label for="cf_edit_placeholder">' . __('Placeholder attribute', 'cforms2') . '</label>'
                    . '<input type="text" id="cf_edit_placeholder" name="cf_edit_placeholder" value="" />';
        }

        return $out;

    }

    public static function register() {
        $types = array(
            'html5color' => 'HTML5 ' . __('Color Field', 'cforms2'),
            'html5date' => 'HTML5 ' . __('Date Field', 'cforms2'),
            'html5datetime' => 'HTML5 ' . __('Date/Time Field', 'cforms2'),
            'html5datetime-local' => 'HTML5 ' . __('Date/Time (local) Field', 'cforms2'),
            'html5email' => 'HTML5 ' . __('Email Field', 'cforms2'),
            'html5month' => 'HTML5 ' . __('Month Field', 'cforms2'),
            'html5number' => 'HTML5 ' . __('Number Field', 'cforms2'),
            'html5range' => 'HTML5 ' . __('Range Field', 'cforms2'),
            'html5search' => 'HTML5 ' . __('Search Field', 'cforms2'),
            'html5time' => 'HTML5 ' . __('Time Field', 'cforms2'),
            'html5url' => 'HTML5 ' . __('URL Field', 'cforms2'),
            'html5week' => 'HTML5 ' . __('Week Field', 'cforms2'),
            'html5tel' => 'HTML5 ' . __('Telephone Number Field', 'cforms2')
        );
        foreach ($types as $id => $label) {
            $t = new cforms2_fieldtype_html5($id, $label);
            $t->register_at_filter();
        }

    }

}
