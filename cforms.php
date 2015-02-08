<?php
/*
 * Copyright (c) 2006-2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
 * Copyright (c) 2014      Bastian Germann
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
 * Version: 14.8.1
 * Text Domain: cforms
 * Domain Path: ____Plugin_Localization
 */

global $localversion;
$localversion = '14.8.1';

### db settings
global $wpdb;

$cformsSettings				= get_option('cforms_settings');
$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

require_once(plugin_dir_path(__FILE__) . 'lib_functions.php');
require_once(plugin_dir_path(__FILE__) . 'lib_activate.php');



### activate cforms
function cforms2_activate() {

	$role = get_role('administrator');
	if(!$role->has_cap('manage_cforms')) {
		$role->add_cap('manage_cforms');
	}
	if(!$role->has_cap('track_cforms')) {
		$role->add_cap('track_cforms');
	}
    cforms2_setup_db();
}
// TODO check if this is run when updated without explicitly activating
add_action('activate_' . plugin_basename(__FILE__), 'cforms2_activate' );



### settings corruputed?
if ( !is_array($cformsSettings) ){
	add_action('admin_menu', 'cforms2_settings_corrupted');
    return;
}
function cforms2_settings_corrupted() {
	$tmp = plugin_dir_path(dirname(__FILE__));

	add_menu_page(__('cformsII', 'cforms'), __('cformsII', 'cforms'), 'manage_cforms', $tmp.'cforms-corrupted.php', '', plugin_dir_url(__FILE__).'images/cformsicon.png' );
	add_submenu_page($tmp.'cforms-corrupted.php', __('Corrupted Settings', 'cforms'), __('Corrupted Settings', 'cforms'), 'manage_cforms', $tmp.'cforms-corrupted.php' );

    add_action('admin_enqueue_scripts', 'cforms2_enqueue_style_admin' );
}


require_once (plugin_dir_path(__FILE__) . 'lib_email.php');
require_once (plugin_dir_path(__FILE__) . 'lib_aux.php');
require_once (plugin_dir_path(__FILE__) . 'lib_editor.php');


### session control for multi-page form
add_action('template_redirect', 'cforms2_start_session');

function cforms2_start_session() {
	session_cache_limiter('private, must-revalidate');
	session_cache_expire(0);
	if ( !session_id() ){
		session_start();
		### debug
		cforms2_dbg( "After session (".session_id().")start: ".print_r($_SESSION,1) );
	}
}



