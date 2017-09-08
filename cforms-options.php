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

global $wpdb;

$cformsSettings = get_option('cforms_settings');

$plugindir = dirname(plugin_basename(__FILE__));

// Check Whether User Can Manage Database
cforms2_check_access_priv();

// if all data has been erased quit
if (cforms2_check_erased())
    return;


// default to 1 & get real #
$formcount = $cformsSettings['global']['cforms_formcount'];

if (isset($_POST['addbutton'])) {
    $formcount = $formcount + 1;
    $no = $no_disp = $formcount;

    $cformsSettings['global']['cforms_formcount'] = (string) $formcount;

    // new settings container
    $cformsSettings['form' . $no] = array(
        'cforms' . $no . '_dontclear' => false,
        'cforms' . $no . '_count_fields' => '1',
        'cforms' . $no . '_count_field_1' => __('My Fieldset', 'cforms2') . '$#$fieldsetstart$#$0$#$0$#$0$#$0$#$0',
        'cforms' . $no . '_required' => __('(required)', 'cforms2'),
        'cforms' . $no . '_emailrequired' => __('(valid email required)', 'cforms2'),
        'cforms' . $no . '_ajax' => '1',
        'cforms' . $no . '_confirm' => '0',
        'cforms' . $no . '_fname' => __('A new form', 'cforms2'),
        'cforms' . $no . '_csubject' => __('Re: Your note', 'cforms2') . '$#$' . __('Re: Submitted form (copy)', 'cforms2'),
        'cforms' . $no . '_cmsg' => __('Dear {Your Name},', 'cforms2') . "\n" . __('Thank you for your note!', 'cforms2') . "\n" . __('We will get back to you as soon as possible.', 'cforms2') . "\n\n",
        'cforms' . $no . '_cmsg_html' => '<div ' . $cformsSettings['global']['cforms_style']['autoconf'] . '><p ' . $cformsSettings['global']['cforms_style']['dear'] . '>' . __('Dear {Your Name},', 'cforms2') . "</p>\n<p " . $cformsSettings['global']['cforms_style']['confp'] . '>' . __('Thank you for your note!', 'cforms2') . "</p>\n<p " . $cformsSettings['global']['cforms_style']['confp'] . '>' . __('We will get back to you as soon as possible.', 'cforms2') . "\n<div " . $cformsSettings['global']['cforms_style']['confirmationmsg'] . '>' . __('This is an automatic confirmation message.', 'cforms2') . " {Date}.</div></div>\n\n",
        'cforms' . $no . '_emailoff' => '0',
        'cforms' . $no . '_emptyoff' => '0',
        'cforms' . $no . '_tellafriend' => '0',
        'cforms' . $no . '_customnames' => '0',
        'cforms' . $no . '_startdate' => ' ',
        'cforms' . $no . '_enddate' => ' ',
        'cforms' . $no . '_formaction' => false,
        'cforms' . $no . '_email' => get_bloginfo('admin_email'),
        'cforms' . $no . '_fromemail' => get_bloginfo('admin_email'),
        'cforms' . $no . '_bcc' => '',
        'cforms' . $no . '_header' => __('A new submission (form: "{Form Name}")', 'cforms2') . "\r\n============================================\r\n" . __('Submitted on: {Date}', 'cforms2') . "\r\n" . __('Via: {Page}', 'cforms2') . "\r\n" . __('By {IP} (visitor IP)', 'cforms2') . ".\r\n" . ".\r\n",
        'cforms' . $no . '_header_html' => '<p ' . $cformsSettings['global']['cforms_style']['meta'] . '>' . __('A form has been submitted on {Date}, via: {Page} [IP {IP}]', 'cforms2') . '</p>',
        'cforms' . $no . '_formdata' => '1111',
        'cforms' . $no . '_space' => '30',
        'cforms' . $no . '_noattachments' => '0',
        'cforms' . $no . '_subject' => __('A comment from {Your Name}', 'cforms2'),
        'cforms' . $no . '_submit_text' => __('Submit', 'cforms2'),
        'cforms' . $no . '_success' => __('Thank you for your comment!', 'cforms2'),
        'cforms' . $no . '_failure' => __('Please fill in all the required fields.', 'cforms2'),
        'cforms' . $no . '_limittxt' => '<strong>' . __('No more submissions accepted at this time.', 'cforms2') . '</strong>',
        'cforms' . $no . '_working' => __('One moment please...', 'cforms2'),
        'cforms' . $no . '_showpos' => 'ynyy',
        'cforms' . $no . '_hide' => false,
        'cforms' . $no . '_redirect' => false,
        'cforms' . $no . '_redirect_page' => __('http://redirect.to.this.page', 'cforms2'),
        'cforms' . $no . '_action' => '0',
        'cforms' . $no . '_action_page' => 'http://',
        'cforms' . $no . '_upload_ext' => 'txt,zip',
        'cforms' . $no . '_upload_size' => '1024',
        'cforms' . $no . '_mp' => array(
            "mp_form" => false,
            "mp_next" => "",
            "mp_first" => false,
            "mp_reset" => false,
            "mp_resettext" => "",
            "mp_back" => false,
            "mp_backtext" => ""
        )
    );

    update_option('cforms_settings', $cformsSettings);
    echo '<div id="message" class="updated fade"><p>' . __('A new form with default fields has been added.', 'cforms2') . '</p></div>';
} elseif (isset($_POST['dupbutton'])) {
    $no_disp = '1';
    $no = '';
    if (isset($_POST['no'])) {
        if ($_POST['no'] !== '1')
            $no_disp = $no = (int) $_POST['no'];
    }

    $formcount++;
    $cformsSettings['global']['cforms_formcount'] = (string) ($formcount);

    // new settings container
    foreach (array_keys($cformsSettings['form' . $no]) as $k) {
        $tmp = preg_match('/cforms\d*_(.*)/', $k, $kk);
        if (strpos($k, '_fname') !== false)
            $cformsSettings['form' . $formcount]['cforms' . $formcount . '_' . $kk[1]] = $cformsSettings['form' . $no][$k] . ' (' . __('copy of form #', 'cforms2') . ($no == '' ? '1' : $no) . ')';
        else
            $cformsSettings['form' . $formcount]['cforms' . $formcount . '_' . $kk[1]] = $cformsSettings['form' . $no][$k];
    }

    echo '<div id="message" class="updated fade"><p>' . __('The form has been duplicated, you\'re now working on the copy.', 'cforms2') . '</p></div>';

    update_option('cforms_settings', $cformsSettings);

    //set $no afterwards: need it to duplicate fields
    $no = $no_disp = $formcount;
} elseif (isset($_POST['delbutton']) && $formcount > 1) {
    $no_disp = '1';
    $no = '';
    if ($_POST['no'] !== '1')
        $no_disp = $no = (int) $_POST['no'];

    for ($i = (int) $no_disp; $i < $cformsSettings['global']['cforms_formcount']; $i++) {  // move all forms "to the left"
        $n = ($i == 1) ? '' : $i;
        unset($cformsSettings['form' . $n]);

        foreach (array_keys($cformsSettings['form' . ($i + 1)]) as $key) {
            $newkey = ( strpos($key, 'form2_') !== false ) ? str_replace('2_', '_', $key) : str_replace(($i + 1) . '_', $i . '_', $key);
            $cformsSettings['form' . $n][$newkey] = $cformsSettings['form' . ($i + 1)][$key];
        }
    }

    unset($cformsSettings['form' . $cformsSettings['global']['cforms_formcount']]);

    $formcount = $formcount - 1;

    if ($formcount > 1) {
        if (isset($_POST['no']) && (int) $_POST['no'] > $formcount) // otherwise stick with the current form
            $no = $no_disp = $formcount;
    } else {
        $no_disp = '1';
        $no = '';
    }
    $cformsSettings['global']['cforms_formcount'] = (string) ($formcount);

    update_option('cforms_settings', $cformsSettings);

    echo '<div id="message" class="updated fade"><p>' . __('Form deleted', 'cforms2') . '.</p></div>';
} else {

    // set parameters to default, if not exists
    $no_disp = '1';
    $no = '';
    if (isset($_POST['switchform'])) { // only set when hitting form chg buttons
        if ($_POST['switchform'] !== '1')
            $no_disp = $no = (int) $_POST['switchform'];
    }
    elseif (isset($_POST['go'])) { // only set when hitting form chg buttons
        if ($_POST['pickform'] !== '1')
            $no_disp = $no = (int) $_POST['pickform'];
    }
    elseif (isset($_POST['noSub']) && (int) $_POST['noSub'] > 1) { // otherwise stick with the current form
        $no_disp = $no = (int) $_POST['noSub'];
    }
}

