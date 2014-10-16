<?php


### plugin removal
if( isset($_POST['cfdeleteall']) && !function_exists("wp_get_current_user") ) {

	global $current_user,$user_ID;
	$u = get_currentuserinfo();

	if( is_user_logged_in() && in_array('administrator',$current_user->roles) ) {
		$alloptions =  $wpdb->query("DELETE FROM `$wpdb->options` WHERE option_name LIKE 'cforms%'");
		$wpdb->query("DROP TABLE IF EXISTS $wpdb->cformssubmissions");
		$wpdb->query("DROP TABLE IF EXISTS $wpdb->cformsdata");
	}

    ### deactivate cforms plugin
	$curPlugs = get_settings('active_plugins');
	array_splice($curPlugs, array_search( 'cforms', $curPlugs), 1 ); // Array-function!
	update_option('active_plugins', $curPlugs);
	header('Location: plugins.php?deactivate=true');

}


### backup/download cforms settings
$buffer='';
function cforms2_download(){
	global $buffer, $wpdb, $cformsSettings;
	$br="\n";

	if( isset($_REQUEST['savecformsdata']) || isset($_REQUEST['saveallcformsdata']) ) {

		if( isset($_REQUEST['savecformsdata']) ){
	        $noDISP = '1'; $no='';
	        if( $_REQUEST['noSub']<>'1' )
	            $noDISP = $no = $_REQUEST['noSub'];

	    	$buffer .= cforms2_save_array($cformsSettings['form'.$no]);
//	    	$buffer .= cforms2_save_array($cformsSettings['form'.$no]).$br;
			$filename = 'form-settings.txt';
		}else{
	    	$buffer .= cforms2_save_array($cformsSettings);
//	    	$buffer .= cforms2_save_array($cformsSettings).$br;
			$filename = 'all-cforms-settings.txt';
		}
        ob_end_clean();
		header('Pragma: public;');
		header('Expires: 0;');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0;');
		header('Content-Type: application/force-download;');
		header('Content-Type: application/octet-stream;');
		header('Content-Type: application/download;');
		header('Content-Disposition: attachment; filename="'.$filename.'";');
		header('Content-Transfer-Encoding: binary;');
		//header('Content-Length: ' .(string)(strlen($buffer)) . ';' );   // causes FF corrupt page issue
        flush();
		print $buffer;
		exit(0);
	}
}

### backup/download cforms settings :: save the array
function cforms2_save_array($vArray){
	global $buffer;
    // Every array starts with chr(1)+"{"
    $buffer .=  "\0{";

    // Go through the given array
    reset($vArray);
    while (true)
    {
        $Current = current($vArray);
        $MyKey = addslashes(strval( key($vArray) ));
        if (is_array($Current)) {
            $buffer .= $MyKey."\0";
            cforms2_save_array($Current);
            $buffer .= "\0";
        } else {
            $Current = addslashes($Current);
            $buffer .= "$MyKey\0$Current\0";
        }

        ++$i;

        while ( next($vArray)===false )
            if (++$i > count($vArray)) break;

        if ($i > count($vArray)) break;
    }
    $buffer .= "\0}";
}



### check user access
function cforms2_check_access_priv($r='manage_cforms'){
	if( !current_user_can($r) ){
		$err = '<div class="wrap"><div id="icon-cforms-error" class="icon32"><br/></div><h2>'.__('cforms error','cforms').'</h2><div class="updated fade" id="message"><p>'.__('You do not have the proper privileges to access this page.','cforms').'</p></div></div>';
		die( $err );
    }
}



### add cforms menu
function cforms2_menu() {
	global $wpdb, $submenu;

	$cformsSettings = get_option('cforms_settings');
    $p = dirname(plugin_basename(__FILE__));

	$tablesup = ($wpdb->get_var("show tables like '$wpdb->cformssubmissions'") == $wpdb->cformssubmissions)?true:false;

	$o = $p.'/cforms-options.php';

    if (function_exists('add_menu_page')) {
		add_menu_page(__('cformsII', 'cforms'), __('cformsII', 'cforms'), 'manage_cforms', $o, '', plugin_dir_url(__FILE__).'images/cformsicon.png');
	}
	elseif (function_exists('add_management_page')) {
		add_management_page(__('cformsII', 'cforms'), __('cformsII', 'cforms'), 'manage_cforms', $o);
	}

	if (function_exists('add_submenu_page')) {
		add_submenu_page($o, __('Form Settings', 'cforms'), __('Form Settings', 'cforms'), 'manage_cforms', $o);
		add_submenu_page($o, __('Global Settings', 'cforms'), __('Global Settings', 'cforms'), 'manage_cforms', $p.'/cforms-global-settings.php');
		if ( ($tablesup || isset($_REQUEST['cforms_database'])) && !isset($_REQUEST['deletetables']) )
			add_submenu_page($o, __('Tracking', 'cforms'), __('Tracking', 'cforms'), 'track_cforms', $p.'/cforms-database.php');
		add_submenu_page($o, __('Styling', 'cforms'), __('Styling', 'cforms'), 'manage_cforms', $p.'/cforms-css.php');
		add_submenu_page($o, __('Help!', 'cforms'), __('Help!', 'cforms'), 'manage_cforms', $p.'/cforms-help.php');
	}
}



