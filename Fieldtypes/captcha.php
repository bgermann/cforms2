<?php
/*
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

/**
 * The foundation for the pluggable CAPTCHA support.
 * Each implementation should be instantiated once and added to cformsII
 * by using the filter cforms2_add_captcha with the method add_instance.
 */
abstract class cforms2_captcha extends cforms2_fieldtype {

    /**
     * Returns an associative array consisting of
     * "html" => HTML including at least an input field with this class's name as name
     * "hint" => the information needed for check_response method to evaluate the response
     * 
     * @param string $input_classes The class names for the input field
     * @param string $input_title The title for the input field
     * @return string HTML representing the captcha
     */
    abstract public function get_request($input_id, $input_classes, $input_title);

    /**
     * Checks the answer given by the user for correctness.
     * 
     * @param string $post The possibly filtered HTTP POST data from submitting a form.
     * @return bool true, if the answer was correct.
     */
    abstract public function check_response($post);

    /**
     * @return bool true, if all users have to resolve the CAPTCHA, including the authenticated users.
     */
    public function check_authn_users() {
        return false;

    }

    public function is_special() {
        return true;

    }

    protected function register_at_filter() {
        add_filter('cforms2_add_fieldtype', array($this, 'add_instance'));
        add_filter('cforms2_add_captcha', array($this, 'add_instance'));

    }

}
