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

function cforms2_json_die($no, $result, $html, $hide = false, $redirection = null) {
    header('Content-Type: application/json');
    echo json_encode(array(
        'no' => $no,
        'result' => $result,
        'html' => $html,
        'hide' => $hide,
        'redirection' => $redirection
    ));
    die();

}

/**
 * submit form
 */
function cforms2_submitcform() {
    check_admin_referer('submitcform');

    // The form ID is either the empty string (default form) or a natural
    // number. Anything else has no matching settings and would otherwise
    // trigger PHP warnings/fatals in the settings lookups below, so reject
    // it early with a clean JSON error.
    $no = isset($_POST['cforms_id']) ? $_POST['cforms_id'] : '';
    if (!is_string($no) || ($no !== '' && !preg_match('/^\d+$/', $no)) || Cforms2\FormSettings::form($no) == null) {
        cforms2_json_die('', ' failure', __('The form you submitted is not valid.', 'cforms2'));
        return;
    }

    $_POST['sendbutton' . $no] = true;
    $validation_result = cforms2_validate($no);
    $hide = $validation_result['all_valid'] && Cforms2\FormSettings::form($no)->getHide();
    $cf_redirect = null;
    if (Cforms2\FormSettings::form($no)->getRedirect()) {
        $cf_redirect = Cforms2\FormSettings::form($no)->getRedirectPage();
    }
    cforms2_json_die($no, $validation_result['class'], $validation_result['text'], $hide, $cf_redirect);

}