### cforms init
function cforms2_init() {
	global $wpdb;

	$plugindir   = basename(dirname(__FILE__));
	$sep = strpos(dirname(__FILE__), '\\') !==false ? '\\' : '/';

	$role = get_role('administrator');
	if(!$role->has_cap('manage_cforms')) {
		$role->add_cap('manage_cforms');
	}
	if(!$role->has_cap('track_cforms')) {
		$role->add_cap('track_cforms');
	}

}


### get current page
function cforms2_get_request_uri() {
	$request_uri = $_SERVER['REQUEST_URI'];
	if ( !isset($_SERVER['REQUEST_URI']) || (strpos($_SERVER['SERVER_SOFTWARE'],'IIS')!==false && strpos($_SERVER['REQUEST_URI'],'wp-admin')===false) ){
	    if(isset($_SERVER['SCRIPT_NAME']))
	        $request_uri = $_SERVER['SCRIPT_NAME'];
	    else
	        $request_uri = $_SERVER['PHP_SELF'];
	}
	return $request_uri;
}


### cforms JS scripts
function cforms2_scripts() {
	global $wp_scripts, $localversion;

	### get options
	$cformsSettings = get_option('cforms_settings');
	$r=plugin_dir_url(__FILE__);

	### Add admin styles
	wp_register_style('cforms-admin-style', $r . 'cforms-admin.css' );
	wp_enqueue_style('cforms-admin-style'); 

	if ( strpos(cforms2_get_request_uri(),'cforms-options')!==false ){
		wp_register_style('calendar-style', $r . 'styling/calendar.css' );
		wp_enqueue_style('calendar-style'); 
		
		wp_enqueue_script('jquery');
	    wp_enqueue_script('jquery-ui-core');

	    wp_register_script('cforms_admin_cal',$r.'js/cformsadmincal.js',false,$localversion);
	    wp_enqueue_script('cforms_admin_cal');
	}

    wp_register_script('cforms_interface',$r.'js/interface.js',false,$localversion);
    wp_register_script('cforms_admin',$r.'js/cformsadmin.js',false,$localversion);
    wp_localize_script('cforms_admin', 'cforms2_nonces', array(
        'installpreset' => wp_create_nonce('cforms2_installpreset'),

		'checkbox'      => wp_create_nonce('cforms2_field_checkbox'),
		'checkboxgroup' => wp_create_nonce('cforms2_field_checkboxgroup'),
		'fieldsetstart' => wp_create_nonce('cforms2_field_fieldsetstart'),
		'html5field'    => wp_create_nonce('cforms2_field_html5field'),
		'selectbox'     => wp_create_nonce('cforms2_field_selectbox'),
		'textfield'     => wp_create_nonce('cforms2_field_textfield'),
		'textonly'      => wp_create_nonce('cforms2_field_textonly'),

        'deleteentries' => wp_create_nonce('database_deleteentries'),
		'deleteentry'   => wp_create_nonce('database_deleteentry'),
		'dlentries'     => wp_create_nonce('database_dlentries'),
		'getentries'    => wp_create_nonce('database_getentries'),
		'savedata'      => wp_create_nonce('database_savedata')
    ) );

    wp_enqueue_script('cforms_interface');
    wp_enqueue_script('cforms_admin');
}



