<?php
/**
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
namespace Cforms2;

class FormSettings
{
    private static $forms = array();
    private $ind;
    private $pre;

    private function __construct($index_form, $index_prefix)
    {
        $this->ind = $index_form;
        $this->pre = $index_prefix;
    }

    public function name()
    {
        return Settings::instance()->get($this->ind, $this->pre . 'fname');
    }

    /**
     * Converts a natural number to the corresponding form ID.
     *
     * @param  int $no a natural number
     * @return string the cformsII form ID
     */
    private static function formId($no)
    {
        if ($no < 2) {
            return '';
        }
        return strval($no);
    }

    /**
     * Returns the settings object of the form with ID $id. If it does not exist returns null.
     *
     * @param  mixed $id a cformsII form ID
     * @return FormSettings
     */
    public static function form($id)
    {
        if (is_int($id)) {
            $id = FormSettings::formId($id);
        }
        if (isset(FormSettings::$forms[$id])) {
            return FormSettings::$forms[$id];
        } elseif (!empty(Settings::instance()->get("form{$id}"))) {
            FormSettings::$forms[$id] = new FormSettings("form{$id}", "cforms{$id}_");
            return FormSettings::$forms[$id];
        }
        return null;
    }

    /**
     * @return array associative array of FormSettings instances
     */
    public static function forms()
    {
        $no = 1;
        while (FormSettings::form($no) != null) {
            $no += 1;
        }
        return FormSettings::$forms;
    }
}