// Update Settings TODO What is array_search("X", $_POST) for?
if (isset($_POST['SubmitOptions']) || isset($_POST['AddField']) || array_search("X", $_POST)) {
    require_once(plugin_dir_path(__FILE__) . 'lib_options_sub.php');
    cforms2_option_submission($no, $cformsSettings);
}

// default: $field_count = what's in the DB
$field_count = $cformsSettings['form' . $no]['cforms' . $no . '_count_fields'];

// Reset Admin and AutoConf messages
if (isset($_POST['cforms_resetAdminMsg'])) {
    $cformsSettings['form' . $no]['cforms' . $no . '_header'] = __('A new submission (form: "{Form Name}")', 'cforms2') . "\r\n============================================\r\n" . __('Submitted on: {Date}', 'cforms2') . "\r\n" . __('Via: {Page}', 'cforms2') . "\r\n" . __('By {IP} (visitor IP)', 'cforms2') . ".\r\n" . ".\r\n";
    $cformsSettings['form' . $no]['cforms' . $no . '_header_html'] = '<p ' . $cformsSettings['global']['cforms_style']['meta'] . '>' . __('A form has been submitted on {Date}, via: {Page} [IP {IP}]', 'cforms2') . '</p>';
    update_option('cforms_settings', $cformsSettings);
}
if (isset($_POST['cforms_resetAutoCMsg'])) {
    $cformsSettings['form' . $no]['cforms' . $no . '_cmsg'] = __('Dear {Your Name},', 'cforms2') . "\n" . __('Thank you for your note!', 'cforms2') . "\n" . __('We will get back to you as soon as possible.', 'cforms2') . "\n\n";
    $cformsSettings['form' . $no]['cforms' . $no . '_cmsg_html'] = '<div ' . $cformsSettings['global']['cforms_style']['autoconf'] . '><p ' . $cformsSettings['global']['cforms_style']['dear'] . '>' . __('Dear {Your Name},', 'cforms2') . "</p>\n<p " . $cformsSettings['global']['cforms_style']['confp'] . '>' . __('Thank you for your note!', 'cforms2') . "</p>\n<p " . $cformsSettings['global']['cforms_style']['confp'] . '>' . __('We will get back to you as soon as possible.', 'cforms2') . "\n<div " . $cformsSettings['global']['cforms_style']['confirmationmsg'] . '>' . __('This is an automatic confirmation message.', 'cforms2') . " {Date}.</div></div>\n\n";
    update_option('cforms_settings', $cformsSettings);
}

// delete field if we find one and move the rest up
$deletefound = 0;
if (strlen($cformsSettings['form' . $no]['cforms' . $no . '_count_field_' . $field_count]) > 0) {

    $temp_count = 1;
    while ($temp_count <= $field_count) {

        if (isset($_POST['DeleteField' . $temp_count])) {
            $deletefound = 1;
            $cformsSettings['form' . $no]['cforms' . $no . '_count_fields'] = ($field_count - 1);
        }

        if ($deletefound && $temp_count < $field_count) {
            $temp_val = $cformsSettings['form' . $no]['cforms' . $no . '_count_field_' . ($temp_count + 1)];
            $cformsSettings['form' . $no]['cforms' . $no . '_count_field_' . ($temp_count)] = $temp_val;
        }

        $temp_count++;
    }

    if ($deletefound == 1) {
        // now delete
        unset($cformsSettings['form' . $no]['cforms' . $no . '_count_field_' . $field_count]);
        $field_count--;
    }
    update_option('cforms_settings', $cformsSettings);
}


// prepare dropdown box for form selection
$formlistbox = ' <select id="pickform" name="pickform">';
for ($i = 1; $i <= $formcount; $i++) {
    $j = ( $i > 1 ) ? $i : '';
    $sel = ($no_disp == $i) ? ' selected="selected"' : '';
    $formlistbox .= '<option value="' . $i . '" ' . $sel . '>' . stripslashes($cformsSettings['form' . $j]['cforms' . $j . '_fname']) . '</option>';
}
$formlistbox .= '</select>';


// make sure at least the default FROM address is set
if ($cformsSettings['form' . $no]['cforms' . $no . '_fromemail'] == '') {
    $cformsSettings['form' . $no]['cforms' . $no . '_fromemail'] = '"' . get_option('blogname') . '" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>';
    update_option('cforms_settings', $cformsSettings);
}

// check if HTML needs to be enabled
$fd = $cformsSettings['form' . $no]['cforms' . $no . '_formdata'];
if (strlen($fd) <= 2) {
    $fd .= empty($cformsSettings['form' . $no]['cforms' . $no . '_header_html']) ? '0' : '1';
    $fd .= empty($cformsSettings['form' . $no]['cforms' . $no . '_cmsg_html']) ? '0' : '1';
    $cformsSettings['form' . $no]['cforms' . $no . '_formdata'] = $fd;
    update_option('cforms_settings', $cformsSettings);
}
?>

<div class="wrap" id="top">
    <h2><?php _e('Form Settings', 'cforms2') ?></h2>

    <form enctype="multipart/form-data" id="cformsdata" name="mainform" method="post" action="#">
        <table class="chgformbox" title="<?php _e('Navigate to your other forms.', 'cforms2') ?>">
            <tr>
                <td class="chgL">
                    <label for="pickform" class="bignumber navbar"><?php _e('Navigate to', 'cforms2') ?> </label>
                    <?php echo $formlistbox; ?><input type="submit" class="allbuttons go" id="go" name="go" value="<?php _e('Go', 'cforms2'); ?>"/>
                </td>
                <td class="chgM">
<?php
for ($i = 1; $i <= $formcount; $i++) {
    $j = ( $i > 1 ) ? $i : '';
    echo '<input title="' . stripslashes($cformsSettings['form' . $j]['cforms' . $j . '_fname']) . '" class="allbuttons chgbutton' . (($i != $no_disp) ? '' : 'hi') . '" type="submit" name="switchform" value="' . $i . '"/>';
}
?>
                </td>
            </tr>
        </table>
        <input type="hidden" name="no" value="<?php echo $no_disp; ?>"/>
        <input type="hidden" name="noSub" value="<?php echo $no_disp; ?>" />

        <p>
                    <?php _e('cformsII allows you to insert one or more customly designed contact forms, which sends the visitor info via email and optionally stores the feedback in the database.', 'cforms2'); ?>
                    <?php printf(__('<a href="%s" %s>Here</a> is a quick step by step quide to get you up and running quickly.', 'cforms2'), '?page=' . $plugindir . '/cforms-help.php#guide', ''); ?>
        </p>

        <table class="mainoptions">
            <tr>
                <td class="chgL">
                    <label for="cforms_fname" class="bignumber"><?php _e('Form Name', 'cforms2') ?></label>
                    <input id="cforms_fname" name="cforms_fname" class="cforms_fname" size="40" value="<?php echo stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_fname']); ?>" title="<?php _e('You may give each form an optional name to better identify incoming emails.', 'cforms2') ?>"/>
                    <input title="<?php _e('Enables or disables Ajax support for this form.', 'cforms2') ?>" id="cforms_ajax" type="checkbox" class="allchk cforms_ajax" name="cforms_ajax" <?php if ($cformsSettings['form' . $no]['cforms' . $no . '_ajax'] == "1") echo "checked=\"checked\""; ?>/>
                    <label title="<?php _e('Enables or disables Ajax support for this form.', 'cforms2') ?>" for="cforms_ajax" class="bignumber"><?php _e('Ajax enabled', 'cforms2') ?></label>
                </td>
            </tr>
        </table>

        <fieldset id="anchorfields" class="cf-content">

            <div>