### some css for arranging the table fields in wp-admin
function cforms2_options_page_style() {

	global $localversion;
	$cformsSettings = get_option('cforms_settings');
	$nav = $cformsSettings['global']['cforms_dp_nav'];

	echo "\n<!-- Start of script generated by cformsII v".$localversion." -->\n";
    echo '<script type="text/javascript">'."\n/* <![CDATA[ */\n".
		'var cfCAL={};'."\n".
		'cfCAL.dayNames = ['.stripslashes($cformsSettings['global']['cforms_dp_days']).'];'."\n".
		'cfCAL.abbrDayNames = ['.stripslashes($cformsSettings['global']['cforms_dp_days']).'];'."\n".
		'cfCAL.monthNames = ['.stripslashes($cformsSettings['global']['cforms_dp_months']).'];'."\n".
		'cfCAL.abbrMonthNames = ['.stripslashes($cformsSettings['global']['cforms_dp_months']).'];'."\n".
		'cfCAL.firstDayOfWeek = "'.stripslashes($cformsSettings['global']['cforms_dp_start']).'";'."\n".
		'cfCAL.fullYearStart = "20";'."\n".
		'cfCAL.TEXT_PREV_YEAR="'.stripslashes($nav[0]).'";'."\n". // not needed with 3.3
		'cfCAL.TEXT_NEXT_YEAR="'.stripslashes($nav[2]).'";'."\n". // not needed with 3.3
		'cfCAL.TEXT_PREV_MONTH="'.stripslashes($nav[1]).'";'."\n".
		'cfCAL.TEXT_NEXT_MONTH="'.stripslashes($nav[3]).'";'."\n".
		'cfCAL.TEXT_CLOSE="'.stripslashes($nav[4]).'";'."\n".
		'cfCAL.TEXT_CHOOSE_DATE="'.stripslashes($nav[5]).'";'."\n". 
		'cfCAL.changeYear='. ($nav[6]==1? 'true':'false') .';'."\n". 
		'cfCAL.ROOT="'.plugin_dir_url( __FILE__ ).'";' ."\n\n"; 
?>
jQuery(function() {

if( jQuery(".cf_timebutt1").length>0 && jQuery(".cf_timebutt2").length>0 ){
    jQuery(".cf_timebutt1").clockpick({military:true, layout:'horizontal', starthour : 0,endhour : 23,showminutes : true, valuefield : 'cforms_starttime' });
    jQuery(".cf_timebutt2").clockpick({military:true, layout:'horizontal', starthour : 0,endhour : 23,showminutes : true, valuefield : 'cforms_endtime' });
}

if( jQuery(".cf_date").length>0 ){

	jQuery(".cf_date").datepicker({
			"buttonImage": cfCAL.ROOT+"js/calendar.gif", changeYear: cfCAL.changeYear, buttonImageOnly: true, buttonText: cfCAL.TEXT_CHOOSE_DATE, showOn: "both",
			"dateFormat": "dd/mm/yy", "dayNamesMin": cfCAL.dayNames, "dayNamesShort": cfCAL.dayNames, "monthNames": cfCAL.monthNames, "firstDay":cfCAL.firstDayOfWeek,
			"nextText": cfCAL.TEXT_NEXT_MONTH, "prevText": cfCAL.TEXT_PREV_MONTH, "closeText": cfCAL.TEXT_CLOSE });

    jQuery('#cforms_startdate').bind(
        'dpClosed',
        function(e, selectedDates)
        {
            var d = selectedDates[0];
            if (d) {
                d = new Date(d);
                jQuery('#cforms_enddate').dpSetStartDate(d.asString());
            }
        }
    );
    jQuery('#cforms_enddate').bind(
        'dpClosed',
        function(e, selectedDates)
        {
            var d = selectedDates[0];
            if (d) {
                d = new Date(d);
                jQuery('#cforms_startdate').dpSetEndDate(d.asString());
            }
        }
    );

}
});
<?php
	echo  "/* ]]> */\n".'</script>'."\n";
	echo '<!-- End Of Script Generated By cformsII -->'."\n\n";
}



### footer
function cforms2_footer() {
	global $localversion;
?>	<p style="padding-top:50px; font-size:11px; text-align:center;">
		<em>
			<?php echo sprintf(__('For more information and support, visit the <strong>cforms</strong> %s support forum %s. ', 'cforms'),'<a href="http://www.deliciousdays.com/cforms-forum/" title="cforms support forum">','</a>') ?>
			<?php _e('Translation provided by Oliver Seidel, for updates <a href="http://deliciousdays.com/cforms-plugin">check here.</a>', 'cforms') ?>
		</em>
	</p>
	<p align="center">Version v<?php echo $localversion; ?></p>
<?php
}



### plugin uninstalled?
function cforms2_check_erased() {
	global $cformsSettings;
    if ( $cformsSettings['global']['cforms_formcount'] == '' ){
		?>
		<div class="wrap">
		<div id="icon-cforms-global" class="icon32"><br/></div><h2><?php _e('All cforms data has been erased!', 'cforms') ?></h2>
	    <p class="ex" style="padding:5px 35px 10px 41px;"><?php _e('Please go to your <strong>Plugins</strong> tab and either disable the plugin, or toggle its status (disable/enable) to revive cforms!', 'cforms') ?></p>
	    <p class="ex" style="padding:5px 35px 10px 41px;"><?php _e('In case disabling/enabling doesn\'t seem to properly set the plugin defaults, try login out and back in and <strong>don\'t select the checkbox for activation</strong> on the plugin page.', 'cforms') ?></p>
	    </div>
		<?php
	    return true;
	}
	return false;
}

### add menu items to admin bar
function cforms2_add_admin_bar_root($admin_bar, $id, $ti){
	$arr = array(	'id' => $id, 
					'title' => $ti, 
					'href'  => false 
				);
	$admin_bar->add_node( $arr );
}

function cforms2_add_admin_bar_item($admin_bar, $id,$ti,$hi,$ev,$p = 'cforms-bar'){
	$arr = array(	'parent' => $p, 
					'id' => $id, 
					'title' => $ti, 
					'href'  => '#', 
					'meta'  => array(	'title'  => $hi, 
										'onclick'  => $ev )
				);
	
	$admin_bar->add_node( $arr );
}


### get_magic_quotes_gpc() workaround
if ( !function_exists('get_magic_quotes_gpc') ) {
	function get_magic_quotes_gpc(){
		return false;
	}
}
function cforms2_magic($v){
	global $wp_version;
	$vercomp = (version_compare(strval($wp_version), strval('2.9'), '>='));
	return ( get_magic_quotes_gpc() || $vercomp ) ? $v : addslashes($v);
}
