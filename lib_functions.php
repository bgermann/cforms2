<?php
$buffer='';
function download_cforms() {

	global $buffer, $wpdb, $cformsSettings;
	$br="\n";

	if( isset($_REQUEST['savecformsdata']) || isset($_REQUEST['saveallcformsdata']) ) {

		if( isset($_REQUEST['savecformsdata']) ){
	        $noDISP = '1'; $no='';
	        if( $_REQUEST['noSub']<>'1' )
	            $noDISP = $no = $_REQUEST['noSub'];

	    	$buffer .= SaveArray($cformsSettings['form'.$no]).$br;
			$filename = 'form-settings.txt';
		}else{
	    	$buffer .= SaveArray($cformsSettings).$br;
			$filename = 'all-cforms-settings.txt';
		}

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " .(string)(strlen($buffer)) );
		print $buffer;
		exit();

	} ### saving form settings

}

### Save the array
function SaveArray($vArray)
{
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
            SaveArray($Current);
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


### admin message
function check_access_priv($r='manage_cforms'){
	if( !current_user_can($r) ){
		$err = '<div class="wrap"><div id="icon-cforms-error" class="icon32"><br/></div><h2>'.__('cforms error','cforms').'</h2><div class="updated fade" id="message"><p>'.__('You do not have the proper privileges to access this page.','cforms').'</p></div></div>';
		die( $err );
    }
}


### Add cforms menu to admin
function cforms_menu() {
	global $wpdb, $submenu;

	$cformsSettings = get_option('cforms_settings');

	$tablesup = ($wpdb->get_var("show tables like '$wpdb->cformssubmissions'") == $wpdb->cformssubmissions)?true:false;

	$optfile = $cformsSettings['global']['plugindir'].'/cforms-options.php';

    if (function_exists('add_menu_page')) {
		add_menu_page(__('cformsII', 'cforms'), __('cformsII', 'cforms'), 'manage_cforms', $optfile, '', $cformsSettings['global']['cforms_root'].'/images/cformsicon.gif');
	}
	elseif (function_exists('add_management_page')) {
		add_management_page(__('cformsII', 'cforms'), __('cformsII', 'cforms'), 'manage_cforms', $optfile);
	}

	if (function_exists('add_submenu_page')) {
		add_submenu_page($optfile, __('Form Settings', 'cforms'), __('Form Settings', 'cforms'), 'manage_cforms', $optfile);
		add_submenu_page($optfile, __('Global Settings', 'cforms'), __('Global Settings', 'cforms'), 'manage_cforms', $cformsSettings['global']['plugindir'].'/cforms-global-settings.php');
		if ( ($tablesup || isset($_REQUEST['cforms_database'])) && !isset($_REQUEST['deletetables']) )
			add_submenu_page($optfile, __('Tracking', 'cforms'), __('Tracking', 'cforms'), 'track_cforms', $cformsSettings['global']['plugindir'].'/cforms-database.php');
		add_submenu_page($optfile, __('Styling', 'cforms'), __('Styling', 'cforms'), 'manage_cforms', $cformsSettings['global']['plugindir'].'/cforms-css.php');
		add_submenu_page($optfile, __('Help!', 'cforms'), __('Help!', 'cforms'), 'manage_cforms', $cformsSettings['global']['plugindir'].'/cforms-help.php');
	}
}

function cforms_init() {
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

	### alter tracking tables if needed
	$tables = $wpdb->get_col("SHOW TABLES FROM `" . DB_NAME . "` LIKE '$wpdb->cformssubmissions'",0);

	if( $tables[0]==$wpdb->cformssubmissions ) {
		$columns = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->cformssubmissions}");
		if ( $columns[2]->Field == 'date' )
			$result = $wpdb->query("ALTER TABLE `{$wpdb->cformssubmissions}` CHANGE `date` `sub_date` TIMESTAMP");
	}

	### try to adjust cforms.js automatically
	$jsContent = $jsContentNew = '';
	if ( $fhandle = fopen(dirname(__FILE__).'/js/cforms.js', "r") ) {
		$jsContent = fread($fhandle, filesize(dirname(__FILE__).'/js/cforms.js'));
	    fclose($fhandle);

		$URIprefix = get_option('siteurl');
		$pathToAjax = $URIprefix . '/wp-content/plugins/cforms/lib_ajax.php';

        if ( defined('WP_CONTENT_URL') )
			$pathToAjax = $URIprefix.'/'.WP_CONTENT_URL.'/plugins/'.$plugindir. '/lib_ajax.php';

        if ( defined('WP_PLUGIN_URL') )
			$pathToAjax = $URIprefix.'/'.WP_PLUGIN_URL.'/'.$plugindir. '/lib_ajax.php';

        if ( defined('PLUGINDIR') )
			$pathToAjax = $URIprefix.'/'.PLUGINDIR.'/'.$plugindir. '/lib_ajax.php';

       	$jsContentNew = str_replace('\'/wp-content/plugins/cforms/lib_ajax.php\'',"'{$pathToAjax}'",$jsContent);
	}
	if ( $jsContentNew<>'' && $jsContentNew<>$jsContent && ($fhandle = fopen(dirname(__FILE__).$sep.'js'.$sep.'cforms.js', "w")) ) {
	    fwrite($fhandle, $jsContentNew);
	    fclose($fhandle);
	}

	### save ABSPATH for ajax routines
	if ( defined('ABSPATH') && ($fhandle = fopen(dirname(__FILE__).$sep.'abspath.php', "w")) ) {
	    fwrite($fhandle, "<?php \$abspath = '". addslashes(ABSPATH) ."'; ?>\n");
	    fclose($fhandle);
	}

}