<?php
echo sprintf(__('Please see the <strong>Help!</strong> section for information on how to deploy the various <a href="%s" %s>supported fields</a>,', 'cforms2'), '?page=' . $plugindir . '/cforms-help.php#fields', '') . ' ' .
 sprintf(__('set up forms using <a href="%s" %s>FIELDSETS</a>,', 'cforms2'), '?page=' . $plugindir . '/cforms-help.php#hfieldsets', '') .
 sprintf(__('use <a href="%s" %s>default values</a> &amp; <a href="%s" %s>regular expressions</a> for single &amp; multi-line fields. ', 'cforms2'), '?page=' . $plugindir . '/cforms-help.php#single', '', '?page=' . $plugindir . '/cforms-help.php#regexp', '') .
 sprintf(__('Besides the generic success &amp; failure messages below, you can add <a href="%s" %s>custom error messages</a>.', 'cforms2'), '?page=' . $plugindir . '/cforms-help.php#customerr', '');
?>
            </div>

            <div class="tableheader">
                <div id="cformswarning" class="dashicons-before dashicons-info" style="display:none"><?php _e('Please save the new order of fields (<em>Update Settings</em>)!', 'cforms2'); ?></div>
                <div>
                    <div class="fh1" title="<?php _e('Can be a simple label or a more complex expression. See Help!', 'cforms2'); ?>"><br /><span class="abbr"><?php _e('Field Name', 'cforms2'); ?></span></div>
                    <div class="fh2" title="<?php _e('Pick one of the supported input field types.', 'cforms2'); ?>"><br /><span class="abbr"><?php _e('Type', 'cforms2'); ?></span></div>
                    <div title="<?php _e('Makes an input field required for proper form validation.', 'cforms2'); ?>"><span class="dashicons dashicons-forms"></span><br /><?php _e('required', 'cforms2'); ?></div>
                    <div title="<?php _e('Makes the field required and verifies the email address.', 'cforms2'); ?>"><span class="dashicons dashicons-email-alt"></span><br /><?php _e('e-mail', 'cforms2'); ?></div>
                    <div title="<?php _e('Clears the field (default value) upon focus.', 'cforms2'); ?>"><span class="dashicons dashicons-editor-removeformatting"></span><br /><?php _e('auto-clear', 'cforms2'); ?></div>
                    <div title="<?php _e('Grey\'s out a form field (field will be completely disabled).', 'cforms2'); ?>"><span class="dashicons dashicons-dismiss"></span><br /><?php _e('disabled', 'cforms2'); ?></div>
                    <div title="<?php _e('Form field will be readonly!', 'cforms2'); ?>"><span class="dashicons dashicons-lock"></span><br /><?php _e('read-only', 'cforms2'); ?></div>
                </div>
            </div>

            <div id="allfields" class="groupWrapper">

<?php
$ti = 1;

// pre-check for verification field
$ccboxused = false;
$emailtoboxused = false;
$fileupload = false; // only for hide/show options

$alternate = ' ';
$fieldsadded = false;

for ($i = 1; $i <= $field_count; $i++) {
    $allfields[$i] = $cformsSettings['form' . $no]['cforms' . $no . '_count_field_' . $i];
    if (strpos($allfields[$i], 'emailtobox') !== false)
        $emailtoboxused = true;
    if (strpos($allfields[$i], 'ccbox') !== false)
        $ccboxused = true;
    if (strpos($allfields[$i], 'upload') !== false)
        $fileupload = true; // needed for config
}

$captchas = cforms2_get_pluggable_captchas();
$field_types = cforms2_get_fieldtypes();

