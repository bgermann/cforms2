<?php
/*
 * Copyright (c) 2006-2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
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

### db settings
global $wpdb;
$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

$cformsSettings = get_option('cforms_settings');

$plugindir   = dirname(plugin_basename(__FILE__));

### Check Whether User Can Manage Database
cforms2_check_access_priv();


### if all data has been erased quit
if ( cforms2_check_erased() )
	return;

if ( isset($_REQUEST['deletetables']) ) {

	$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'cformssubmissions');
	$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'cformsdata');

    $cformsSettings['global']['cforms_database'] = '0';
    update_option('cforms_settings',$cformsSettings);

	?>
	<div id="message" class="updated fade">
		<p>
		<strong><?php echo sprintf (__('cforms tracking tables %s have been deleted.', 'cforms2'),'(<code>cformssubmissions</code> &amp; <code>cformsdata</code>)') ?></strong>
			<br />
			<?php _e('Please backup/clean-up your upload directory, chances are that when you turn tracking back on, existing (older) attachments may be <u>overwritten</u>!') ?>
			<br />
			<small><?php _e('(only of course, if your form includes a file upload field)') ?></small>
		</p>
	</div>
	<?php

} else if( isset($_REQUEST['restoreallcformsdata']) )
	require_once(plugin_dir_path(__FILE__) . 'lib_options_up.php');

// Update Settings
if( isset($_REQUEST['SubmitOptions']) ) {

	$cforms_dp_nav = stripslashes(cforms2_get_from_request('cforms_dp_nav'));
	if (empty($cforms_dp_nav)) {
		$cforms_dp_nav = array();
	} else {
		$cforms_dp_nav = json_decode($cforms_dp_nav, true);
		if ($cforms_dp_nav === null) {
			// As WordPress has a compatibility layer, json_last_error_msg (PHP >= 5.5) can be used
			$cforms_dp_nav = array("ERROR" => json_last_error_msg());
		}
	}
	$cformsSettings['global']['cforms_dp_nav']        = $cforms_dp_nav;
	$cformsSettings['global']['cforms_html5']         = cforms2_get_boolean_from_request('cforms_html5');
	$cformsSettings['global']['cforms_show_quicktag'] = cforms2_get_boolean_from_request('cforms_show_quicktag');
	$cformsSettings['global']['cforms_sec_qa']        = cforms2_get_from_request('cforms_sec_qa');
	$cformsSettings['global']['cforms_codeerr']       = cforms2_get_from_request('cforms_codeerr');
	$cformsSettings['global']['cforms_database']      = cforms2_get_boolean_from_request('cforms_database');
	$cformsSettings['global']['cforms_showdashboard'] = cforms2_get_boolean_from_request('cforms_showdashboard');
	$cformsSettings['global']['cforms_datepicker']    = cforms2_get_boolean_from_request('cforms_datepicker');
	$cformsSettings['global']['cforms_dp_date']       = cforms2_get_from_request('cforms_dp_date');

 	$cformsSettings['global']['cforms_inexclude']['ex'] = '';

	if( cforms2_get_from_request('cforms_inc-or-ex')=='exclude' )
		$cformsSettings['global']['cforms_inexclude']['ex'] = '1';

	$cformsSettings['global']['cforms_inexclude']['ids'] = cforms2_get_from_request('cforms_include');

	$cformsSettings['global']['cforms_crlf']['b'] =	   	cforms2_get_boolean_from_request('cforms_crlf')?'1':'0';
	$cformsSettings['global']['cforms_smtp'] = null ;

	$cformsSettings['global']['cforms_upload_err1'] = cforms2_get_from_request('cforms_upload_err1');
	$cformsSettings['global']['cforms_upload_err2'] = cforms2_get_from_request('cforms_upload_err2');
	$cformsSettings['global']['cforms_upload_err3'] = cforms2_get_from_request('cforms_upload_err3');
	$cformsSettings['global']['cforms_upload_err4'] = cforms2_get_from_request('cforms_upload_err4');
	$cformsSettings['global']['cforms_upload_err5'] = cforms2_get_from_request('cforms_upload_err5');

	$cap = array();
	$cap['w'] = cforms2_get_from_request('cforms_cap_w');
	$cap['h'] = cforms2_get_from_request('cforms_cap_h');
	$cap['c'] = cforms2_get_from_request('cforms_cap_c');
	$cap['l'] = cforms2_get_from_request('cforms_cap_l');
	$cap['fo']= cforms2_get_from_request('cforms_cap_fo');
	$cap['foqa']= cforms2_get_from_request('cforms_cap_foqa');
	$cap['f1']= cforms2_get_from_request('cforms_cap_f1');
	$cap['f2']= cforms2_get_from_request('cforms_cap_f2');
	$cap['c1']= cforms2_get_from_request('cforms_cap_c1');
	$cap['c2']= cforms2_get_from_request('cforms_cap_c2');
	$cap['ac']= cforms2_get_from_request('cforms_cap_ac');
	
    ###	update new settings container
	$cformsSettings['global']['cforms_captcha_def'] = $cap;

	update_option('cforms_settings',$cformsSettings);

	// Setup database tables ?
	if ( isset($_REQUEST['cforms_database']) && $_REQUEST['cforms_database_new']=='true' ) {

		if ( $wpdb->get_var("show tables like '$wpdb->cformssubmissions'") <> $wpdb->cformssubmissions ){

			$sql = "CREATE TABLE " . $wpdb->cformssubmissions . " (
					  id int(11) unsigned auto_increment,
					  form_id varchar(3) default '',
					  sub_date timestamp,
					  email varchar(40) default '',
					  ip varchar(15) default '',
					  PRIMARY KEY  (id) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);

			$sql = "CREATE TABLE " . $wpdb->cformsdata . " (
					  f_id int(11) unsigned auto_increment primary key,
					  sub_id int(11) unsigned NOT NULL,
					  field_name varchar(100) NOT NULL default '',
					  field_val text) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);

            ###check
	        if( $wpdb->get_var("show tables like '$wpdb->cformssubmissions'") <> $wpdb->cformssubmissions ) {
	            ?>
	            <div id="message" class="updated fade">
	                <p><strong><?php echo sprintf(__('ERROR: cforms tracking tables %s could not be created.', 'cforms2'),'(<code>cformssubmissions</code> &amp; <code>cformsdata</code>)') ?></strong></p>
	            </div>
	            <?php
			    $cformsSettings['global']['cforms_database'] = '0';
			    update_option('cforms_settings',$cformsSettings);
            }else{
	            ?>
	            <div id="message" class="updated fade">
	                <p><strong><?php echo sprintf(__('cforms tracking tables %s have been created.', 'cforms2'),'(<code>cformssubmissions</code> &amp; <code>cformsdata</code>)') ?></strong></p>
	            </div>
	            <?php
            }

		} else {

			$sets = $wpdb->get_var("SELECT count(id) FROM $wpdb->cformssubmissions");
			?>
			<div id="message" class="updated fade">
				<p><strong><?php echo sprintf(__('Found existing cforms tracking tables with %s records!', 'cforms2'),$sets) ?></strong></p>
			</div>
			<?php
		}
	}
}

?>

<div class="wrap" id="top">
    <div id="icon-cforms-global" class="icon32"><br/></div><h2><?php _e('Global Settings','cforms2')?></h2>

    <?php if ( WP_DEBUG && isset($_POST['showinfo']) ) : ###debug "easter egg" 

        echo '<h2>'.__('Debug Info (all major setting groups)', 'cforms2').'</h2><br/><pre style="font-size:11px;background-color:#F5F5F5;">';
        echo print_r(array_keys($cformsSettings),1)."</pre>";
        echo '<h2>'.__('Debug Info (all cforms settings)', 'cforms2').'</h2><br/><pre style="font-size:11px;background-color:#F5F5F5;">'.print_r($cformsSettings,1)."</pre>";
    
	else : ?>
	
    <p><?php _e('All settings and configuration options on this page apply to all forms.', 'cforms2') ?></p>

	<form enctype="multipart/form-data" id="cformsdata" name="mainform" method="post">
		<input type="hidden" name="cforms_database_new" value="<?php if($cformsSettings['global']['cforms_database']=="0") echo 'true'; ?>"/>

		<fieldset id="inandexclude" class="cformsoptions">
			<div class="cflegend op-closed" id="p27" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Include cforms header data only on specific pages', 'cforms2')?>
            </div>

			<div class="cf-content" id="o27">
				<p><?php _e('Specify the ID(s) of <strong>pages or posts</strong> separated by comma on which you\'d like to show or not show cforms. The cforms header will only be included specifically on those pages, helping to maintain all other pages neat.', 'cforms2') ?></p>

				<table class="form-table">
				<tr class="ob inexclude">
					<td class="obL"><label for="cforms_include"><strong><?php _e('Page / Post ID(s)', 'cforms2'); $ex = ($cformsSettings['global']['cforms_inexclude']['ex']=='1'); ?></strong></label></td>
					<td class="obR">
              <input class="allchk"<?php echo !$ex?' checked="checked"':''; ?> type="radio" id="include" value="include" name="cforms_inc-or-ex"/><label for="include"><?php _e('include', 'cforms2') ?></label>  <input class="allchk"<?php echo $ex?' checked="checked"':''; ?> type="radio" id="exclude" value="exclude" name="cforms_inc-or-ex"/><label for="exclude"><?php _e('exclude', 'cforms2') ?></label><br />
              <input type="text" id="cforms_include" name="cforms_include" value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_inexclude']['ids'] )); ?>"/><br />
              <?php _e('Leave empty to include cforms header files throughout your blog', 'cforms2') ?>
          </td>
				</tr>
				</table>
			</div>
		</fieldset>

		<fieldset id="popupdate" class="cformsoptions">
			<div class="cflegend op-closed" id="p9" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Popup Date Picker', 'cforms2')?>
            </div>

			<div class="cf-content" id="o9">
				<p><?php echo sprintf(__('If you\'d like to offer a Javascript based date picker for more convenient date entry, enable this feature here. This will add a <strong>new input field</strong> for you to add to your form. See <a href="%s" %s>Help!</a> for more info and <strong>date formats</strong>.', 'cforms2'),'?page='.$plugindir.'/cforms-help.php#datepicker','onclick="setshow(19)"') ?></p>

				<table class="form-table">
				<tr class="ob">
					<td class="obL">&nbsp;</td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_datepicker" name="cforms_datepicker" <?php if($cformsSettings['global']['cforms_datepicker']=="1") echo "checked=\"checked\""; ?>/><label for="cforms_datepicker"><strong><?php _e('Enable Javascript date picker', 'cforms2') ?></strong></label> <a class="infobutton" href="#" name="it10"><?php _e('Note &raquo;', 'cforms2'); ?></a></td>
				</tr>
				<tr id="it10" class="infotxt"><td>&nbsp;</td><td class="ex"><?php _e('Note that turning on this feature will result in loading an additional Javascript file to support the date picker.', 'cforms2') ?></td></tr>

				<tr class="ob space15">
					<td class="obL"><label for="cforms_dp_date"><strong><?php _e('Date Format', 'cforms2'); ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_dp_date" name="cforms_dp_date" value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_dp_date'] )); ?>"/><a href="http://api.jqueryui.com/datepicker/#utility-formatDate"><?php _e('See supported date formats &raquo;', 'cforms2'); ?></a></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_dp_nav"><strong><a href="http://api.jqueryui.com/datepicker/"><?php _e('Additional options (JSON)', 'cforms2') ?></a></strong></label></td>
					<td class="obR"><textarea name="cforms_dp_nav" id="cforms_dp_nav" placeholder="{ &quot;changeYear&quot; : true }"><?php echo htmlspecialchars(json_encode($cformsSettings['global']['cforms_dp_nav'], JSON_PRETTY_PRINT)) ?></textarea></td>
				</tr>
				</table>
			</div>
		</fieldset>


		<fieldset id="smtp" class="cformsoptions">
			<div class="cflegend op-closed" id="p10" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Mail Server Settings', 'cforms2')?>
            </div>

			<div class="cf-content" id="o10">

				<p><?php _e('cforms produces RFC compliant emails with CRLF (carriage-return/line-feed) as line separators. If your mail server adds additional line breaks to the email, you may want to try and turn on the below option.', 'cforms2') ?>
				<table class="form-table">
				<tr class="ob">
					<td class="obL">&nbsp;</td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_crlf" name="cforms_crlf" <?php if($cformsSettings['global']['cforms_crlf']['b']=="1") echo "checked=\"checked\""; ?>/><label for="cforms_crlf"><?php echo sprintf(__('Separate lines in email %sbody%s with LF only (CR suppressed)', 'cforms2'),'<strong>','</strong>') ?></label></td>
				</tr>
				</table>

			</div>
		</fieldset>


		<fieldset id="upload" class="cformsoptions">
			<div class="cflegend op-closed" id="p11" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Global File Upload Settings', 'cforms2')?>
            </div>

			<div class="cf-content" id="o11">
				<p>
					<?php echo sprintf(__('Configure and double-check these settings in case you are adding a "<code>File Upload Box</code>" to your form (also see the <a href="%s" %s>Help!</a> for further information).', 'cforms2'),'?page='.$plugindir.'/cforms-help.php#upload','onclick="setshow(19)"'); ?>
					<?php echo sprintf(__('Form specific settings (directory path etc.) have been moved to <a href="%s" %s>here</a>.', 'cforms2'),'?page='.$plugindir.'/cforms-options.php#fileupload','onclick="setshow(0)"'); ?>
				</p>

				<p class="ex">
					<?php _e('Also, note that by adding a <em>File Upload Box</em> to your form, the Ajax (if enabled) submission method will (automatically) <strong>gracefully degrade</strong> to the standard method, due to general HTML limitations.', 'cforms2') ?>
					<?php _e('Below, error messages shown in case something goes awry:', 'cforms2') ?>
				</p>

				<table class="form-table">
				<tr class="ob">
					<td class="obL"><label for="cforms_upload_err5"><strong><?php _e('File type not allowed', 'cforms2'); ?></strong></label></td>
					<td class="obR">
						<table><tr><td><textarea class="errmsgbox" name="cforms_upload_err5" id="cforms_upload_err5" ><?php echo stripslashes(htmlspecialchars($cformsSettings['global']['cforms_upload_err5'])); ?></textarea></td></tr></table>
					</td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_upload_err1"><strong><?php _e('Generic (unknown) error', 'cforms2'); ?></strong></label></td>
					<td class="obR">
						<table><tr><td><textarea class="errmsgbox" name="cforms_upload_err1" id="cforms_upload_err1" ><?php echo stripslashes(htmlspecialchars($cformsSettings['global']['cforms_upload_err1'])); ?></textarea></td></tr></table>
					</td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_upload_err2"><strong><?php _e('File is empty', 'cforms2'); ?></strong></label></td>
					<td class="obR">
						<table><tr><td><textarea  class="errmsgbox" name="cforms_upload_err2" id="cforms_upload_err2" ><?php echo stripslashes(htmlspecialchars($cformsSettings['global']['cforms_upload_err2'])); ?></textarea></td></tr></table>
					</td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_upload_err3"><strong><?php _e('File size too big', 'cforms2'); ?></strong></label></td>
					<td class="obR">
						<table><tr><td><textarea class="errmsgbox" name="cforms_upload_err3" id="cforms_upload_err3" ><?php echo stripslashes(htmlspecialchars($cformsSettings['global']['cforms_upload_err3'])); ?></textarea></td></tr></table>
					</td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_upload_err4"><strong><?php _e('Error during upload', 'cforms2'); ?></strong></label></td>
					<td class="obR">
						<table><tr><td><textarea class="errmsgbox" name="cforms_upload_err4" id="cforms_upload_err4" ><?php echo stripslashes(htmlspecialchars($cformsSettings['global']['cforms_upload_err4'])); ?></textarea></td></tr></table>
					</td>
				</tr>
				</table>
			</div>
		</fieldset>


		<fieldset id="wpeditor" class="cformsoptions">
			<div class="cflegend op-closed" id="p12" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('WP Editor Button support', 'cforms2')?>
            </div>

			<div class="cf-content" id="o12">
				<p><?php _e('If you would like to use editor buttons to insert your cforms please enable them below.', 'cforms2') ?></p>

				<table class="form-table">
				<tr class="ob">
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_show_quicktag" name="cforms_show_quicktag" <?php if($cformsSettings['global']['cforms_show_quicktag']=="1") echo "checked=\"checked\""; ?>/> <label for="cforms_show_quicktag"><strong><?php _e('Enable TinyMCE', 'cforms2') ?></strong> <?php _e('&amp; Code editor buttons', 'cforms2') ?></label></td>
				</tr>
				</table>
			</div>
		</fieldset>
		<?php
			$cap = $cformsSettings['global']['cforms_captcha_def'];
			if (class_exists('cforms2_really_simple_captcha', false)) :
		?>
		<fieldset id="captcha" class="cformsoptions">
			<div class="cflegend op-closed" id="p26" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('CAPTCHA Image Settings', 'cforms2')?>
            </div>

			<div class="cf-content" id="o26">

				<?php
					$h = cforms2_prep( $cap['h'],25 );
					$w = cforms2_prep( $cap['w'],115 );
					$c = cforms2_prep( $cap['c'],'#000066' );
					$l = cforms2_prep( $cap['l'],'#000066' );
					$f1 = cforms2_prep( $cap['f1'],17 );
					$f2 = cforms2_prep( $cap['f2'],19 );
					$c1 = cforms2_prep( $cap['c1'],4 );
					$c2 = cforms2_prep( $cap['c2'],5 );
					$ac = cforms2_prep( $cap['ac'],'abcdefghijkmnpqrstuvwxyz23456789' );

				?>

				<table class="form-table">

				<tr class="ob">
					<td class="obL"><label for="cforms_cap_fo"><strong><?php _e('Force display', 'cforms2') ?></strong></label></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_cap_fo" name="cforms_cap_fo" value="1" <?php if($cap['fo']) echo "checked=\"checked\""; ?>/><label for="cforms_cap_fo"><?php _e('Force CAPTCHA display for logged in users', 'cforms2') ?></label></td>
				</tr>

				<tr class="ob space15">
					<td class="obL"><label for="cforms_cap_w"><strong><?php _e('Width', 'cforms2') ?></strong></label></td>
					<td class="obR">
						<input class="cap" type="text" id="cforms_cap_w" name="cforms_cap_w" value="<?php echo $w; ?>"/>
						<label for="cforms_cap_h" class="second-l"><strong><?php _e('Height', 'cforms2') ?></strong></label><input class="cap" type="text" id="cforms_cap_h" name="cforms_cap_h" value="<?php echo $h; ?>"/>
					</td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="inputID1"><strong><?php _e('Background Color', 'cforms2') ?></strong></label></td>
					<td class="obR">
						<input class="cap colorpicker" type="text" id="inputID1" name="cforms_cap_l" value="<?php echo $l; ?>"/>
					</td>
				</tr>

				<tr class="ob space15">
					<td class="obL">&nbsp;</td>
                    <td class="obR"><strong><?php _e('Font Type', 'cforms2') ?></strong></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_cap_f1"><strong><?php _e('Min Size', 'cforms2') ?></strong></label></td>
					<td class="obR">
						<input class="cap" type="text" id="cforms_cap_f1" name="cforms_cap_f1" value="<?php echo $f1; ?>"/>
						<label for="cforms_cap_f2" class="second-l"><strong><?php _e('Max Size', 'cforms2') ?></strong></label><input class="cap" type="text" id="cforms_cap_f2" name="cforms_cap_f2" value="<?php echo $f2; ?>"/>
					</td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="inputID2"><strong><?php _e('Color', 'cforms2') ?></strong></label></td>
					<td class="obR">
						<input class="cap colorpicker" type="text" id="inputID2" name="cforms_cap_c" value="<?php echo $c; ?>"/>
					</td>
				</tr>

				<tr class="ob space15">
					<td class="obL">&nbsp;</td>
                    <td class="obR"><strong><?php _e('Number of shown characters', 'cforms2') ?></strong></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_cap_c1"><strong><?php _e('Minimum', 'cforms2') ?></strong></label></td>
					<td class="obR">
						<input class="cap" type="text" id="cforms_cap_c1" name="cforms_cap_c1" value="<?php echo $c1; ?>"/>
						<label for="cforms_cap_c2" class="second-l"><strong><?php _e('Maximum', 'cforms2') ?></strong></label><input class="cap" type="text" id="cforms_cap_c2" name="cforms_cap_c2" value="<?php echo $c2; ?>"/>
					</td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_cap_ac"><strong><?php _e('Allowed characters', 'cforms2') ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_cap_ac" name="cforms_cap_ac" value="<?php echo $ac; ?>"/></td>
				</tr>
				</table>
			</div>
		</fieldset>
		<?php endif; ?>

		<fieldset id="visitorv" class="cformsoptions">
			<div class="cflegend op-closed" id="p13" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Visitor Verification Settings (Q&amp;A)', 'cforms2')?>
            </div>

			<div class="cf-content" id="o13">
				<p><?php echo sprintf(__('Getting a lot of <strong>SPAM</strong>? Use these Q&amp;A\'s to counteract spam and ensure it\'s a human submitting the form. To use in your form, add the corresponding input field %s preferably in its own FIELDSET!', 'cforms2'), '<code>' . __('Visitor verification (Q&amp;A)', 'cforms2') . '</code>'); ?></p>

				<table class="form-table">
				<tr class="ob">
					<td class="obL"><label for="cforms_cap_foqa"><strong><?php _e('Force display', 'cforms2') ?></strong></label></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_cap_foqa" name="cforms_cap_foqa" value="1" <?php if($cap['foqa']) echo "checked=\"checked\""; ?>/><label for="cforms_cap_foqa"><?php _e('Force Q&amp;A display for logged in users', 'cforms2') ?></label></td>
				</tr>

				<tr class="ob space15">
					<td class="obL">&nbsp;</td>
					<td class="obR"><a class="infobutton" href="#" name="it12"><?php _e('Note &raquo;', 'cforms2'); ?></a></td>
				</tr>
				<tr id="it12" class="infotxt"><td>&nbsp;</td><td class="ex">
							<?php _e('The below error/failure message is also used for <strong>captcha</strong> verification!', 'cforms2') ?><br />
				</td></tr>

				<tr class="ob space15">
					<td class="obL"><label for="cforms_codeerr"><?php _e('<strong>Failure message</strong><br />(for a wrong answer)', 'cforms2'); ?></label></td>
					<td class="obR">
						<table><tr><td><textarea name="cforms_codeerr" id="cforms_codeerr" ><?php echo stripslashes(htmlspecialchars($cformsSettings['global']['cforms_codeerr'])); ?></textarea></td></tr></table>
					</td>
				</tr>

				<?php $qa = stripslashes(htmlspecialchars($cformsSettings['global']['cforms_sec_qa'])); ?>

				<tr class="ob">
					<td class="obL"><label for="cforms_sec_qa"><?php _e('<strong>Questions &amp; Answers</strong><br />format: Q=A', 'cforms2') ?></label></td>
					<td class="obR"><table><tr><td><textarea name="cforms_sec_qa" id="cforms_sec_qa" ><?php echo $qa; ?></textarea></td></tr></table></td>
				</tr>
				</table>
			</div>
		</fieldset>


		<fieldset id="tracking" class="cformsoptions">
			<div class="cflegend op-closed" id="p14" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Database Input Tracking', 'cforms2')?>
            </div>

			<div class="cf-content" id="o14">
				<p><?php _e('If you like to track your form submissions also via the database, please enable this feature below. If required, two new tables will be created and you\'ll see a new sub tab "<strong>Tracking</strong>" under the cforms menu.', 'cforms2') ?></p>

				<table class="form-table">
				<tr class="ob">
					<td class="obL">&nbsp;</td>
					<td class="obR"><a class="infobutton" href="#" name="it13"><?php _e('Note &raquo;', 'cforms2'); ?></a></td>
				</tr>
				<tr id="it13" class="infotxt"><td>&nbsp;</td><td class="ex"><?php echo sprintf(__('If you\'ve enabled the <a href="%s" %s>auto confirmation message</a> feature or have included a <code>CC: me</code> input field, you can optionally configure the subject line/message of the email to include the form tracking ID by using the variable <code>{ID}</code>.', 'cforms2'),'?page=' . $plugindir . '/cforms-options.php#autoconf','onclick="setshow(5)"'); ?></td></tr>

				<tr class="ob space15">
					<td class="obL"><label for="cforms_database"><strong><?php _e('Enable Database Tracking', 'cforms2') ?></strong></label></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_database" name="cforms_database" <?php if($cformsSettings['global']['cforms_database']=="1") echo "checked=\"checked\""; ?>/> <?php _e('Will create two new tables in your WP database.', 'cforms2') ?></td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_showdashboard"><strong><?php _e('Show on dashboard', 'cforms2') ?></strong></label></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_showdashboard" name="cforms_showdashboard" <?php if($cformsSettings['global']['cforms_showdashboard']=="1") echo "checked=\"checked\""; ?>/> <?php _e('Make sure to enable your forms individually as well!', 'cforms2') ?></td>
				</tr>
				</table>
			</div>
		</fieldset>

	    <div class="cf_actions" id="cf_actions" style="display:none;">
			<input id="cfbar-showinfo" class="allbuttons addbutton" type="submit" name="showinfo" value=""/>
			<input id="cfbar-deleteall" class="jqModalDelAll allbuttons deleteall" type="button" name="deleteallbutton" value=" "/>
			<input id="deletetables" class="allbuttons deleteall" type="submit" name="deletetables" value=""/>
			<input id="backup" type="button" class="jqModalBackup allbuttons" name="backup"  value=" "/>
			<input id="cfbar-SubmitOptions" type="submit" name="SubmitOptions" class="allbuttons updbutton formupd" value="" />
	    </div>
		
	</form>

	<?php endif; ### not showing debug msgs. ?> 
	
	<?php cforms2_footer(); ?>
</div>

<div class="jqmWindow" id="cf_backupbox">
    <div class="cf_ed_header"><?php _e('Backup &amp; Restore All Settings','cforms2'); ?></div>
    <div class="cf_ed_main_backup">
        <form enctype="multipart/form-data" name="backupform" method="post">
            <div class="controls">

				<p class="ex"><?php _e('Restoring all settings will overwrite all form specific &amp; global settings!', 'cforms2') ?></p>
				<p>
                	<input type="submit" name="saveallcformsdata" title="<?php _e('Backup all settings now!', 'cforms2') ?>" class="allbuttons" value="<?php _e('Backup all settings now!', 'cforms2') ?>" onclick="javascript:jQuery('#cf_backupbox').jqmHide();"/>&nbsp;&nbsp;&nbsp;
                	<input type="file" id="importall" name="importall" size="25" /><input type="submit" name="restoreallcformsdata" title="<?php _e('Restore all settings now!', 'cforms2') ?>" class="allbuttons deleteall" value="<?php _e('Restore all settings now!', 'cforms2') ?>" onclick="return confirm('<?php _e('With a broken backup file, this action may erase all your settings! Do you want to continue?', 'cforms2') ?>');"/>
				</p>
				<em><?php _e('PS: Individual form configurations can be backup up on the respective form admin page.', 'cforms2') ?></em>
                <p class="cancel"><a href="#" class="jqmClose dashicons dashicons-no-alt" title="<?php _e('Cancel', 'cforms2') ?>"></a></p>

            </div>
            <input type="hidden" name="noSub" value="<?php if (!empty($noDISP)) echo $noDISP; ?>"/>
        </form>
    </div>
</div>
<div class="jqmWindow" id="cf_delall_dialog">
    <div class="cf_ed_header"><?php _e('Uninstalling / Removing cforms','cforms2'); ?></div>
    <div class="cf_ed_main_backup">
        <form name="deleteform" method="post">
            <div id="cf_target_del"><?php _e('Warning!','cforms2'); ?></div>
            <div class="controls">
				<p><?php _e('Generally, simple deactivation of cforms does <strong>not</strong> erase any of its data. If you like to quit using cforms for good, please erase all data before deactivating the plugin.', 'cforms2') ?></p>
				<p><strong><?php _e('This is irrevocable!', 'cforms2') ?></strong>&nbsp;&nbsp;&nbsp;<br />
					 <input type="submit" name="cfdeleteall" title="<?php _e('Are you sure you want to do this?!', 'cforms2') ?>" class="allbuttons deleteall" value="<?php _e('DELETE *ALL* CFORMS DATA', 'cforms2') ?>" onclick="return confirm('<?php _e('Final Warning!', 'cforms2') ?>');"/></p>

                <p class="cancel"><a href="#" class="jqmClose dashicons dashicons-no-alt" title="<?php _e('Cancel', 'cforms2') ?>"></a></p>
            </div>
        </form>
    </div>
</div>

<?php

### strip stuff
function cforms2_prep($v,$d) {
	return ($v<>'')?stripslashes(htmlspecialchars($v)):$d;
}
