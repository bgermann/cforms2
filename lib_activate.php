<?php
global $wpdb, $cformsSettings;
$cformsSettings = (array) $cformsSettings;

### new global settings container

### a few basic things, always reset during (re)activation
$cformsSettings['global']['plugindir'] = basename(dirname(__FILE__));
$cformsSettings['global']['cforms_root'] = get_cf_plugindir(); // . $cformsSettings['global']['plugindir'];
$cformsSettings['global']['tinyURI'] = get_cf_siteurl() . '/wp-includes/js/tinymce';
$cformsSettings['global']['cforms_root_dir'] = dirname(__FILE__);
$cformsSettings['global']['cforms_IIS'] = strpos(dirname(__FILE__), '\\') !==false ? '\\' : '/';
$cformsSettings['global']['v'] = $localversion;

### Common HTML message information

$cformsSettings['global']['cforms_style_doctype'] 	= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';

unset ( $cformsSettings['global']['cforms_style'] );
$cformsSettings['global']['cforms_style']['body'] 	= 'style="margin:0; padding:0; font-family: Verdana, Arial; font-size: 13px; color:#555;"';
$cformsSettings['global']['cforms_style']['meta'] 	= 'style="font-size: 90%; margin:0; background:#aaaaaa; padding:1em 2em 1em 0.6em; color:#555555; text-shadow:0 1px 0 #c5c5c5; border-bottom:1px solid #9d9d9d;"';
$cformsSettings['global']['cforms_style']['admin'] 	= 'style="background:#f0f0f0; border-top:1px solid #777; box-shadow:0 -2px 2px #999; -webkit-box-shadow:0 -2px 2px #999;"';
$cformsSettings['global']['cforms_style']['title'] 	= 'style="font-size: 90%; margin:0; background:#fcfcfc; padding:1em 2em 1em 0.6em; color:#888888; display:inline-block;"';
$cformsSettings['global']['cforms_style']['table'] 	= 'style="width:auto; margin: 0.2em 2em 2em; font-size: 100%;"';
$cformsSettings['global']['cforms_style']['fs'] 	= 'style="color:#555; padding:1em 0 0.4em; font-size: 110%; font-weight:bold; text-shadow:0 1px 0 #fff;"';
$cformsSettings['global']['cforms_style']['key_td']	= 'style="padding: 0.3em 1em; border-bottom:1px dotted #ddd; padding-right:2em; color:#888; width:1%;"';
$cformsSettings['global']['cforms_style']['val_td'] = 'style="padding: 0.3em 1em; border-bottom:1px dotted #ddd; padding-left:0; color:#333;"';
$cformsSettings['global']['cforms_style']['cforms'] = 'style="display:block; padding:1em 0.6em; margin-top:1em; background:#f7f7f7; color:#777; font-size:90%; text-align:right; font-family:Tahoma,Arial;"';

$cformsSettings['global']['cforms_style']['autoconf'] 	= 'style="padding:1em 1em 0; background:#f0f0f0; color:#333;"';
$cformsSettings['global']['cforms_style']['dear'] 		= 'style="margin:0.5em 30px; font-weight:bold; margin-bottom:1.2em;"';
$cformsSettings['global']['cforms_style']['confp'] 		= 'style="margin:0.5em 30px;"';
$cformsSettings['global']['cforms_style']['confirmationmsg'] = 'style="margin:4em 30px 0; padding-bottom:1em; font-size:80%; color:#aaa; font-family:Tahoma,Arial;"';


### check for upgrade pre-9.0 to 9.0
if ( check_update() )
    return;

### file upload
setINI('form','cforms_upload_dir', (defined('WP_CONTENT_DIR')? WP_CONTENT_DIR.'/plugins/'.basename(dirname(__FILE__)) : ABSPATH . 'wp-content/plugins/' . basename(dirname(__FILE__)) . '/attachments') );
setINI('form','cforms_upload_ext', 'txt,zip,doc,rtf,xls');
setINI('form','cforms_upload_size', '1024');
setINI('form','cforms_dontclear', false);

### tracking settings
setINI('form','cforms_rsskey', md5(rand()) );
setINI('form','cforms_rss', false );
setINI('form','cforms_rss_count', 5 );

### fields for default form
setINI('form','cforms_count_fields', '5');
setINI('form','cforms_count_field_1', __('My Fieldset', 'cforms').'$#$fieldsetstart$#$0$#$0$#$0$#$0$#$0');
setINI('form','cforms_count_field_2', __('Your Name|Your Name', 'cforms').'$#$textfield$#$1$#$0$#$1$#$0$#$0');
setINI('form','cforms_count_field_3', __('Email', 'cforms').'$#$textfield$#$1$#$1$#$0$#$0$#$0');
setINI('form','cforms_count_field_4', __('Website', 'cforms').'|http://$#$textfield$#$0$#$0$#$0$#$0$#$0');
setINI('form','cforms_count_field_5', __('Message', 'cforms').'$#$textarea$#$0$#$0$#$0$#$0$#$0');

