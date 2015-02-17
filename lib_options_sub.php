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

	### set to nothing
	$usermsg='';

	$verification=false;
	$captcha=false;
	$ccbox=false;
	$emailtobox=false;
	$WPc=false;
	$taf=false;
	$uploadfield=false;

	for($i = 1; $i <= $field_count; $i++) {

		if ($_REQUEST['field_' . $i . '_name']<>''){ ### safety

	        $allgood=true;
	        $name = str_replace('$#$', '$', $_REQUEST['field_' . $i . '_name']);
	        $type = $_REQUEST['field_' . $i . '_type'];
	        $required = 0;
	        $emailcheck = 0;
	        $clear = 0;
	        $disabled = 0;
	        $readonly = 0;

			     $isTAF = (int)substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1);

        if( !$uploadfield )
          $uploadfield = ($type == 'upload');

				if( in_array($type, array('cauthor','email','url','comment','send2author')) && !($isTAF==2) ){
					$allgood = $WPc?false:true;
					$usermsg .= '<span class="exMsg">'.__('WP comment form fields only supported when <em>WP comment feature</em> turned on!', 'cforms').'</span>';
					$WPc=true;
				}
				if( in_array($type, array('yourname','youremail','friendsname','friendsemail')) && !($isTAF==1) ){
					$allgood = $taf?false:true;
					$usermsg .= '<span class="exMsg">'.__('TAF fields only supported when <em>TAF feature</em> turned on!', 'cforms').'</span>';
					$taf=true;
				}

				if( $type=='verification' ){
					$allgood = $verification?false:true;
					$usermsg .= $verification?'<span class="exMsg">'.__('Only one <em>Visitor verification</em> field is permitted!', 'cforms').'</span>':'';
					$verification=true;
				}
				if( $type=='captcha' ){
					$allgood = $captcha?false:true;
					$usermsg .= $captcha?'<span class="exMsg">'.__('Only one <em>captcha</em> field is permitted!', 'cforms').'</span>':'';
					$captcha=true;
				}
				if( $type=='ccbox' ){
					$allgood = $ccbox?false:true;
					$usermsg .= $ccbox?'<span class="exMsg">'.__('Only one <em>CC:</em> field is permitted!', 'cforms').'</span>':'';
					$ccbox=true;
				}
				if( $type=='emailtobox' ){
					$allgood = $emailtobox?false:true;
					$usermsg .= $emailtobox?'<span class="exMsg">'.__('Only one <em>Multiple Recipients</em> field is permitted!', 'cforms').'</span>':'';
					$emailtobox=true;
				}

				if(isset($_REQUEST['field_' . $i . '_required']) && ((strpos($type, 'tml5')!==false) || in_array($type,array('pwfield','textfield','datepicker','textarea','checkbox','multiselectbox','selectbox','emailtobox','upload','yourname','youremail','friendsname','friendsemail','email','cauthor','url','comment','radiobuttons'))) ) {
					$required = 1;
				}

				if(isset($_REQUEST['field_' . $i . '_emailcheck']) && in_array($type,array('html5email','textfield','datepicker','youremail','friendsemail','email')) ){
					$emailcheck = 1;
				}

				if(isset($_REQUEST['field_' . $i . '_clear']) && ((strpos($type, 'tml5')!==false) || in_array($type,array('pwfield','textfield','datepicker','textarea','yourname','youremail','friendsname','friendsemail','email','cauthor','url','comment'))) ) {
					$clear = 1;
				}

				if(isset($_REQUEST['field_' . $i . '_disabled']) && ((strpos($type, 'tml5')!==false) || in_array($type,array('pwfield','textarea','datepicker','textfield','checkbox','checkboxgroup','multiselectbox','selectbox','radiobuttons','upload'))) ) {
					$disabled = 1;
				}

				if(isset($_REQUEST['field_' . $i . '_readonly']) && ((strpos($type, 'tml5')!==false) || in_array($type,array('pwfield','textarea','datepicker','textfield','checkbox','checkboxgroup','multiselectbox','selectbox','radiobuttons','upload'))) ) {
					$readonly = 1;
				}

				$all_fields[$i-1] = $name . '$#$' . $type . '$#$' . $required. '$#$' . $emailcheck . '$#$'. $clear . '$#$' . $disabled . '$#$' . $readonly;
				
				if ($allgood)
						$cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . $i] = $all_fields[$i-1];

		}
	}


	### update new settings container
	$cformsSettings['form'.$no]['cforms'.$no.'_fname'] =          preg_replace( array('/\\\+/','/\//','/"/'), array('\\','-','\''), $_REQUEST['cforms_fname'] );
	if (!isset($_REQUEST['cforms_upload_noid']))
		$_REQUEST['cforms_upload_noid'] = "";
	$cformsSettings['form'.$no]['cforms'.$no.'_noid'] =           $_REQUEST['cforms_upload_noid']?'1':'0';
	if (!isset($_REQUEST['cforms_upload_dir']))
		$_REQUEST['cforms_upload_dir'] = "";
	if (!isset($_REQUEST['cforms_upload_dir_url']))
		$_REQUEST['cforms_upload_dir_url'] = "";
	if( $uploadfield && $_REQUEST['cforms_upload_dir']<>'' )
		$cformsSettings['form'.$no]['cforms'.$no.'_upload_dir'] =     $_REQUEST['cforms_upload_dir'].'$#$'.$_REQUEST['cforms_upload_dir_url'];
	$cformsSettings['form'.$no]['cforms'.$no.'_upload_ext'] =     (isset($_REQUEST['cforms_upload_ext'])) ?  $_REQUEST['cforms_upload_ext']:"";
	$cformsSettings['form'.$no]['cforms'.$no.'_upload_size'] =    (isset($_REQUEST['cforms_upload_size'])) ? $_REQUEST['cforms_upload_size']:"";
	$cformsSettings['form'.$no]['cforms'.$no.'_noattachments'] =  (isset($_REQUEST['cforms_noattachments']) && $_REQUEST['cforms_noattachments'])?'1':'0';

	$cformsSettings['form'.$no]['cforms'.$no.'_submit_text'] =   isset($_REQUEST['cforms_submit_text']) ? $_REQUEST['cforms_submit_text']:"";
	$cformsSettings['form'.$no]['cforms'.$no.'_working'] =       isset($_REQUEST['cforms_working']) ? $_REQUEST['cforms_working']:0;
  	$cformsSettings['form'.$no]['cforms'.$no.'_required'] =      isset($_REQUEST['cforms_required']) ? $_REQUEST['cforms_required']:"";
  	$cformsSettings['form'.$no]['cforms'.$no.'_emailrequired'] = isset($_REQUEST['cforms_emailrequired']) ? $_REQUEST['cforms_emailrequired']:"";
	$cformsSettings['form'.$no]['cforms'.$no.'_success'] =       isset($_REQUEST['cforms_success']) ? $_REQUEST['cforms_success']:"";
	$cformsSettings['form'.$no]['cforms'.$no.'_failure'] =       isset($_REQUEST['cforms_failure']) ? $_REQUEST['cforms_failure']:"";

	if (!isset($_REQUEST['cforms_popup1']))   $_REQUEST['cforms_popup1']="";
	if (!isset($_REQUEST['cforms_popup2']))   $_REQUEST['cforms_popup2']="";
	if (!isset($_REQUEST['cforms_showposa'])) $_REQUEST['cforms_showposa']="";
	if (!isset($_REQUEST['cforms_showposb'])) $_REQUEST['cforms_showposb']="";
	if (!isset($_REQUEST['cforms_errorLI']))  $_REQUEST['cforms_errorLI']="";
	if (!isset($_REQUEST['cforms_errorINS'])) $_REQUEST['cforms_errorINS']="";

	$cformsSettings['form'.$no]['cforms'.$no.'_popup'] =   ($_REQUEST['cforms_popup1']?'y':'n').($_REQUEST['cforms_popup2']?'y':'n') ;
	$cformsSettings['form'.$no]['cforms'.$no.'_showpos'] = ($_REQUEST['cforms_showposa']?'y':'n').($_REQUEST['cforms_showposb']?'y':'n').
																	($_REQUEST['cforms_errorLI']?'y':'n').($_REQUEST['cforms_errorINS']?'y':'n').
																	($_REQUEST['cforms_jump']?'y':'n') ;

    if (!isset($_REQUEST['cforms_formaction']))  $_REQUEST['cforms_formaction']  = "";
    if (!isset($_REQUEST['cforms_dontclear']))   $_REQUEST['cforms_dontclear']   = "";
	if (!isset($_REQUEST['cforms_dashboard']))   $_REQUEST['cforms_dashboard']   = "";
	if (!isset($_REQUEST['cforms_notracking']))  $_REQUEST['cforms_notracking']  = "";
	if (!isset($_REQUEST['cforms_customnames'])) $_REQUEST['cforms_customnames'] = "";
	if (!isset($_REQUEST['cforms_hide']))		 $_REQUEST['cforms_hide']		 = "";

	if (!isset($_REQUEST['cforms_startdate']))  $_REQUEST['cforms_startdate'] ="";
	if (!isset($_REQUEST['cforms_starttime']))  $_REQUEST['cforms_starttime'] ="";
	if (!isset($_REQUEST['cforms_enddate']))    $_REQUEST['cforms_enddate']   ="";
	if (!isset($_REQUEST['cforms_endtime']))    $_REQUEST['cforms_endtime']   ="";
	
	
	$cformsSettings['form'.$no]['cforms'.$no.'_formaction'] =    $_REQUEST['cforms_formaction']?true:false;
	$cformsSettings['form'.$no]['cforms'.$no.'_dontclear'] =     $_REQUEST['cforms_dontclear']?true:false;
	$cformsSettings['form'.$no]['cforms'.$no.'_dashboard'] =	 $_REQUEST['cforms_dashboard']?'1':'0';
    $cformsSettings['form'.$no]['cforms'.$no.'_notracking'] =    $_REQUEST['cforms_notracking']?true:false;
	$cformsSettings['form'.$no]['cforms'.$no.'_customnames'] =	 $_REQUEST['cforms_customnames']?'1':'0';
	$cformsSettings['form'.$no]['cforms'.$no.'_hide'] =			 $_REQUEST['cforms_hide']?true:false;

	$cformsSettings['form'.$no]['cforms'.$no.'_maxentries'] =	 				$_REQUEST['cforms_maxentries']==''?'':(int)$_REQUEST['cforms_maxentries'];
	if ($_REQUEST['cforms_startdate']<>'' && $_REQUEST['cforms_starttime']=='') $_REQUEST['cforms_starttime'] = '00:00';
	if ($_REQUEST['cforms_starttime']<>'' && $_REQUEST['cforms_startdate']=='') $_REQUEST['cforms_startdate'] = date('d/m/Y');
	if ($_REQUEST['cforms_enddate']<>'' && $_REQUEST['cforms_endtime']=='')     $_REQUEST['cforms_endtime'] = '00:00';
	if ($_REQUEST['cforms_endtime']<>'' && $_REQUEST['cforms_enddate']=='')     $_REQUEST['cforms_enddate'] = date('d/m/Y');
	$cformsSettings['form'.$no]['cforms'.$no.'_startdate'] = 					preg_replace("/\\\+/", "\\",$_REQUEST['cforms_startdate']).' '.
    																			preg_replace("/\\\+/", "\\",$_REQUEST['cforms_starttime']);
    $cformsSettings['form'.$no]['cforms'.$no.'_enddate'] =  					preg_replace("/\\\+/", "\\",$_REQUEST['cforms_enddate']).' '.
    																			preg_replace("/\\\+/", "\\",$_REQUEST['cforms_endtime']);
	if( isset($_REQUEST['cforms_limittxt']) )
		$cformsSettings['form'.$no]['cforms'.$no.'_limittxt'] = $_REQUEST['cforms_limittxt'];
	else
		$cformsSettings['form'.$no]['cforms'.$no.'_limittxt'] = "";

	if (!isset($_REQUEST['cforms_redirect']))        $_REQUEST['cforms_redirect']="";
	if (!isset($_REQUEST['cforms_redirect_page']))   $_REQUEST['cforms_redirect_page']="";
	if (!isset($_REQUEST['cforms_action']))          $_REQUEST['cforms_action']="";
	if (!isset($_REQUEST['cforms_action_page']))     $_REQUEST['cforms_action_page']="";
	if (!isset($_REQUEST['cforms_rss']))             $_REQUEST['cforms_rss']="";
	if (!isset($_REQUEST['cforms_rsscount']))        $_REQUEST['cforms_rsscount']="";
	
	$cformsSettings['form'.$no]['cforms'.$no.'_redirect'] =       $_REQUEST['cforms_redirect']?true:false;
	$cformsSettings['form'.$no]['cforms'.$no.'_redirect_page'] =  preg_replace("/\\\+/", "\\",$_REQUEST['cforms_redirect_page']);
	$cformsSettings['form'.$no]['cforms'.$no.'_action'] =         $_REQUEST['cforms_action']?'1':'0';
	$cformsSettings['form'.$no]['cforms'.$no.'_action_page'] =    preg_replace("/\\\+/", "\\",$_REQUEST['cforms_action_page']);
	$cformsSettings['form'.$no]['cforms'.$no.'_rss'] =            $_REQUEST['cforms_rss']?true:false;
	$cformsSettings['form'.$no]['cforms'.$no.'_rss_count'] =      $_REQUEST['cforms_rsscount'];
	
	if( isset($_REQUEST['cforms_rssfields']) ){
		$i=1;
		foreach($_REQUEST['cforms_rssfields'] as $e) {
        	$cformsSettings['form'.$no]['cforms'.$no.'_rss_fields'][$i++] = $e;
        }
	}

	if (!isset($_REQUEST['cforms_emailoff']     ))  $_REQUEST['cforms_emailoff']= "";
	if (!isset($_REQUEST['cforms_emptyoff']     ))  $_REQUEST['cforms_emptyoff']= "";
	if (!isset($_REQUEST['cforms_fromemail']    ))  $_REQUEST['cforms_fromemail']= "";
	if (!isset($_REQUEST['cforms_email']        ))  $_REQUEST['cforms_email']= "";
	if (!isset($_REQUEST['cforms_bcc']          ))  $_REQUEST['cforms_bcc']= "";
	if (!isset($_REQUEST['cforms_subject']      ))  $_REQUEST['cforms_subject']= "";
	if (!isset($_REQUEST['emailprio']           ))  $_REQUEST['emailprio']= "";
	if (!isset($_REQUEST['cforms_header']       ))  $_REQUEST['cforms_header']= "";
	if (!isset($_REQUEST['cforms_header_html']  ))  $_REQUEST['cforms_header_html']= "";
	if (!isset($_REQUEST['cforms_formdata_txt'] ))  $_REQUEST['cforms_formdata_txt']= "";
	if (!isset($_REQUEST['cforms_formdata_html']))  $_REQUEST['cforms_formdata_html']= "";
	if (!isset($_REQUEST['cforms_admin_html']   ))  $_REQUEST['cforms_admin_html']= "";
	if (!isset($_REQUEST['cforms_user_html']    ))  $_REQUEST['cforms_user_html']= "";
	if (!isset($_REQUEST['cforms_space']        ))  $_REQUEST['cforms_space']= "";

	$cformsSettings['form'.$no]['cforms'.$no.'_emailoff'] =		 $_REQUEST['cforms_emailoff']?'1':'0';
	$cformsSettings['form'.$no]['cforms'.$no.'_emptyoff'] =		 $_REQUEST['cforms_emptyoff']?'1':'0';
	$cformsSettings['form'.$no]['cforms'.$no.'_fromemail'] =     $_REQUEST['cforms_fromemail'];
	$cformsSettings['form'.$no]['cforms'.$no.'_email'] =         $_REQUEST['cforms_email'];
	$cformsSettings['form'.$no]['cforms'.$no.'_bcc'] =           $_REQUEST['cforms_bcc'];
	$cformsSettings['form'.$no]['cforms'.$no.'_subject'] =       $_REQUEST['cforms_subject'];
	$cformsSettings['form'.$no]['cforms'.$no.'_emailpriority'] = $_REQUEST['emailprio'];
	$cformsSettings['form'.$no]['cforms'.$no.'_header'] =        preg_replace("/\\\+/", "\\",$_REQUEST['cforms_header']);
	$cformsSettings['form'.$no]['cforms'.$no.'_header_html'] =   preg_replace("/\\\+/", "\\",$_REQUEST['cforms_header_html']);
	$cformsSettings['form'.$no]['cforms'.$no.'_formdata'] =      ($_REQUEST['cforms_formdata_txt']?'1':'0').($_REQUEST['cforms_formdata_html']?'1':'0').
    															 ($_REQUEST['cforms_admin_html']?'1':'0').($_REQUEST['cforms_user_html']?'1':'0') ;
	$cformsSettings['form'.$no]['cforms'.$no.'_space'] =         $_REQUEST['cforms_space'];

    ## quickly get old vals
    $t=explode('$#$',$cformsSettings['form'.$no]['cforms'.$no.'_csubject']);
	if( !isset($_REQUEST['cforms_confirm']))  $_REQUEST['cforms_confirm'] = "";
	if( !isset($_REQUEST['cforms_cattachment']))  $_REQUEST['cforms_cattachment'] = "";
	if( !isset($_REQUEST['cforms_csubject']))     $_REQUEST['cforms_csubject'] = "";
	if( !isset($_REQUEST['cforms_cmsg']))         $_REQUEST['cforms_cmsg'] = "";
	if( !isset($_REQUEST['cforms_cmsg_html']))    $_REQUEST['cforms_cmsg_html'] = "";
	
    if( $_REQUEST['cforms_confirm'] && $cformsSettings['form'.$no]['cforms'.$no.'_confirm']==1 ){
        $t[0] = 													  preg_replace("/\\\+/", "\\",$_REQUEST['cforms_csubject']);
	    $cformsSettings['form'.$no]['cforms'.$no.'_cattachment'][0] = $_REQUEST['cforms_cattachment'];
	    $cformsSettings['form'.$no]['cforms'.$no.'_cmsg'] =     	  preg_replace("/\\\+/", "\\",$_REQUEST['cforms_cmsg']);
	    $cformsSettings['form'.$no]['cforms'.$no.'_cmsg_html'] =	  preg_replace("/\\\+/", "\\",$_REQUEST['cforms_cmsg_html']);

	}

    $cformsSettings['form'.$no]['cforms'.$no.'_confirm'] =		$_REQUEST['cforms_confirm']?'1':'0';

    if( $_REQUEST['cforms_ccsubject']!='' )
		$t[1] = preg_replace("/\\\+/", "\\",$_REQUEST['cforms_ccsubject']);

    $cformsSettings['form'.$no]['cforms'.$no.'_csubject'] =		$t[0].'$#$'.$t[1];

	if (!isset($_REQUEST['cforms_mp_form'])) $_REQUEST['cforms_mp_form'] = "";
	if (!isset($_REQUEST['cforms_mp_next'])) $_REQUEST['cforms_mp_next'] = "";
	$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_form'] = 	 (isset($_REQUEST['cforms_mp_form']) && $_REQUEST['cforms_mp_form'])?true:false;
	if ( $_REQUEST['cforms_mp_form']==true && $_REQUEST['cforms_mp_next']=='' )
		$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_next'] = -1;
    else
		$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_next'] = $_REQUEST['cforms_mp_next'];

	if( !isset($_REQUEST['cforms_mp_first']))        $_REQUEST['cforms_mp_first']="";
	if( !isset($_REQUEST['cforms_mp_email']))        $_REQUEST['cforms_mp_email']="";
	if( !isset($_REQUEST['cforms_mp_reset']))        $_REQUEST['cforms_mp_reset']="";
	if( !isset($_REQUEST['cforms_mp_resettext']))    $_REQUEST['cforms_mp_resettext']="";
	if( !isset($_REQUEST['cforms_mp_back']))         $_REQUEST['cforms_mp_back']="";
	if( !isset($_REQUEST['cforms_mp_backtext']))     $_REQUEST['cforms_mp_backtext']="";
	
	$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_first'] = 		$_REQUEST['cforms_mp_first']?true:false;
	$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_email'] =  		$_REQUEST['cforms_mp_email']?true:false;
	$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_reset'] = 		$_REQUEST['cforms_mp_reset']?true:false;
	$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_resettext']=	$_REQUEST['cforms_mp_resettext'];
	$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_back']     = 	$_REQUEST['cforms_mp_back']?true:false;
	$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_backtext'] =	$_REQUEST['cforms_mp_backtext'];
	if( !isset($_REQUEST['cforms_mp_form'])) $_REQUEST['cforms_mp_form']="";
	if( !isset($_REQUEST['cforms_ajax'])) $_REQUEST['cforms_ajax'] = "";
	if( !isset($_REQUEST['cforms_tafCC'])) $_REQUEST['cforms_tafCC'] = "";
	if( $_REQUEST['cforms_mp_form'] ){
		$cformsSettings['form'.$no]['cforms'.$no.'_ajax']       = '0';
		$cformsSettings['form'.$no]['cforms'.$no.'_dontclear']  = false; // NOTE that it can't be set with MP!
	} else
		$cformsSettings['form'.$no]['cforms'.$no.'_ajax'] = 			$_REQUEST['cforms_ajax']?'1':'0';


	$cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'] = 		'01'; ### default
	$cformsSettings['form'.$no]['cforms'.$no.'_tafCC'] = 	   		$_REQUEST['cforms_tafCC']?'1':'0';

	if ( isset($_REQUEST['cforms_taftrick']) )
		$cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'] = 	'31';

	if ( isset($_REQUEST['cforms_tellafriend']) )
		$cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'] =	'1'.(isset($_REQUEST['cforms_tafdefault']) && $_REQUEST['cforms_tafdefault']?'1':'0') ;


	if ( isset($_REQUEST['cforms_commentrep']) )
		$cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'] =	'2'.(isset($_REQUEST['cforms_commentXnote']) && $_REQUEST['cforms_commentXnote']?'1':'0') ;


	$cformsSettings['form'.$no]['cforms'.$no.'_tracking'] =      preg_replace("/\\\+/", "\\",isset($_REQUEST['cforms_tracking'])&& $_REQUEST['cforms_tracking']);


	### reorder fields
	if(isset($_REQUEST['field_order']) && $_REQUEST['field_order']<>'') {
		$j=0;

		$result = preg_match_all('/allfields\[\]=f([^&]+)&?/',$_REQUEST['field_order'],$order);
		$order  = $order[1];
		$tempcount = isset($_REQUEST['AddField'])?($field_count-$_POST['AddFieldNo']):($field_count);
		while($j < $tempcount)
		{
				$new_f = $order[$j]-1;
				if ( $j <> $new_f )
						$cformsSettings['form'.$no]['cforms'.$no.'_count_field_'.($j+1)] = $all_fields[$new_f];
		$j++;
		}

	} ### if order changed


	### new field added (will actually be added below!)
	if( isset($_REQUEST['AddField']) && isset($_REQUEST['field_count_submit']) ){

	        $field_count = $_POST['field_count_submit'] + $_POST['AddFieldNo'];
	        $cformsSettings['form'.$no]['cforms'.$no.'_count_fields'] = $field_count;

	        $_POST['AddFieldPos'] = ($_POST['AddFieldPos']=="0")?1:$_POST['AddFieldPos']; ###safety

	        ### need to insert empty fields in between?
	        if( $_POST['AddFieldPos']<>'' && $_POST['AddFieldPos']<$_POST['field_count_submit'] ){
	            for($i = $_POST['field_count_submit']; $i >= $_POST['AddFieldPos']; $i--) {
	                $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . (int)($i+$_POST['AddFieldNo'])] = $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . (int)($i)];
                }

	            for($i = $_POST['AddFieldPos']; $i < ($_POST['AddFieldPos']+$_POST['AddFieldNo']); $i++) {
	                $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . (int)($i)] = '';
                }
	        }

	}
    update_option('cforms_settings',$cformsSettings);
	echo '<div id="message" class="updated fade"><p>'.__('Form settings updated.', 'cforms').'</p>'.$usermsg.'</div>';
