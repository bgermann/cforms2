<?php

### supporting WP2.6 wp-load & custom wp-content / plugin dir
if ( file_exists('../../abspath.php') )
	include_once('../../abspath.php');
else
	$abspath='../../../../../';

if ( file_exists( $abspath . 'wp-load.php') )
	require_once( $abspath . 'wp-load.php' );
else
	require_once( $abspath . 'wp-config.php' );

if( !current_user_can('track_cforms') )
	wp_die("access restricted.");

### mini firewall

global $wpdb;

$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

### new global settings container, will eventually be the only one!
$cformsSettings = get_option('cforms_settings');

### get form names
for ($i=1; $i <= $cformsSettings['global']['cforms_formcount']; $i++){
	$n = ( $i==1 )?'':$i;
	$fnames[$i]=stripslashes($cformsSettings['form'.$n]['cforms'.$n.'_fname']);
}


$format = $_GET['format'];
$sub_ids = $_GET['ids'];
$sortBy = $_GET['sortBy'];
$sortOrder = $_GET['sortOrder'];

$qtype = $_GET['qtype'];
$query = $_GET['query'];

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
	$where = "AND $querystr";
elseif ( $query<>'' )
	$where = "AND $qtype LIKE '$querystr'";
else
	$where = '';


if ( !$sortBy || $sortBy=='undefined' )
	$sortBy = 'id';
if ( !$sortOrder || $sortOrder=='undefined' )
	$sortOrder = 'desc';

if ($sub_ids<>'') {

	if ( $sub_ids<>'all' )
		$in_list = 'AND sub_id in ('.substr($sub_ids,0,-1).')';
	else
		$in_list = '';

	$sql = "SELECT *, form_id FROM {$wpdb->cformsdata},{$wpdb->cformssubmissions} WHERE sub_id=id $where $in_list ORDER BY $sortBy $sortOrder, f_id ASC";

	$entries = $wpdb->get_results($sql);

	if ( $format=='xml' )
		$buffer = getXML($entries);
	else if ( $format=='csv' )
		$buffer = getCSVTAB($entries);
	else if ( $format=='tab' )
		$buffer = getCSVTAB($entries,'tab');


	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/force-download");
	header("Content-Type: text/download");
	header("Content-Type: text/$format");
	header("Content-Disposition: attachment; filename=\"formdata." . $format . "\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " .(string)(strlen($buffer)) );
	print $buffer;
	exit();

}

function getCSVTAB($arr,$format='csv'){
	global $fnames;

	$br="\n";
	$buffer='';
	$body='';

    $sub_id='';
	$format = ($format=="csv")?",":"\t";
	$head = ($_GET['header']=='true')?$format . $format:'';

    $last_n = '';

	foreach ($arr as $entry){
		if ( $entry->field_name=='page' || strpos($entry->field_name,'Fieldset')!==false )
			continue;

        $n = ( $entry->form_id=='' )?'1':$entry->form_id;

		if( $sub_id<>$entry->sub_id ){

			if ( $_GET['header']=='true' && $sub_id<>'' && $last_n<>$n ){

                if ( $last_n=='' )
					$buffer .= $head . $br . substr($body,0,-1) . $br ;
				else
					$buffer .= substr($body,0,-1) . $br . $head . $br;

	            $last_n = $n;
				$head = $format . $format;
                $body = '';
            }
			else if ( $sub_id<>'' ){
				$buffer .= substr($body,0,-1) . $br;
				$head = ($_GET['header']=='true')?$format . $format:'';
                $body = '';
            }

			$sub_id = $entry->sub_id;

			$body .= __('Form','cforms').': "' . utf8_decode($fnames[$n]). '"'. $format .'"'. utf8_decode($entry->sub_date) .'"' . $format;
		}

        $head .= ($_GET['header']=='true')?'"'.utf8_decode($entry->field_name).'"' . $format:'';

		$body .= '"' . str_replace('"','""', utf8_decode(stripslashes($entry->field_val))) . '"' . $format;
	}
	return $buffer.$body;
}

function getXML($arr){
	global $fnames;

	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<entries>\n";

	$sub_id ='';
	foreach ($arr as $entry) {
		if ( $entry->field_name=='page' || strpos($entry->field_name,'Fieldset')!==false )
			continue;

		$n = ( $entry->form_id=='' )?'1':$entry->form_id;
		if( $sub_id<>$entry->sub_id ){

			if ( $sub_id<>'' )
				$xml .= "</entry>\n";

			$xml .= '<entry form="'.utf8_decode( $fnames[$n]).'" date="'.utf8_decode( $entry->sub_date )."\">\n";
			$sub_id = $entry->sub_id;
		}
		$xml .= '<data col="'.utf8_decode( stripslashes($entry->field_name) ).'"><![CDATA['.utf8_decode( stripslashes($entry->field_val) ).']]></data>'."\n";
	}
	if($sub_id<>'')
	 $xml .= "</entry>\n</entries>\n";
	return $xml;
}
?>