setINI('form','cforms_required', __('(required)', 'cforms'));
setINI('form','cforms_emailrequired', __('(valid email required)', 'cforms'));

setINI('form','cforms_confirm', '0');
setINI('form','cforms_ajax', '1');
setINI('form','cforms_emailpriority', '3');
setINI('form','cforms_fname', __('Your default form', 'cforms'));
setINI('form','cforms_csubject', __('Re: Your note', 'cforms').'$#$'.__('Re: Submitted form (copy)', 'cforms'));
### below is also in cforms-options.php!
setINI('form','cforms_cmsg', __('Dear {Your Name},', 'cforms') . "\n" . __('Thank you for your note!', 'cforms') . "\n". __('We will get back to you as soon as possible.', 'cforms') . "\n\n");
setINI('form','cforms_cmsg_html', '<div '.$cformsSettings['global']['cforms_style']['autoconf'].'><p '.$cformsSettings['global']['cforms_style']['dear'] .'>'. __('Dear {Your Name},', 'cforms') . "</p>\n<p ". $cformsSettings['global']['cforms_style']['confp'].'>'. __('Thank you for your note!', 'cforms') . "</p>\n<p ".$cformsSettings['global']['cforms_style']['confp'].'>'. __('We will get back to you as soon as possible.', 'cforms') . "\n<div ".$cformsSettings['global']['cforms_style']['confirmationmsg'].'>'.__('This is an automatic confirmation message.', 'cforms')." {Date}.</div></div>\n\n" );
###
setINI('form','cforms_email', get_bloginfo('admin_email') );
setINI('form','cforms_fromemail', get_bloginfo('admin_email') );
setINI('form','cforms_bcc', '');

### below is also in cforms-options.php!
setINI('form','cforms_header', __('A new submission (form: "{Form Name}")', 'cforms') . "\r\n============================================\r\n" . __('Submitted on: {Date}', 'cforms') . "\r\n" . __('Via: {Page}', 'cforms') . "\r\n" . __('By {IP} (visitor IP)', 'cforms') . ".\r\n" . ".\r\n" );
setINI('form','cforms_header_html', '<p '.$cformsSettings['global']['cforms_style']['meta'].'>' . __('A form has been submitted on {Date}, via: {Page} [IP {IP}]', 'cforms') . '</p>' );
###
setINI('form','cforms_formdata', '1111');
setINI('form','cforms_space', '30');
setINI('form','cforms_noattachments', '0');

setINI('form','cforms_subject', __('A comment from {Your Name}', 'cforms'));
setINI('form','cforms_submit_text', __('Submit', 'cforms'));
setINI('form','cforms_success', __('Thank you for your comment!', 'cforms'));
setINI('form','cforms_failure', __('Please fill in all the required fields.', 'cforms'));
setINI('form','cforms_limittxt', '<strong>'.__('No more submissions accepted at this time.', 'cforms').'</strong>');

setINI('form','cforms_working', __('One moment please...', 'cforms'));
setINI('form','cforms_popup', 'nn');
setINI('form','cforms_showpos', 'ynyyy');

setINI('form','cforms_hide', false);
setINI('form','cforms_redirect', false);
setINI('form','cforms_redirect_page', __('http://redirect.to.this.page', 'cforms'));

setINI('form','cforms_action', '0');
setINI('form','cforms_action_page', 'http://');

setINI('form','cforms_tracking', '');
setINI('form','cforms_showdashboard', '1');
setINI('form','cforms_maxentries', '');
setINI('form','cforms_tellafriend', '01');
setINI('form','cforms_dashboard', '0');

### $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_resettext'];

### global file settings

setINI('global','cforms_formcount', '1');
setINI('global','cforms_upload_err1', __('Generic file upload error. Please try again', 'cforms'));
setINI('global','cforms_upload_err2', __('File is empty. Please upload something more substantial.', 'cforms'));
setINI('global','cforms_upload_err3', __('Sorry, file is too large. You may try to zip your file.', 'cforms'));
setINI('global','cforms_upload_err4', __('File upload failed. Please try again or contact the blog admin.', 'cforms'));
setINI('global','cforms_upload_err5', __('File not accepted, file type not allowed.', 'cforms'));

setINI('global','cforms_rsskeyall', md5(rand()) );
setINI('global','cforms_rssall', false );
setINI('global','cforms_rssall_count', 5 );

