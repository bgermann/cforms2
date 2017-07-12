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
 * main function
 */
function cforms2($no = '', $customfields = array()) {

    global $cformsSettings;

    // remember old value to reset session when in new multi-part form
    $oldno = ($no == '1') ? '' : $no;

    cforms2_dbg("Original form on page #$oldno");

    // multi-part form: overwrite $no
    $isMPform = $cformsSettings['form' . $oldno]['cforms' . $oldno . '_mp']['mp_form'];

    cforms2_dbg("Multi-part form = $isMPform");

    if ($isMPform && is_array($_SESSION['cforms']) && $_SESSION['cforms']['current'] > 0) {
        $no = $_SESSION['cforms']['current'];
    }

    // Safety, in case someone uses '1' for the default form
    $no = ($no == '1') ? '' : $no;

    cforms2_dbg("Switch to form #$no");

    $moveBack = false;
    // multi-part form: reset button
    if (isset($_POST['resetbutton' . $no]) && is_array($_SESSION['cforms'])) {
        $no = $oldno;
        unset($_SESSION['cforms']);
        $_SESSION['cforms']['current'] = 0;
        $_SESSION['cforms']['first'] = $oldno;
        $_SESSION['cforms']['pos'] = 1;
        unset($_POST);

        cforms2_dbg("Reset-Button pressed");
    }
    // multi-part form: back button
    elseif (isset($_POST['backbutton' . $no]) && isset($_SESSION['cforms']) && ($_SESSION['cforms']['pos'] - 1) >= 0) {
        $no = $_SESSION['cforms']['list'][($_SESSION['cforms']['pos'] --) - 1];
        $_SESSION['cforms']['current'] = $no;
        $moveBack = true;

        cforms2_dbg("Back-Button pressed");
    }
    // multi-part form init: must be multi-part, first and not submitted!
    elseif ($isMPform && (!is_array($_SESSION['cforms']) || $_SESSION['cforms']['first'] !== $oldno) && $cformsSettings['form' . $oldno]['cforms' . $oldno . '_mp']['mp_first']) {
        $no = $oldno;
        unset($_SESSION['cforms']);
        $_SESSION['cforms']['current'] = 0;
        $_SESSION['cforms']['first'] = $no;
        $_SESSION['cforms']['pos'] = 1;

        cforms2_dbg("Session found, you're on the first form and session is reset!");
    }


    // custom fields support
    if (empty($customfields)) {
        $customfields = array();
        $custom = false;
        $field_count = $cformsSettings['form' . $no]['cforms' . $no . '_count_fields'];
    } else {
        $no = substr($no, 0, -1);
        $customfields = cforms2_build_fstat($customfields);
        $field_count = count($customfields);
        $custom = true;
    }


    $content = '';

    $track = array();

    $custom_error = '';
    $usermessage_class = '';
    $usermessage_text = "";

    // TODO integrate this check better
    $server_upload_size_error = false;
    $displayMaxSize = ini_get('post_max_size');
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) &&
            empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
        $server_upload_size_error = true;
        $msgSize = $_SERVER['CONTENT_LENGTH'] / 1048576;
        echo ("<pre>Maximum size allowed:" . $displayMaxSize . "; size of your message:" . number_format((float) $msgSize, 2, '.', '') . "M</pre>");
    }

    // non-AJAX method
    $all_valid = true;
    if (isset($_POST['sendbutton' . $no]) || $server_upload_size_error) {
        $validation_result = cforms2_validate($no, $isMPform, $custom, $customfields);
        $all_valid = $validation_result['all_valid'];
        $usermessage_text = $validation_result['text'];
        $usermessage_class = $validation_result['class'];
        $track = $validation_result['track'];
        $validations = $validation_result['validations'];

        if ($all_valid && $cformsSettings['form' . $no]['cforms' . $no . '_redirect']) {
            $cf_redirect = $cformsSettings['form' . $no]['cforms' . $no . '_redirect_page'];
            if (!empty($cf_redirect)) { // TODO rework to do this via HTTP?
                echo '<script type="text/javascript">'
                . 'location.href = "' . $cf_redirect . '"</script>';
            }
        }
    }

    // paint form
    $success = false;

    $umc = (!empty($usermessage_class) && $no > 1) ? ' ' . $usermessage_class . $no : '';

    cforms2_dbg("User info for form #$no");

    // where to show message
    if (substr($cformsSettings['form' . $no]['cforms' . $no . '_showpos'], 0, 1) == 'y') {
        $content .= '<div id="usermessage' . $no . 'a" class="cf_info' . $usermessage_class . $umc . ' ">' . $usermessage_text . '</div>';
        $actiontarget = 'a';
    } elseif (substr($cformsSettings['form' . $no]['cforms' . $no . '_showpos'], 1, 1) == 'y')
        $actiontarget = 'b';


    // multi-part form: overwrite $no, move on to next form
    $oldcurrent = $no;
    if ($all_valid && isset($_POST['sendbutton' . $no])) {

        $isMPformNext = false; // default

        cforms2_dbg("Form is all valid & sendbutton pressed.");

        if ($isMPform && isset($_SESSION['cforms']) && $_SESSION['cforms']['current'] > 0 && $cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_next'] != -1) {

            $isMPformNext = true;
            $no = cforms2_check_form_name($cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_next']);

            cforms2_dbg("Session active and now moving on to form #$no");

            $oldcurrent = $_SESSION['cforms']['current'];
            $_SESSION['cforms']['current'] = ($no == '') ? 1 : $no;

            $field_count = $cformsSettings['form' . $no]['cforms' . $no . '_count_fields'];
        } elseif ($isMPform && $cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_next'] == -1) {

            cforms2_dbg("Session was active but is being reset now");

            $oldcurrent = $no;

            $no = $_SESSION['cforms']['first'];
            unset($_SESSION['cforms']);

            $_SESSION['cforms']['current'] = 0;
            $_SESSION['cforms']['first'] = $no;
            $_SESSION['cforms']['pos'] = 1;

            $field_count = $cformsSettings['form' . $no]['cforms' . $no . '_count_fields'];
        }
    }


    cforms2_dbg("All good, currently on form #$no");
    cforms2_dbg(print_r($track, 1));

    // redirect == 2 : hide form? || or if max entries reached!
    if ($all_valid && (
            ( $cformsSettings['form' . $no]['cforms' . $no . '_hide'] && isset($_POST['sendbutton' . $no]) ) ||
            ( $cformsSettings['form' . $oldcurrent]['cforms' . $oldcurrent . '_hide'] && isset($_POST['sendbutton' . $oldcurrent]) )
            )
    )
        return $content;
    elseif (!cforms2_check_time($no)) {

        if ($validation_result['limit_reached'])
            return stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_limittxt']);
        else
            return $content . stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_limittxt']);
    }



    // alternative form action
    $alt_action = false;
    if ($cformsSettings['form' . $no]['cforms' . $no . '_action'] == '1') {
        $action = $cformsSettings['form' . $no]['cforms' . $no . '_action_page'];
        $alt_action = true;
    } else
        $action = cforms2_get_current_page() . '#usermessage' . $no . $actiontarget;


    $enctype = $cformsSettings['form' . $no]['cforms' . $no . '_formaction'] ? 'enctype="application/x-www-form-urlencoded"' : 'enctype="multipart/form-data"';

    // session item counter (for default values)
    $sItem = 1;

    $formcontent = '';

    // start with no fieldset
    $fieldsetopen = false;

    $upload = false;
    $fscount = 1;
    $ol = false;

    $inpFieldArr = array();
    for ($i = 1; $i <= $field_count; $i++) {

        if ($custom)
            $field_stat = explode('$#$', $customfields[$i - 1]);
        else
            $field_stat = explode('$#$', $cformsSettings['form' . $no]['cforms' . $no . '_count_field_' . $i]);

        $field_name = $field_stat[0];
        $field_type = $field_stat[1];
        $field_required = $field_stat[2];
        $field_emailcheck = $field_stat[3];
        $field_clear = $field_stat[4];
        $field_disabled = $field_stat[5];
        $field_readonly = $field_stat[6];


        // check for html5 attributes
        $obj = explode('|html5:', $field_name, 2);
        $obj[] = "";
        $html5 = empty($obj[1]) ? '' : preg_split('/\x{00A4}/u', $obj[1], -1);

        cforms2_dbg("\t\t html5 check, settings = " . print_r($html5, 1));

        // check for custom error message and split field_name
        $obj = explode('|err:', $obj[0], 2);
        $obj[] = "";
        $fielderr = $obj[1];

        cforms2_dbg("\t adding $field_type field: $field_name");

        if (!empty($fielderr)) {
            switch ($field_type) {
                case 'upload':
                    $custom_error .= 'cf_uploadfile' . $no . '-' . $i . '$#$' . $fielderr . '|';
                    break;

                default:
                    preg_match('/^([^#\|]*).*/', $field_name, $input_name);
                    if (strpos($input_name[1], '[id:') > 0)
                        preg_match('/\[id:(.+)\]/', $input_name[1], $input_name);

                    $custom_error .= ($cformsSettings['form' . $no]['cforms' . $no . '_customnames'] == '1') ? cforms2_sanitize_ids($input_name[1]) : 'cf' . $no . '_field_' . $i;
                    $custom_error .= '$#$' . $fielderr . '|';
            }
        }


        // check for title attribute
        $obj = explode('|title:', $obj[0], 2);
        $obj[] = "";
        $fieldTitle = empty($obj[1]) ? '' : str_replace('"', '&quot;', stripslashes($obj[1]));

        cforms2_dbg("\t\t title check, obj[0] = " . $obj[0]);


        // special treatment for selectboxes
        if (in_array($field_type, array('multiselectbox', 'selectbox', 'radiobuttons', 'checkbox', 'checkboxgroup', 'ccbox', 'emailtobox'))) {

            $chkboxClicked = array();
            if (in_array($field_type, array('checkbox', 'ccbox')) && strpos($obj[0], '|set:') > 1) {
                $chkboxClicked = explode('|set:', stripslashes($obj[0]));
                $obj[0] = $chkboxClicked[0];
            }
            $chkboxClicked[] = "";
            $chkboxClicked[] = "";

            cforms2_dbg("\t\t found checkbox:, obj[0] = " . $obj[0]);

            $options = explode('#', stripslashes($obj[0]));

            if (in_array($field_type, array('checkbox', 'ccbox')))
                $field_name = ( $options[0] == '' ) ? $options[1] : $options[0];
            else
                $field_name = $options[0];

            cforms2_dbg("\t\t left from '#' (=field_name) = " . $options[0] . ", right from '#': " . $options[1] . "  -> field_name= $field_name");
        }


        // check if fieldset is open
        if (!$fieldsetopen && !$ol && $field_type !== 'fieldsetstart') {
            $formcontent .= '<ol class="cf-ol">';
            $ol = true;
        }


        $defaultvalue = '';
        // setting the default value and regexpression if it exists
        if (!in_array($field_type, array('fieldsetstart', 'fieldsetend', 'radiobuttons', 'checkbox', 'checkboxgroup', 'ccbox', 'emailtobox', 'multiselectbox', 'selectbox'))) {

            // check if default value and regexpression are set
            $obj = explode('|', $obj[0], 3);
            $obj[] = "";
            $obj[] = "";

            if (!empty($obj[2]))
                $reg_exp = str_replace('"', '&quot;', stripslashes($obj[2]));
            else
                $reg_exp = '';
            if (!empty($obj[1]))
                $defaultvalue = str_replace(array('"', '\n'), array('&quot;', "\r"), cforms2_check_default_vars(stripslashes(($obj[1])), $no));

            $field_name = $obj[0];
        }


        // label IDs
        $labelIDx = '';
        $labelID = ' id="label-' . $no . '-' . $i . '"';

        // <li> IDs
        $liID = ' id="li-' . $no . '-' . $i . '"';

        // input field names and label
        $isFieldArray = false;
        if ($cformsSettings['form' . $no]['cforms' . $no . '_customnames'] == '1') {

            if (strpos($field_name, '[id:') !== false) {
                $isFieldArray = strpos($field_name, '[]');
                $idPartA = strpos($field_name, '[id:');
                $idPartB = strrpos($field_name, ']', $idPartA);

                if ($isFieldArray) {

                    $input_id = $input_name = cforms2_sanitize_ids(substr($field_name, $idPartA + 4, ($idPartB - $idPartA) - 4));

                    if (!$inpFieldArr[$input_id] || $inpFieldArr[$input_id] == '') {
                        $inpFieldArr[$input_id] = 1;
                    }

                    $input_id .= $inpFieldArr[$input_id] ++;
                    $input_name .= '[]';
                } else
                    $input_id = $input_name = cforms2_sanitize_ids(substr($field_name, $idPartA + 4, ($idPartB - $idPartA) - 4));

                $field_name = substr_replace($field_name, '', $idPartA, ($idPartB - $idPartA) + 1);

                cforms2_dbg("\t\t parsing custom ID/NAME...new field_name = $field_name, ID=$input_id");
            } else
                $input_id = $input_name = cforms2_sanitize_ids(stripslashes($field_name));
        } else
            $input_id = $input_name = 'cf' . $no . '_field_' . $i;


        $field_class = '';
        $field_value = '';

        $captchas = cforms2_get_pluggable_captchas();
        if (array_key_exists($field_type, $captchas) && is_user_logged_in() && !$captchas[$field_type]->check_authn_users())
            continue;

        switch ($field_type) {
            case 'upload':
                $input_id = $input_name = 'cf_uploadfile' . $no . '-' . $i;
                $field_class = 'upload';
                break;
            case "datepicker":
            case "textfield":
            case "pwfield":
                $field_class = 'single';
                break;
            case "hidden":
                $field_class = 'hidden';
                break;
            case 'textarea':
                $field_class = 'area';
                break;
            default:
        }



        // additional field classes
        if ($field_disabled)
            $field_class .= ' disabled';
        if ($field_readonly)
            $field_class .= ' readonly';
        if ($field_emailcheck)
            $field_class .= ' fldemail';
        if ($field_required)
            $field_class .= ' fldrequired';


        // error?
        $liERR = $insertErr = '';


        // only for multi-part forms
        if ($moveBack || $isMPform) {  // $isMPformNext
            $field_value = htmlspecialchars(stripslashes($_SESSION['cforms']['cf_form' . $no][$_SESSION['cforms']['cf_form' . $no]['$$$' . ($sItem++)]]));
            cforms2_dbg('retrieving session values to pre-fill...' . $field_value);
        }

        if (!$all_valid) {

            if (!$server_upload_size_error && $validations[$i] != 1) {
                $field_class .= ' cf_error';
                $liERR = 'cf_li_err';
                if (substr($cformsSettings['form' . $no]['cforms' . $no . '_showpos'], 3, 1) == "y")
                    $insertErr = empty($fielderr) ? '' : '<ul class="cf_li_text_err"><li>' . stripslashes($fielderr) . '</li></ul>';
            }

            if (!isset($_POST[$input_name]))
                $_POST[$input_name] = ''; // the field could not be there at all
            if ($field_type == 'multiselectbox' || $field_type == 'checkboxgroup')
                $field_value = $_POST[$input_name]; // In this case it's an array. We will do the stripping later.
            else
                $field_value = htmlspecialchars(stripslashes($_POST[$input_name]));
        } elseif ((!isset($_POST['sendbutton' . $no]) && isset($_POST[$input_name])) || $cformsSettings['form' . $no]['cforms' . $no . '_dontclear']) {

            // only pre-populating fields
            if ($field_type == 'multiselectbox' || $field_type == 'checkboxgroup')
                $field_value = $_POST[$input_name]; // In this case it's an array. We will do the stripping later.
            else {
                $field_value = htmlspecialchars(stripslashes($_POST[$input_name]));
            }
        }


        // Print label only for non "textonly" fields! Skip some others also, and handle them below individually.
        $standard_field = !in_array($field_type, array('hidden', 'textonly', 'fieldsetstart', 'fieldsetend', 'ccbox', 'checkbox', 'checkboxgroup', 'radiobuttons'));
        if ($standard_field) {
            $formcontent .= '<li' . $liID . ' class="' . $liERR . '">' . $insertErr;
            if (!in_array($field_type, array_keys($captchas)))
                $formcontent .= '<label' . $labelID . ' for="' . $input_id . '"' . ($field_type == 'captcha' ? ' class="seccap"' : '') . '><span>' . stripslashes(($field_name)) . '</span></label>';
        }


        // if not reloaded (due to error) then use default values
        if ($field_value == '' && !empty($defaultvalue))
            $field_value = $defaultvalue;

        // field disabled or readonly, greyed out?
        $disabled = $field_disabled ? ' disabled="disabled"' : '';
        $readonly = $field_readonly ? ' readonly="readonly"' : '';


        // add input field
        $dp = '';
        $field = '';
        $val = '';
        if (array_key_exists($field_type, $captchas)) {
            $html = $captchas[$field_type]->get_request($input_id, 'secinput fldrequired ' . $field_class, $fieldTitle);
            $field = $html;
        } else
            switch ($field_type) {

                case "upload":
                    // set upload flag for ajax suppression!
                    $upload = true;
                    $field = '<input' . $readonly . $disabled . ' type="file" name="cf_uploadfile' . $no . '[]" id="cf_uploadfile' . $no . '-' . $i . '" class="cf_upload ' . $field_class . '" title="' . $fieldTitle . '"/>';
                    break;

                case "textonly":
                    $field .= '<li' . $liID . ' class="textonly' . (empty($defaultvalue) ? '' : ' ' . $defaultvalue) . '"' . (empty($reg_exp) ? '' : ' style="' . $reg_exp . '" ') . '>' . stripslashes(($field_name)) . '</li>';
                    break;

                case "fieldsetstart":
                    if ($fieldsetopen) {
                        $field = '</ol></fieldset>';
                        $fieldsetopen = false;
                        $ol = false;
                    }
                    if (!$fieldsetopen) {
                        $fieldsethide = explode('|set:', $field_name, 2);
                        $fieldsethide [] = '';
                        $fieldsethide[1] = strcasecmp($fieldsethide[1], 'true') === 0 ? 'display:none;' : ''; // TODO add condition for at least one previous fields invalid

                        if ($ol)
                            $field = '</ol>';

                        $field .= '<fieldset class="cf-fs' . $fscount++ . '" style="' . $fieldsethide[1] . '">'
                                . '<legend>' . stripslashes($fieldsethide[0]) . '</legend>'
                                . '<ol class="cf-ol">';
                        $fieldsetopen = true;
                        $ol = true;
                    }
                    break;

                case "fieldsetend":
                    if ($fieldsetopen) {
                        $field = '</ol></fieldset>';
                        $fieldsetopen = false;
                        $ol = false;
                    } else
                        $field = '';
                    break;

                case "datepicker":
                case "textfield":
                case "pwfield":
                case "html5color":
                case "html5date":
                case "html5datetime":
                case "html5datetime-local":
                case "html5email":
                case "html5month":
                case "html5number":
                case "html5range":
                case "html5search":
                case "html5tel":
                case "html5time":
                case "html5url":
                case "html5week":

                    $field_value = cforms2_check_post_vars($field_value);

                    $h5 = '';
                    if (strpos($field_type, 'tml5') !== false) {
                        $type = substr($field_type, 5);
                        if (is_array($html5)) {
                            $h5_0 = ( $html5[0] == '1' ) ? ' autocomplete="on"' : '';
                            $h5_1 = ( $html5[1] == '1' ) ? ' autofocus ="autofocus"' : '';
                            $h5_2 = ( $html5[2] != '' ) ? ' min="' . $html5[2] . '"' : '';
                            $h5_3 = ( $html5[3] != '' ) ? ' max="' . $html5[3] . '"' : '';
                            $h5_4 = ( $html5[4] != '' ) ? ' pattern="' . $html5[4] . '"' : '';
                            $h5_5 = ( $html5[5] != '' ) ? ' step="' . $html5[5] . '"' : '';
                            $h5_6 = ( $html5[6] != '' ) ? ' placeholder="' . $html5[6] . '"' : '';
                            $h5 = $h5_0 . $h5_1 . $h5_2 . $h5_3 . $h5_4 . $h5_5 . $h5_6;
                        }
                        $h5_7 = ( $field_required ) ? ' required="required"' : '';
                        $h5 .= $h5_7 . ' ';

                        cforms2_dbg('......html5 attributes: ' . $h5);
                    } else
                        $type = ($field_type == 'pwfield') ? 'password' : 'text';

                    $field_class = ($field_type == 'datepicker') ? $field_class . ' cf_date' : $field_class;

                    $onfocus = $field_clear ? ' onfocus="clearField(this)" onblur="setField(this)"' : '';

                    $field = '<input' . $h5 . $readonly . $disabled . ' type="' . $type . '" name="' . $input_name . '" id="' . $input_id . '" class="' . $field_class . '" value="' . $field_value . '"' . $onfocus . ' title="' . $fieldTitle . '"/>';
                    if (!empty($reg_exp))
                        $field .= '<input type="hidden" name="' . $input_name . '_regexp" id="' . $input_id . '_regexp" value="' . $reg_exp . '" title="' . $fieldTitle . '"/>';

                    $field .= $dp;
                    break;

                case "hidden":

                    $field_value = cforms2_check_post_vars($field_value);
                    $field_value = cforms2_check_default_vars($field_value, $no);

                    if (preg_match('/^<([a-zA-Z0-9]+)>$/', $field_value, $getkey))
                        $field_value = $_GET[$getkey[1]];

                    $field .= '<li class="cf_hidden"><input type="hidden" class="cfhidden" name="' . $input_name . '" id="' . $input_id . '" value="' . $field_value . '" title="' . $fieldTitle . '"/></li>';
                    break;

                case "textarea":
                    $onfocus = $field_clear ? ' onfocus="clearField(this)" onblur="setField(this)"' : '';

                    $field = '<textarea' . $readonly . $disabled . ' cols="30" rows="8" name="' . $input_name . '" id="' . $input_id . '" class="' . $field_class . '"' . $onfocus . ' title="' . $fieldTitle . '">' . $field_value . '</textarea>';
                    if (!empty($reg_exp))
                        $field .= '<input type="hidden" name="' . $input_name . '_regexp" id="' . $input_id . '_regexp" value="' . $reg_exp . '" title="' . $fieldTitle . '"/>';
                    break;

                case "ccbox":
                case "checkbox":
                    if (!$all_valid || ($all_valid && $cformsSettings['form' . $no]['cforms' . $no . '_dontclear']) || ($isMPform && is_array($_SESSION['cforms']['cf_form' . $no]))) // exclude MP! if first time on the form = array = null
                        $preChecked = ( $field_value && !empty($field_value) ) ? ' checked="checked"' : '';  // for MPs
                    else
                        $preChecked = ( strpos($chkboxClicked[1], 'true') !== false ) ? ' checked="checked"' : '';  // $all_valid = user choice prevails

                    $err = '';
                    if (!$server_upload_size_error && !$all_valid && $validations[$i] != 1)
                        $err = ' cf_errortxt';

                    $opt = explode('|', $field_name, 2);
                    $opt[] = "";
                    if (!empty($options[1])) {
                        $before = '<li' . $liID . ' class="' . $liERR . '">' . $insertErr;
                        $after = '<label' . $labelID . ' for="' . $input_id . '" class="cf-after' . $err . '"><span>' . $options[1] . '</span></label></li>';
                        $ba = 'a';
                    } else {
                        $before = '<li' . $liID . ' class="' . $liERR . '">' . $insertErr . '<label' . $labelID . ' for="' . $input_name . '" class="cf-before' . $err . '"><span>' . $opt[0] . '</span></label>';
                        $after = '</li>';
                        $ba = 'b';
                    }

                    if ($val == '')
                        $val = empty($opt[1]) ? '' : ' value="' . $opt[1] . '"';

                    $field = $before . '<input' . $readonly . $disabled . ' type="checkbox" name="' . $input_name . '" id="' . $input_id . '" class="cf-box-' . $ba . $field_class . '"' . $val . ' title="' . $fieldTitle . '"' . $preChecked . '/>' . $after;

                    break;


                case "checkboxgroup":
                    $liID_b = empty($liID) ? '' : substr($liID, 0, -1) . 'items"';
                    array_shift($options);
                    $field .= '<li' . $liID . ' class="cf-box-title">' . (($field_name)) . '</li>' .
                            '<li' . $liID_b . ' class="cf-box-group">';
                    $id = 1;
                    $j = 0;

                    // MP session support
                    if (( $moveBack || $isMPform ) && !is_array($field_value))
                        $field_value = explode(',', $field_value);

                    foreach ($options as $option) {

                        // supporting names & values
                        $boxPreset = explode('|set:', $option);
                        $opt = explode('|', $boxPreset[0], 2);
                        if ($opt[1] == '')
                            $opt[1] = $opt[0];

                        $checked = '';
                        if ($moveBack || $isMPform) {
                            if (in_array($opt[1], array_values($field_value)))
                                $checked = 'checked="checked"';
                        } elseif (is_array($field_value)) {
                            if ($opt[1] == htmlspecialchars(stripslashes(strip_tags($field_value[$j])))) {
                                $checked = 'checked="checked"';
                                $j++;
                            }
                        } else {
                            if (strpos($boxPreset[1], 'true') !== false)
                                $checked = ' checked="checked"';
                        }

                        $brackets = $isFieldArray ? '' : '[]';

                        if (!empty($labelID))
                            $labelIDx = substr($labelID, 0, -1) . $id . '"';

                        if ($opt[0] == '')
                            $field .= '<br />';
                        else
                            $field .= '<input' . $readonly . $disabled . ' type="checkbox" id="' . $input_id . '-' . $id . '" name="' . $input_name . $brackets . '" value="' . $opt[1] . '" ' . $checked . ' class="cf-box-b" title="' . $fieldTitle . '"/>' .
                                    '<label' . $labelIDx . ' for="' . $input_id . '-' . ($id++) . '" class="cf-group-after"><span>' . $opt[0] . "</span></label>";
                    }
                    $field .= '</li>';
                    break;


                case "multiselectbox":
                    $field .= '<select' . $readonly . $disabled . ' multiple="multiple" name="' . $input_name . '[]" id="' . $input_id . '" class="cfselectmulti ' . $field_class . '" title="' . $fieldTitle . '">';
                    array_shift($options);
                    $j = 0;

                    // MP session support
                    if ($moveBack || $isMPform)
                        $field_value = explode(',', $field_value);

                    foreach ($options as $option) {

                        // supporting names and values
                        $optPreset = explode('|set:', $option);
                        $opt = explode('|', $optPreset[0], 2);
                        if (empty($opt[1]))
                            $opt[1] = $opt[0];

                        $checked = '';
                        if ($moveBack || $isMPform) {
                            if (in_array($opt[1], array_values($field_value)))
                                $checked = 'selected="selected"';
                        } elseif (is_array($field_value)) {
                            if ($opt[1] == stripslashes(htmlspecialchars(strip_tags($field_value[$j])))) {
                                $checked = ' selected="selected"';
                                $j++;
                            }
                        } elseif (isset($optPreset[1]) && strpos($optPreset[1], 'true') !== false)
                            $checked = ' selected="selected"';

                        $field .= '<option value="' . str_replace('"', '&quot;', $opt[1]) . '"' . $checked . '>' . $opt[0] . '</option>';
                    }
                    $field .= '</select>';
                    break;

                case "emailtobox":
                case "selectbox":
                    $field = '<select' . $readonly . $disabled . ' name="' . $input_name . '" id="' . $input_id . '" class="cformselect' . $field_class . '" title="' . $fieldTitle . '">';
                    array_shift($options);
                    $jj = $j = 0;

                    foreach ($options as $option) {

                        // supporting names and values
                        $optPreset = explode('|set:', $option);
                        $optPreset[] = "";
                        $opt = explode('|', $optPreset[0], 2);
                        $opt[] = "";
                        if ($opt[1] == '')
                            $opt[1] = $opt[0];

                        // email-to-box valid entry?
                        if ($field_type === 'emailtobox' && $opt[1] !== '-')
                            $jj = $j;
                        else
                            $jj = '-';
                        $j++;

                        $checked = '';

                        if ($field_value == '' || $field_value == '-') {
                            if (strpos($optPreset[1], 'true') !== false)
                                $checked = ' selected="selected"';
                        } elseif ($opt[1] == $field_value || $jj == $field_value) {
                            $checked = ' selected="selected"';
                        }

                        $field .= '<option value="' . (($field_type == 'emailtobox') ? $jj : $opt[1]) . '"' . $checked . '>' . $opt[0] . '</option>';
                    }
                    $field .= '</select>';
                    break;

                case "radiobuttons":
                    $liID_b = empty($liID) ? '' : substr($liID, 0, -1) . 'items"'; // only if label IDs active

                    array_shift($options);
                    $field .= '<li' . $liID . ' class="' . $liERR . ' cf-box-title">' . $insertErr . (($field_name)) . '</li>' .
                            '<li' . $liID_b . ' class="cf-box-group">';

                    $id = 1;
                    foreach ($options as $option) {
                        $checked = '';

                        // supporting names and values
                        $radioPreset = explode('|set:', $option);
                        $opt = explode('|', $radioPreset[0], 2);
                        $opt[] = "";
                        if ($opt[1] == '')
                            $opt[1] = $opt[0];

                        if ($field_value == '') {

                            if (isset($radioPreset[1]) && strpos($radioPreset[1], 'true') !== false)
                                $checked = ' checked="checked"';
                        } elseif ($opt[1] == $field_value)
                            $checked = ' checked="checked"';

                        if (!empty($labelID))
                            $labelIDx = substr($labelID, 0, -1) . $id . '"';

                        if ($opt[0] == '')
                            $field .= '<br />';
                        else
                            $field .= '<input' . $readonly . $disabled . ' type="radio" id="' . $input_id . '-' . $id . '" name="' . $input_name . '" value="' . $opt[1] . '"' . $checked . ' class="cf-box-b' . ($field_required ? ' fldrequired' : '') . '" title="' . $fieldTitle . '"/>' .
                                    '<label' . $labelIDx . ' for="' . $input_id . '-' . ($id++) . '" class="cf-after"><span>' . $opt[0] . "</span></label>";
                    }
                    $field .= '</li>';
                    break;
            }

        cforms2_dbg("Form setup: $field_type, val=$field_value, default=$defaultvalue");

        // add new field
        $formcontent .= $field;

        // adding "required" text if needed
        if ($field_emailcheck == 1)
            $formcontent .= '<span class="emailreqtxt">' . stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_emailrequired']) . '</span>';
        elseif ($field_required == 1 && !in_array($field_type, array('ccbox', 'checkbox', 'radiobuttons')))
            $formcontent .= '<span class="reqtxt">' . stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_required']) . '</span>';

        // close <li> item
        if ($standard_field)
            $formcontent .= '</li>';
    }

    // close any open tags
    if ($ol)
        $formcontent .= '</ol>';
    if ($fieldsetopen)
        $formcontent .= '</fieldset>';


    $direct_submission = '';
    if ($cformsSettings['form' . $no]['cforms' . $no . '_ajax'] == '0' || $upload || $custom || $alt_action)
        $direct_submission = 'cformsdirect ';


    $formcontent .= '<fieldset class="cf_hidden"><legend>&nbsp;</legend>';

    // custom error
    $custom_error = substr($cformsSettings['form' . $no]['cforms' . $no . '_showpos'], 3, 1) . $custom_error;


    // Extra Fields
    if (substr($cformsSettings['form' . $oldno]['cforms' . $oldno . '_tellafriend'], 0, 1) === '3') {
        $formcontent .= '<input type="hidden" name="comment_post_ID' . $no . '" id="comment_post_ID' . $no . '" value="' . get_the_ID() . '"/>' .
                '<input type="hidden" name="cforms_pl' . $no . '" id="cforms_pl' . $no . '" value="' . get_permalink() . '"/>';
    }


    $formcontent .= '<input type="hidden" name="cf_working' . $no . '" id="cf_working' . $no . '" value="<span>' . rawurlencode($cformsSettings['form' . $no]['cforms' . $no . '_working']) . '</span>"/>' .
            '<input type="hidden" name="cf_failure' . $no . '" id="cf_failure' . $no . '" value="<span>' . rawurlencode($cformsSettings['form' . $no]['cforms' . $no . '_failure']) . '</span>"/>' .
            '<input type="hidden" name="cf_customerr' . $no . '" id="cf_customerr' . $no . '" value="' . rawurlencode($custom_error) . '"/>';

    $formcontent .= '</fieldset>';

    // start with form tag
    $content .= '<form ' . $enctype . ' action="' . $action . '" method="post" class="cform ' . $direct_submission . sanitize_title_with_dashes($cformsSettings['form' . $no]['cforms' . $no . '_fname']) . ' ' . ( $cformsSettings['form' . $no]['cforms' . $no . '_dontclear'] ? ' cfnoreset' : '' ) . '" id="cforms' . $no . 'form">';

    // multi-part form: reset
    $reset = '';
    if ($cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_form'] && $cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_reset'])
        $reset = '<input tabindex="999" type="submit" name="resetbutton' . $no . '" id="resetbutton' . $no . '" class="resetbutton" value="' . $cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_resettext'] . '" onclick="return confirm(\'' . __('Note: This will reset all your input!', 'cforms2') . '\')">';


    // multi-part form: back
    $back = '';
    if ($cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_form'] && $cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_back'] && !$cformsSettings['form' . $oldno]['cforms' . $no . '_mp']['mp_first'])
        $back = '<input type="submit" name="backbutton' . $no . '" id="backbutton' . $no . '" class="backbutton" value="' . $cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_backtext'] . '">';


    $content .= $formcontent . '<p class="cf-sb">' . $reset . $back . '<input type="submit" name="sendbutton' . $no . '" id="sendbutton' . $no . '" class="sendbutton" value="' . stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_submit_text'])) . '" /></p></form>';

    if (substr($cformsSettings['form' . $no]['cforms' . $no . '_showpos'], 1, 1) == 'y' && !($success && $cformsSettings['form' . $no]['cforms' . $no . '_hide']))
        $content .= '<div id="usermessage' . $no . 'b" class="cf_info ' . $usermessage_class . $umc . '" >' . $usermessage_text . '</div>';

    return $content;

}

