<?php
/*
 * Copyright (c) 2012      Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
 * Copyright (c) 2014-2016 Bastian Germann
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

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

delete_option('cforms_settings');
WP_Roles::remove_cap('administrator', 'manage_cforms');
WP_Roles::remove_cap('administrator', 'track_cforms');

global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'tell-a-friend'");
$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'cformssubmissions');
$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'cformsdata');