### form verification
$cap['h'] = 25;
$cap['w'] = 115;
$cap['c'] = '000066';
$cap['l'] = '000066';
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
setINI('global','cforms_captcha_def', $cap );
setINI('global','cforms_sec_qa', __('What color is snow?=white', 'cforms'). "\r\n" . __('The color of grass is=green', 'cforms'). "\r\n" . __('Ten minus five equals=five', 'cforms'));
setINI('global','cforms_codeerr', __('Please double-check your verification code.', 'cforms'));

setINI('global','cforms_show_quicktag', '1');
setINI('global','cforms_show_quicktag_js', false );

### comment feature
setINI('global','cforms_commentsuccess', __('Thank you for leaving a comment.', 'cforms'));
setINI('global','cforms_commentWait', '15');
setINI('global','cforms_commentParent', 'mycommentlist');
setINI('global','cforms_commentHTML', "<li id=\"comment-{id}\">{moderation}\n<p>{usercomment}</p>\n<p>\n<cite>Comment by <a href=\"{url}\" rel=\"external nofollow\">{author}</a> &mdash; {date} @ <a href=\"#comment-{id}\">{time}</a></cite>\n</p>\n</li>");
setINI('global','cforms_commentInMod', '<em>'.__('Your comment is awaiting moderation.', 'cforms').'</em>');
setINI('global','cforms_avatar', '32');

setINI('global','cforms_css', 'cforms2012.css');
setINI('global','cforms_labelID', '0');
setINI('global','cforms_liID', '0');

setINI('global','cforms_database', '0');

setINI('global','cforms_datepicker', '0');
setINI('global','cforms_dp_start', '0');
setINI('global','cforms_dp_date', 'mm/dd/yy');
setINI('global','cforms_dp_days', __('"S","M","T","W","T","F","S"', 'cforms'));
setINI('global','cforms_dp_months', __('"January","February","March","April","May","June","July","August","September","October","November","December"', 'cforms'));

$nav[0]=__('Previous Year', 'cforms');
$nav[1]=__('Previous Month', 'cforms');
$nav[2]=__('Next Year', 'cforms');
$nav[3]=__('Next Month', 'cforms');
$nav[4]=__('Close', 'cforms');
$nav[5]=__('Choose Date', 'cforms');
setINI('global','cforms_dp_nav', $nav);


### migrate previous MP settings
for( $i=1; $i<=$cformsSettings['global']['cforms_formcount']; $i++ ){

	$no = ($i=='1')?'':$i;
    if( is_array($cformsSettings['form'.$no]['mp']) && !is_array($cformsSettings['form'.$no]['cforms'.$no.'_mp']) ){

	    foreach( array_keys($cformsSettings['form'.$no]['mp']) as $k ){
	        $tmp = preg_match('/cforms\d*_(.*)/',$k, $kk);
            $cformsSettings['form'.$no]['cforms'.$no.'_mp'][$kk[1]] = $cformsSettings['form'.$no]['mp'][$k];
	    }

	}
}



### migrate include/exclude pre v11.2 !

if( $cformsSettings['global']['cforms_include'] <> '' ){
  $cformsSettings['global']['cforms_inexclude']['ids'] = $cformsSettings['global']['cforms_include'];
  unset($cformsSettings['global']['cforms_include']);
}


### UPDATE 'the one'
if ( get_option('cforms_settings') )
    update_option('cforms_settings',$cformsSettings);
else
    add_option('cforms_settings',$cformsSettings);



### updates existing tracking db
if ( $wpdb->get_var("show tables like '$wpdb->cformsdata'") == $wpdb->cformsdata ) {
    ### fetch table column structure from the database
    $tablefields = $wpdb->get_results("DESCRIBE {$wpdb->cformsdata};");

    $afield = array();
    foreach($tablefields as $field)
        array_push ($afield,$field->Field);

    if ( !in_array('f_id', $afield) ) {
        $sql = "ALTER TABLE " . $wpdb->cformsdata . "
                  ADD f_id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  CHANGE field_name field_name varchar(100) NOT NULL default '';";
        $wpdb->query($sql);
    }
}

### check if option is set
function setINI($s,$v,$d) {
	global $cformsSettings;
	if( !is_array($cformsSettings[$s]) || !in_array($v, array_keys($cformsSettings[$s]) ) )
    	$cformsSettings[$s][$v]=$d;
}


### check if update screen will be shown
function check_update() {
    global $cformsSettings;
	if( is_array($cformsSettings) && get_option('cforms_formcount') ){
		$cformsSettings['global']['update'] = true;
         update_option('cforms_settings',$cformsSettings);
    }
	return get_option('cforms_formcount');
}
?>