for ($i = 1; $i <= $field_count; $i++) {

    $field_stat = explode('$#$', $allfields[$i]);

    // default values
    $field_name = __('New Field', 'cforms2');
    $field_type = 'textfield';
    $field_required = '0';
    $field_emailcheck = '0';
    $field_clear = '0';
    $field_disabled = '0';
    $field_readonly = '0';

    if (sizeof($field_stat) >= 3) {
        $field_name = stripslashes(htmlspecialchars($field_stat[0]));
        $field_type = $allfields[$i] = $field_stat[1];
        $field_required = $field_stat[2];
        $field_emailcheck = $field_stat[3];
        $field_clear = $field_stat[4];
        $field_disabled = $field_stat[5];
        $field_readonly = $field_stat[6];
    } elseif (sizeof($field_stat) == 1) {
        $cformsSettings['form' . $no]['cforms' . $no . '_count_field_' . $i] = __('New Field', 'cforms2') . '$#$textfield$#$0$#$0$#$0$#$0$#$0';
        $fieldsadded = true;
    }


    // convert old CAPTCHAs
    if ($field_type == 'verification')
        $field_type = 'cforms2_question_and_answer';

    switch ($field_type) {
        case 'emailtobox':
            $specialclass = 'style="background:#CBDDFE"';
            break;
        case 'ccbox':
            $specialclass = 'style="background:#D8FFCA"';
            break;
        case 'textonly':
            $specialclass = 'style="background:#E1EAE6"';
            break;
        case 'fieldsetstart':
        case 'fieldsetend':
            $specialclass = 'style="background:#ECFEA5"';
            break;
        default:
            $specialclass = '';
    }
    if (in_array($field_type, array_keys($captchas)))
        $specialclass = 'style="background:#D1B6E9"';

    $alternate = ($alternate == '') ? ' rowalt' : '';
    ?>

                    <div id="allfields=f<?php echo $i; ?>" class="groupItem<?php echo $alternate; ?>">

                        <div class="itemContent">

                            <span class="itemHeader<?php echo empty($alternate) ? '' : ' altmove'; ?>" title="<?php _e('Drag me', 'cforms2') ?>"><?php echo (($i < 10) ? '0' : '') . $i; ?></span>

                            <input tabindex="<?php echo $ti++ ?>" title="<?php _e('Please enter field definition', 'cforms2'); ?>" class="inpfld" <?php echo $specialclass; ?> name="field_<?php echo($i); ?>_name" id="field_<?php echo($i); ?>_name" size="30" value="<?php echo ($field_type == 'fieldsetend') ? '--' : $field_name; ?>" />

                    <?php
                    $out = '<span title="' . plugin_dir_url(__FILE__) . 'include/">';
                    $out .= '<input value="&#xF111;" type="button" class="wrench cf_editbox_button" title="' . __('Edit', 'cforms2') . '"/></span>';
                    $out .= '<select tabindex="' . ($ti++) . '" title="' . __('Pick a field type', 'cforms2') . '" class="fieldtype selfld" ' . $specialclass . ' name="field_' . $i . '_type" id="field_' . $i . '_type">';
                    $out .= '<optgroup label="' . __('----- General form fields ----', 'cforms2') . '">';
                    foreach ($field_types as $field_id => $field_type_desc) {
                        if ($field_type_desc->is_special())
                            continue;
                        $out .= $field_type_desc->render_form_option($field_type === $field_id, false);
                    }
                    $out .= '</optgroup>';


                    $out .= '<optgroup label="' . __('--------- Special ------------', 'cforms2') . '">';

                    $out .= $field_types['datepicker']->render_form_option($field_type === 'datepicker', $cformsSettings['global']['cforms_datepicker'] != '1');
                    $out .= $field_types['ccbox']->render_form_option($field_type === 'ccbox', $ccboxused && $field_type !== 'ccbox');
                    $out .= $field_types['emailtobox']->render_form_option($field_type === 'emailtobox', $emailtoboxused && $field_type !== 'emailtobox');

                    foreach ($captchas as $field_id => $field_type_desc) {
                        $out .= $field_type_desc->render_form_option($field_type === $field_id, strpos($allfields[$i], $field_id) !== false);
                    }

                    $out .= '</optgroup>';


                    $out .= '<optgroup label="' . __('--- HTML5 form fields ---', 'cforms2') . '">';
                    foreach ($field_types as $field_id => $field_type_desc) {
                        if ('html5' !== substr($field_id, 0, strlen('html5')))
                            continue;
                        $out .= $field_type_desc->render_form_option($field_type === $field_id, false);
                    }
                    $out .= '</optgroup></select>';

                    $out .= '<input tabindex="' . ($ti++) . '" ' . (($field_count <= 1) ? 'disabled="disabled"' : '') . ' class="' . (($field_count <= 1) ? 'noxbutton' : 'xbutton') . '" type="submit" name="DeleteField' . $i . '" value="&#xF153;" title="' . __('Remove input field', 'cforms2') . '" />';


                    $out .= '<input tabindex="' . ($ti++) . '" class="allchk fieldisreq chkfld" type="checkbox" title="' . __('input required', 'cforms2') . '" name="field_' . ($i) . '_required"' . ($field_required == '1' ? ' checked="checked"' : '');
                    if (in_array($field_type, array_merge(array_keys($captchas), array('hidden', 'checkboxgroup', 'fieldsetstart', 'fieldsetend', 'ccbox', 'textonly'))))
                        $out .= ' disabled="disabled"';
                    $out .= '/>';


                    $out .= '<input tabindex="' . ($ti++) . '" class="allchk fieldisemail chkfld" type="checkbox" title="' . __('email required', 'cforms2') . '" name="field_' . $i . '_emailcheck"' . ($field_emailcheck == '1' ? ' checked="checked"' : '');
                    if (!in_array($field_type, array('html5email', 'textfield', 'email')))
                        $out .= ' disabled="disabled"';
                    $out .= '/>';


                    $out .= '<input tabindex="' . ($ti++) . '" class="allchk fieldclear chkfld" type="checkbox" title="' . __('clear field', 'cforms2') . '" name="field_' . ($i) . '_clear"' . ($field_clear == '1' ? ' checked="checked"' : '');
                    if (!((strpos($field_type, 'tml5') !== false) || in_array($field_type, array('pwfield', 'textarea', 'textfield', 'datepicker'))))
                        $out .= ' disabled="disabled"';
                    $out .= '/>';


                    $out .= '<input tabindex="' . ($ti++) . '" class="allchk fielddisabled chkfld" type="checkbox" title="' . __('disabled', 'cforms2') . '" name="field_' . ($i) . '_disabled"' . ($field_disabled == '1' ? ' checked="checked"' : '');
                    if (!((strpos($field_type, 'tml5') !== false) || in_array($field_type, array('pwfield', 'textarea', 'textfield', 'datepicker', 'checkbox', 'checkboxgroup', 'selectbox', 'multiselectbox', 'radiobuttons', 'upload'))))
                        $out .= ' disabled="disabled"';
                    $out .= '/>';


                    $out .= '<input tabindex="' . ($ti++) . '" class="allchk fieldreadonly chkfld" type="checkbox" title="' . __('read-only', 'cforms2') . '" name="field_' . ($i) . '_readonly"' . ($field_readonly == '1' ? ' checked="checked"' : '');
                    if (!((strpos($field_type, 'tml5') !== false) || in_array($field_type, array('pwfield', 'textarea', 'textfield', 'datepicker', 'checkbox', 'checkboxgroup', 'selectbox', 'multiselectbox', 'radiobuttons', 'upload'))))
                        $out .= ' disabled="disabled"';
                    $out .= '/>';

                    echo $out;
                    ?>
                        </div>

                    </div>

                            <?php
                        }
                        if ($fieldsadded)
                            update_option('cforms_settings', $cformsSettings);
                        ?>
            </div>

            <p class="addfieldbox">
                <input tabindex="<?php echo $ti++; ?>" type="submit" name="AddField" class="allbuttons addbutton" title="<?php _e('Add more input field(s)', 'cforms2'); ?>" value="<?php _e('Add', 'cforms2'); ?>" />
                <input tabindex="<?php echo $ti++; ?>" type="text" name="AddFieldNo" value="1" class="addfieldno"/><?php _e('new field(s) @ position', 'cforms2'); ?>
                <select tabindex="<?php echo $ti++; ?>" name="AddFieldPos" class="addfieldno">
                <?php
                for ($i = 1; $i <= $field_count; $i++) {
                    echo '<option value="' . $i . '">' . $i . '</option>';
                }
                ?>
                </select>

                <input type="hidden" name="field_order" value="" />
                <input type="hidden" name="field_count_submit" value="<?php echo($field_count); ?>" />
            </p>

        </fieldset>


                    <?php if ($fileupload) { ?>
            <fieldset id="fileupload" class="cformsoptions">
                <div class="cflegend op-closed" id="p0" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
                    <a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindminus"></div><?php _e('File Upload Settings', 'cforms2') ?>
                </div>

                <div class="cf-content" id="o0">
                    <p>
    <?php printf(__('Configure and double-check these settings in case you are adding a "<code>File Upload Box</code>" to your form (also see the <a href="%s" %s>Help!</a> for further information).', 'cforms2'), '?page=' . $plugindir . '/cforms-help.php#upload', ''); ?>
    <?php printf(__('You may also want to verify the global, file upload specific  <a href="%s" %s>error messages</a>.', 'cforms2'), '?page=' . $plugindir . '/cforms-global-settings.php#upload', ''); ?>
                    </p>
                    <table class="form-table">
                        <tr class="ob space15">
                            <td class="obL"><label for="cforms_upload_ext"><strong><?php _e('Allowed file extensions', 'cforms2') ?></strong></label></td>
                            <td class="obR"><input type="text" id="cforms_upload_ext" name="cforms_upload_ext" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_upload_ext'])); ?>"/> <?php _e('[empty=no files are allowed]', 'cforms2') ?></td>
                        </tr>

                        <tr class="ob">
                            <td class="obL"><label for="cforms_upload_size"><strong><?php _e('Maximum file size<br />in kilobyte', 'cforms2') ?></strong></label></td>
                            <td class="obR"><input type="text" id="cforms_upload_size" name="cforms_upload_size" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_upload_size'])); ?>"/></td>
                        </tr>

                        <tr class="ob">
                            <td class="obL"><label for="cforms_noattachments"><strong><?php _e('Do not email attachments', 'cforms2') ?></strong></label></td>
                            <td class="obR"><input class="allchk" type="checkbox" id="cforms_noattachments" name="cforms_noattachments" <?php if ($cformsSettings['form' . $no]['cforms' . $no . '_noattachments'] == '1') echo "checked=\"checked\""; ?>/></td>
                        </tr>
                    </table>
                </div>
            </fieldset>
<?php } ?>


        <fieldset class="cformsoptions" id="anchormessage">
            <div class="cflegend op-closed" id="p1" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
                <a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindminus"></div><?php _e('Messages, Text and Button Label', 'cforms2') ?>
            </div>

            <div class="cf-content" id="o1">
                <p><?php printf(__('These are the messages displayed to the user on successful (or failed) form submission. These messages are form specific, a general message for entering a wrong <strong>visitor verification code</strong> can be found <a href="%s" %s>here</a>.', 'cforms2'), '?page=' . $plugindir . '/cforms-global-settings.php#visitorv', ''); ?></p>

                <table class="form-table">

                    <tr class="ob">
                        <td class="obL"><label for="cforms_submit_text"><strong><?php _e('Submit button text', 'cforms2'); ?></strong></label></td>
                        <td class="obR"><input type="text" name="cforms_submit_text" id="cforms_submit_text" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_submit_text'])); ?>" /></td>
                    </tr>

                    <tr class="ob space15">
                        <td class="obL"><label for="cforms_working"><strong><?php _e('Waiting message', 'cforms2'); ?></strong></label></td>
                        <td class="obR"><input type="text" name="cforms_working" id="cforms_working" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_working'])); ?>" /></td>
                    </tr>
                    <tr class="ob space15">
                        <td class="obL"><label for="cforms_required"><strong><?php _e('"required" label', 'cforms2'); ?></strong></label></td>
                        <td class="obR"><input type="text" name="cforms_required" id="cforms_required" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_required'])); ?>"/></td>
                    </tr>
                    <tr class="ob">
                        <td class="obL"><label for="cforms_emailrequired"><strong><?php _e('"email required" label', 'cforms2'); ?></strong></label></td>
                        <td class="obR"><input type="text" name="cforms_emailrequired" id="cforms_emailrequired" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_emailrequired'])); ?>"/></td>
                    </tr>

                    <tr class="ob space15">
                        <td class="obL"><label for="cforms_success"><?php _e('<strong>Success message</strong><br />when form filled out correctly', 'cforms2'); ?></label></td>
                        <td class="obR">
                            <table><tr>
                                    <td><textarea name="cforms_success" id="cforms_success"><?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_success'])); ?></textarea></td>
                                </tr></table>
                        </td>
                    </tr>

                    <tr class="ob">
                        <td class="obL"><label for="cforms_failure"><?php _e('<strong>Failure message</strong><br />when missing fields or wrong field<br />formats (regular expr.)', 'cforms2'); ?></label></td>
                        <td class="obR">
                            <table><tr>
                                    <td><textarea name="cforms_failure" id="cforms_failure" ><?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_failure'])); ?></textarea></td>
                                </tr></table>
                        </td>
                    </tr>
                    <tr class="ob space15">
                        <td class="obL"><label for="cforms_showposa"><strong><?php _e('Show messages', 'cforms2'); ?></strong></label></td>
                        <td class="obR">
                            <input class="allchk" type="checkbox" id="cforms_showposa" name="cforms_showposa" <?php if (substr($cformsSettings['form' . $no]['cforms' . $no . '_showpos'], 0, 1) == "y") echo "checked=\"checked\""; ?>/><label for="cforms_showposa"><?php _e('Above form', 'cforms2'); ?></label><br />
                            <input class="allchk" type="checkbox" id="cforms_showposb" name="cforms_showposb" <?php if (substr($cformsSettings['form' . $no]['cforms' . $no . '_showpos'], 1, 1) == "y") echo "checked=\"checked\""; ?>/><label for="cforms_showposb"><?php _e('Below form', 'cforms2'); ?></label>
                        </td>
                    </tr>
                    <tr class="ob">
                        <td class="obL"><label for="cforms_errorINS"><strong><?php _e('Embedded Custom Error<br />Messages', 'cforms2'); ?></strong></label></td>
                        <td class="obR">
                            <input class="allchk" type="checkbox" id="cforms_errorINS" name="cforms_errorINS" <?php if (substr($cformsSettings['form' . $no]['cforms' . $no . '_showpos'], 3, 1) == "y") echo "checked=\"checked\""; ?>/><label for="cforms_errorINS"><?php _e('Field specific error messages will be shown above input field', 'cforms2'); ?></label>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>


        <fieldset class="cformsoptions" id="anchoremail">
            <div class="cflegend op-closed" id="p2" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
                <a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindminus"></div><?php _e('Core Form Admin / Email Options', 'cforms2') ?>
            </div>

            <div class="cf-content" id="o2">
                <p><?php printf(__('These settings determine basic cforms behaviour and the form email recipient(s). Both %s and %s formats are valid, but check if your mail server does accept the format of choice!', 'cforms2'), '"<strong>xx@yy.zz</strong>"', '"<strong>abc &lt;xx@yy.zz&gt;</strong>"') ?></p>
                <p><?php _e('The default FROM: address is based on your blog\'s name and the WP default address. It can be changed, but I highly recommend you do not, as it may render the plugin useless. If you do change the FROM: address, triple check if all admin emails are being sent/received! ', 'cforms2') ?></p>

                <table class="form-table">

                    <tr class="ob">
                        <td class="obL"><strong><?php _e('Core options', 'cforms2') ?></strong></td>
                        <td class="obR">
                            <input class="allchk" type="checkbox" id="cforms_formaction" name="cforms_formaction" <?php if ($cformsSettings['form' . $no]['cforms' . $no . '_formaction']) echo "checked=\"checked\""; ?>/><label for="cforms_formaction"><?php printf(__('Disable %s multipart/form-data enctype %s, e.g. to enable salesforce.com', 'cforms2'), '<strong>', '</strong>'); ?></label>
                        </td>
                    </tr>

                    <tr class="ob space10">
                        <td class="obL"></td>
                        <td class="obR">
                            <input class="allchk" type="checkbox" id="cforms_dontclear" name="cforms_dontclear" <?php
