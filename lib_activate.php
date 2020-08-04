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

function cforms2_setup_db() {
    global $cformsSettings;
    $cformsSettings = get_option('cforms_settings');
    if (!is_array($cformsSettings)) {
        $cformsSettings = array();
        $cformsSettings['global'] = array();
    }

    // New global settings container
    // Common HTML message information

    $cformsSettings['global']['cforms_style_doctype'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
    $cformsSettings['global']['v'] = CFORMS2_VERSION;

    unset($cformsSettings['global']['cforms_style']);
    $cformsSettings['global']['cforms_style']['body'] = 'style="margin:0; padding:0; font-size: 13px; color:#555;"';
    $cformsSettings['global']['cforms_style']['meta'] = 'style="font-size: 90%; margin:0; background:#aaaaaa; padding:1em 2em 1em 0.6em; color:#555555; border-bottom:1px solid #9d9d9d;"';
    $cformsSettings['global']['cforms_style']['admin'] = 'style="background:#f0f0f0; border-top:1px solid #777;"';
    $cformsSettings['global']['cforms_style']['title'] = 'style="font-size: 90%; margin:0; background:#fcfcfc; padding:1em 2em 1em 0.6em; color:#888888; display:inline-block;"';
    $cformsSettings['global']['cforms_style']['table'] = 'style="width:auto; margin: 0.2em 2em 2em; font-size: 100%;"';
    $cformsSettings['global']['cforms_style']['fs'] = 'style="color:#555; padding:1em 0 0.4em; font-size: 110%; font-weight:bold;"';
    $cformsSettings['global']['cforms_style']['key_td'] = 'style="padding: 0.3em 1em; border-bottom:1px dotted #ddd; padding-right:2em; color:#888; width:1%;"';
    $cformsSettings['global']['cforms_style']['val_td'] = 'style="padding: 0.3em 1em; border-bottom:1px dotted #ddd; padding-left:0; color:#333;"';

    $cformsSettings['global']['cforms_style']['autoconf'] = 'style="padding:1em 1em 0; background:#f0f0f0; color:#333;"';
    $cformsSettings['global']['cforms_style']['dear'] = 'style="margin:0.5em 30px; font-weight:bold; margin-bottom:1.2em;"';
    $cformsSettings['global']['cforms_style']['confp'] = 'style="margin:0.5em 30px;"';
    $cformsSettings['global']['cforms_style']['confirmationmsg'] = 'style="margin:4em 30px 0; padding-bottom:1em; font-size:80%; color:#aaa;"';

    if (!isset($cformsSettings['global']['cforms_inexclude']['ex']))
        $cformsSettings['global']['cforms_inexclude']['ex'] = '';
    if (!isset($cformsSettings['global']['cforms_inexclude']['ids']))
        $cformsSettings['global']['cforms_inexclude']['ids'] = '';

    // file upload
    cforms2_set_ini('form', 'cforms_upload_ext', 'txt,zip');
    cforms2_set_ini('form', 'cforms_upload_size', '1024');
    cforms2_set_ini('form', 'cforms_dontclear', false);

    // fields for default form
    cforms2_set_ini('form', 'cforms_count_fields', '5');
    cforms2_set_ini('form', 'cforms_count_field_1', __('My Fieldset', 'cforms2') . '$#$fieldsetstart$#$0$#$0$#$0$#$0$#$0');
    cforms2_set_ini('form', 'cforms_count_field_2', __('Your Name|Your Name', 'cforms2') . '$#$textfield$#$1$#$0$#$1$#$0$#$0');
    cforms2_set_ini('form', 'cforms_count_field_3', __('Email', 'cforms2') . '$#$textfield$#$1$#$1$#$0$#$0$#$0');
    cforms2_set_ini('form', 'cforms_count_field_4', __('Website', 'cforms2') . '|http://$#$textfield$#$0$#$0$#$0$#$0$#$0');
    cforms2_set_ini('form', 'cforms_count_field_5', __('Message', 'cforms2') . '$#$textarea$#$0$#$0$#$0$#$0$#$0');

    cforms2_set_ini('form', 'cforms_required', __('(required)', 'cforms2'));
    cforms2_set_ini('form', 'cforms_emailrequired', __('(valid email required)', 'cforms2'));

    cforms2_set_ini('form', 'cforms_confirm', '0');
    cforms2_set_ini('form', 'cforms_ajax', '1');
    cforms2_set_ini('form', 'cforms_fname', __('Your default form', 'cforms2'));
    cforms2_set_ini('form', 'cforms_csubject', __('Re: Your note', 'cforms2') . '$#$' . __('Re: Submitted form (copy)', 'cforms2'));

    cforms2_set_ini('form', 'cforms_cmsg', __('Dear {Your Name},', 'cforms2') . "\n" . __('Thank you for your note!', 'cforms2') . "\n" . __('We will get back to you as soon as possible.', 'cforms2') . "\n\n");
    cforms2_set_ini('form', 'cforms_cmsg_html', '<div ' . $cformsSettings['global']['cforms_style']['autoconf'] . '><p ' . $cformsSettings['global']['cforms_style']['dear'] . '>' . __('Dear {Your Name},', 'cforms2') . "</p>\n<p " . $cformsSettings['global']['cforms_style']['confp'] . '>' . __('Thank you for your note!', 'cforms2') . "</p>\n<p " . $cformsSettings['global']['cforms_style']['confp'] . '>' . __('We will get back to you as soon as possible.', 'cforms2') . "\n<div " . $cformsSettings['global']['cforms_style']['confirmationmsg'] . '>' . __('This is an automatic confirmation message.', 'cforms2') . " {Date}.</div></div>\n\n");

    cforms2_set_ini('form', 'cforms_emailoff', '0');
    cforms2_set_ini('form', 'cforms_emptyoff', '0');
    cforms2_set_ini('form', 'cforms_tellafriend', '0');
    cforms2_set_ini('form', 'cforms_customnames', false);
    cforms2_set_ini('form', 'cforms_startdate', ' ');
    cforms2_set_ini('form', 'cforms_enddate', ' ');
    cforms2_set_ini('form', 'cforms_formaction', false);

    cforms2_set_ini('form', 'cforms_email', get_bloginfo('admin_email'));
    cforms2_set_ini('form', 'cforms_fromemail', get_bloginfo('admin_email'));
    cforms2_set_ini('form', 'cforms_bcc', '');

    cforms2_set_ini('form', 'cforms_header', __('A new submission (form: "{Form Name}")', 'cforms2') . "\r\n============================================\r\n" . __('Submitted on: {Date}', 'cforms2') . "\r\n" . __('Via: {Page}', 'cforms2') . "\r\n" . __('By {IP} (visitor IP)', 'cforms2') . ".\r\n" . ".\r\n");
    cforms2_set_ini('form', 'cforms_header_html', '<p ' . $cformsSettings['global']['cforms_style']['meta'] . '>' . __('A form has been submitted on {Date}, via: {Page} [IP {IP}]', 'cforms2') . '</p>');

    cforms2_set_ini('form', 'cforms_formdata', '1111');
    cforms2_set_ini('form', 'cforms_space', '30');
    cforms2_set_ini('form', 'cforms_noattachments', '0');

    cforms2_set_ini('form', 'cforms_subject', __('A comment from {Your Name}', 'cforms2'));
    cforms2_set_ini('form', 'cforms_submit_text', __('Submit', 'cforms2'));
    cforms2_set_ini('form', 'cforms_success', __('Thank you for your comment!', 'cforms2'));
    cforms2_set_ini('form', 'cforms_failure', __('Please fill in all the required fields.', 'cforms2'));
    cforms2_set_ini('form', 'cforms_limittxt', '<strong>' . __('No more submissions accepted at this time.', 'cforms2') . '</strong>');

    cforms2_set_ini('form', 'cforms_working', __('One moment please...', 'cforms2'));
    cforms2_set_ini('form', 'cforms_showpos', 'ynyy');

    cforms2_set_ini('form', 'cforms_hide', false);
    cforms2_set_ini('form', 'cforms_redirect', false);
    cforms2_set_ini('form', 'cforms_redirect_page', '');

    cforms2_set_ini('form', 'cforms_action', false);
    cforms2_set_ini('form', 'cforms_action_page', '');
    cforms2_set_ini('form', 'cforms_mp', array(
        "mp_form" => false,
        "mp_next" => "",
        "mp_first" => false,
        "mp_reset" => false,
        "mp_resettext" => "",
        "mp_back" => false,
        "mp_backtext" => ""
    ));

    // global file settings

    cforms2_set_ini('global', 'cforms_upload_err1', __('Generic file upload error. Please try again', 'cforms2'));
    cforms2_set_ini('global', 'cforms_upload_err2', __('File is empty. Please upload something more substantial.', 'cforms2'));
    cforms2_set_ini('global', 'cforms_upload_err3', __('Sorry, file is too large. You may try to zip your file.', 'cforms2'));
    cforms2_set_ini('global', 'cforms_upload_err4', __('File upload failed. Please try again or contact the blog admin.', 'cforms2'));
    cforms2_set_ini('global', 'cforms_upload_err5', __('File not accepted, file type not allowed.', 'cforms2'));

    cforms2_set_ini('global', 'cforms_captcha_def', array('foqa' => '0'));
    cforms2_set_ini('global', 'cforms_sec_qa', __('What color is snow?=white', 'cforms2') . "\r\n" . __('The color of grass is=green', 'cforms2') . "\r\n" . __('Ten minus five equals=five', 'cforms2'));
    cforms2_set_ini('global', 'cforms_codeerr', __('Please double-check your verification code.', 'cforms2'));

    cforms2_set_ini('global', 'cforms_show_quicktag', '1');

    cforms2_set_ini('global', 'cforms_css', 'cforms2012.css');

    // migrate previous MP settings
    for ($i = 1; $i <= count(Cforms2\FormSettings::forms()); $i++) {

        $no = ($i == '1') ? '' : $i;
        if (isset($cformsSettings['form' . $no]['mp']) && is_array($cformsSettings['form' . $no]['mp']) && !is_array($cformsSettings['form' . $no]['cforms' . $no . '_mp'])) {

            foreach (array_keys($cformsSettings['form' . $no]['mp']) as $k) {
                preg_match('/cforms\d*_(.*)/', $k, $kk);
                $cformsSettings['form' . $no]['cforms' . $no . '_mp'][$kk[1]] = $cformsSettings['form' . $no]['mp'][$k];
            }
        }
    }


    // Update 'the one'
    update_option('cforms_settings', $cformsSettings);

}

/** check if option is set */
function cforms2_set_ini($s, $v, $d) {
    global $cformsSettings;
    if (!is_array($cformsSettings))
        $cformsSettings = array();
    if (!array_key_exists($s, $cformsSettings) || !is_array($cformsSettings[$s]))
        $cformsSettings[$s] = array();
    if (!in_array($v, array_keys($cformsSettings[$s])))
        $cformsSettings[$s][$v] = $d;

}
