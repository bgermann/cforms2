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

	### set to nothing
	$usermsg='';

	$ccbox=false;
	$emailtobox=false;
	$WPc=false;
	$uploadfield=false;

	for($i = 1; $i <= $field_count; $i++) {

		if ($_REQUEST['field_' . $i . '_name']<>''){ ### safety

			$allgood = true;
			$name = str_replace('$#$', '$', $_REQUEST['field_' . $i . '_name']);
			$type = $_REQUEST['field_' . $i . '_type'];
			$required = 0;
			$emailcheck = 0;
			$clear = 0;
			$disabled = 0;
			$readonly = 0;

        if( !$uploadfield )
          $uploadfield = ($type == 'upload');

				if( $type=='ccbox' ){
					$allgood = $ccbox?false:true;
					$usermsg .= $ccbox?'<span class="exMsg">'.__('Only one <em>CC:</em> field is permitted!', 'cforms2').'</span>':'';
					$ccbox=true;
				}
				if( $type=='emailtobox' ){
					$allgood = $emailtobox?false:true;
					$usermsg .= $emailtobox?'<span class="exMsg">'.__('Only one <em>Multiple Recipients</em> field is permitted!', 'cforms2').'</span>':'';
					$emailtobox=true;
				}

				if(isset($_REQUEST['field_' . $i . '_required']) && ((strpos($type, 'tml5')!==false) || in_array($type,array('pwfield','textfield','datepicker','textarea','checkbox','multiselectbox','selectbox','emailtobox','upload','radiobuttons'))) ) {
					$required = 1;
				}

				if(isset($_REQUEST['field_' . $i . '_emailcheck']) && in_array($type,array('html5email','textfield','datepicker')) ){
					$emailcheck = 1;
				}

				if(isset($_REQUEST['field_' . $i . '_clear']) && ((strpos($type, 'tml5')!==false) || in_array($type,array('pwfield','textfield','datepicker','textarea'))) ) {
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
	$cformsSettings['form'.$no]['cforms'.$no.'_noid'] =           cforms2_get_boolean_from_request('cforms_upload_noid');
	if( $uploadfield && cforms2_get_from_request('cforms_upload_dir')<>'' )
		$cformsSettings['form'.$no]['cforms'.$no.'_upload_dir'] =     cforms2_get_from_request('cforms_upload_dir').'$#$'.cforms2_get_from_request('cforms_upload_dir_url');
	$cformsSettings['form'.$no]['cforms'.$no.'_upload_ext'] =     cforms2_get_from_request('cforms_upload_ext');
	$cformsSettings['form'.$no]['cforms'.$no.'_upload_size'] =    cforms2_get_from_request('cforms_upload_size');
	$cformsSettings['form'.$no]['cforms'.$no.'_noattachments'] =  cforms2_get_boolean_from_request('cforms_noattachments');

	$cformsSettings['form'.$no]['cforms'.$no.'_submit_text'] =   cforms2_get_from_request('cforms_submit_text');
	$cformsSettings['form'.$no]['cforms'.$no.'_working'] =       cforms2_get_from_request('cforms_working');
  	$cformsSettings['form'.$no]['cforms'.$no.'_required'] =      cforms2_get_from_request('cforms_required');
  	$cformsSettings['form'.$no]['cforms'.$no.'_emailrequired'] = cforms2_get_from_request('cforms_emailrequired');
	$cformsSettings['form'.$no]['cforms'.$no.'_success'] =       cforms2_get_from_request('cforms_success');
	$cformsSettings['form'.$no]['cforms'.$no.'_failure'] =       cforms2_get_from_request('cforms_failure');

	$cformsSettings['form'.$no]['cforms'.$no.'_showpos'] = (cforms2_get_from_request('cforms_showposa')?'y':'n').(cforms2_get_from_request('cforms_showposb')?'y':'n').
																	(cforms2_get_from_request('cforms_errorLI')?'y':'n').(cforms2_get_from_request('cforms_errorINS')?'y':'n').
																	(cforms2_get_from_request('cforms_jump')?'y':'n');

	$cformsSettings['form'.$no]['cforms'.$no.'_formaction'] =    cforms2_get_from_request('cforms_formaction')?true:false;
	$cformsSettings['form'.$no]['cforms'.$no.'_dontclear'] =     cforms2_get_from_request('cforms_dontclear')?true:false;
	$cformsSettings['form'.$no]['cforms'.$no.'_dashboard'] =	 cforms2_get_boolean_from_request('cforms_dashboard');
    $cformsSettings['form'.$no]['cforms'.$no.'_notracking'] =    cforms2_get_from_request('cforms_notracking')?true:false;
	$cformsSettings['form'.$no]['cforms'.$no.'_customnames'] =	 cforms2_get_boolean_from_request('cforms_customnames');
	$cformsSettings['form'.$no]['cforms'.$no.'_hide'] =			 cforms2_get_from_request('cforms_hide')?true:false;

	$cformsSettings['form'.$no]['cforms'.$no.'_maxentries'] =	 				cforms2_get_from_request('cforms_maxentries')==''?'':(int)cforms2_get_from_request('cforms_maxentries');

	$startdate = cforms2_get_from_request('cforms_startdate');
	$starttime = cforms2_get_from_request('cforms_starttime');
	$enddate = cforms2_get_from_request('cforms_enddate');
	$endtime = cforms2_get_from_request('cforms_endtime');
	if ($startdate<>'' && $starttime=='')
		$_REQUEST['cforms_starttime'] = '00:00';
	if ($starttime<>'' && $startdate=='')
		$_REQUEST['cforms_startdate'] = current_time('d/m/Y');
	if ($enddate<>'' && $endtime=='')
		$_REQUEST['cforms_endtime'] = '00:00';
	if ($endtime<>'' && $enddate=='')
		$_REQUEST['cforms_enddate'] = current_time('d/m/Y');
	$cformsSettings['form'.$no]['cforms'.$no.'_startdate'] = 	preg_replace("/\\\+/", "\\",$startdate).' '.
    															preg_replace("/\\\+/", "\\",$starttime);
    $cformsSettings['form'.$no]['cforms'.$no.'_enddate'] =  	preg_replace("/\\\+/", "\\",$enddate).' '.
    															preg_replace("/\\\+/", "\\",$endtime);
	$cformsSettings['form'.$no]['cforms'.$no.'_limittxt'] = cforms2_get_from_request('cforms_limittxt');

	$cformsSettings['form'.$no]['cforms'.$no.'_redirect'] =       cforms2_get_from_request('cforms_redirect')?true:false;
	$cformsSettings['form'.$no]['cforms'.$no.'_redirect_page'] =  preg_replace("/\\\+/", "\\",cforms2_get_from_request('cforms_redirect_page'));
	$cformsSettings['form'.$no]['cforms'.$no.'_action'] =         cforms2_get_boolean_from_request('cforms_action');
	$cformsSettings['form'.$no]['cforms'.$no.'_action_page'] =    preg_replace("/\\\+/", "\\",cforms2_get_from_request('cforms_action_page'));

	$cformsSettings['form'.$no]['cforms'.$no.'_emailoff'] =		 cforms2_get_boolean_from_request('cforms_emailoff');
	$cformsSettings['form'.$no]['cforms'.$no.'_emptyoff'] =		 cforms2_get_boolean_from_request('cforms_emptyoff');
	$cformsSettings['form'.$no]['cforms'.$no.'_fromemail'] =     cforms2_get_from_request('cforms_fromemail');
	$cformsSettings['form'.$no]['cforms'.$no.'_email'] =         cforms2_get_from_request('cforms_email');
	$cformsSettings['form'.$no]['cforms'.$no.'_bcc'] =           cforms2_get_from_request('cforms_bcc');
	$cformsSettings['form'.$no]['cforms'.$no.'_subject'] =       cforms2_get_from_request('cforms_subject');
	$cformsSettings['form'.$no]['cforms'.$no.'_header'] =        preg_replace("/\\\+/", "\\",cforms2_get_from_request('cforms_header'));
	$cformsSettings['form'.$no]['cforms'.$no.'_header_html'] =   preg_replace("/\\\+/", "\\",cforms2_get_from_request('cforms_header_html'));
	$cformsSettings['form'.$no]['cforms'.$no.'_formdata'] =      cforms2_get_boolean_from_request('cforms_formdata_txt').cforms2_get_boolean_from_request('cforms_formdata_html').
    															 cforms2_get_boolean_from_request('cforms_admin_html').cforms2_get_boolean_from_request('cforms_user_html');
	$cformsSettings['form'.$no]['cforms'.$no.'_space'] =         cforms2_get_from_request('cforms_space');

    ## quickly get old vals
    $t=explode('$#$',$cformsSettings['form'.$no]['cforms'.$no.'_csubject']);
	
    if( isset($_REQUEST['cforms_confirm']) && $_REQUEST['cforms_confirm'] && $cformsSettings['form'.$no]['cforms'.$no.'_confirm']==1 ){
        $t[0] = 													  preg_replace("/\\\+/", "\\",cforms2_get_from_request('cforms_csubject'));
	    $cformsSettings['form'.$no]['cforms'.$no.'_cattachment'][0] = cforms2_get_from_request('cforms_cattachment');
	    $cformsSettings['form'.$no]['cforms'.$no.'_cmsg'] =     	  preg_replace("/\\\+/", "\\",cforms2_get_from_request('cforms_cmsg'));
	    $cformsSettings['form'.$no]['cforms'.$no.'_cmsg_html'] =	  preg_replace("/\\\+/", "\\",cforms2_get_from_request('cforms_cmsg_html'));

	}

    $cformsSettings['form'.$no]['cforms'.$no.'_confirm'] = cforms2_get_boolean_from_request('cforms_confirm');

    if( cforms2_get_from_request('cforms_ccsubject')!='' )
		$t[1] = preg_replace("/\\\+/", "\\",cforms2_get_from_request('cforms_ccsubject'));

    $cformsSettings['form'.$no]['cforms'.$no.'_csubject'] =	$t[0].'$#$'.$t[1];

	$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_form'] = cforms2_get_from_request('cforms_mp_form')?true:false;
	if ( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_form'] && cforms2_get_from_request('cforms_mp_next')=='' )
		$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_next'] = -1;
    else
		$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_next'] = cforms2_get_from_request('cforms_mp_next');
	
	$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_first'] = 		cforms2_get_from_request('cforms_mp_first')?true:false;
	$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_email'] =  		cforms2_get_from_request('cforms_mp_email')?true:false;
	$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_reset'] = 		cforms2_get_from_request('cforms_mp_reset')?true:false;
	$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_resettext']=	cforms2_get_from_request('cforms_mp_resettext');
	$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_back']     = 	cforms2_get_from_request('cforms_mp_back')?true:false;
	$cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_backtext'] =	cforms2_get_from_request('cforms_mp_backtext');
	if( cforms2_get_from_request('cforms_mp_form') ){
		$cformsSettings['form'.$no]['cforms'.$no.'_ajax']       = '0';
		$cformsSettings['form'.$no]['cforms'.$no.'_dontclear']  = false; // NOTE that it can't be set with MP!
	} else
		$cformsSettings['form'.$no]['cforms'.$no.'_ajax'] = cforms2_get_boolean_from_request('cforms_ajax');


	// up to version 14.12 this option string had two characters, which is reflected in its usage
	$cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'] = '0';

	if ( isset($_REQUEST['cforms_taftrick']) )
		$cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'] = '3';


	$cformsSettings['form'.$no]['cforms'.$no.'_tracking'] = preg_replace("/\\\+/", "\\",cforms2_get_from_request('cforms_tracking'));


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
	echo '<div id="message" class="updated fade"><p>'.__('Form settings updated.', 'cforms2').'</p>'.$usermsg.'</div>';
