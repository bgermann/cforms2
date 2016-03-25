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
 * 
 * Plugin Name: cforms2
 * Plugin URI: https://wordpress.org/plugins/cforms2/
 * Description: cformsII offers unparalleled flexibility in deploying contact forms across your blog. Features include: comprehensive SPAM protection, Ajax support, Backup & Restore, Multi-Recipients, Role Manager support, Database tracking and many more.
 * Author: Oliver Seidel, Bastian Germann
 * Version: 14.12
 * Text Domain: cforms2
 */

define( 'CFORMS2_VERSION', '14.12' );

### db settings
global $wpdb;

$cformsSettings				= get_option('cforms_settings');
$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

require_once(plugin_dir_path(__FILE__) . 'lib_functions.php');
require_once(plugin_dir_path(__FILE__) . 'lib_activate.php');



$role = get_role('administrator');
if($role != null) {
	$role->add_cap('manage_cforms');
	$role->add_cap('track_cforms');
}

### activate cforms
function cforms2_activate() {
    cforms2_setup_db();
}
add_action('activate_' . plugin_basename(__FILE__), 'cforms2_activate' );



### settings corruputed?
if ( !is_array($cformsSettings) ){
	add_action('admin_menu', 'cforms2_settings_corrupted');
    return;
}
function cforms2_settings_corrupted() {
	$tmp = plugin_dir_path(__FILE__).'cforms-corrupted.php';

	add_menu_page(__('cformsII', 'cforms2'), __('cformsII', 'cforms2'), 'manage_cforms', $tmp, '', plugin_dir_url(__FILE__).'images/cformsicon.png' );
	add_submenu_page($tmp, __('Corrupted Settings', 'cforms2'), __('Corrupted Settings', 'cforms2'), 'manage_cforms', $tmp );

    add_action('admin_enqueue_scripts', 'cforms2_enqueue_style_admin' );
}


require_once (plugin_dir_path(__FILE__) . 'lib_email.php');
require_once (plugin_dir_path(__FILE__) . 'lib_aux.php');
require_once (plugin_dir_path(__FILE__) . 'lib_editor.php');


### session control for multi-page form
add_action('template_redirect', 'cforms2_start_session');

function cforms2_start_session() {
	session_cache_limiter('nocache');
	$session_id = session_id();
	if ( empty($session_id) ){
		session_start();
		### debug
		cforms2_dbg( "After session (".session_id().")start: ".print_r($_SESSION,1) );
	}
}



