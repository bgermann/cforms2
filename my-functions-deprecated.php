<?php
/*
 * Copyright (c) 2016 Bastian Germann
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
 * This is a compatibility layer for the transition from the old
 * my-functions API to native WordPress actions/filters.
 * 
 * @deprecated since version 14.11
 */
class cforms2_deprecated_api {

	public static function my_cforms_action($cformsdata) {
		if( function_exists('my_cforms_action') ) {
			trigger_error('You should switch from using my_cforms_action function to cforms2_after_processing_action WordPress action.', E_USER_WARNING);
			my_cforms_action($cformsdata);
		}
	}

}

add_action('cforms2_after_processing_action', 'cforms2_deprecated_api::my_cforms_action');

if (!function_exists('cf_extra_comment_data')) {
	/**
	 * @deprecated since version 14.11.3
	 */
	function cf_extra_comment_data( $id ) {
		trigger_error('cf_extra_comment_data is useless', E_USER_WARNING);
	}
}

if (!function_exists('is_tellafriend')) {
	/**
	 * @deprecated since version 14.12
	 */
	function is_tellafriend($pid) {
		trigger_error('is_tellafriend is useless', E_USER_WARNING);
	}
}
