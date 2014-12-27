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
 */

###
###  ajax submission of form
###

require_once(plugin_dir_path(__FILE__) . 'lib_email.php');
require_once(plugin_dir_path(__FILE__) . 'lib_aux.php');

add_action( 'wp_ajax_submitcomment', 'cforms2_submitcomment' );
add_action( 'wp_ajax_nopriv_submitcomment', 'cforms2_submitcomment' );

###
###  submit comment
###
function cforms2_submitcomment() {
	check_admin_referer( 'submitcomment' );
	global $cformsSettings, $wpdb, $subID, $track, $trackf, $Ajaxpid, $AjaxURL, $WPresp, $commentparent;

	header ('Content-Type: text/plain');
	$content = '';
	if (isset($_POST['rsargs']))
		$content = $_POST['rsargs'];

    $WPsuccess=false;

	$content = explode('+++', $content); ###  Added special fields

	if ( count($content) > 3 ){
	    $commentparent = $content[1];
	    $Ajaxpid = $content[2];
	    $AjaxURL = $content[3];
    }else {
	    $Ajaxpid = $content[1];
	    $AjaxURL = $content[2];
    }

	$segments = explode('$#$', $content[0]);
	$params = array();

    $CFfunctionsC = plugin_dir_path(dirname(__FILE__)).'cforms-custom'.DIRECTORY_SEPARATOR.'my-functions.php';
    $CFfunctions = plugin_dir_path(__FILE__).'my-functions.php';
    if ( file_exists($CFfunctionsC) )
        include_once($CFfunctionsC);
    else if ( file_exists($CFfunctions) )
        include_once($CFfunctions);


	$user = wp_get_current_user();

	for($i = 1; $i <= sizeof($segments); $i++) {
		$params['field_' . $i] = $segments[$i];
    }

	###  fix reference to first form
	if ( $segments[0]=='1' ) $params['id'] = $no = ''; else $params['id'] = $no = $segments[0];


	### TAF flag
    $isTAF = substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1);


	###  user filter ?
	if( function_exists('my_cforms_ajax_filter') )
		$params = my_cforms_ajax_filter($params);


	###  init variables
	$track = array();
	$trackinstance = array();

 	$to_one = -1;
  	$ccme = false;
	$field_email = '';
	$off = 0;
	$fieldsetnr=1;

	$taf_youremail = false;
	$taf_friendsemail = false;

	###  form limit reached
	if ( ($cformsSettings['form'.$no]['cforms'.$no.'_maxentries']<>'' && cforms2_get_submission_left($no)==0) || !cforms2_check_time($no) ){
	    $pre = $segments[0].'*$#'.substr($cformsSettings['form'.$no]['cforms'.$no.'_popup'],0,1);
	    echo $pre . preg_replace ( '|\r\n|', '<br />', stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_limittxt']));
		die();
	}

	$captchaopt = $cformsSettings['global']['cforms_captcha_def'];

	for($i = 1; $i <= sizeof($params)-2; $i++) {

			$field_stat = explode('$#$', $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . ((int)$i+(int)$off)] );

			while ( in_array($field_stat[1],array('fieldsetstart','fieldsetend','textonly','captcha','verification')) ) {

				if ( $field_stat[1] == 'captcha' && !(is_user_logged_in() && !$captchaopt['fo']=='1') )
					break;
				if ( $field_stat[1] == 'verification' && !(is_user_logged_in() && !$captchaopt['foqa']=='1') )
					break;

                if ( $field_stat[1] == 'fieldsetstart' ){
                        $track['$$$'.((int)$i+(int)$off)] = 'Fieldset'.$fieldsetnr;
                        $track['Fieldset'.$fieldsetnr++] = $field_stat[0];
                    } elseif ( $field_stat[1] == 'fieldsetend' ){
                        $track['FieldsetEnd'.$fieldsetnr++] = '--';
                }

                ### get next in line...
                $off++;
                $field_stat = explode('$#$', $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . ((int)$i+(int)$off)] );

                if( $field_stat[1] == '')
                    break 2; ###  all fields searched, break both while & for

			}

			###  filter all redundant WP comment fields if user is logged in
			while ( in_array($field_stat[1],array('cauthor','email','url')) && $user->ID ) {

			    $temp = explode('|', $field_stat[0],3); ### get field name
			    $temp = explode('#', $temp[0],2);
		 		switch( $field_stat[1] ){
						case 'cauthor':
							$track['cauthor'] = $track[$temp[0]] = $user->display_name;
							$track['$$$'.((int)$i+(int)$off)] = $temp[0];
							break;
						case 'email':
							$track['email'] = $track[$temp[0]] = $field_email = $user->user_email;
							$track['$$$'.((int)$i+(int)$off)] = $temp[0];
							break;
						case 'url':
							$track['url'] = $track[$temp[0]] = $user->user_url;
							$track['$$$'.((int)$i+(int)$off)] = $temp[0];
							break;
					}

					$off++;
					$field_stat = explode('$#$', $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . ((int)$i+(int)$off)] );

					if( $field_stat[1] == '')
						break 2; ###  all fields searched, break both while & for
			}

			$field_name = $field_stat[0];
			$field_type = $field_stat[1];

			### remove [id: ] first
			if ( strpos($field_name,'[id:')!==false ){
				
				preg_match('/^([^\[]*)\[id:([^\|]+(\[\])?)\]([^\|]*).*/',$field_name,$input_name); // 2.6.2012  
				$field_name = $input_name[1].$input_name[4];
				$customTrackingID	= cforms2_sanitize_ids( $input_name[2] );

			}
			else
				$customTrackingID='';


			###  dissect field
		    $obj = explode('|', $field_name,3);

			###  strip out default value
			$field_name = $obj[0];


			###  special WP comment fields
			if( in_array($field_stat[1],array('cauthor','email','url','comment','send2author')) ){
			    $temp = explode('#', $field_name,2);

				if ( $temp[0] == '' )
                	$field_name = $field_stat[1];
				else
                	$field_name = $temp[0];

				### keep copy of values
    			$track[$field_stat[1]] = stripslashes( $params['field_' . $i] );

				if ( $field_stat[1] == 'email' )
					$field_email = $params['field_' . $i];
			}

			###  special Tell-A-Friend fields
			if ( $taf_friendsemail == '' && $field_type=='friendsemail' && $field_stat[3]=='1'){
					
					preg_match("/^[_a-z0-9+-]+(\.[_a-z0-9+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $params ['field_' . $i], $r);
					$field_email = $taf_friendsemail = $r[1];  // double checking anti spam TAF

			}
			if ( $taf_youremail == '' && $field_type=='youremail' && $field_stat[3]=='1')
					$taf_youremail = $params ['field_' . $i];
			if ( $field_type=='friendsname' )
					$taf_friendsname = $params ['field_' . $i];
			if ( $field_type=='yourname' )
					$taf_yourname = $params ['field_' . $i];


			###  lets find an email field ("Is Email") and that's not empty!
			if ( $field_email == '' && $field_stat[3]=='1') {
					$field_email = $params ['field_' . $i];
			}

			###  special case: select & radio
			if ( $field_type == "multiselectbox" || $field_type == "selectbox" || $field_type == "radiobuttons" || $field_type == "checkboxgroup") {
			  $field_name = explode('#',$field_name);
			  $field_name = $field_name[0];
			}

			###  special case: check box
			if ( $field_type == "checkbox" || $field_type == "ccbox" ) {
			  $field_name = explode('#',$field_name);
			  $field_name = ($field_name[1]=='')?$field_name[0]:$field_name[1];

			  $field_name = explode('|',$field_name);
			  $field_name = $field_name[0];

				###  if ccbox & checked
			  if ($field_type == "ccbox" && $params ['field_' . $i]<>"" ) //10.2. removed "-"
			      ##$ccme = 'field_' . $i;
			      $ccme = $field_name;
			}

			if ( $field_type == "emailtobox" ){  			### special case where the value needs to bet get from the DB!

                $to_one = $params ['field_' . $i];
				$field_name = explode('#',$field_stat[0]);  ### can't use field_name, since '|' check earlier

	            $tmp = explode('|', $field_name[$to_one+1] );   ###  remove possible |set:true
	            $value  = $tmp[0];                              ###  values start from 0 or after!
				$to = $replyto = stripslashes($tmp[1]);

				$field_name = $field_name[0];
	 		}
			else {
			    if ( strtoupper(get_option('blog_charset')) <> 'UTF-8' && function_exists('mb_convert_encoding'))
        		    $value = mb_convert_encoding(utf8_decode( stripslashes( $params['field_' . $i] ) ), get_option('blog_charset'));   ###  convert back and forth to support also other than UTF8 charsets
                else
                    $value = stripslashes( $params['field_' . $i] );
            }

			### only if hidden!
			if( $field_type == 'hidden' )
				$value = rawurldecode($value);


			###  Q&A verification
			if ( $field_type == "verification" )
					$field_name = __('Q&A','cforms');

			### check boxes
			if ( $field_type == "checkbox" || $field_type == "ccbox" ) {

					if ( $value == 'on' )
						$value = '(x)';
					else
						$value = '';

			}
			
			### determine tracked field name
			$inc='';
			$trackname=trim($field_name);
			if ( array_key_exists($trackname, $track) ){
				if ( $trackinstance[$trackname]=='' )
					$trackinstance[$trackname]=2;
				$inc = '___'.($trackinstance[$trackname]++);
			}

			$track['$$$'.(int)($i+$off)] = $trackname.$inc;
			$track[$trackname.$inc] = $value;
			if( $customTrackingID<>'' )
				$track['$$$'.$customTrackingID] = $trackname.$inc;

	} ###  for

	###  prefilter user input
	if( function_exists('my_cforms_filter') )
        my_cforms_filter($no);

	###  assemble text & html email
	$r = cforms2_format_email($track,$no);
    $formdata = $r['text'];
    $htmlformdata = $r['html'];


	###
	###  record:
	###
	$subID = ( $isTAF=='2' && $track['send2author']<>'1' )?'noid':cforms2_write_tracking_record($no,$field_email);


	###
	###  allow the user to use form data for other apps
	###
	$trackf['id'] = $no;
	$trackf['data'] = $track;
	if( function_exists('my_cforms_action') ) {
		try {
			my_cforms_action($trackf);
		} catch ( Exception $exc ) {
			echo $segments[0].'*$#y' . $exc->getMessage() .'|---';
			die();
		}
	}

	$isAjaxWPcomment = substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'], 0, 1) === '2';

    ###  Catch WP-Comment function | if send2author just continue
    if ( $isAjaxWPcomment!==false && (!isset($track['send2author']) || $track['send2author']=='0') ){
		require_once (plugin_dir_path(__FILE__) . 'lib_WPcomment.php');

	    ###  Catch WP-Comment function: error
	    if ( !$WPsuccess ) {
    	    echo $segments[0].'*$#'.substr($cformsSettings['form'.$no]['cforms'.$no.'_popup'],1,1) . $WPresp .'|---';
			die();
		}
    } ### Catch WP-Comment function



	###  multiple recipients? and to whom is the email sent? to_one = picked recip.
	if ( $isAjaxWPcomment!==false && $track['send2author']=='1' ) {
		$to = $wpdb->get_results($wpdb->prepare("SELECT U.user_email FROM $wpdb->users as U, $wpdb->posts as P WHERE P.ID = %d AND U.ID=P.post_author", $Ajaxpid));
		$to = $replyto = ($to[0]->user_email<>'')?$to[0]->user_email:$replyto;
	}
	else if ( !($to_one<>-1 && $to<>'') ){
		$to = $replyto = preg_replace( array('/;|#|\|/'), array(','), stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_email']) );
	}


	### from
	$frommail = cforms2_check_cust_vars(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_fromemail']),$track);


	###  T-A-F override?
	if ( $isTAF=='1' && $taf_youremail && $taf_friendsemail )
		$replyto = "\"{$taf_yourname}\" <{$taf_youremail}>";

    ### logic: dynamic admin email address
    if ( function_exists('my_cforms_logic') )
        $to = my_cforms_logic($trackf, $to,'adminTO');  ### use trackf!

	### either use configured subject or user determined
	$vsubject = stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_subject']);
	if (function_exists('my_cforms_logic'))
		$vsubject = my_cforms_logic($trackf,$vsubject,'adminEmailSUBJ');
	$vsubject = cforms2_check_default_vars($vsubject,$no);
	$vsubject = cforms2_check_cust_vars($vsubject,$track);


	###  prep message text, replace variables
	$message = stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_header']);
	if ( function_exists('my_cforms_logic') )
		$message = my_cforms_logic($trackf, $message,'adminEmailTXT');
	$message = cforms2_check_default_vars($message,$no);
	$message = cforms2_check_cust_vars($message,$track);

	###  actual user message
    $htmlmessage='';
    if( substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],2,1)=='1' ){
		$htmlmessage = stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_header_html']);
	    if ( function_exists('my_cforms_logic') )
	        $htmlmessage = my_cforms_logic($trackf, $htmlmessage,'adminEmailHTML');
		$htmlmessage = cforms2_check_default_vars($htmlmessage,$no);
	    $htmlmessage = cforms2_check_cust_vars($htmlmessage,$track,true);

	}

	### custom user ReplyTo handling
	if ( function_exists('my_cforms_logic') )
		$userReplyTo = my_cforms_logic($trackf, $field_email, 'ReplyTo');
	else
		$userReplyTo = $field_email;
	
	$mail = new cforms2_mail($no,$frommail,$to,$userReplyTo, true);
	$mail->subj  = $vsubject;
	$mail->char_set = 'utf-8';

	### HTML email
	if ( $mail->html_show ) {
	    $mail->is_html(true);
	    $mail->body     =  $cformsSettings['global']['cforms_style_doctype'] .$mail->eol."<html xmlns=\"http://www.w3.org/1999/xhtml\">".$mail->eol."<head><title></title></head>".$mail->eol."<body {$cformsSettings['global']['cforms_style']['body']}>".$htmlmessage.( $mail->f_html?$mail->eol.$htmlformdata:'').$mail->eol."</body></html>".$mail->eol;
	    $mail->body_alt  =  $message . ($mail->f_txt?$mail->eol.$formdata:'');
	}
	else
	    $mail->body     =  $message . ($mail->f_txt?$mail->eol.$formdata:'');


    if( $cformsSettings['form'.$no]['cforms'.$no.'_emailoff']=='1' || ($WPsuccess && $cformsSettings['form'.$no]['cforms'.$no.'_tellafriend']!='21') )
        $sentadmin = 1;
	else
	    $sentadmin = $mail->send();

	if( $sentadmin == 1 )
	{

		###  send copy or notification?
	    if ( ($cformsSettings['form'.$no]['cforms'.$no.'_confirm']=='1' && $field_email<>'') || ($ccme&&$trackf[data][$ccme]<>'') )  ###  not if no email & already CC'ed
	    {

	                $frommail = cforms2_check_cust_vars(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_fromemail']),$track);

	                ###  actual user message
	                $cmsg = stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_cmsg']);
	                if ( function_exists('my_cforms_logic') )
	                    $cmsg = my_cforms_logic($trackf, $cmsg,'autoConfTXT');
	                $cmsg = cforms2_check_default_vars($cmsg,$no);
	                $cmsg = cforms2_check_cust_vars($cmsg,$track);

	                ###  HTML text
	                $cmsghtml='';
	                if( substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],3,1)=='1' ){
	                    $cmsghtml = stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_cmsg_html']);
	                    if ( function_exists('my_cforms_logic') )
	                        $cmsghtml = my_cforms_logic($trackf, $cmsghtml,'autoConfHTML');
	                    $cmsghtml = cforms2_check_default_vars($cmsghtml,$no);
	                    $cmsghtml = cforms2_check_cust_vars($cmsghtml,$track,true);
	                }

	                ### subject
	                $subject2 = stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_csubject']);
					if (function_exists('my_cforms_logic'))
						$subject2 = my_cforms_logic($trackf,$subject2,'autoConfSUBJ');
					$subject2 = cforms2_check_default_vars($subject2,$no);
	                $subject2 = cforms2_check_cust_vars($subject2,$track);

	                ###  different cc & ac subjects?
	                $s=explode('$#$',$subject2);
	                $s[1] = ($s[1]<>'') ? $s[1] : $s[0];

	                ###  email tracking via 3rd party?
	                ###  if in Tell-A-Friend Mode, then overwrite header stuff...
	                if ( $taf_youremail && $taf_friendsemail && $isTAF=='1' )
	                    $field_email = "\"{$taf_friendsname}\" <{$taf_friendsemail}>"; ### sanitize taf_friendsemail !!!
	                else
	                    $field_email = ($cformsSettings['form'.$no]['cforms'.$no.'_tracking']<>'')?$field_email.$cformsSettings['form'.$no]['cforms'.$no.'_tracking']:$field_email;

	                $mail = new cforms2_mail($no,$frommail,$field_email,$replyto);

	                ### auto conf attachment?
	                $a = $cformsSettings['form'.$no]['cforms'.$no.'_cattachment'][0];
	                $a = (substr($a,0,1)=='/') ? $a : plugin_dir_path(__FILE__).$a;
	                if ( $a<>'' && file_exists( $a ) ) {
	                    $n = substr( $a, strrpos($a,DIRECTORY_SEPARATOR)+1, strlen($a) );
	                    $m = wp_check_filetype( strtolower( $n ) );
	                    $mail->add_file($a, $n,'base64',$m); ### optional name
	                }

	                $mail->char_set = 'utf-8';

	                ### CC or auto conf?
	                if ( $ccme&&$trackf[data][$ccme]<>'' ) {
	                        $mail->subj = $s[1];
	                        if ( $mail->html_show ) {  // 3.2.2012 changed from html_show_ac > admin email setting dictates this!
	                            $mail->is_html(true);
	                            $mail->body     =  $cformsSettings['global']['cforms_style_doctype'] .$mail->eol."<html xmlns=\"http://www.w3.org/1999/xhtml\">".$mail->eol."<head><title></title></head>".$mail->eol."<body {$cformsSettings['global']['cforms_style']['body']}>".$htmlmessage.( $mail->f_html?$mail->eol.$htmlformdata:'').$mail->eol."</body></html>".$mail->eol;
	                            $mail->body_alt  =  $message . ($mail->f_txt?$mail->eol.$formdata:'');
	                        }
	                        else
	                            $mail->body     =  $message . ($mail->f_txt?$mail->eol.$formdata:'');

	                        $sent = $mail->send();
	                }
	                else {
	                        $mail->subj = $s[0];
	                        if ( $mail->html_show_ac ) {
	                            $mail->is_html(true);
	                            $mail->body     =  $cformsSettings['global']['cforms_style_doctype'] .$mail->eol."<html xmlns=\"http://www.w3.org/1999/xhtml\">".$mail->eol."<head><title></title></head>".$mail->eol."<body {$cformsSettings['global']['cforms_style']['body']}>".$cmsghtml."</body></html>".$mail->eol;
	                            $mail->body_alt  =  $cmsg;
	                        }
	                        else
	                            $mail->body     =  $cmsg;

	                        $sent = $mail->send();
	                }

	                if( $sent<>'1' ) {
	                    $err = __('Error occurred while sending the auto confirmation message: ','cforms') . '<br />'. $mail->err;
	                    $pre = $segments[0].'*$#'.substr($cformsSettings['form'.$no]['cforms'.$no.'_popup'],1,1);
	                    echo $pre . $err .'|!!!';
						die();
	                }
	    } ###  cc

		###  return success msg
	    $pre = $segments[0].'*$#'.substr($cformsSettings['form'.$no]['cforms'.$no.'_popup'],0,1);

		###  WP-Comment: override
		if ( $WPsuccess )
			$successMsg = $WPresp;
		else{
        	$successMsg	= cforms2_check_default_vars(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_success']),$no);
			$successMsg	= str_replace ( $mail->eol, '<br />', $successMsg);
		}

		$successMsg	= cforms2_check_cust_vars($successMsg,$track);

	    ### logic: possibly change usermessage
	    if ( function_exists('my_cforms_logic') )
	        $successMsg = my_cforms_logic($trackf, $successMsg,'successMessage');


		$opt='';
		###  hide?
        if ( $cformsSettings['form'.$no]['cforms'.$no.'_hide'] || cforms2_get_submission_left($no)==0 )
			$opt .= '|~~~';

		###  redirect to a different page on suceess?
		if ( $cformsSettings['form'.$no]['cforms'.$no.'_redirect'] ) {
			if ( function_exists('my_cforms_logic') ){
				$red = my_cforms_logic($trackf, $cformsSettings['form'.$no]['cforms'.$no.'_redirect_page'],'redirection');
            	if ( $red<>'' )
                	$opt .= '|>>>' . $red;  ### use trackf!
            } else
				$opt .= '|>>>' . $cformsSettings['form'.$no]['cforms'.$no.'_redirect_page'];
		}

	    echo $pre.$successMsg.$opt;

	}
	else {  ###  no admin mail sent!

		###  return error msg
		$err = __('Error occurred while sending the message: ','cforms') . '<br />'. $mail->err;
	    $pre = $segments[0].'*$#'.substr($cformsSettings['form'.$no]['cforms'.$no.'_popup'],1,1);
	    echo $pre . $err .'|!!!';

	}

	die();
}