###
### main function
###
function cforms2($args = '',$no = '') {

	global $subID, $track, $cformsSettings, $trackf, $send2author;

    $oldno = ($no=='1')?'':$no;  ### remeber old val, to reset session when in new MP form

    ##debug
    cforms2_dbg("Original form on page #$oldno");

	### multi page form: overwrite $no
    $isWPcommentForm = (substr($cformsSettings['form'.$oldno]['cforms'.$oldno.'_tellafriend'],0,1)=='2');
    $isMPform = $cformsSettings['form'.$oldno]['cforms'.$oldno.'_mp']['mp_form'];
    $isTAF = substr($cformsSettings['form'.$oldno]['cforms'.$oldno.'_tellafriend'],0,1);

	##debug
    cforms2_dbg("Comment form = $isWPcommentForm");
    cforms2_dbg("Multi-page form = $isMPform");
    cforms2_dbg("PHP Session = ".(isset($_SESSION)?"yes":"no").$_SESSION['cforms']['current'] );

	if( $isMPform && is_array($_SESSION['cforms']) && $_SESSION['cforms']['current']>0 && !$isWPcommentForm ){
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
	$all_valid = 1;
	$off=0;
	$fieldsetnr=1;

	$c_errflag=false;
	$custom_error='';
	$usermessage_class='';


	$user = wp_get_current_user();


    ### non Ajax method
    if( isset($_REQUEST['sendbutton'.$no]) ) {
		require_once (plugin_dir_path(__FILE__) . 'lib_nonajax.php');
		$usermessage_class = $all_valid?' success':' failure';
	}

    ### called from lib_WPcomments ?
	if ( $isWPcommentForm && $send2author )
		return $all_valid;




	###
	###
	### paint form
	###
	###
	$success=false;

    ###  fix for WP Comment (loading after redirect)
	if ( isset($_GET['cfemail']) && $isWPcommentForm ){
		$usermessage_class = ' success';
		$success=true;
		if ( $_GET['cfemail']=='sent' ){
			$usermessage_text = preg_replace ( '|\r\n|', '<br />', stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_success']) );
		} elseif ( $_GET['cfemail']=='posted' ){
			$usermessage_text = preg_replace ( '|\r\n|', '<br />', stripslashes($cformsSettings['global']['cforms_commentsuccess']) );
		} else {
			$usermessage_class = ' failure';
			$success=false;		
		}
	}

	### either show info message above or below
	$usermessage_text	= cforms2_check_default_vars($usermessage_text,$no);
	$usermessage_text	= cforms2_check_cust_vars($usermessage_text,$track);
	### logic: possibly change usermessage
	if ( function_exists('my_cforms_logic') )
	    $usermessage_text = my_cforms_logic($trackf, $usermessage_text,'successMessage');

   	$umc = ($usermessage_class<>''&&$no>1)?' '.$usermessage_class.$no:'';

    ##debug
    cforms2_dbg("User info for form #$no");

	### where to show message
	if( substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],0,1)=='y' ) {
		$content .= '<div id="usermessage'.$no.'a" class="cf_info' . $usermessage_class . $umc .' ">' . $usermessage_text . '</div>';
		$actiontarget = 'a';
 	} else if ( substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],1,1)=='y' )
		$actiontarget = 'b';


	### multi page form: overwrite $no, move on to next form
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

        }

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
	else if( $isWPcommentForm )
		$action = admin_url('admin-ajax.php'); ### re-route and use WP comment processing
 	else
		$action = cforms2_get_current_page() . '#usermessage'. $no . $actiontarget;


	$enctype = $cformsSettings['form'.$no]['cforms'.$no.'_formaction'] ? 'enctype="application/x-www-form-urlencoded"':'enctype="multipart/form-data"';

	### start with form tag
	$content .= '<form '.$enctype.' action="' . $action . '" method="post" class="cform ' . sanitize_title_with_dashes($cformsSettings['form'.$no]['cforms'.$no.'_fname']). ' ' .( $cformsSettings['form'.$no]['cforms'.$no.'_dontclear']?' cfnoreset':'' ). '" id="cforms'.$no.'form">';


    ### Session item counter (for default values)
    $sItem=1;

	### start with no fieldset
	$fieldsetopen = false;
	$verification = false;

	$captcha = false;
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


		### ommit certain fields
		if( in_array($field_type,array('cauthor','url','email')) && $user->ID )
			continue;
			
 
		### check for html5 attributes
	    $obj = explode('|html5:', $field_name,2);
		$html5 = ($obj[1]<>'') ? preg_split('/\x{00A4}/u',$obj[1], -1) : '';

		###debug
		cforms2_dbg("\t\t html5 check, settings = ".print_r($html5,1));
		
		### check for custom err message and split field_name
	    $obj = explode('|err:', $obj[0],2);
	    $fielderr = $obj[1];
		
		###debug
		cforms2_dbg("\t adding $field_type field: $field_name");
		
		if ( $fielderr <> '')	{
		    switch ( $field_type ) {
			    case 'upload':
					$custom_error .= 'cf_uploadfile' . $no . '-'. $i . '$#$'.$fielderr.'|';
	    			break;

			    case 'captcha':
					$custom_error .= 'cforms_captcha' . $no . '$#$'.$fielderr.'|';
	    			break;

			    case 'verification':
					$custom_error .= 'cforms_q'. $no . '$#$'.$fielderr.'|';
	    			break;

				case "cauthor":
				case "url":
				case "email":
				case "comment":
					$custom_error .= $field_type . '$#$'.$fielderr.'|';
	    			break;

			    default:
    				preg_match('/^([^#\|]*).*/',$field_name,$input_name);
    				if ( strpos($input_name[1],'[id:')>0 )
    					preg_match ('/\[id:(.+)\]/',$input_name[1],$input_name);

					$custom_error .= ($cformsSettings['form'.$no]['cforms'.$no.'_customnames']=='1')?cforms2_sanitize_ids($input_name[1]):'cf'.$no.'_field_'.$i;
					$custom_error .= '$#$'.$fielderr.'|';
	    			break;
		    }
		}


		### check for title attrib
	    $obj = explode('|title:', $obj[0],2);
		$fieldTitle = ($obj[1]<>'')?' title="'.str_replace('"','&quot;',stripslashes($obj[1])).'"':'';

		###debug
		cforms2_dbg("\t\t title check, obj[0] = ".$obj[0]);
		

		### special treatment for selectboxes
		if (  in_array($field_type,array('multiselectbox','selectbox','radiobuttons','send2author','checkbox','checkboxgroup','ccbox','emailtobox'))  ){

			$chkboxClicked = array();
			if (  in_array($field_type,array('checkbox','ccbox')) && strpos($obj[0],'|set:')>1 ){
				$chkboxClicked = explode('|set:', stripslashes($obj[0]) );
				$obj[0] = $chkboxClicked[0];
			}
			
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


		$labelclass='';
		### visitor verification
		if ( !$verification && $field_type == 'verification' ) {
			srand(microtime()*1000003);
        	$qall = explode( "\r\n", $cformsSettings['global']['cforms_sec_qa'] );
			$n = rand(0,(count(array_keys($qall))-1));
			$q = $qall[ $n ];
			$q = explode( '=', $q );  ### q[0]=qestion  q[1]=answer
			$field_name = stripslashes(htmlspecialchars($q[0]));
			$labelclass = ' class="secq"';
		}
		else if ( $field_type == 'captcha' )
			$labelclass = ' class="seccap"';


		$defaultvalue = '';
		### setting the default val & regexp if it exists
		if ( ! in_array($field_type,array('fieldsetstart','fieldsetend','radiobuttons','send2author','checkbox','checkboxgroup','ccbox','emailtobox','multiselectbox','selectbox','verification')) ) {

		    ### check if default val & regexp are set
		    $obj = explode('|', $obj[0],3);

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

		switch ($field_type){
			case 'verification':
				if( is_user_logged_in() && $cformsSettings['global']['cforms_captcha_def']['foqa']<>'1' )
					continue(2);
				$input_id = $input_name = 'cforms_q'.$no;
				break;
			case 'captcha':
				if( is_user_logged_in() && $cformsSettings['global']['cforms_captcha_def']['fo']<>'1' )
					continue(2);
				$input_id = $input_name = 'cforms_captcha'.$no;
				break;
			case 'upload':
				$input_id = $input_name = 'cf_uploadfile'.$no.'-'.$i;
				$field_class = 'upload';
				break;
			case "send2author":
			case "email":
			case "cauthor":
			case "url":
				$input_id = $input_name = $field_type;
			case "datepicker":
			case "yourname":
			case "youremail":
			case "friendsname":
			case "friendsemail":
			case "textfield":
			case "pwfield":
				$field_class = 'single';
				break;
			case "hidden":
				$field_class = 'hidden';
				break;
			case 'comment':
				$input_id = $input_name = $field_type;
				$field_class = 'area';
				break;
			case 'textarea':
				$field_class = 'area';
				break;
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
			if ( $validations[$i]==1 )
				$field_class .= '';
			else{
				$field_class .= ' cf_error';

				### enhanced error display
				if(substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],2,1)=="y")
					$liERR = 'cf_li_err';
				if(substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],3,1)=="y")
					$insertErr = ($fielderr<>'')?'<ul class="cf_li_text_err"><li>'.stripslashes($fielderr).'</li></ul>':'';
			}


			if ( $field_type == 'multiselectbox' || $field_type == 'checkboxgroup' ){
				$field_value = $_REQUEST[$input_name];  ### in this case it's an array! will do the stripping later
			}
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
		if( ! in_array($field_type,array('hidden','textonly','fieldsetstart','fieldsetend','ccbox','checkbox','checkboxgroup','send2author','radiobuttons')) )
			$content .= '<li'.$liID.' class="'.$liERR.'">'.$insertErr.'<label' . $labelID . ' for="'.$input_id.'"'. $labelclass . '><span>' . stripslashes(($field_name)) . '</span></label>';


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
		$force_checked = false;
		$cookieset = '';

		switch($field_type) {

			case "upload":
	  			$upload=true;  ### set upload flag for ajax suppression!
				$field = '<input' . $readonly.$disabled . ' type="file" name="cf_uploadfile'.$no.'[]" id="cf_uploadfile'.$no.'-'.$i.'" class="cf_upload ' . $field_class . '"'.$fieldTitle.'/>';
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

			case "verification":
				$field = '<input type="text" name="'.$input_name.'" id="cforms_q'.$no.'" class="secinput ' . $field_class . '" value=""'.$fieldTitle.'/>';
		    	$verification=true;
				break;

			case "captcha":
				$field = '<input type="text" name="'.$input_name.'" id="cforms_captcha'.$no.'" class="secinput' . $field_class . '" value=""'.$fieldTitle.'/>'.
						 '<img id="cf_captcha_img'.$no.'" class="captcha" src="#" alt=""/><script type="text/javascript">jQuery(function() {reset_captcha('.$no.');});</script>'.
						 '<a title="'.__('reset captcha image', 'cforms').'" href="javascript:reset_captcha(\''.$no.'\')"><img class="captcha-reset" src="'.plugin_dir_url(__FILE__).'images/spacer.gif" alt="Captcha"/></a>';
		    	$captcha=true;
				break;

			case "cauthor":
				$cookieset = 'comment_author_'.COOKIEHASH;
			case "url":
				$cookieset = ($cookieset=='')?'comment_author_url_'.COOKIEHASH:$cookieset;
			case "email":
				$cookieset = ($cookieset=='')?'comment_author_email_'.COOKIEHASH:$cookieset;
				$field_value = ( $_COOKIE[$cookieset]<>'' ) ? $_COOKIE[$cookieset] : $field_value;
			
			case "datepicker":
			case "yourname":
			case "youremail":
			case "friendsname":
			case "friendsemail":
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

				$field = '<input' . $h5.$readonly.$disabled . ' type="'.$type.'" name="'.$input_name.'" id="'.$input_id.'" class="' . $field_class . '" value="' . $field_value  . '"'.$onfocus.$fieldTitle.'/>';
				  if ( $reg_exp<>'' )
	           		 $field .= '<input type="hidden" name="'.$input_name.'_regexp" id="'.$input_id.'_regexp" value="'.$reg_exp.'"'.$fieldTitle.'/>';

				$field .= $dp;
				break;

			case "hidden":

				$field_value = cforms2_check_post_vars($field_value);
				$field_value = cforms2_check_default_vars($field_value,$no);

                if ( preg_match('/^<([a-zA-Z0-9]+)>$/',$field_value,$getkey) )
                    $field_value = $_GET[$getkey[1]];

				$field .= '<li class="cf_hidden"><input type="hidden" class="cfhidden" name="'.$input_name.'" id="'.$input_id.'" value="' . $field_value  . '"'.$fieldTitle.'/></li>';
				break;

			case "comment":
			case "textarea":
			    $onfocus = $field_clear?' onfocus="clearField(this)" onblur="setField(this)"' : '';

				$field = '<textarea' . $readonly.$disabled . ' cols="30" rows="8" name="'.$input_name.'" id="'.$input_id.'" class="' . $field_class . '"'. $onfocus.$fieldTitle.'>' . $field_value  . '</textarea>';
				  if ( $reg_exp<>'' )
	           		 $field .= '<input type="hidden" name="'.$input_name.'_regexp" id="'.$input_id.'_regexp" value="'.$reg_exp.'"'.$fieldTitle.'/>';
				break;

	   		case "ccbox":
			case "checkbox":
				if ( !$all_valid || ($all_valid && $cformsSettings['form'.$no]['cforms'.$no.'_dontclear']) || ($isMPform && is_array($_SESSION['cforms']['cf_form'.$no])) ) //exclude MP! if first time on the form = array = null
					$preChecked = ( $field_value && $field_value<>'' )? ' checked="checked"':'';  // for MPs 
				else
					$preChecked = ( strpos($chkboxClicked[1],'true') !== false ) ? ' checked="checked"':'';  // $all_valid = user choice prevails

				$err='';
				if( !$all_valid && $validations[$i]<>1 )
					$err = ' cf_errortxt';

			    $opt = explode('|', $field_name,2);
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
					
				$field = $before . '<input' . $readonly.$disabled . ' type="checkbox" name="'.$input_name.'" id="'.$input_id.'" class="cf-box-' . $ba . $field_class . '"'.$val.$fieldTitle.$preChecked.'/>' . $after;

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
							$field .= '<input' . $readonly.$disabled . ' type="checkbox" id="'. $input_id .'-'. $id . '" name="'. $input_name . $brackets .'" value="'.$opt[1].'" '.$checked.' class="cf-box-b"'.$fieldTitle.'/>'.
									  '<label' . $labelIDx . ' for="'. $input_id .'-'. ($id++) . '" class="cf-group-after"><span>'.$opt[0] . "</span></label>";

					}
				$field .= '</li>';
				break;


			case "multiselectbox":
				$field .= '<select' . $readonly.$disabled . ' multiple="multiple" name="'.$input_name.'[]" id="'.$input_id.'" class="cfselectmulti ' . $field_class . '"'.$fieldTitle.'>';
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
				$field = '<select' . $readonly.$disabled . ' name="'.$input_name.'" id="'.$input_id.'" class="cformselect' . $field_class . '" '.$fieldTitle.'>';
				array_shift($options); $jj=$j=0;

				foreach( $options as $option  ) {

					### supporting names & values
					$optPreset = explode('|set:', $option );
				    $opt = explode('|', $optPreset[0],2);
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

			case "send2author":
				$force_checked = ( strpos($field_stat[0],'|set:')===false )? true:false;
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
						if ( $opt[1]=='' ) $opt[1] = $opt[0];

						if( $field_value == '' ) {

								if ( strpos($radioPreset[1],'true')!==false || ($force_checked && $id==1))
								    $checked = ' checked="checked"';

						}	else
								if ( $opt[1]==$field_value ) $checked = ' checked="checked"';

						if ( $labelID<>'' ) $labelIDx = substr($labelID,0,-1) . $id . '"';

						if ( $opt[0]=='' )
							$field .= '<br />';
						else
							$field .=
								  '<input' . $readonly.$disabled . ' type="radio" id="'. $input_id .'-'. $id . '" name="'.$input_name.'" value="'.$opt[1].'"'.$checked.' class="cf-box-b' . ($field_required?' fldrequired':'') .'"'.$fieldTitle.'/>'.
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
		if ( ! in_array($field_type,array('hidden','fieldsetstart','fieldsetend','radiobuttons','checkbox','checkboxgroup','ccbox','textonly','send2author')) )
			$content .= '</li>';

	} ### all fields


	### close any open tags
	if ( $ol )
		$content .= '</ol>';
	if ( $fieldsetopen )
		$content .= '</fieldset>';


	### rest of the form
	if ( $cformsSettings['form'.$no]['cforms'.$no.'_ajax']=='1' && !$upload && !$custom && !$alt_action )
		$ajaxenabled = ' onclick="return cforms_validate(\''.$no.'\', false)"';
	else if ( ($upload || $custom || $alt_action) && $cformsSettings['form'.$no]['cforms'.$no.'_ajax']=='1' )
		$ajaxenabled = ' onclick="return cforms_validate(\''.$no.'\', true)"';
	else
		$ajaxenabled = '/>'
			. '<input type="hidden" name="action" value="submitcomment_direct"/>'
			. '<input type="hidden" name="_wpnonce" value="' . wp_create_nonce('submitcomment_direct') . '"';


	### just to appease html "strict"
	$content .= '<fieldset class="cf_hidden"><legend>&nbsp;</legend>';


	### if visitor verification turned on:
	if ( $verification )
		$content .= '<input type="hidden" name="cforms_a'.$no.'" id="cforms_a'.$no.'" value="' . md5(rawurlencode(strtolower($q[1]))) . '"/>';

	### custom error
	$custom_error=substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],2,1).substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],3,1).substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],4,1).$custom_error;


	### TAF or WP comment or Extra Fields
	if ( (int)$isTAF > 0 ){

		$nono = $isWPcommentForm?'':$no;

		if ( $isWPcommentForm )
			$content .= '<input type="hidden" name="comment_parent" id="comment_parent" value="'.( ($_REQUEST['replytocom']<>'')?$_REQUEST['replytocom']:'0' ).'"/>';

		$content .= '<input type="hidden" name="comment_post_ID'.$nono.'" id="comment_post_ID'.$nono.'" value="' . ( isset($_GET['pid'])? $_GET['pid'] : get_the_ID() ) . '"/>' .
					'<input type="hidden" name="cforms_pl'.$no.'" id="cforms_pl'.$no.'" value="' . ( isset($_GET['pid'])? get_permalink($_GET['pid']) : get_permalink() ) . '"/>';
	}


	$content .= '<input type="hidden" name="cf_working'.$no.'" id="cf_working'.$no.'" value="<span>'.rawurlencode($cformsSettings['form'.$no]['cforms'.$no.'_working']).'</span>"/>'.
				'<input type="hidden" name="cf_failure'.$no.'" id="cf_failure'.$no.'" value="<span>'.rawurlencode($cformsSettings['form'.$no]['cforms'.$no.'_failure']).'</span>"/>'.
				'<input type="hidden" name="cf_codeerr'.$no.'" id="cf_codeerr'.$no.'" value="<span>'.rawurlencode($cformsSettings['global']['cforms_codeerr']).'</span>"/>'.
				'<input type="hidden" name="cf_customerr'.$no.'" id="cf_customerr'.$no.'" value="'.rawurlencode($custom_error).'"/>'.
				'<input type="hidden" name="cf_popup'.$no.'" id="cf_popup'.$no.'" value="'.$cformsSettings['form'.$no]['cforms'.$no.'_popup'].'"/>';

	$content .= '</fieldset>';


    ### multi page form: reset
	$reset='';
    if( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_form'] && $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_reset'] )
		$reset = '<input tabindex="999" type="submit" name="resetbutton'.$no.'" id="resetbutton'.$no.'" class="resetbutton" value="' . $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_resettext'] . '" onclick="return confirm(\''.__('Note: This will reset all your input!', 'cforms').'\')">';


    ### multi page form: back
	$back='';
    if( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_form'] && $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_back'] && !$cformsSettings['form'.$oldno]['cforms'.$no.'_mp']['mp_first'] )
		$back = '<input type="submit" name="backbutton'.$no.'" id="backbutton'.$no.'" class="backbutton" value="' . $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_backtext'] . '">';


	$content .= '<p class="cf-sb">'.$reset.$back.'<input type="submit" name="sendbutton'.$no.'" id="sendbutton'.$no.'" class="sendbutton" value="' . stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_submit_text'])) . '"'.$ajaxenabled.'/></p>';
	if ($isWPcommentForm) {
		ob_start();
		do_action( 'comment_form', get_the_ID() );
		$content .= ob_get_clean();
	}
	$content .= '</form>';

	### either show message above or below
	$usermessage_text	= cforms2_check_default_vars($usermessage_text,$no);
	$usermessage_text	= cforms2_check_cust_vars($usermessage_text,$track);

	if( substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],1,1)=='y' && !($success&&$cformsSettings['form'.$no]['cforms'.$no.'_hide']))
		$content .= '<div id="usermessage'.$no.'b" class="cf_info ' . $usermessage_class . $umc . '" >' . $usermessage_text . '</div>';

	### debug
	cforms2_dbg( "(cforms) Last stop...".print_r($_SESSION,1) );

	return $content;
}


