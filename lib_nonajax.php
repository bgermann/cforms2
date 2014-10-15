<?php
###
###  VALIDATE all fields
###

$CFfunctionsC = dirname(dirname(__FILE__)).$cformsSettings['global']['cforms_IIS'].'cforms-custom'.$cformsSettings['global']['cforms_IIS'].'my-functions.php';
$CFfunctions = dirname(__FILE__).$cformsSettings['global']['cforms_IIS'].'my-functions.php';
if ( file_exists($CFfunctionsC) )
    include_once($CFfunctionsC);
else if ( file_exists($CFfunctions) )
    include_once($CFfunctions);


require_once (dirname(__FILE__) . '/lib_validate.php');


if( isset($_POST['sendbutton'.$no]) && $all_valid ) {

	    ###
	    ###  all valid? get ready to send
	    ###
		if( function_exists('my_cforms_filter') )
			$_POST = my_cforms_filter($_POST);

		if ( ($cformsSettings['form'.$no]['cforms'.$no.'_maxentries']<>'' && get_cforms_submission_left($no)==0) || !cf_check_time($no) ){
			$cflimit = 'reached';
			return;
		}

		$usermessage_text = preg_replace ( '|\r\n|', '<br />', stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_success']) );

		$track = array();
		$trackinstance = array();

  		$to_one = "-1";
		$ccme = false;
		$field_email = '';

		$filefield=0;
		$taf_youremail = false;
		$taf_friendsemail = false;
		$send2author = false;

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

				preg_match('/^([^#\|]*).*/',$field_name,$input_name);

				if ( strpos($input_name[1],'[id:')!==false ){
					$idPartA = strpos($input_name[1],'[id:');
					$idPartB = strpos($input_name[1],']',$idPartA);
					$customTrackingID = substr($input_name[1],$idPartA+4,($idPartB-$idPartA)-4);
					$current_field = cf_sanitize_ids( $customTrackingID );

					$field_name = substr_replace($input_name[1],'',$idPartA,($idPartB-$idPartA)+1);
				} else{
					$current_field = cf_sanitize_ids($input_name[1]);
					$customTrackingID='';
				}

			}
			else
				$current_field = 'cf'.$no.'_field_' . $i;

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
			  if ($field_type == "ccbox" && isset($_POST[$current_field]) )
			      $ccme = $field_name;
			}


		if ( $field_type == "emailtobox" ){  				### special case where the value needs to bet get from the DB!

            $field_name = explode('#',$field_stat[0]);  ### can't use field_name, since '|' check earlier
            $to_one = $_POST[$current_field];

			$tmp = explode('|set:', $field_name[1] );	###  remove possible |set:true
            $offset = (strpos($tmp[0],'|')===false) ? 1 : 2; ###  names come usually right after the label

            $value 	= $field_name[(int)$to_one+$offset];  ###  values start from 0 or after!
            $field_name = $field_name[0];
 		}
 		else if ( $field_type == "upload" ){

 			### $fsize = $file['size'][$filefield]/1000;
 			$value = str_replace(' ','_',$file['name'][$filefield++]);

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

		else
			$value = $_POST[$current_field];       ###  covers all other fields' values

		### check boxes
		if ( $field_type == "checkbox" || $field_type == "ccbox" ) {

				if ( isset($_POST[$current_field]) )
					$value = ($_POST[$current_field]<>'')?$_POST[$current_field]:'X';
				else
					$value = '-';

		} else if ( $field_type == "radiobuttons" ) {

				if ( ! isset($_POST[$current_field]) )
					$value = '-';

		}

		### determine tracked field name
        $inc='';
        $trackname = trim( ($field_type == "upload")?$field_name.'[*'.($no==''?1:$no).']':$field_name );
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

    ### multi-form session
	$inSession = 'noSess';
	if( $cformsSettings['form'.$no]['mp']['cforms'.$no.'_mp_form'] ){
		if( $field_email<>'' )
       		$_SESSION['cforms']['email']=$field_email;
		$_SESSION['cforms']['list'][$_SESSION['cforms']['pos']++]=$no;
	    $_SESSION['cforms']['current']=$no==''?1:$no;
	    $_SESSION['cforms']['cf_form'.$no] = $track;
        $field_email = $_SESSION['cforms']['email']; ### fetch from prev. def
		$inSession = '1';
	}


	###  assemble text & html email
	if( $cformsSettings['form'.$no]['mp']['cforms'.$no.'_mp_form'] &&
    	!$cformsSettings['form'.$no]['mp']['cforms'.$no.'_mp_email'] &&
    	$cformsSettings['form'.$no]['mp']['cforms'.$no.'_mp_next']==-1 &&
        is_array($_SESSION['cforms']) ){
			$track = allTracks($_SESSION['cforms']);
            $inSession = '0';
		}


    $r = formatEmail($track,$no);
    $formdata = $r['text'];
    $htmlformdata = $r['html'];


	###
	###  FIRST into the database is required!
	###
	$subID = ( $isTAF =='2' && !$send2author )?'noid':write_tracking_record($no,$field_email);


	###
	###  allow the user to use form data for other apps
	###
	$trackf['id'] = $no;
	$trackf['data'] = $track;
	if( function_exists('my_cforms_action') )
		my_cforms_action($trackf);



	###
	### set reply-to & watch out for T-A-F
	###
	$replyto = preg_replace( array('/;|#|\|/'), array(','), stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_email']) );

	###  WP comment form > email to author
	if ( $isTAF=='2' && $track['send2author']=='1'){
			$to = $wpdb->get_results("SELECT U.user_email FROM $wpdb->users as U, $wpdb->posts as P WHERE P.ID = ".($_POST['comment_post_ID'.$no])." AND U.ID=P.post_author");
			$to = $replyto =  ($to[0]->user_email<>'')?$to[0]->user_email:$replyto;
	}
    ### multiple recipients? and to whom is the email sent?
	else if ( $to_one <> "-1" ) {
			$all_to_email = explode(',', $replyto);
			$replyto = $to = $all_to_email[ $to_one ];
	} else
			$to = $replyto;

	### T-A-F overwrite
	if ( $taf_youremail && $taf_friendsemail && $isTAF=='1' )
		$replyto = "\"{$taf_yourname}\" <{$taf_youremail}>";



	###
	###  Files attached??
	###
	if(is_array($file)){
	    if( $subID<>-1 && $inSession!='0' )
	        cf_move_files($no, $subID);
	    else
	        cf_move_files($no, 'xx');
	}
    if( $inSession=='0' && is_array($_SESSION['cforms']['upload']) ){
    	foreach ( array_keys($_SESSION['cforms']['upload']) as $n )
	    	foreach ( array_keys($_SESSION['cforms']['upload'][$n]) as $m )
				if( file_exists($_SESSION['cforms']['upload'][$n][$m]) )
	                rename($_SESSION['cforms']['upload'][$n][$m],str_replace('xx',$subID,$_SESSION['cforms']['upload'][$n][$m]));
    }



	###
	###  ready to send email
	###  email header
	###
	$eolH = "\r\n";
    $eol = ($cformsSettings['global']['cforms_crlf']!=1)?"\r\n":"\n";

	$frommail = check_cust_vars(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_fromemail']),$track,$no);
	if ( $frommail=='' )
		$frommail = '"'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>';

	$headers = 'From: '. $frommail . $eolH;
	$headers.= 'Reply-To: ' . $field_email . $eolH;

	if ( ($tempBcc=stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_bcc'])) != "")
	    $headers.= 'Bcc: ' . $tempBcc . $eolH;

	$headers.= 'MIME-Version: 1.0'  .$eolH;
	$headers.= 'Content-Type: multipart/mixed; boundary="----MIME_BOUNDRY_main_message"';

	###  prep message text, replace variables
	$message	= stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_header']);
	if ( function_exists('my_cforms_logic') )
		$message = my_cforms_logic($trackf, $message,'adminEmailTXT');
	$message	= check_default_vars($message,$no);
	$message	= check_cust_vars($message,$track,$no);

	###  text & html message
	$fmessage = 'This is a multi-part message in MIME format.'  . $eol;
	$fmessage .= '------MIME_BOUNDRY_main_message'  . $eol;


	###  HTML message part?
	$html_show = ( substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],2,1)=='1' )?true:false;
	$htmlmessage = '';

	if ( $html_show ) {
		$fmessage .= 'Content-Type: multipart/alternative; boundary="----MIME_BOUNDRY_sub_message"' . $eol . $eol;
		$fmessage .= '------MIME_BOUNDRY_sub_message'  . $eol;
		$fmessage .= 'Content-Type: text/plain; charset="' . get_option('blog_charset') . '"; format=flowed' . $eol;
		$fmessage .= 'Content-Transfer-Encoding: quoted-printable'  . $eol . $eol;
    }
	else
		$fmessage .= 'Content-Type: text/plain; charset="' . get_option('blog_charset') . '"; format=flowed' . $eol . $eol;

	$fmessage .= $message . $eol;

	###  need to add form data summary or is all in the header anyway?
	if(substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],0,1)=='1')
		$fmessage .= $eol . $formdata . $eol;


	###  HTML text
	if ( $html_show ) {

		###  actual user message
		$htmlmessage = stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_header_html']);
	    if ( function_exists('my_cforms_logic') )
	        $htmlmessage = my_cforms_logic($trackf, $htmlmessage,'adminEmailHTML');
		$htmlmessage = check_default_vars($htmlmessage,$no);
		$htmlmessage = str_replace('=','=3D', check_cust_vars($htmlmessage,$track,$no) );

		$fmessage .= '------MIME_BOUNDRY_sub_message'  . $eol;
		$fmessage .= 'Content-Type: text/html; charset="' . get_option('blog_charset') . '"' . $eol;
		$fmessage .= 'Content-Transfer-Encoding: quoted-printable'  . $eol . $eol;

		$fmessage .= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'  . $eol;
		$fmessage .= '<html><body>' . $eol;

		$fmessage .= $htmlmessage;

		###  need to add form data summary or is all in the header anyway?
		if(substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],1,1)=='1')
			$fmessage .= $eol . $htmlformdata;

		$fmessage .= '</body></html>'  . $eol . $eol;

		$fmessage .= '------MIME_BOUNDRY_sub_message--'  . $eol;

	}
	###  end of sub message


	###
	###  finally send mails
	###

	### either use configured subject or user determined
	### now replace the left over {xyz} variables with the input data
	$vsubject = stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_subject']);
	$vsubject = check_default_vars($vsubject,$no);
	$vsubject = check_cust_vars($vsubject,$track,$no);

    ### logic: dynamic admin email address
    if ( function_exists('my_cforms_logic') )
        $to = my_cforms_logic($trackf, $to,'adminTO');  ### use trackf!


	### Skip admin email when MP form
    $MPok = !$cformsSettings['form'.$no]['mp']['cforms'.$no.'_mp_form'] || ($cformsSettings['form'.$no]['mp']['cforms'.$no.'_mp_form'] && !$cformsSettings['form'.$no]['mp']['cforms'.$no.'_mp_email']);

	if ( $MPok ){



	    ###
	    ### adding attachments now
	    ###
	    $attached='';
		global $fdata,$fpointer;
		$fdata = array();
		$fpointer = 0;

	    ###  attachments wanted?
	    if ( !$cformsSettings['form'.$no]['cforms'.$no.'_noattachments'] ) {

			### no session, single form w/ files
        	if ( $inSession!='0' && is_array($file) ){
				foreach( $file[tmp_name] as $fn ){
						cf_base64($fn);
                }
            }
			### session w/ files
	        if( $inSession=='0' && is_array($_SESSION['cforms']['upload']) ){
	            foreach ( array_keys($_SESSION['cforms']['upload']) as $n )
	                foreach ( array_keys($_SESSION['cforms']['upload'][$n]) as $m )
						cf_base64(str_replace('xx',$subID,$_SESSION['cforms']['upload'][$n][$m]));
	        }

            foreach ( $fdata as $file ) {
				if ( $file[name] <> '' ){

	                $n = substr( $file[name], strrpos($file[name],$cformsSettings['global']['cforms_IIS'])+1, strlen($file[name]) );
	                $m = getMIME( strtolower( substr($n,strrpos($n, '.')+1,strlen($n)) ) );

	                $attached .= $eol . '------MIME_BOUNDRY_main_message' . $eol;
	                $attached .= 'Content-Type: '.$m.';'.$eol."\t".'name="' . $n . '"' . $eol;
	                $attached .= 'Content-Transfer-Encoding: base64' . $eol;
	                $attached .= 'Content-Disposition: inline;'.$eol."\t".'filename="' . $n . '"' . $eol;
	                $attached .= $eol . $file[data] . $eol;  ### The base64 encoded message

				}
            } ### for

	    }
	    ### end adding attachments
		else
	   		$fmessage .= '------MIME_BOUNDRY_main_message--';


		###  SMTP server or native PHP mail() ?
		if( $cformsSettings['form'.$no]['cforms'.$no.'_emailoff']=='1' )
	        $sentadmin = 1;
	    else if ( $smtpsettings[0]=='1' )
            $sentadmin = cforms_phpmailer( $no, $frommail, $field_email, $to, $vsubject, $message, $formdata, $htmlmessage, $htmlformdata, '1' );
	    else
	        $sentadmin = @mail($to, encode_header($vsubject), $fmessage.$attached, $headers);

	    if( $sentadmin==1 ) {
	            ###  send copy or notification?
	            if ( ($cformsSettings['form'.$no]['cforms'.$no.'_confirm']=='1' && $field_email<>'') || ($ccme&&$trackf[$ccme]<>'-') ){  ###  not if no email & already CC'ed

	                $frommail = check_cust_vars(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_fromemail']),$track,$no);
	                if ( $frommail=='' )
	                    $frommail = '"'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>';

	                ###  HTML message part?
	                $html_show_ac = ( substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],3,1)=='1' )?true:false;
	                $automsg = '';

	                $headers2 = 'From: '. $frommail . $eolH;
	                $headers2.= 'Reply-To: ' . $replyto . $eolH;

	                if ( $taf_youremail && $taf_friendsemail && $isTAF=='1' ) ### TAF: add CC
	                    $headers2.= 'CC: ' . $replyto . $eolH;

	                $headers2.= 'MIME-Version: 1.0'  .$eolH;

	                if( $html_show_ac || ($html_show && ($ccme&&$trackf[$ccme]<>'-')) ){
	                    $headers2.= 'Content-Type: multipart/alternative; boundary="----MIME_BOUNDRY_main_message"';
	                    $automsg .= 'This is a multi-part message in MIME format.'  . $eol;
	                    $automsg .= '------MIME_BOUNDRY_main_message'  . $eol;
	                    $automsg .= 'Content-Type: text/plain; charset="' . get_option('blog_charset') . '"; format=flowed' . $eol;
	                    $automsg .= 'Content-Transfer-Encoding: quoted-printable'  . $eol . $eol;
	                }
	                else
	                    $headers2.= 'Content-Type: text/plain; charset="' . get_option('blog_charset') . '"; format=flowed';


	                ###  actual user message
	                $cmsg = stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_cmsg']);
	                if ( function_exists('my_cforms_logic') )
	                    $cmsg = my_cforms_logic($trackf, $cmsg,'autoConfTXT');
	                $cmsg = check_default_vars($cmsg,$no);
	                $cmsg = check_cust_vars($cmsg,$track,$no);


	                ###  text text
	                $automsg .= $cmsg . $eol;

	                ###  HTML text
	                if ( $html_show_ac ) {

	                    ###  actual user message
	                    $cmsghtml = stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_cmsg_html']);
	                    if ( function_exists('my_cforms_logic') )
	                        $cmsghtml = my_cforms_logic($trackf, $cmsghtml,'autoConfHTML');
	                    $cmsghtml = check_default_vars($cmsghtml,$no);
	                    $cmsghtml = str_replace(array("=","\n"),array("=3D","<br />\r\n"), check_cust_vars($cmsghtml,$track,$no) );

	                    $automsg .= $eol . '------MIME_BOUNDRY_main_message'  . $eol;
	                    $automsg .= 'Content-Type: text/html; charset="' . get_option('blog_charset') . '"'  . $eol;
	                    $automsg .= 'Content-Transfer-Encoding: quoted-printable'  . $eol . $eol;

	                    $automsg .= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'  . $eol;
	                    $automsg .= '<html><body>'  . $eol;
	                    $automsg .= $cmsghtml;
	                    $automsg .= '</body></html>'  . $eol . $eol;

	                    $automsg .= '------MIME_BOUNDRY_main_message--';
	                }

	                $subject2 = stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_csubject']);
	                $subject2 = check_default_vars($subject2,$no);
	                $subject2 = check_cust_vars($subject2,$track,$no);

	                ###  different cc & ac subjects?
	                $t=explode('$#$',$subject2);
	                $t[1] = ($t[1]<>'') ? $t[1] : $t[0];

	                ###  email tracking via 3rd party?
	                $field_email = ($cformsSettings['form'.$no]['cforms'.$no.'_tracking']<>'')?$field_email.$cformsSettings['form'.$no]['cforms'.$no.'_tracking']:$field_email;

	                ###  if in Tell-A-Friend Mode, then overwrite header stuff...
	                if ( $taf_youremail && $taf_friendsemail && $isTAF=='1' )
	                    $field_email = "\"{$taf_friendsname}\" <{$taf_friendsemail}>";

	                if ( $ccme&&$trackf[$ccme]<>'-' ) {
	                    if ( $smtpsettings[0]=='1' )
	                        $sent = cforms_phpmailer( $no, $frommail, $replyto, $field_email, $t[1], $message, $formdata, $htmlmessage, $htmlformdata, 'ac' );
	                    else
	                        $sent = @mail($field_email, encode_header($t[1]), $fmessage, $headers2); ### the admin one
	                }
	                else {
	                    if ( $smtpsettings[0]=='1' )
	                        $sent = cforms_phpmailer( $no, $frommail, $replyto, $field_email, $t[0] , $cmsg , '', $cmsghtml, '', 'ac' );
	                    else
	                        $sent = @mail($field_email, encode_header($t[0]), $automsg, $headers2); ### takes the above
	                }

	                if( $sent<>'1' )
	                        $usermessage_text = __('Error occurred while sending the auto confirmation message: ','cforms')." ($sent)";
	            }

	        ###  redirect to a different page on suceess?
	        if ( $cformsSettings['form'.$no]['cforms'.$no.'_redirect']==1 && !$isWPcommentForm ) {
	            if ( function_exists('my_cforms_logic') )
	                $rp = my_cforms_logic($trackf, $cformsSettings['form'.$no]['cforms'.$no.'_redirect_page'],'redirection');  ### use trackf!
	            else
	                $rp = $cformsSettings['form'.$no]['cforms'.$no.'_redirect_page'];

	            ?>
	            <script type="text/javascript">
	                location.href = '<?php echo $rp; ?>';
	            </script>
	            <?php
	        }

	    } ###  if $sentadmin
	    else
	        $usermessage_text = __('Error occurred while sending the message: ','cforms') . '<br />'. $smtpsettings[0]?'<br />'.$sentadmin:'';
	} ### if $MPok


} ### if isset & valid sendbutton

?>