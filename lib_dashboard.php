<?php add_action( 'wp_dashboard_setup', 'cforms2_dashboard_setup', 1 );

### Show entries on dashboard for WP2.7+
function cforms2_dashboard_setup() {
	wp_add_dashboard_widget( 'cforms_dashboard', __( 'Recent cforms entries','cforms' ), 'cforms2_dashboard' );
}


### Show entries on dashboard
function cforms2_dashboard() {
	global $wpdb, $cformsSettings;

	if (!current_user_can('track_cforms')) return;

	$WHERE='';
	for($i=0; $i<$cformsSettings['global']['cforms_formcount']; $i++){
		$no = ($i==0)?'':($i+1);
		if ( $cformsSettings['form'.$no]['cforms'.$no.'_dashboard'] == '1' )
			$WHERE .= "'$no',";
	}

	if ( $WHERE <> '')
		$WHERE = "WHERE form_id in (".substr($WHERE,0,-1).")";
	else
		return;

	$entries = $wpdb->get_results("SELECT * FROM {$wpdb->cformssubmissions} $WHERE ORDER BY sub_date DESC LIMIT 0,5");

	$content .= "<style>\n".
				"img.dashboardIcon{\n".
				"vertical-align: middle;\n".
				"margin-right: 6px;\n".
				"}\n".
				"</style>\n".
				"<ul>";

	if( count($entries)>0 ){
		foreach($entries as $entry){
				$dateConv = mysql2date(get_option('date_format'), $entry->sub_date);
				$content.= '<li><img class="dashboardIcon" alt="" src="'.plugin_dir_url(__FILE__).'images/cformsicon.png">'.
				"<a title=\"". __('click for details','cforms') ."\" href='admin.php?page=".plugin_dir_path(plugin_basename(__FILE__))."/cforms-database.php&d-id=$entry->id#entry$entry->id'>$entry->email</a> ".
				__('via','cforms') . " <strong>". $cformsSettings['form'.$entry->form_id]['cforms'.$entry->form_id.'_fname']. "</strong>".
				" on ". $dateConv ."</li>";
		}
	}
	else
		$content.= '<li>'.__('No entries yet','cforms').'</li>';

	$content .= "</ul><p class=\"youhave\"><a href='admin.php?page=".plugin_dir_path(plugin_basename(__FILE__))."/cforms-database.php'>" . __('Visit the cforms tracking page for all entries ','cforms') . " &raquo;</a> </p>";

	echo $content;
}