### some css for positioning the form elements
function cforms2_enqueue_scripts() {
	global $wp_query, $localversion, $cformsSettings;

	### add content actions and filters
	$page_obj = $wp_query->get_queried_object();

	$exclude  = ($cformsSettings['global']['cforms_inexclude']['ex']=='1');
	$onPages  = str_replace(' ','',stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_inexclude']['ids'] )));
	$onPagesA = explode(',', $onPages);

	if( $onPages=='' || (in_array($page_obj->ID,$onPagesA) && !$exclude) || (!in_array($page_obj->ID,$onPagesA) && $exclude)){

		if( $cformsSettings['global']['cforms_no_css']<>'1' ) {
			wp_register_style( 'cforms2', plugin_dir_url(__FILE__) . 'styling/' . $cformsSettings['global']['cforms_css'] );
			wp_enqueue_style('cforms2');
		}

		### add calendar
		if( $cformsSettings['global']['cforms_datepicker']=='1' ){
			cforms2_enqueue_script_datepicker($localversion);
		}
        wp_register_script( 'jquery-md5', plugin_dir_url(__FILE__) . "js/jquery.md5.js", array('jquery'), '1.2.1');
		wp_register_script( 'cforms2', plugin_dir_url(__FILE__) . 'js/cforms.js', array('jquery', 'jquery-md5'), $localversion);
		wp_localize_script( 'cforms2', 'cforms2_ajax', array(
			'url'    => admin_url('admin-ajax.php'),
			'nonces' => array(
				'reset_captcha' => wp_create_nonce('cforms2_reset_captcha'),
				'submitcomment' => wp_create_nonce('submitcomment')
			)
		) );
	    wp_enqueue_script('cforms2');
		
	}
}


### custom routine to find last item
function cforms2_findlast( $haystack,$needle,$offset=NULL ){
	if( ($pos = strpos( strrev($haystack) , strrev($needle) , $offset)) === false ) return false;
    return strlen($haystack) - $pos - strlen($needle);
}


/**
 * replace placeholder by generated code
 * @deprecated since version 14.7.1
 */
function cforms2_insert( $content ) {
	global $cformsSettings; $newcontent='';

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
			  if ( cforms2_check_for_taf( $fns[$Fname],cforms2_cfget_pid() ) )
  				$newcontent .= cforms2('',$fns[$Fname]);
			}else{
			  if ( cforms2_check_for_taf( $Fid,cforms2_cfget_pid() ) )
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

	$pid = cforms2_cfget_pid();

	if( !is_numeric($no) )
		$no = cforms2_check_form_name( $no );

	if ( !$pid )
		echo cforms2($custom,$no.$c);
	else
		echo cforms2_check_for_taf($no,$pid)?cforms2($custom,$no.$c):'';
}
}