### check for abspath.php
function abspath_check() {
	global $cformsSettings;
	if ( !file_exists( dirname(__FILE__).$cformsSettings['global']['cforms_IIS'].'abspath.php' ) ){
    	echo '<div class="updated fade"><p>'.
        	__('It appears that cforms was not able to create <strong>abspath.php</strong> in your cforms plugin folder. Please check file/folder permissions (plugins/cforms), then <strong>re-activate</strong> cforms.', 'cforms').
            '</p><p>'.
            __('If the problem persists, please create a file (using your preferred text editor) manually with the following content:', 'cforms').
            '<p><code>&lt;?php $abspath = \''.addslashes(ABSPATH).'\'; ?&gt;</code></p>'.
            '<p>'.__('Save the file as abspath.php and ftp to your cforms folder.', 'cforms').'</p></div>';
        }
}

### get WP plugin dir
function get_cf_plugindir(){
	$cr = defined('PLUGINDIR') ? get_option('siteurl') .'/'. PLUGINDIR . '/' : get_option('siteurl') . '/wp-content/plugins/';
	$cr = defined('WP_CONTENT_URL') ? WP_CONTENT_URL.'/plugins/' : $cr;
	$cr = defined('WP_PLUGIN_URL') ? WP_PLUGIN_URL .'/' : $cr;
	return $cr;
}

### cforms JS scripts
function cforms_scripts() {

	global $wp_scripts, $localversion;

	### get options
	$cformsSettings = get_option('cforms_settings');

	### global settings
	$request_uri = get_request_uri();

    if ( version_compare(strval($wp_scripts->registered['jquery']->ver), strval("1.2.6") ) === -1 ){
		wp_deregister_script('jquery');
	    wp_register_script('jquery',$cformsSettings['global']['cforms_root'].'/js/jquery.js',false,'1.2.6');
    	wp_enqueue_script('jquery');
    }

   	echo '<link rel="stylesheet" type="text/css" href="' . $cformsSettings['global']['cforms_root'] . '/cforms-admin.css" />' . "\n";

	if ( strpos($request_uri,'cforms-options')!==false ){
		echo '<link rel="stylesheet" type="text/css" href="' . $cformsSettings['global']['cforms_root'] . '/styling/calendar.css" />' . "\n";
	    wp_register_script('cforms_admin_cal',$cformsSettings['global']['cforms_root'].'/js/cformsadmincal.js',false,$localversion);
	    wp_enqueue_script('cforms_admin_cal');
	}

    wp_deregister_script('prototype');

    wp_register_script('cforms_interface',$cformsSettings['global']['cforms_root'].'/js/interface.js',false,$localversion);
    wp_register_script('cforms_admin',$cformsSettings['global']['cforms_root'].'/js/cformsadmin.js',false,$localversion);

    wp_enqueue_script('cforms_interface');
    wp_enqueue_script('cforms_admin');
}


