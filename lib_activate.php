<?php
/*
 * Copyright (c) 2006-2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
 * Copyright (c) 2014-2015 Bastian Germann
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

function cforms2_setup_db () {
global $wpdb, $cformsSettings, $localversion;
$cformsSettings = get_option('cforms_settings');

### new global settings container

### Common HTML message information

$cformsSettings['global']['cforms_style_doctype'] 	= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
$cformsSettings['global']['v'] = $localversion;

unset ( $cformsSettings['global']['cforms_style'] );
$cformsSettings['global']['cforms_style']['body'] 	= 'style="margin:0; padding:0; font-family: Verdana, Arial; font-size: 13px; color:#555;"';
$cformsSettings['global']['cforms_style']['meta'] 	= 'style="font-size: 90%; margin:0; background:#aaaaaa; padding:1em 2em 1em 0.6em; color:#555555; text-shadow:0 1px 0 #c5c5c5; border-bottom:1px solid #9d9d9d;"';
$cformsSettings['global']['cforms_style']['admin'] 	= 'style="background:#f0f0f0; border-top:1px solid #777; box-shadow:0 -2px 2px #999; -webkit-box-shadow:0 -2px 2px #999;"';
$cformsSettings['global']['cforms_style']['title'] 	= 'style="font-size: 90%; margin:0; background:#fcfcfc; padding:1em 2em 1em 0.6em; color:#888888; display:inline-block;"';
$cformsSettings['global']['cforms_style']['table'] 	= 'style="width:auto; margin: 0.2em 2em 2em; font-size: 100%;"';
$cformsSettings['global']['cforms_style']['fs'] 	= 'style="color:#555; padding:1em 0 0.4em; font-size: 110%; font-weight:bold; text-shadow:0 1px 0 #fff;"';
$cformsSettings['global']['cforms_style']['key_td']	= 'style="padding: 0.3em 1em; border-bottom:1px dotted #ddd; padding-right:2em; color:#888; width:1%;"';
$cformsSettings['global']['cforms_style']['val_td'] = 'style="padding: 0.3em 1em; border-bottom:1px dotted #ddd; padding-left:0; color:#333;"';

$cformsSettings['global']['cforms_style']['autoconf'] 	= 'style="padding:1em 1em 0; background:#f0f0f0; color:#333;"';
$cformsSettings['global']['cforms_style']['dear'] 		= 'style="margin:0.5em 30px; font-weight:bold; margin-bottom:1.2em;"';
$cformsSettings['global']['cforms_style']['confp'] 		= 'style="margin:0.5em 30px;"';
$cformsSettings['global']['cforms_style']['confirmationmsg'] = 'style="margin:4em 30px 0; padding-bottom:1em; font-size:80%; color:#aaa; font-family:Tahoma,Arial;"';

cforms2_setINI('global','cforms_showdashboard', '');
if (!isset($cformsSettings['global']['cforms_inexclude']['ex']))
	$cformsSettings['global']['cforms_inexclude']['ex'] = '';
if (!isset($cformsSettings['global']['cforms_inexclude']['ids']))
	$cformsSettings['global']['cforms_inexclude']['ids'] = '';
cforms2_setINI('global','cforms_no_css', '');

### file upload
$wp_upload_dir = wp_upload_dir();
cforms2_setINI('form','cforms_upload_dir', $wp_upload_dir['basedir'] );
cforms2_setINI('form','cforms_upload_ext', 'txt,zip,doc,rtf,xls');
cforms2_setINI('form','cforms_upload_size', '1024');
cforms2_setINI('form','cforms_dontclear', false);

### tracking settings
cforms2_setINI('form','cforms_rsskey', md5(mt_rand()) );
cforms2_setINI('form','cforms_rss', false );
cforms2_setINI('form','cforms_rss_count', 5 );

### fields for default form
cforms2_setINI('form','cforms_count_fields', '5');
cforms2_setINI('form','cforms_count_field_1', __('My Fieldset', 'cforms2').'$#$fieldsetstart$#$0$#$0$#$0$#$0$#$0');
cforms2_setINI('form','cforms_count_field_2', __('Your Name|Your Name', 'cforms2').'$#$textfield$#$1$#$0$#$1$#$0$#$0');
cforms2_setINI('form','cforms_count_field_3', __('Email', 'cforms2').'$#$textfield$#$1$#$1$#$0$#$0$#$0');
cforms2_setINI('form','cforms_count_field_4', __('Website', 'cforms2').'|http://$#$textfield$#$0$#$0$#$0$#$0$#$0');
cforms2_setINI('form','cforms_count_field_5', __('Message', 'cforms2').'$#$textarea$#$0$#$0$#$0$#$0$#$0');

cforms2_setINI('form','cforms_required', __('(required)', 'cforms2'));
cforms2_setINI('form','cforms_emailrequired', __('(valid email required)', 'cforms2'));

cforms2_setINI('form','cforms_confirm', '0');
cforms2_setINI('form','cforms_ajax', '1');
cforms2_setINI('form','cforms_fname', __('Your default form', 'cforms2'));
cforms2_setINI('form','cforms_csubject', __('Re: Your note', 'cforms2').'$#$'.__('Re: Submitted form (copy)', 'cforms2'));
### below is also in cforms-options.php!
cforms2_setINI('form','cforms_cmsg', __('Dear {Your Name},', 'cforms2') . "\n" . __('Thank you for your note!', 'cforms2') . "\n". __('We will get back to you as soon as possible.', 'cforms2') . "\n\n");
cforms2_setINI('form','cforms_cmsg_html', '<div '.$cformsSettings['global']['cforms_style']['autoconf'].'><p '.$cformsSettings['global']['cforms_style']['dear'] .'>'. __('Dear {Your Name},', 'cforms2') . "</p>\n<p ". $cformsSettings['global']['cforms_style']['confp'].'>'. __('Thank you for your note!', 'cforms2') . "</p>\n<p ".$cformsSettings['global']['cforms_style']['confp'].'>'. __('We will get back to you as soon as possible.', 'cforms2') . "\n<div ".$cformsSettings['global']['cforms_style']['confirmationmsg'].'>'.__('This is an automatic confirmation message.', 'cforms2')." {Date}.</div></div>\n\n" );
###
cforms2_setINI('form','cforms_email', get_bloginfo('admin_email') );
cforms2_setINI('form','cforms_fromemail', get_bloginfo('admin_email') );
cforms2_setINI('form','cforms_bcc', '');

### below is also in cforms-options.php!
cforms2_setINI('form','cforms_header', __('A new submission (form: "{Form Name}")', 'cforms2') . "\r\n============================================\r\n" . __('Submitted on: {Date}', 'cforms2') . "\r\n" . __('Via: {Page}', 'cforms2') . "\r\n" . __('By {IP} (visitor IP)', 'cforms2') . ".\r\n" . ".\r\n" );
cforms2_setINI('form','cforms_header_html', '<p '.$cformsSettings['global']['cforms_style']['meta'].'>' . __('A form has been submitted on {Date}, via: {Page} [IP {IP}]', 'cforms2') . '</p>' );
###
cforms2_setINI('form','cforms_formdata', '1111');
cforms2_setINI('form','cforms_space', '30');
cforms2_setINI('form','cforms_noattachments', '0');

cforms2_setINI('form','cforms_subject', __('A comment from {Your Name}', 'cforms2'));
cforms2_setINI('form','cforms_submit_text', __('Submit', 'cforms2'));
cforms2_setINI('form','cforms_success', __('Thank you for your comment!', 'cforms2'));
cforms2_setINI('form','cforms_failure', __('Please fill in all the required fields.', 'cforms2'));
cforms2_setINI('form','cforms_limittxt', '<strong>'.__('No more submissions accepted at this time.', 'cforms2').'</strong>');

cforms2_setINI('form','cforms_working', __('One moment please...', 'cforms2'));
cforms2_setINI('form','cforms_showpos', 'ynyyy');

cforms2_setINI('form','cforms_hide', false);
cforms2_setINI('form','cforms_redirect', false);
cforms2_setINI('form','cforms_redirect_page', __('http://redirect.to.this.page', 'cforms2'));

cforms2_setINI('form','cforms_action', '0');
cforms2_setINI('form','cforms_action_page', 'http://');

cforms2_setINI('form','cforms_tracking', '');
cforms2_setINI('form','cforms_showdashboard', '1');
cforms2_setINI('form','cforms_maxentries', '');
cforms2_setINI('form','cforms_tellafriend', '01');
cforms2_setINI('form','cforms_dashboard', '0');

### global file settings

cforms2_setINI('global','cforms_formcount', '1');
cforms2_setINI('global','cforms_upload_err1', __('Generic file upload error. Please try again', 'cforms2'));
cforms2_setINI('global','cforms_upload_err2', __('File is empty. Please upload something more substantial.', 'cforms2'));
cforms2_setINI('global','cforms_upload_err3', __('Sorry, file is too large. You may try to zip your file.', 'cforms2'));
cforms2_setINI('global','cforms_upload_err4', __('File upload failed. Please try again or contact the blog admin.', 'cforms2'));
cforms2_setINI('global','cforms_upload_err5', __('File not accepted, file type not allowed.', 'cforms2'));

cforms2_setINI('global','cforms_rsskeyall', md5(mt_rand()) );
cforms2_setINI('global','cforms_rssall', false );
cforms2_setINI('global','cforms_rssall_count', 5 );

### form verification
$cap['h'] = 25;
$cap['w'] = 115;
$cap['c'] = '#000066';
$cap['l'] = '#000066';
$cap['f'] = 'font4.ttf';
$cap['a1'] = -12;
$cap['a2'] = 12;
$cap['f1'] = 17;
$cap['f2'] = 19;
$cap['bg'] = '1.gif';
$cap['c1'] = 4;
$cap['c2'] = 5;
$cap['ac'] = 'abcdefghijkmnpqrstuvwxyz23456789';
$cap['i'] = 'i';
$cap['fo'] = '0';
$cap['foqa'] = '0';
cforms2_setINI('global','cforms_captcha_def', $cap );
cforms2_setINI('global','cforms_sec_qa', __('What color is snow?=white', 'cforms2'). "\r\n" . __('The color of grass is=green', 'cforms2'). "\r\n" . __('Ten minus five equals=five', 'cforms2'));
cforms2_setINI('global','cforms_codeerr', __('Please double-check your verification code.', 'cforms2'));

cforms2_setINI('global','cforms_show_quicktag', '1');

### comment feature
cforms2_setINI('global','cforms_commentsuccess', __('Thank you for leaving a comment.', 'cforms2'));
cforms2_setINI('global','cforms_commentWait', '15');
cforms2_setINI('global','cforms_commentParent', 'mycommentlist');
cforms2_setINI('global','cforms_commentHTML', "<li id=\"comment-{id}\">{moderation}\n<p>{usercomment}</p>\n<p>\n<cite>Comment by <a href=\"{url}\" rel=\"external nofollow\">{author}</a> &mdash; {date} @ <a href=\"#comment-{id}\">{time}</a></cite>\n</p>\n</li>");
cforms2_setINI('global','cforms_commentInMod', '<em>'.__('Your comment is awaiting moderation.', 'cforms2').'</em>');
cforms2_setINI('global','cforms_avatar', '32');
cforms2_setINI('global','cforms_crlf', array('b' => '0'));

cforms2_setINI('global','cforms_css', 'cforms2012.css');
cforms2_setINI('global','cforms_jqueryuitheme', 'smoothness');
cforms2_setINI('global','cforms_labelID', '0');
cforms2_setINI('global','cforms_liID', '0');

cforms2_setINI('global','cforms_database', '0');

cforms2_setINI('global','cforms_datepicker', '0');
cforms2_setINI('global','cforms_dp_start', '0');
cforms2_setINI('global','cforms_dp_date', 'mm/dd/yy');
cforms2_setINI('global','cforms_dp_days', __('S,M,T,W,T,F,S', 'cforms2'));
cforms2_setINI('global','cforms_dp_months', implode(',', array(
    __("January", 'cforms2'),
    __("February", 'cforms2'),
    __("March", 'cforms2'),
    __("April", 'cforms2'),
    __("May", 'cforms2'),
    __("June", 'cforms2'),
    __("July", 'cforms2'),
    __("August", 'cforms2'),
    __("September", 'cforms2'),
    __("October", 'cforms2'),
    __("November", 'cforms2'),
    __("December", 'cforms2')
)));

$nav[0]=__('Previous Year', 'cforms2');
$nav[1]=__('Previous Month', 'cforms2');
$nav[2]=__('Next Year', 'cforms2');
$nav[3]=__('Next Month', 'cforms2');
$nav[4]=__('Close', 'cforms2');
$nav[5]=__('Choose Date', 'cforms2');
$nav[6]='0';
cforms2_setINI('global','cforms_dp_nav', $nav);


### migrate previous MP settings
for( $i=1; $i<=$cformsSettings['global']['cforms_formcount']; $i++ ){

	$no = ($i=='1')?'':$i;
    if( isset($cformsSettings['form'.$no]['mp']) && is_array($cformsSettings['form'.$no]['mp']) && !is_array($cformsSettings['form'.$no]['cforms'.$no.'_mp']) ){

	    foreach( array_keys($cformsSettings['form'.$no]['mp']) as $k ){
	        preg_match('/cforms\d*_(.*)/',$k, $kk);
            $cformsSettings['form'.$no]['cforms'.$no.'_mp'][$kk[1]] = $cformsSettings['form'.$no]['mp'][$k];
	    }

	}
}



### migrate include/exclude pre v11.2 !
if( isset($cformsSettings['global']['cforms_include']) && $cformsSettings['global']['cforms_include'] <> '' ){
    $cformsSettings['global']['cforms_inexclude']['ids'] = $cformsSettings['global']['cforms_include'];
    unset($cformsSettings['global']['cforms_include']);
}

### migrate quoted values in months/days fields
$cformsSettings['global']['cforms_dp_days'] = str_replace('"', '', $cformsSettings['global']['cforms_dp_days']);
$cformsSettings['global']['cforms_dp_months'] = str_replace('"', '', $cformsSettings['global']['cforms_dp_months']);

### UPDATE 'the one'
update_option('cforms_settings',$cformsSettings);


### updates existing tracking db
if ( $wpdb->get_var("show tables like '$wpdb->cformsdata'") == $wpdb->cformsdata ) {
    ### fetch table column structure from the database
    $tablefields = $wpdb->get_results("DESCRIBE {$wpdb->cformsdata};");

    $afield = array();
    foreach($tablefields as $field) {
        array_push ($afield,$field->Field);
    }

    if ( !in_array('f_id', $afield) ) {
        $sql = "ALTER TABLE " . $wpdb->cformsdata . "
                  ADD f_id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  CHANGE field_name field_name varchar(100) NOT NULL default '';";
        $wpdb->query($sql);
    }
}
}

### check if option is set
function cforms2_setINI($s,$v,$d) {
	global $cformsSettings;
	if( !is_array($cformsSettings) )
		$cformsSettings = array();
	if( !array_key_exists($s, $cformsSettings) || !is_array($cformsSettings[$s]) )
		$cformsSettings[$s] = array();
	if( !in_array($v, array_keys($cformsSettings[$s]) ) )
    	$cformsSettings[$s][$v]=$d;
}