###
### main function
###
function cforms2($args = '',$no = '') {

	global $subID, $track, $cformsSettings, $trackf;

    $oldno = ($no=='1')?'':$no;  ### remeber old val, to reset session when in new MP form

    ##debug
    cforms2_dbg("Original form on page #$oldno");

	### multi page form: overwrite $no
    $isMPform = $cformsSettings['form'.$oldno]['cforms'.$oldno.'_mp']['mp_form'];

	##debug
    cforms2_dbg("Multi-page form = $isMPform");
   	if (isset($_SESSION) && isset($_SESSION['cforms']['current']))
		cforms2_dbg("PHP Session = ".$_SESSION['cforms']['current'] );

	if( $isMPform && is_array($_SESSION['cforms']) && $_SESSION['cforms']['current']>0 ){
		cforms2_dbg("form no. rewrite from #{$no} to #").$_SESSION['cforms']['current'];
		$no = $_SESSION['cforms']['current'];
	}

	### Safety, in case someone uses '1' for the default form
	$no = ($no=='1')?'':$no;

    ##debug
    cforms2_dbg("Switch to form #$no");

    $moveBack=false;
	### multi page form: reset button
	if( isset($_REQUEST['resetbutton'.$no]) && is_array($_SESSION['cforms']) ){
		$no = $oldno;
		unset($_SESSION['cforms']);
        $_SESSION['cforms']['current']=0;
	    $_SESSION['cforms']['first']=$oldno;
	    $_SESSION['cforms']['pos']=1;
	    unset( $_REQUEST );
	    ##debug
	    cforms2_dbg("Reset-Button pressed");
	}
	else ### multi page form: back button
	if( isset($_REQUEST['backbutton'.$no]) && isset($_SESSION['cforms']) && ($_SESSION['cforms']['pos']-1)>=0){
		$no = $_SESSION['cforms']['list'][($_SESSION['cforms']['pos']--)-1];
	    $_SESSION['cforms']['current']=$no;
        $moveBack=true;
	    ##debug
	    cforms2_dbg("Back-Button pressed");
	}
	else ### mp init: must be mp, first & not submitted!
	if( $isMPform && !is_array($_SESSION['cforms']) && $cformsSettings['form'.$oldno]['cforms'.$oldno.'_mp']['mp_first'] ){
	//if( $isMPform && $cformsSettings['form'.$oldno]['cforms'.$oldno.'_mp']['mp_first'] && !isset($_REQUEST['sendbutton'.$no]) ){
	    ##debug
	    cforms2_dbg("Current form is *first* MP-form");
        cforms2_dbg("Session found, you're on the first form and session is reset!");

        $no = ($oldno=='1')?'':$oldno; ### restore old val
        unset($_SESSION['cforms']);

        $_SESSION['cforms']['current']=0;
        $_SESSION['cforms']['first']=$no;
        $_SESSION['cforms']['pos']=1;
    }
	

	##debug
	cforms2_dbg( print_r($_SESSION,1) );


	### custom fields support
	if ( !(strpos($no,'+') === false) ) {
	    $no = substr($no,0,-1);
		$customfields = cforms2_build_fstat($args);
		$field_count = count($customfields);
		$custom=true;
	} else {
		$custom=false;
		$field_count = $cformsSettings['form'.$no]['cforms'.$no.'_count_fields'];
	}


	$content = '';

	$err=0;

	$validations = array();
	$all_valid = true;

	$custom_error='';
	$usermessage_class='';
	$usermessage_text	= "";

	// TODO integrate this check better
	$server_upload_size_error = false;
	$displayMaxSize = ini_get('post_max_size');
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) &&
		 empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0 ){
		$server_upload_size_error = true;
		$msgSize = $_SERVER['CONTENT_LENGTH'] / 1048576;
		echo ("<pre>Maximum size allowed:" . $displayMaxSize . "; size of your message:" . number_format((float)$msgSize, 2, '.', '') . "M</pre>");
	}

    ### non Ajax method
    if( isset($_REQUEST['sendbutton'.$no]) || $server_upload_size_error ) {
		global $cf_redirect;
		require_once (plugin_dir_path(__FILE__) . 'lib_validate.php');
		$usermessage_class = $all_valid?' success':' failure';
		if ( $cf_redirect <> '' ) { // TODO rework to do this via HTTP?
	        echo '<script type="text/javascript">'
                . 'location.href = "' .$cf_redirect. '"</script>';
		}
	}

	###
	###
	### paint form
	###
	###
	$success=false;

	### either show info message above or below
	$usermessage_text	= cforms2_check_default_vars($usermessage_text,$no);
	$usermessage_text	= cforms2_check_cust_vars($usermessage_text,$track);
	### logic: possibly change usermessage
	if ( function_exists('my_cforms_logic') )
	    $usermessage_text = my_cforms_logic($trackf, $usermessage_text,'successMessage');

   	$umc = ($usermessage_class<>'' && $no>1)?' '.$usermessage_class.$no:'';

    ##debug
    cforms2_dbg("User info for form #$no");

	### where to show message
	if( substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],0,1)=='y' ) {
		$content .= '<div id="usermessage'.$no.'a" class="cf_info' . $usermessage_class . $umc .' ">' . $usermessage_text . '</div>';
		$actiontarget = 'a';
 	} else if ( substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],1,1)=='y' )
		$actiontarget = 'b';


	### multi page form: overwrite $no, move on to next form
   	$oldcurrent = $no;
	if( $all_valid && isset($_REQUEST['sendbutton'.$no]) ){

		$isMPformNext=false; ### default
    	$oldcurrent = $no;

		##debug
		cforms2_dbg("Form is all valid & sendbutton pressed.");

		if( $isMPform && isset($_SESSION['cforms']) && $_SESSION['cforms']['current']>0 && $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_next']<>-1 ){

        	$isMPformNext=true;
            $no = cforms2_check_form_name( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_next'] );

	        ##debug
	        cforms2_dbg("Session active and now moving on to form #$no");

	        ### logic: possibly change next form
	        if ( function_exists('my_cforms_logic') )
	            $no = my_cforms_logic($trackf, $no,"nextForm");  ### use trackf!

			$oldcurrent = $_SESSION['cforms']['current'];
	        $_SESSION['cforms']['current'] = ($no=='')?1:$no;

			$field_count = $cformsSettings['form'.$no]['cforms'.$no.'_count_fields'];

	    }elseif( $isMPform && $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_next']==-1 ){

	        ##debug
	        cforms2_dbg("Session was active but is being reset now");

			$oldcurrent = $no;

	        $no = $_SESSION['cforms']['first'];
	        unset( $_SESSION['cforms'] );

	        $_SESSION['cforms']['current']=0;
	        $_SESSION['cforms']['first']=$no;
	        $_SESSION['cforms']['pos']=1;

			$field_count = $cformsSettings['form'.$no]['cforms'.$no.'_count_fields'];
        } else {
	        unset( $_SESSION['cforms'] );
	        $_SESSION['cforms']['current']=0;
	        $_SESSION['cforms']['first']=$no;
	        $_SESSION['cforms']['pos']=1;
			}
	} elseif (!$isMPform) {
		unset( $_SESSION['cforms'] );
		$_SESSION['cforms']['current']=0;
		$_SESSION['cforms']['first']=$no;
		$_SESSION['cforms']['pos']=1;
	}

	
	
    ##debug
    cforms2_dbg("All good, currently on form #$no, [current]=".$_SESSION['cforms']['current']);

	##debug
	cforms2_dbg(print_r($_SESSION,1));
	cforms2_dbg(print_r($track,1));

	### redirect == 2 : hide form?    || or if max entries reached! w/ SESSION support if#2
	if (  $all_valid && (
    		( $cformsSettings['form'.$no]['cforms'.$no.'_hide'] && isset($_REQUEST['sendbutton'.$no]) ) ||
    	  	( $cformsSettings['form'.$oldcurrent]['cforms'.$oldcurrent.'_hide'] && isset($_REQUEST['sendbutton'.$oldcurrent]) )
          				)
       )
		return $content;
	else if ( ($cformsSettings['form'.$no]['cforms'.$no.'_maxentries']<>'' && cforms2_get_submission_left($no)<=0) || !cforms2_check_time($no) ){

        global $cflimit;
		if ( $cflimit == "reached" )
			return stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_limittxt']);
		else
			return $content.stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_limittxt']);

	}



 	### alternative form action
	$alt_action=false;
	if( $cformsSettings['form'.$no]['cforms'.$no.'_action']=='1' ) {
		$action = $cformsSettings['form'.$no]['cforms'.$no.'_action_page'];
		$alt_action=true;
	}
 	else
		$action = cforms2_get_current_page() . '#usermessage'. $no . $actiontarget;


	$enctype = $cformsSettings['form'.$no]['cforms'.$no.'_formaction'] ? 'enctype="application/x-www-form-urlencoded"':'enctype="multipart/form-data"';

	### start with form tag
	$content .= '<form '.$enctype.' action="' . $action . '" method="post" class="cform ' . sanitize_title_with_dashes($cformsSettings['form'.$no]['cforms'.$no.'_fname']). ' ' .( $cformsSettings['form'.$no]['cforms'.$no.'_dontclear']?' cfnoreset':'' ). '" id="cforms'.$no.'form">';


    ### Session item counter (for default values)
    $sItem=1;

	### start with no fieldset
	$fieldsetopen = false;

	$upload = false;
	$fscount = 1;
	$ol = false;

	$inpFieldArr = array(); // for var[] type input fields	
	for($i = 1; $i <= $field_count; $i++) {

		if ( !$custom )
      		$field_stat = explode('$#$', $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . $i]);
		else
    		$field_stat = explode('$#$', $customfields[$i-1]);

		$field_name       = $field_stat[0];
		$field_type       = $field_stat[1];
		$field_required   = $field_stat[2];
		$field_emailcheck = $field_stat[3];
		$field_clear      = $field_stat[4];
		$field_disabled   = $field_stat[5];
		$field_readonly   = $field_stat[6];

		
		### check for html5 attributes
	    $obj = explode('|html5:', $field_name,2);
		$obj[] = "";
		$html5 = ($obj[1]<>'') ? preg_split('/\x{00A4}/u',$obj[1], -1) : '';

		###debug
		cforms2_dbg("\t\t html5 check, settings = ".print_r($html5,1));
		
		### check for custom err message and split field_name
	    $obj = explode('|err:', $obj[0],2);
		$obj[] = "";
		$fielderr = $obj[1];
		
		###debug
		cforms2_dbg("\t adding $field_type field: $field_name");
		
		if ( $fielderr <> '')	{
		    switch ( $field_type ) {
			    case 'upload':
					$custom_error .= 'cf_uploadfile' . $no . '-'. $i . '$#$'.$fielderr.'|';
	    			break;

			    default:
    				preg_match('/^([^#\|]*).*/',$field_name,$input_name);
    				if ( strpos($input_name[1],'[id:')>0 )
    					preg_match ('/\[id:(.+)\]/',$input_name[1],$input_name);

					$custom_error .= ($cformsSettings['form'.$no]['cforms'.$no.'_customnames']=='1')?cforms2_sanitize_ids($input_name[1]):'cf'.$no.'_field_'.$i;
					$custom_error .= '$#$'.$fielderr.'|';
		    }
		}


		### check for title attrib
	    $obj = explode('|title:', $obj[0],2);
		$obj[] = "";
		$fieldTitle = ($obj[1]<>'')?str_replace('"','&quot;',stripslashes($obj[1])):'';

		###debug
		cforms2_dbg("\t\t title check, obj[0] = ".$obj[0]);
		

		### special treatment for selectboxes
		if (  in_array($field_type,array('multiselectbox','selectbox','radiobuttons','checkbox','checkboxgroup','ccbox','emailtobox'))  ){

			$chkboxClicked = array();
			if (  in_array($field_type,array('checkbox','ccbox')) && strpos($obj[0],'|set:')>1 ){
				$chkboxClicked = explode('|set:', stripslashes($obj[0]) );
				$obj[0] = $chkboxClicked[0];
			}
			$chkboxClicked[] = "";
			$chkboxClicked[] = "";
			###debug
			cforms2_dbg("\t\t found checkbox:, obj[0] = ".$obj[0]);

			$options = explode('#', stripslashes($obj[0]) );
			
            if (  in_array($field_type,array('checkbox','ccbox'))  )
				$field_name = ( $options[0]=='' ) ? $options[1]:$options[0];
			else
				$field_name = $options[0];
				
			###debug
			cforms2_dbg("\t\t left from '#' (=field_name) = ".$options[0].", right from '#': ".$options[1] . "  -> field_name= $field_name");

		}


		### check if fieldset is open
		if ( !$fieldsetopen && !$ol && $field_type<>'fieldsetstart') {
			$content .= '<ol class="cf-ol">';
			$ol = true;
		}


		$defaultvalue = '';
		### setting the default val & regexp if it exists
		if ( ! in_array($field_type,array('fieldsetstart','fieldsetend','radiobuttons','checkbox','checkboxgroup','ccbox','emailtobox','multiselectbox','selectbox')) ) {

		    ### check if default val & regexp are set
		    $obj = explode('|', $obj[0],3);
			$obj[] = "";
			$obj[] = "";

			if ( $obj[2] <> '')	$reg_exp = str_replace('"','&quot;',stripslashes($obj[2])); else $reg_exp='';
		    if ( $obj[1] <> '')	$defaultvalue = str_replace( array('"','\n'),array('&quot;',"\r"), cforms2_check_default_vars(stripslashes(($obj[1])),$no) );

			$field_name = $obj[0];
		}


		### label ID's
		$labelIDx = '';
		$labelID  = ($cformsSettings['global']['cforms_labelID']=='1')?' id="label-'.$no.'-'.$i.'"':'';

		### <li> ID's
		$liID = ( $cformsSettings['global']['cforms_liID']=='1' ||
				  substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],2,1)=="y" ||
				  substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],3,1)=="y" )?' id="li-'.$no.'-'.$i.'"':'';

		### input field names & label
		$isFieldArray = false;
		if ( $cformsSettings['form'.$no]['cforms'.$no.'_customnames']=='1' ){

			if ( strpos($field_name,'[id:')!==false ){
				$isFieldArray = strpos($field_name,'[]');
				$idPartA = strpos($field_name,'[id:');
				$idPartB = strrpos($field_name,']',$idPartA);

				if( $isFieldArray ){
				
					$input_id = $input_name = cforms2_sanitize_ids( substr($field_name,$idPartA+4,($idPartB-$idPartA)-4) );
					
					if( !$inpFieldArr[$input_id] || $inpFieldArr[$input_id]=='' ){
						$inpFieldArr[$input_id]=1;
					} 
					
					$input_id	.= $inpFieldArr[$input_id]++;
					$input_name .= '[]';
				
				} else
					$input_id = $input_name = cforms2_sanitize_ids( substr($field_name,$idPartA+4,($idPartB-$idPartA)-4) );

				$field_name = substr_replace($field_name,'',$idPartA,($idPartB-$idPartA)+1);
				###debug
				cforms2_dbg("\t \t parsing custom ID/NAME...new field_name = $field_name, ID=$input_id");
				
			} else
				$input_id = $input_name = cforms2_sanitize_ids(stripslashes($field_name));

		} else
			$input_id = $input_name = 'cf'.$no.'_field_'.$i;


		$field_class = '';
		$field_value = '';

		$captchas = cforms2_get_pluggable_captchas();
		if ( array_key_exists($field_type, $captchas) && is_user_logged_in() &&  !$captchas[$field_type]->check_authn_users())
			continue;

		switch ($field_type){
			case 'upload':
				$input_id = $input_name = 'cf_uploadfile'.$no.'-'.$i;
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



		### additional field classes
		if ( $field_disabled )		$field_class .= ' disabled';
		if ( $field_readonly )		$field_class .= ' readonly';
		if ( $field_emailcheck )	$field_class .= ' fldemail';
		if ( $field_required ) 		$field_class .= ' fldrequired';


		### error ?
		$liERR = $insertErr = '';


		### only for mp forms
		if( $moveBack || $isMPform ){  // $isMPformNext
				$field_value = htmlspecialchars( stripslashes(  $_SESSION['cforms']['cf_form'.$no][ $_SESSION['cforms']['cf_form'.$no]['$$$'.($sItem++)] ] ) );
				cforms2_dbg( 'retrieving session values to pre-fill...'.$field_value);
		}

		if( !$all_valid ){
			### errors...
			if ( !$server_upload_size_error && $validations[$i]!=1) {
				$field_class .= ' cf_error';

				### enhanced error display
				if(substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],2,1)=="y")
					$liERR = 'cf_li_err';
				if(substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],3,1)=="y")
					$insertErr = ($fielderr<>'')?'<ul class="cf_li_text_err"><li>'.stripslashes($fielderr).'</li></ul>':'';
			}

			if (!isset($_REQUEST[$input_name]))
				$_REQUEST[$input_name] = '';            ### the field could not be there at all
			if ( $field_type == 'multiselectbox' || $field_type == 'checkboxgroup' )
				$field_value = $_REQUEST[$input_name];  ### in this case it's an array! will do the stripping later
			else
				$field_value = htmlspecialchars(stripslashes($_REQUEST[$input_name]));

		} else if( (!isset($_REQUEST['sendbutton'.$no]) && isset($_REQUEST[$input_name])) || $cformsSettings['form'.$no]['cforms'.$no.'_dontclear'] ){

		    ### only pre-populating fields...
			if ( $field_type == 'multiselectbox' || $field_type == 'checkboxgroup' )
				$field_value = $_REQUEST[$input_name];  ### in this case it's an array! will do the stripping later
			else{
				$field_value = htmlspecialchars(stripslashes($_REQUEST[$input_name]));
			}
	    }


		### print label only for non "textonly" fields! Skip some others too, and handle them below indiv.
		$standard_field = !in_array($field_type, array('hidden','textonly','fieldsetstart','fieldsetend','ccbox','checkbox','checkboxgroup','radiobuttons'));
		if($standard_field) {
			$content .= '<li'.$liID.' class="'.$liERR.'">'.$insertErr;
			if (!in_array($field_type, array_keys($captchas)))
				$content .= '<label' . $labelID . ' for="'.$input_id.'"'. ($field_type == 'captcha' ? ' class="seccap"' : '') . '><span>' . stripslashes(($field_name)) . '</span></label>';
		}


		### if not reloaded (due to err) then use default values
		if ( $field_value=='' && $defaultvalue<>'' )
			$field_value = $defaultvalue;

		### field disabled or readonly, greyed out?
		$disabled = $field_disabled?' disabled="disabled"':'';
		$readonly = $field_readonly?' readonly="readonly"':'';


		### add input field
		$dp = '';
		$field  = '';
		$val = '';
		if (array_key_exists($field_type, $captchas)){
			$html = $captchas[$field_type]->get_request($input_id, 'secinput fldrequired '.$field_class, $fieldTitle);
			$field = $html;
		}
		else switch($field_type) {

			case "upload":
	  			$upload=true;  ### set upload flag for ajax suppression!
				$field = '<input' . $readonly.$disabled . ' type="file" name="cf_uploadfile'.$no.'[]" id="cf_uploadfile'.$no.'-'.$i.'" class="cf_upload ' . $field_class . '" title="'.$fieldTitle.'"/>';
				break;

			case "textonly":
				$field .= '<li'.$liID.' class="textonly' . (($defaultvalue<>'')?' '.$defaultvalue:'') . '"' . (($reg_exp<>'')?' style="'.$reg_exp.'" ':'') . '>' . stripslashes(($field_name)) . '</li>';
				break;

			case "fieldsetstart":
				if ($fieldsetopen) {
						$field = '</ol></fieldset>';
						$fieldsetopen = false;
						$ol = false;
				}
				if (!$fieldsetopen) {
						if ($ol)
							$field = '</ol>';

						$field .= '<fieldset class="cf-fs'.$fscount++.'">'
						       .  '<legend>' . stripslashes($field_name) . '</legend>'
						       .  '<ol class="cf-ol">';
						$fieldsetopen = true;
						$ol = true;
		 		}
				break;

			case "fieldsetend":
				if ($fieldsetopen) {
						$field = '</ol></fieldset>';
						$fieldsetopen = false;
						$ol = false;
				} else $field='';
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
				if( strpos($field_type,'tml5')!==false ){
					$type = substr($field_type,5);
					if( is_array($html5) ){
						$h5_0 = ( $html5[0] == '1' ) ? ' autocomplete="on"' :'';
						$h5_1 = ( $html5[1] == '1' ) ? ' autofocus ="autofocus"' :'';
						$h5_2 = ( $html5[2] != '' ) ? ' min="'.$html5[2].'"' :'';
						$h5_3 = ( $html5[3] != '' ) ? ' max="'.$html5[3].'"' :'';
						$h5_4 = ( $html5[4] != '' ) ? ' pattern="'.$html5[4].'"' :'';
						$h5_5 = ( $html5[5] != '' ) ? ' step="'.$html5[5].'"' :'';
						$h5_6 = ( $html5[6] != '' ) ? ' placeholder="'.$html5[6].'"' :'';
						$h5 = $h5_0.$h5_1.$h5_2.$h5_3.$h5_4.$h5_5.$h5_6;
					}
					$h5_7 = ( $field_required ) ? ' required="required"' : '';
					$h5 .= $h5_7 . ' ';
					###debug
					cforms2_dbg('......html5 attributes: '.$h5);
				}else
					$type = ($field_type=='pwfield')?'password':'text';
					
				$field_class = ($field_type=='datepicker')?$field_class.' cf_date':$field_class;

			    $onfocus = $field_clear?' onfocus="clearField(this)" onblur="setField(this)"' : '';

				$field = '<input' . $h5.$readonly.$disabled . ' type="'.$type.'" name="'.$input_name.'" id="'.$input_id.'" class="' . $field_class . '" value="' . $field_value  . '"'.$onfocus.' title="'.$fieldTitle.'"/>';
				  if ( $reg_exp<>'' )
	           		 $field .= '<input type="hidden" name="'.$input_name.'_regexp" id="'.$input_id.'_regexp" value="'.$reg_exp.'" title="'.$fieldTitle.'"/>';

				$field .= $dp;
				break;

			case "hidden":

				$field_value = cforms2_check_post_vars($field_value);
				$field_value = cforms2_check_default_vars($field_value,$no);

                if ( preg_match('/^<([a-zA-Z0-9]+)>$/',$field_value,$getkey) )
                    $field_value = $_GET[$getkey[1]];

				$field .= '<li class="cf_hidden"><input type="hidden" class="cfhidden" name="'.$input_name.'" id="'.$input_id.'" value="' . $field_value  . '" title="'.$fieldTitle.'"/></li>';
				break;

			case "textarea":
			    $onfocus = $field_clear?' onfocus="clearField(this)" onblur="setField(this)"' : '';

				$field = '<textarea' . $readonly.$disabled . ' cols="30" rows="8" name="'.$input_name.'" id="'.$input_id.'" class="' . $field_class . '"'. $onfocus.' title="'.$fieldTitle.'">' . $field_value  . '</textarea>';
				  if ( $reg_exp<>'' )
	           		 $field .= '<input type="hidden" name="'.$input_name.'_regexp" id="'.$input_id.'_regexp" value="'.$reg_exp.'" title="'.$fieldTitle.'"/>';
				break;

	   		case "ccbox":
			case "checkbox":
				if ( !$all_valid || ($all_valid && $cformsSettings['form'.$no]['cforms'.$no.'_dontclear']) || ($isMPform && is_array($_SESSION['cforms']['cf_form'.$no])) ) //exclude MP! if first time on the form = array = null
					$preChecked = ( $field_value && $field_value<>'' )? ' checked="checked"':'';  // for MPs 
				else
					$preChecked = ( strpos($chkboxClicked[1],'true') !== false ) ? ' checked="checked"':'';  // $all_valid = user choice prevails

				$err='';
				if( !$server_upload_size_error && !$all_valid && $validations[$i]<>1 )
					$err = ' cf_errortxt';

			    $opt = explode('|', $field_name,2);
				$opt[] = "";
				if ( $options[1]<>'' ) {  ### $options =  explode('#', stripslashes($obj[0]) ) (line 476)
				 		$before = '<li'.$liID.' class="'.$liERR.'">'.$insertErr;
						$after  = '<label'. $labelID . ' for="'.$input_id.'" class="cf-after'.$err.'"><span>' . $opt[0] . '</span></label></li>';
				 		$ba = 'a';
				}
				else {
						$before = '<li'.$liID.' class="'.$liERR.'">'.$insertErr.'<label' . $labelID . ' for="'.$input_name.'" class="cf-before'. $err .'"><span>' . $opt[0] . '</span></label>';
				 		$after  = '</li>';
				 		$ba = 'b';
				}
				### if | val provided, then use "X"
				if( $val=='' )
					$val = ($opt[1]<>'')?' value="'.$opt[1].'"':'';
					
				$field = $before . '<input' . $readonly.$disabled . ' type="checkbox" name="'.$input_name.'" id="'.$input_id.'" class="cf-box-' . $ba . $field_class . '"'.$val.' title="'.$fieldTitle.'"'.$preChecked.'/>' . $after;

				break;


			case "checkboxgroup":
				$liID_b = ($liID <>'')?substr($liID,0,-1) . 'items"':'';
				array_shift($options);
				$field .= '<li'.$liID.' class="cf-box-title">' . (($field_name)) . '</li>' .
						  '<li'.$liID_b.' class="cf-box-group">';
				$id=1; $j=0;

                ### mp session support
                if ( ( $moveBack || $isMPform ) && !is_array($field_value) )
                    $field_value = explode(',',$field_value);

				foreach( $options as $option  ) {

						### supporting names & values
						$boxPreset = explode('|set:', $option );
				    	$opt = explode('|', $boxPreset[0],2);
						if ( $opt[1]=='' ) $opt[1] = $opt[0];

	                    $checked = '';
						if( $moveBack || $isMPform ){ //$isMPformNext
		                    if ( in_array($opt[1],array_values($field_value)) )
		                        $checked = 'checked="checked"';
	                    } elseif ( is_array($field_value) ){
		                    if ( $opt[1]==htmlspecialchars( stripslashes(strip_tags($field_value[$j])) ) )  {
		                        $checked = 'checked="checked"';
		                        $j++;
		                    }
	                    }else{
							if ( strpos($boxPreset[1],'true')!==false )
						    $checked = ' checked="checked"';
	                    }

						$brackets = $isFieldArray ? '' : '[]';

						if ( $labelID<>'' ) $labelIDx = substr($labelID,0,-1) . $id . '"';

						if ( $opt[0]=='' )
							$field .= '<br />';
						else
							$field .= '<input' . $readonly.$disabled . ' type="checkbox" id="'. $input_id .'-'. $id . '" name="'. $input_name . $brackets .'" value="'.$opt[1].'" '.$checked.' class="cf-box-b" title="'.$fieldTitle.'"/>'.
									  '<label' . $labelIDx . ' for="'. $input_id .'-'. ($id++) . '" class="cf-group-after"><span>'.$opt[0] . "</span></label>";

					}
				$field .= '</li>';
				break;


			case "multiselectbox":
				$field .= '<select' . $readonly.$disabled . ' multiple="multiple" name="'.$input_name.'[]" id="'.$input_id.'" class="cfselectmulti ' . $field_class . '" title="'.$fieldTitle.'">';
				array_shift($options);
				$j=0;

                ### mp session support
                if ( $moveBack || $isMPform ) //$isMPformNext
                    $field_value = explode(',',$field_value);

				foreach( $options as $option  ) {

                    ### supporting names & values
					$optPreset = explode('|set:', $option );
				    $opt = explode('|', $optPreset[0],2);
                    if ( $opt[1]=='' ) $opt[1] = $opt[0];

                    $checked = '';
					if( $moveBack || $isMPform ){
	                    if ( in_array($opt[1],array_values($field_value)) )
	                        $checked = 'selected="selected"';
                    } elseif ( is_array($field_value) ){
	                    if ( $opt[1]==stripslashes(htmlspecialchars(strip_tags($field_value[$j]))) )  {
	                        $checked = ' selected="selected"';
	                        $j++;
	                    }
	                }else{
						if ( strpos($optPreset[1],'true')!==false )
						    $checked = ' selected="selected"';
	                }

                    $field.= '<option value="'. str_replace('"','&quot;',$opt[1]) .'"'.$checked.'>'.$opt[0].'</option>';

				}
				$field.= '</select>';
				break;

			case "emailtobox":
			case "selectbox":
				$field = '<select' . $readonly.$disabled . ' name="'.$input_name.'" id="'.$input_id.'" class="cformselect' . $field_class . '" title="'.$fieldTitle.'">';
				array_shift($options); $jj=$j=0;

				foreach( $options as $option  ) {

					### supporting names & values
					$optPreset = explode('|set:', $option );
					$optPreset[] = "";
				    $opt = explode('|', $optPreset[0],2);
					$opt[]="";
					if ( $opt[1]=='' ) $opt[1] = $opt[0];
					
					### email-to-box valid entry?
			    if ( $field_type == 'emailtobox' && $opt[1]<>'-' )
							$jj = $j; else $jj = '-';
          $j++;

				    $checked = '';

					if( $field_value == '' || $field_value == '-') {
							if ( strpos($optPreset[1],'true')!==false )
							    $checked = ' selected="selected"';
					}	else
							if ( $opt[1]==$field_value || $jj==$field_value )
								$checked = ' selected="selected"';

					$field.= '<option value="'.(($field_type=='emailtobox')?$jj:$opt[1]).'"'.$checked.'>'.$opt[0].'</option>';

				}
				$field.= '</select>';
				break;

			case "radiobuttons":
				$liID_b = ($liID <>'')?substr($liID,0,-1) . 'items"':'';	### only if label ID's active

				array_shift($options);
				$field .= '<li'.$liID.' class="'.$liERR.' cf-box-title">'. $insertErr . (($field_name)) . '</li>' .
						  '<li'.$liID_b.' class="cf-box-group">';

				$id=1;
				foreach( $options as $option  ) {
				    $checked = '';

						### supporting names & values
						$radioPreset = explode('|set:', $option );
				    	$opt = explode('|', $radioPreset[0],2);
						$opt[]="";
						if ( $opt[1]=='' ) $opt[1] = $opt[0];

						if( $field_value == '' ) {

								if ( strpos($radioPreset[1],'true')!==false)
								    $checked = ' checked="checked"';

						}	else
								if ( $opt[1]==$field_value ) $checked = ' checked="checked"';

						if ( $labelID<>'' ) $labelIDx = substr($labelID,0,-1) . $id . '"';

						if ( $opt[0]=='' )
							$field .= '<br />';
						else
							$field .=
								  '<input' . $readonly.$disabled . ' type="radio" id="'. $input_id .'-'. $id . '" name="'.$input_name.'" value="'.$opt[1].'"'.$checked.' class="cf-box-b' . ($field_required?' fldrequired':'') .'" title="'.$fieldTitle.'"/>'.
								  '<label' . $labelIDx . ' for="'. $input_id .'-'. ($id++) . '" class="cf-after"><span>'.$opt[0] . "</span></label>";

					}
				$field .= '</li>';
				break;

		}

		### debug
		cforms2_dbg("Form setup: $field_type, val=$field_value, default=$defaultvalue");

		### add new field
		$content .= $field;

		### adding "required" text if needed
		if($field_emailcheck == 1)
			$content .= '<span class="emailreqtxt">'.stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_emailrequired']).'</span>';
		else if($field_required == 1 && !in_array($field_type,array('ccbox','checkbox','radiobuttons')) )
			$content .= '<span class="reqtxt">'.stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_required']).'</span>';

		### close out li item
		if ($standard_field)
			$content .= '</li>';

	} ### all fields


	### close any open tags
	if ( $ol )
		$content .= '</ol>';
	if ( $fieldsetopen )
		$content .= '</fieldset>';


	### rest of the form
	if ( $cformsSettings['form'.$no]['cforms'.$no.'_ajax']=='1' && !$upload && !$custom && !$alt_action)
		$ajaxenabled = ' onclick="return cforms_validate(\''.$no.'\', false)"';
	else if ( ($upload || $custom || $alt_action) && $cformsSettings['form'.$no]['cforms'.$no.'_ajax']=='1' )
		$ajaxenabled = ' onclick="return cforms_validate(\''.$no.'\', true)"';
	else
		$ajaxenabled = '';


	### just to appease html "strict"
	$content .= '<fieldset class="cf_hidden"><legend>&nbsp;</legend>';

	### custom error
	$custom_error=substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],2,1).substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],3,1).substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],4,1).$custom_error;


	### Extra Fields
	if ( substr($cformsSettings['form'.$oldno]['cforms'.$oldno.'_tellafriend'],0,1) === '3' ){
		$content .= '<input type="hidden" name="comment_post_ID'.$no.'" id="comment_post_ID'.$no.'" value="' . ( isset($_GET['pid'])? $_GET['pid'] : get_the_ID() ) . '"/>' .
					'<input type="hidden" name="cforms_pl'.$no.'" id="cforms_pl'.$no.'" value="' . ( isset($_GET['pid'])? get_permalink($_GET['pid']) : get_permalink() ) . '"/>';
	}


	$content .= '<input type="hidden" name="cf_working'.$no.'" id="cf_working'.$no.'" value="<span>'.rawurlencode($cformsSettings['form'.$no]['cforms'.$no.'_working']).'</span>"/>'.
				'<input type="hidden" name="cf_failure'.$no.'" id="cf_failure'.$no.'" value="<span>'.rawurlencode($cformsSettings['form'.$no]['cforms'.$no.'_failure']).'</span>"/>'.
				'<input type="hidden" name="cf_customerr'.$no.'" id="cf_customerr'.$no.'" value="'.rawurlencode($custom_error).'"/>';

	$content .= '</fieldset>';


    ### multi page form: reset
	$reset='';
    if( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_form'] && $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_reset'] )
		$reset = '<input tabindex="999" type="submit" name="resetbutton'.$no.'" id="resetbutton'.$no.'" class="resetbutton" value="' . $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_resettext'] . '" onclick="return confirm(\''.__('Note: This will reset all your input!', 'cforms2').'\')">';


    ### multi page form: back
	$back='';
    if( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_form'] && $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_back'] && !$cformsSettings['form'.$oldno]['cforms'.$no.'_mp']['mp_first'] )
		$back = '<input type="submit" name="backbutton'.$no.'" id="backbutton'.$no.'" class="backbutton" value="' . $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_backtext'] . '">';


	$content .= '<p class="cf-sb">'.$reset.$back.'<input type="submit" name="sendbutton'.$no.'" id="sendbutton'.$no.'" class="sendbutton" value="' . stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_submit_text'])) . '"'.$ajaxenabled.'/></p></form>';

	### either show message above or below
	$usermessage_text	= cforms2_check_default_vars($usermessage_text,$no);
	$usermessage_text	= cforms2_check_cust_vars($usermessage_text,$track);

	if( substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],1,1)=='y' && !($success && $cformsSettings['form'.$no]['cforms'.$no.'_hide']))
		$content .= '<div id="usermessage'.$no.'b" class="cf_info ' . $usermessage_class . $umc . '" >' . $usermessage_text . '</div>';

	return $content;
}


