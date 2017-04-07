<?php
/*
 * Copyright (c) 2016-2017 Bastian Germann
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

/**
 * The foundation for all field types.
 */
abstract class cforms2_fieldtype {

    /**
     * @return string A unique identifier for this field type.
     */
    abstract public function get_id();

    /**
     * @return string The human readable name for this field type that appears in the GUI.
     */
    abstract public function get_name();

    /**
     * @return boolean true if this field type is special.
     * Special fields are not automatically rendered as an option for fields.
     */
    public function is_special() {
        return false;

    }

    /**
     * @return the HTML required for the settings modal dialog
     */
    public function render_settings() {
        $out = '<form method="post">';
        $inputs = $this->get_text_inputs();
        foreach ($inputs as $id => $label) {
            $out .= sprintf('<label for="%1$s">%2$s</label><input type="text" id="%1$s" name="%1$s" class="cf_text" />', $id, $label);
        }
        $out .= $this->render_additional_settings();
        $out .= '</form>';
        return $out;

    }

    /**
     * @return the HTML required for the field type dropdown selection menu
     */
    public function render_form_option($selected, $disabled) {
        $out = '<option';
        if ($selected)
            $out .= ' selected="selected"';
        elseif ($disabled)
            $out .= ' disabled="disabled" class="disabled"';
        $out .= ' value="' . $this->get_id() . '">';
        $out .= $this->get_name();
        $out .= '</option>';
        return $out;

    }

    /**
     * @return array the HTML text input elements that represent the field's settings.
     */
    protected function get_text_inputs() {
        return array('cf_edit_label' => __('Field label', 'cforms2'));

    }

    /**
     * @return string the HTML input elements that cannot be represented by get_text_inputs().
     */
    protected function render_additional_settings() {
        return '';

    }

    /**
     * Adds this instance with the id as a key to the array.
     * 
     * @param array $instances
     * @return array The original array with a pair added.
     */
    public final function add_instance(array $instances) {
        $instances[$this->get_id()] = $this;
        return $instances;

    }

    /**
     * Registers the field type to be used by cformsII.
     */
    protected function register_at_filter() {
        add_filter('cforms2_add_fieldtype', array($this, 'add_instance'));

    }

    /**
     * Registers the field type with all possible combinations of ID and name to be used by cformsII.
     */
    public static function register() {
        require_once(plugin_dir_path(__FILE__) . 'multiid.php');
        cforms2_fieldtype_multi_id::register();

        require_once(plugin_dir_path(__FILE__) . 'checkbox.php');
        cforms2_fieldtype_checkbox::register();

        require_once(plugin_dir_path(__FILE__) . 'checkboxgroup.php');
        cforms2_fieldtype_checkboxgroup::register();

        require_once(plugin_dir_path(__FILE__) . 'fieldsetstart.php');
        cforms2_fieldtype_fieldsetstart::register();

        require_once(plugin_dir_path(__FILE__) . 'html5.php');
        cforms2_fieldtype_html5::register();

        require_once(plugin_dir_path(__FILE__) . 'captcha.php');
        require_once(plugin_dir_path(__FILE__) . 'question_and_answer.php');
        cforms2_question_and_answer::register();

        require_once(plugin_dir_path(__FILE__) . 'selectbox.php');
        cforms2_fieldtype_selectbox::register();

        require_once(plugin_dir_path(__FILE__) . 'text.php');
        cforms2_fieldtype_text::register();

        require_once(plugin_dir_path(__FILE__) . 'textonly.php');
        cforms2_fieldtype_textonly::register();

    }

}
