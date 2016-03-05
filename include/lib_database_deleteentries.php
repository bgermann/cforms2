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

add_action( 'wp_ajax_database_deleteentries', 'cforms2_database_deleteentries' );

function cforms2_database_deleteentries() {
check_admin_referer( 'database_deleteentries' );
if( !current_user_can('track_cforms') )
	die("access restricted.");

global $wpdb;

$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

$cformsSettings = get_option('cforms_settings');

$sub_ids = $_POST['ids'];
$qtype = $_POST['qtype'];
$query = $_POST['query'];

### get form id from name
$query = str_replace('*','',$query);
$form_ids = false;
if ( $qtype == 'form_id' && $query <> '' ){

	$forms = $cformsSettings['global']['cforms_formcount'];

	for ($i=0;$i<$forms;$i++) {
		$no = ($i==0)?'':($i+1);

		if ( preg_match( '/'.$query.'/i', $cformsSettings['form'.$no]['cforms'.$no.'_fname'] ) ){
        	$form_ids = $form_ids . "'$no',";
		}
	}
	$querystr = ( !$form_ids )?'$%&/':' form_id IN ('.substr($form_ids,0,-1).')';
}else{
	$querystr = '%'.$query.'%';
}


if ( $form_ids )
	$doquery = "AND $querystr";
elseif ( !empty($query) && $sub_ids=='all' )
	$doquery = "AND $qtype LIKE '$querystr'";
else
	$doquery = '';


if ( $sub_ids<>'' ){

	if ( $sub_ids=='all' )
		$all_entries[0] = 'all';
	else
		$all_entries = explode(',',substr($sub_ids,0,-1));

	foreach ($all_entries as $entry) :
		$entry = (int) $entry;

		if ($entry <> 'all')
			$sub_id = "sub_id = '$entry'";
		else
			$sub_id = '1';

		$sql = "SELECT field_val,form_id,sub_id FROM {$wpdb->cformsdata},{$wpdb->cformssubmissions} WHERE $sub_id $doquery AND id=sub_id AND field_name LIKE '%[*%'";
		$filevalues = $wpdb->get_results($sql); //TODO check SQL injection

		$found = 0;

		foreach( $filevalues as $fileval ) {

			$temp = explode( '$#$',stripslashes(htmlspecialchars($cformsSettings['form'.$fileval->form_id]['cforms'.$fileval->form_id.'_upload_dir'])) );
			$fileuploaddir = $temp[0];

			$file = $fileuploaddir.'/'.$fileval->sub_id.'-'.$fileval->field_val;

			if ( $fileval->field_val <> '' ){
				if ( file_exists( $file ) ){
					unlink ( $file );
					$found = $found | 1;
				}
				else{
					$found = $found | 2;
				}
			}

		}

		if ($entry<>'all'){
			$whereD = "sub_id = '$entry'";
			$whereS = "id = '$entry'";
		}
		else{
			$whereD = '1';
			$whereS = '1';
		}

		if ( !empty($query) && $sub_ids=='all' )
			$dospecialquery = "AND sub_id IN ( SELECT id FROM {$wpdb->cformssubmissions} WHERE $qtype LIKE '%$query%') ";
		else
			$dospecialquery = '';

		$nuked = $wpdb->query("DELETE FROM {$wpdb->cformsdata} WHERE $whereD $dospecialquery"); //TODO check SQL injection
		$nuked = $wpdb->query("DELETE FROM {$wpdb->cformssubmissions} WHERE $whereS $doquery"); //TODO check SQL injection
	endforeach;

	 _e('Entries successfully removed from the tracking tables!', 'cforms2');
}
die();
}
