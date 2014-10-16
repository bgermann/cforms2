<?php
### Show entries on dashboard for WP2.7+
function cforms_dashboard_27_setup() {
	wp_add_dashboard_widget( 'cforms_dashboard', __( 'Recent cforms entries','cforms' ), 'cforms_dashboard' );
}


### Show entries on dashboard
function cforms_dashboard() {
	global $wpdb, $plugindir, $wp_db_version, $cformsSettings;

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
				"</style>\n";
	if ( $wp_db_version < 6846 ){
		$content .= "<ul style='font-size:0.8em'>";
	}
	else {
		$content .= "<ul>";
	}

	if( count($entries)>0 ){
		foreach($entries as $entry){
				$dateConv = mysql2date(get_option('date_format'), $entry->sub_date);
				$content.= '<li><img class="dashboardIcon" alt="" src="'.$cformsSettings['global']['cforms_root'].'/images/cformsicon.png">'.
				"<a title=\"". __('click for details','cforms') ."\" href='admin.php?page=".$plugindir."/cforms-database.php&d-id=$entry->id#entry$entry->id'>$entry->email</a> ".
				__('via','cforms') . " <strong>". $cformsSettings['form'.$entry->form_id]['cforms'.$entry->form_id.'_fname']. "</strong>".
				" on ". $dateConv ."</li>";
		}
	}
	else
		$content.= '<li>'.__('No entries yet','cforms').'</li>';

	$content.= '</ul>';
	
	if ( $wp_db_version < 6846 ){
		$content .= "<h3><a href='admin.php?page=".$plugindir."/cforms-database.php'>" . __('Visit the cforms tracking page for all entries ','cforms') . " &raquo;</a> </h3>";
	}
	else {
		$content .= "<p class=\"youhave\"><a href='admin.php?page=".$plugindir."/cforms-database.php'>" . __('Visit the cforms tracking page for all entries ','cforms') . " &raquo;</a> </p>";
	}

	echo $content;
}
?>