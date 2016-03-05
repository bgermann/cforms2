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

add_action( 'wp_ajax_database_getentries', 'cforms2_database_getentries' );

function cforms2_database_getentries() {
check_admin_referer( 'database_getentries' );
if( !current_user_can('track_cforms') )
	die("access restricted.");

global $wpdb;

$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

$cformsSettings = get_option('cforms_settings');

$showIDs = $_POST['showids'];
if ($showIDs<>'') {
    $sortBy = isset($_POST['sortby']) && $_POST['sortby']<>'' ? $_POST['sortby'] : 'sub_id';
    $sortOrder = isset($_POST['sortorder']) && $_POST['sortorder'] === 'asc' ? 'asc' : 'desc';

    $qtype = $_POST['qtype'];

    ### get form id from name
    $query = str_replace('*', '', $_POST['query']);
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

    $sql = "SELECT *, form_id, ip FROM $wpdb->cformsdata, $wpdb->cformssubmissions WHERE sub_id=id ";
    $sqlargs = array();

	if ( $showIDs<>'all' ) {
        $sub_ids = explode(',', substr($showIDs,0,-1));
        $placeholder = implode(',', array_fill(0, count($sub_ids), '%d'));
		$sql .= "AND sub_id in ($placeholder) ";
        $sqlargs = array_merge($sqlargs, $sub_ids);
    }

    if ( $form_ids ) {
        $sql .= "AND %s ";
        $sqlargs[] = $querystr;
    }
    elseif ( $query<>'' ) {
        $sql .= "AND %s LIKE %s ";
        $sqlargs[] = $qtype;
        $sqlargs[] = $querystr;
    }

    $sql .= "ORDER BY %s $sortOrder, f_id";
    $sqlargs[] = $sortBy;

    $sql = $wpdb->prepare($sql, $sqlargs);
	$entries = $wpdb->get_results($sql);
    cforms2_dbg($sql);
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
					 '<table class="dataheader"><tr><td>'.__('Form:','cforms2').' </td><td class="b">'. stripslashes($cformsSettings['form'.$entry->form_id]['cforms'.$entry->form_id.'_fname']) . '</td><td class="e">(ID:' . $entry->sub_id . ')</td><td class="d">' . $time.' &nbsp; '.$date. '</td>' .
					 '<td class="s">&nbsp;</td><td><a href="#" class="xdatabutton allbuttons deleteall" type="submit" id="xbutton'.$entry->sub_id.'">'.__('Delete this entry', 'cforms2').'</a></td>' .
					 '<td><a class="cdatabutton dashicons dashicons-dismiss" type="submit" id="cbutton'.$entry->sub_id.'" title="'.__('close this entry', 'cforms2').'"></a></td>' .
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

					if ( $fileuploaddirurl=='' ) {
						$fileurl = get_site_url() . substr( trailingslashit($fileuploaddir), strlen(get_home_path()) - 1 );
					} else
						$fileurl = trailingslashit($fileuploaddirurl);


					$passID = ($cformsSettings['form'.$no]['cforms'.$no.'_noid']) ? '':$entry->sub_id;
					$fileInfoArr = array('name'=>strip_tags($val), 'path'=>$fileurl, 'subID'=>$passID);

					if ( function_exists('my_cforms_logic') )
						$fileInfoArr = my_cforms_logic( $entries, $fileInfoArr, 'fileDestinationTrackingPage');

					if( ! array_key_exists('modified', $fileInfoArr) )
						$fileInfoArr['name'] = $subID . $fileInfoArr['name'];
					
					$fileurl = $fileInfoArr['path'] . $fileInfoArr['name'] . $_GET['format'];
					
					echo '<div class="showformfield meta"><div class="L">';
					echo substr($name, 0,strpos($name,'[*'));
					if ( $entry->field_val == '' )
						echo 	'</div><div class="R">' . __('-','cforms2') . '</div></div>' . "\n";
					else
						echo 	'</div><div class="R">' . '<a href="' . $fileurl . '">' . str_replace("\n","<br />", strip_tags($val) ) . '</a>' . '</div></div>' . "\n";

			}
			elseif ($name=='page') {  // special field: page

					echo '<div class="showformfield meta"><div class="L">';
					_e('Submitted via page', 'cforms2');
					echo 	'</div><div class="R">' . str_replace("\n","<br />", strip_tags($val) ) . '</div></div>' . "\n";

					echo '<div class="showformfield meta"><div class="L">';
					_e('IP address', 'cforms2');
					echo 	'</div><div class="R"><a href="http://geomaplookup.net/?ip='.$entry->ip.'" title="'.__('IP Lookup', 'cforms2').'">'.$entry->ip.'</a></div></div>' . "\n";


			} elseif ( strpos($name,'Fieldset')!==false ) {

					if ( strpos($name,'FieldsetEnd')===false )
                    	echo '<div class="showformfield tfieldset"><div class="L">&nbsp;</div><div class="R">' . strip_tags($val)  . '</div></div>' . "\n";

			} else {

					echo '<div class="showformfield"><div class="L">' . $name . '</div>' .
							'<div id="'.$entry->f_id.'" class="R">' . str_replace("\n","<br />", strip_tags($val) ) . '</div></div>' . "\n";

			}

		}
		echo '</div>';

	else : ?>

		<p align="center"><?php _e('Sorry, data not found. Please refresh your data table.', 'cforms2') ?></p>
		</div>

	<?php endif;

}
die();
}
