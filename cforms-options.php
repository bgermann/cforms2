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

global $wpdb;

require_once(plugin_dir_path(__FILE__) . 'lib_functions.php');

$cformsSettings = get_option('cforms_settings');

$plugindir   = dirname(plugin_basename(__FILE__));

### Check Whether User Can Manage Database
cforms2_check_access_priv();

### if all data has been erased quit
if ( cforms2_check_erased() )
	return;


### default to 1 & get real #
$FORMCOUNT=$cformsSettings['global']['cforms_formcount'];

if(isset($_REQUEST['addbutton'])){
	require_once(plugin_dir_path(__FILE__) . 'lib_options_add.php');

} elseif(isset($_REQUEST['dupbutton'])) {
	require_once(plugin_dir_path(__FILE__) . 'lib_options_dup.php');

} elseif( isset($_REQUEST['uploadcformsdata']) ) {
	require_once(plugin_dir_path(__FILE__) . 'lib_options_up.php');

} elseif(isset($_REQUEST['delbutton']) && $FORMCOUNT>1) {
	require_once(plugin_dir_path(__FILE__) . 'lib_options_del.php');

} else {

	### set paramters to default, if not exists
	$noDISP='1';$no='';
	if( isset($_REQUEST['switchform']) ) { ### only set when hitting form chg buttons
		if( $_REQUEST['switchform']<>'1' )
			$noDISP = $no = $_REQUEST['switchform'];
	}
	else if( isset($_REQUEST['go']) ) { ### only set when hitting form chg buttons
		if( $_REQUEST['pickform']<>'1' )
			$noDISP = $no = $_REQUEST['pickform'];
	}
	else{
		if( isset($_REQUEST['noSub']) && (int)$_REQUEST['noSub']>1 ) ### otherwise stick with the current form
			$noDISP = $no = $_REQUEST['noSub'];
	}

}

### PRESETS
if ( isset($_REQUEST['formpresets']) )
	require_once(plugin_dir_path(__FILE__) . 'lib_options_presets.php');


### default: $field_count = what's in the DB
$field_count = $cformsSettings['form'.$no]['cforms'.$no.'_count_fields'];


### Update Settings
if( isset($_REQUEST['SubmitOptions']) || isset($_REQUEST['AddField']) || array_search("X", $_REQUEST) ){
	require_once(plugin_dir_path(__FILE__) . 'lib_options_sub.php');
}


### Reset Admin and AutoConf messages
if( isset($_REQUEST['cforms_resetAdminMsg']) ) {
	$cformsSettings['form'.$no]['cforms'.$no.'_header'] = __('A new submission (form: "{Form Name}")', 'cforms2') . "\r\n============================================\r\n" . __('Submitted on: {Date}', 'cforms2') . "\r\n" . __('Via: {Page}', 'cforms2') . "\r\n" . __('By {IP} (visitor IP)', 'cforms2') . ".\r\n" . ".\r\n";
	$cformsSettings['form'.$no]['cforms'.$no.'_header_html'] = '<p '.$cformsSettings['global']['cforms_style']['meta'].'>' . __('A form has been submitted on {Date}, via: {Page} [IP {IP}]', 'cforms2') . '</p>';
	update_option('cforms_settings',$cformsSettings);
}
if( isset($_REQUEST['cforms_resetAutoCMsg']) ) {
	$cformsSettings['form'.$no]['cforms'.$no.'_cmsg'] = __('Dear {Your Name},', 'cforms2') . "\n" . __('Thank you for your note!', 'cforms2') . "\n". __('We will get back to you as soon as possible.', 'cforms2') . "\n\n";
	$cformsSettings['form'.$no]['cforms'.$no.'_cmsg_html'] = '<div '.$cformsSettings['global']['cforms_style']['autoconf'].'><p '.$cformsSettings['global']['cforms_style']['dear'] .'>'. __('Dear {Your Name},', 'cforms2') . "</p>\n<p ". $cformsSettings['global']['cforms_style']['confp'].'>'. __('Thank you for your note!', 'cforms2') . "</p>\n<p ".$cformsSettings['global']['cforms_style']['confp'].'>'. __('We will get back to you as soon as possible.', 'cforms2') . "\n<div ".$cformsSettings['global']['cforms_style']['confirmationmsg'].'>'.__('This is an automatic confirmation message.', 'cforms2')." {Date}.</div></div>\n\n";
	update_option('cforms_settings',$cformsSettings);
}

### delete field if we find one and move the rest up
$deletefound = 0;
if(strlen($cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . $field_count]) > 0) {

	$temp_count = 1;
	while($temp_count <= $field_count) {

		if(isset($_REQUEST['DeleteField' . $temp_count])) {
			$deletefound = 1;
			$cformsSettings['form'.$no]['cforms'.$no.'_count_fields'] = ($field_count - 1);
		}

		if($deletefound && $temp_count<$field_count) {
			$temp_val = $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . ($temp_count+1)];
			$cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . ($temp_count)] = $temp_val;
		}

		$temp_count++;
	} ### while

	if($deletefound == 1) {  ### now delete
	  	unset( $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . $field_count] );
		$field_count--;
	}
    update_option('cforms_settings',$cformsSettings);
} ### if


### check possible errors
require_once(plugin_dir_path(__FILE__) . 'lib_options_err.php');


###
### prep drop down box for form selection
###
$formlistbox = ' <select id="pickform" name="pickform">';
for ($i=1; $i<=$FORMCOUNT; $i++){
	$j   = ( $i > 1 )?$i:'';
	$sel = ($noDISP==$i)?' selected="selected"':'';
	$formlistbox .= '<option value="'.$i.'" '.$sel.'>'.stripslashes($cformsSettings['form'.$j]['cforms'.$j.'_fname']).'</option>';
}
$formlistbox .= '</select>';


### make sure at least the default FROM: address is set
if ( $cformsSettings['form'.$no]['cforms'.$no.'_fromemail'] == '' ){
	$cformsSettings['form'.$no]['cforms'.$no.'_fromemail'] = '"'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>';
    update_option('cforms_settings',$cformsSettings);
}

### check if HTML needs to be enabled
$fd = $cformsSettings['form'.$no]['cforms'.$no.'_formdata'];
if( strlen($fd)<=2 ) {
	$fd .= ( $cformsSettings['form'.$no]['cforms'.$no.'_header_html']<>''  )?'1':'0';
	$fd .= ( $cformsSettings['form'.$no]['cforms'.$no.'_cmsg_html']<>'' )?'1':'0';
	$cformsSettings['form'.$no]['cforms'.$no.'_formdata'] = $fd;
    update_option('cforms_settings',$cformsSettings);
}

?>

