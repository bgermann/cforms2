<?php add_action( 'wp_ajax_database_dlentries', 'cforms2_database_dlentries' );

function cforms2_database_dlentries() {
check_admin_referer( 'database_dlentries' );
if( !current_user_can('track_cforms') )
	die("access restricted.");

global $wpdb;

$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

### new global settings container, will eventually be the only one!
$cformsSettings = get_option('cforms_settings');


### get custom functions
$CFfunctionsC = dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPERATOR.'cforms-custom'.DIRECTORY_SEPERATOR.'my-functions.php';
$CFfunctions = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPERATOR.'my-functions.php';
if ( file_exists($CFfunctionsC) )
    include_once($CFfunctionsC);
else if ( file_exists($CFfunctions) )
    include_once($CFfunctions);


### get form names
for ($i=1; $i <= $cformsSettings['global']['cforms_formcount']; $i++){
	$n = ( $i==1 )?'':$i;
	$fnames[$i]=stripslashes($cformsSettings['form'.$n]['cforms'.$n.'_fname']);
}

$format = $_GET['format'];
$sub_ids = $_GET['ids'];
$sortBy = $_GET['sortBy'];
$sortOrder = $_GET['sortOrder'];
$charset = $_GET['enc'];

$qtype = $_GET['qtype'];
$query = $_GET['query'];

$tempfile = wp_tempnam('data.tmp');

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

	$in_list = ($sub_ids<>'all')?'AND id in ('.substr($sub_ids,0,-1).')':'';

	$count = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->cformssubmissions} WHERE TRUE $where $in_list");

    if( !is_writable($tempfile) ){
		$err = sprintf( __('File (data.tmp) in %s not writable! %sPlease adjust its file permissions/ownership!','cforms'),"\r\n\r\n --->  <code>". $tempfile ."\r\n\r\n","\r\n\r\n");

	    header("Pragma: public");
	    header("Expires: 0");
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	    header("Content-Type: application/force-download");
	    header("Content-Type: text/download");
	    header("Content-Type: text/txt");
	    header("Content-Disposition: attachment; filename=\"error.txt\"");
	    header("Content-Transfer-Encoding: binary");
	    header("Content-Length: " .(string)(strlen($err)) );
		echo $err;
		die();
    }

    $handle = fopen($tempfile, "w");

    ### UTF8 header
    if ( $charset=='utf-8' )
        fwrite($handle, pack("CCC",0xef,0xbb,0xbf));
trigger_error("$fnames, $count, $where, $in_list, $sortBy, $sortOrder, $cformsSettings, $charset");
	switch ( $format ){
		case 'xml': cforms2_get_xml($handle, $fnames, $count, $where, $in_list, $sortBy, $sortOrder, $cformsSettings, $charset); break;
		case 'csv': cforms2_get_csv_tab($handle, 'csv', $fnames, $count, $where, $in_list, $sortBy, $sortOrder, $cformsSettings, $charset); break;
		case 'tab': cforms2_get_csv_tab($handle, 'tab', $fnames, $count, $where, $in_list, $sortBy, $sortOrder, $cformsSettings, $charset); break;
	}

    fclose($handle);

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/force-download");
	header("Content-Type: text/download");
	header("Content-Type: text/$format");
	header("Content-Disposition: attachment; filename=\"formdata." . $format . "\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " .(string)(filesize($tempfile)) );
    ob_clean();

    readfile( $tempfile );
    ob_flush();
    flush();
    unlink($tempfile);
	die();

}
}

