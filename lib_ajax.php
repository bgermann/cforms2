<?php
/*
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

###
###  ajax submission of form
###

require_once(plugin_dir_path(__FILE__) . 'lib_email.php');
require_once(plugin_dir_path(__FILE__) . 'lib_aux.php');

function cforms2_json_die($no, $result, $html, $hide = false, $redirection = null) {
	header ('Content-Type: application/json');
	echo json_encode(array(
		'no' => $no,
		'result' => $result,
		'html' => $html,
		'hide' => $hide,
		'redirection' => $redirection
	));
	die();
}
add_action( 'wp_ajax_submitcform', 'cforms2_submitcform' );
add_action( 'wp_ajax_nopriv_submitcform', 'cforms2_submitcform' );

###
###  submit form
###
function cforms2_submitcform() {
	global $cformsSettings, $usermessage_class, $usermessage_text, $cf_redirect;
	check_admin_referer( 'submitcform' );
	$cformsSettings = get_option('cforms_settings');
	$all_valid = true;
	$no = $_POST['cforms_id'];
	$_POST['sendbutton'.$no] = true;
	require_once (plugin_dir_path(__FILE__) . 'lib_validate.php');
	$hide = $all_valid && ($cformsSettings['form'.$no]['cforms'.$no.'_hide'] || cforms2_get_submission_left($no)==0);
	cforms2_json_die($no, $usermessage_class, $usermessage_text, $hide, $cf_redirect);
}
