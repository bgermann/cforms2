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

/**
 * Replaces the old Q&A feature.
 */
final class cforms2_question_and_answer extends cforms2_captcha {

    protected function __construct() {
    }

    public function get_id() {
        return get_class($this);

    }

    public function get_name() {
        return __('Visitor verification (Q&amp;A)', 'cforms2');

    }

    public function check_authn_users() {
        $cformsSettings = get_option('cforms_settings');
        return $cformsSettings['global']['cforms_captcha_def']['foqa'] == '1';

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

        $req = '<label for="' . $input_id . '" class="secq"><span>' . stripslashes(($label)) . '</span></label>'
                . '<input type="text" name="' . $this->get_id() . '" id="' . $input_id . '" '
                . 'class="' . $input_classes . '" title="' . $input_title . '"/>'
                . '<input type="hidden" name="' . $this->get_id() . '/hint" value="' . $q[0] . '"/>';
        return $req;

    }

    /**
     * Returns the nth question & answer pair.
     * 
     * @param int $n The nth pair. If negative, $n is random.
     * @return array array(n, qestion, answer)
     */
    private function question_and_answer($n = -1) {
        $cformsSettings = get_option('cforms_settings');
        $qall = explode("\r\n", $cformsSettings['global']['cforms_sec_qa']);
        if ($n < 0)
            $n = mt_rand(0, count($qall) - 1);
        $q = explode('=', $qall[$n]);
        array_unshift($q, $n);
        return $q;

    }

    public static function register() {
        $t = new cforms2_question_and_answer();
        $t->register_at_filter();

    }

}
