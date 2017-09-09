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
 * 
 * Plugin Name: cforms2
 * Plugin URI: https://wordpress.org/plugins/cforms2/
 * Description: cformsII is a customizable, flexible and powerful form plugin including simple spam protection, multi-step forms, role manager support, submission database tracking and custom themes.
 * Author: Oliver Seidel, Bastian Germann
 * Version: 14.14
 * Text Domain: cforms2
 */

define('CFORMS2_VERSION', '14.14');

// debug message handling
if (!defined('WP_DEBUG_CFORMS2')) {
    define('WP_DEBUG_CFORMS2', false);
}

require_once(plugin_dir_path(__FILE__) . 'fieldtypes/fieldtype.php');
require_once(plugin_dir_path(__FILE__) . 'lib_activate.php');
require_once(plugin_dir_path(__FILE__) . 'lib_ajax.php');
require_once(plugin_dir_path(__FILE__) . 'lib_aux.php');
require_once(plugin_dir_path(__FILE__) . 'lib_deprecated.php');
require_once(plugin_dir_path(__FILE__) . 'lib_functions.php');
require_once(plugin_dir_path(__FILE__) . 'lib_render.php');
require_once(plugin_dir_path(__FILE__) . 'lib_validate.php');
require_once(plugin_dir_path(__FILE__) . 'lib_widget.php');

global $cformsSettings;
$cformsSettings = get_option('cforms_settings');

$role = get_role('administrator');
if ($role != null) {
    $role->add_cap('manage_cforms');
    $role->add_cap('track_cforms');
}

register_activation_hook(__FILE__, 'cforms2_setup_db');
cforms2_fieldtype::register();

// settings corrupted?
if (!is_array($cformsSettings)) {
    add_action('admin_menu', 'cforms2_settings_corrupted');
    return;
}

if (is_admin()) {
    require_once(plugin_dir_path(__FILE__) . 'include/lib_database_deleteentry.php');
    require_once(plugin_dir_path(__FILE__) . 'include/lib_database_getentries.php');
    require_once(plugin_dir_path(__FILE__) . 'include/lib_database_overview.php');
    require_once(plugin_dir_path(__FILE__) . 'lib_editor.php');

    cforms2_register_editor();

    if (array_key_exists('QUERY_STRING', $_SERVER) && strpos($_SERVER['QUERY_STRING'], 'cforms') !== false) {
        add_action('admin_enqueue_scripts', 'cforms2_admin_enqueue_scripts');
    }

    add_action('admin_menu', 'cforms2_menu');
    add_action('wp_ajax_cforms2_field', 'cforms2_field');

    // admin bar
    if (isset($_GET['page'])) {
        $plugin_page = stripslashes($_GET['page']);
        $plugin_page = plugin_basename($plugin_page);
        if (strpos($plugin_page, 'cforms-options.php'))
            add_action('admin_bar_menu', 'cforms2_add_items_options', 999);
        elseif (strpos($plugin_page, 'cforms-global-settings.php'))
            add_action('admin_bar_menu', 'cforms2_add_items_global', 999);
    }
}

// attaching to hooks
add_action('template_redirect', 'cforms2_start_session');
add_action('wp_ajax_submitcform', 'cforms2_submitcform');
add_action('wp_ajax_nopriv_submitcform', 'cforms2_submitcform');
add_action('plugins_loaded', 'cforms2_localization');
add_action('widgets_init', 'cforms2_widget_init');
add_action('init', 'cforms2_delete_db_and_deactivate');
add_action('wp_enqueue_scripts', 'cforms2_enqueue_scripts');
add_action('wp_mail_failed', 'cforms2_wp_mail_failed');
add_shortcode('cforms', 'cforms2_shortcode');

cforms2_warn_on_existing_my_functions();
