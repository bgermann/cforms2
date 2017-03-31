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

class cforms2_fieldtype_selectbox extends cforms2_fieldtype_multi_id {

    protected function __construct($id, $name, $special) {
        parent::__construct($id, $name, $special);

    }

    protected function get_text_inputs() {
        return array(
            "cf_edit_label_select" => __('Field label', 'cforms2'),
            "cf_edit_title" => __('Input field title (displayed when mouse hovers over field)', 'cforms2'),
            "cf_edit_customerr" => __('Custom error message (make sure to enable custom, per field err messages!)', 'cforms2')
        );

    }

    protected function render_additional_settings() {
        $out = '<div class="cf_edit_groups_header">';
        $out .= '<span class="cf_option">' . __('Check box/radio box option (displayed)', 'cforms2') . '</span>'
                . '<span class="cf_optVal">' . __('Optional value (transmitted)', 'cforms2') . '</span>'
                . '<span class="cf_chked dashicons dashicons-yes" title="' . __('Set default state', 'cforms2') . '"></span></div>';
        $out .= '<div id="cf_edit_groups"></div>';
        $out .= '<div class="add_group_item"><a href="#" id="add_group_button" class="cf_edit_plus dashicons dashicons-plus-alt"></a></div>';
        return $out;

    }

    public static function register() {
        $types = array(
            'emailtobox' => array(__('Multiple Recipients', 'cforms2'), true),
            'selectbox' => array(__('Select Box', 'cforms2'), false),
            'multiselectbox' => array(__('Multi Select Box', 'cforms2'), false)
        );
        foreach ($types as $id => $label) {
            $t = new cforms2_fieldtype_selectbox($id, $label[0], $label[1]);
            $t->register_at_filter();
        }

    }

}
