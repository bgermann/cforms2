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

function cforms2_add_file($fn, &$fdata, &$fpointer, $attachFlag = false) {
    if (file_exists($fn)) {
        $fdata[$fpointer]['name'] = $fn;
        $fdata[$fpointer]['doAttach'] = $attachFlag;
        $fpointer++;
    }
    return;

}

/** move uploaded files to local dir */
function cforms2_move_files($no, $inSession, &$file) {
    global $cformsSettings;

    $_SESSION['cforms']['upload'][$no]['doAttach'] = !($cformsSettings['form' . $no]['cforms' . $no . '_noattachments']);

    if (is_array($file) && isset($file['tmp_name'])) {
        foreach ($file['tmp_name'] as $i => $tmpfile) {

            // copy attachment to local server dir
            if (is_uploaded_file($tmpfile)) {

                $fileName = basename(str_replace(' ', '_', $file['name'][$i]));
                $destfile = get_temp_dir() . $fileName;

                move_uploaded_file($tmpfile, $destfile);
                $file['tmp_name'][$i] = $destfile;

                if ($inSession)
                    $_SESSION['cforms']['upload'][$no]['files'][] = $destfile;
            }
        }
    }

}

/**
 * write DB record
 */
function cforms2_write_tracking_record($no, $field_email, $track) {
    global $wpdb, $cformsSettings;

    if ($cformsSettings['global']['cforms_database'] == '1') {
        cforms2_dbg('WRITING TRACKING RECORD');

        // first process fields, perhaps need to bail out
        $additional_fields = array();
        $dosave = false;
        foreach ($track as $key => $value) {

            // clean up keys
            if (preg_match('/\$\$\$/', $key) || strpos($key, '[*') !== false)
                continue;

            if (strpos($key, 'cf_form') !== false && preg_match('/^cf_form\d*_(.+)/', $key, $r))
                $key = $r[1];

            if (strpos($key, '___') !== false && preg_match('/^(.+)___\d+/', $key, $r))
                $key = $r[1];


            $additional_fields[$key] = $value;
            $dosave = true;
        }
        if (!$dosave)
            return;

        $wpdb->insert($wpdb->prefix . 'cformssubmissions', array('form_id' => $no,
            'email' => $field_email,
            'ip' => cforms2_get_ip(),
            'sub_date' => current_time('Y-m-d H:i:s')
        ));

        $subID = $wpdb->get_row("select LAST_INSERT_ID() as number from {$wpdb->prefix}cformssubmissions;");
        $subID = ($subID->number == '') ? '1' : $subID->number;

        $sql = $wpdb->prepare("INSERT INTO {$wpdb->prefix}cformsdata (sub_id,field_name,field_val) VALUES (%s,'page',%s)", $subID, cforms2_get_current_page());
        foreach ($additional_fields as $key => $value) {
            $sql .= ',' . $wpdb->prepare('(%s,%s,%s)', $subID, $key, $value);
        }

        $wpdb->query($sql);
    }

}

function cforms2_is_email($string) {
    return preg_match("/^[_a-z0-9+-]+(\.[_a-z0-9+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,63})$/i", $string);

}