function cform2_shortcode($atts, $content) {
	if (empty($atts))
		$callform = $content;
	else
		$callform = array_pop( $atts );
	if (empty($callform))
		return '';
	return insert_cform($callform);
}

### GET $pid
function cforms2_cfget_pid() {
	global $post;

	if ( isset($_GET['pid']) )
		$pid = $_GET['pid'];
	else if ($post->ID == 0)
		$pid = false;
	else
		$pid = $post->ID;

  return $pid;
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


### check if t-f-a is set
function cforms2_check_for_taf($no,$pid) {
	global $cformsSettings;

	if ( substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)<>'1')
		return true;

  if( is_single() || in_the_loop() ){
  	$tmp = get_post_custom($pid);
  	return ( $tmp["tell-a-friend"][0] == '1' )?true:false;
  }else
    return true;
}


### public function: check if post is t-a-f enabled
if (!function_exists('is_tellafriend')) {
	function is_tellafriend($pid) {
		$tmp = get_post_custom($pid);
		return ($tmp["tell-a-friend"][0]=='1')?true:false;
	}
}


### WP 2.7 admin menu hook
function cforms2_post_box(){
	global $tafstring;
	echo $tafstring;
}


function cforms2_add_cforms_post_boxes(){
	add_meta_box('cformspostbox', __('cforms Tell-A-Friend', 'cforms'), 'cforms2_post_box', 'post', 'normal', 'high');
	add_meta_box('cformspostbox', __('cforms Tell-A-Friend', 'cforms'), 'cforms2_post_box', 'page', 'normal', 'high');
}


