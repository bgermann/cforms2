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

	$FORMCOUNT=$FORMCOUNT+1;
	$no = $noDISP = $FORMCOUNT;

	$cformsSettings['global']['cforms_formcount'] = (string)($FORMCOUNT);

	### new settings container
	$cformsSettings['form'.$no]['cforms'.$no.'_rsskey'] = md5(rand());
	$cformsSettings['form'.$no]['cforms'.$no.'_rss'] = false;
	$cformsSettings['form'.$no]['cforms'.$no.'_rss_count'] = 5;

	$cformsSettings['form'.$no]['cforms'.$no.'_dontclear'] = false;

	$cformsSettings['form'.$no]['cforms'.$no.'_count_fields'] = '5';
	$cformsSettings['form'.$no]['cforms'.$no.'_count_field_1'] = __('My Fieldset', 'cforms').'$#$fieldsetstart$#$0$#$0$#$0$#$0$#$0';
	$cformsSettings['form'.$no]['cforms'.$no.'_count_field_2'] = __('Your Name|Your Name', 'cforms').'$#$textfield$#$1$#$0$#$1$#$0$#$0';
	$cformsSettings['form'.$no]['cforms'.$no.'_count_field_3'] = __('Email', 'cforms').'$#$textfield$#$1$#$1$#$0$#$0$#$0';
	$cformsSettings['form'.$no]['cforms'.$no.'_count_field_4'] = __('Website|http://', 'cforms').'$#$textfield$#$0$#$0$#$0$#$0$#$0';
	$cformsSettings['form'.$no]['cforms'.$no.'_count_field_5'] = __('Message', 'cforms').'$#$textarea$#$0$#$0$#$0$#$0$#$0';

	$cformsSettings['form'.$no]['cforms'.$no.'_required'] = __('(required)', 'cforms');
	$cformsSettings['form'.$no]['cforms'.$no.'_emailrequired'] = __('(valid email required)', 'cforms');

	$cformsSettings['form'.$no]['cforms'.$no.'_ajax'] = '1';
	$cformsSettings['form'.$no]['cforms'.$no.'_confirm'] = '0';
	$cformsSettings['form'.$no]['cforms'.$no.'_fname'] = __('A new form', 'cforms');
	$cformsSettings['form'.$no]['cforms'.$no.'_csubject'] = __('Re: Your note', 'cforms').'$#$'.__('Re: Submitted form (copy)', 'cforms');
	$cformsSettings['form'.$no]['cforms'.$no.'_cmsg'] = __('Dear {Your Name},', 'cforms') . "\n". __('Thank you for your note!', 'cforms') . "\n". __('We will get back to you as soon as possible.', 'cforms') . "\n\n";

	$cformsSettings['form'.$no]['cforms'.$no.'_cmsg_html'] = '<div '.$cformsSettings['global']['cforms_style']['autoconf'].'><p '.$cformsSettings['global']['cforms_style']['dear'] .'>'. __('Dear {Your Name},', 'cforms') . "</p>\n<p ". $cformsSettings['global']['cforms_style']['confp'].'>'. __('Thank you for your note!', 'cforms') . "</p>\n<p ".$cformsSettings['global']['cforms_style']['confp'].'>'. __('We will get back to you as soon as possible.', 'cforms') . "\n<div ".$cformsSettings['global']['cforms_style']['confirmationmsg'].'>'.__('This is an automatic confirmation message.', 'cforms')." {Date}.</div></div>\n\n";

	$cformsSettings['form'.$no]['cforms'.$no.'_email'] = get_bloginfo('admin_email') ;
	$cformsSettings['form'.$no]['cforms'.$no.'_fromemail'] = get_bloginfo('admin_email') ;
	$cformsSettings['form'.$no]['cforms'.$no.'_bcc'] = '';
	$cformsSettings['form'.$no]['cforms'.$no.'_header'] = __('A new submission (form: "{Form Name}")', 'cforms') . "\r\n============================================\r\n" . __('Submitted on: {Date}', 'cforms') . "\r\n" . __('Via: {Page}', 'cforms') . "\r\n" . __('By {IP} (visitor IP)', 'cforms') . ".\r\n" . ".\r\n" ;
	$cformsSettings['form'.$no]['cforms'.$no.'_header_html'] = '<p '.$cformsSettings['global']['cforms_style']['meta'].'>' . __('A form has been submitted on {Date}, via: {Page} [IP {IP}]', 'cforms') . '</p>' ;
	$cformsSettings['form'.$no]['cforms'.$no.'_formdata'] = '1111';
	$cformsSettings['form'.$no]['cforms'.$no.'_space'] = '30';
	$cformsSettings['form'.$no]['cforms'.$no.'_noattachments'] = '0';

	$cformsSettings['form'.$no]['cforms'.$no.'_subject'] = __('A comment from {Your Name}', 'cforms');
	$cformsSettings['form'.$no]['cforms'.$no.'_submit_text'] = __('Submit', 'cforms');
	$cformsSettings['form'.$no]['cforms'.$no.'_success'] = __('Thank you for your comment!', 'cforms');
	$cformsSettings['form'.$no]['cforms'.$no.'_failure'] = __('Please fill in all the required fields.', 'cforms');
	$cformsSettings['form'.$no]['cforms'.$no.'_limittxt'] = '<strong>'.__('No more submissions accepted at this time.', 'cforms').'</strong>';
	$cformsSettings['form'.$no]['cforms'.$no.'_working'] = __('One moment please...', 'cforms');
	$cformsSettings['form'.$no]['cforms'.$no.'_popup'] = 'nn';
	$cformsSettings['form'.$no]['cforms'.$no.'_showpos'] = 'ynyyy';

	$cformsSettings['form'.$no]['cforms'.$no.'_hide'] = false;
	$cformsSettings['form'.$no]['cforms'.$no.'_redirect'] = false;
	$cformsSettings['form'.$no]['cforms'.$no.'_redirect_page'] = __('http://redirect.to.this.page', 'cforms');

	$cformsSettings['form'.$no]['cforms'.$no.'_action'] = '0';
	$cformsSettings['form'.$no]['cforms'.$no.'_action_page'] = 'http://';

	/*file upload*/
    $wp_upload_dir = wp_upload_dir();
	$cformsSettings['form'.$no]['cforms'.$no.'_upload_dir'] = $wp_upload_dir['basedir'];
	$cformsSettings['form'.$no]['cforms'.$no.'_upload_ext'] = 'txt,zip,doc,rtf,xls';
	$cformsSettings['form'.$no]['cforms'.$no.'_upload_size'] = '1024';

	$cformsSettings['form'.$no]['cforms'.$no.'_tracking'] = '';
	$cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'] = '01';
	$cformsSettings['form'.$no]['cforms'.$no.'_dashboard'] = '0';
	$cformsSettings['form'.$no]['cforms'.$no.'_maxentries'] = '';

    update_option('cforms_settings',$cformsSettings);
	echo '<div id="message" class="updated fade"><p>'.__('A new form with default fields has been added.', 'cforms').'</p></div>';