### some css for arranging the table fields in wp-admin
function cforms_options_page_style() {

	global $localversion;
	$cformsSettings = get_option('cforms_settings');
	$nav = $cformsSettings['global']['cforms_dp_nav'];

	echo "\n<!-- Start Of Script Generated By cforms v".$localversion." [Oliver Seidel | www.deliciousdays.com] -->\n";
    echo '<script type="text/javascript">'."\n/* <![CDATA[ */\n".
         "\t".'Date.dayNames = ['.stripslashes($cformsSettings['global']['cforms_dp_days']).'];'."\n".
         "\t".'Date.abbrDayNames = ['.stripslashes($cformsSettings['global']['cforms_dp_days']).'];'."\n".
         "\t".'Date.monthNames = ['.stripslashes($cformsSettings['global']['cforms_dp_months']).'];'."\n".
         "\t".'Date.abbrMonthNames = ['.stripslashes($cformsSettings['global']['cforms_dp_months']).'];'."\n".
         "\t".'Date.firstDayOfWeek = 0;'."\n".
         "\t".''."\n".
         "\t".'Date.fullYearStart = "20";'."\n".
         "\t".'cforms.dpText = { TEXT_PREV_YEAR:"'.stripslashes($nav[0]).'",'. ### Previous year
         'TEXT_PREV_MONTH:"'.stripslashes($nav[1]).'",'.
         'TEXT_NEXT_YEAR:"'.stripslashes($nav[2]).'",'.
         'TEXT_NEXT_MONTH:"'.stripslashes($nav[3]).'",'.
         'TEXT_CLOSE:"'.stripslashes($nav[4]).'",'.
         'TEXT_CHOOSE_DATE:"'.stripslashes($nav[5]).'",'.
         'ROOT:"'.$cformsSettings['global']['cforms_root'].'"};'."\n";
?>
	cforms(function() {

    if( cforms(".cf_timebutt1").length>0 && cforms(".cf_timebutt2").length>0 ){
	    cforms(".cf_timebutt1").clockpick({military:true, layout:'horizontal', starthour : 0,endhour : 23,showminutes : true, valuefield : 'cforms_starttime' });
    	cforms(".cf_timebutt2").clockpick({military:true, layout:'horizontal', starthour : 0,endhour : 23,showminutes : true, valuefield : 'cforms_endtime' });
	}

    if( cforms(".cf_date").length>0 ){
		Date.format = "dd/mm/yyyy";
		cforms(".cf_date").datePicker( );

	    cforms('#cforms_startdate').bind(
	        'dpClosed',
	        function(e, selectedDates)
	        {
	            var d = selectedDates[0];
	            if (d) {
	                d = new Date(d);
	                cforms('#cforms_enddate').dpSetStartDate(d.asString());
	            }
	        }
	    );
	    cforms('#cforms_enddate').bind(
	        'dpClosed',
	        function(e, selectedDates)
	        {
	            var d = selectedDates[0];
	            if (d) {
	                d = new Date(d);
	                cforms('#cforms_startdate').dpSetEndDate(d.asString());
	            }
	        }
	    );
	}

	});
<?php
	echo  "\n/* ]]> */\n".'</script>'."\n";
	echo '<!-- End Of Script Generated By cforms -->'."\n\n";
}

### footer unbder all options pages
function cforms_footer() {
	global $localversion;
?>	<p style="padding-top:50px; font-size:11px; text-align:center;">
		<em>
			<?php echo sprintf(__('For more information and support, visit the %s support forum %s. ', 'cforms'),'<strong>cforms</strong> <a href="http://www.deliciousdays.com/cforms-forum/" title="cforms support forum">','</a>') ?>
			<?php _e('Translation provided by Oliver Seidel, for updates <a href="http://deliciousdays.com/cforms-plugin">check here.</a>', 'cforms') ?>
		</em>
	</p>

	<p align="center">Version v<?php echo $localversion; ?></p>
<?php
}

### if all data has been erased quit
function check_erased() {
	global $cformsSettings;
    if ( $cformsSettings['global']['cforms_formcount'] == '' ){
		?>
	    <div class="wrap">
	    <h2><?php _e('All cforms data has been erased!', 'cforms') ?></h2>
	    <p class="ex"><?php _e('Please go to your <strong>Plugins</strong> tab and either disable the plugin, or toggle its status (disable/enable) to revive cforms!', 'cforms') ?></p>
	    <p class="ex"><?php _e('In case disabling/enabling doesn\'t seem to properly set the plugin defaults, try login out and back in and <strong>don\'t select the checkbox for activation</strong> on the plugin page.', 'cforms') ?></p>
	    </div>
		<?php
	    return true;
	}
	return false;
}
?>