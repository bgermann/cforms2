<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

delete_option('cforms_settings');

global $wpdb;
$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'cformssubmissions');
$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'cformsdata');
