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

add_action('wp_ajax_database_getentries', 'cforms2_database_getentries');

function cforms2_database_getentries() {
    check_admin_referer('database_getentries');
    if (!current_user_can('track_cforms'))
        die("access restricted.");

    global $wpdb;

    $cformsSettings = get_option('cforms_settings');

    $showIDs = $_POST['showids'];
    if (!empty($showIDs)) {
        $sortBy = isset($_POST['sortby']) && !empty($_POST['sortby']) ? $_POST['sortby'] : 'sub_id';
        $sortOrder = isset($_POST['sortorder']) && $_POST['sortorder'] === 'asc' ? 'asc' : 'desc';

        $sql = "SELECT *, form_id, ip FROM {$wpdb->prefix}cformsdata, {$wpdb->prefix}cformssubmissions WHERE sub_id=id ";
        $sqlargs = array();

        if ($showIDs !== 'all') {
            $sub_ids = explode(',', substr($showIDs, 0, -1));
            $placeholder = implode(',', array_fill(0, count($sub_ids), '%d'));
            $sql .= "AND sub_id in ($placeholder) ";
            $sqlargs = array_merge($sqlargs, $sub_ids);
        }

        $sql .= "ORDER BY %s $sortOrder, f_id";
        $sqlargs[] = $sortBy;

        $sql = $wpdb->prepare($sql, $sqlargs);
        $entries = $wpdb->get_results($sql);

        echo '<div>';

        if ($entries) {

            $sub_id = '';
            foreach ($entries as $entry) {

                if ($sub_id != $entry->sub_id) {

                    if (!empty($sub_id))
                        echo '</div>';

                    $sub_id = $entry->sub_id;

                    $date = mysql2date(get_option('date_format'), $entry->sub_date);
                    $time = mysql2date(get_option('time_format'), $entry->sub_date);

                    echo '<div class="showform" id="entry' . $entry->sub_id . '">' .
                    '<table class="dataheader"><tr><td>' . __('Form:', 'cforms2') . ' </td><td class="b">' . stripslashes($cformsSettings['form' . $entry->form_id]['cforms' . $entry->form_id . '_fname']) . '</td><td class="e">(ID:' . $entry->sub_id . ')</td><td class="d">' . $time . ' &nbsp; ' . $date . '</td>' .
                    '<td class="s">&nbsp;</td><td><a href="#" class="xdatabutton allbuttons deleteall" type="submit" id="xbutton' . $entry->sub_id . '">' . __('Delete this entry', 'cforms2') . '</a></td>' .
                    '<td><a class="cdatabutton dashicons dashicons-dismiss" type="submit" id="cbutton' . $entry->sub_id . '" title="' . __('close this entry', 'cforms2') . '"></a></td>' .
                    "</tr></table>\n";
                }

                $name = $entry->field_name == '' ? '' : stripslashes($entry->field_name);
                $val = $entry->field_val == '' ? '' : stripslashes($entry->field_val);

                if (strpos($name, '[*') !== false) {  // attachments?
                    preg_match('/.*\[\*(.*)\]$/i', $name, $r);
                    $no = $r[1] == '' ? $entry->form_id : ($r[1] == 1 ? '' : $r[1]);

                    $temp = explode('$#$', stripslashes(htmlspecialchars($cformsSettings['form' . $no]['cforms' . $no . '_upload_dir'])));
                    $fileuploaddir = trailingslashit($temp[0]);

                    $rawfileName = strip_tags($val);

                    $fileName = $entry->sub_id . '-' . $rawfileName;
                    if (!file_exists($fileuploaddir . $fileName))
                        $fileName = $rawfileName;

                    echo '<div class="showformfield meta"><div class="L">';
                    echo substr($name, 0, strpos($name, '[*'));
                    if ($entry->field_val == '')
                        echo '</div><div class="R">' . __('-', 'cforms2') . '</div></div>' . "\n";
                    else
                        echo '</div><div class="R">' . str_replace("\n", "<br />", strip_tags($val)) . '</div></div>' . "\n";
                }
                elseif ($name == 'page') {  // special field: page
                    echo '<div class="showformfield meta"><div class="L">';
                    _e('Submitted via page', 'cforms2');
                    echo '</div><div class="R">' . str_replace("\n", "<br />", strip_tags($val)) . '</div></div>' . "\n";

                    echo '<div class="showformfield meta"><div class="L">';
                    _e('IP address', 'cforms2');
                    echo '</div><div class="R"><a href="http://geomaplookup.net/?ip=' . $entry->ip . '" title="' . __('IP Lookup', 'cforms2') . '">' . $entry->ip . '</a></div></div>' . "\n";
                } elseif (strpos($name, 'Fieldset') !== false) {

                    if (strpos($name, 'FieldsetEnd') === false)
                        echo '<div class="showformfield tfieldset"><div class="L">&nbsp;</div><div class="R">' . strip_tags($val) . '</div></div>' . "\n";
                } else {

                    echo '<div class="showformfield"><div class="L">' . $name . '</div>' .
                    '<div id="' . $entry->f_id . '" class="R">' . str_replace("\n", "<br />", strip_tags($val)) . '</div></div>' . "\n";
                }
            }
        } else {
            echo '<p align="center">' . __('Sorry, data not found. Please refresh your data table.', 'cforms2') . '</p>';
        }
        echo '</div>';
    }

    die();

}
