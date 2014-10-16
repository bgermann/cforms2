<?php add_action( 'wp_ajax_database_savedata', 'cforms2_database_savedata' );

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

	$sql="UPDATE {$wpdb->cformsdata} SET field_val='$newVal' WHERE f_id = '$f_id'";
	$entries = $wpdb->get_results($sql);
	echo str_replace("\n",'<br />',stripslashes(stripslashes($newVal)));

}
die();
}
