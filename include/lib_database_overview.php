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

add_action('wp_ajax_database_overview', 'cforms2_database_overview');

function cforms2_database_overview() {
    ob_start();
    check_admin_referer('database_overview');
    if (!current_user_can('track_cforms'))
        die("access restricted.");

    $page = (int) $_POST['page'];
    if ($page < 1)
        $page = 1;

    $rp = (int) $_POST['rp'];
    if ($rp < 1)
        $rp = 10;

    $start = (($page - 1) * $rp);

    global $wpdb;
    $sql = "SELECT count(id) FROM {$wpdb->prefix}cformssubmissions";
    $total = $wpdb->get_var($sql);

    $result = get_cforms_entries(false, false, false, $_POST['sortname'], $rp, $_POST['sortorder'], $start);

    $xml = "<?xml version=\"1.0\"?>\n";
    $xml .= "<rows>";
    $xml .= "<page>$page</page>";
    $xml .= "<total>$total</total>";

    foreach ($result as $entry) {
        $xml .= "<row id='" . $entry['id'] . "'>";
        $xml .= "<cell><![CDATA[" . $entry['id'] . "]]></cell>";
        $xml .= "<cell><![CDATA[" . ( $entry['form'] ) . "]]></cell>";
        $xml .= "<cell><![CDATA[" . ( $entry['email'] ) . "]]></cell>";
        $xml .= "<cell><![CDATA[" . ( $entry['date'] ) . "]]></cell>";
        $xml .= "<cell><![CDATA[" . ( $entry['ip'] ) . "]]></cell>";
        $xml .= "</row>";
    }

    $xml .= "</rows>";
    ob_end_clean();
    echo $xml;
    die();

}