### Add Tell A Friend processing
function cforms2_enable_tellafriend($post_ID) {

	if ( isset($_POST['action']) && ($_POST['action']=='autosave' || $_POST['action']=='inline-save')  )
    	return;

	$tellafriend_status = isset($_POST['tellafriend']);

	if($tellafriend_status && intval($post_ID) > 0)
		add_post_meta($post_ID, 'tell-a-friend', '1', true);
	else if ( isset($_POST['post_ID']) )
		delete_post_meta($post_ID, 'tell-a-friend');
}


### cforms widget
function cforms2_widget_init() {
	global $cformsSettings;
	$cformsSettings = get_option('cforms_settings');
	require_once(plugin_dir_path(__FILE__) . 'lib_widget.php');
	register_widget('cforms2_widget');
}

### get # of submission left (max subs)
function cforms2_get_submission_left($no='') {
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
	// For compatibility reasons, use slug cforms, not cforms2.
	load_plugin_textdomain( 'cforms',  false, plugin_dir_path( plugin_basename( __FILE__ ) ) . '____Plugin_Localization/' );
}

### add actions
global $tafstring;

### widget init
add_action('plugins_loaded', 'cforms2_localization' );
add_action('widgets_init', 'cforms2_widget_init');

$admin = is_admin();
$cfadmin = strpos($_SERVER['QUERY_STRING'],plugin_dir_path(plugin_basename(__FILE__)).'cforms') !== false;

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

