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

/** used to insert button in editor */
function cforms2_mce_button($buttons) {
    array_push($buttons, "separator", "cforms");
    return $buttons;

}

/** adding to TinyMCE */
function cforms2_mce($plugins) {
    $plugins['cforms'] = plugin_dir_url(__FILE__) . 'js/cforms.tinymce.js';
    return $plugins;

}

function cforms2_mce_translation($mce_translation) {
    $mce_translation['Insert a form'] = __('Insert a form', 'cforms2');
    return $mce_translation;

}

function cforms2_mce_script() {
    $cformsSettings = get_option('cforms_settings');
    $fns = array();
    $forms = $cformsSettings['global']['cforms_formcount'];
    for ($i = 0; $i < $forms; $i++) {
        $no = ($i == 0) ? '' : ($i + 1);
        $fns[] = $cformsSettings['form' . $no]['cforms' . $no . '_fname'];
    }
    echo '<script type="text/javascript">// @license magnet:?xt=urn:btih:1f739d935676111cfff4b4693e3816e664797050&dn=gpl-3.0.txt GPL-v3-or-Later'
    . "\ncforms2_formnames = " . json_encode($fns) . ";\n// @license-end</script>";

}

function cforms2_register_editor() {
    // only insert buttons if enabled!
    $cformsSettings = get_option('cforms_settings');
    if ($cformsSettings['global']['cforms_show_quicktag'] && is_admin()) {
        add_filter('mce_external_plugins', 'cforms2_mce');
        add_filter('wp_mce_translation', 'cforms2_mce_translation');
        add_filter('mce_buttons', 'cforms2_mce_button');
        add_action('admin_print_scripts', 'cforms2_mce_script');
    }

}
