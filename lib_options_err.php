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

if (!isset($cformsSettings['form'.$no]['cforms'.$no.'_confirmerr']))
	$cformsSettings['form'.$no]['cforms'.$no.'_confirmerr'] = 0;
$userconfirm = $cformsSettings['form'.$no]['cforms'.$no.'_confirmerr'];

echo '<form name="errmessages" action="#" method="post"><input type="hidden" name="switchform" value="'.$noDISP.'"/>';

if ( $cformsSettings['form'.$no]['cforms'.$no.'_showpos']=='' && (($userconfirm&1)==0) ) {
	$text = sprintf(__('please check the <a href="%s" %s>success/failure message settings</a> and >>Show messages<< options below!', 'cforms2'),'#anchormessage','onclick="setshow(1)"');
	cforms2_showmessage(1, $text, $no, $userconfirm);
}


if ( $cformsSettings['form'.$no]['cforms'.$no.'_upload_dir']=='' && (($userconfirm&2)==0) ) {
	$text = sprintf(__('please check the new <a href="%s" %s>file upload/attachment</a> relevant settings below! You can ignore the message if you\'re not using any file upload field(s).', 'cforms2'),'#fileupload','onclick="setshow(0)"');
	cforms2_showmessage(2, $text, $no, $userconfirm);
}


### check for set email header
if ( $cformsSettings['form'.$no]['cforms'.$no.'_header']=='' && (($userconfirm&4)==0) ) {
	$text = sprintf(__('please check the <a href="%s" %s>email header settings</a> below!', 'cforms2'),'#anchoremail','onclick="setshow(2)"');
	cforms2_showmessage(4, $text, $no, $userconfirm);
}

echo '</form>';

function cforms2_showmessage($confirm, $text, $no, $userconfirm){
	global $cformsSettings;

	if ( $confirm<8 )
		$text = __('It seems that you have recently upgraded cforms','cforms2').' '.$text;

	if ( isset($_GET['cf_confirm']) && $_GET['cf_confirm']=='confirm'.$confirm ){
		$cformsSettings['form'.$no]['cforms'.$no.'_confirmerr'] = ($userconfirm|$confirm);
		update_option('cforms_settings',$cformsSettings);
    }
	else
		echo '<div id="message'.$confirm.'" class="updated fade"><p>'.$text.'</p><p><a href="?page='.plugin_dir_path(plugin_basename(__FILE__)).'cforms-options.php&cf_confirm=confirm'.$confirm.'" class="rm_button allbuttons">'.__('Remove Message','cforms2').'</a></p></div>';
}
