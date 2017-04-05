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

global $wpdb;
$wpdb->cformssubmissions = $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata = $wpdb->prefix . 'cformsdata';

// Check Whether User Can Manage Database
cforms2_check_access_priv('track_cforms');

$cformsSettings = get_option('cforms_settings');

// if all data has been erased quit
if (cforms2_check_erased())
    return;
?>
<div class="wrap" id="top">
    <h2><?php _e('Tracking Form Data', 'cforms2') ?></h2>

    <p><?php _e('All your recorded form submissions are listed below. View individual entries or a whole bunch. Attachments can be accessed in the details section. When deleting an entry, associated attachments will be removed, too! ', 'cforms2') ?></p>

    <table id="flex1" style="display:none"><tr><td></td></tr></table>
    <div id="entries"></div>
    <div id="geturl" title="<?php echo plugin_dir_url(__FILE__); ?>include/"></div>

    <script type="text/javascript">
        // @license magnet:?xt=urn:btih:1f739d935676111cfff4b4693e3816e664797050&dn=gpl-3.0.txt GPL-v3-or-Later'
        jQuery("#flex1").flexigrid({
            url: ajaxurl,
            dataType: 'xml',
            colModel: [
                {display: '#', name: 'id', width: 40, sortable: true, align: 'center'},
                {display: '<?php _e('Form Name', 'cforms2'); ?>', name: 'form_id', width: 240, sortable: true, align: 'center'},
                {display: '<?php _e('e-mail Address', 'cforms2'); ?>', name: 'email', width: 200, sortable: true, align: 'center'},
                {display: '<?php _e('Date', 'cforms2'); ?>', name: 'sub_date', width: 160, sortable: true, align: 'center'},
                {display: '<?php _e('IP', 'cforms2'); ?>', name: 'ip', width: 100, sortable: true, align: 'center'}
            ],
            buttons: [
                {name: '<?php _e('View records', 'cforms2'); ?>', bclass: 'dashicons-before dashicons-media-text', onpress: cf_tracking_view}
            ],
            sortname: "id",
            sortorder: "desc",
            usepager: true,
            title: '<?php _e('Form Submissions', 'cforms2'); ?>',
            errormsg: '<?php _e('Connection Error', 'cforms2'); ?>',
            pagestat: '<?php _e('Displaying {from} to {to} of {total} items', 'cforms2'); ?>',
            procmsg: '<?php _e('Processing, please wait ...', 'cforms2'); ?>',
            nomsg: '<?php _e('No items', 'cforms2'); ?>',
            outof: '<?php _e('of', 'cforms2'); ?>',
            findtext: '<?php _e('Find', 'cforms2'); ?>',
            useRp: true,
            blockOpacity: 0.9,
            rp: 30,
            params: [
                {name: 'action', value: 'database_overview'},
                {name: '_wpnonce', value: '<?php echo wp_create_nonce('database_overview'); ?>'}
            ],
            rpOptions: [10, 30, 50, 100, 200],
            showTableToggleBtn: true,
            width: 900,
            height: 250
        });
        // @license-end
    </script>

    <?php cforms2_footer(); ?>
</div>
