<?php
/*
 * Copyright (c) 2006-2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
 * Copyright (c) 2014-2016 Bastian Germann
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
abstract class cforms2_captcha {

	/**
	 * @return string A unique identifier for this CAPTCHA type.
	 */
	abstract public function get_id();

	/**
	 * @return string The human readable name for this CAPTCHA type that appears in the GUI.
	 */
	abstract public function get_name();

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
	 * Renders the HTML required for the settings modal dialog.
	 */
	abstract public function render_settings();

	/**
	 * Adds this instance with the classname as a key to the array.
	 * 
	 * @param array $captchas
	 * @return array The original array with a pair added.
	 */
	public final function add_instance(array $captchas) {
		$captchas[$this->get_id()] = $this;
		return $captchas;
	}

	/**
	 * @return bool true, if all users have to resolve the CAPTCHA, including the authenticated users.
	 */
	public function check_authn_users() {
		return false;
	}

}

/**
 * Replaces the old Q&A feature.
 */
final class cforms2_question_and_answer extends cforms2_captcha {

	private $cforms_settings;

	public function __construct() {
		$this->cforms_settings = get_option('cforms_settings');
	}

	public function get_id() {
		return get_class($this);
	}

	public function get_name() {
		return __('Visitor verification (Q&amp;A)', 'cforms2');
	}

	public function check_authn_users() {
		return $this->cforms_settings['global']['cforms_captcha_def']['foqa'] == '1';
	}

	public function check_response($post) {
		$hint = $post[$this->get_id() . '/hint'];
		$answer = $post[$this->get_id()];
		$q = $this->question_and_answer(intval($hint));
		return strcasecmp($answer, $q[2]) === 0;
	}

	public function get_request($input_id, $input_classes, $input_title) {
        $q = $this->question_and_answer();
		$label = stripslashes(htmlspecialchars($q[1]));

		$req = '<label for="'.$input_id.'" class="secq"><span>' . stripslashes(($label)) . '</span></label>'
			 . '<input type="text" name="'.$this->get_id().'" id="'.$input_id.'" '
		     . 'class="'.$input_classes.'" title="'.$input_title.'"/>'
		     . '<input type="hidden" name="'.$this->get_id().'/hint" value="' . $q[0] . '"/>';
		return $req;
	}
	
	/**
	 * Returns the nth question & answer pair.
	 * 
	 * @param int $n The nth pair. If negative, $n is random.
	 * @return array array(n, qestion, answer)
	 */
	private function question_and_answer($n = -1) {
		$qall = explode( "\r\n", $this->cforms_settings['global']['cforms_sec_qa'] );
		if ($n < 0)
			$n = mt_rand( 0, count($qall)-1 );
		$q = explode( '=', $qall[$n]);
		array_unshift($q, $n);
		return $q;
	}

	public function render_settings() {
		require ('include/textfield.php');
	}

}

add_filter('cforms2_add_captcha', array(new cforms2_question_and_answer(), 'add_instance'));
