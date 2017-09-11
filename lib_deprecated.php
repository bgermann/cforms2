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

if (!function_exists('insert_custom_cform')) {

    /**
     * @deprecated since version 14.14
     */
    function insert_custom_cform($customfields = array(), $no = '') {
        trigger_error('You should switch from using insert_custom_cform function to insert_cform.', E_USER_DEPRECATED);
        insert_cform($no, $customfields);

    }

}

/**
 * If one of the deprecated functions of the old my-functions API is declared, the user is warned.
 */
function cforms2_warn_on_existing_my_functions() {

    if (function_exists('my_cforms_action')) {
        trigger_error('my_cforms_action is useless since cformsII version 14.14.', E_USER_DEPRECATED);
    }

    if (function_exists('my_cforms_filter')) {
        trigger_error('my_cforms_filter is useless since cformsII version 14.14.', E_USER_DEPRECATED);
    }

    if (function_exists('my_cforms_ajax_filter')) {
        trigger_error('my_cforms_ajax_filter is useless since cformsII version 14.14.', E_USER_DEPRECATED);
    }

    if (function_exists('my_cforms_logic')) {
        trigger_error('my_cforms_logic is useless since cformsII version 14.14', E_USER_DEPRECATED);
    }

}
