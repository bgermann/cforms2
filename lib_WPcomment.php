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

/**
 * Posts a new WordPress comment.
 */
function cforms2_new_comment($no) {
	global $wpdb;

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
	$user = wp_get_current_user();

	require_once (plugin_dir_path(__FILE__) . 'lib_validate.php');

	$comment_post_ID = (int) $_POST['comment_post_ID'];
	$cfpre = ( strpos( get_permalink($_POST['comment_post_ID']) ,'?')!==false ) ? '&':'?';

	if ( $all_valid ) {


		###
		### Filter first?
		###
		if( function_exists('my_cforms_filter') )
			my_cforms_filter($_POST);

		###
		### Write Comment
		###
		$status = $wpdb->get_row($wpdb->prepare("SELECT post_status, comment_status FROM $wpdb->posts WHERE ID = %s", $comment_post_ID));

		if ( empty($status->comment_status) ) {
			do_action('comment_id_not_found', $comment_post_ID);
			wp_die(__('Comment ID not found.','cforms2'));
		} elseif ( 'closed' ==  $status->comment_status ) {
			do_action('comment_closed', $comment_post_ID);
			wp_die(__('Sorry, comments are closed for this item.','cforms2'));
		} elseif ( in_array($status->post_status, array('draft', 'pending') ) ) {
			do_action('comment_on_draft', $comment_post_ID);
			wp_die(__('Comment is on draft.','cforms2'));
		}

		### If the user is logged in
		if ( $user->ID ) {
			$comment_author       = esc_sql($user->display_name);
			$comment_author_email = esc_sql($user->user_email);
			$comment_author_url   = esc_sql($user->user_url);
			if ( current_user_can('unfiltered_html') && wp_create_nonce('unfiltered-html-comment_' . $comment_post_ID) != $_POST['_wp_unfiltered_html_comment'] ) {
				kses_remove_filters(); ### start with a clean slate
				kses_init_filters(); ### set up the filters
			}
		} elseif ( get_option('comment_registration') ) {
			wp_die(__('Sorry, you must be logged in to post a comment.','cforms2'));
		} else {
			$comment_author       = trim(strip_tags($_POST['cauthor']));
			$comment_author_email = trim($_POST['email']);
			$comment_author_url   = trim($_POST['url']);
		}

		$comment_content = trim($_POST['comment']);
        $comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;

        $commentdata = array(
			'comment_post_ID'      => $comment_post_ID,
			'comment_author'       => $comment_author,
			'comment_author_email' => $comment_author_email,
			'comment_author_url'   => $comment_author_url,
			'comment_content'      => $comment_content,
			'comment_type'         => '',
			'comment_parent'       => $comment_parent,
			'user_id'              => $user->ID
		);

		$comment_id = wp_new_comment( $commentdata );
		$comment = get_comment( $comment_id );

		if ( !$user->ID ) {
			$time = current_time('timestamp') + 30000000;
			setcookie('comment_author_' . COOKIEHASH, $comment->comment_author, $time, COOKIEPATH, COOKIE_DOMAIN);
			setcookie('comment_author_email_' . COOKIEHASH, $comment->comment_author_email, $time, COOKIEPATH, COOKIE_DOMAIN);
			setcookie('comment_author_url_' . COOKIEHASH, esc_url($comment->comment_author_url), $time, COOKIEPATH, COOKIE_DOMAIN);
		}

		$location = ( empty($_POST['redirect_to'] ) ? get_permalink($_POST['comment_post_ID']).$cfpre.'cfemail=posted'.'#cforms'.$no.'form' : $_POST['redirect_to'] );
		$location = apply_filters('comment_post_redirect', $location, $comment);
		wp_redirect($location);
	} else {

		$err='';
		foreach( array_keys($_POST) as $postvar ) {
			$err .= '&' . $postvar . '=' . urlencode($_POST[$postvar]);
		}

		header("HTTP/1.0 301 Temporary redirect");
		header("Location: ".get_permalink($comment_post_ID).$cfpre.'cfemail=err'.$err. '#cforms'.$no.'form' );
	}
}
