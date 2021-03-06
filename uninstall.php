<?php
/*
 * Copyright (c) 2012      Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
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

if (!defined('WP_UNINSTALL_PLUGIN'))
    die();

delete_option('cforms_settings');

$role = get_role('administrator');
if ($role != null) {
    $role->remove_cap('manage_cforms');
    $role->remove_cap('track_cforms');
}

global $wpdb;
$wpdb->delete($wpdb->postmeta, array('meta_key' => 'tell-a-friend'));