if ($cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_form'])
    echo 'disabled="disabled"';
if ($cformsSettings['form' . $no]['cforms' . $no . '_dontclear'])
    echo "checked=\"checked\"";
?>/><label for="cforms_dontclear"><?php printf(__('%sDo not reset%s input fields after submission', 'cforms2'), '<strong>', '</strong>'); ?></label>
                        </td>
                    </tr>

<?php if ($cformsSettings['global']['cforms_database'] == '1') { ?>
                        <tr class="ob">
                            <td class="obL"></td>
                            <td class="obR"><input class="allchk" type="checkbox" id="cforms_notracking" name="cforms_notracking" <?php if ($cformsSettings['form' . $no]['cforms' . $no . '_notracking']) echo "checked=\"checked\""; ?>/><label for="cforms_notracking"><?php printf(__('%sExclude this form%s from database tracking', 'cforms2'), '<strong>', '</strong>') ?></label></td>
                        </tr>
                            <?php } ?>

                    <tr class="ob">
                        <td class="obL"></td>
                        <td class="obR"><input class="allchk" type="checkbox" id="cforms_customnames" name="cforms_customnames" <?php if ($cformsSettings['form' . $no]['cforms' . $no . '_customnames'] == '1') echo "checked=\"checked\""; ?>/><label for="cforms_customnames"><?php printf(__('Use %scustom input field NAMES &amp; ID\'s%s', 'cforms2'), '<strong>', '</strong>') ?></label> <a class="infobutton" href="#" name="it4"><?php _e('Read note &raquo;', 'cforms2'); ?></a></td>
                    </tr>

                    <tr id="it4" class="infotxt"><td>&nbsp;</td><td class="ex"><?php _e('This feature replaces the default NAMEs/IDs (e.g. <strong>cf_field_12</strong>) with <em>custom ones</em>, either derived from the field label you have provided or by specifically declaring it via <strong>[id:XYZ]</strong>,e.g. <em>Your Name[id:the-name]</em>. This will for instance help to feed data to third party systems (requiring certain input field names in the $_POST variable).', 'cforms2') ?></td></tr>

                    <tr class="ob">
                        <td class="obL"></td>
                        <td class="obR"><input class="allchk" type="checkbox" id="cforms_taftrick" name="cforms_taftrick" <?php if (substr($cformsSettings['form' . $no]['cforms' . $no . '_tellafriend'], 0, 1) === '3') echo "checked=\"checked\""; ?>/><label for="cforms_taftrick"><?php printf(__('%sExtra variables%s e.g. {Title}', 'cforms2'), '<strong>', '</strong>') ?></label> <a class="infobutton" href="#" name="it5"><?php _e('Read note &raquo;', 'cforms2'); ?></a></td>
                    </tr>

                    <tr id="it5" class="infotxt"><td>&nbsp;</td><td class="ex"><?php printf(__('There are <a href="%s" %s>three additional</a>, <em>predefined variables</em> that can be enabled here.', 'cforms2'), '?page=' . $plugindir . '/cforms-help.php#extravariables', ''); ?> <strong><u><?php _e('Note:', 'cforms2') ?></u></strong> <?php _e('This will add two more hidden fields to your form to ensure that all data is available also in AJAX mode.', 'cforms2') ?></td></tr>

                    <tr class="ob">
                        <td class="obL"></td>
                        <td class="obR">
                            <input class="allchk" type="checkbox" id="cforms_hide" name="cforms_hide" <?php if ($cformsSettings['form' . $no]['cforms' . $no . '_hide']) echo "checked=\"checked\""; ?>/><label for="cforms_hide"><?php printf(__('%sHide form%s after successful submission', 'cforms2'), '<strong>', '</strong>'); ?></label>
                        </td>
                    </tr>

                    <tr class="obSEP"><td colspan="2"></td></tr>

                    <tr class="ob">
                        <td class="obL" style="padding-top:7px"><strong><?php _e('Start Date', 'cforms2'); ?></strong></td>
<?php $date = explode(' ', stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_startdate']))); ?>
                        <td class="obR">
                            <input type="text" class="cf_date" id="cforms_startdate" name="cforms_startdate" placeholder="<?php echo cforms2_admin_date_format(); ?>" value="<?php echo $date[0]; ?>"/>
                            <input type="text" id="cforms_starttime" name="cforms_starttime" placeholder="<?php _e('HH:MM', 'cforms2'); ?>" value="<?php echo $date[1]; ?>" title="<?php _e('Time entry.', 'cforms2') ?>"/>
                            <label for="cforms_startdate"><?php
$dt = 'x';
if (strlen($cformsSettings['form' . $no]['cforms' . $no . '_startdate']) > 1) {
    $dt = human_time_diff(cforms2_make_time(stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_startdate'])));
    if ($dt > 0) {
        echo __('The form will be available in ', 'cforms2') . $dt;
    } else {
        echo __('The form is available now.', 'cforms2');
    }
}
?>
                            </label>
                        </td>
                    </tr>

                    <tr class="ob">
                        <td class="obL" style="padding-top:7px"><strong><?php _e('End Date', 'cforms2'); ?></strong></td>
                                <?php $date = explode(' ', stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_enddate']))); ?>
                        <td class="obR">
                            <input type="text" class="cf_date" id="cforms_enddate" name="cforms_enddate" placeholder="<?php echo cforms2_admin_date_format(); ?>" value="<?php echo $date[0]; ?>"/>
                            <input type="text" id="cforms_endtime" name="cforms_endtime" placeholder="<?php _e('HH:MM', 'cforms2'); ?>" value="<?php echo $date[1]; ?>" title="<?php _e('Time entry.', 'cforms2') ?>"/>
                            <label for="cforms_startdate"><?php
                                if ($dt == 'x' && strlen($cformsSettings['form' . $no]['cforms' . $no . '_enddate']) > 1) {
                                    $dt = human_time_diff(cforms2_make_time(stripslashes($cformsSettings['form' . $no]['cforms' . $no . '_enddate'])));
                                    if ($dt > 0) {
                                        echo __('The form will be available for another ', 'cforms2') . $dt;
                                    } else {
                                        echo __('The form is not available anymore.', 'cforms2');
                                    }
                                }
                                ?></label>
                        </td>
                    </tr>

                    <?php if (!empty($cformsSettings['form' . $no]['cforms' . $no . '_startdate']) || !empty($cformsSettings['form' . $no]['cforms' . $no . '_enddate'])) { ?>
                        <tr class="ob">
                            <td class="obL"><label for="cforms_limittxt"><strong><?php _e('Limit text', 'cforms2'); ?></strong></label></td>
                            <td class="obR"><table><tr><td><textarea name="cforms_limittxt" id="cforms_limittxt"><?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_limittxt'])); ?></textarea></td></tr></table></td>
                        </tr>
                    <?php } ?>

                <tr class="obSEP"><td colspan="2"></td></tr>

                <tr class="ob">
                    <td class="obL"><label for="cforms_redirect"><?php _e('<strong>Redirect</strong><br />options:', 'cforms2'); ?></label></td>
                    <td class="obR">
                        <input class="allchk" type="checkbox" id="cforms_redirect" name="cforms_redirect" <?php if ($cformsSettings['form' . $no]['cforms' . $no . '_redirect']) echo "checked=\"checked\""; ?>/><label for="cforms_redirect"><?php _e('Enable alternative success page (redirect)', 'cforms2'); ?></label><br />
                        <input name="cforms_redirect_page" id="cforms_redirect_page" value="<?php echo ($cformsSettings['form' . $no]['cforms' . $no . '_redirect_page']); ?>" />
                    </td>
                </tr>

                <tr class="obSEP"><td colspan="2"></td></tr>

                <tr class="ob">
                    <td class="obL"><label for="cforms_action"><?php _e('<strong>Send form data</strong><br /> to an alternative page:', 'cforms2'); ?></label></td>
                    <td class="obR">
                        <input class="allchk" type="checkbox" id="cforms_action" name="cforms_action" <?php if ($cformsSettings['form' . $no]['cforms' . $no . '_action']) echo "checked=\"checked\""; ?>/><label for="cforms_action"><?php _e('Enable alternative form action!', 'cforms2'); ?></label><br />
                        <input name="cforms_action_page" id="cforms_action_page" value="<?php echo ($cformsSettings['form' . $no]['cforms' . $no . '_action_page']); ?>" /> <a class="infobutton" href="#" name="it2"><?php _e('Please read note &raquo;', 'cforms2'); ?></a>
                    </td>
                </tr>

                <tr id="it2" class="infotxt"><td>&nbsp;</td><td class="ex"><?php _e('If you enable an alternative <strong>form action</strong> you <u>will loose any cforms application logic</u> (spam security, field validation, DB tracking etc.) in non-ajax mode! This setting is really only for developers that require additional capabilities around forwarding of form data and will turn cforms into a front-end only, a "form builder" so to speak.', 'cforms2') ?></td></tr>
                </table>
            </div>
        </fieldset>


        <fieldset class="cformsoptions" id="emailoptions">
            <div class="cflegend op-closed" id="p3" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
                <a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindminus"></div><?php _e('Admin Email Message Options', 'cforms2') ?>
            </div>

            <div class="cf-content" id="o3">
                <p><?php _e('Generally, emails sent to the admin and the submitting user can be both in plain text and HTML. The TXT part <strong>is required</strong>, HTML is <strong>optional</strong>.', 'cforms2'); ?></p>
                <p><?php printf(__('Below you\'ll find the settings for both the <strong>TXT part</strong> of the admin email as well as the <strong>optional HTML part</strong> of the message. Both areas permit the use of any of the <strong>pre-defined variables</strong> or <strong>data from input fields</strong>. <a href="%s" %s>Please see the documentation on the HELP page</a> (including HTML message examples!).', 'cforms2'), '?page=' . $plugindir . '/cforms-help.php#variables', ''); ?></p>

                <table class="form-table">
                <tr class="ob space15">
                    <td class="obL"></td>
                    <td class="obR"><input class="allchk" type="checkbox" id="cforms_emailoff" name="cforms_emailoff" <?php if ($cformsSettings['form' . $no]['cforms' . $no . '_emailoff'] == '1') echo "checked=\"checked\""; ?>/><label for="cforms_emailoff"><?php printf(__('%sTurn off%s admin email', 'cforms2'), '<strong>', '</strong>') ?></label></td>
                </tr>
                </table>

                <table class="form-table<?php if ($cformsSettings['form' . $no]['cforms' . $no . '_emailoff'] == '1') echo " hidden"; ?>">
                <tr class="">
                    <td class="obL"></td>
                    <td class="obR"><input class="allchk" type="checkbox" id="cforms_emptyoff" name="cforms_emptyoff" <?php if ($cformsSettings['form' . $no]['cforms' . $no . '_emptyoff'] == '1') echo "checked=\"checked\""; ?>/><label for="cforms_emptyoff"><?php printf(__('%sExclude empty fields%s from admin email', 'cforms2'), '<strong>', '</strong>') ?></label></td>
                </tr>

                <tr class="ob space15">
                    <td class="obL"><label for="cforms_fromemail"><strong><?php _e('FROM: email address', 'cforms2') ?></strong></label></td>
                    <td class="obR"><input type="text" name="cforms_fromemail" id="cforms_fromemail" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_fromemail'])); ?>" /></td>
                </tr>

                <tr class="ob space15">
                    <td class="obL"><label for="cforms_email"><strong><?php _e('Admin email address(es)', 'cforms2') ?></strong></label></td>
                    <td class="obR"><input type="text" name="cforms_email" id="cforms_email" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_email'])); ?>" /> <a class="infobutton" href="#" name="it1"><?php _e('More than one "<strong>form admin</strong>"? &raquo;', 'cforms2'); ?></a></td>
                </tr>

                <tr id="it1" class="infotxt"><td>&nbsp;</td><td class="ex"><?php printf(__('Simply add additional email addresses separated by a <strong style="color:red">comma</strong>. &nbsp; <em><u>Note:</u></em> &nbsp; If you want the visitor to choose from any of these per select box, you need to add a "<code>Multiple Recipients</code>" input field <a href="#anchorfields">above</a> (see the HELP section for <a href="%s" %s>more details</a>. If <strong>no</strong> "Multiple Recipients" input field is defined above, the submitted form data will go out to <strong>every address listed</strong>!', 'cforms2'), '?page=' . $plugindir . '/cforms-help.php#multirecipients', ''); ?></td></tr>

                <tr class="ob">
                    <td class="obL"><label for="cforms_bcc"><strong><?php _e('BCC', 'cforms2') ?></strong></label></td>
                    <td class="obR"><input type="text" name="cforms_bcc" id="cforms_bcc" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_bcc'])); ?>" /></td>
                </tr>
                <tr class="ob">
                    <td class="obL"><label for="cforms_subject"><strong><?php _e('Subject admin email', 'cforms2') ?></strong></label></td>
                    <td class="obR"><input type="text" name="cforms_subject" id="cforms_subject" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_subject'])); ?>" /> <?php printf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms2'), '?page=' . $plugindir . '/cforms-help.php#variables', ''); ?></td>
                </tr>

                <tr class="ob space20">
                    <td class="obL" style="padding-bottom:0">&nbsp;</td>
                    <td class="obR" style="padding-bottom:0">
                        <input type="submit" class="allbuttons" name="cforms_resetAdminMsg" id="cforms_resetAdminMsg" value="<?php _e('Reset admin message to default', 'cforms2') ?>" />
                    </td>
                </tr>

                <tr class="ob">
                    <td class="obL" style="padding-bottom:0">
                        <label for="cforms_header"><?php _e('<strong>Admin TEXT message</strong><br />(Header)', 'cforms2') ?></label>
                    </td>
                    <td class="obR" style="padding-bottom:0">
                        <table><tr>
                        <td><textarea name="cforms_header" id="cforms_header" ><?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_header'])); ?></textarea></td>
                        <td><?php printf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms2'), '?page=' . $plugindir . '/cforms-help.php#variables', ''); ?></td>
                        </tr></table>
                    </td>
                </tr>
                <tr class="ob">
                    <td class="obL" style="padding-top:0"><?php _e('(Footer)', 'cforms2') ?></td>
                    <td class="obR" style="padding-top:0"><input class="allchk" type="checkbox" id="cforms_formdata_txt" name="cforms_formdata_txt" <?php if (substr($cformsSettings['form' . $no]['cforms' . $no . '_formdata'], 0, 1) == '1') echo "checked=\"checked\""; ?>/><label for="cforms_formdata_txt"><?php _e('<strong>Include</strong> user input at the bottom of the admin email', 'cforms2') ?></label></td>
                </tr>
                <tr class="ob">
                    <td class="obL" style="padding-top:0">&nbsp;</td>
                    <td class="obR" style="padding-top:0"><input type="text" name="cforms_space" id="cforms_space" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_space'])); ?>" /><label for="cforms_space"><?php _e('(# characters) : spacing between labels &amp; data, for plain txt version only', 'cforms2') ?></label></td>
                </tr>

                <tr class="ob space20">
                    <td class="obL"><label for="cforms_admin_html"><strong><?php _e('Enable HTML', 'cforms2') ?></strong></label></td>
                    <td class="obR"><input class="allchk" type="checkbox" id="cforms_admin_html" name="cforms_admin_html" <?php $o = substr($cformsSettings['form' . $no]['cforms' . $no . '_formdata'], 2, 1) == '1';
                    if ($o) echo "checked=\"checked\""; ?>/></td>
                </tr>

                <tr class="ob <?php if (!$o == '1') echo "hidden"; ?>">
                    <td class="obL" style="padding-bottom:0"><label for="cforms_header_html"><?php _e('<strong>Admin HTML message</strong><br />(Header)', 'cforms2') ?></label></td>
                    <td class="obR" style="padding-bottom:0">
                        <table><tr>
                        <td><textarea name="cforms_header_html" id="cforms_header_html" ><?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_header_html'])); ?></textarea></td>
                        <td><?php printf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms2'), '?page=' . $plugindir . '/cforms-help.php#variables', ''); ?></td>
                        </tr></table>
                    </td>
                </tr>
                <tr class="ob <?php if (!$o == '1') echo "hidden"; ?>">
                    <td class="obL" style="padding-top:0"><?php _e('(Footer)', 'cforms2') ?></td>
                    <td class="obR" style="padding-top:0"><input class="allchk" type="checkbox" id="cforms_formdata_html" name="cforms_formdata_html" <?php if (substr($cformsSettings['form' . $no]['cforms' . $no . '_formdata'], 1, 1) == '1') echo "checked=\"checked\""; ?>/><label for="cforms_formdata_html"><?php _e('<strong>Include</strong> user input at the bottom of the admin email', 'cforms2') ?></label></td>
                </tr>
                <tr><td>&nbsp;</td><td><a class="infobutton" href="#" name="it3"><?php _e('\'Don\'t like the default form data block in your admin email?  &raquo;', 'cforms2'); ?></a></td></tr>
                <tr id="it3" class="infotxt"><td>&nbsp;</td><td class="ex"><strong><u><?php _e('Note:', 'cforms2') ?></u></strong> <?php _e('To avoid sending ALL of the submitted user data (especially for very long forms) to the form admin simply <strong>uncheck</strong> "<em>Include user input ...</em>" and instead specify the fields you\'d like to receive via the use of <strong>custom variables</strong>.', 'cforms2'); ?></td></tr>
                </table>
            </div>
        </fieldset>


        <fieldset class="cformsoptions <?php if (!$ccboxused) echo "hidden"; ?>" id="cc">
            <div class="cflegend op-closed" id="p4" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
                <a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindminus"></div><?php _e('CC Settings', 'cforms2') ?>
            </div>

            <div class="cf-content" id="o4">
                <p><?php _e('This is the subject of the CC email that goes out the user submitting the form and as such requires the <strong>CC:</strong> field in your form definition above.', 'cforms2') ?></p>

                <table class="form-table">
                <tr class="ob">
                    <td class="obL"><label for="cforms_ccsubject"><strong><?php _e('Subject CC', 'cforms2') ?></strong></label></td>
                    <td class="obR"><input type="text" name="cforms_ccsubject" id="cforms_ccsubject" value="<?php
                $t = explode('$#$', $cformsSettings['form' . $no]['cforms' . $no . '_csubject']);
                echo stripslashes(htmlspecialchars($t[1]));
                ?>" /> <?php printf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms2'), '?page=' . $plugindir . '/cforms-help.php#variables', ''); ?></td>
                </tr>
                </table>

            </div>
        </fieldset>


        <fieldset class="cformsoptions" id="autoconf">
            <div class="cflegend op-closed" id="p5" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
                <a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindminus"></div><?php _e('Auto Confirmation', 'cforms2') ?>
            </div>

            <div class="cf-content" id="o5">
                <p><?php _e('These settings apply to an auto response/confirmation sent to the visitor. If enabled AND your form contains a "<code>CC me</code>" field <strong>AND</strong> the visitor selected it, no extra confirmation email is sent!', 'cforms2') ?></p>

                <table class="form-table">
                <tr class="ob">
                    <td class="obL">&nbsp;</td>
                    <td class="obR">
                        <input class="allchk" type="checkbox" id="cforms_confirm" name="cforms_confirm" <?php $o = $cformsSettings['form' . $no]['cforms' . $no . '_confirm'] == "1";
            if ($o) echo "checked=\"checked\""; ?>/><label for="cforms_confirm"><strong><?php _e('Activate auto confirmation', 'cforms2') ?></strong></label><br />
                        <a class="infobutton" href="#" name="it8"><?php _e('Please read note &raquo;', 'cforms2'); ?></a>
                    </td>
                </tr>

                <tr id="it8" class="infotxt"><td>&nbsp;</td><td class="ex"><?php _e('For the <em>auto confirmation</em> feature to work, make sure to mark at least one field <code>Email</code>, otherwise <strong>NO</strong> auto confirmation email will be sent out! If multiple fields are checked "Email", only the first in the list will receive a notification.', 'cforms2') ?></td></tr>

                        <?php if ($o == "1") { ?>
                            <tr class="ob">
                                <td class="obL"><label for="cforms_csubject"><strong><?php _e('Subject auto confirmation', 'cforms2') ?></strong></label></td>
                                <td class="obR"><input type="text" name="cforms_csubject" id="cforms_csubject" value="<?php
                            $t = explode('$#$', $cformsSettings['form' . $no]['cforms' . $no . '_csubject']);
                            echo stripslashes(htmlspecialchars($t[0]));
                            ?>" /> <?php printf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms2'), '?page=' . $plugindir . '/cforms-help.php#variables', ''); ?></td>
                            </tr>
                            <tr class="ob space20">
                                <td class="obL" style="padding-bottom:0">&nbsp;</td>
                                <td class="obR" style="padding-bottom:0">
                                    <input type="submit" class="allbuttons" name="cforms_resetAutoCMsg" id="cforms_resetAutoCMsg" value="<?php _e('Reset auto confirmation message to default', 'cforms2') ?>" />
                                </td>
                            </tr>
                            <tr class="ob">
                                <td class="obL"><label for="cforms_cmsg"><strong><?php _e('TEXT message', 'cforms2') ?></strong></label></td>
                                <td class="obR">
                                    <table><tr>
                                    <td><textarea name="cforms_cmsg" id="cforms_cmsg" ><?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_cmsg'])); ?></textarea></td>
                                    <td><?php printf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms2'), '?page=' . $plugindir . '/cforms-help.php#variables', ''); ?></td>
                                    </tr></table>
                                </td>
                            </tr>
                            <tr class="ob space15">
                                <td class="obL"><label for="cforms_user_html"><strong><?php _e('Enable HTML', 'cforms2') ?></strong></label></td>
                                <td class="obR"><input class="allchk" type="checkbox" id="cforms_user_html" name="cforms_user_html" <?php $o2 = substr($cformsSettings['form' . $no]['cforms' . $no . '_formdata'], 3, 1) == '1'; if ($o2) echo "checked=\"checked\""; ?>/></td>
                            </tr>
                            <tr class="ob <?php if (!$o2 == '1') echo "hidden"; ?>">
                                <td class="obL"><label for="cforms_cmsg_html"><strong><?php _e('HTML message', 'cforms2') ?></strong></label></td>
                                <td class="obR">
                                    <table><tr>
                                    <td><textarea name="cforms_cmsg_html" id="cforms_cmsg_html" ><?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_cmsg_html'])); ?></textarea></td>
                                    <td><?php printf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms2'), '?page=' . $plugindir . '/cforms-help.php#variables', ''); ?></td>
                                    </tr></table>
                                </td>
                            </tr>

                            <?php
                                $a = $cformsSettings['form' . $no]['cforms' . $no . '_cattachment'][0];
                                $err = '';
                                $t = (substr($a, 0, 1) == '/') ? $a : plugin_dir_path(__FILE__) . $a;
                                if (!empty($t) && !file_exists($t)) {
                                    $err = '<br /><p class="error">' . sprintf(__('Can\'t find the specified <strong>Attachment</strong> (%s)! Please verify the server path!', 'cforms2'), $t) . '</p>';
                                }
                                ?>

                            <tr class="ob">
                                <td class="obL"><label for="cforms_cattachment"><strong><?php _e('Attachment', 'cforms2') ?></strong></label></td>
                                <td class="obR"><input type="text" name="cforms_cattachment" id="cforms_cattachment" value="<?php echo stripslashes(htmlspecialchars($a)); ?>" /> <?php printf(__('File path: relative to the cforms plugin folder or an absolute path.', 'cforms2')); ?><?php echo $err; ?></td>
                            </tr>
                    <?php } ?>

                </table>
            </div>
        </fieldset>


        <fieldset class="cformsoptions" id="multipart">
            <div class="cflegend op-closed" id="p29" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
                <a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindminus"></div><?php _e('Multi-part forms', 'cforms2') ?>
            </div>

            <div class="cf-content" id="o29">
                <p><?php _e('If enabled, new options will be shown below.', 'cforms2'); ?> <label for="cforms_mp_form"><?php _e('Mark this form to belong to a series of forms:', 'cforms2') ?></label> <input class="allchk" type="checkbox" id="cforms_mp_form" name="cforms_mp_form" <?php if ($cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_form'] == '1') echo "checked=\"checked\""; ?>/></p>

        <?php if ($cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_form']) { ?>

                    <table class="form-table">

                        <tr class="ob">
                            <td class="obL"><strong><?php _e('First Form', 'cforms2') ?></strong></td>
                            <td class="obR"><input class="allchk" type="checkbox" id="cforms_mp_first" name="cforms_mp_first" <?php if ($cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_first'] == '1') echo "checked=\"checked\""; ?>/><label for="cforms_mp_first"><?php _e('This is the *first* form of a series of forms', 'cforms2') ?></label></td>
                        </tr>

                        <tr class="obSEP"><td colspan="2"></td></tr>

                        <tr class="ob">
                            <td class="obL"><strong><?php _e('Reset Button', 'cforms2') ?></strong></td>
                            <td class="obR"><input class="allchk" type="checkbox" id="cforms_mp_reset" name="cforms_mp_reset" <?php if ($cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_reset'] == '1') echo "checked=\"checked\""; ?>/><label for="cforms_mp_reset"><?php _e('Add a reset button to this form (reset to the first form in a series)', 'cforms2') ?></label></td>
                        </tr>

                        <tr class="ob">
                            <td class="obL"><strong><?php _e('Text', 'cforms2') ?></strong></td>
                            <td class="obR"><input type="text" id="cforms_mp_resettext" name="cforms_mp_resettext" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_resettext'])); ?>"/><label for="cforms_mp_resettext"><?php _e('Text for reset button', 'cforms2') ?></label></td>
                        </tr>

                        <tr class="obSEP"><td colspan="2"></td></tr>

                        <tr class="ob">
                            <td class="obL"><strong><?php _e('Back Button', 'cforms2') ?></strong></td>
                            <td class="obR"><input class="allchk" type="checkbox" id="cforms_mp_back" name="cforms_mp_back" <?php if ($cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_back'] == '1') echo "checked=\"checked\""; ?>/><label for="cforms_mp_back"><?php _e('Add a back button to this form (back to the previous form)', 'cforms2') ?></label></td>
                        </tr>

                        <tr class="ob">
                            <td class="obL"><strong><?php _e('Text', 'cforms2') ?></strong></td>
                            <td class="obR"><input type="text" id="cforms_mp_backtext" name="cforms_mp_backtext" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_backtext'])); ?>"/><label for="cforms_mp_backtext"><?php _e('Text for back button', 'cforms2') ?></label></td>
                        </tr>

                        <tr class="obSEP"><td colspan="2"></td></tr>

                        <tr class="ob">
                            <td class="obL">&nbsp;</td>
                            <td class="obR">
                    <?php
                $formlistbox = ' <select id="picknextform" name="cforms_mp_next"' . ($cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_last'] == '1' ? ' disabled="disabled"' : '') . '>';
                for ($i = 1; $i <= $formcount; $i++) {
                    $j = ( $i > 1 ) ? $i : '';
                    $sel = ($cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_next'] == $cformsSettings['form' . $j]['cforms' . $j . '_fname']) ? ' selected="selected"' : '';
                    $formlistbox .= '<option ' . $sel . '>' . $cformsSettings['form' . $j]['cforms' . $j . '_fname'] . '</option>';
                }
                $formlistbox .= '<option style="background:#F2D7E0;" value="-1" ' . (($cformsSettings['form' . $no]['cforms' . $no . '_mp']['mp_next'] == '-1') ? ' selected="selected"' : '') . '>' . __('* stop here (last form) *', 'cforms2') . '</option>';
                $formlistbox .= '</select>';
                echo $formlistbox;
                ?>
                            <?php _e('Please choose the next form after this', 'cforms2') ?>
                            </td>
                        </tr>
                    </table>
                        <?php } ?>
            </div>
        </fieldset>

        <div class="cf_actions" id="cf_actions" style="display:none;">
            <input id="cfbar-addbutton" class="allbuttons addbutton" type="submit" name="addbutton" value=""/>
            <input id="cfbar-dupbutton" class="allbuttons dupbutton" type="submit" name="dupbutton" value=""/>
            <input id="cfbar-delbutton" class="allbuttons deleteall" type="submit" name="delbutton" value=""/>
            <input id="cfbar-SubmitOptions" type="submit" name="SubmitOptions" class="allbuttons updbutton formupd" value="" />
        </div>

    </form>

            <?php cforms2_footer(); ?>
</div>

<?php
add_action('admin_footer', 'cforms2_insert_modal');
