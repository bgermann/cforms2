<?php
/*
 * Copyright (c) 2006-2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
 * Copyright (c) 2014-2017 Bastian Germann
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

function cforms2_localization() {
    load_plugin_textdomain('cforms2');

}

function cforms2_settings_corrupted() {
    $tmp = plugin_dir_path(__FILE__) . 'cforms-corrupted.php';

    add_menu_page(__('cformsII', 'cforms2'), __('cformsII', 'cforms2'), 'manage_cforms', $tmp, '', plugin_dir_url(__FILE__) . 'images/cformsicon.png');
    add_submenu_page($tmp, __('Corrupted Settings', 'cforms2'), __('Corrupted Settings', 'cforms2'), 'manage_cforms', $tmp);

    add_action('admin_enqueue_scripts', 'cforms2_enqueue_style_admin');

}

function cforms2_start_session() {
    session_cache_limiter('nocache');
    $session_id = session_id();
    if (empty($session_id)) {
        session_start();
    }

}

function cforms2_field() {
    check_admin_referer('cforms2_field');

    $type = $_POST['type'];
    $fields = cforms2_get_fieldtypes();
    if (array_key_exists($type, $fields))
        echo $fields[$type]->render_settings();

    die();

}

function cforms2_delete_db_and_deactivate() {
    if (!isset($_POST['cfdeleteall']))
        return;

    if (is_user_logged_in() && current_user_can('manage_options')) {
        define('WP_UNINSTALL_PLUGIN', true);
        require_once(plugin_dir_path(__FILE__) . 'uninstall.php');

        // deactivate cforms plugin
        $curPlugs = get_option('active_plugins');
        array_splice($curPlugs, array_search('cforms2', $curPlugs), 1); // Array-function!
        update_option('active_plugins', $curPlugs);
        header('Location: plugins.php?deactivate=true');
        die();
    }

}

/** check user access */
function cforms2_check_access_priv($r = 'manage_cforms') {
    if (!current_user_can($r)) {
        $err = '<h2>' . __('cforms error', 'cforms2') . '</h2><div class="updated fade" id="message"><p>' . __('You do not have the proper privileges to access this page.', 'cforms2') . '</p></div></div>';
        die($err);
    }

}

/** some css for positioning the form elements */
function cforms2_enqueue_scripts() {
    global $wp_query, $cformsSettings;

    // add content actions and filters
    $page_obj = $wp_query->get_queried_object();

    $exclude = ($cformsSettings['global']['cforms_inexclude']['ex'] == '1');
    $onPages = str_replace(' ', '', stripslashes(htmlspecialchars($cformsSettings['global']['cforms_inexclude']['ids'])));
    $onPagesA = explode(',', $onPages);

    if ($onPages == '' || (in_array($page_obj->ID, $onPagesA) && !$exclude) || (!in_array($page_obj->ID, $onPagesA) && $exclude)) {

        if ($cformsSettings['global']['cforms_no_css'] != '1') {
            wp_register_style('cforms2', plugin_dir_url(__FILE__) . 'styling/' . $cformsSettings['global']['cforms_css'], array(), CFORMS2_VERSION);
            wp_enqueue_style('cforms2');
        }

        // add calendar
        if ($cformsSettings['global']['cforms_datepicker'] == '1') {
            cforms2_enqueue_script_datepicker(stripslashes($cformsSettings['global']['cforms_dp_date']));
        }
        wp_register_script('cforms2', plugin_dir_url(__FILE__) . 'js/cforms.js', array('jquery'), CFORMS2_VERSION);
        wp_localize_script('cforms2', 'cforms2_ajax', array(
            'url' => admin_url('admin-ajax.php'),
            'nonces' => array(
                'submitcform' => wp_create_nonce('submitcform')
            )
        ));
        wp_enqueue_script('cforms2');
    }

}

function cforms2_is_table_present() {
    global $wpdb;
    $table_name = "{$wpdb->prefix}cformssubmissions";
    return $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;

}

