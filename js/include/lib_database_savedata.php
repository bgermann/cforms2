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

add_action( 'wp_ajax_database_savedata', 'cforms2_database_savedata' );

function cforms2_database_savedata() {
check_admin_referer( 'database_savedata' );
if( !current_user_can('track_cforms') )
	die("access restricted.");

global $wpdb;

$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

$f_id   = $_POST['element_id'];
$oldVal = addslashes($_POST['original_html']);
$newVal = addslashes($_POST['update_value']);

if ( $f_id<>'' && $newVal<>$oldVal  ) {

	$sql="UPDATE {$wpdb->cformsdata} SET field_val=%s WHERE f_id = %s";
	$wpdb->get_results($wpdb->prepare($sql, $newVal, $f_id));
	echo str_replace("\n",'<br />',stripslashes(stripslashes($newVal)));

}
die();
}
