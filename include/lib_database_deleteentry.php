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

add_action('wp_ajax_database_deleteentry', 'cforms2_database_deleteentry');

function cforms2_database_deleteentry() {
    check_admin_referer('database_deleteentry');
    if (!current_user_can('track_cforms'))
        die("access restricted.");

    global $wpdb;

    $cformsSettings = get_option('cforms_settings');

    $sub_id = $_POST['id'];

    if (!empty($sub_id) && $sub_id >= 0) {

        $sql = "SELECT field_val,form_id FROM {$wpdb->prefix}cformsdata, {$wpdb->prefix}cformssubmissions WHERE sub_id = %s AND id=sub_id AND field_name LIKE '%%[*%%'";
        $filevalues = $wpdb->get_results($wpdb->prepare($sql, $sub_id));

        $del = '';
        $found = 0;

        foreach ($filevalues as $fileval) {

            $temp = explode('$#$', stripslashes(htmlspecialchars($cformsSettings['form' . $fileval->form_id]['cforms' . $fileval->form_id . '_upload_dir'])));
            $fileuploaddir = $temp[0];

            $file = $fileuploaddir . '/' . $sub_id . '-' . $fileval->field_val;

            if (!empty($fileval->field_val)) {
                if (file_exists($file)) {
                    unlink($file);
                    $found = $found | 1;
                } else {
                    $found = $found | 2;
                }
            }
        }

        if ($found == 3)
            $del = ' ' . __('(some associated attachment/s were not found!)', 'cforms2');
        elseif ($found == 2)
            $del = ' ' . __('(associated attachment/s were not found!)', 'cforms2');
        elseif ($found == 1)
            $del = ' ' . __('(including all attachment/s)', 'cforms2');

        $wpdb->delete($wpdb->prefix . 'cformssubmissions', array('id' => $sub_id));
        $wpdb->delete($wpdb->prefix . 'cformsdata', array('sub_id' => $sub_id));

        echo '<p><strong>' . __('Entry successfully removed', 'cforms2') . $del . '</strong></p>';
    }
    die();

}