function cforms2_get_csv_tab($handle, $format='csv', $fnames, $count, $where, $in_list, $sortBy, $sortOrder, $cformsSettings, $charset){
	global $wpdb;

    $results = $wpdb->get_results( "SELECT ip, id, sub_date, form_id, field_name,field_val FROM {$wpdb->cformsdata},{$wpdb->cformssubmissions} WHERE sub_id=id $where $in_list ORDER BY $sortBy $sortOrder, f_id ASC" );
	
	$br="\n";
	$buffer=array();
	$body='';

    $sub_id='';
	$format = ($format=="csv")?",":"\t";
    $ipTab = ($_GET['addip']=='true'?$format:'');

	$head = ($_GET['header']=='true')?$format . $format . $ipTab:'';

    $last_n = '';

	foreach( $results as $key => $entry ) {

	### while( $entry = mysql_fetch_array($r) ){

		if ( $entry->field_name=='page' || strpos($entry->field_name,'Fieldset')!==false )
			continue;

        $next_n = ( $entry->form_id=='' )?'1':$entry->form_id;

		if( $sub_id<>$entry->id ){   ### new record starts

			if ( $buffer[body]<>'' ){
                if( $_GET['header']=='true' && $buffer[last_n]<>$buffer[last2_n])
					fwrite($handle, $buffer[head] . $br . $buffer[body] . $br);
				else
					fwrite($handle, $buffer[body] . $br);
            }
            $buffer[body]   = $body;  ### save 1 line
            $buffer[head]   = $head;  ### save 1 line
            $buffer[last2_n]= $buffer[last_n];
            $buffer[last_n] = $last_n;

			$body  = '"'.__('Form','cforms').': ' . cforms2_enc_data($fnames[$next_n], $charset). '"'. $format .'"'. cforms2_enc_data($entry->sub_date, $charset) .'"' . $format . ($_GET['addip']=='true'?$entry->ip.$format:'');
			$head  = ($_GET['header']=='true')?$format . $format . $ipTab:'';
			$last_n = $next_n;

			$sub_id = $entry->id;
		}

		$url='';
        $urlTab='';
        if( $_GET['addurl']=='true' && strpos($entry->field_name,'[*') ){

            preg_match('/.*\[\*(.*)\]$/i',$entry->field_name,$t);
            $no   = $t[1]==''?$entry->form_id:($t[1]==1?'':$t[1]);

		    $urlTab = $format;
			$entry->field_name = substr($entry->field_name,0,strpos($entry->field_name,'[*'));

            $t = explode( '$#$',stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_upload_dir'])) );
            $fdir = $t[0];
            $fdirURL = $t[1];

			$subID = $cformsSettings['form'.$no]['cforms'.$no.'_noid'] ? '' : $entry->id.'-';

            if ( $fdirURL=='' ) {
				$plugindir = dirname(dirname(dirname(plugin_basename(__FILE__))));
                $url = plugin_dir_url( __FILE__ ).substr( $fdir, strpos($fdir,$plugindir) + strlen($plugindir) + 1 );
			} else
                $url = $fdirURL;

			$passID = ($cformsSettings['form'.$no]['cforms'.$no.'_noid']) ? '':$entry->id;
			$fileInfoArr = array('name'=>strip_tags($entry->field_val), 'path'=>$url, 'subID'=>$passID);

			if ( function_exists('my_cforms_logic') )
				$fileInfoArr = my_cforms_logic( $results, $fileInfoArr, 'fileDestinationTrackingPage' );
			
			if( ! array_key_exists('modified', $fileInfoArr) )
				$fileInfoArr['name'] = $subID . $fileInfoArr['name'];
			
			$url = $fileInfoArr['path'] . '/' . $fileInfoArr['name'] . $format;				
				
		}

        $head .= ($_GET['header']=='true')?'"'.cforms2_enc_data(stripslashes($entry->field_name), $charset).'"' . $format . $urlTab:'';
		$body .= '"' . str_replace('"','""', cforms2_enc_data(stripslashes($entry->field_val)), $charset) . '"' . $format . $url;

	} ### foreach


   	### clean up buffer
    if ( $buffer[body]<>'' ){
        if( $_GET['header']=='true' && $buffer[last_n]<>$buffer[last2_n])
            fwrite($handle, $buffer[head] . $br . $buffer[body] . $br);
        else
            fwrite($handle, $buffer[body] . $br);
    }

    ### clean up last body
	if( $_GET['header']=='true' && $buffer[last_n]<>$next_n)
	    fwrite($handle, $head . $br . $body . $br);
	else
	    fwrite($handle, $body . $br);

/*
	mysql_free_result($r);
	mysql_close();
*/
	return;
}



function cforms2_get_xml($handle, $fnames, $count, $where, $in_list, $sortBy, $sortOrder, $cformsSettings, $charset){
	global $wpdb;

	if( $charset=='utf-8' )
		fwrite($handle, "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<entries>\n");
	else
		fwrite($handle, "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n<entries>\n");

	$results = $wpdb->get_results(
	       "SELECT ip, id, sub_date, form_id, field_name,field_val FROM {$wpdb->cformsdata},{$wpdb->cformssubmissions} WHERE sub_id=id $where $in_list ORDER BY $sortBy $sortOrder, f_id ASC"
		   //,"ARRAY_A"
	);
	
	// echo '<br><pre>'.print_r($results,1).'</pre>';
	
	/*
	mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	@mysql_select_cforms2_dbgDB_NAME) or die( "Unable to select database");

 	$sql = "SELECT ip, id, sub_date, form_id, field_name,field_val FROM {$wpdb->cformsdata},{$wpdb->cformssubmissions} WHERE sub_id=id $where $in_list ORDER BY $sortBy $sortOrder, f_id ASC";
	$r = mysql_query($sql);
	*/
	
	//  &#10;
	
    $sub_id ='';
    foreach( $results as $key => $entry ) {
	### while( $entry = mysql_fetch_array($r) ){

	        if ( $entry->field_name=='page' || strpos($entry->field_name,'Fieldset')!==false )
	            continue;

			//echo '<br><pre>'."$key =>".print_r($entry,1).'</pre>';

	        $n = ( $entry->form_id=='' )?'1':$entry->form_id;
	        if( $sub_id<>$entry->id ){

	            if ( $sub_id<>'' )
	            	fwrite($handle, "</entry>\n");

	            fwrite($handle, '<entry form="'.cforms2_enc_data_xml( $fnames[$n], $charset).'" date="'.cforms2_enc_data_xml( $entry->sub_date, $charset ).'"'.($_GET['addip']=='true'?' ip="'.$entry->ip.'"':'').">\n");

	            $sub_id = $entry->id;
	        }
	        fwrite($handle, '<data col="'.cforms2_enc_data_xml( stripslashes($entry->field_name), $charset ).'"><![CDATA['.cforms2_enc_data_xml( stripslashes($entry->field_val), $charset ).']]></data>'."\n");
			//echo '<br><pre>'.$entry->field_name."=".$entry->field_val.'</pre>';

	} ### while

	/*
	mysql_free_result($r);
	mysql_close();
	*/
	
	if($sub_id<>'')
	 fwrite($handle, "</entry>\n</entries>\n");

	return;
}

function cforms2_enc_data ( $d, $charset ){
	$d = str_replace( array('"',"\r","\n"), array('&quot;',"","\r"),$d );
	$d = ( $charset=='utf-8' ) ? $d : utf8_decode($d);
	return $d;
}
function cforms2_enc_data_xml ( $d , $charset ){
	$d = str_replace( array('"'), array('&quot;'),$d );
	$d = ( $charset=='utf-8' ) ? $d : utf8_decode($d);
	return $d;
} ?>