/** build field_stat string from array (for custom forms) */
function cforms2_build_fstat($f) {
    $cfarray = array();
    for ($i = 0; $i < count($f['label']); $i++) {
        if ($f['type'][$i] == '')
            $f['type'][$i] = 'textfield';
        if ($f['isreq'][$i] == '')
            $f['isreq'][$i] = '0';
        if ($f['isemail'][$i] == '')
            $f['isemail'][$i] = '0';
        if ($f['isclear'][$i] == '')
            $f['isclear'][$i] = '0';
        if ($f['isdisabled'][$i] == '')
            $f['isdisabled'][$i] = '0';
        if ($f['isreadonly'][$i] == '')
            $f['isreadonly'][$i] = '0';
        $cfarray[$i] = $f['label'][$i] . '$#$' . $f['type'][$i] . '$#$' . $f['isreq'][$i] . '$#$' . $f['isemail'][$i] . '$#$' . $f['isclear'][$i] . '$#$' . $f['isdisabled'][$i] . '$#$' . $f['isreadonly'][$i];
    }
    return $cfarray;

}

/** check form names/id's */
function cforms2_check_form_name($no) {
    $cformsSettings = get_option('cforms_settings');
    if (is_numeric($no) || $no == '')
        return $no;

    $forms = $cformsSettings['global']['cforms_formcount'];

    for ($i = 0; $i < $forms; $i++) {
        $no2 = ($i == 0) ? '' : ($i + 1);
        if (stripslashes($cformsSettings['form' . $no2]['cforms' . $no2 . '_fname']) == $no)
            return $no2;
    }
    return '';

}

function cforms2_shortcode($atts, $content) {
    if (empty($atts))
        $callform = $content;
    else
        $callform = array_pop($atts);
    if (empty($callform))
        return '';
    return cforms2(cforms2_check_form_name($callform));

}
