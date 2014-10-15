<?php
// phpmailer support
function cforms_phpmailer( $no, $frommail, $field_email, $to, $vsubject, $message, $formdata, $htmlmessage, $htmlformdata, $fileext='' ) {

	global $smtpsettings, $phpmailer, $cformsSettings;

	if ( file_exists(dirname(__FILE__) . '/class.phpmailer.php') && !class_exists('PHPMailer') ) {
		require_once(dirname(__FILE__) . '/class.phpmailer.php');
		require_once(dirname(__FILE__) . '/class.smtp.php');

		if( $smtpsettings[6]=='1' )
			require_once(dirname(__FILE__) . '/class.pop3.php');
	}

		### pop before smtp?
		if( $smtpsettings[6]=='1' && class_exists('POP3') ){
			$debuglevel = 0;
		    $pop = new POP3();
	    	$pop->Authorise($smtpsettings[7], $smtpsettings[8], 30, $smtpsettings[9], $smtpsettings[10], $debuglevel);
		}

		$mail = new PHPMailer();
		$mail->ClearAllRecipients();
		$mail->ClearAddresses();
		$mail->ClearAttachments();
		$mail->CharSet = 'utf-8';
        $mail->SetLanguage('en', dirname(__FILE__).'/');

		$mail->PluginDir = dirname(__FILE__).'/';

		$mail->IsSMTP();                    // send via SMTP

		$mail->Host			= $smtpsettings[1]; // SMTP servers

		//$mail->SMTPDebug = true;

		if ( $smtpsettings[4]<>'' ){
			$mail->SMTPSecure = $smtpsettings[4];   // sets the prefix to the servier
			$mail->Port       = $smtpsettings[5];   // set the SMTP port
		}

		if ( $smtpsettings[2]<>'' ){
			$mail->SMTPAuth = true;         	// turn on SMTP authentication
			$mail->Username = $smtpsettings[2]; // SMTP username
			$mail->Password = $smtpsettings[3]; // SMTP password
		}


		$temp2=array();
		//from
		if( preg_match('/([\w-\.]+@([\w-]+\.)+[\w-]{2,4})/',$frommail,$temp) )
			$mail->From     = $temp[0];

		if( preg_match('/(.*)\s+(([\w-\.]+@|<)).*/',$frommail,$temp2) )
			$mail->FromName = str_replace('"','',$temp2[1]);
		else
			$mail->FromName = $temp[0];

		$temp2=array();

		//reply-to
		if( preg_match('/([\w-\.]+@([\w-]+\.)+[\w-]{2,4})/',$field_email,$temp) ) {
			if ( preg_match('/(.*)\s+(([\w-\.]+@|<)).*/',$field_email,$temp2) )
				$mail->AddReplyTo($temp[0] ,str_replace('"','',$temp2[1]) );
			else
				$mail->AddReplyTo($temp[0]);
		}
		//TAF: add CC
		if ( substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)=='1' && $fileext=='ac')
			$mail->AddCC($temp[0],str_replace('"','',$temp2[1]));


		//bcc
		if( preg_match('/[\w-\.]+@([\w-]+\.)+[\w-]{2,4}/',stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_bcc']),$temp) )
			$mail->AddBCC($temp[0]);

		//to
		//if( preg_match('/[\w-\.]+@([\w-]+\.)+[\w-]{2,4}/',$to,$temp) )
		//	$mail->AddAddress($temp[0]);

		$addresses = explode(',',$to);
		foreach( $addresses as $address ){

			if( preg_match('/([\w-\.]+@([\w-]+\.)+[\w-]{2,4})/',$address,$temp) ) {
				if ( preg_match('/(.*)\s+(([\w-\.]+@|<)).*/',$address,$temp2) )
					$mail->AddAddress($temp[0] ,str_replace('"','',$temp2[1]) );
				else
					$mail->AddAddress($temp[0]);
			}

		}

		if ($htmlmessage<>'') {     // send as HTML
			$htmlmessage = str_replace('=3D','=',$htmlmessage);  //remove 3D's
			$htmlformdata = str_replace('=3D','=',$htmlformdata);  //remove 3D's,
			$mail->IsHTML(true);
			$mail->Body     =  "<html>\r\n".$styles."<body>".stripslashes($htmlmessage).
            					( (substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],1,1)=='1' && $htmlformdata<>'') ? $eol.$htmlformdata : '' ).
                                "\r\n</body></html>\r\n";
			$mail->AltBody  =  stripslashes($message).((substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],0,1)=='1'&&$formdata<>'')?$eol.$formdata:'');
		}
		else
			$mail->Body     =  stripslashes($message).((substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],0,1)=='1'&&$formdata<>'')?$eol.$formdata:'');

		// possibly add attachment with indiv. mime types
		if ( $fileext<>'ac' && $fileext<>'' && count($_FILES['cf_uploadfile'.$no]['tmp_name']) > 0 && isset($_FILES['cf_uploadfile'.$no]) && !$cformsSettings['form'.$no]['cforms'.$no.'_noattachments'] ) {

		 		$all_mime = array("txt"=>"text/plain", "htm"=>"text/html", "html"=>"text/html", "gif"=>"image/gif", "png"=>"image/x-png",
		 						 "jpeg"=>"image/jpeg", "jpg"=>"image/jpeg", "tif"=>"image/tiff", "bmp"=>"image/x-ms-bmp", "wav"=>"audio/x-wav",
		 						 "mpeg"=>"video/mpeg", "mpg"=>"video/mpeg", "mov"=>"video/quicktime", "avi"=>"video/x-msvideo",
		 						 "rtf"=>"application/rtf", "pdf"=>"application/pdf", "zip"=>"application/zip", "hqx"=>"application/mac-binhex40",
		 						 "sit"=>"application/x-stuffit", "exe"=>"application/octet-stream", "ppz"=>"application/mspowerpoint",
								 "ppt"=>"application/vnd.ms-powerpoint", "ppj"=>"application/vnd.ms-project", "xls"=>"application/vnd.ms-excel",
								 "doc"=>"application/msword");

	            $temp = explode( '$#$',stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_upload_dir'])) );
	            $fileuploaddir = $temp[0];

				for ( $filefield=0; $filefield < count($_FILES['cf_uploadfile'.$no][name]); $filefield++) {
					if ( $_FILES['cf_uploadfile'.$no]['size'][$filefield] > 0 ){
						$mime = (!$all_mime[$fileext[$filefield]])?'application/octet-stream':$all_mime[$fileext[$filefield]];
						$mail->AddAttachment($_FILES['cf_uploadfile'.$no]['tmp_name'][$filefield], $_FILES['cf_uploadfile'.$no]['name'][$filefield],'base64',$mime); // optional name
					}

				}

 		}

		$mail->Subject  = $vsubject;
		$sentadmin      = $mail->Send();

		if ($sentadmin)
			return true;
		else
			return $mail->ErrorInfo;
}
?>