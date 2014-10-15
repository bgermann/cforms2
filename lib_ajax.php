<?php
###
###  ajax submission of form
###

### supporting WP2.6 wp-load & custom wp-content / plugin dir
if ( file_exists('abspath.php') )
	include_once('abspath.php');
else
	$abspath='../../../';

if ( file_exists( $abspath . 'wp-load.php') )
	require_once( $abspath . 'wp-load.php' );
else
	require_once( $abspath . 'wp-config.php' );

require_once(dirname(__FILE__) . '/lib_aux.php');

###
###  reset captcha image
###
function reset_captcha( $no = '' ){
	### fix for windows!!!
	if ( strpos(__FILE__,'\\') ){
		$path = preg_replace( '|.*(wp-content.*)lib_ajax.php|','${1}', __FILE__ );
		$path = '/'.str_replace('\\','/',$path);
	}
	else
		$path = preg_replace( '|.*(/wp-content/.*)/.*|','${1}', __FILE__ );

	$path = get_bloginfo('wpurl') . $path;

	$newimage = 'newcap|'.$no.'|'.$path.'/cforms-captcha.php?ts='.$no.str_replace('&amp;','&',get_captcha_uri());
	return $newimage;
}

###
###  submit comment
###
function cforms_submitcomment($content) {
	global $cformsSettings, $wpdb, $subID, $styles, $smtpsettings, $track, $trackf, $Ajaxpid, $AjaxURL, $wp_locale, $abspath;

	$isAjaxWPcomment = strpos($content,'***');###  WP comment feature
	$content = explode('***', $content);
	$content = $content[0];

	$content = explode('+++', $content); ###  Added special fields
	$Ajaxpid = $content[1];
	$AjaxURL = $content[2];

	$segments = explode('$#$', $content[0]);
	$params = array();

	$sep = (strpos(__FILE__,'/')===false)?'\\':'/';
	$WPpluggable = $abspath . 'wp-includes'.$sep.'pluggable.php';
	if ( file_exists($WPpluggable) )
		require_once($WPpluggable);

    $CFfunctionsC = dirname(dirname(__FILE__)).$cformsSettings['global']['cforms_IIS'].'cforms-custom'.$cformsSettings['global']['cforms_IIS'].'my-functions.php';
    $CFfunctions = dirname(__FILE__).$cformsSettings['global']['cforms_IIS'].'my-functions.php';
    if ( file_exists($CFfunctionsC) )
        include_once($CFfunctionsC);
    else if ( file_exists($CFfunctions) )
        include_once($CFfunctions);


	if ( function_exists('wp_get_current_user') )
		$user = wp_get_current_user();

	for($i = 1; $i <= sizeof($segments); $i++)
		$params['field_' . $i] = $segments[$i];

	###  fix reference to first form
	if ( $segments[0]=='1' ) $params['id'] = $no = ''; else $params['id'] = $no = $segments[0];


	###  user filter ?
	if( function_exists('my_cforms_ajax_filter') )
		$params = my_cforms_ajax_filter($params);


	###  init variables
	$track = array();
	$trackinstance = array();

 	$to_one = "-1";
  	$ccme = false;
	$field_email = '';
	$off = 0;
	$fieldsetnr=1;

	$taf_youremail = false;
	$taf_friendsemail = false;

	###  form limit reached
	if ( ($cformsSettings['form'.$no]['cforms'.$no.'_maxentries']<>'' && get_cforms_submission_left($no)==0) || !cf_check_time($no) ){
	    $pre = $segments[0].'*$#'.substr($cformsSettings['form'.$no]['cforms'.$no.'_popup'],0,1);
	    return $pre . preg_replace ( '|\r\n|', '<br />', stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_limittxt'])) . $hide;
	}

	### for comment luv
	get_currentuserinfo();
	global $user_level;

	### Subscribe-To-Comments
	$isSubscribed=='';
	if ( class_exists('sg_subscribe') ){
		global $sg_subscribe;
		sg_subscribe_start();
		$isSubscribed = $sg_subscribe->current_viewer_subscription_status();
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

			 		switch( $field_stat[1] ){
						case 'cauthor':
							$track['cauthor'] = $user->display_name;
							$track['$$$'.((int)$i+(int)$off)] = 'cauthor';
							break;
						case 'email':
							$track['email'] = $field_email = $user->user_email;
							$track['$$$'.((int)$i+(int)$off)] = 'email';
							break;
						case 'url':
							$track['url'] = $user->user_url;
							$track['$$$'.((int)$i+(int)$off)] = 'url';
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
				$idPartA = strpos($field_name,'[id:');
				$idPartB = strpos($field_name,']',$idPartA);
				$customTrackingID = substr($field_name,$idPartA+4,($idPartB-$idPartA)-4);
				$field_name = substr_replace($field_name,'',$idPartA,($idPartB-$idPartA)+1);
			}
			else
				$customTrackingID='';


			###  check if fields needs to be cleared
		    $obj = explode('|', $field_name,3);
			$defaultval = stripslashes($obj[1]);
			if ( $params ['field_' . $i] == $defaultval && $field_stat[4]=='1')
				$params ['field_' . $i] = '';

			###  strip out default value
			$field_name = $obj[0];


			###  special WP comment fields
			if( in_array($field_stat[1],array('luv','subscribe','cauthor','email','url','comment','send2author')) ){
				$field_name = $field_stat[1];
				if ( $field_stat[1] == 'email' )
					$field_email = $params['field_' . $i];
			}

			###  special Tell-A-Friend fields
			if ( $taf_friendsemail == '' && $field_type=='friendsemail' && $field_stat[3]=='1')
					$field_email = $taf_friendsemail = $params ['field_' . $i];
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
			  if ($field_type == "ccbox" && $params ['field_' . $i]<>"-" )
			      $ccme = 'field_' . $i;
			}

			if ( $field_type == "emailtobox" ){  			### special case where the value needs to bet get from the DB!

				$field_name = explode('#',$field_stat[0]);  ### can't use field_name, since '|' check earlier
				$to_one = $params ['field_' . $i];

				$tmp = explode('|set:', $field_name[1] );	###  remove possible |set:true
				$offset = (strpos($tmp[0],'|')===false) ? 1 : 2; ###  names come usually right after the label


				$value = $field_name[(int)$to_one+$offset];  ###  values start from 0 or after!
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


			### for db tracking
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


	###  assemble text & html email
	$r = formatEmail($track,$no);
    $formdata = $r['text'];
    $htmlformdata = $r['html'];


    ### multi-form session
    /*
    $_SESSION['cforms']['count']++;
    $_SESSION['cforms']['pos']++;
    $_SESSION['cforms']['current']=$no;
    $_SESSION['cforms']['cf_form'.$no] = $track;
	*/

	###
	###  allow the user to use form data for other apps
	###
	$trackf['id'] = $no;
	$trackf['data'] = $track;
	if( function_exists('my_cforms_action') )
		my_cforms_action($trackf);



	###  Catch WP-Comment function
	if ( $isAjaxWPcomment!==false && $track['send2author']=='0' ){

		require_once (dirname(__FILE__) . '/lib_WPcomment.php');

		if ($WPsuccess){

			### send a notification if required
			if ( $cformsSettings['form'.$no]['cforms'.$no.'_tellafriend']=='21' )
				ajaxMail($isAjaxWPcomment,$no,$field_email,$formdata,$htmlformdata,$taf_youremail,$taf_friendsemail,$taf_yourname,$taf_youremail,$ccme,$to_one,$segments);

			$hide='';
			###  redirect to a different page on suceess?
			if      ( $cformsSettings['form'.$no]['cforms'.$no.'_redirect']==1 ){
	            if ( function_exists('my_cforms_logic') )
	                return my_cforms_logic($trackf, $cformsSettings['form'.$no]['cforms'.$no.'_redirect_page'],'redirection');  ### use trackf!
	            else
	            	return $cformsSettings['form'.$no]['cforms'.$no.'_redirect_page'];
			} else if ( $cformsSettings['form'.$no]['cforms'.$no.'_redirect']==2 )	$hide = '|~~~';

		    $pre = $segments[0].'*$#'.substr($cformsSettings['form'.$no]['cforms'.$no.'_popup'],0,1);
		    return $pre . $WPresp . $hide;
		}
		else {
		    $pre = $segments[0].'*$#'.substr($cformsSettings['form'.$no]['cforms'.$no.'_popup'],1,1);
		    return $pre . $WPresp .'|---';
		}

	}

	return ajaxMail($isAjaxWPcomment,$no,$field_email,$formdata,$htmlformdata,$taf_youremail,$taf_friendsemail,$taf_yourname,$taf_youremail,$ccme,$to_one,$segments);

} ### function


function ajaxMail($isAjaxWPcomment,$no,$field_email,$formdata,$htmlformdata,$taf_youremail,$taf_friendsemail,$taf_yourname,$taf_youremail,$ccme,$to_one,$segments){

	global $wpdb, $subID, $styles, $smtpsettings, $track, $trackf, $Ajaxpid, $AjaxURL, $wp_locale, $cformsSettings;

	###
	### reply to all email recipients
	###
	$replyto = preg_replace( array('/;|#|\|/'), array(','), stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_email']) );

	###  multiple recipients? and to whom is the email sent? to_one = picked recip.
	if ( $isAjaxWPcomment!==false && $track['send2author']=='1' ){
			$to = $wpdb->get_results("SELECT U.user_email FROM $wpdb->users as U, $wpdb->posts as P WHERE P.ID = {$Ajaxpid} AND U.ID=P.post_author");
			$to = $replyto = ($to[0]->user_email<>'')?$to[0]->user_email:$replyto;
	}
	else if ( $to_one <> "-1" ) {
			$all_to_email = explode(',', $replyto);
			$replyto = $to = $all_to_email[ $to_one ];
	} else
			$to = $replyto;

	###  T-A-F override?
	if ( substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)=='1' && $taf_youremail && $taf_friendsemail )
		$replyto = "\"{$taf_yourname}\" <{$taf_youremail}>";



	###
	###  FIRST write into the cforms tables!
	###
	$subID = write_tracking_record($no,$field_email);

	###
	###  ready to send email
	###  email header
	###

	$html_show = ( substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],2,1)=='1' )?true:false;

	$fmessage='';
	$htmlmessage='';

	$eol = "\n";

	$frommail = check_cust_vars(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_fromemail']),$track,$no);
	if ( $frommail=='' )
		$frommail = '"'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>';

	$headers = "From: ". $frommail . $eol;
	$headers.= "Reply-To: " . $field_email . $eol;

	if ( ($tempBcc = stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_bcc'])) != "")
	    $headers.= "Bcc: " . $tempBcc . $eol;

	$headers.= "MIME-Version: 1.0"  .$eol;
	if ($html_show) {
		$headers.= "Content-Type: multipart/alternative; boundary=\"----MIME_BOUNDRY_main_message\"";
		$fmessage = "This is a multi-part message in MIME format."  . $eol;
		$fmessage .= "------MIME_BOUNDRY_main_message"  . $eol;
		$fmessage .= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"" . $eol;
		$fmessage .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;
	}
	else
		$headers.= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"";

	###  prep message text, replace variables
	$message = $cformsSettings['form'.$no]['cforms'.$no.'_header'];
	$message = check_default_vars($message,$no);
	$message = stripslashes( check_cust_vars($message,$track,$no) );

	###  text text
	$fmessage .= $message . $eol;

	###  need to add form data summary or is all in the header anyway?
	if(substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],0,1)=='1')
		$fmessage .= $eol . $formdata . $eol;


	###  HTML text
	if ($html_show) {

		###  actual user message
		$htmlmessage = $cformsSettings['form'.$no]['cforms'.$no.'_header_html'];
		$htmlmessage = check_default_vars($htmlmessage,$no);
		$htmlmessage = str_replace(array("=","\n"),array("=3D","<br />\n"), stripslashes( check_cust_vars($htmlmessage,$track,$no) ) );

		$fmessage .= "------MIME_BOUNDRY_main_message"  . $eol;
		$fmessage .= "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"". $eol;
		$fmessage .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;

		$fmessage .= "<!DOCTYPE HTML PUBLIC \"-### W3C### DTD HTML 4.0 Transitional### EN\">"  . $eol;
		$fmessage .= "<HTML>" . $eol;
		$fmessage .= "<BODY>" . $eol;

		$fmessage .= $htmlmessage;

		###  need to add form data summary or is all in the header anyway?
		if(substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],1,1)=='1')
			$fmessage .= $eol . $htmlformdata;

		$fmessage .= "</BODY></HTML>"  . $eol . $eol;

	}


	### either use configured subject or user determined
	$vsubject = $cformsSettings['form'.$no]['cforms'.$no.'_subject'];
	$vsubject = check_default_vars($vsubject,$no);
	$vsubject = stripslashes( check_cust_vars($vsubject,$track,$no) );

    ### logic: dynamic admin email address
    if ( function_exists('my_cforms_logic') )
        $to = my_cforms_logic($trackf, $to,'adminTO');  ### use trackf!

	###  SMTP server or native PHP mail() ?
	if ( $smtpsettings[0]=='1' )
		$sentadmin = cforms_phpmailer( $no, $frommail, $field_email, $to, $vsubject, $message, $formdata, $htmlmessage, $htmlformdata );
	else
		$sentadmin = @mail($to, encode_header($vsubject), $fmessage, $headers);

	if( $sentadmin==1 )
	{
		  ###  send copy or notification?
	    if ( ($cformsSettings['form'.$no]['cforms'.$no.'_confirm']=='1' && $field_email<>'') || ($ccme&&$trackf[$ccme]<>'-') )  ###  not if no email & already CC'ed
	    {

					$frommail = check_cust_vars(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_fromemail']),$track,$no);
					if ( $frommail=='' )
						$frommail = '"'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>';

					###  HTML message part?
					$html_show_ac = ( substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],3,1)=='1' )?true:false;

					$automessage = '';

					$headers2 = "From: ". $frommail . $eol;
					$headers2.= "Reply-To: " . $replyto . $eol;

					if ( $taf_youremail && $taf_friendsemail && substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)=='1' ) ### TAF: add CC
						$headers2.= "CC: " . $replyto . $eol;

					$headers2.= "MIME-Version: 1.0"  .$eol;
					if( $html_show_ac || ($html_show && ($ccme&&$trackf[$ccme]<>'-')) ){
						$headers2.= "Content-Type: multipart/alternative; boundary=\"----MIME_BOUNDRY_main_message\"";
						$automessage = "This is a multi-part message in MIME format."  . $eol;
						$automessage .= "------MIME_BOUNDRY_main_message"  . $eol;
						$automessage .= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"" . $eol;
						$automessage .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;
					}
					else
						$headers2.= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"";


					###  actual user message
					$cmsg = $cformsSettings['form'.$no]['cforms'.$no.'_cmsg'];
					$cmsg = check_default_vars($cmsg,$no);
					$cmsg = check_cust_vars($cmsg,$track,$no);

					###  text text
					$automessage .= $cmsg  . $eol;

					###  HTML text
					if ( $html_show_ac ) {

						###  actual user message
						$cmsghtml = $cformsSettings['form'.$no]['cforms'.$no.'_cmsg_html'];
						$cmsghtml = check_default_vars($cmsghtml,$no);
						$cmsghtml = str_replace(array("=","\n"),array("=3D","<br />\n"), check_cust_vars($cmsghtml,$track,$no) );

						$automessage .= "------MIME_BOUNDRY_main_message"  . $eol;
						$automessage .= "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"". $eol;
						$automessage .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;;

						$automessage .= "<!DOCTYPE HTML PUBLIC \"-### W3C### DTD HTML 4.0 Transitional### EN\">"  . $eol;
						$automessage .= "<HTML><BODY>"  . $eol;
						$automessage .= $cmsghtml;
						$automessage .= "</BODY></HTML>"  . $eol . $eol;
					}

					###  replace variables
				    $subject2 = $cformsSettings['form'.$no]['cforms'.$no.'_csubject'];
					$subject2 = check_default_vars($subject2,$no);
					$subject2 = check_cust_vars($subject2,$track,$no);

					###  different cc & ac subjects?
					$t = explode('$#$',$subject2);
					$t[1] = ($t[1]<>'') ? $t[1] : $t[0];


					###  email tracking via 3rd party?
					$field_email = ($cformsSettings['form'.$no]['cforms'.$no.'_tracking']<>'')?$field_email.$cformsSettings['form'.$no]['cforms'.$no.'_tracking']:$field_email;

					###  if in Tell-A-Friend Mode, then overwrite header stuff...
					if ( $taf_youremail && $taf_friendsemail && substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)=='1' )
						$field_email = "\"{$taf_friendsname}\" <{$taf_friendsemail}>";


					if ( $ccme&&$trackf[$ccme]<>'-' ) {
						if ( $smtpsettings[0]=='1' )
							$sent = cforms_phpmailer( $no, $frommail, $replyto, $field_email, stripslashes($t[1]), $message, $formdata, $htmlmessage, $htmlformdata,'ac' );
						else
							$sent = @mail($field_email, encode_header(stripslashes($t[1])), $fmessage, $headers2); ### takes $message!!
					}
					else {
						if ( $smtpsettings[0]=='1' )
							$sent = cforms_phpmailer( $no, $frommail, $replyto, $field_email, stripslashes($t[0]) , $cmsg , '', $cmsghtml, '','ac' );
						else
							$sent = @mail($field_email, encode_header(stripslashes($t[0])), stripslashes($automessage), $headers2);
					}

		  		if( $sent<>'1' ) {
					$err = __('Error occurred while sending the auto confirmation message: ','cforms') . ($smtpsettings[0]?" ($sent)":'');
				    $pre = $segments[0].'*$#'.substr($cformsSettings['form'.$no]['cforms'.$no.'_popup'],1,1);
				    return $pre . $err .'|!!!';

		  		}
	    } ###  cc

		$hide='';
		###  redirect to a different page on suceess?
		if ( $cformsSettings['form'.$no]['cforms'.$no.'_redirect']==1 ) {
			if ( function_exists('my_cforms_logic') )
            	return my_cforms_logic($trackf, $cformsSettings['form'.$no]['cforms'.$no.'_redirect_page'],'redirection');  ### use trackf!
            else
				return $cformsSettings['form'.$no]['cforms'.$no.'_redirect_page'];
		}
		else if ( $cformsSettings['form'.$no]['cforms'.$no.'_redirect']==2 || get_cforms_submission_left($no)==0 )
			$hide = '|~~~';

		###  return success msg
	    $pre = $segments[0].'*$#'.substr($cformsSettings['form'.$no]['cforms'.$no.'_popup'],0,1);

		$successMsg	= check_default_vars(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_success']),$no);
		$successMsg	= check_cust_vars($successMsg,$track,$no);

	    return $pre . preg_replace ( '|\r\n|', '<br />', $successMsg) . $hide;

	} ###  no admin mail sent!

	else {

		###  return error msg
		$err = __('Error occurred while sending the message: ','cforms') . ($smtpsettings[0]?'<br />'.$sentadmin:'');
	    $pre = $segments[0].'*$#'.substr($cformsSettings['form'.$no]['cforms'.$no.'_popup'],1,1);
	    return $pre . $err .'|!!!';
	}

}