/** add cforms menu */
function cforms2_menu() {
    global $wpdb;

    $p = plugin_dir_path(plugin_basename(__FILE__));
    $o = $p . 'cforms-options.php';

    add_menu_page(__('cformsII', 'cforms2'), __('cformsII', 'cforms2'), 'manage_cforms', $o, '', plugin_dir_url(__FILE__) . 'images/cformsicon.png');

    add_submenu_page($o, __('Form Settings', 'cforms2'), __('Form Settings', 'cforms2'), 'manage_cforms', $o);
    add_submenu_page($o, __('Global Settings', 'cforms2'), __('Global Settings', 'cforms2'), 'manage_cforms', $p . 'cforms-global-settings.php');
    if (cforms2_is_table_present() && !isset($_POST['deletetables']))
        add_submenu_page($o, __('Tracking', 'cforms2'), __('Tracking', 'cforms2'), 'track_cforms', $p . 'cforms-database.php');
    add_submenu_page($o, __('Styling', 'cforms2'), __('Styling', 'cforms2'), 'manage_cforms', $p . 'cforms-css.php');
    add_submenu_page($o, __('Help!', 'cforms2'), __('Help!', 'cforms2'), 'manage_cforms', $p . 'cforms-help.php');

}

/** get current page */
function cforms2_get_request_uri() {
    $request_uri = $_SERVER['REQUEST_URI'];
    if (!isset($_SERVER['REQUEST_URI']) || (strpos($_SERVER['SERVER_SOFTWARE'], 'IIS') !== false && strpos($_SERVER['REQUEST_URI'], 'wp-admin') === false)) {
        if (isset($_SERVER['SCRIPT_NAME']))
            $request_uri = $_SERVER['SCRIPT_NAME'];
        else
            $request_uri = $_SERVER['PHP_SELF'];
    }
    return $request_uri;

}

function cforms2_enqueue_script_datepicker($date_format) {
    global $wp_scripts;
    $suffix = SCRIPT_DEBUG ? '' : '.min';

    $cformsSettings = get_option('cforms_settings');

    wp_register_script('cforms-calendar', plugin_dir_url(__FILE__) . 'js/cforms.calendar.js', array('jquery', 'jquery-ui-datepicker'), CFORMS2_VERSION);
    $day_names = explode(',', __('S,M,T,W,T,F,S', 'cforms2'));
    $month_names = array(
        __("January", 'cforms2'),
        __("February", 'cforms2'),
        __("March", 'cforms2'),
        __("April", 'cforms2'),
        __("May", 'cforms2'),
        __("June", 'cforms2'),
        __("July", 'cforms2'),
        __("August", 'cforms2'),
        __("September", 'cforms2'),
        __("October", 'cforms2'),
        __("November", 'cforms2'),
        __("December", 'cforms2')
    );
    $dp_options = array_merge(array(
        'buttonImageOnly' => true,
        'showOn' => 'both',
        'dayNamesMin' => $day_names,
        'dayNamesShort' => $day_names,
        'monthNames' => $month_names,
        'firstDay' => get_option('start_of_week'),
        'prevText' => __('Previous Month', 'cforms2'),
        'nextText' => __('Next Month', 'cforms2'),
        'closeText' => __('Close', 'cforms2'),
        'buttonText' => __('Choose Date', 'cforms2'),
        'buttonImage' => plugin_dir_url(__FILE__) . 'images/calendar.png'
            ), $cformsSettings['global']['cforms_dp_nav']);
    $dp_options['dateFormat'] = $date_format;
    wp_localize_script('cforms-calendar', 'cforms2_cal', $dp_options);
    wp_enqueue_script('cforms-calendar');

    $jqui = $wp_scripts->query('jquery-ui-datepicker');
    $theme = $cformsSettings['global']['cforms_jqueryuitheme'];
    if (empty($theme)) {
        $theme = 'smoothness';
    }
    wp_register_style('jquery-ui-theme', "https://ajax.googleapis.com/ajax/libs/jqueryui/$jqui->ver/themes/$theme/jquery-ui$suffix.css", false, $jqui->ver);
    wp_enqueue_style('jquery-ui-theme');

}