### public ajax
require_once ('lib_ajax.php');
require_once ('cforms-captcha.php');

### other admin stuff
if ( is_admin() ) {
	require_once(plugin_dir_path(__FILE__) . 'lib_functions.php');
	add_action('admin_menu', 'cforms2_menu');

	### Check all forms for TAF and set variables
	for ( $i=1;$i<=$cformsSettings['global']['cforms_formcount'];$i++ ) {
		$tafenabled = ( substr($cformsSettings['form'.(($i=='1')?'':$i)]['cforms'.(($i=='1')?'':$i).'_tellafriend'],0,1)=='1') ? true : false;
		if ( $tafenabled ) break;
	}
	$tafform = ($i==1)?'':$i;

	if ( $tafenabled ){
		$edit_post = intval($_GET['post']);
		$tmp = get_post_custom($edit_post);
		$taf = $tmp["tell-a-friend"][0];

		$tafchk = ($taf=='1' || ($edit_post=='' && substr($cformsSettings['form'.$tafform]['cforms'.$tafform.'_tellafriend'],1,1)=='1') )?'checked="checked"':'';

		$tafstring = '<label for="tellafriend" class="selectit"><input type="checkbox" id="tellafriend" name="tellafriend" value="1"'. $tafchk .'/>&nbsp;'. __('T-A-F enable this post/page', 'cforms').'</label>';

		### add admin boxes
		add_action('admin_menu', 'cforms2_add_cforms_post_boxes');
		add_action('save_post', 'cforms2_enable_tellafriend');

	} ### if tafenabled

	### admin ajax
	require_once ('js/include/installpreset.php');

	require_once ('js/include/checkbox.php');
	require_once ('js/include/checkboxgroup.php');
	require_once ('js/include/fieldsetstart.php');
	require_once ('js/include/html5field.php');
	require_once ('js/include/selectbox.php');
	require_once ('js/include/textfield.php');
	require_once ('js/include/textonly.php');

	require_once ('js/include/lib_database_deleteentries.php');
	require_once ('js/include/lib_database_deleteentry.php');
	require_once ('js/include/lib_database_dlentries.php');
	require_once ('js/include/lib_database_getentries.php');
	require_once ('js/include/lib_database_overview.php');
	require_once ('js/include/lib_database_savedata.php');

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
	
	cforms2_add_admin_bar_item($admin_bar, 'cforms-showinfo', __('Produce debug output', 'cforms'), __('Outputs -for debug purposes- all cforms settings', 'cforms'), 'jQuery("#cfbar-showinfo").trigger("click"); return false;');
	cforms2_add_admin_bar_item($admin_bar, 'cforms-dellAllButton', __('Uninstalling / removing cforms', 'cforms'), __('Be careful here...', 'cforms'), 'jQuery("#cfbar-deleteall").trigger("click"); return false;');

	if ( $wpdb->get_var("show tables like '$wpdb->cformssubmissions'") == $wpdb->cformssubmissions ) 
		cforms2_add_admin_bar_item($admin_bar, 'cforms-deletetables', __('Delete cforms tracking tables', 'cforms'), __('Be careful here...', 'cforms'), 'if ( confirm("'.__('Do you really want to erase all collected data?', 'cforms').'") ) jQuery("#deletetables").trigger("click"); return false;');

	cforms2_add_admin_bar_item($admin_bar, 'cforms-backup', __('Backup / restore all settings', 'cforms'), __('Better safe than sorry ;)', 'cforms'), 'jQuery("#backup").trigger("click"); return false;');
	
	cforms2_add_admin_bar_item($admin_bar, 'cforms-SubmitOptions', __('Save & update form settings', 'cforms'), '', 'document.mainform.action="#"+getFieldset(focusedFormControl); jQuery("#cfbar-SubmitOptions").trigger("click"); return false;', 'root-default');

}

