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

class cforms2_fieldtype_text extends cforms2_fieldtype_multi_id {

	protected function __construct($id, $name) {
		parent::__construct($id, $name);
	}

	protected function get_text_inputs() {
		return array(
			'cf_edit_label' => __('Field label', 'cforms2'),
			'cf_edit_default' => __('Default value', 'cforms2'),
			'cf_edit_regexp' => sprintf(__('Regular expression for field validation (e.g. %s). See Help! for more examples.', 'cforms2'),'^[A-Za-z ]+$'),
			'cf_edit_title' => __('Input field title (displayed when mouse hovers over field)', 'cforms2'),
			'cf_edit_customerr' => __('Custom error message (make sure to enable custom, per field err messages!)', 'cforms2')
		);
	}

	public static function register() {
		$types = array(
			'textfield' => __('Single line of text', 'cforms2'),
			'upload' => __('File Upload Box', 'cforms2'),
			'datepicker' => __('Date Entry/Dialog', 'cforms2'),
			'textarea' => __('Multiple lines of text', 'cforms2'),
			'pwfield' => __('Password Field', 'cforms2'),
			'hidden' => __('Hidden Field', 'cforms2')
		);
		foreach ($types as $id => $label) {
			$t = new cforms2_fieldtype_text($id, $label);
			$t->register_at_filter();
		}
	}

}
