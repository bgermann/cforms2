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
class cforms2_dreprecated_api {

	public static function my_cforms_action($cformsdata) {
		if( function_exists('my_cforms_action') ) {
			my_cforms_action($cformsdata);
		}
	}

}

add_action('cforms2_after_processing_action', 'cforms2_dreprecated_api::my_cforms_action');