<div class="wrap" id="top">
	<div id="icon-cforms-settings" class="icon32"><br/></div><h2><?php _e('Form Settings', 'cforms2')?></h2>

	<form enctype="multipart/form-data" id="cformsdata" name="mainform" method="post" action="#">
		<table class="chgformbox" title="<?php _e('Navigate to your other forms.', 'cforms2') ?>">
		<tr>
            <td class="chgL">
            	<label for="pickform" class="bignumber navbar"><?php _e('Navigate to', 'cforms2') ?> </label>
                <?php echo $formlistbox; ?><input type="submit" class="allbuttons go" id="go" name="go" value="<?php _e('Go', 'cforms2');?>"/>
            </td>
            <td class="chgM">
                <?php
                for ($i=1; $i<=$FORMCOUNT; $i++) {
                    $j   = ( $i > 1 )?$i:'';
                    echo '<input title="'.stripslashes($cformsSettings['form'.$j]['cforms'.$j.'_fname']).'" class="allbuttons chgbutton'.(($i <> $noDISP)?'':'hi').'" type="submit" name="switchform" value="'.$i.'"/>';
                }
                ?>
        	</td>
			</tr>
        </table>
		<input type="hidden" name="no" value="<?php echo $noDISP; ?>"/>
		<input type="hidden" name="noSub" value="<?php echo $noDISP; ?>" />

	    <p>
	        <?php echo sprintf(__('<strong>cforms</strong> allows you <a href="%s" %s>to insert</a> one or more custom designed contact forms, which on submission (preferably via Ajax) will send the visitor info via email and optionally stores the feedback in the database.', 'cforms2'),'?page='. $plugindir.'/cforms-help.php#inserting','onclick="setshow(18)"'); ?>
	        <?php echo sprintf(__('<a href="%s" %s>Here</a> is a quick step by step quide to get you up and running quickly.', 'cforms2'),'?page='. $plugindir.'/cforms-help.php#guide','onclick="setshow(17)"'); ?>
	    </p>

		<table class="mainoptions">
		<tr>
			<td class="chgL">
            	<label for="cforms_fname" class="bignumber"><?php _e('Form Name', 'cforms2') ?></label>
				<input id="cforms_fname" name="cforms_fname" class="cforms_fname" size="40" value="<?php echo stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_fname']);  ?>" title="<?php _e('You may give each form an optional name to better identify incoming emails.', 'cforms2') ?>"/>
				<input title="<?php _e('Enables or disables Ajax support for this form.', 'cforms2') ?>" id="cforms_ajax" type="checkbox" class="allchk cforms_ajax" name="cforms_ajax" <?php if($cformsSettings['form'.$no]['cforms'.$no.'_ajax']=="1") echo "checked=\"checked\""; ?>/>
				<label title="<?php _e('Enables or disables Ajax support for this form.', 'cforms2') ?>" for="cforms_ajax" class="bignumber"><?php _e('Ajax enabled', 'cforms2') ?></label>
			</td>
        </tr>
        </table>

	<fieldset id="anchorfields" class="cf-content">

		<div>
			<?php echo sprintf(__('Please see the <strong>Help!</strong> section for information on how to deploy the various <a href="%s" %s>supported fields</a>,', 'cforms2'),'?page='.$plugindir.'/cforms-help.php#fields','onclick="setshow(19)"') . ' ' .
					   sprintf(__('set up forms using <a href="%s" %s>FIELDSETS</a>,', 'cforms2'), '?page='.$plugindir.'/cforms-help.php#hfieldsets','onclick="setshow(19)"') .
					   sprintf(__('use <a href="%s" %s>default values</a> &amp; <a href="%s" %s>regular expressions</a> for single &amp; multi-line fields. ', 'cforms2'),'?page='.$plugindir.'/cforms-help.php#single','onclick="setshow(19)"','?page='.$plugindir.'/cforms-help.php#regexp','onclick="setshow(19)"') .
					   sprintf(__('Besides the generic success &amp; failure messages below, you can add <a href="%s" %s>custom error messages</a>.', 'cforms2'),'?page='.$plugindir.'/cforms-help.php#customerr','onclick="setshow(20)"'); ?>
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

                    ### pre-check for verification field
                    $ccboxused=false;
                    $emailtoboxused=false;
                    $fileupload=false; ### only for hide/show options

                    $alternate=' ';
                    $fieldsadded = false;

                    for($i = 1; $i <= $field_count; $i++) {
                            $allfields[$i] = $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . $i];
                            if ( strpos($allfields[$i],'emailtobox')!==false )      $emailtoboxused = true;
                            if ( strpos($allfields[$i],'ccbox')!==false )           $ccboxused = true;
                            if ( strpos($allfields[$i],'upload')!==false )          $fileupload = true; //needed for config
                    }
					
					$captchas = cforms2_get_pluggable_captchas();

                    for($i = 1; $i <= $field_count; $i++) {

                        $field_stat = explode('$#$', $allfields[$i] );

                        ### default vals
                        $field_name = __('New Field', 'cforms2');
                        $field_type = 'textfield';
                        $field_required = '0';
                        $field_emailcheck = '0';
                        $field_clear = '0';
                        $field_disabled = '0';
                        $field_readonly = '0';

                        if(sizeof($field_stat) >= 3) {
                            $field_name = stripslashes(htmlspecialchars($field_stat[0]));
                            $field_type = $allfields[$i] = $field_stat[1];
                            $field_required = $field_stat[2];
                            $field_emailcheck = $field_stat[3];
                            $field_clear = $field_stat[4];
                            $field_disabled = $field_stat[5];
                            $field_readonly = $field_stat[6];
                        }
                        else if(sizeof($field_stat) == 1){
                            $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . $i] = __('New Field', 'cforms2').'$#$textfield$#$0$#$0$#$0$#$0$#$0';
                            $fieldsadded = true;
                        }


						// convert old CAPTCHAs
						if ($field_type == 'verification')
							$field_type = 'cforms2_question_and_answer';

                    	switch ( $field_type ) {
	                       case 'emailtobox':   $specialclass = 'style="background:#CBDDFE"'; break;
	                        case 'ccbox':       $specialclass = 'style="background:#D8FFCA"'; break;
	                        case 'textonly':    $specialclass = 'style="background:#E1EAE6"'; break;
	                        case 'fieldsetstart':
	                        case 'fieldsetend': $specialclass = 'style="background:#ECFEA5"'; break;
	                        default:            $specialclass = ''; break;
                        }
						if (in_array($field_type, array_keys($captchas)))
							$specialclass = 'style="background:#D1B6E9"';

                    	$alternate = ($alternate=='')?' rowalt':''; ?>

                    	<div id="allfields=f<?php echo $i; ?>" class="groupItem<?php echo $alternate; ?>">

                        	<div class="itemContent">

	                            <span class="itemHeader<?php echo ($alternate<>'')?' altmove':''; ?>" title="<?php _e('Drag me', 'cforms2')?>"><?php echo (($i<10)?'0':'').$i; ?></span>

	                            <input tabindex="<?php echo $ti++ ?>" title="<?php _e('Please enter field definition', 'cforms2'); ?>" class="inpfld" <?php echo $specialclass; ?> name="field_<?php echo($i); ?>_name" id="field_<?php echo($i); ?>_name" size="30" value="<?php echo ($field_type == 'fieldsetend')?'--':$field_name; ?>" /><span title="<?php echo plugin_dir_url(__FILE__).'include/'; ?>"><input value="&#xF111;" type="submit" onfocus="this.blur()" class="wrench jqModal" title="<?php _e('Edit', 'cforms2'); ?>"/></span><select tabindex="<?php echo $ti++ ?>" title="<?php _e('Pick a field type', 'cforms2'); ?>" class="fieldtype selfld" <?php echo $specialclass; ?> name="field_<?php echo($i); ?>_type" id="field_<?php echo($i); ?>_type">

								<optgroup label="<?php _e('----- General form fields ----', 'cforms2'); ?>">
									<option value="fieldsetstart" <?php echo($field_type == 'fieldsetstart'?' selected="selected"':''); ?>><?php _e('Begin Fieldset', 'cforms2'); ?></option>
									<option value="fieldsetend" <?php echo($field_type == 'fieldsetend'?' selected="selected"':''); ?>><?php _e('End Fieldset', 'cforms2'); ?></option>
									<option value="textonly" <?php echo($field_type == 'textonly'?' selected="selected"':''); ?>><?php _e('Text only (no input)', 'cforms2'); ?></option>
									<option value="textfield" <?php echo($field_type == 'textfield'?' selected="selected"':''); ?>><?php _e('Single line of text', 'cforms2'); ?></option>
									<option value="textarea" <?php echo($field_type == 'textarea'?' selected="selected"':''); ?>><?php _e('Multiple lines of text', 'cforms2'); ?></option>
									<option value="checkbox" <?php echo($field_type == 'checkbox'?' selected="selected"':''); ?>><?php _e('Check Box', 'cforms2'); ?></option>
									<option value="checkboxgroup" <?php echo($field_type == 'checkboxgroup'?' selected="selected"':''); ?>><?php _e('Check Box Group', 'cforms2'); ?></option>
									<option value="radiobuttons" <?php echo($field_type == 'radiobuttons'?' selected="selected"':''); ?>><?php _e('Radio Buttons', 'cforms2'); ?></option>
									<option value="selectbox" <?php echo($field_type == 'selectbox'?' selected="selected"':''); ?>><?php _e('Select Box', 'cforms2'); ?></option>
									<option value="multiselectbox" <?php echo($field_type == 'multiselectbox'?' selected="selected"':''); ?>><?php _e('Multi Select Box', 'cforms2'); ?></option>
									<option value="upload" <?php echo($field_type == 'upload'?' selected="selected"':''); ?>><?php _e('File Upload Box', 'cforms2'); ?></option>
                                
									<option<?php if ( $cformsSettings['global']['cforms_datepicker']<>'1' ) echo ' disabled="disabled" class="disabled"'; ?> value="datepicker" <?php echo($field_type == 'datepicker'?' selected="selected"':''); ?>><?php _e('Date Entry/Dialog', 'cforms2'); ?></option>
									<option value="pwfield" <?php echo($field_type == 'pwfield'?' selected="selected"':''); ?>><?php _e('Password Field', 'cforms2'); ?></option>
									<option value="hidden" <?php echo($field_type == 'hidden'?' selected="selected"':''); ?>><?php _e('Hidden Field', 'cforms2'); ?></option>
								</optgroup>

								<optgroup label="<?php _e('--------- Special ------------', 'cforms2'); ?>">
									<option<?php if ( $ccboxused && $field_type<>"ccbox" ) echo ' disabled="disabled" class="disabled"'; ?> value="ccbox" <?php echo($field_type == 'ccbox'?' selected="selected"':''); ?>><?php _e('CC: option for user', 'cforms2'); ?></option>
									<option<?php if ( $emailtoboxused && $field_type<>"emailtobox" ) echo ' disabled="disabled" class="disabled"'; ?>  value="emailtobox" <?php echo($field_type == 'emailtobox'?' selected="selected"':''); ?>><?php _e('Multiple Recipients', 'cforms2'); ?></option>
									<?php
										$out = '';
										foreach ($captchas as $captcha_id => $captcha) {
											$out .= '<option';
											if ( $field_type == $captcha_id )
												$out .= ' selected="selected"';
											elseif (strpos($allfields[$i], $captcha_id) !== false)
												// This CAPTCHA type is already used
												$out .= ' disabled="disabled" class="disabled"';
											
											$out .= ' value="'. $captcha_id .'">';
											$out .= $captcha->get_name();
											$out .= '</option>';
										}
										echo $out;
									?>
								</optgroup>

								<optgroup label="<?php _e('--- HTML5 form fields ---', 'cforms2'); ?>">
									<option value="html5color" <?php echo($field_type == 'html5color'?' selected="selected"':''); ?>>html5&nbsp;<?php _e('Color Field', 'cforms2'); ?></option>
									<option value="html5date" <?php echo($field_type == 'html5date'?' selected="selected"':''); ?>>html5&nbsp;<?php _e('Date Field', 'cforms2'); ?></option>
									<option value="html5datetime" <?php echo($field_type == 'html5datetime'?' selected="selected"':''); ?>>html5&nbsp;<?php _e('Date/Time Field', 'cforms2'); ?></option>
									<option value="html5datetime-local" <?php echo($field_type == 'html5datetime-local'?' selected="selected"':''); ?>>html5&nbsp;<?php _e('Date/Time (local) Field', 'cforms2'); ?></option>
									<option value="html5email" <?php echo($field_type == 'html5email'?' selected="selected"':''); ?>>html5&nbsp;<?php _e('Email Field', 'cforms2'); ?></option>
									<option value="html5month" <?php echo($field_type == 'html5month'?' selected="selected"':''); ?>>html5&nbsp;<?php _e('Month Field', 'cforms2'); ?></option>
									<option value="html5number" <?php echo($field_type == 'html5number'?' selected="selected"':''); ?>>html5&nbsp;<?php _e('Number Field', 'cforms2'); ?></option>
									<option value="html5range" <?php echo($field_type == 'html5range'?' selected="selected"':''); ?>>html5&nbsp;<?php _e('Range Field', 'cforms2'); ?></option>
									<option value="html5search" <?php echo($field_type == 'html5search'?' selected="selected"':''); ?>>html5&nbsp;<?php _e('Search Field', 'cforms2'); ?></option>
									<option value="html5tel" <?php echo($field_type == 'html5tel'?' selected="selected"':''); ?>>html5&nbsp;<?php _e('Telephone Number Field', 'cforms2'); ?></option>
									<option value="html5time" <?php echo($field_type == 'html5time'?' selected="selected"':''); ?>>html5&nbsp;<?php _e('Time Field', 'cforms2'); ?></option>
									<option value="html5url" <?php echo($field_type == 'html5url'?' selected="selected"':''); ?>>html5&nbsp;<?php _e('URL Field', 'cforms2'); ?></option>
									<option value="html5week" <?php echo($field_type == 'html5week'?' selected="selected"':''); ?>>html5&nbsp;<?php _e('Week Field', 'cforms2'); ?></option>
								</optgroup>
                            	</select><?php

                            echo '<input tabindex="'.($ti++).'" '.(($field_count<=1)?'disabled="disabled"':'').' class="'.(($field_count<=1)?'noxbutton':'xbutton').'" type="submit" name="DeleteField'.$i.'" value="&#xF153;" title="'.__('Remove input field', 'cforms2').'" onfocus="this.blur()"/>';

                            
                            echo '<input tabindex="'.($ti++).'" class="allchk fieldisreq chkfld" type="checkbox" title="'.__('input required', 'cforms2').'" name="field_'.($i).'_required"'.($field_required == '1'?' checked="checked"':'');
							if ( in_array($field_type,array_merge(array_keys($captchas), array('hidden','checkboxgroup', 'fieldsetstart','fieldsetend','ccbox','textonly'))) )
									echo ' disabled="disabled"';
							echo '/>';


                            echo '<input tabindex="'.($ti++).'" class="allchk fieldisemail chkfld" type="checkbox" title="'.__('email required', 'cforms2').'" name="field_'.($i).'_emailcheck"'.($field_emailcheck == '1'?' checked="checked"':'');
                            if( ! in_array($field_type,array('html5email','textfield','email')) )
                                echo ' disabled="disabled"';
							echo '/>';


                            echo '<input tabindex="'.($ti++).'" class="allchk fieldclear chkfld" type="checkbox" title="'.__('clear field', 'cforms2').'" name="field_'.($i).'_clear"'.($field_clear == '1'?' checked="checked"':'');
                            if( ! ((strpos($field_type, 'tml5')!==false) || in_array($field_type,array('pwfield','textarea','textfield','datepicker'))) )
                                echo ' disabled="disabled"';
							echo '/>';


                            echo '<input tabindex="'.($ti++).'" class="allchk fielddisabled chkfld" type="checkbox" title="'.__('disabled', 'cforms2').'" name="field_'.($i).'_disabled"'.($field_disabled == '1'?' checked="checked"':'');
                            if( ! ((strpos($field_type, 'tml5')!==false) || in_array($field_type,array('pwfield','textarea','textfield','datepicker','checkbox','checkboxgroup','selectbox','multiselectbox','radiobuttons','upload'))) )
                                echo ' disabled="disabled"';
							echo '/>';


                            echo '<input tabindex="'.($ti++).'" class="allchk fieldreadonly chkfld" type="checkbox" title="'.__('read-only', 'cforms2').'" name="field_'.($i).'_readonly"'.($field_readonly == '1'?' checked="checked"':'');
                            if( ! ((strpos($field_type, 'tml5')!==false) || in_array($field_type,array('pwfield','textarea','textfield','datepicker','checkbox','checkboxgroup','selectbox','multiselectbox','radiobuttons','upload'))) )
                                echo ' disabled="disabled"';
							echo '/>';

                        ?></div> <!--itemContent-->

                    </div> <!--groupItem-->

            <?php   }   ### for loop
                    if( $fieldsadded )
                        update_option('cforms_settings',$cformsSettings);
            ?>
		</div> <!--groupWrapper-->

		<p class="addfieldbox">
            <input tabindex="<?php echo $ti++;?>" type="submit" name="AddField" class="allbuttons addbutton" title="<?php _e('Add more input field(s)', 'cforms2'); ?>" value="<?php _e('Add', 'cforms2'); ?>" onfocus="this.blur()" onclick="javascript:document.mainform.action='#anchorfields';" />
        	<input tabindex="<?php echo $ti++;?>" type="text" name="AddFieldNo" value="1" class="addfieldno"/><?php _e('new field(s) @ position', 'cforms2'); ?>
			<select tabindex="<?php echo $ti++;?>" name="AddFieldPos" class="addfieldno">
			<?php
	            for($i = 1; $i <= $field_count; $i++) {
    	        	echo '<option value="'.$i.'">'.$i.'</option>';
                }
			?>
            </select>

	        <input type="hidden" name="field_order" value="" />
	        <input type="hidden" name="field_count_submit" value="<?php echo($field_count); ?>" />
        </p>

	</fieldset>


    <?php if( $fileupload) : ?>
	<fieldset id="fileupload" class="cformsoptions">
			<div class="cflegend op-closed" id="p0" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('File Upload Settings', 'cforms2')?>
            </div>

			<div class="cf-content" id="o0">
				<p>
					<?php echo sprintf(__('Configure and double-check these settings in case you are adding a "<code>File Upload Box</code>" to your form (also see the <a href="%s" %s>Help!</a> for further information).', 'cforms2'),'?page='.$plugindir.'/cforms-help.php#upload','onclick="setshow(19)"'); ?>
					<?php echo sprintf(__('You may also want to verify the global, file upload specific  <a href="%s" %s>error messages</a>.', 'cforms2'),'?page='.$plugindir.'/cforms-global-settings.php#upload','onclick="setshow(11)"'); ?>
				</p>

			    <?php
			    $temp = explode( '$#$',stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_upload_dir'])) );
			    $fileuploaddir = $temp[0];
			    $fileuploaddirurl = $temp[1];
				if ( $fileupload && !file_exists($fileuploaddir) ) {
			        echo '<div class="updated fade"><p>' . __('Can\'t find the specified <strong>Upload Directory</strong> ! Please verify that it exists!', 'cforms2' ) . '</p></div>';
			    }
				?>
				<table class="form-table">
				<tr class="ob space15">
					<td class="obL"><label for="cforms_upload_dir"><strong><?php _e('Upload directory (absolute path)', 'cforms2') ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_upload_dir" name="cforms_upload_dir" value="<?php echo $fileuploaddir; ?>"/> <?php _e('e.g. /home/user/www/wp-content/my-upload-dir', 'cforms2') ?></td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_upload_dir_url"><strong><?php _e('Upload directory URL (relative path/URL)', 'cforms2') ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_upload_dir_url" name="cforms_upload_dir_url" value="<?php echo $fileuploaddirurl; ?>"/> <?php _e('e.g. /wp-content/my-upload-dir', 'cforms2') ?></td>
				</tr>

				<tr class="ob space10">
					<td class="obL"><label for="cforms_upload_noid"><strong><?php _e('Disable noid- (tracking ID) prefix', 'cforms2') ?></strong></label></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_upload_noid" name="cforms_upload_noid" <?php if($cformsSettings['form'.$no]['cforms'.$no.'_noid']=='1') echo "checked=\"checked\""; ?>/></td>
				</tr>

				<tr class="ob space15">
					<td class="obL"><label for="cforms_upload_ext"><strong><?php _e('Allowed file extensions', 'cforms2') ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_upload_ext" name="cforms_upload_ext" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_upload_ext'])); ?>"/> <?php _e('[empty=no files are allowed]', 'cforms2') ?></td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_upload_size"><strong><?php _e('Maximum file size<br />in kilobyte', 'cforms2') ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_upload_size" name="cforms_upload_size" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_upload_size'])); ?>"/></td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_noattachments"><strong><?php _e('Do not email attachments', 'cforms2') ?></strong></label></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_noattachments" name="cforms_noattachments" <?php if($cformsSettings['form'.$no]['cforms'.$no.'_noattachments']=='1') echo "checked=\"checked\""; ?>/><br /><?php echo sprintf(__('<u>Note</u>: Attachments are stored on the server &amp; can be accessed via the <a href="%s" %s>cforms tracking</a> tables.', 'cforms2'),'?page='. $plugindir.'/cforms-global-settings.php#tracking','onclick="setshow(14)"'); ?></td>
				</tr>
				</table>
			</div>
		</fieldset>
    <?php endif; ?>


		<fieldset class="cformsoptions" id="anchormessage">
			<div class="cflegend op-closed" id="p1" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Messages, Text and Button Label', 'cforms2')?>
            </div>

			<div class="cf-content" id="o1">
				<p><?php echo sprintf(__('These are the messages displayed to the user on successful (or failed) form submission. These messages are form specific, a general message for entering a wrong <strong>visitor verification code</strong> can be found <a href="%s" %s>here</a>.', 'cforms2'),'?page='.$plugindir.'/cforms-global-settings.php#visitorv','onclick="setshow(13)"'); ?></p>

				<table class="form-table">

				<tr class="ob">
					<td class="obL"><label for="cforms_submit_text"><strong><?php _e('Submit button text', 'cforms2'); ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_submit_text" id="cforms_submit_text" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_submit_text']));  ?>" /></td>
				</tr>

				<tr class="ob space15">
					<td class="obL"><label for="cforms_working"><strong><?php _e('Waiting message', 'cforms2'); ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_working" id="cforms_working" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_working']));  ?>" /></td>
				</tr>
				<tr class="ob space15">
					<td class="obL"><label for="cforms_required"><strong><?php _e('"required" label', 'cforms2'); ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_required" id="cforms_required" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_required'])); ?>"/></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_emailrequired"><strong><?php _e('"email required" label', 'cforms2'); ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_emailrequired" id="cforms_emailrequired" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_emailrequired'])); ?>"/></td>
				</tr>

				<tr class="ob space15">
					<td class="obL"><label for="cforms_success"><?php _e('<strong>Success message</strong><br />when form filled out correctly', 'cforms2'); ?></label></td>
					<td class="obR">
                    	<table><tr>
						<td><textarea name="cforms_success" id="cforms_success"><?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_success'])); ?></textarea></td>
                    	</tr></table>
					</td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_failure"><?php _e('<strong>Failure message</strong><br />when missing fields or wrong field<br />formats (regular expr.)', 'cforms2'); ?></label></td>
					<td class="obR">
                    	<table><tr>
						<td><textarea name="cforms_failure" id="cforms_failure" ><?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_failure'])); ?></textarea></td>
                    	</tr></table>
					</td>
				</tr>
				<tr class="ob space15">
					<td class="obL"><label for="cforms_showposa"><strong><?php _e('Show messages', 'cforms2'); ?></strong></label></td>
					<td class="obR">
						<input class="allchk" type="checkbox" id="cforms_showposa" name="cforms_showposa" <?php if(substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],0,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_showposa"><?php _e('Above form', 'cforms2'); ?></label><br />
						<input class="allchk" type="checkbox" id="cforms_showposb" name="cforms_showposb" <?php if(substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],1,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_showposb"><?php _e('Below form', 'cforms2'); ?></label>
					</td>
				</tr>

				<tr class="obSEP"><td colspan="2"></td></tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_jump"><strong><?php _e('Jump to Error', 'cforms2'); ?></strong></label></td>
					<td class="obR">
						<input class="allchk" type="checkbox" id="cforms_jump" name="cforms_jump" <?php if(substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],4,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_jump"><?php _e('(Only Javascript)', 'cforms2'); ?></label>
					</td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_errorLI"><strong><?php _e('Fancy Error messages', 'cforms2'); ?></strong></label></td>
					<td class="obR">
						<input class="allchk" type="checkbox" id="cforms_errorLI" name="cforms_errorLI" <?php if(substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],2,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_errorLI"><?php _e('Enhanced display of errors (see also new theme CSS classes)', 'cforms2'); ?></label>
					</td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_errorINS"><strong><?php _e('Embedded Custom Error<br />Messages', 'cforms2'); ?></strong></label></td>
					<td class="obR">
						<input class="allchk" type="checkbox" id="cforms_errorINS" name="cforms_errorINS" <?php if(substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],3,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_errorINS"><?php _e('Field specific error messages will be shown above input field', 'cforms2'); ?></label>
					</td>
				</tr>
		 		</table>
			</div>
		</fieldset>


		<fieldset class="cformsoptions" id="anchoremail">
			<div class="cflegend op-closed" id="p2" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Core Form Admin / Email Options', 'cforms2')?>
            </div>

			<div class="cf-content" id="o2">
				<p><?php echo sprintf(__('These settings determine basic cforms behaviour and the form email recipient(s). Both %s and %s formats are valid, but check if your mail server does accept the format of choice!', 'cforms2'),'"<strong>xx@yy.zz</strong>"','"<strong>abc &lt;xx@yy.zz&gt;</strong>"') ?></p>
				<p><?php _e('The default FROM: address is based on your blog\'s name and the WP default address. It can be changed, but I highly recommend you do not, as it may render the plugin useless. If you do change the FROM: address, triple check if all admin emails are being sent/received! ', 'cforms2') ?></p>

				<table class="form-table">

                <tr class="ob">
                    <td class="obL"><strong><?php _e('Core options', 'cforms2') ?></strong></td>
					<td class="obR">
						<input class="allchk" type="checkbox" id="cforms_formaction" name="cforms_formaction" <?php if($cformsSettings['form'.$no]['cforms'.$no.'_formaction']) echo "checked=\"checked\""; ?>/><label for="cforms_formaction"><?php echo sprintf(__('Disable %s multipart/form-data enctype %s, e.g. to enable salesforce.com', 'cforms2'),'<strong>','</strong>'); ?></label>
		 			</td>
                </tr>

				<tr class="ob space10">
					<td class="obL"></td>
					<td class="obR">
						<input class="allchk" type="checkbox" id="cforms_dontclear" name="cforms_dontclear" <?php if($cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_form']) echo 'disabled="disabled"'; if($cformsSettings['form'.$no]['cforms'.$no.'_dontclear']) echo "checked=\"checked\""; ?>/><label for="cforms_dontclear"><?php echo sprintf(__('%sDo not reset%s input fields after submission', 'cforms2'),'<strong>','</strong>'); ?></label>
		 			</td>
	  			</tr>

				<?php if( $cformsSettings['global']['cforms_showdashboard'] == '1' ) : ?>
					<tr class="ob space10">
						<td class="obL"></td>
						<td class="obR"><input class="allchk" type="checkbox" id="cforms_dashboard" name="cforms_dashboard" <?php if($o=$cformsSettings['form'.$no]['cforms'.$no.'_dashboard']=='1') echo "checked=\"checked\""; ?>/><label for="cforms_dashboard"><?php echo sprintf(__('Show new entries on %sdashboard%s', 'cforms2'),'<strong>','</strong>') ?></label></td>
		  			</tr>
				<?php endif; ?>

				<?php if( $cformsSettings['global']['cforms_database'] == '1' ) : ?>
					<tr class="ob">
						<td class="obL"></td>
						<td class="obR"><input class="allchk" type="checkbox" id="cforms_notracking" name="cforms_notracking" <?php if( $cformsSettings['form'.$no]['cforms'.$no.'_notracking'] ) echo "checked=\"checked\""; ?>/><label for="cforms_notracking"><?php echo sprintf(__('%sExclude this form%s from database tracking', 'cforms2'),'<strong>','</strong>') ?></label></td>
		  			</tr>
				<?php endif; ?>

				<tr class="ob">
					<td class="obL"></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_customnames" name="cforms_customnames" <?php if($cformsSettings['form'.$no]['cforms'.$no.'_customnames']=='1') echo "checked=\"checked\""; ?>/><label for="cforms_customnames"><?php echo sprintf(__('Use %scustom input field NAMES &amp; ID\'s%s', 'cforms2'),'<strong>','</strong>') ?></label> <a class="infobutton" href="#" name="it4"><?php _e('Read note &raquo;', 'cforms2'); ?></a></td>
				</tr>

				<tr id="it4" class="infotxt"><td>&nbsp;</td><td class="ex"><?php _e('This feature replaces the default NAMEs/IDs (e.g. <strong>cf_field_12</strong>) with <em>custom ones</em>, either derived from the field label you have provided or by specifically declaring it via <strong>[id:XYZ]</strong>,e.g. <em>Your Name[id:the-name]</em>. This will for instance help to feed data to third party systems (requiring certain input field names in the $_POST variable).', 'cforms2') ?></td></tr>

				<tr class="ob">
					<td class="obL"></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_taftrick" name="cforms_taftrick" <?php if(substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)==='3') echo "checked=\"checked\""; ?>/><label for="cforms_taftrick"><?php echo sprintf(__('%sExtra variables%s e.g. {Title}', 'cforms2'),'<strong>','</strong>') ?></label> <a class="infobutton" href="#" name="it5"><?php _e('Read note &raquo;', 'cforms2'); ?></a></td>
				</tr>

				<tr id="it5" class="infotxt"><td>&nbsp;</td><td class="ex"><?php echo sprintf(__('There are <a href="%s" %s>three additional</a>, <em>predefined variables</em> that can be enabled here.', 'cforms2'),'?page='. $plugindir.'/cforms-help.php#extravariables','onclick="setshow(23)"'); ?> <strong><u><?php _e('Note:', 'cforms2')?></u></strong> <?php _e('This will add two more hidden fields to your form to ensure that all data is available also in AJAX mode.', 'cforms2')?></td></tr>

				<tr class="ob">
					<td class="obL"></td>
					<td class="obR">
						<input class="allchk" type="checkbox" id="cforms_hide" name="cforms_hide" <?php if($cformsSettings['form'.$no]['cforms'.$no.'_hide']) echo "checked=\"checked\""; ?>/><label for="cforms_hide"><?php echo sprintf(__('%sHide form%s after successful submission', 'cforms2'),'<strong>','</strong>'); ?></label>
		 			</td>
				</tr>

				<tr class="obSEP"><td colspan="2"></td></tr>

				<tr class="ob">
					<td class="obL"><strong><?php _e('Submission Limit', 'cforms2'); ?></strong></td>
					<td class="obR"><input type="text" id="cforms_maxentries" name="cforms_maxentries" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_maxentries'])); ?>"/><label for="cforms_maxentries"><?php _e('<u>total</u> # of submissions accepted [<strong>empty or 0 (zero) = off</strong>] (tracking must be enabled!)', 'cforms2') ?></label></td>
				</tr>

				<tr class="ob">
					<td class="obL" style="padding-top:7px"><strong><?php _e('Start Date', 'cforms2'); ?></strong></td>
					<?php $date = explode(' ',stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_startdate'])) ); ?>
					<td class="obR">
                    	<input type="text" class="cf_date" id="cforms_startdate" name="cforms_startdate" placeholder="<?php echo cforms2_admin_date_format(); ?>" value="<?php echo $date[0]; ?>"/>
                        <input type="text" id="cforms_starttime" name="cforms_starttime" placeholder="<?php _e('HH:MM', 'cforms2'); ?>" value="<?php echo $date[1]; ?>" title="<?php _e('Time entry.', 'cforms2') ?>"/>
						<label for="cforms_startdate"><?php
						$dt='x';
                        if( strlen($cformsSettings['form'.$no]['cforms'.$no.'_startdate'])>1 ):
                            $dt = human_time_diff(cforms2_make_time(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_startdate'])));
							if( $dt>0 ):
	                                echo __('The form will be available in ', 'cforms2').$dt;
	                            else:
	                                echo __('The form is available now.', 'cforms2');
							endif;
						endif;
                        ?>
                        </label>
                    </td>
				</tr>

				<tr class="ob">
					<td class="obL" style="padding-top:7px"><strong><?php _e('End Date', 'cforms2'); ?></strong></td>
					<?php $date = explode(' ',stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_enddate'])) ); ?>
					<td class="obR">
                    	<input type="text" class="cf_date" id="cforms_enddate" name="cforms_enddate" placeholder="<?php echo cforms2_admin_date_format(); ?>" value="<?php echo $date[0]; ?>"/>
                        <input type="text" id="cforms_endtime" name="cforms_endtime" placeholder="<?php _e('HH:MM', 'cforms2'); ?>" value="<?php echo $date[1]; ?>" title="<?php _e('Time entry.', 'cforms2') ?>"/>
						<label for="cforms_startdate"><?php
                        if( $dt=='x' && strlen($cformsSettings['form'.$no]['cforms'.$no.'_enddate'])>1 ):
                            $dt = human_time_diff(cforms2_make_time(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_enddate'])));
							if( $dt>0 ):
	                                echo __('The form will be available for another ', 'cforms2').$dt;
	                            else:
	                                echo __('The form is not available anymore.', 'cforms2');
							endif;
						endif;
                        ?></label>
                    </td>
				</tr>

				<?php if( $cformsSettings['form'.$no]['cforms'.$no.'_maxentries'] <> '' || $cformsSettings['form'.$no]['cforms'.$no.'_startdate'] <> '' || $cformsSettings['form'.$no]['cforms'.$no.'_enddate'] <> '' ) : ?>
				<tr class="ob">
	            	<td class="obL"><label for="cforms_limittxt"><strong><?php _e('Limit text', 'cforms2'); ?></strong></label></td>
	                <td class="obR"><table><tr><td><textarea name="cforms_limittxt" id="cforms_limittxt"><?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_limittxt'])); ?></textarea></td></tr></table></td>
				</tr>
				<?php endif; ?>

				<tr class="obSEP"><td colspan="2"></td></tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_redirect"><?php _e('<strong>Redirect</strong><br />options:', 'cforms2'); ?></label></td>
					<td class="obR">
						<input class="allchk" type="checkbox" id="cforms_redirect" name="cforms_redirect" <?php if($cformsSettings['form'.$no]['cforms'.$no.'_redirect']) echo "checked=\"checked\""; ?>/><label for="cforms_redirect"><?php _e('Enable alternative success page (redirect)', 'cforms2'); ?></label><br />
						<input name="cforms_redirect_page" id="cforms_redirect_page" value="<?php echo ($cformsSettings['form'.$no]['cforms'.$no.'_redirect_page']);  ?>" />
		 			</td>
				</tr>

				<tr class="obSEP"><td colspan="2"></td></tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_action"><?php _e('<strong>Send form data</strong><br /> to an alternative page:', 'cforms2'); ?></label></td>
					<td class="obR">
						<input class="allchk" type="checkbox" id="cforms_action" name="cforms_action" <?php if($cformsSettings['form'.$no]['cforms'.$no.'_action']) echo "checked=\"checked\""; ?>/><label for="cforms_action"><?php _e('Enable alternative form action!', 'cforms2'); ?></label><br />
						<input name="cforms_action_page" id="cforms_action_page" value="<?php echo ($cformsSettings['form'.$no]['cforms'.$no.'_action_page']);  ?>" /> <a class="infobutton" href="#" name="it2"><?php _e('Please read note &raquo;', 'cforms2'); ?></a>
		 			</td>
				</tr>

				<tr id="it2" class="infotxt"><td>&nbsp;</td><td class="ex"><?php _e('If you enable an alternative <strong>form action</strong> you <u>will loose any cforms application logic</u> (spam security, field validation, DB tracking etc.) in non-ajax mode! This setting is really only for developers that require additional capabilities around forwarding of form data and will turn cforms into a front-end only, a "form builder" so to speak.', 'cforms2') ?></td></tr>
				</table>
			</div>
		</fieldset>


		<fieldset class="cformsoptions" id="emailoptions">
			<div class="cflegend op-closed" id="p3" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Admin Email Message Options', 'cforms2')?>
            </div>

			<div class="cf-content" id="o3">
				<p><?php _e('Generally, emails sent to the admin and the submitting user can be both in plain text and HTML. The TXT part <strong>is required</strong>, HTML is <strong>optional</strong>.', 'cforms2'); ?></p>
				<p><?php echo sprintf(__('Below you\'ll find the settings for both the <strong>TXT part</strong> of the admin email as well as the <strong>optional HTML part</strong> of the message. Both areas permit the use of any of the <strong>pre-defined variables</strong> or <strong>data from input fields</strong>. <a href="%s" %s>Please see the documentation on the HELP page</a> (including HTML message examples!).', 'cforms2'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></p>

				<table class="form-table">
                <tr class="ob space15">
                    <td class="obL"></td>
                    <td class="obR"><input class="allchk" type="checkbox" id="cforms_emailoff" name="cforms_emailoff" <?php if($cformsSettings['form'.$no]['cforms'.$no.'_emailoff']=='1') echo "checked=\"checked\""; ?>/><label for="cforms_emailoff"><?php echo sprintf(__('%sTurn off%s admin email', 'cforms2'),'<strong>','</strong>') ?></label></td>
                </tr>
				</table>

				<table class="form-table">
                <tr class="">
                    <td class="obL"></td>
                    <td class="obR"><input class="allchk" type="checkbox" id="cforms_emptyoff" name="cforms_emptyoff" <?php if($cformsSettings['form'.$no]['cforms'.$no.'_emptyoff']=='1') echo "checked=\"checked\""; ?>/><label for="cforms_emptyoff"><?php echo sprintf(__('%sExclude empty fields%s from admin email', 'cforms2'),'<strong>','</strong>') ?></label></td>
                </tr>
				</table>

				<table class="form-table<?php if( $cformsSettings['form'.$no]['cforms'.$no.'_emailoff']=='1' ) echo " hidden"; ?>">
                <tr class="ob space15">
					<td class="obL"><label for="cforms_fromemail"><strong><?php _e('FROM: email address', 'cforms2') ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_fromemail" id="cforms_fromemail" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_fromemail'])); ?>" /></td>
				</tr>

				<tr class="ob space15">
					<td class="obL"><label for="cforms_email"><strong><?php _e('Admin email address(es)', 'cforms2') ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_email" id="cforms_email" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_email'])); ?>" /> <a class="infobutton" href="#" name="it1"><?php _e('More than one "<strong>form admin</strong>"? &raquo;', 'cforms2'); ?></a></td>
				</tr>

				<tr id="it1" class="infotxt"><td>&nbsp;</td><td class="ex"><?php echo sprintf(__('Simply add additional email addresses separated by a <strong style="color:red">comma</strong>. &nbsp; <em><u>Note:</u></em> &nbsp; If you want the visitor to choose from any of these per select box, you need to add a "<code>Multiple Recipients</code>" input field <a href="#anchorfields">above</a> (see the HELP section for <a href="%s" %s>more details</a>. If <strong>no</strong> "Multiple Recipients" input field is defined above, the submitted form data will go out to <strong>every address listed</strong>!', 'cforms2'),'?page='.$plugindir.'/cforms-help.php#multirecipients','onclick="setshow(19)"'); ?></td></tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_bcc"><strong><?php _e('BCC', 'cforms2') ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_bcc" id="cforms_bcc" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_bcc'])); ?>" /></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_subject"><strong><?php _e('Subject admin email', 'cforms2') ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_subject" id="cforms_subject" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_subject'])); ?>" /> <?php echo sprintf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms2'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></td>
				</tr>

				<tr class="ob space20">
					<td class="obL" style="padding-bottom:0">&nbsp;</td>				
					<td class="obR" style="padding-bottom:0">
						<input type="submit" class="allbuttons" name="cforms_resetAdminMsg" id="cforms_resetAdminMsg" value="<?php _e('Reset admin message to default', 'cforms2') ?>" onclick="javascript:document.mainform.action='#emailoptions';" />
		 			</td>
				</tr>
				
				<tr class="ob">
					<td class="obL" style="padding-bottom:0">
						<label for="cforms_header"><?php _e('<strong>Admin TEXT message</strong><br />(Header)', 'cforms2') ?></label>
					</td>
					<td class="obR" style="padding-bottom:0">
                    	<table><tr>
						<td><textarea name="cforms_header" id="cforms_header" ><?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_header'])); ?></textarea></td>
						<td><?php echo sprintf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms2'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></td>
                    	</tr></table>
		 			</td>
				</tr>
				<tr class="ob">
					<td class="obL" style="padding-top:0"><?php _e('(Footer)', 'cforms2')?></td>
					<td class="obR" style="padding-top:0"><input class="allchk" type="checkbox" id="cforms_formdata_txt" name="cforms_formdata_txt" <?php if(substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],0,1)=='1') echo "checked=\"checked\""; ?>/><label for="cforms_formdata_txt"><?php _e('<strong>Include</strong> user input at the bottom of the admin email', 'cforms2') ?></label></td>
				</tr>
				<tr class="ob">
					<td class="obL" style="padding-top:0">&nbsp;</td>
					<td class="obR" style="padding-top:0"><input type="text" name="cforms_space" id="cforms_space" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_space'])); ?>" /><label for="cforms_space"><?php _e('(# characters) : spacing between labels &amp; data, for plain txt version only', 'cforms2') ?></label></td>
				</tr>

				<tr class="ob space20">
					<td class="obL"><label for="cforms_admin_html"><strong><?php _e('Enable HTML', 'cforms2') ?></strong></label></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_admin_html" name="cforms_admin_html" <?php if($o=substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],2,1)=='1') echo "checked=\"checked\""; ?>/></td>
				</tr>

				<tr class="ob <?php if( !$o=='1' ) echo "hidden"; ?>">
					<td class="obL" style="padding-bottom:0"><label for="cforms_header_html"><?php _e('<strong>Admin HTML message</strong><br />(Header)', 'cforms2') ?></label></td>
					<td class="obR" style="padding-bottom:0">
                    	<table><tr>
						<td><textarea name="cforms_header_html" id="cforms_header_html" ><?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_header_html'])); ?></textarea></td>
						<td><?php echo sprintf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms2'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></td>
                    	</tr></table>
		 			</td>
				</tr>
				<tr class="ob <?php if( !$o=='1' ) echo "hidden"; ?>">
					<td class="obL" style="padding-top:0"><?php _e('(Footer)', 'cforms2')?></td>
					<td class="obR" style="padding-top:0"><input class="allchk" type="checkbox" id="cforms_formdata_html" name="cforms_formdata_html" <?php if(substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],1,1)=='1') echo "checked=\"checked\""; ?>/><label for="cforms_formdata_html"><?php _e('<strong>Include</strong> user input at the bottom of the admin email', 'cforms2') ?></label></td>
				</tr>
				<tr><td>&nbsp;</td><td><a class="infobutton" href="#" name="it3"><?php _e('\'Don\'t like the default form data block in your admin email?  &raquo;', 'cforms2'); ?></a></td></tr>
				<tr id="it3" class="infotxt"><td>&nbsp;</td><td class="ex"><strong><u><?php _e('Note:', 'cforms2')?></u></strong> <?php _e('To avoid sending ALL of the submitted user data (especially for very long forms) to the form admin simply <strong>uncheck</strong> "<em>Include user input ...</em>" and instead specify the fields you\'d like to receive via the use of <strong>custom variables</strong>.', 'cforms2'); ?></td></tr>
				</table>
			</div>
		</fieldset>


		<fieldset class="cformsoptions <?php if( !$ccboxused ) echo "hidden"; ?>" id="cc">
			<div class="cflegend op-closed" id="p4" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('CC Settings', 'cforms2')?>
            </div>

			<div class="cf-content" id="o4">
				<p><?php _e('This is the subject of the CC email that goes out the user submitting the form and as such requires the <strong>CC:</strong> field in your form definition above.', 'cforms2') ?></p>

				<table class="form-table">
				<tr class="ob">
					<td class="obL"><label for="cforms_ccsubject"><strong><?php _e('Subject CC', 'cforms2') ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_ccsubject" id="cforms_ccsubject" value="<?php $t=explode('$#$',$cformsSettings['form'.$no]['cforms'.$no.'_csubject']); echo stripslashes(htmlspecialchars($t[1])); ?>" /> <?php echo sprintf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms2'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></td>
				</tr>
				</table>

			</div>
		</fieldset>


		<fieldset class="cformsoptions" id="autoconf">
			<div class="cflegend op-closed" id="p5" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Auto Confirmation', 'cforms2')?>
            </div>

			<div class="cf-content" id="o5">
				<p><?php _e('These settings apply to an auto response/confirmation sent to the visitor. If enabled AND your form contains a "<code>CC me</code>" field <strong>AND</strong> the visitor selected it, no extra confirmation email is sent!', 'cforms2') ?></p>

				<table class="form-table">
				<tr class="ob">
					<td class="obL">&nbsp;</td>
					<td class="obR">
						<input class="allchk" type="checkbox" id="cforms_confirm" name="cforms_confirm" <?php if($o=$cformsSettings['form'.$no]['cforms'.$no.'_confirm']=="1") echo "checked=\"checked\""; ?>/><label for="cforms_confirm"><strong><?php _e('Activate auto confirmation', 'cforms2') ?></strong></label><br />
						<a class="infobutton" href="#" name="it8"><?php _e('Please read note &raquo;', 'cforms2'); ?></a>
		 			</td>
				</tr>
				
				<tr id="it8" class="infotxt"><td>&nbsp;</td><td class="ex"><?php _e('For the <em>auto confirmation</em> feature to work, make sure to mark at least one field <code>Email</code>, otherwise <strong>NO</strong> auto confirmation email will be sent out! If multiple fields are checked "Email", only the first in the list will receive a notification.', 'cforms2') ?></td></tr>

                <?php if( $o=="1" ) :?>
				<tr class="ob">
					<td class="obL"><label for="cforms_csubject"><strong><?php _e('Subject auto confirmation', 'cforms2') ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_csubject" id="cforms_csubject" value="<?php $t=explode('$#$',$cformsSettings['form'.$no]['cforms'.$no.'_csubject']); echo stripslashes(htmlspecialchars($t[0])); ?>" /> <?php echo sprintf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms2'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></td>
				</tr>
				<tr class="ob space20">
					<td class="obL" style="padding-bottom:0">&nbsp;</td>				
					<td class="obR" style="padding-bottom:0">
						<input type="submit" class="allbuttons" name="cforms_resetAutoCMsg" id="cforms_resetAutoCMsg" value="<?php _e('Reset auto confirmation message to default', 'cforms2') ?>" onclick="javascript:document.mainform.action='#autoconf';"/>
		 			</td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_cmsg"><strong><?php _e('TEXT message', 'cforms2') ?></strong></label></td>
					<td class="obR">
                    	<table><tr>
						<td><textarea name="cforms_cmsg" id="cforms_cmsg" ><?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_cmsg'])); ?></textarea></td>
						<td><?php echo sprintf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms2'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></td>
                    	</tr></table>
		 			</td>
				</tr>
				<tr class="ob space15">
					<td class="obL"><label for="cforms_user_html"><strong><?php _e('Enable HTML', 'cforms2') ?></strong></label></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_user_html" name="cforms_user_html" <?php if($o2=substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],3,1)=='1') echo "checked=\"checked\""; ?>/></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_cmsg_html"><strong><?php _e('HTML message', 'cforms2') ?></strong></label></td>
					<td class="obR">
                    	<table><tr>
						<td><textarea name="cforms_cmsg_html" id="cforms_cmsg_html" ><?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_cmsg_html'])); ?></textarea></td>
						<td><?php echo sprintf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms2'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></td>
                    	</tr></table>
		 			</td>
				</tr>

			    <?php
			    $a=$cformsSettings['form'.$no]['cforms'.$no.'_cattachment'][0];
                $err='';
				$t = (substr($a,0,1)=='/')?$a:plugin_dir_path(__FILE__).$a;
				if ( $t<>'' && !file_exists( $t ) ) {
			        $err = '<br /><p class="error">' . sprintf(__('Can\'t find the specified <strong>Attachment</strong> (%s)! Please verify the server path!', 'cforms2' ),$t) . '</p>';
			    }
				?>

				<tr class="ob">
					<td class="obL"><label for="cforms_cattachment"><strong><?php _e('Attachment', 'cforms2') ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_cattachment" id="cforms_cattachment" value="<?php echo stripslashes(htmlspecialchars($a)); ?>" /> <?php echo sprintf(__('File path: relative to the cforms plugin folder or an absolute path.', 'cforms2')); ?><?php echo $err; ?></td>
				</tr>
                <?php endif; ?>

				</table>
			</div>
		</fieldset>


		<fieldset class="cformsoptions" id="multipage">
			<div class="cflegend op-closed" id="p29" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Multi-Part / Multi-Page Forms', 'cforms2')?>
            </div>

			<div class="cf-content" id="o29">
				<p><?php _e('If enabled, new options will be shown below.', 'cforms2'); ?> <label for="cforms_mp_form"><?php _e('Mark this form to belong to a series of forms:', 'cforms2') ?></label> <input class="allchk" type="checkbox" id="cforms_mp_form" name="cforms_mp_form" <?php if( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_form']=='1' ) echo "checked=\"checked\""; ?>/></p>

				<?php if( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_form'] ) : ?>

				<table class="form-table">
				<tr class="ob">
					<td class="obL"><strong><?php _e('Email &amp; Tracking', 'cforms2') ?></strong></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_mp_email" name="cforms_mp_email" <?php if( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_email']=='1' ) echo "checked=\"checked\""; ?>/><label for="cforms_mp_email"><?php _e('Suppress admin email and DB tracking for *this* form', 'cforms2') ?></label></td>
				</tr>

				<tr class="ob">
					<td class="obL"><strong><?php _e('First Form', 'cforms2') ?></strong></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_mp_first" name="cforms_mp_first" <?php if( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_first']=='1' ) echo "checked=\"checked\""; ?>/><label for="cforms_mp_first"><?php _e('This is the *first* form of a series of forms', 'cforms2') ?></label></td>
				</tr>

				<tr class="obSEP"><td colspan="2"></td></tr>

                <tr class="ob">
					<td class="obL"><strong><?php _e('Reset Button', 'cforms2') ?></strong></td>
                    <td class="obR"><input class="allchk" type="checkbox" id="cforms_mp_reset" name="cforms_mp_reset" <?php if( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_reset']=='1' ) echo "checked=\"checked\""; ?>/><label for="cforms_mp_reset"><?php _e('Add a reset button to this form (reset to the first form in a series)', 'cforms2') ?></label></td>
				</tr>

				<tr class="ob">
					<td class="obL"><strong><?php _e('Text', 'cforms2') ?></strong></td>
					<td class="obR"><input type="text" id="cforms_mp_resettext" name="cforms_mp_resettext" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_resettext'])); ?>"/><label for="cforms_mp_resettext"><?php _e('Text for reset button', 'cforms2') ?></label></td>
				</tr>

				<tr class="obSEP"><td colspan="2"></td></tr>

                <tr class="ob">
					<td class="obL"><strong><?php _e('Back Button', 'cforms2') ?></strong></td>
                    <td class="obR"><input class="allchk" type="checkbox" id="cforms_mp_back" name="cforms_mp_back" <?php if( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_back']=='1' ) echo "checked=\"checked\""; ?>/><label for="cforms_mp_back"><?php _e('Add a back button to this form (back to the previous form)', 'cforms2') ?></label></td>
				</tr>

				<tr class="ob">
					<td class="obL"><strong><?php _e('Text', 'cforms2') ?></strong></td>
					<td class="obR"><input type="text" id="cforms_mp_backtext" name="cforms_mp_backtext" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_backtext'])); ?>"/><label for="cforms_mp_backtext"><?php _e('Text for back button', 'cforms2') ?></label></td>
				</tr>

				<tr class="obSEP"><td colspan="2"></td></tr>

				<tr class="ob">
					<td class="obL">&nbsp;</td>
					<td class="obR">
					<?php
	                    $formlistbox = ' <select id="picknextform" name="cforms_mp_next"'. ($cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_last']=='1'?' disabled="disabled"':'') .'>';
	                    for ($i=1; $i<=$FORMCOUNT; $i++){
	                        $j   = ( $i > 1 )?$i:'';
	                        $sel = ($cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_next']==$cformsSettings['form'.$j]['cforms'.$j.'_fname'])?' selected="selected"':'';
	                        $formlistbox .= '<option '.$sel.'>'.$cformsSettings['form'.$j]['cforms'.$j.'_fname'].'</option>';
	                    }
                        $formlistbox .= '<option style="background:#F2D7E0;" value="-1" '.(($cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_next']=='-1')?' selected="selected"':'').'>'.__('* stop here (last form) *', 'cforms2').'</option>';
                        $formlistbox .= '</select>';
                        echo $formlistbox;
                    ?>
                        <?php _e('Please choose the next form after this', 'cforms2') ?>
		 			</td>
				</tr>
				</table>
				<?php endif; ?>
			</div>
		</fieldset>


		<fieldset class="cformsoptions" id="readnotify">
			<div class="cflegend op-closed" id="p8" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('3rd Party Read-Notification Support', 'cforms2')?>
            </div>

			<div class="cf-content" id="o8">
				<p><?php echo sprintf(__('If you\'d like to utilize 3rd party email tracking such as %s or %s, add the respective suffix (e.g.: %s) here:', 'cforms2'),'<strong>readnotify.com</strong>','<strong>didtheyreadit.com</strong>','<code>.readnotify.com</code>') ?></p>

				<table class="form-table">
				<tr class="ob">
					<td class="obL"><label for="cforms_tracking"><strong><?php _e('Suffix for email tracking', 'cforms2') ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_tracking" name="cforms_tracking" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_tracking'])); ?>"/></td>
				</tr>
				</table>
			</div>
		</fieldset>

	    <div class="cf_actions" id="cf_actions" style="display:none;">
			<input id="cfbar-addbutton" class="allbuttons addbutton" type="submit" name="addbutton" value=""/>
			<input id="cfbar-dupbutton" class="allbuttons dupbutton" type="submit" name="dupbutton" value=""/>
			<input id="cfbar-delbutton" class="allbuttons deleteall" type="submit" name="delbutton" value=""/>
			<input id="preset" type="button" class="jqModalInstall allbuttons" name="<?php echo plugin_dir_url(__FILE__); ?>include/" value=" "/>
			<input id="backup" type="button" class="jqModalBackup allbuttons" name="backup"  value=" "/>
			<input id="cfbar-SubmitOptions" type="submit" name="SubmitOptions" class="allbuttons updbutton formupd" value="" />
	    </div>

		</form>

	<?php cforms2_footer(); ?>
</div>

<?php
add_action('admin_footer', 'cforms2_insert_modal');
function cforms2_insert_modal(){
	global $noDISP;
?>
	<div class="jqmWindow" id="cf_editbox">
		<div class="cf_ed_header"><?php _e('Input Field Settings', 'cforms2'); ?></div>
		<div class="cf_ed_main">
			<div id="cf_target"></div>
			<div class="controls"><a href="#" id="ok" class="jqmClose dashicons dashicons-yes" title="<?php _e('OK', 'cforms2') ?>"></a><a href="#" class="jqmClose dashicons dashicons-no-alt" title="<?php _e('Cancel', 'cforms2') ?>"></a></div>
		</div>
	</div>
	<div class="jqmWindow" id="cf_installbox">
		<div class="cf_ed_header"><?php _e('cforms Out-Of-The-Box Form Repository', 'cforms2'); ?></div>
		<div class="cf_ed_main">
			<form name="installpreset" method="post">
				<div id="cf_installtarget"></div>
				<div class="controls"><a href="#" id="okInstall" class="jqmClose dashicons dashicons-yes" title="<?php _e('OK', 'cforms2') ?>"></a><a href="#" id="cancelInstall" class="jqmClose dashicons dashicons-no-alt" title="<?php _e('Cancel', 'cforms2') ?>"></a></div>
				<input type="hidden" name="noSub" value="<?php echo $noDISP; ?>"/>
			</form>
		</div>
	</div>
	<div class="jqmWindow" id="cf_backupbox">
		<div class="cf_ed_header"><?php _e('Backup &amp; Restore Form Settings', 'cforms2'); ?></div>
		<div class="cf_ed_main_backup">
			<form enctype="multipart/form-data" name="backupform" method="post">
				<div class="controls">

	                <input type="submit" id="savecformsdata" name="savecformsdata" class="allbuttons backupbutton"  value="<?php _e('Backup current form settings', 'cforms2'); ?>" onclick="javascript:jQuery('#cf_backupbox').jqmHide();" /><br />
	                <label for="upload"><?php _e(' or restore previously saved settings:', 'cforms2'); ?></label>
	                <input type="file" id="upload" name="importall" size="25" />
	                <input type="submit" name="uploadcformsdata" class="allbuttons restorebutton" value="<?php _e('Restore from file', 'cforms2'); ?>" onclick="javascript:jQuery('#cf_backupbox').jqmHide();" />

                    <p class="cancel"><a href="#" class="jqmClose dashicons dashicons-no-alt" title="<?php _e('Cancel', 'cforms2') ?>"></a></p>

        	    </div>
				<input type="hidden" name="noSub" value="<?php echo $noDISP; ?>"/>
			</form>
		</div>
	</div>
<?php
}