function cforms2_enqueue_style_admin() {
    wp_register_style('cforms-admin', plugin_dir_url(__FILE__) . 'cforms-admin.css', false, CFORMS2_VERSION);
    wp_enqueue_style('cforms-admin');

}

function cforms2_admin_enqueue_scripts() {

    $r = plugin_dir_url(__FILE__);

    wp_register_style('jquery-flexigrid', $r . 'js/css/flexigrid.css', false, '1.1');
    wp_enqueue_style('jquery-flexigrid');

    wp_register_script('jquery-flexigrid', $r . "js/jquery.flexigrid.js", array('jquery'), '1.1');
    wp_enqueue_script('jquery-flexigrid');

    wp_register_script('cforms-admin', $r . 'js/cforms.admin.js', array(
        'jquery', 'jquery-ui-button', 'jquery-ui-dialog', 'jquery-ui-draggable', 'jquery-ui-sortable'
            ), CFORMS2_VERSION);
    wp_localize_script('cforms-admin', 'cforms2_nonces', array(
        'cforms2_field' => wp_create_nonce('cforms2_field'),
        'deleteentry' => wp_create_nonce('database_deleteentry'),
        'getentries' => wp_create_nonce('database_getentries')
    ));
    wp_localize_script('cforms-admin', 'cforms2_i18n', array(
        'OK' => __('OK', 'cforms2'),
        'Cancel' => __('Cancel', 'cforms2')
    ));
    wp_enqueue_script('cforms-admin');

    cforms2_enqueue_style_admin();
    cforms2_enqueue_script_datepicker('dd/mm/yy');

}

function cforms2_footer() {
    echo '<p style="padding-top:50px; font-size:11px; text-align:center;"><em>'
    . sprintf(__('For more information and support, visit the <strong>cforms</strong> %s support forum %s. ', 'cforms2'), '<a href="http://wordpress.org/support/plugin/cforms2" title="cforms support forum">', '</a>')
    . '</em><br />Version ' . CFORMS2_VERSION . '</p>';

}

/** plugin uninstalled? */
function cforms2_check_erased() {
    global $cformsSettings;
    if ($cformsSettings['global']['cforms_formcount'] == '') {
        echo '<div class="wrap"><h2>'
        . __('All cforms data has been erased!', 'cforms2')
        . '</h2>'
        . '<p class="ex" style="padding:5px 35px 10px 41px;">' . __('Please go to your <strong>Plugins</strong> tab and either disable the plugin, or toggle its status (disable/enable) to revive cforms!', 'cforms2') . '</p>'
        . '<p class="ex" style="padding:5px 35px 10px 41px;">' . __('In case disabling/enabling doesn\'t seem to properly set the plugin defaults, try login out and back in and <strong>don\'t select the checkbox for activation</strong> on the plugin page.', 'cforms2') . '</p>'
        . '</div>';
        return true;
    }
    return false;

}

/** add menu items to admin bar */
function cforms2_add_admin_bar_root($admin_bar, $id, $ti) {
    $arr = array('id' => $id,
        'title' => $ti,
        'href' => false
    );
    $admin_bar->add_node($arr);

}

function cforms2_add_admin_bar_item($admin_bar, $id, $ti, $hi, $p = 'cforms-bar') {
    $arr = array('parent' => $p,
        'id' => $id,
        'title' => $ti,
        'href' => '#',
        'meta' => array('title' => $hi)
    );

    $admin_bar->add_node($arr);

}

