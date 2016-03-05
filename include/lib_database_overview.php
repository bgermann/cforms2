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

add_action( 'wp_ajax_database_overview', 'cforms2_database_overview' );

function cforms2_database_overview() {
ob_start();
check_admin_referer( 'database_overview' );
if( !current_user_can('track_cforms') )
	die("access restricted.");

global $wpdb;

$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

$cformsSettings = get_option('cforms_settings');

$page = $_POST['page'];
$rp = $_POST['rp'];
$sortname = $_POST['sortname'];
$sortorder = $_POST['sortorder'];

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
	$where = "WHERE $querystr";
elseif ( $query<>'' )
	$where = "WHERE $qtype LIKE '$querystr'";
else
	$where = '';

if (!$sortname)
	$sortname = 'id';
if (!$sortorder) $sortorder = 'desc';
	$sort = "ORDER BY $sortname $sortorder";
if (!$page)
	$page = 1;
if (!$rp)
	$rp = 10;

$start = (($page-1) * $rp);
$limit = "LIMIT $start, $rp";

for ($i=1; $i <= $cformsSettings['global']['cforms_formcount']; $i++){
	$n = ( $i==1 )?'':$i;
	$fnames[$i]=stripslashes($cformsSettings['form'.$n]['cforms'.$n.'_fname']);
}


### total count
if ( $qtype=='id' )
	$total = 1;
else{
	$sql = "SELECT count(id) FROM {$wpdb->cformssubmissions} $where";
	$total = $wpdb->get_var($sql); //TODO check SQL injection
}

### get results
$sql="SELECT * FROM {$wpdb->cformssubmissions} $where $sort $limit";
$result = $wpdb->get_results($sql); //TODO check SQL injection

/*
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header("Pragma: no-cache" );
header("Content-type: text/xml");
*/

$xml = "<?xml version=\"1.0\"?>\n";
$xml .= "<rows>";
$xml .= "<page>$page</page>";
$xml .= "<total>$total</total>";

foreach ($result as $entry) {
	$n = ( $entry->form_id=='' )?'1':$entry->form_id;
	$xml .= "<row id='".$entry->id."'>";
	$xml .= "<cell><![CDATA[".$entry->id."]]></cell>";
	$xml .= "<cell><![CDATA[".( $fnames[$n] )."]]></cell>";
	$xml .= "<cell><![CDATA[".( $entry->email )."]]></cell>";
	$xml .= "<cell><![CDATA[".( $entry->sub_date )."]]></cell>";
	$xml .= "<cell><![CDATA[".( $entry->ip )."]]></cell>";
	$xml .= "</row>";
}

$xml .= "</rows>";
ob_end_clean();
echo $xml;
die();
}
