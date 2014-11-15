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
### WP comment feature
###

global $WPsuccess, $subID, $wpdb, $track, $comment_author_IP;

### new global settings container, will eventually be the only one!
$cformsSettings = get_option('cforms_settings');

$custom		 = false;
$field_count = $cformsSettings['form'.$no]['cforms'.$no.'_count_fields'];
$content 	 = '';

$err		 = 0;

$validations = array();
$all_valid 	 = 1;
$off		 = 0;
$fieldsetnr	 = 1;

$c_errflag	 = false;
$custom_error= '';
$usermessage_class='';

global $comment;

###
### VALIDATE all fields
###

if ( $isAjaxWPcomment ){
	###
	### comment submission via Ajax WP
	###
	$comment_post_ID = $Ajaxpid;

		###
		### Write Comment
		###
		$status = $wpdb->get_row($wpdb->prepare("SELECT post_status, comment_status FROM $wpdb->posts WHERE ID = %s", $comment_post_ID));

		if ( empty($status->comment_status) ) {
			$WPresp = __('Comment ID not found.','cforms');
			do_action('comment_id_not_found', $comment_post_ID);
		} elseif ( 'closed' ==  $status->comment_status ) {
			$WPresp = __('Sorry, comments are closed for this item.','cforms');
			do_action('comment_closed', $comment_post_ID);
		} elseif ( in_array($status->post_status, array('draft', 'pending') ) ) {
			$WPresp = __('Comment is on draft.','cforms');
			do_action('comment_on_draft', $comment_post_ID);
		}
		else{

			$comment_author       = strip_tags($track['cauthor']);
			$comment_author_email = trim($track['email']);
			$comment_author_url   = trim($track['url']);
			$comment_content      = trim($track['comment']);
			$user_ID			  = $user->ID;

			### If the user is logged in
			if ( $user->ID ) {
				$comment_author       = esc_sql($user->display_name);
				$comment_author_email = esc_sql($user->user_email);
				$comment_author_url   = esc_sql($user->user_url);
				if ( current_user_can('unfiltered_html') ) {
					if ( wp_create_nonce('unfiltered-html-comment_' . $comment_post_ID) != $_POST['_wp_unfiltered_html_comment'] ) {
						kses_remove_filters(); ### start with a clean slate
						kses_init_filters(); ### set up the filters
					}
				}
			} elseif ( get_option('comment_registration') ){
					$WPresp = __('Sorry, you must be logged in to post a comment.','cforms');
					return;
			}


			$comment_parent = ($commentparent<>'')?absint($commentparent):0;

			$comment_type = '';
			$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');

			### Simple duplicate check
			if( $wpdb->get_var($wpdb->prepare("SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = %s AND ( comment_author = %s " .($comment_author_email ? " OR comment_author_email = '".esc_sql($comment_author_email)."'" : ""). ") AND comment_content = %s LIMIT 1;", $comment_post_ID, $comment_author, $comment_content)) ) {
				$WPresp = __('You\'ve said that before. No need to repeat yourself.','cforms');
				return;
			}

			### Simple flood-protection
			if ( $lasttime = $wpdb->get_var($wpdb->prepare("SELECT comment_date_gmt FROM $wpdb->comments WHERE comment_author_IP = %s OR comment_author_email = %s ORDER BY comment_date DESC LIMIT 1", $comment_author_IP, $comment_author_email)) ) {
				$time_lastcomment = mysql2date('U', $lasttime);
				$time_newcomment  = mysql2date('U', current_time('mysql', 1));

				if ( ($time_newcomment - $time_lastcomment) < (int)$cformsSettings['global']['cforms_commentWait'] ) {
				  do_action('comment_flood_trigger', $time_lastcomment, $time_newcomment);
				  $WPresp = __('You are posting comments too quickly. Slow down.','cforms');
				  return;
				}
			}

			$comment_id = wp_new_comment( $commentdata );
			$comment = get_comment($comment_id);

			if ( !$user->ID ) {
				setcookie('comment_author_' . COOKIEHASH, $comment->comment_author, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
				setcookie('comment_author_email_' . COOKIEHASH, $comment->comment_author_email, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
				setcookie('comment_author_url_' . COOKIEHASH, esc_url($comment->comment_author_url), time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
			}

	        ### keep track of custom comment fields
	        cforms2_write_tracking_record($no,$comment_author_email,$comment_id);

			$template = stripslashes($cformsSettings['global']['cforms_commentHTML']);

			$comment_in_mod = $comment->comment_approved?'':stripslashes($cformsSettings['global']['cforms_commentInMod']);

			$template = str_replace('{moderation}', preg_replace ( '|\r?\n|', '<br />',$comment_in_mod),$template);
			$template = str_replace('{id}',         $comment_id,$template);

			$template = str_replace('{usercomment}',preg_replace ( '|\r?\n|', '<br />',$comment->comment_content),$template);

			$template = str_replace('{url}',        $comment_author_url,$template);
			$template = str_replace('{author}',     $comment_author,$template);
			$template = str_replace('{date}',       mysql2date(get_option('date_format'), current_time('mysql')),$template);
			$template = str_replace('{time}',       gmdate(get_option('time_format'), current_time('timestamp')),$template);
			$template = str_replace('{avatar}',     get_avatar( $comment->comment_author_email, stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_avatar'] )) ), $template);

			$WPresp = stripslashes( $cformsSettings['global']['cforms_commentParent'] ).'$#$'. $template .'$#$'. preg_replace ( '|\r?\n|', '<br />', stripslashes($cformsSettings['global']['cforms_commentsuccess']));
			$WPsuccess = true;
			return;
		}

} else{
	###
	### non Ajax WP comment submission
	###
	$keys = array_keys($_POST);

	foreach ( $keys as $key ){
		if ( preg_match('/sendbutton(.*)/',$key,$no ) )
			break;
	}

	$no = $no[1];

	$user = wp_get_current_user();

	require_once (plugin_dir_path(__FILE__) . 'lib_validate.php');

	$comment_post_ID = (int) $_POST['comment_post_ID'];
	$cfpre = ( strpos( get_permalink($_POST['comment_post_ID']) ,'?')!==false ) ? '&':'?';

	if ( $all_valid ) {

		if ( isset($_POST['send2author']) && $_POST['send2author']=='1' ){
			cforms( '',$no );
			header("HTTP/1.0 301 Temporary redirect");
			header("Location: ".get_permalink($comment_post_ID).$cfpre.'cfemail=sent#cforms'.$no.'form' );
			die();
		}

		###
		### Filter first?
		###
	    $CFfunctionsC = plugin_dir_path(dirname(__FILE__)).'cforms-custom'.DIRECTORY_SEPARATOR.'my-functions.php';
		$CFfunctions = plugin_dir_path(__FILE__).'my-functions.php';
        if ( file_exists($CFfunctionsC) )
			include_once($CFfunctionsC);
		else if ( file_exists($CFfunctions) )
			include_once($CFfunctions);

		if( function_exists('my_cforms_filter') )
			$_POST = my_cforms_filter($_POST);

		###
		### Write Comment
		###
		$status = $wpdb->get_row($wpdb->prepare("SELECT post_status, comment_status FROM $wpdb->posts WHERE ID = %s", $comment_post_ID));

		if ( empty($status->comment_status) ) {
			do_action('comment_id_not_found', $comment_post_ID);
			die();
		} elseif ( 'closed' ==  $status->comment_status ) {
			do_action('comment_closed', $comment_post_ID);
			wp_die( __('Sorry, comments are closed for this item.','cforms') );
		} elseif ( in_array($status->post_status, array('draft', 'pending') ) ) {
			do_action('comment_on_draft', $comment_post_ID);
			die();
		}

		$comment_author       = trim(strip_tags($_POST['cauthor']));
		$comment_author_email = trim($_POST['email']);
		$comment_author_url   = trim($_POST['url']);
		$comment_content      = trim($_POST['comment']);

		### If the user is logged in
		if ( $user->ID ) {
			$comment_author       = esc_sql($user->display_name);
			$comment_author_email = esc_sql($user->user_email);
			$comment_author_url   = esc_sql($user->user_url);
			if ( current_user_can('unfiltered_html') ) {
				if ( wp_create_nonce('unfiltered-html-comment_' . $comment_post_ID) != $_POST['_wp_unfiltered_html_comment'] ) {
					kses_remove_filters(); ### start with a clean slate
					kses_init_filters(); ### set up the filters
				}
			}
		} else {
			if ( get_option('comment_registration') )
				wp_die( __('Sorry, you must be logged in to post a comment.','cforms') );
		}

        $comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;

        $comment_type = '';
        $commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');

		$comment_id = wp_new_comment( $commentdata );
		$comment = get_comment( $comment_id );

		if ( !$user->ID ) {
			setcookie('comment_author_' . COOKIEHASH, $comment->comment_author, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
			setcookie('comment_author_email_' . COOKIEHASH, $comment->comment_author_email, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
			setcookie('comment_author_url_' . COOKIEHASH, esc_url($comment->comment_author_url), time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
		}

		### send a notification if required
		if ( $cformsSettings['form'.$no]['cforms'.$no.'_tellafriend']=='21' )
			cforms( '',$no );

		### keep track of custom comment fields
        cforms2_write_tracking_record($no,$comment_author_email,$comment_id);

		$location = ( empty($_POST['redirect_to'] ) ? get_permalink($_POST['comment_post_ID']).$cfpre.'cfemail=posted'.'#cforms'.$no.'form' : $_POST['redirect_to'] );
		$location = apply_filters('comment_post_redirect', $location, $comment);
		wp_redirect($location);

	}
	else{
		$err='';
		foreach( array_keys($_POST) as $postvar ) {
			$err .= '&' . $postvar . '=' . urlencode($_POST[$postvar]);
        }

		header("HTTP/1.0 301 Temporary redirect");
		header("Location: ".get_permalink($comment_post_ID).$cfpre.'cfemail=err'.$err. '#cforms'.$no.'form' );
	}
} ### non Ajax