###
###  sajax stuff
###

if (!isset($SAJAX_INCLUDED)) {

	$GLOBALS['sajax_version'] = '0.12';
	$GLOBALS['sajax_debug_mode'] = 0;
	$GLOBALS['sajax_export_list'] = array();
	$GLOBALS['sajax_request_type'] = 'POST';
	$GLOBALS['sajax_remote_uri'] = '';
	$GLOBALS['sajax_failure_redirect'] = '';

	function sajax_init() {
	}

	function sajax_get_my_uri() {
		return $_SERVER["REQUEST_URI"];
	}
	$sajax_remote_uri = sajax_get_my_uri();

	function sajax_get_js_repr($value) {
		$type = gettype($value);

		if ($type == "boolean") {
			return ($value) ? "Boolean(true)" : "Boolean(false)";
		}
		elseif ($type == "integer") {
			return "parseInt($value)";
		}
		elseif ($type == "double") {
			return "parseFloat($value)";
		}
		elseif ($type == "array" || $type == "object" ) {
			$s = "{ ";
			if ($type == "object") {
				$value = get_object_vars($value);
			}
			foreach ($value as $k=>$v) {
				$esc_key = sajax_esc($k);
				if (is_numeric($k))
					$s .= "$k: " . sajax_get_js_repr($v) . ", ";
				else
					$s .= "\"$esc_key\": " . sajax_get_js_repr($v) . ", ";
			}
			if (count($value))
				$s = substr($s, 0, -2);
			return $s . " }";
		}
		else {
			$esc_val = sajax_esc($value);
			$s = "'$esc_val'";
			return $s;
		}
	}

	function sajax_handle_client_request() {
		global $sajax_export_list;

		$mode = "";

		if (! empty($_GET["rs"]))
			$mode = "get";

		if (!empty($_POST["rs"]))
			$mode = "post";

		if (empty($mode))
			return;

		$target = "";

		if ($mode == "get") {
			###  Bust cache in the head
			header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    ###  Date in the past
			header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			###  always modified
			header ("Cache-Control: no-cache, must-revalidate");  ###  HTTP/1.1
			header ("Pragma: no-cache");                          ###  HTTP/1.0
			$func_name = $_GET["rs"];
			if (! empty($_GET["rsargs"]))
				$args = $_GET["rsargs"];
			else
				$args = array();
		}
		else {
			$func_name = $_POST["rs"];
			if (! empty($_POST["rsargs"]))
				$args = $_POST["rsargs"];
			else
				$args = array();
		}

		if (! in_array($func_name, $sajax_export_list))
			echo "-:$func_name not callable";
		else {
			$result = call_user_func_array($func_name, $args);
			echo "+:";
			echo "var res = " . trim(sajax_get_js_repr($result)) . "; res;";
		}
		exit;
	}

	###  javascript escape a value
	function sajax_esc($val)
	{
		$val = str_replace("\\", "\\\\", $val);
		$val = str_replace("\r", "\\r", $val);
		$val = str_replace("\n", "\\n", $val);
		$val = str_replace("'", "\\'", $val);
		return str_replace('"', '\\"', $val);
	}

	function sajax_get_one_stub($func_name) {
		ob_start();
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	function sajax_show_one_stub($func_name) {
		echo sajax_get_one_stub($func_name);
	}

	function sajax_export() {
		global $sajax_export_list;

		$n = func_num_args();
		for ($i = 0; $i < $n; $i++) {
			$sajax_export_list[] = func_get_arg($i);
		}
	}

	$sajax_js_has_been_shown = 0;
	function sajax_get_javascript()
	{
		global $sajax_js_has_been_shown;
		global $sajax_export_list;

		$html = "";
		if (! $sajax_js_has_been_shown) {
			$html .= sajax_get_common_js();
			$sajax_js_has_been_shown = 1;
		}
		foreach ($sajax_export_list as $func) {
			$html .= sajax_get_one_stub($func);
		}
		return $html;
	}

	$SAJAX_INCLUDED = 1;
}

###  $sajax_debug_mode = 1;
sajax_init();
sajax_export("cforms_submitcomment");
sajax_export("reset_captcha");
sajax_handle_client_request();
?>