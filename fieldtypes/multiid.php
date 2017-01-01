<?php
/*
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

class cforms2_fieldtype_multi_id extends cforms2_fieldtype {

	private $id;
	private $name;

	protected function __construct($id, $name) {
		$this->id = $id;
		$this->name = $name;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_name() {
		return $this->name;
	}
	
	public static function register() {
		$t = new cforms2_fieldtype_multi_id('fieldsetend', __('End Fieldset', 'cforms2'));
		$t->register_at_filter();
	}

}
