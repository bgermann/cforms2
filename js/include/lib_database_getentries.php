<?php

### supporting WP2.6 wp-load & custom wp-content / plugin dir
### check if called from cforms-database.php
if ( !defined('ABSPATH') ){
	if ( file_exists('../../abspath.php') )
	    include_once('../../abspath.php');
	else
	    $abspath='../../../../../';

	if ( file_exists( $abspath . 'wp-load.php') )
	    require_once( $abspath . 'wp-load.php' );
	else
	    require_once( $abspath . 'wp-config.php' );
}

### mini firewall
if( !current_user_can('track_cforms') )
	wp_die("access restricted.");

global $wpdb;

$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

### new global settings container, will eventually be the only one!
$cformsSettings = get_option('cforms_settings');

### get custom functions
$CFfunctionsC = dirname(dirname(dirname(dirname(__FILE__)))).$cformsSettings['global']['cforms_IIS'].'cforms-custom'.$cformsSettings['global']['cforms_IIS'].'my-functions.php';
$CFfunctions = dirname(dirname(dirname(__FILE__))).$cformsSettings['global']['cforms_IIS'].'my-functions.php';
if ( file_exists($CFfunctionsC) )
    include_once($CFfunctionsC);
else if ( file_exists($CFfunctions) )
    include_once($CFfunctions);

### get form names
for ($i=1; $i <= $cformsSettings['global']['cforms_formcount']; $i++){
	$n = ( $i==1 )?'':$i;
	$fnames[$i]=stripslashes($cformsSettings['form'.$n]['cforms'.$n.'_fname']);
}

$showIDs = $_POST['showids'];
$sortBy = ($_POST['sortby']<>'')?$_POST['sortby']:'sub_id';
$sortOrder = ($_POST['sortorder']<>'')?substr($_POST['sortorder'],1):'desc';

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
	$where = "AND $querystr";
elseif ( $query<>'' )
	$where = "AND $qtype LIKE '$querystr'";
else
	$where = '';

if ($showIDs<>'') {

	if ( $showIDs<>'all' )
		$in_list = 'AND sub_id in ('.substr($showIDs,0,-1).')';
	else
		$in_list = '';

	$sql="SELECT *, form_id, ip FROM {$wpdb->cformsdata},{$wpdb->cformssubmissions} WHERE sub_id=id $in_list $where ORDER BY $sortBy $sortOrder, f_id";
	$entries = $wpdb->get_results($sql);
	?>

	<div id="top">
	<?php if ($entries) :

		$sub_id='';
		foreach ($entries as $entry){

			if( $sub_id<>$entry->sub_id ){

				if( $sub_id<>'' )
					echo '</div>';

				$sub_id = $entry->sub_id;

	            $date = mysql2date(get_option('date_format'), $entry->sub_date);
	            $time = mysql2date(get_option('time_format'), $entry->sub_date);

				echo '<div class="showform" id="entry'.$entry->sub_id.'">'.
					 '<table class="dataheader"><tr><td>'.__('Form:','cforms').' </td><td class="b">'. stripslashes($cformsSettings['form'.$entry->form_id]['cforms'.$entry->form_id.'_fname']) . '</td><td class="e">(ID:' . $entry->sub_id . ')</td><td class="d">' . $time.' &nbsp; '.$date. '</td>' .
					 '<td class="s">&nbsp;</td><td><a href="#" class="xdatabutton allbuttons deleteall" type="submit" id="xbutton'.$entry->sub_id.'">'.__('Delete this entry', 'cforms').'</a></td>' .
					 '<td><a class="cdatabutton" type="submit" id="cbutton'.$entry->sub_id.'" title="'.__('close this entry', 'cforms').'" value=""></a></td>' .
                     "</tr></table>\n";
			}

			$name = $entry->field_name==''?'':stripslashes($entry->field_name);
			$val  = $entry->field_val ==''?'':stripslashes($entry->field_val);

			if (strpos($name,'[*')!==false) {  // attachments?

					preg_match('/.*\[\*(.*)\]$/i',$name,$r);
					$no   = $r[1]==''?$entry->form_id:($r[1]==1?'':$r[1]);

					$temp = explode( '$#$',stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_upload_dir'])) );
					$fileuploaddir = $temp[0];
					$fileuploaddirurl = $temp[1];

					$subID = ($cformsSettings['form'.$no]['cforms'.$no.'_noid'])?'':$entry->sub_id.'-';

					if ( $fileuploaddirurl=='' )
	                    $fileurl = $cformsSettings['global']['cforms_root'].substr($fileuploaddir,strpos($fileuploaddir,$cformsSettings['global']['plugindir'])+strlen($cformsSettings['global']['plugindir']),strlen($fileuploaddir));
					else
	                    $fileurl = $fileuploaddirurl;


					$passID = ($cformsSettings['form'.$no]['cforms'.$no.'_noid']) ? '':$entry->sub_id;
					$fileInfoArr = array('name'=>strip_tags($val), 'path'=>$fileurl, 'subID'=>$passID);

					if ( function_exists('my_cforms_logic') )
						$fileInfoArr = my_cforms_logic( $results, $fileInfoArr, 'fileDestinationTrackingPage');

					if( ! array_key_exists('modified', $fileInfoArr) )
						$fileInfoArr['name'] = $subID . $fileInfoArr['name'];
					
					$fileurl = $fileInfoArr['path'] . '/' . $fileInfoArr['name'] . $format;
					
					echo '<div class="showformfield meta"><div class="L">';
					echo substr($name, 0,strpos($name,'[*'));
					if ( $entry->field_val == '' )
						echo 	'</div><div class="R">' . __('-','cforms') . '</div></div>' . "\n";
					else
						echo 	'</div><div class="R">' . '<a href="' . $fileurl . '">' . str_replace("\n","<br />", strip_tags($val) ) . '</a>' . '</div></div>' . "\n";

			}
			elseif ($name=='page') {  // special field: page

					echo '<div class="showformfield meta"><div class="L">';
					_e('Submitted via page', 'cforms');
					echo 	'</div><div class="R">' . str_replace("\n","<br />", strip_tags($val) ) . '</div></div>' . "\n";

					echo '<div class="showformfield meta"><div class="L">';
					_e('IP address', 'cforms');
					echo 	'</div><div class="R"><a href="http://geomaplookup.net/?ip='.$entry->ip.'" title="'.__('IP Lookup', 'cforms').'">'.$entry->ip.'</a></div></div>' . "\n";


			} elseif ( strpos($name,'Fieldset')!==false ) {

					if ( strpos($name,'FieldsetEnd')===false )
                    	echo '<div class="showformfield tfieldset"><div class="L">&nbsp;</div><div class="R">' . strip_tags($val)  . '</div></div>' . "\n";

			} else {

					echo '<div class="showformfield"><div class="L">' . $name . '</div>' .
							'<div id="'.$entry->f_id.'" class="R editable" title="'.__('edit this field', 'cforms').'">' . str_replace("\n","<br />", strip_tags($val) ) . '</div></div>' . "\n";

			}

		}
		echo '</div>';

	else : ?>

		<p align="center"><?php _e('Sorry, data not found. Please refresh your data table.', 'cforms') ?></p>
		</div>

	<?php endif;

}
?>