function cforms2_add_items_global($admin_bar) {

    global $wpdb;

    cforms2_add_admin_bar_root($admin_bar, 'cforms-bar', 'cforms Admin');

    cforms2_add_admin_bar_item($admin_bar, 'cforms-showinfo', __('Produce debug output', 'cforms2'), __('Outputs -for debug purposes- all cforms settings', 'cforms2'));
    cforms2_add_admin_bar_item($admin_bar, 'cforms-deleteall', __('Uninstalling / removing cforms', 'cforms2'), __('Be careful here...', 'cforms2'));

    if (cforms2_is_table_present())
        cforms2_add_admin_bar_item($admin_bar, 'cforms-deletetables', __('Delete cforms tracking tables', 'cforms2'), __('Be careful here...', 'cforms2'));

    cforms2_add_admin_bar_item($admin_bar, 'cforms-SubmitOptions', __('Save & update form settings', 'cforms2'), '', 'root-default');

}

function cforms2_add_items_options($admin_bar) {

    $cformsSettings = get_option('cforms_settings');

    cforms2_add_admin_bar_root($admin_bar, 'cforms-bar', 'cforms Admin');

    cforms2_add_admin_bar_item($admin_bar, 'cforms-addbutton', __('Add new form', 'cforms2'), __('Adds a new form with default values', 'cforms2'));
    cforms2_add_admin_bar_item($admin_bar, 'cforms-dupbutton', __('Duplicate current form', 'cforms2'), __('Clones the current form', 'cforms2'));
    if ((int) $cformsSettings['global']['cforms_formcount'] > 1)
        cforms2_add_admin_bar_item($admin_bar, 'cforms-delbutton', __('Delete current form (!)', 'cforms2'), __('Clicking this button WILL delete this form', 'cforms2'));

    cforms2_add_admin_bar_item($admin_bar, 'cforms-SubmitOptions', __('Save & update form settings', 'cforms2'), '', 'root-default');

}

function cforms2_get_boolean_from_request($index) {
    if (isset($_POST[$index]) && $_POST[$index])
        return '1';
    else
        return '0';

}

function cforms2_get_from_request($index) {
    if (isset($_POST[$index]) && $_POST[$index])
        return $_POST[$index];
    else
        return '';

}

function cforms2_get_pluggable_captchas() {
    static $captchas = array();
    if (empty($captchas))
    // This filter is meant to add one element to the associative array per cforms2_captcha
    // implementation consisting of a unique identifier as key and object as value.
    // known identifiers are:
    //   cforms2_question_and_answer
    //   verification
    //   captcha
        $captchas = apply_filters('cforms2_add_captcha', $captchas);
    return $captchas;

}

function cforms2_get_fieldtypes() {
    static $fields = array();
    if (empty($fields)) {
        // This filter is meant to add one element to the associative array per cforms2_fieldtype
        // implementation consisting of a unique identifier as key and object as value.
        $fields = apply_filters('cforms2_add_fieldtype', $fields);
    }
    return $fields;

}

function cforms2_check_pluggable_captchas_authn_users($field_type) {
    $captchas = cforms2_get_pluggable_captchas();
    return array_key_exists($field_type, $captchas) && is_user_logged_in() && !$captchas[$field_type]->check_authn_users();

}

function cforms2_admin_date_format() {
    return __('dd', 'cforms2') . '/' . __('mm', 'cforms2') . '/' . __('yyyy', 'cforms2');

}

function cforms2_wp_mail_failed($wp_mail_failed_error) {
    $err_data = "\nError data: " . print_r($wp_mail_failed_error->get_error_data('wp_mail_failed'), 1);
    trigger_error($wp_mail_failed_error->get_error_message('wp_mail_failed') . $err_data, E_USER_WARNING);

}

function cforms2_insert_modal() {
    echo '<div id="cf_editbox" title="' . __('Input Field Settings', 'cforms2') . '">'
    . '<div class="cf_ed_main"><div id="cf_target"></div></div></div>';

}

/** strip stuff */
function cforms2_prep($v, $d) {
    return empty($v) ? $d : stripslashes(htmlspecialchars($v));

}
