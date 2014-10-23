<?php


### plugin removal
function cforms2_delete_db_and_deactivate () {
    if( !isset($_POST['cfdeleteall']))
        return;

    if( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
        define( 'WP_UNINSTALL_PLUGIN', true );
        require_once(plugin_dir_path(__FILE__) . 'uninstall.php');

        ### deactivate cforms plugin
        $curPlugs = get_option('active_plugins');
        array_splice($curPlugs, array_search( 'cforms2', $curPlugs), 1 ); // Array-function!
        update_option('active_plugins', $curPlugs);
        header('Location: plugins.php?deactivate=true');
        die();
    }

}

### backup/download cforms settings
$buffer='';
function cforms2_download(){
	global $buffer, $cformsSettings;

	if( isset($_REQUEST['savecformsdata']) || isset($_REQUEST['saveallcformsdata']) ) {

		if( isset($_REQUEST['savecformsdata']) ){
	        $noDISP = '1'; $no='';
	        if( $_REQUEST['noSub']<>'1' )
	            $noDISP = $no = $_REQUEST['noSub'];

	    	$buffer .= cforms2_save_array($cformsSettings['form'.$no]);
			$filename = 'form-settings.txt';
		}else{
	    	$buffer .= cforms2_save_array($cformsSettings);
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

        while ( next($vArray)===false ) {
            if (++$i > count($vArray)) break;
        }

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
	global $wpdb;

    $p = plugin_dir_path(plugin_basename(__FILE__));

	$tablesup = ($wpdb->get_var("show tables like '$wpdb->cformssubmissions'") == $wpdb->cformssubmissions)?true:false;

	$o = $p.'cforms-options.php';

    if (function_exists('add_menu_page')) {
		add_menu_page(__('cformsII', 'cforms'), __('cformsII', 'cforms'), 'manage_cforms', $o, '', plugin_dir_url(__FILE__).'images/cformsicon.png');
	}
	elseif (function_exists('add_management_page')) {
		add_management_page(__('cformsII', 'cforms'), __('cformsII', 'cforms'), 'manage_cforms', $o);
	}

	if (function_exists('add_submenu_page')) {
		add_submenu_page($o, __('Form Settings', 'cforms'), __('Form Settings', 'cforms'), 'manage_cforms', $o);
		add_submenu_page($o, __('Global Settings', 'cforms'), __('Global Settings', 'cforms'), 'manage_cforms', $p.'cforms-global-settings.php');
		if ( ($tablesup || isset($_REQUEST['cforms_database'])) && !isset($_REQUEST['deletetables']) )
			add_submenu_page($o, __('Tracking', 'cforms'), __('Tracking', 'cforms'), 'track_cforms', $p.'cforms-database.php');
		add_submenu_page($o, __('Styling', 'cforms'), __('Styling', 'cforms'), 'manage_cforms', $p.'cforms-css.php');
		add_submenu_page($o, __('Help!', 'cforms'), __('Help!', 'cforms'), 'manage_cforms', $p.'cforms-help.php');
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


function cforms2_enqueue_script_datepicker($localversion) {
    $cformsSettings = get_option('cforms_settings');
	$nav = $cformsSettings['global']['cforms_dp_nav'];

    wp_register_script('cforms-calendar', plugin_dir_url(__FILE__) . 'js/cforms.calendar.js', array('jquery', 'jquery-ui-datepicker'), $localversion);
    wp_localize_script('cforms-calendar', 'cfCAL', array(
        'dateFormat'       => stripslashes($cformsSettings['global']['cforms_dp_date']),
        'dayNames'         => explode( ',', stripslashes($cformsSettings['global']['cforms_dp_days']) ),
        'abbrDayNames'     => explode( ',', stripslashes($cformsSettings['global']['cforms_dp_days']) ),
        'monthNames'       => explode( ',', stripslashes($cformsSettings['global']['cforms_dp_months']) ),
        'abbrMonthNames'   => explode( ',', stripslashes($cformsSettings['global']['cforms_dp_months']) ),
        'firstDayOfWeek'   => stripslashes($cformsSettings['global']['cforms_dp_start']),
        'fullYearStart'    => '20',
        'TEXT_PREV_MONTH'  => stripslashes($nav[1]),
        'TEXT_NEXT_MONTH'  => stripslashes($nav[3]),
        'TEXT_CLOSE'       => stripslashes($nav[4]),
        'TEXT_CHOOSE_DATE' => stripslashes($nav[5]),
        'changeYear'       => $nav[6]==1,
        'ROOT'             => plugin_dir_url( __FILE__ ),
    ) );
    wp_enqueue_script('cforms-calendar');
    
    wp_register_style('jquery-ui-datepicker', plugin_dir_url(__FILE__) . 'styling/calendar.css', false, $localversion );
    wp_enqueue_style('jquery-ui-datepicker');
}

function cforms2_admin_enqueue_scripts() {
	global $localversion;

	$suffix = SCRIPT_DEBUG ? '' : '.min';
	$r=plugin_dir_url(__FILE__);

    wp_enqueue_style('wp-color-picker');

	wp_register_style('cforms-admin', $r . 'cforms-admin.css', false, $localversion );
	wp_enqueue_style('cforms-admin');

    wp_register_style('jquery-flexigrid', $r . 'js/css/flexigrid.css', false, '1.1' );
    wp_enqueue_style('jquery-flexigrid');

    wp_register_style('jquery-clockpick', $r . 'js/css/jquery.clockpick.css', false, '1.2.9' );
    wp_enqueue_style('jquery-clockpick');

    // The Sortables with their dependencies seem to be used.
    wp_register_script('jquery-interface',$r.'js/jquery.interface.js',array('jquery'));
    wp_enqueue_script('jquery-interface');

    wp_register_script('jquery-textarearesizer',$r.'js/jquery.textarearesizer.js',array('jquery'),'1.0.4');
    wp_enqueue_script('jquery-textarearesizer');

    wp_register_script('jquery-flexigrid',$r."js/jquery.flexigrid$suffix.js",array('jquery'),'1.1');
    wp_enqueue_script('jquery-flexigrid');

    wp_register_script('jquery-jqdnr',$r.'js/jquery.jqdnr.js',array('jquery'),'r2');
    wp_register_script('jquery-jqmodal',$r.'js/jquery.jqmodal.js',array('jquery', 'jquery-jqdnr'),'1.1.0');
    wp_register_script('jquery-clockpick',$r."js/jquery.clockpick$suffix.js",array('jquery'),'1.2.9');
    wp_register_script('jquery-in-place-editor',$r.'js/jquery.in-place-editor.js',array('jquery'),'2.3.0');
    wp_register_script('cforms-admin',$r.'js/cformsadmin.js',array('jquery', 'jquery-jqmodal', 'jquery-in-place-editor', 'jquery-clockpick', 'wp-color-picker'),$localversion);
    wp_localize_script('cforms-admin', 'cforms2_nonces', array(
        'installpreset' => wp_create_nonce('cforms2_installpreset'),
        'reset_captcha' => wp_create_nonce('cforms2_reset_captcha'),

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
    wp_enqueue_script('cforms-admin');

    cforms2_enqueue_script_datepicker($localversion);
}


### footer
function cforms2_footer() {
	global $localversion;
?>	<p style="padding-top:50px; font-size:11px; text-align:center;">
		<em>
			<?php echo sprintf(__('For more information and support, visit the <strong>cforms</strong> %s support forum %s. ', 'cforms'),'<a href="http://www.deliciousdays.com/cforms-forum/" title="cforms support forum">','</a>') ?>
			<?php _e('Translation provided by Oliver Seidel.', 'cforms') ?>
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