function cforms2_validate($no, $isMPform = false, $custom = false, $customfields = array()) {
    require_once(plugin_dir_path(__FILE__) . 'lib_email.php');
    global $cformsSettings;

    $validations = array();
    $all_valid = true;

    $inpFieldArr = array(); // for var[] type input fields
    $field_count = $cformsSettings['form' . $no]['cforms' . $no . '_count_fields'];
    if ($custom)
        $field_count = count($customfields);
    $filefield = 0;
    $err = 0;

    $pid = cforms2_get_the_id($no);

    cforms2_dbg("lib_validate.php: validating fields for form no. $no");
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) &&
            empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {

        $all_valid = false;
        $err = 3;
        $fileerr = $cformsSettings['global']['cforms_upload_err3'];
    }

    cforms2_dbg("FILES:" . print_r($_FILES, 1));
    $off = 0;
    $c_errflag = false;

    if ($all_valid)
        for ($i = 1; $i <= $field_count; $i++) {
            if (!$custom)
                $field_stat = explode('$#$', $cformsSettings['form' . $no]['cforms' . $no . '_count_field_' . ((int) $i + (int) $off)]);
            else
                $field_stat = explode('$#$', $customfields[((int) $i + (int) $off) - 1]);
            $field_stat[] = "";
            $field_stat[] = "";
            $field_stat[] = "";

            // filter non-input fields
            if ($field_stat[1] == 'fieldsetstart' || $field_stat[1] == 'fieldsetend' || $field_stat[1] == 'textonly') {
                // auto approved
                $validations[$i + $off] = 1;
                continue;
            }

            while ($field_stat[1] == 'fieldsetstart' || $field_stat[1] == 'fieldsetend' || $field_stat[1] == 'textonly') {
                $off++;

                if (!$custom)
                    $field_stat = explode('$#$', $cformsSettings['form' . $no]['cforms' . $no . '_count_field_' . ((int) $i + (int) $off)]);
                else
                    $field_stat = explode('$#$', $customfields[((int) $i + (int) $off) - 1]);
                $field_stat[] = "";
                $field_stat[] = "";
                $field_stat[] = "";

                if ($field_stat[1] == '') {
                    // all fields searched, break both while & for
                    break 2;
                }
            }


            // custom error set?
            $c_err = explode('|err:', $field_stat[0], 2);
            $c_err[] = "";
            $c_title = explode('|title:', $c_err[0], 2);

            $field_name = $c_title[0];
            $field_type = $field_stat[1];
            $field_required = $field_stat[2];
            $field_emailcheck = $field_stat[3];

            cforms2_dbg("\t ...validating field $field_name");

            $captchas = cforms2_get_pluggable_captchas();

            // captcha not for logged in users
            if (cforms2_check_pluggable_captchas_authn_users($field_stat[1]))
                continue;

            // input field names and label
            $custom_names = $cformsSettings['form' . $no]['cforms' . $no . '_customnames'] == '1';
            $isFieldArray = false;

            if ($custom_names) {

                // hardcoded for now
                $tmpName = $field_name;
                cforms2_dbg("\t\t ...custom names/id's...($tmpName)");

                if (strpos($tmpName, '[id:') !== false) {

                    $isFieldArray = strpos($tmpName, '[]');
                    preg_match('/^([^\[]*)\[id:([^\|\]]+(\[\])?)\]([^\|]*).*/', $tmpName, $input_name);
                    $field_name = $input_name[1] . $input_name[4];
                    $trackingID = cforms2_sanitize_ids($input_name[2]);

                    /*
                      First Name[id:firstname]yy||^[A-Za-z ]*$Array
                      (
                      [0] => First Name[id:firstname]yy||^[A-Za-z ]*$
                      [1] => First Name
                      [2] => firstname
                      [3] =>
                      [4] => yy
                      )
                     */
                    if (!isset($_POST[$trackingID]))
                        $_POST[$trackingID] = "";
                    if ($isFieldArray) {

                        if (!isset($inpFieldArr[$trackingID]) || !$inpFieldArr[$trackingID] || $inpFieldArr[$trackingID] == '')
                            $inpFieldArr[$trackingID] = 0;

                        $current_field = $_POST[$trackingID][$inpFieldArr[$trackingID] ++];
                    } else
                        $current_field = $_POST[$trackingID];

                    cforms2_dbg("\t\t\t ...currentField field_name = \"$field_name\", current_field = $current_field, request-id = $trackingID");
                } else {
                    if (strpos($tmpName, '#') !== false && strpos($tmpName, '#') == 0) {
                        // special case with checkboxes with right label only and no ID
                        preg_match('/^#([^\|]*).*/', $field_name, $input_name);
                    } else {
                        // just take front part
                        preg_match('/^([^#\|]*).*/', $field_name, $input_name);
                    }
                    $current_field = isset($_POST[cforms2_sanitize_ids($input_name[1])]) ? $_POST[cforms2_sanitize_ids($input_name[1])] : "";
                }
            } else
                $current_field = isset($_POST['cf' . $no . '_field_' . ((int) $i + (int) $off)]) ? $_POST['cf' . $no . '_field_' . ((int) $i + (int) $off)] : "";

            $current_field = is_array($current_field) ? $current_field : stripslashes($current_field);

            if ($field_emailcheck) {

                cforms2_dbg("\t\t ...found email field ($current_field) is_email = " . cforms2_is_email($current_field));

                $validations[$i + $off] = cforms2_is_email($current_field) || (!$field_required && $current_field == '');

                if (!$validations[$i + $off] && $err == 0)
                    $err = 1;
            } elseif (array_key_exists($field_type, $captchas)) {
                // pluggable captcha
                $validations[$i + $off] = 1;
                if (!$captchas[$field_type]->check_response($_POST)) {
                    $validations[$i + $off] = 0;
                    $err = $err ? $err : 2;
                }
            } elseif ($field_required) {

                cforms2_dbg("\t\t ...is required! check: current_field=$current_field");

                if (in_array($field_type, array('html5color', 'html5date', 'html5datetime', 'html5datetime-local', 'html5email', 'html5month', 'html5number', 'html5range', 'html5search', 'html5tel', 'html5time', 'html5url', 'html5week',
                            'pwfield', 'textfield', 'datepicker', 'textarea'))) {

                    $validations[$i + $off] = $current_field != '';
                } elseif ($field_type == "checkbox") {

                    $validations[$i + $off] = $current_field != '';
                } elseif ($field_type == "selectbox" || $field_type == "emailtobox") {

                    $validations[$i + $off] = !($current_field == '-' );
                } elseif ($field_type == "multiselectbox") {

                    // how many multiple selects?
                    $all_options = $current_field;
                    if (count($all_options) <= 1 && $all_options[0] == '')
                        $validations[$i + $off] = false;
                    else
                        $validations[$i + $off] = true;
                } elseif ($field_type == "upload") {
                    $validations[$i + $off] = !($_FILES['cf_uploadfile' . $no]['name'][$filefield] == '');
                    if (!$validations[$i + $off] && $err == 0) {
                        $err = 3;
                        $fileerr = $cformsSettings['global']['cforms_upload_err2'];
                    }
                } elseif ($field_type == "radiobuttons") {

                    $validations[$i + $off] = $current_field != '';
                }

                if (!$validations[$i + $off] && $err == 0)
                    $err = 1;
            }
            else {
                $validations[$i + $off] = 1;
            }

            if ($field_type == "upload" && isset($_FILES['cf_uploadfile' . $no]['name'][$filefield])) {
                $filefield++;
            }

            // regexpression now outside of 'is required'
            if (in_array($field_type, array('pwfield', 'textfield', 'datepicker', 'textarea'))) {

                // regexpression set for textfields?
                $obj = explode('|', $c_title[0], 3);
                $obj[] = "";
                $obj[] = "";

                if (!empty($obj[2])) {
                    // check against other field!
                    if (isset($_POST[$obj[2]]) && !empty($_POST[$obj[2]])) {

                        if ($current_field != $_POST[$obj[2]])
                            $validations[$i + $off] = false;
                    }
                    else {
                        // classic regexpression
                        $reg_exp = str_replace('/', '\/', stripslashes($obj[2]));

                        cforms2_dbg("\t\t ...REGEXP check content: $current_field =? $reg_exp");

                        // multi-line textarea regexp trick
                        if ($field_type == 'textarea')
                            $valField = (string) str_replace(array("\r", "\r\n", "\n"), ' ', $current_field);
                        else
                            $valField = $current_field;

                        if (!empty($current_field) && !preg_match('/' . $reg_exp . '/', $valField)) {
                            $validations[$i + $off] = false;
                        }
                    }
                }
                if (!$validations[$i + $off] && $err == 0)
                    $err = 1;
            }



            $all_valid = $all_valid && $validations[$i + $off];

            if (!empty($c_err[1]) && $validations[$i + $off] == false) {
                $c_errflag = 4;
                $custom_error .= '<li><a href="#li-' . $no . '-' . ($i + $off) . '">' . stripslashes($c_err[1]) . ' &raquo;</li></a>';
            }
        }


    // have to upload a file?

    $file = array();

    if (isset($_FILES['cf_uploadfile' . $no]) && $all_valid) {

        for ($i = 0; $i < $filefield; $i++) {
            $file['name'][] = $_FILES['cf_uploadfile' . $no]['name'][$i];
            $file['type'][] = $_FILES['cf_uploadfile' . $no]['type'][$i];
            $file['tmp_name'][] = $_FILES['cf_uploadfile' . $no]['tmp_name'][$i];
            $file['error'][] = $_FILES['cf_uploadfile' . $no]['error'][$i];
            $file['size'][] = $_FILES['cf_uploadfile' . $no]['size'][$i];
        }

        foreach ($file['name'] as $i => $value) {

            // this will check if any blank field is entered
            if (!empty($value)) {

                $fileerr = '';
                // A successful upload will pass this test. It makes no sense to override this one.
                if ($file['error'][$i] > 0)
                    $fileerr = $cformsSettings['global']['cforms_upload_err1'];

                // A successful upload will pass this test. It makes no sense to override this one.
                $fileext[$i] = strtolower(substr($value, strrpos($value, '.') + 1, strlen($value)));
                $allextensions = explode(',', preg_replace('/\s/', '', strtolower($cformsSettings['form' . $no]['cforms' . $no . '_upload_ext'])));

                if (!in_array($fileext[$i], $allextensions) && $allextensions[0] !== "*")
                    $fileerr = $cformsSettings['global']['cforms_upload_err5'];

                // A non-empty file will pass this test.
                if (!( $file['size'][$i] > 0 ))
                    $fileerr = $cformsSettings['global']['cforms_upload_err2'];

                // A non-empty file will pass this test.
                if ((int) $cformsSettings['form' . $no]['cforms' . $no . '_upload_size'] > 0) {
                    if ($file['size'][$i] >= (int) $cformsSettings['form' . $no]['cforms' . $no . '_upload_size'] * 1024)
                        $fileerr = $cformsSettings['global']['cforms_upload_err3'];
                }

                // A properly uploaded file will pass this test. There should be no reason to override this one.
                if (!is_uploaded_file($file['tmp_name'][$i]))
                    $fileerr = $cformsSettings['global']['cforms_upload_err4'];

                if (!empty($fileerr)) {
                    $err = 3;
                    $all_valid = false;
                }
            }
        }
    }

    // what kind of error message?
    switch ($err) {
        case 0: break;
        case 1:
            $usermessage_text = preg_replace(array("|\\\'|", '/\\\"/', '|\r\n|'), array('&#039;', '&quot;', '<br />'), '<span>' . $cformsSettings['form' . $no]['cforms' . $no . '_failure'] . '</span>');
            $usermessage_class = ' failure';
            break;
        case 2:
            $usermessage_text = preg_replace(array("|\\\'|", '/\\\"/', '|\r\n|'), array('&#039;', '&quot;', '<br />'), '<span>' . $cformsSettings['global']['cforms_codeerr'] . '</span>');
            $usermessage_class = ' failure';
            break;
        case 3:
            $usermessage_text = preg_replace(array("|\\\'|", '/\\\"/', '|\r\n|'), array('&#039;', '&quot;', '<br />'), '<span>' . $fileerr . '</span>');
            $usermessage_class = ' failure';
            break;
        case 4:
            $usermessage_text = preg_replace(array("|\\\'|", '/\\\"/', '|\r\n|'), array('&#039;', '&quot;', '<br />'), '<span>' . $cformsSettings['form' . $no]['cforms' . $no . '_failure'] . '</span>');
            $usermessage_class = ' failure';
            break;
    }

    if ($err !== 0 && $c_errflag)
        $usermessage_text .= '<ol>' . $custom_error . '</ol>';


    $track = array();

    if (isset($_POST['sendbutton' . $no]) && $all_valid) {

        // all valid? get ready to send

        if (!cforms2_check_time($no)) {
            return array(
                'text' => $usermessage_text,
                'class' => $usermessage_class,
                'all_valid' => $all_valid,
                'track' => $track,
                'limit_reached' => true,
                'validations' => $validations
            );
        }

        $usermessage_text = preg_replace('|\r\n|', '<br />', stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_success']));
        $usermessage_class = ' success';

        $trackinstance = array();

        $fieldsetnr = 1;
        $to_one = -1;
        $ccme = false;
        $field_email = '';

        $filefield = 0;

        $inpFieldArr = array(); // for var[] type input fields

        $captchas = cforms2_get_pluggable_captchas();

        for ($i = 1; $i <= $field_count; $i++) {

            if (!$custom)
                $field_stat = explode('$#$', $cformsSettings['form' . $no]['cforms' . $no . '_count_field_' . $i]);
            else
                $field_stat = explode('$#$', $customfields[$i - 1]);
            $field_stat[] = "";

            // filter non input fields
            while (in_array($field_stat[1], array_merge(array_keys($captchas), array('fieldsetstart', 'fieldsetend', 'textonly')))) {

                if (cforms2_check_pluggable_captchas_authn_users($field_stat[1]))
                    break;

                if ($field_stat[1] == 'fieldsetstart') {
                    $track['$$$' . $i] = 'Fieldset' . $fieldsetnr;
                    $track['Fieldset' . $fieldsetnr++] = $field_stat[0];
                } elseif ($field_stat[1] == 'fieldsetend') {
                    $track['FieldsetEnd' . $fieldsetnr++] = '--';
                }

                // get next in line...
                $i++;

                if (!$custom)
                    $field_stat = explode('$#$', $cformsSettings['form' . $no]['cforms' . $no . '_count_field_' . $i]);
                else
                    $field_stat = explode('$#$', $customfields[$i - 1]);
                $field_stat[] = "";

                if ($field_stat[1] == '') {
                    // all fields searched, break both while & for
                    break 2;
                }
            }

            $field_name = $field_stat[0];
            $field_type = $field_stat[1];

            $custom_names = $cformsSettings['form' . $no]['cforms' . $no . '_customnames'] == '1';

            if ($custom_names) {

                // hardcoded for now
                $tmpName = $field_name;

                if (strpos($tmpName, '[id:') !== false) {
                    $isFieldArray = strpos($tmpName, '[]');

                    preg_match('/^([^\[]*)\[id:([^\|\]]+(\[\])?)\]([^\|]*).*/', $tmpName, $input_name); // author: cbacchini
                    $field_name = $input_name[1] . $input_name[4];
                    $customTrackingID = cforms2_sanitize_ids($input_name[2]);

                    $current_field = cforms2_sanitize_ids($customTrackingID);
                } else {
                    if (strpos($tmpName, '#') !== false && strpos($tmpName, '#') == 0) {
                        // special case with checkboxes with right label only and no ID
                        preg_match('/^#([^\|]*).*/', $field_name, $input_name);
                    } else {
                        // just take front part
                        preg_match('/^([^#\|]*).*/', $field_name, $input_name);
                    }
                    $current_field = cforms2_sanitize_ids($input_name[1]);
                    $customTrackingID = '';
                }
            } else
                $current_field = 'cf' . $no . '_field_' . $i;

            cforms2_dbg("(lib_validate) looking at field: $current_field");

            // dissect field
            $obj = explode('|', $field_name, 3);
            $obj[] = "";

            // strip out default value
            $field_name = $obj[0];
            if (!isset($_POST[$current_field]))
                $_POST[$current_field] = "";


            // find email address
            if ($field_stat[3] == '1') {
                if (!empty($field_email))
                    $field_email .= ', ';
                $field_email .= $_POST[$current_field];
            }


            // special case: select box and radio box
            if ($field_type == "checkboxgroup" || $field_type == "multiselectbox" || $field_type == "selectbox" || $field_type == "radiobuttons") {
                $field_name = explode('#', $field_name);
                $field_name = $field_name[0];
            }


            // special case: check box
            if ($field_type == "checkbox" || $field_type == "ccbox") {
                $field_name = explode('#', $field_name);
                $field_name = ($field_name[1] == '') ? $field_name[0] : $field_name[1];
                // if ccbox
                if ($field_type == "ccbox" && isset($_POST[$current_field])) {
                    if ($isMPform)
                        $ccme = 'cf_form' . $no . '_' . $field_name;
                    else
                        $ccme = $field_name;
                }
            }


            // special case where the value needs to be fetched from the DB!
            if ($field_type == "emailtobox") {
                $to_one = $_POST[$current_field];

                // can't use field_name, since '|' check earlier
                $field_name = explode('#', $field_stat[0]);

                // remove possible |set:true
                $tmp = explode('|', $field_name[$to_one + 1]);

                // values start from 0 or after!
                $value = $tmp[0];
                $to = $replyto = stripslashes($tmp[1]);

                $field_name = $field_name[0];
            } elseif ($field_type == "upload") {

                if (is_array($file) && is_array($file['name']) && isset($file['name'][$filefield])) {
                    $value = str_replace(' ', '_', $file['name'][$filefield++]);
                } else {
                    $value = '';
                }
            } elseif ($field_type == "multiselectbox" || $field_type == "checkboxgroup") {

                $all_options = $_POST[$current_field];
                if (!empty($all_options))
                    $value = stripslashes(implode(',', $all_options));
                else
                    $value = '';
            } elseif (array_key_exists($field_stat[1], $captchas)) {
                $value = $_POST[$field_stat[1]];
            } elseif ($field_type == 'hidden') {
                $value = rawurldecode($_POST[$current_field]);
            } elseif ($isFieldArray) {

                if (!$inpFieldArr[$current_field] || $inpFieldArr[$current_field] == '') {
                    $inpFieldArr[$current_field] = 0;
                }

                // covers all other fields' values
                $value = $_POST[$current_field][$inpFieldArr[$current_field] ++];
            } else {
                // covers all other fields' values
                $value = $_POST[$current_field];
            }

            // check boxes
            if ($field_type == "checkbox" || $field_type == "ccbox") {

                if ($value == 'on')
                    $value = '(x)';
                else
                    $value = '';
            }

            // determine tracked field name
            $inc = '';
            $trackname = trim(($field_type == "upload") ? $field_name . '[*' . ($no == '' ? 1 : $no) . ']' : $field_name);
            if (array_key_exists($trackname, $track)) {
                if (!isset($trackinstance[$trackname]) || $trackinstance[$trackname] == '')
                    $trackinstance[$trackname] = 2;
                $inc = '___' . ($trackinstance[$trackname] ++);
            }

            $track['$$$' . $i] = $trackname . $inc;
            $track[$trackname . $inc] = $value;
            if (!empty($customTrackingID))
                $track['$$$' . $customTrackingID] = $trackname . $inc;
        }

        // multi-part form session
        $ongoingSession = 'noSess';
        if ($cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_form']) {

            if (!empty($field_email))
                $_SESSION['cforms']['email'] = $field_email;
            if (!empty($ccme))
                $_SESSION['cforms']['ccme'] = $ccme;
            $_SESSION['cforms']['list'][$_SESSION['cforms']['pos'] ++] = $no;
            $_SESSION['cforms']['current'] = $no == '' ? 1 : $no;
            $_SESSION['cforms']['cf_form' . $no] = $track;

            cforms2_dbg("(lib_validate) In Session tracking for ($no)..." . print_r($_SESSION, 1));

            // fetch from prev. def
            $field_email = $_SESSION['cforms']['email'];

            $ccme = $_SESSION['cforms']['ccme'];
            $ongoingSession = '1';
        }


        // assemble text and HTML email
        if ($cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_form'] &&
                $cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_next'] == -1 &&
                is_array($_SESSION['cforms'])) {
            $track = cforms2_all_tracks($_SESSION['cforms']);
            $ongoingSession = '0';
        }

        cforms2_dbg('$track = ' . print_r($track, 1));


        $r = cforms2_format_email($track, $no);
        $formdata = $r['text'];
        $htmlformdata = $r['html'];


        $noTracking = $cformsSettings['form' . $no]['cforms' . $no . '_notracking'];
        if (!$noTracking)
            cforms2_write_tracking_record($no, $field_email, $track);

        if (!($to_one != -1 && !empty($to))) {
            $to = $replyto = preg_replace(array('/;|#|\|/'), array(','), stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_email']));
        }


        // Files attached??
        if (is_array($file)) {
            $inSession = $noTracking || $ongoingSession == '0';
            cforms2_move_files($no, $inSession, $file);
        }
        // end of session


        $trackf = array();
        $trackf['id'] = $no;
        $trackf['data'] = $track;
        $trackf['data']['page'] = cforms2_get_current_page();
        $trackf['title'] = $cformsSettings['form' . $no]['cforms' . $no . '_fname'];

        // ready to send email

        $frommail = cforms2_check_cust_vars(stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_fromemail']), $track);

        // either use configured subject or user determined
        // now replace the left over {xyz} variables with the input data
        $vsubject = stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_subject']);
        $vsubject = cforms2_check_default_vars($vsubject, $no);
        $vsubject = cforms2_check_cust_vars($vsubject, $track);

        // prepare message text, replace variables
        $message = stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_header']);
        $message = cforms2_check_default_vars($message, $no);
        $message = cforms2_check_cust_vars($message, $track);

        // actual user message
        $htmlmessage = '';
        if (substr($cformsSettings['form' . $no]['cforms' . $no . '_formdata'], 2, 1) == '1') {
            $htmlmessage = stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_header_html']);
            $htmlmessage = cforms2_check_default_vars($htmlmessage, $no);
            $htmlmessage = cforms2_check_cust_vars($htmlmessage, $track, true);
        }

        $mail = new cforms2_mail($no, $frommail, $to, $field_email, true);
        $mail->subj = $vsubject;

        // HTML email
        if ($mail->html_show) {
            $mail->set_html(true);
            $mail->body = $cformsSettings['global']['cforms_style_doctype'] . "\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\n<head><title></title></head><body {$cformsSettings['global']['cforms_style']['body']}>" . $htmlmessage . ( $mail->f_html ? "\n$htmlformdata" : '') . "\n</body></html>\n";
            $mail->body_alt = $message . ($mail->f_txt ? "\n$formdata" : '');
        } else
            $mail->body = $message . ($mail->f_txt ? "\n$formdata" : '');

        $usermessage_text = cforms2_check_default_vars($usermessage_text, $no);
        $usermessage_text = cforms2_check_cust_vars($usermessage_text, $track);
        $usermessage_text = apply_filters('cforms2_usermessage_filter', $usermessage_text, $no, $pid);


        $fdata = array();
        $fpointer = 0;

        cforms2_dbg('File Attachments:');

        // attachments wanted for current form? (tracking session form uploads handled above!)
        $doAttach = !($cformsSettings['form' . $no]['cforms' . $no . '_noattachments']);

        // form with files, within session or single form
        if ($ongoingSession != '0' && is_array($file) && !empty($file)) {
            foreach ($file['tmp_name'] as $fn) {
                cforms2_add_file($fn, $fdata, $fpointer, $doAttach);
                cforms2_dbg("File = $fn, attach = $doAttach");
            }
        }

        // end of session with files
        if ($ongoingSession == '0' && is_array($_SESSION['cforms']['upload'])) {
            foreach (array_keys($_SESSION['cforms']['upload']) as $n) {
                if ($_SESSION['cforms']['upload'][$n]['files'])
                    foreach (array_keys($_SESSION['cforms']['upload'][$n]['files']) as $m) {
                        cforms2_add_file($_SESSION['cforms']['upload'][$n]['files'][$m], $fdata, $fpointer, $_SESSION['cforms']['upload'][$n]['doAttach']);
                        cforms2_dbg("(end of session) File = " . $_SESSION['cforms']['upload'][$n]['files'][$m] . ", attach = " . $_SESSION['cforms']['upload'][$n]['doAttach']);
                    }
            }
        }
        // parse through all files (both single and multi-part forms)
        foreach ($fdata as $file) {
            if ($file['doAttach'] && !empty($file['name'])) {
                $mail->add_file($file['name']); // optional name
                cforms2_dbg('Attaching file (' . $file['name'] . ') to email');
            }
        }

        // end adding attachments



        $trackf['uploaded_files'] = $fdata;
        $trackf['submit_time'] = time();
        $trackf['email'] = $field_email;
        try {
            // This action is meant to enable you to implement additional features
            // after validating and most other processing are done
            do_action('cforms2_after_processing_action', $trackf);
        } catch (Exception $exc) {
            $usermessage_text = $exc->getMessage();
            $usermessage_class = ' failure';
            $sentadmin = 1;
        }

        cforms2_dbg('TRACKF' . print_r($trackf, 1) . "\n");

        if ($cformsSettings['form' . $no]['cforms' . $no . '_emailoff'] == '1') {
            $sentadmin = 1;
        } else {
            // This filter allows manipulation of the admin email just before sending
            $mail = apply_filters('cforms2_admin_email_filter', $mail, $no, $pid);
            $sentadmin = $mail->send();
        }

        if ($sentadmin == 1) {

            if (isset($trackf['data'][$ccme]))
                cforms2_dbg("is CC: = $ccme, active = {$trackf['data'][$ccme]} | ");

            // send copy or notification?
            // not if no email and already CC'ed
            if (($cformsSettings['form' . $no]['cforms' . $no . '_confirm'] == '1' && !empty($field_email)) || $ccme && !empty($trackf['data'][$ccme])) {

                $frommail = cforms2_check_cust_vars(stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_fromemail']), $track);

                // actual user message
                $cmsg = stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_cmsg']);
                $cmsg = cforms2_check_default_vars($cmsg, $no);
                $cmsg = cforms2_check_cust_vars($cmsg, $track);

                // HTML text
                $cmsghtml = '';
                if (substr($cformsSettings['form' . $no]['cforms' . $no . '_formdata'], 3, 1) == '1') {
                    $cmsghtml = stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_cmsg_html']);
                    $cmsghtml = cforms2_check_default_vars($cmsghtml, $no);
                    $cmsghtml = cforms2_check_cust_vars($cmsghtml, $track, true);
                }

                // subject
                $subject2 = stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_csubject']);
                $subject2 = cforms2_check_default_vars($subject2, $no);
                $subject2 = cforms2_check_cust_vars($subject2, $track);

                // different cc and ac subjects?
                $s = explode('$#$', $subject2);
                $s[1] = empty($s[1]) ? $s[0] : $s[1];

                $mail = new cforms2_mail($no, $frommail, $field_email, $replyto);

                // auto conf attachment?
                $a = $cformsSettings['form' . $no]['cforms' . $no . '_cattachment'][0];
                if (!empty($a)) {
                    $a = (substr($a, 0, 1) === '/') ? $a : plugin_dir_path(__FILE__) . $a;
                    if (file_exists($a))
                        $mail->add_file($a);
                }

                // CC or auto conf?
                if ($ccme && !empty($trackf['data'][$ccme])) {
                    $mail->subj = $s[1];
                    if ($mail->html_show) {
                        $mail->set_html(true);
                        $mail->body = $cformsSettings['global']['cforms_style_doctype'] . "\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\n<head><title></title></head><body {$cformsSettings['global']['cforms_style']['body']}>" . $htmlmessage . ( $mail->f_html ? "\n$htmlformdata" : '') . "\n</body></html>\n";
                        $mail->body_alt = $message . ($mail->f_txt ? "\n$formdata" : '');
                    } else
                        $mail->body = $message . ($mail->f_txt ? "\n$formdata" : '');

                    // This filter allows manipulation of the cc me email just before sending
                    $mail = apply_filters('cforms2_cc_me_email_filter', $mail, $no, $pid);
                    $sent = $mail->send();
                }
                else { // auto conf
                    $mail->subj = $s[0];
                    if ($mail->html_show_ac) {
                        $mail->set_html(true);
                        $mail->body = $cformsSettings['global']['cforms_style_doctype'] . "\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\n<head><title></title></head><body {$cformsSettings['global']['cforms_style']['body']}>" . $cmsghtml . "</body></html>\n";
                        $mail->body_alt = $cmsg;
                    } else
                        $mail->body = $cmsg;

                    // This filter allows manipulation of the auto conf email just before sending
                    $mail = apply_filters('cforms2_auto_conf_email_filter', $mail, $no, $pid);
                    $sent = $mail->send();
                }

                if (!$sent) {
                    $usermessage_text = __('Error occurred while sending the auto confirmation message: ', 'cforms2') . '<br />' . $mail->err;
                    $usermessage_class = ' mailerr';
                }
            }
        } else {
            $usermessage_text = __('Error occurred while sending the message: ', 'cforms2') . '<br />' . $mail->err;
            $usermessage_class = ' mailerr';
        }
    }

    return array(
        'text' => $usermessage_text,
        'class' => $usermessage_class,
        'all_valid' => $all_valid,
        'track' => $track,
        'limit_reached' => false,
        'validations' => $validations
    );

}
