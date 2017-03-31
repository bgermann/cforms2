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

class cforms2_fieldtype_checkboxgroup extends cforms2_fieldtype_multi_id {

    protected function __construct($id, $name) {
        parent::__construct($id, $name, false);

    }

    protected function get_text_inputs() {
        return array(
            "cf_edit_label_group" => __('Field label', 'cforms2'),
            "cf_edit_title" => __('Input field title (displayed when mouse hovers over field)', 'cforms2'),
            "cf_edit_customerr" => __('Custom error message (make sure to enable custom, per field err messages!)', 'cforms2')
        );

    }

    protected function render_additional_settings() {
        $out = '<div class="cf_edit_groups_header">'
                . '<span class="cf_option">' . __('Check box/radio box option (displayed)', 'cforms2') . '</span>'
                . '<span class="cf_optVal">' . __('Optional value (transmitted)', 'cforms2') . '</span>'
                . '<span class="cf_chked dashicons dashicons-yes" title="' . __('Set default state', 'cforms2') . '"></span>'
                . '<span class="cf_br dashicons dashicons-editor-break" title="' . __('Carriage return / New Line', 'cforms2') . '"></span>'
                . '</div>';

        $out .= '<div id="cf_edit_groups"></div>';

        $out .= '<div class="add_group_item"><a href="#" id="add_group_button" class="cf_edit_plus dashicons dashicons-plus-alt"></a></div>';

        return $out;

    }

    public static function register() {
        $types = array(
            'checkboxgroup' => __('Check Box Group', 'cforms2'),
            'radiobuttons' => __('Radio Buttons', 'cforms2')
        );
        foreach ($types as $id => $label) {
            $t = new cforms2_fieldtype_checkboxgroup($id, $label);
            $t->register_at_filter();
        }

    }

}