function cforms2_add_items_options( $admin_bar ){

	$cfo = get_option('cforms_settings');

	cforms2_add_admin_bar_root($admin_bar,'cforms-bar', 'cforms Admin');
	
	cforms2_add_admin_bar_item($admin_bar,'cforms-addbutton', __('Add new form', 'cforms'), __('Adds a new form with default values', 'cforms'), 'jQuery("#cfbar-addbutton").trigger("click"); return false;');
	cforms2_add_admin_bar_item($admin_bar,'cforms-dupbutton', __('Duplicate current form', 'cforms'), __('Clones the current form', 'cforms'), 'jQuery("#cfbar-dupbutton").trigger("click"); return false;');
	if ( (int)$cfo['global']['cforms_formcount'] > 1)
		cforms2_add_admin_bar_item($admin_bar,'cforms-delbutton', __('Delete current form (!)', 'cforms'), __('Clicking this button WILL delete this form', 'cforms'), 'if ( confirm("'.__('This will delete the current form!', 'cforms').'")) jQuery("#cfbar-delbutton").trigger("click"); return false;');

	cforms2_add_admin_bar_item($admin_bar,'cforms-preset', __('Install a form preset', 'cforms'), __('Pick a form preset from the repository', 'cforms'), 'jQuery("#preset").trigger("click"); return false;');
	cforms2_add_admin_bar_item($admin_bar,'cforms-backup', __('Backup / restore this form only', 'cforms'), __('Better safe than sorry ;)', 'cforms'), 'jQuery("#backup").trigger("click"); return false;');

	cforms2_add_admin_bar_item($admin_bar,'cforms-SubmitOptions', __('Save & update form settings', 'cforms'), '', 'document.mainform.action="#"+getFieldset(focusedFormControl); jQuery("#cfbar-SubmitOptions").trigger("click"); return false;', 'root-default');

}

function cforms2_submitcomment_direct() {
	check_admin_referer( 'submitcomment_direct' );
	require_once (plugin_dir_path(__FILE__) . 'lib_WPcomment.php');
	die();
}

### attaching to filters
add_action('init', 'cforms2_delete_db_and_deactivate');
add_action('wp_ajax_submitcomment_direct', 'cforms2_submitcomment_direct');
add_action('wp_ajax_nopriv_submitcomment_direct', 'cforms2_submitcomment_direct');
add_action('wp_enqueue_scripts', 'cforms2_enqueue_scripts');
add_filter('the_content', 'cforms2_insert', 101);
add_shortcode('cforms' , 'cform2_shortcode' );