### some css for positioning the form elements
function cforms2_enqueue_scripts() {
	global $wp_query, $cformsSettings;

	### add content actions and filters
	$page_obj = $wp_query->get_queried_object();

	$exclude  = ($cformsSettings['global']['cforms_inexclude']['ex']=='1');
	$onPages  = str_replace(' ','',stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_inexclude']['ids'] )));
	$onPagesA = explode(',', $onPages);

	if( $onPages=='' || (in_array($page_obj->ID,$onPagesA) && !$exclude) || (!in_array($page_obj->ID,$onPagesA) && $exclude)){

		if( $cformsSettings['global']['cforms_no_css']<>'1' ) {
			wp_register_style( 'cforms2', plugin_dir_url(__FILE__) . 'styling/' . $cformsSettings['global']['cforms_css'], array(), CFORMS2_VERSION );
			wp_enqueue_style('cforms2');
		}

		### add calendar
		if( $cformsSettings['global']['cforms_datepicker']=='1' ){
			cforms2_enqueue_script_datepicker(stripslashes($cformsSettings['global']['cforms_dp_date']));
		}
		wp_register_script( 'cforms2', plugin_dir_url(__FILE__) . 'js/cforms.js', array('jquery'), CFORMS2_VERSION);
		wp_localize_script( 'cforms2', 'cforms2_ajax', array(
			'url'    => admin_url('admin-ajax.php'),
			'nonces' => array(
				'submitcform' => wp_create_nonce('submitcform')
			)
		) );
	    wp_enqueue_script('cforms2');
		
		wp_enqueue_style('dashicons');
	}
}


### custom routine to find last item
function cforms2_findlast( $haystack,$needle,$offset=null ){
	if( ($pos = strpos( strrev($haystack) , strrev($needle) , $offset)) === false ) return false;
    return strlen($haystack) - $pos - strlen($needle);
}


/**
 * replace placeholder by generated code
 * @deprecated since version 14.7.1
 */
function cforms2_insert( $content ) {
	global $cformsSettings;
	$newcontent='';

	$last=0;
	if ( ($a=strpos($content,'<!--cforms'))!==false ) {  ### only if form tag is present!

		$p_offset= 0;
		$part_content = substr( $content, 0, $a-$last );
		$p_open  = cforms2_findlast($part_content,'<p>');
		$p_close = cforms2_findlast($part_content,'</p>');

		### wrapped in <p> ?
		$p_offset = ($p_close < $p_open || ($p_open!==false && $p_close===false) ) ? $p_open : $a;

		$forms = $cformsSettings['global']['cforms_formcount'];

		$fns = array();
		for ($i=0;$i<$forms;$i++) {
			$no = ($i==0)?'':($i+1);
			$fns[sanitize_title_with_dashes($cformsSettings['form'.$no]['cforms'.$no.'_fname'])] = $i+1;
		}

		while( $a !== false ){

			$b = strpos($content,'-->',$a);

			$Fid = substr($content,$a+10,($b-$a-10));
			$Fname = '';

			if ( ($fQ=strpos($Fid,'"'))!==false )
				$Fname = sanitize_title_with_dashes(substr( $Fid, $fQ+1, strpos($Fid,'"',$fQ+1)-$fQ-1 ));

			$newcontent .= substr($content,$last,$p_offset-$last);

			if( $Fname !== '' ){
				$newcontent .= cforms2('',$fns[$Fname]);
			}else{
				$newcontent .= cforms2('',$Fid);
            }

			$p_open_after  = strpos($content,'<p>',$b);
			$p_close_after = strpos($content,'</p>',$b);

			### wrapped in <p> ?
			$b = ($p_close_after < $p_open_after || ($p_close_after!==false && $p_open_after===false)) ? $p_close_after+1 : $b;  //add'l +3 covered by $last = $b+3; !! :-)


			$a = strpos($content,'<!--cforms',$b);
			$last = $b+3;


			### next wrapping <p> tags
			$part_content = substr( $content, $last, $a-$last );
			$p_open  = cforms2_findlast($part_content,'<p>');
			$p_close = cforms2_findlast($part_content,'</p>');

			### wrapped in <p> ?
			$p_offset = ($p_close < $p_open) ? $a-(strlen($part_content)-$p_open) : $a;

		}
		$newcontent .= substr($content,$last);

		return $newcontent;
	}
	else
		return $content;
}


### build field_stat string from array (for custom forms)
function cforms2_build_fstat($f) {
    $cfarray = array();
    for($i=0; $i<count($f['label']); $i++) {
        if ( $f['type'][$i] == '') $f['type'][$i] = 'textfield';
        if ( $f['isreq'][$i] == '') $f['isreq'][$i] = '0';
        if ( $f['isemail'][$i] == '') $f['isemail'][$i] = '0';
        if ( $f['isclear'][$i] == '') $f['isclear'][$i] = '0';
        if ( $f['isdisabled'][$i] == '') $f['isdisabled'][$i] = '0';
        if ( $f['isreadonly'][$i] == '') $f['isreadonly'][$i] = '0';
        $cfarray[$i]=$f['label'][$i].'$#$'.$f['type'][$i].'$#$'.$f['isreq'][$i].'$#$'.$f['isemail'][$i].'$#$'.$f['isclear'][$i].'$#$'.$f['isdisabled'][$i].'$#$'.$f['isreadonly'][$i];
    }
    return $cfarray;
}


### inserts a cform anywhere you want
if (!function_exists('insert_cform')) {
function insert_cform($no='',$custom='',$c='') {

	if( !is_numeric($no) )
		$no = cforms2_check_form_name( $no );

	echo cforms2($custom,$no.$c);
}
}

function cforms2_shortcode($atts, $content) {
	if (empty($atts))
		$callform = $content;
	else
		$callform = array_pop( $atts );
	if (empty($callform))
		return '';
	return cforms2('',cforms2_check_form_name($callform));
}

### inserts a custom cform anywhere you want
if (!function_exists('insert_custom_cform')) {
	function insert_custom_cform($fields='',$no='') {
		insert_cform($no, $fields, '+');
	}
}


### check form names/id's
function cforms2_check_form_name($no) {
    $cformsSettings = get_option('cforms_settings');
	if( is_numeric($no) || $no=='' )
		return $no;

	$forms = $cformsSettings['global']['cforms_formcount'];

	for ($i=0;$i<$forms;$i++) {
		$no2 = ($i==0)?'':($i+1);
		if ( stripslashes($cformsSettings['form'.$no2]['cforms'.$no2.'_fname']) == $no )
			return $no2;
	}
	return '';
}


### cforms widget
function cforms2_widget_init() {
	global $cformsSettings;
	$cformsSettings = get_option('cforms_settings');
	require_once(plugin_dir_path(__FILE__) . 'lib_widget.php');
	register_widget('cforms2_widget');
}

### get # of submissions left (max subs)
function cforms2_get_submission_left($no) {
	global $wpdb, $cformsSettings;

	if ( $no==0 || $no==1 ) $no='';
	$max   = (int)$cformsSettings['form'.$no]['cforms'.$no.'_maxentries'];

	if( $max == '' || $max == 0 || $cformsSettings['global']['cforms_database']=='0' )
		return -1;

	$entries = $wpdb->get_row($wpdb->prepare("SELECT count(id) as submitted FROM {$wpdb->cformssubmissions} WHERE form_id=%s", $no));

	if( $max-$entries->submitted > 0)
		return ($max-$entries->submitted);
	else
		return 0;
}


function cforms2_localization () {
	load_plugin_textdomain( 'cforms2' );
}


### widget init
add_action('plugins_loaded', 'cforms2_localization' );
add_action('widgets_init', 'cforms2_widget_init');

$admin = is_admin();
$cfadmin = array_key_exists('QUERY_STRING', $_SERVER);
if ($cfadmin)
	$cfadmin = strpos($_SERVER['QUERY_STRING'], 'cforms') !== false;

### dashboard
if ( $cformsSettings['global']['cforms_showdashboard']=='1' && $cformsSettings['global']['cforms_database']=='1' ) {
	require_once(plugin_dir_path(__FILE__) . 'lib_dashboard.php');
}
### cforms specific stuff
if ( $cfadmin ) {
	require_once(plugin_dir_path(__FILE__) . 'lib_functions.php');
	add_action('init', 'cforms2_download');
	add_action('admin_enqueue_scripts', 'cforms2_admin_enqueue_scripts' );
}

require_once (plugin_dir_path(__FILE__) . 'my-functions-deprecated.php');
require_once (plugin_dir_path(__FILE__) . 'lib_ajax.php');
require_once (plugin_dir_path(__FILE__) . 'cforms-captcha.php');

function cforms2_field() {
	check_admin_referer( 'cforms2_field' );

	static $html5 = array(
		'html5color',
        'html5date',
        'html5datetime',
        'html5datetime-local',
        'html5email',
        'html5month',
        'html5number',
        'html5range',
        'html5search',
        'html5time',
        'html5url',
        'html5week',
        'html5tel'
	);
	static $fieldsetstart = array(
        'fieldsetstart',
		'fieldsetend'
	);
	static $checkbox = array(
        'ccbox',
        'checkbox'
	);
	static $selectbox = array(
        'emailtobox',
        'selectbox',
        'multiselectbox'
	);
	static $textfield = array(
		'upload',
		'datepicker',
		'textfield',
		'textarea',
		'pwfield',
		'hidden'
	);
	static $checkboxgroup = array(
        'checkboxgroup',
        'radiobuttons'
    );

	$type = $_REQUEST['type'];
	if (in_array($type, $html5))
		require ('include/html5field.php');
	else if (in_array($type, $checkbox))
		require ('include/checkbox.php');
	else if (in_array($type, $checkboxgroup))
		require ('include/checkboxgroup.php');
	else if (in_array($type, $fieldsetstart))
		require ('include/fieldsetstart.php');
	else if (in_array($type, $selectbox))
		require ('include/selectbox.php');
	else if (in_array($type, $textfield))
		require ('include/textfield.php');
	else if ($type == 'textonly')
		require ('include/textonly.php');
	else {
		$captchas = cforms2_get_pluggable_captchas();
		if (array_key_exists($type, $captchas))
			$captchas[$type]->render_settings();
	}
	die();
}

### other admin stuff
if ( is_admin() ) {
	require_once(plugin_dir_path(__FILE__) . 'lib_functions.php');
	add_action('admin_menu', 'cforms2_menu');
	add_action( 'wp_ajax_cforms2_field', 'cforms2_field' );

	### admin ajax
	require_once (plugin_dir_path(__FILE__) . 'include/installpreset.php');

	require_once (plugin_dir_path(__FILE__) . 'include/lib_database_deleteentries.php');
	require_once (plugin_dir_path(__FILE__) . 'include/lib_database_deleteentry.php');
	require_once (plugin_dir_path(__FILE__) . 'include/lib_database_dlentries.php');
	require_once (plugin_dir_path(__FILE__) . 'include/lib_database_getentries.php');
	require_once (plugin_dir_path(__FILE__) . 'include/lib_database_overview.php');

} ### if admin


### admin bar
if ( isset($_GET['page']) ) {
	$plugin_page = stripslashes($_GET['page']);
	$plugin_page = plugin_basename($plugin_page);
	if( strpos($plugin_page, 'cforms-options.php') )
		add_action('admin_bar_menu', 'cforms2_add_items_options',999);
	else if( strpos($plugin_page, 'cforms-global-settings.php') )
		add_action('admin_bar_menu', 'cforms2_add_items_global',999);
}

function cforms2_add_items_global( $admin_bar ){
	
	global $wpdb;

	cforms2_add_admin_bar_root($admin_bar, 'cforms-bar', 'cforms Admin');
	
	cforms2_add_admin_bar_item($admin_bar, 'cforms-showinfo', __('Produce debug output', 'cforms2'), __('Outputs -for debug purposes- all cforms settings', 'cforms2'), 'jQuery("#cfbar-showinfo").trigger("click"); return false;');
	cforms2_add_admin_bar_item($admin_bar, 'cforms-dellAllButton', __('Uninstalling / removing cforms', 'cforms2'), __('Be careful here...', 'cforms2'), 'jQuery("#cfbar-deleteall").trigger("click"); return false;');

	if ( $wpdb->get_var("show tables like '$wpdb->cformssubmissions'") == $wpdb->cformssubmissions ) 
		cforms2_add_admin_bar_item($admin_bar, 'cforms-deletetables', __('Delete cforms tracking tables', 'cforms2'), __('Be careful here...', 'cforms2'), 'if ( confirm("'.__('Do you really want to erase all collected data?', 'cforms2').'") ) jQuery("#deletetables").trigger("click"); return false;');

	cforms2_add_admin_bar_item($admin_bar, 'cforms-backup', __('Backup / restore all settings', 'cforms2'), __('Better safe than sorry ;)', 'cforms2'), 'jQuery("#backup").trigger("click"); return false;');
	
	cforms2_add_admin_bar_item($admin_bar, 'cforms-SubmitOptions', __('Save & update form settings', 'cforms2'), '', 'document.mainform.action="#"+getFieldset(focusedFormControl); jQuery("#cfbar-SubmitOptions").trigger("click"); return false;', 'root-default');

}

function cforms2_add_items_options( $admin_bar ){

	$cfo = get_option('cforms_settings');

	cforms2_add_admin_bar_root($admin_bar,'cforms-bar', 'cforms Admin');
	
	cforms2_add_admin_bar_item($admin_bar,'cforms-addbutton', __('Add new form', 'cforms2'), __('Adds a new form with default values', 'cforms2'), 'jQuery("#cfbar-addbutton").trigger("click"); return false;');
	cforms2_add_admin_bar_item($admin_bar,'cforms-dupbutton', __('Duplicate current form', 'cforms2'), __('Clones the current form', 'cforms2'), 'jQuery("#cfbar-dupbutton").trigger("click"); return false;');
	if ( (int)$cfo['global']['cforms_formcount'] > 1)
		cforms2_add_admin_bar_item($admin_bar,'cforms-delbutton', __('Delete current form (!)', 'cforms2'), __('Clicking this button WILL delete this form', 'cforms2'), 'if ( confirm("'.__('This will delete the current form!', 'cforms2').'")) jQuery("#cfbar-delbutton").trigger("click"); return false;');

	cforms2_add_admin_bar_item($admin_bar,'cforms-preset', __('Install a form preset', 'cforms2'), __('Pick a form preset from the repository', 'cforms2'), 'jQuery("#preset").trigger("click"); return false;');
	cforms2_add_admin_bar_item($admin_bar,'cforms-backup', __('Backup / restore this form only', 'cforms2'), __('Better safe than sorry ;)', 'cforms2'), 'jQuery("#backup").trigger("click"); return false;');

	cforms2_add_admin_bar_item($admin_bar,'cforms-SubmitOptions', __('Save & update form settings', 'cforms2'), '', 'document.mainform.action="#"+getFieldset(focusedFormControl); jQuery("#cfbar-SubmitOptions").trigger("click"); return false;', 'root-default');

}

### attaching to filters
add_action('init', 'cforms2_delete_db_and_deactivate');
add_action('wp_enqueue_scripts', 'cforms2_enqueue_scripts');
add_filter('the_content', 'cforms2_insert', 101);
add_shortcode('cforms' , 'cforms2_shortcode' );
