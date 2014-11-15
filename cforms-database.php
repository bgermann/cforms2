<?php
/*
 * Copyright (c) 2006-2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
 * Copyright (c) 2014      Bastian Germann
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

### DB settings
global $wpdb;
$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

### Check Whether User Can Manage Database
cforms2_check_access_priv('track_cforms');

### New global settings container, will eventually be the only one!
$cformsSettings = get_option('cforms_settings');

### if all data has been erased quit
if ( cforms2_check_erased() )
	return;
?>
<div class="wrap" id="top">
	<div id="icon-cforms-tracking" class="icon32"><br/></div><h2><?php _e('Tracking Form Data','cforms')?></h2>

	<p><?php _e('All your recorded form submissions are listed below. View individual entries or a whole bunch and download as XML, TAB or CSV formatted file. Attachments can be accessed in the details section (<strong>View records</strong>). When deleting entries, associated attachments will be removed, too! ', 'cforms') ?></p>

	<p class="ex" style="margin-bottom:30px;"><?php _e('If you want to select <strong>ALL</strong> entries, e.g. for download, simply don\'t select any particular row. When <strong>viewing records</strong>: Fields with a <em>grey background</em> can be clicked on and edited!', 'cforms') ?></p>

	<div id="ctrlmessage"></div>
	<div class="bborderx"><table id="flex1" style="display:none"><tr><td></td></tr></table></div>
	<div id="entries"></div>
	<div id="geturl" title="<?php echo plugin_dir_url(__FILE__); ?>js/include/"></div>

	<?php
	### if called from dashboard
	$dashboard = '';
    if ( isset($_GET['d-id']) ){
	    $dashboard = "qtype: 'id', query: '".$_GET['d-id']."',";
	}
	?>

<script type="text/javascript">
jQuery("#flex1").flexigrid ( {
	url: ajaxurl,
	dataType: 'xml',
	colModel : [
		{display: '#', name : 'id', width : 40, sortable : true, align: 'center'},
		{display: '<?php _e('Form Name','cforms'); ?>', name : 'form_id', width : 240, sortable : true, align: 'center'},
		{display: '<?php _e('e-mail Address','cforms'); ?>', name : 'email', width : 200, sortable : true, align: 'center'},
		{display: '<?php _e('Date','cforms'); ?>', name : 'sub_date', width : 160, sortable : true, align: 'center'},
		{display: '<?php _e('IP','cforms'); ?>', name : 'ip', width : 100, sortable : true, align: 'center'}
		],
	buttons : [
		{name: '<?php _e('View records','cforms'); ?>', bclass: 'add', onpress : cf_tracking_view},
		{name: '<?php _e('Delete records','cforms'); ?>', bclass: 'delete', onpress : function (){jQuery('#cf_delete_dialog').jqmShow();} },
		{name: '<?php _e('Download records','cforms'); ?>', bclass: 'dl', onpress : function (){jQuery('#cf_dl_dialog').jqmShow();}},
		{separator: true}
		],
	searchitems : [
		{display: '<?php _e('# Number(s)','cforms'); ?>', name : 'id'},
		{display: '<?php _e('Form Name','cforms'); ?>', name : 'form_id'},
		{display: '<?php _e('e-mail Address','cforms'); ?>', name : 'email', isdefault: true},
		{display: '<?php _e('Date','cforms'); ?>', name : 'sub_date'},
		{display: '<?php _e('IP','cforms'); ?>', name : 'ip'}
		],<?php echo $dashboard; ?>
	sortname: "id",
	sortorder: "desc",
	usepager: true,
	title: '<?php _e('Form Submissions','cforms'); ?>',
	errormsg: '<?php _e('Connection Error','cforms'); ?>',
	pagestat: '<?php _e('Displaying {from} to {to} of {total} items','cforms'); ?>',
	procmsg: '<?php _e('Processing, please wait ...','cforms'); ?>',
	nomsg: '<?php _e('No items','cforms'); ?>',
	outof: '<?php _e('of','cforms'); ?>',
	findtext: '<?php _e('Find','cforms'); ?>',
	useRp: true,
	blockOpacity: 0.9,
	rp: 30,
	params : [
		{name:'action', value:'database_overview'},
		{name:'_wpnonce', value: '<?php echo wp_create_nonce('database_overview'); ?>'}
	],
	rpOptions: [10,30,50,100,200],
	showTableToggleBtn: true,
	width: 900,
	height: 250
});
</script>
<?php

### if called from dashboard
if ( isset($_GET['d-id']) ){
	$_POST['showids'] = $_GET['d-id'].',';
}

cforms2_footer();
?>
</div> <!-- wrap -->

<?php
add_action('admin_footer', 'cforms2_insert_modal_tracking');
function cforms2_insert_modal_tracking(){

?>
	<div class="jqmWindow" id="cf_delete_dialog">
		<div class="cf_ed_header jqDrag"><?php _e('Please Confirm','cforms'); ?></div>
		<div class="cf_ed_main">
			<form action="" name="deleteform" method="post">
				<div id="cf_target_del"><?php _e('Are you sure you want to delete the record(s)?','cforms'); ?></div>
				<div class="controls"><a href="#" id="okDelete" class="jqmClose"><img src="<?php echo plugin_dir_url(__FILE__) ?>images/dialog_ok.gif" alt="<?php _e('Install', 'cforms') ?>" title="<?php _e('OK', 'cforms') ?>"/></a><a href="#" class="jqmClose"><img src="<?php echo plugin_dir_url(__FILE__) ?>images/dialog_cancel.gif" alt="<?php _e('Cancel', 'cforms') ?>" title="<?php _e('Cancel', 'cforms') ?>"/></a></div>
			</form>
		</div>
	</div>
	<div class="jqmWindow" id="cf_dl_dialog">
		<div class="cf_ed_header jqDrag"><?php _e('Please Confirm','cforms'); ?></div>
		<div class="cf_ed_main">
			<form action="" name="downloadform" method="post" id="downloadform">
				<div id="cf_target_dl">
                    <select id="pickDLformat" name="format">
                        <option value="xml">&nbsp;&nbsp;&nbsp;XML&nbsp;&nbsp;&nbsp;</option>
                        <option value="csv">&nbsp;&nbsp;&nbsp;CSV&nbsp;&nbsp;&nbsp;</option>
                        <option value="tab">&nbsp;&nbsp;&nbsp;TAB&nbsp;&nbsp;&nbsp;</option>
                    </select><label for="pickDLformat"><?php echo sprintf(__('Please pick a format!','cforms')); ?></label>
                    <br />
                    <input type="radio" class="chkBoxW" id="enc-utf8" name="enc" value="utf-8"/><label for="enc-utf8"><?php echo sprintf(__('UTF-8','cforms')); ?></label>
                    <input type="radio" class="chkBoxW" id="enc-iso" name="enc" value="iso" checked="checked"/><label for="enc-iso"><?php echo sprintf(__('ISO-8859-1','cforms')); ?></label>
                    <br />
                    <input type="checkbox" class="chkBoxW" id="header" name="header" value="true"/><label for="header"><?php echo sprintf(__('Include field names / header','cforms')); ?></label><br />
                    <input type="checkbox" class="chkBoxW" id="addip" name="addip" value="true"/><label for="addip"><?php echo sprintf(__('Include IP address of submitting user','cforms')); ?></label><br />
                    <input type="checkbox" class="chkBoxW" id="addurl" name="addurl" value="true"/><label for="addurl"><?php echo sprintf(__('Add URL for upload fields','cforms')); ?></label>
				</div>
				<div class="controls"><a href="#" id="okDL" class="jqmClose"><img src="<?php echo plugin_dir_url(__FILE__); ?>images/dialog_ok.gif" alt="<?php _e('Install', 'cforms') ?>" title="<?php _e('OK', 'cforms') ?>"/></a><a href="#" class="jqmClose"><img src="<?php echo plugin_dir_url(__FILE__); ?>images/dialog_cancel.gif" alt="<?php _e('Cancel', 'cforms') ?>" title="<?php _e('Cancel', 'cforms') ?>"/></a></div>
			</form>
		</div>
	</div>
<?php
}
