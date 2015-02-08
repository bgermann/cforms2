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
###  Validate all fields
###

$CFfunctionsC = plugin_dir_path(dirname(__FILE__)) . 'cforms-custom'.DIRECTORY_SEPARATOR.'my-functions.php';
$CFfunctions = plugin_dir_path(__FILE__) . 'my-functions.php';
if ( file_exists($CFfunctionsC) )
    include_once($CFfunctionsC);
else if ( file_exists($CFfunctions) )
    include_once($CFfunctions);

require_once (plugin_dir_path(__FILE__) . 'lib_validate.php');


if( isset($_POST['sendbutton'.$no]) && $all_valid ) {

	    ###
	    ###  all valid? get ready to send
	    ###
		if( function_exists('my_cforms_filter') )
			$_POST = my_cforms_filter($_POST);

		if ( ($cformsSettings['form'.$no]['cforms'.$no.'_maxentries']<>'' && cforms2_get_submission_left($no)==0) || !cforms2_check_time($no) ){
			$cflimit = 'reached';
			return;
		}

		$usermessage_text = preg_replace ( '|\r\n|', '<br />', stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_success']) );

		$track = array();
		$trackinstance = array();

  		$to_one = -1;
		$ccme = false;
		$field_email = '';

		$filefield=0;
		$taf_youremail = false;
		$taf_friendsemail = false;
		$send2author = false;

		$inpFieldArr = array(); // for var[] type input fields

		$key = 0;

		for($i = 1; $i <= $field_count; $i++) {

			if ( !$custom )
				$field_stat = explode('$#$', $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . $i ]);
			else
				$field_stat = explode('$#$', $customfields[$i-1]);

			###  filter non input fields
			while ( in_array($field_stat[1],array('fieldsetstart','fieldsetend','textonly','captcha','verification')) ) {

				if ( $field_stat[1] == 'captcha' && !(is_user_logged_in() && !$captchaopt['fo']=='1') )
					break;
				if ( $field_stat[1] == 'verification' && !(is_user_logged_in() && !$captchaopt['foqa']=='1') )
					break;

                if ( $field_stat[1] == 'fieldsetstart' ){
                    $track['$$$'.$i] = 'Fieldset'.$fieldsetnr;
                    $track['Fieldset'.$fieldsetnr++] = $field_stat[0];
                }elseif ( $field_stat[1] == 'fieldsetend' ){
                    $track['FieldsetEnd'.$fieldsetnr++] = '--';
                }

                ### get next in line...
                $i++;

                if ( !$custom )
                    $field_stat = explode('$#$', $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . $i ]);
                else
                    $field_stat = explode('$#$', $customfields[$i-1]);

                if( $field_stat[1] == '')
                        break 2; ###  all fields searched, break both while & for
			}

			$field_name = $field_stat[0];
  			$field_type = $field_stat[1];

			$custom_names = ($cformsSettings['form'.$no]['cforms'.$no.'_customnames']=='1')?true:false;

    		if ( $custom_names ){

				###preg_match('/^([^#\|]*).*/',$field_name,$input_name);
				###preg_match('/^([^\|]*).*/',$field_name,$input_name);
				$tmpName = $field_name; ###hardcoded for now

				if ( strpos($tmpName,'[id:')!==false ){
					$isFieldArray = strpos($tmpName,'[]');

				preg_match('/^([^\[]*)\[id:([^\|]+(\[\])?)\]([^\|]*).*/',$tmpName,$input_name); // 2.6.2012  
				$field_name = $input_name[1].$input_name[4];
				$customTrackingID	= cforms2_sanitize_ids( $input_name[2] );

				$current_field = cforms2_sanitize_ids( $customTrackingID );
	
				} else{
					if( strpos($tmpName,'#')!==false && strpos($tmpName,'#')==0 )
						preg_match('/^#([^\|]*).*/',$field_name,$input_name); ###special case with checkboxes w/ right label only & no ID
					else
						preg_match('/^([^#\|]*).*/',$field_name,$input_name); ###just take front part
					$current_field = cforms2_sanitize_ids($input_name[1]);
					$customTrackingID='';
				}

			}
			else
				$current_field = 'cf'.$no.'_field_' . $i;

			###debug
			cforms2_dbg("lib_nonajax.php: looking at field: $current_field");
			
			###  dissect field
		    $obj = explode('|', $field_name,3);
			$defaultval = stripslashes($obj[1]);

			###  strip out default value
			$field_name = $obj[0];

			###  special Tell-A-Friend fields
			if ( !$taf_friendsemail && $field_type=='friendsemail' && $field_stat[3]=='1')
					$field_email = $taf_friendsemail = $_POST[$current_field];

			if ( !$taf_youremail && $field_type=='youremail' && $field_stat[3]=='1')
					$taf_youremail = $_POST[$current_field];

			if ( $field_type=='friendsname' )
					$taf_friendsname = $_POST[$current_field];

			if ( $field_type=='yourname' )
					$taf_yourname = $_POST[$current_field];


			###  special email field in WP Commente
			if ( $field_type=='email' )
					$field_email = (isset($_POST['email']))?$_POST['email']:$user->user_email;


			###  special radio button WP Comments
			if( $field_type=='send2author' && $_POST['send2author']=='1') {
				$send2author=true;
				continue; ###  don't record it.
			}

			###  find email address
			if ( $field_email == '' && $field_stat[3]=='1')
					$field_email = $_POST[$current_field];


			###  special case: select box & radio box
			if ( $field_type == "checkboxgroup" || $field_type == "multiselectbox" || $field_type == "selectbox" || $field_type == "radiobuttons" ) { ### only needed for field name
			  $field_name = explode('#',$field_name);
			  $field_name = $field_name[0];
			}


			###  special case: check box
			if ( $field_type == "checkbox" || $field_type == "ccbox" ) {
			  $field_name = explode('#',$field_name);
			  $field_name = ($field_name[1]=='')?$field_name[0]:$field_name[1];
				###  if ccbox
			  if ($field_type == "ccbox" && isset($_POST[$current_field]) ){
				if( $isMPform )
				  $ccme = 'cf_form'.$no.'_'.$field_name;
				 else				 
				  $ccme = $field_name;
				}
			}


		if ( $field_type == "emailtobox" ){  				### special case where the value needs to bet get from the DB!

            $to_one = $_POST[$current_field];
            $field_name = explode('#',$field_stat[0]);  	### can't use field_name, since '|' check earlier

			$tmp = explode('|', $field_name[$to_one+1] );	###  remove possible |set:true
            $value 	= $tmp[0];								###  values start from 0 or after!
			$to = $replyto = stripslashes($tmp[1]);

            $field_name = $field_name[0];
 		}
 		else if ( $field_type == "upload" ){

			if ( is_array($file) && is_array($file['name']) ) {
				### $fsize = $file['size'][$filefield]/1000;
				$value = str_replace(' ','_',$file['name'][$filefield++]);
			}else{
				$value = '';
			}

 		}
 		else if ( $field_type == "multiselectbox" || $field_type == "checkboxgroup"){

            $all_options = $_POST[$current_field];
 		    if ( count($all_options) > 0)
                $value = stripslashes(implode(',', $all_options));
            else
                $value = '';

        }
		else if ( $field_stat[1] == 'captcha' ) ###  captcha response

			$value = $_POST['cforms_captcha'.$no];

		else if ( $field_stat[1] == 'verification' ) { ###  verification Q&A response

			$value = $_POST['cforms_q'.$no]; ###  add Q&A label!
			$field_name = __('Q&A','cforms');

		}
		else if( $field_type == 'cauthor' )  ###  WP Comments special fields
			$value = ($user->display_name<>'')?$user->display_name:$_POST[$field_type];

		else if( $field_type == 'url')
			$value = ($user->user_url<>'')?$user->user_url:$_POST[$field_type];

		else if( $field_type == 'email' )
			$value = ($user->user_email<>'')?$user->user_email:$_POST[$field_type];

		else if( $field_type == 'comment' )
			$value = $_POST[$field_type];

		else if( $field_type == 'hidden' )
			$value = rawurldecode($_POST[$current_field]);

		else{
			if( $isFieldArray ){

				if( !$inpFieldArr[$current_field] || $inpFieldArr[$current_field]=='' ){
					$inpFieldArr[$current_field]=0;
				} 
				$value = $_POST[$current_field][$inpFieldArr[$current_field]++];       ###  covers all other fields' values

			}else
				$value = $_POST[$current_field];       ###  covers all other fields' values
		}

		### check boxes
		if ( $field_type == "checkbox" || $field_type == "ccbox" ) {

				if ( $value == 'on' )
					$value = '(x)';
				else
					$value = '';

		}

		### determine tracked field name
        $inc='';
        $trackname = trim( ($field_type == "upload") ? $field_name.'[*'.($no==''?1:$no).']' : $field_name );
        if ( array_key_exists($trackname, $track) ){
            if ( $trackinstance[$trackname]==''  )
                $trackinstance[$trackname]=2;
            $inc = '___'.($trackinstance[$trackname]++);
        }

        $track['$$$'.$i] = $trackname.$inc;
        $track[$trackname.$inc] = $value;
        if( $customTrackingID<>'' )
            $track['$$$'.$customTrackingID] = $trackname.$inc;

	} ### for all fields

	
	###  prefilter user input
	if( function_exists('my_cforms_filter') )
        my_cforms_filter($no);


    ### multi-form session
	$ongoingSession = 'noSess';
	if( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_form'] ){

		if( $field_email<>'' )
       		$_SESSION['cforms']['email']=$field_email;
		if( $ccme<>'' )
       		$_SESSION['cforms']['ccme']=$ccme;
		$_SESSION['cforms']['list'][$_SESSION['cforms']['pos']++]=$no;
	    $_SESSION['cforms']['current']=$no==''?1:$no;
	    $_SESSION['cforms']['cf_form'.$no] = $track;

		### debug
		cforms2_dbg( "(lib_nonajax) In Session tracking for ($no)...".print_r($_SESSION,1) );

        $field_email = $_SESSION['cforms']['email']; ### fetch from prev. def
   		$ccme = $_SESSION['cforms']['ccme'];
		$ongoingSession = '1';
	}


	###  assemble text & html email
	if( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_form'] &&
    	!$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_email'] &&
    	$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_next']==-1 &&
        is_array($_SESSION['cforms']) ){
			$track = cforms2_all_tracks($_SESSION['cforms']);
            $ongoingSession = '0';
		}
	### debug
	cforms2_dbg( '$track = '.print_r($track,1) );

	
    $r = cforms2_format_email($track,$no);
    $formdata = $r['text'];
    $htmlformdata = $r['html'];


	###
	###  FIRST into the database is required!
	###
	global $subID, $wpdb;
	$subID = ( $isTAF =='2' && !$send2author )?'noid':cforms2_write_tracking_record($no,$field_email);


	###
	###  allow the user to use form data for other apps
	###
	$trackf['id'] = $no;
	$trackf['data'] = $track;
	if( function_exists('my_cforms_action') ) {
		try {
			my_cforms_action($trackf);
		} catch ( Exception $exc ) {
			$usermessage_text = $exc->getMessage();
			$sentadmin = 1;
		}
	}



	###
	### set reply-to & watch out for T-A-F
	###
	$replyto = preg_replace( array('/;|#|\|/'), array(','), stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_email']) );

	###  WP comment form > email to author
	if ( $isTAF=='2' && $track['send2author']=='1'){
			$to = $wpdb->get_results($wpdb->prepare("SELECT U.user_email FROM $wpdb->users as U, $wpdb->posts as P WHERE P.ID = %d AND U.ID=P.post_author", $_POST['comment_post_ID'.$no]));
			$to = $replyto =  ($to[0]->user_email<>'')?$to[0]->user_email:$replyto;
	}
	else if ( !($to_one<>-1 && $to<>'') ){
		$to = $replyto = preg_replace( array('/;|#|\|/'), array(','), stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_email']) );
	}


	### T-A-F overwrite
	if ( $taf_youremail && $taf_friendsemail && $isTAF=='1' )
		$replyto = "\"{$taf_yourname}\" <{$taf_youremail}>";



	###
	###  Files attached?? create $_SESSION['cforms']['upload'] via cforms2_move_files()
	###
	if(is_array($file)){
	    if( $subID<>-1 && $ongoingSession!='0' )
	        cforms2_move_files($track, $no, $subID);
	    else
	        cforms2_move_files($track, $no, 'xx');
	}
	### end of session:
    if( $ongoingSession=='0' && is_array($_SESSION['cforms']['upload']) ){
    	foreach ( array_keys($_SESSION['cforms']['upload']) as $n ) {
			if (  is_array( $_SESSION['cforms']['upload'][$n]['files']) ) {
				foreach ( array_keys($_SESSION['cforms']['upload'][$n]['files']) as $m ) {
					if( file_exists($_SESSION['cforms']['upload'][$n]['files'][$m]) )
						rename($_SESSION['cforms']['upload'][$n]['files'][$m],str_replace('xx',$subID,$_SESSION['cforms']['upload'][$n]['files'][$m]));
				}
			}
        }
    }



	###
	###  ready to send email
	###
	###

	$frommail = cforms2_check_cust_vars(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_fromemail']),$track);

    ### logic: dynamic admin email address
    if ( function_exists('my_cforms_logic') )
        $to = my_cforms_logic($trackf, $to,'adminTO');  ### use trackf!

	### either use configured subject or user determined
	### now replace the left over {xyz} variables with the input data
	$vsubject = stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_subject']);
	if (function_exists('my_cforms_logic'))
		$vsubject = my_cforms_logic($trackf,$vsubject,'adminEmailSUBJ');
	$vsubject = cforms2_check_default_vars($vsubject,$no);
	$vsubject = cforms2_check_cust_vars($vsubject,$track);

	###  prep message text, replace variables
	$message	= stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_header']);
	if ( function_exists('my_cforms_logic') ){
		$message = my_cforms_logic($trackf, $message,'adminEmailTXT');
		$formdata = my_cforms_logic($trackf, $formdata,'adminEmailDataTXT');
	}
	$message	= cforms2_check_default_vars($message,$no);
	$message	= cforms2_check_cust_vars($message,$track);

    ###  actual user message
    $htmlmessage='';
    if( substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],2,1)=='1' ){
	    $htmlmessage = stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_header_html']);
	    if ( function_exists('my_cforms_logic') ){
	        $htmlmessage = my_cforms_logic($trackf, $htmlmessage,'adminEmailHTML');
			$htmlformdata = my_cforms_logic($trackf, $htmlformdata,'adminEmailDataHTML');
	    }
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




	### Skip admin email when MP form
    $MPok = !$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_form'] || ($cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_form'] && !$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_email']);

	if ( $MPok ){

	    ###
	    ### adding attachments now
	    ###
	    $attached='';
		global $fdata, $fpointer;
		$fdata = array();
		$fpointer = 0;

		### debug
		cforms2_dbg( 'File Attachments:' );

	    ###  attachments wanted for current form? (tracking session form uploads handled above!)
		$doAttach = !($cformsSettings['form'.$no]['cforms'.$no.'_noattachments']);
		
		### form w/ files, within session or single form 
		if ( $doAttach && $ongoingSession!='0' && is_array($file) ){
			foreach( $file[tmp_name] as $fn ){
				cforms2_base64($fn, $doAttach);
				### debug
				cforms2_dbg( "File = $fn, attach = $doAttach" );
			}
		}
		
		### end of session w/ files
		if( $ongoingSession=='0' && is_array($_SESSION['cforms']['upload']) ){
			foreach ( array_keys($_SESSION['cforms']['upload']) as $n ) {
				foreach ( array_keys($_SESSION['cforms']['upload'][$n]['files']) as $m ){
					cforms2_base64(str_replace('xx',$subID,$_SESSION['cforms']['upload'][$n]['files'][$m]), $_SESSION['cforms']['upload'][$n]['doAttach'] );
					### debug
					cforms2_dbg( "(end of session) File = ".$_SESSION['cforms']['upload'][$n]['files'][$m].", attach = ".$_SESSION['cforms']['upload'][$n]['doAttach'] );
                }
            }
		}
		### parse through all files (both single and mp forms)
		foreach ( $fdata as $file ) {
			if ( $file[doAttach] && $file[name] <> '' ){
				$n = substr( $file[name], strrpos($file[name],DIRECTORY_SEPARATOR)+1, strlen($file[name]) );
				$m = wp_check_filetype( strtolower( $n ) );
				$mail->add_file($file[name], $n,'base64',$m); ### optional name
				### debug
				cforms2_dbg( 'Attaching file ('.$file[name].') to email' );
			}
		}

	    ### end adding attachments

		### debug
		cforms2_dbg('TRACKF');
		cforms2_dbg(print_r($trackf,1)."\n");

		###
		### Shoot:
		###
		###
		if( $cformsSettings['form'.$no]['cforms'.$no.'_emailoff']=='1' )
	        $sentadmin = 1;
	    else
	        $sentadmin = $mail->send();

	    if( $sentadmin == 1 ) {

				#debug
				cforms2_dbg("is CC: = $ccme, active = {$trackf[data][$ccme]} | ");

	            ###  send copy or notification?
                ###  not if no email & already CC'ed				
	            if ( ($cformsSettings['form'.$no]['cforms'.$no.'_confirm']=='1' && $field_email<>'') || ($ccme&&$trackf[data][$ccme]<>'') ){

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
	                    $cmsghtml =	cforms2_check_cust_vars($cmsghtml,$track,true);
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
	                    $field_email = "\"{$taf_friendsname}\" <{$taf_friendsemail}>";
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
	                else { // ac below
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

	                if( $sent<>'1' )
				        $usermessage_text = __('Error occurred while sending the auto confirmation message: ','cforms') . '<br />'. $mail->err;
	            }

	        ###  redirect to a different page on suceess?
	        if ( $cformsSettings['form'.$no]['cforms'.$no.'_redirect'] && !$isWPcommentForm ) {
	            if ( function_exists('my_cforms_logic') )
	                $rp = my_cforms_logic($trackf, $cformsSettings['form'.$no]['cforms'.$no.'_redirect_page'],'redirection');  ### use trackf!
	            else
	                $rp = $cformsSettings['form'.$no]['cforms'.$no.'_redirect_page'];

	            if ( $rp <> '' ){ // TODO rework to do this via HTTP?
	                ?>
	                <script type="text/javascript">
	                    location.href = '<?php echo $rp; ?>';
	                </script>
	                <?php
				}
	        }

	    } ###  if $sentadmin
	    else
	        $usermessage_text = __('Error occurred while sending the message: ','cforms') . '<br />'. $mail->err;
	} ### if $MPok

} ### if isset & valid sendbutton
