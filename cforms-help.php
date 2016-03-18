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

require_once(plugin_dir_path(__FILE__) . 'lib_functions.php');

$cformsSettings = get_option('cforms_settings');

$plugindir   = dirname(plugin_basename(__FILE__));

?>
<div class="wrap" id="top">

		<div id="icon-cforms-help" class="icon32"><br/></div><h2><?php _e('Help','cforms2')?></h2>

		<p>
        	<?php _e('Here you\'ll find plenty of examples and documentation that should help you configure <strong>cforms</strong>.', 'cforms2'); ?>
		</p>

		<p class="cftoctitle"><?php _e('Table of Contents', 'cforms2'); ?></p>
		<ul class="cftoc">
			<li><a href="#guide" onclick="setshow(17)"><?php _e('Basic steps, a small guide', 'cforms2'); ?></a> &raquo;</li>
			<li><a href="#inserting" onclick="setshow(18)"><?php _e('Inserting a form', 'cforms2'); ?></a> &raquo;</li>
			<li><a href="#fields" onclick="setshow(19)"><?php _e('Configuring form input fields', 'cforms2'); ?></a> &raquo;
			<ul style="margin-top:7px	">
				<li><a href="#qa" onclick="setshow(19)"><?php _e('SPAM protection: Q &amp; A', 'cforms2'); ?></a> &raquo;</li>
				<li><a href="#hfieldsets" onclick="setshow(19)"><?php _e('Fieldsets', 'cforms2'); ?></a> &raquo;</li>
				<li><a href="#regexp" onclick="setshow(19)"><?php _e('Using regular expressions with form fields', 'cforms2'); ?></a> &raquo;</li>
			</ul></li>
			<li><a href="#customerr" onclick="setshow(20)"><?php _e('Custom error messages &amp; input field titles', 'cforms2'); ?></a> &raquo;</li>
			<li><a href="#hook" onclick="setshow(21)"><?php _e('Advanced: cforms APIs &amp; (Post-)Processing of submitted data', 'cforms2'); ?></a> &raquo;</li>
			<li><a href="#dynamicforms" onclick="setshow(22)"><?php _e('Advanced: Real-time creation of dynamic forms', 'cforms2'); ?></a> &raquo;</li>
			<li><a href="#variables" onclick="setshow(23)"><?php _e('Using variables in email subjects &amp; messages', 'cforms2'); ?></a> &raquo;</li>
			<li><a href="#multipage" onclick="setshow(30)"><?php _e('Multi page forms', 'cforms2'); ?></a> &raquo;</li>
			<li><a href="#CSS" onclick="setshow(24)"><?php _e('Styling your forms', 'cforms2'); ?></a> &raquo;</li>
			<li><a href="#troubles" onclick="setshow(25)"><?php _e('Need more help?', 'cforms2'); ?></a> &raquo;</li>
		</ul>

        <div class="cflegend op-closed" id="p17" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
            <a id="guide" class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Basic steps, a small guide', 'cforms2')?>
        </div>

		<div class="cf-content" id="o17">
			<p><?php _e('Admittedly, <strong>cforms</strong> is not the easiest form mailer plugin but it may be the most flexible. The below outline should help you get started with the default form.', 'cforms2'); ?></p>
			<ol style="margin:10px 0 0 100px;">
				<li><?php echo sprintf(__('First take a look at the <a href="%s">default form</a>', 'cforms2'),'?page=' . $plugindir . '/cforms-options.php#anchorfields'); ?>
					<ul style="margin:10px 0 0 30px;">
						<li><?php _e('Verify that it contains all the fields you need, are they in the right order', 'cforms2'); ?> <img style="vertical-align:middle;" src="<?php echo plugin_dir_url(__FILE__); ?>images/move.png" alt="" title=""/>?</li>
						<li><?php _e('Check the field labels (field names), if needed make your adjustments', 'cforms2'); ?> &nbsp;<button type="button" name="wrench" style="vertical-align:middle;" disabled="disabled" class="wrench">&#xF111;</button> </li>
						<li><?php _e('Check the flags for each field (check boxes to the right).', 'cforms2'); ?></li>
						<li><?php echo sprintf(__('Want to include SPAM protection? Choose between <a href="%s" %s>Q&amp;A</a>, <a href="%s" %s>captcha</a> add an input field accordingly and configure <a href="%s" %s>here</a>.', 'cforms2'),'#qa','onclick="setshow(19)"','https://wordpress.org/plugins/cforms2-really-simple-captcha/','','?page=' . $plugindir . '/cforms-global-settings.php#visitorv','onclick="setshow(13)"'); ?></li>
					</ul>
				</li>
				<li><?php echo sprintf(__('Check if the <a href="%s" %s>email admin</a> for your form is configured correctly.', 'cforms2'),'?page=' . $plugindir . '/cforms-options.php#anchoremail','onclick="setshow(2)"'); ?></li>
				<li><?php echo sprintf(__('Decide if you want the visitor to receive an <a href="%s" %s>auto confirmation message</a> upon form submission.', 'cforms2'),'?page=' . $plugindir . '/cforms-options.php#autoconf','onclick="setshow(5)"'); ?></li>
				<li><?php echo sprintf(__('Would you like <a href="%s" %s>to track</a> form submission via the database?', 'cforms2'),'?page=' . $plugindir . '/cforms-global-settings.php#tracking','onclick="setshow(14)"'); ?></li>
				<li><?php echo sprintf(__('<a href="%s" %s>Add the default form</a> to a post or page.', 'cforms2'),'#inserting','onclick="setshow(18)"'); ?></li>
				<li><?php _e('Give it a whirl.', 'cforms2'); ?></li>
			</ol>
		</div>


		<div class="cflegend op-closed" id="p18" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
        	<a id="inserting" class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Inserting a form', 'cforms2')?>
        </div>

		<div class="cf-content" id="o18">
			<h3><strong><?php _e('Editing posts and pages:', 'cforms2'); ?></strong></h3>

			<p><?php echo sprintf(__('If you like to do it the \'code\' way, make sure to use %1s to include them in your <em>Pages/Posts</em>. With %2s being <u>your form NAME</u>.', 'cforms2'),'shortcode <code>[cforms name="XYZ"]</code>','<code>XYZ</code>'); ?></p>
			<p><?php echo sprintf(__('A more elegant and safer way is to use the <strong>TinyMCE Button</strong> (double check if <a href="%3s" %s>Button Support</a> is enabled!).', 'cforms2'),'?page=' . $plugindir . '/cforms-global-settings.php#wpeditor','onclick="setshow(12)"'); ?></p>


			<h3><strong><?php _e('Via PHP function call:', 'cforms2'); ?></strong></h3>
			<p><?php echo sprintf(__('Alternatively, you can specifically insert a form (into the sidebar for instance etc.) per the PHP function call %1s, or alternatively %2s for the default/first form and/or %2s for any other form.', 'cforms2'),'<code>insert_cform(\'XYZ\');</code>','<code>insert_cform();</code>','<code>insert_cform(\'<span style="color:red; font-weight:bold;">X</span>\');</code>'); ?></p>

			<p class="ex"><strong><?php _e('Note:', 'cforms2'); ?></strong> <?php echo sprintf(__('"%1s" represents the number of the form, starting with %2s ..and so forth.', 'cforms2'),'<span style="color:red; font-weight:bold;">X</span>','<span style="color:red; font-weight:bold;">2</span>, 3,4'); ?></p>
		</div>


		<div class="cflegend op-closed" id="p19" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
        	<a id="fields" class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Configuring form input fields', 'cforms2')?>
        </div>

		<div class="cf-content" id="o19">
			<p><?php echo sprintf(__('All supported input fields are listed below, highlighting the expected <em><u>formats</u></em> for their associated %sField Names <sup>*)</sup>%s. Form labels (<em>Field Names</em>) permit the use of <strong>HTML</strong>, see examples below.', 'cforms2'),'<a class="infobutton" href="#" name="it14">','</a>'); ?></p>

			<p class="ex" style="display:none; width:400px;" id="it14"><?php _e('While the <em>Field Names</em> are usually just the label of a field (e.g. "Your Name"), they can contain additional information to support special functionality (e.g. default values, regular expressions for extended field validation etc.)', 'cforms2'); ?></p>
			<p class="helpimg" style="width:400px;"><img src="<?php echo plugin_dir_url(__FILE__); ?>images/example-wizard.png"  alt=""/><br /><?php _e('A new <em>wizard like</em> mode allows you to configure more complex settings in case all the pipes "|" and pounds "#" are overwhelming.', 'cforms2'); ?></p>

			<ul style="margin:10px 0 0 100px; list-style:square;">
				<li><a href="#textonly" onclick="setshow(19)"><?php 	_e('Text only elements', 'cforms2'); ?></a></li>
				<li><a href="#datepicker" onclick="setshow(19)"><?php _e('Javascript Date Picker input field', 'cforms2'); ?></a></li>
				<li><a href="#single" onclick="setshow(19)"><?php 	_e('Single-, Password &amp; Multi-line fields', 'cforms2'); ?></a></li>
				<li><a href="#select" onclick="setshow(19)"><?php 	_e('Select / drop down box &amp; radio buttons', 'cforms2'); ?></a></li>
				<li><a href="#multiselect" onclick="setshow(19)"><?php _e('Multi-select box', 'cforms2'); ?></a></li>
				<li><a href="#check" onclick="setshow(19)"><?php 		_e('Check boxes', 'cforms2'); ?></a></li>
				<li><a href="#checkboxgroup" onclick="setshow(19)"><?php _e('Check box groups', 'cforms2'); ?></a></li>
				<li><a href="#ccme" onclick="setshow(19)"><?php 		_e('CC:me check box', 'cforms2'); ?></a></li>
				<li><a href="#multirecipients" onclick="setshow(19)"><?php _e('Multiple recipients drop down box', 'cforms2'); ?></a></li>
				<li><a href="#hidden" onclick="setshow(19)"><?php 	_e('Hidden fields', 'cforms2'); ?></a></li>
				<li><a href="#qa" onclick="setshow(19)"><?php 		_e('SPAM protection: Q&amp;A input field', 'cforms2'); ?></a></li>
				<li><a href="#upload" onclick="setshow(19)"><?php 	_e('File attachments / upload', 'cforms2'); ?></a></li>
			</ul>


		<br style="clear:both;"/>

		<p class="fieldtitle" id="textonly">
			<span class="h4ff"><?php _e('form<br />field', 'cforms2'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a>
			<?php _e('Text only elements (no input)', 'cforms2'); ?>
		</p>
		<img class="helpimg" src="<?php echo plugin_dir_url(__FILE__); ?>images/example-text.png"  alt=""/>
		<table class="hf">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
				<td class="bright"><?php echo sprintf(__('text paragraph %1$s css class %1$s optional style', 'cforms2'),'<span style="color:red; font-weight:bold;">|</span>'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright"><code><?php _e('Please make sure...', 'cforms2'); ?>|mytextclass|font-size:9x; font-weight:bold;</code></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright"><code><?php echo sprintf(__('Check %s here %s for more info. %s', 'cforms2'),'&lt;a href="http://mysite.com"&gt;','&lt;/a&gt;','||font-size:9x;'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2"><?php _e('HTML: the <code>text paragraph</code> supports HTML. If you need actual &lt;, &gt; in your text please use the proper HTML entity.', 'cforms2'); ?></td>
			</tr>
			<tr>
				<td class="ball" colspan="2"><?php _e('The above expression applies the custom class "<code>mytextclass</code>" <strong>AND</strong> the specific styles "<code>font-size:9x; font-weight:bold;</code>" to the paragraph.', 'cforms2'); ?></td>
			</tr>
			<tr>
				<td class="ball" colspan="2"><?php echo sprintf(__('If you specify a <code>css class</code>, you also need to define it in your current form theme file, <a href="%s">here</a>.', 'cforms2'),'?page=' . $plugindir . '/cforms-css.php'); ?></td>
			</tr>
		</table>

		<br style="clear:both;"/>

		<p class="fieldtitle" id="datepicker">
			<span class="h4ff"><?php _e('form<br />field', 'cforms2'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a>
			<?php _e('Javascript Date Picker', 'cforms2'); ?>
		</p>
		<table class="hf">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
				<td class="bright"><?php echo sprintf(__('field name %1$s default value %1$s <a href="%2$s">regular expression</a>', 'cforms2'),'<span style="color:red; font-weight:bold;">|</span>','#regexp'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright">
					<code><?php _e('Arrival Date', 'cforms2'); echo '|'; echo cforms2_admin_date_format(); ?>|^[0-9][0-9]/[0-9][0-9]/[0-9][0-9][0-9][0-9]$</code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php echo sprintf(__('The example above will set a <em>default value</em> of "%s" so users know the expected format. The <strong>regexp</strong> at the end ensures that only this format is accepted. <strong>NOTE:</strong> You also need to <a href="%s" %s>configure the date picker options</a> to match the date format ("%s")!', 'cforms2'), cforms2_admin_date_format(), '?page=' . $plugindir . '/cforms-global-settings.php#datepicker','onclick="setshow(9)"', cforms2_admin_date_format()); ?>
				</td>
			</tr>
		</table>

		<br style="clear:both;"/>

		<p class="fieldtitle" id="single">
			<span class="h4ff"><?php _e('form<br />field', 'cforms2'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a>
			<?php _e('Single, Password &amp; Multi line input fields', 'cforms2'); ?>
		</p>
		<img class="helpimg" src="<?php echo plugin_dir_url(__FILE__); ?>images/example-single.png"  alt=""/>
		<table class="hf">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
				<td class="bright"><?php echo sprintf(__('field name %1$s default value %1$s <a href="%2$s">regular expression</a>', 'cforms2'),'<span style="color:red; font-weight:bold;">|</span>','#regexp'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright">
					<code><?php _e('Reference', 'cforms2'); ?>#|xxx-xx-xxx|^[0-9A-Z-]+$</code></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright">
					<code><?php _e('Your &lt;u&gt;Full&lt;/u&gt; Name', 'cforms2'); ?>||^[A-Za-z- \.]+$</code></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright">
					<code>&lt;acronym title="<?php echo sprintf(__('We need your email address for confirmation."%sYour EMail', 'cforms2'),'&gt;'); ?>&lt;/acronym&gt;</code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('You can of course omit the <em>default value</em> as in Example 2.', 'cforms2'); ?>
				</td>
			</tr>
		</table>


		<br style="clear:both;"/>


		<p class="fieldtitle" id="select">
			<span class="h4ff"><?php _e('form<br />field', 'cforms2'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a>
			<?php _e('Select boxes &amp; radio buttons', 'cforms2'); ?>
		</p>
		<img class="helpimg" src="<?php echo plugin_dir_url(__FILE__); ?>images/example-dropdown.png"  alt=""/>
		<table class="hf">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
				<td class="bright"><?php echo sprintf(__('field name %1$s option1 %2$s value1 %1$s option2 %2$s value2 %1$s option3...', 'cforms2'),'<span style="color:red; font-weight:bold;">#</span>','<span style="color:red; font-weight:bold;">|</span>'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright">
					<code><?php _e('Your age#12-18|kiddo#19 to 30|young#31 to 45#45+ |older', 'cforms2'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Omitting the <code>field name</code> will result in not showing a label to the left of the field.', 'cforms2'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('The <strong>option</strong> parameter determines the text displayed to the visitor, <strong>value</strong> what is being sent in the email.', 'cforms2'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Is no <strong>value</strong> explicitly given, then the shown option text is the value sent in the email.', 'cforms2'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Note:', 'cforms2'); ?><br /><?php echo sprintf(__('<strong>Select box marked "Required":</strong> Using a minus symbol %1$s for the value (after %2$s), will mark an option as invalid! Example:<br /><code>Your age#Please pick your age group|-#12 to 18|kiddo#19 to 30|young#31 to 45#45+ |older</code>. <br />"Please pick..." is shown but not considered a valid value.', 'cforms2'),'<code>-</code>','<span style="color:red; font-weight:bold;">|</span>'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Note:', 'cforms2'); ?><br /><?php echo sprintf(__('<strong>Radio buttons marked "Required":</strong> You can choose to not preselect a radio button upon form load, yet make a user selection mandatory for the form to validate.', 'cforms2')); ?>
				</td>
			</tr>
		</table>


		<br style="clear:both;"/>

		<p class="fieldtitle" id="multiselect">
			<span class="h4ff"><?php _e('form<br />field', 'cforms2'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a>
			<?php _e('Multi select boxes', 'cforms2'); ?>
		</p>
		<img class="helpimg" src="<?php echo plugin_dir_url(__FILE__); ?>images/example-ms.png"  alt=""/>
		<table class="hf">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
				<td class="bright"><?php echo sprintf(__('field name %1$s option1 %2$s value1 %1$s option2 %2$s value2 %1$s option3...', 'cforms2'),'<span style="color:red; font-weight:bold;">#</span>','<span style="color:red; font-weight:bold;">|</span>'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright">
					<code><?php _e('Pick#red#blue#green#yellow#orange', 'cforms2'); ?></code></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright">
					<code><?php _e('&lt;strong&gt;Select&lt;/strong&gt;#Today#Tomorrow#This Week#Next Month#Never', 'cforms2'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Multi select fields can be set to <strong>Required</strong>. If so and unless at least one option is selected the form won\'t validate.', 'cforms2'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('If <code>value1,2,..</code> are not specified, the values delivered in the email default to <code>option1,2,...</code>.', 'cforms2'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Examples for specific values could be the matching color codes: e.g. <code>red|#ff0000</code>', 'cforms2'); ?>
				</td>
			</tr>
		</table>


		<br style="clear:both;"/>


		<p class="fieldtitle" id="check">
			<span class="h4ff"><?php _e('form<br />field', 'cforms2'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a>
			<?php _e('Check boxes', 'cforms2'); ?>
		</p>
		<img class="helpimg" src="<?php echo plugin_dir_url(__FILE__); ?>images/example-checkbox.png"  alt=""/>
		<table class="hf">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
				<td class="bright"><?php echo sprintf(__('field name <u>left</u> %s field name <u>right</u>', 'cforms2'),'<span style="color:red; font-weight:bold;">#</span>'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright">
					<code><?php _e('#please check if you\'d like more information', 'cforms2'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('You can freely choose on which side of the check box the label appears (e.g. <code>#label-right-only</code>).', 'cforms2'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('If <strong>both</strong> left and right labels are provided, only the <strong>right one</strong> will be considered.', 'cforms2'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Check boxes can be flagged "<strong>Required</strong>" to support special use cases, e.g.: when you require the visitor to confirm that he/she has read term &amp; conditions, before submitting the form.', 'cforms2'); ?>
				</td>
			</tr>
		</table>


		<br style="clear:both;"/>


		<p class="fieldtitle" id="checkboxgroup">
			<span class="h4ff"><?php _e('form<br />field', 'cforms2'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a>
			<?php _e('Check box groups', 'cforms2'); ?>
		</p>
		<img class="helpimg" src="<?php echo plugin_dir_url(__FILE__); ?>images/example-grp.png"  alt=""/>
		<table class="hf">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
				<td class="bright"><?php echo sprintf(__('field name %1$s chk box1 label%2$schk box1 value %1$s chk box2 label %3$s chk box3...', 'cforms2'),'<span style="color:red; font-weight:bold;">#</span>','<span style="color:red; font-weight:bold;">|</span>','<span style="color:red; font-weight:bold;">##</span>'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright">
					<code><?php _e('Select Color#green|00ff00 #red|ff0000 #purple|8726ac #yellow|fff90f', 'cforms2'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Two # (<code>##</code>) in a row will force a new line! This helps to better structure your check box group.', 'cforms2'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Similar to <strong>multi-select boxes</strong> (see above), <strong>Check box groups</strong> allow you to deploy several check boxes (with their labels and corresponding values) that form one logical field. The result submitted via the form email is a single line including all checked options.', 'cforms2'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php echo sprintf(__('If no explicit <strong>value</strong> (text after the pipe symbol \'%1$s\') is specified, the provided check box label is both label &amp; submitted value.', 'cforms2'),'<span style="color:red; font-weight:bold;">|</span>'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('None of the check boxes within a group can be made "Required".', 'cforms2'); ?>
				</td>
			</tr>
		</table>


		<br style="clear:both;"/>


		<p class="fieldtitle" id="ccme">
			<span class="h4ff"><?php _e('form<br />field', 'cforms2'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a>
			<?php _e('CC: option for visitors', 'cforms2'); ?>
		</p>
		<img class="helpimg" src="<?php echo plugin_dir_url(__FILE__); ?>images/example-cc.png"  alt=""/>
		<table class="hf">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
				<td class="bright"><?php echo sprintf(__('field name <u>left</u> %s field name <u>right</u>', 'cforms2'),'<span style="color:red; font-weight:bold;">#</span>'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright">
					<code><?php _e('#please cc: me', 'cforms2'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php echo sprintf(__('If the visitor chooses to be CC\'ed, <strong>no</strong> additional auto confirmation email (<a href="%s" %s>if configured</a>) is sent out!', 'cforms2'),'?page=' . $plugindir . '/cforms-options.php#autoconf','onclick="setshow(5)"'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Please also see <em>check boxes</em> above.', 'cforms2'); ?>
				</td>
			</tr>
		</table>


		<br style="clear:both;"/>


		<p class="fieldtitle" id="multirecipients">
			<span class="h4ff"><?php _e('form<br />field', 'cforms2'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a>
			<?php _e('Multiple form mail recipients', 'cforms2'); ?>
		</p>


		<img class="helpimg" src="<?php echo plugin_dir_url(__FILE__); ?>images/example-multi.png"  alt=""/>
		<table class="hf">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
				<td class="bright"><?php echo sprintf(__('field name %1$s Name1 | email address(es) %1$s Name2 | email address(es)%1$s Name3...', 'cforms2'),'<span style="color:red; font-weight:bold;">#</span>'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright">
					<code><?php _e('Send to#Joe|joe@mail.com#Pete|pete@mail.com#Hillary|hillary@mail.com', 'cforms2'); ?></code></td>
			</tr>
			<tr>
				<td class="bleft"></td><td class="bright">
					<code><?php _e('Send to#Sales|sales1@mail.com, sales2@mail.com, sales3@mail.com#Support|admin@mail.com#HR|hr1@mail.scom, hr2@mail.com', 'cforms2'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Adding the above field to a form, disables the form\'s specific admin email address setting.', 'cforms2'); ?>
				</td>
			</tr>
		</table>


		<br style="clear:both;"/>

		<p class="fieldtitle" id="hidden">
			<span class="h4ff"><?php _e('form<br />field', 'cforms2'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a>
			<?php _e('Hidden input fields', 'cforms2'); ?>
		</p>
		<table class="hf">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
				<td class="bright"><?php echo sprintf(__('field name %1$s default value', 'cforms2'),'<span style="color:red; font-weight:bold;">|</span>'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright">
					<code><?php _e('extra-data', 'cforms2'); ?>|fixed,hidden text</code></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright">
					<code><?php _e('post-data-meta', 'cforms2'); ?>|{custom_field_1}</code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Hidden fields can contain fixed/preset values or <strong>{variables}</strong> which reference custom fields of posts or pages.', 'cforms2'); ?>
				</td>
			</tr>
		</table>


		<br style="clear:both;"/>


		<p class="fieldtitle" id="qa">
			<span class="h4ff"><?php _e('form<br />field', 'cforms2'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a>
			<?php _e('Visitor verification (Q&amp;A)', 'cforms2'); ?>
		</p>
		<img class="helpimg" src="<?php echo plugin_dir_url(__FILE__); ?>images/example-vv.png"  alt=""/>
		<table class="hf">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
				<td class="bright"><?php _e('--', 'cforms2'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright">
					<code><?php _e('--', 'cforms2'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php echo sprintf(__('No <code>field name</code> required, the field has no configurable label per se, as it is determined at run-time from the list of <strong>Question &amp; Answers</strong> provided <a href="%s" %s>here</a>.', 'cforms2'),'?page=' . $plugindir . '/cforms-global-settings.php#visitorv','onclick="setshow(13)"'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('It makes sense to encapsulate this field inside a FIELDSET, to do that simply add a <code>New Fieldset</code> field before this one.', 'cforms2'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('<strong>PLEASE NOTE</strong> that by default the captcha and visitor verification (Q&amp;A) field are <strong>not</strong> shown for logged in users! This can be changed under Global Settings.', 'cforms2'); ?>
				</td>
			</tr>
		</table>


		<br style="clear:both;"/>


		<p class="fieldtitle" id="upload">
			<span class="h4ff"><?php _e('form<br />field', 'cforms2'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a>
			<?php _e('Attachments / File Upload Box', 'cforms2'); ?>
		</p>
		<img class="helpimg" src="<?php echo plugin_dir_url(__FILE__); ?>images/example-upload.png"  alt=""/>
		<table class="hf">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
				<td class="bright"><?php _e('form label', 'cforms2'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright">
					<code><?php _e('Please select a file', 'cforms2'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php echo sprintf(__('Please double-check the <a href="%s" %s>Global Settings</a> for proper configuration of the <code>File Upload</code> functionality (allowed extensions, file size etc.).', 'cforms2'),'?page='.$plugindir.'/cforms-global-settings.php#upload','onclick="setshow(11)"'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php echo sprintf(__('Please enable <a href="%s" %s>Database Input Tracking</a> on the Global Settings page to ensure a unique upload ID per attachment and to avoid accidentally overwriting an attachment.', 'cforms2'),'?page='.$plugindir.'/cforms-global-settings.php#tracking','onclick="setshow(14)"'); ?>
				</td>
			</tr>
		</table>


		<p class="fieldtitle" id="hfieldsets">
			<span class="h4ff"><?php _e('form<br />field', 'cforms2'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a>
			<?php _e('Fieldsets', 'cforms2'); ?>
		</p>

   		<p style="margin:10px 30px;"><?php _e('Fieldsets are definitely part of good form design, they are form elements that are used to create individual sections of content within a given form.', 'cforms2'); ?></p>

		<img class="helpimg" src="<?php echo plugin_dir_url(__FILE__); ?>images/example-fieldsets.png"  alt=""/>
		<table class="hf">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
				<td class="bright"><?php _e('fieldset name', 'cforms2'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright">
					<code><?php _e('My Fieldset', 'cforms2'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Fieldsets can begin anywhere, simply add a <strong>New Fieldset</strong> field between or before your form elements.', 'cforms2'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Fieldsets do not need to explicitly be closed, a <strong>New Fieldset</strong> element will automatically close the existing (if there is one to close) and reopen a new one.', 'cforms2'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('<strong>End Fieldset</strong> <u>can</u> be used, but it works without just as well.', 'cforms2'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('If there is no closing <strong>End Fieldset</strong> element, the plugin assumes that it needs to close the set just before the submit button', 'cforms2'); ?>
				</td>
			</tr>
		</table>


		<br style="clear:both; "/>


		<p class="fieldtitle" id="regexp">
			<span class="h4ff"><?php _e('form<br />field', 'cforms2'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a>
			<?php _e('Using regular expressions with form fields', 'cforms2'); ?>
		</p>

		<p style="margin:10px 30px;"><?php _e('A regular expression (regex or regexp for short) is a special text string for describing a search pattern, according to certain syntax rules. Many programming languages support regular expressions for string manipulation, you can use them here to validate user input. Single/Multi line input fields:', 'cforms2'); ?></p>

		<!-- no img for regexps-->
		<table class="hf">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
				<td class="bright"><?php echo sprintf(__('field name %1$s default value %1$s regular expression', 'cforms2'),'<span style="color:red; font-weight:bold;">|</span>'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:<br />US zip code', 'cforms2'); ?></td><td class="bright">
					<code><?php _e('zip code', 'cforms2'); ?>||^\d{5}$|^\d{5}-\d{4}$</code></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:<br />US phone #', 'cforms2'); ?></td><td class="bright">
					<code><?php _e('phone', 'cforms2'); ?>||^[\(]?(\d{0,3})[\)]?[\s]?[\-]?(\d{3})[\s]?[\-]?(\d{4})[\s]?[x]?(\d*)$</code></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Special Example:<br />comparing two input fields', 'cforms2'); ?></td><td class="bright">
					<code><?php _e('please repeat email', 'cforms2'); ?>||<span style="color:red">cf2_field_2</span></code></td>
			</tr>
			<tr>
				<td class="bright" colspan="2">
					<?php echo '<strong style="color:red">'.__('Important:','cforms2').'</strong>';?><br />
					<?php _e('<strong>If you need to compare two input fields (e.g. email verification):</strong> simply use the regexp field (see special example above, to point to the <u>HTML element ID</u> of the field you want to compare the current one to. To find the <u>HTML element ID</u> you would have to look into the html source code of the form (e.g.', 'cforms2'); ?> <code style="color:red">cf2_field_2</code>).
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<strong><?php _e('GENERAL:', 'cforms2'); ?></strong>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Ensure that the input field in question is tagged \'<strong>Required</strong>\'!', 'cforms2'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<code>^</code> <?php _e('and', 'cforms2'); ?> <code>$</code> <?php _e('define the start and the end of the input', 'cforms2'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					"<code>ab*</code>": <?php _e('...matches a string that has an "a" followed by zero or more "b\'s" ("a", "ab", "abbb", etc.);', 'cforms2'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					"<code>ab+</code>": <?php _e('...same, but there\'s at least one b ("ab", "abbb", etc.);', 'cforms2'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					"<code>[a-d]</code>": <?php _e('...a string that has lowercase letters "a" through "d"', 'cforms2'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php echo sprintf(__('More information can be found <a href="%s">here</a>, a great regexp repository <a href="%s">here</a>.', 'cforms2'),'http://php.net/manual/en/book.regex.php','http://regexlib.com'); ?>
				</td>
			</tr>
		</table>
	</div>


		<div class="cflegend op-closed" id="p20" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
        	<a id="customerr" class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Custom error messages &amp; input field titles', 'cforms2')?>
        </div>

		<div class="cf-content" id="o20">
			<p><?php echo sprintf(__('On top of their labels, input fields can have titles, too. Simply append a %s to a given field configuration string.', 'cforms2'),'<code>|title:XXX</code>'); ?></p>
			<p><?php echo sprintf(__('If you like to add custom error messages (next to your generic <a href="%s" %s>success</a> and <a href="%s" %s>error</a> messages) for your input fields, simply append a %s to a given <em>definition string/field name</em>. HTML is supported.', 'cforms2'),'?page=' . $plugindir . '/cforms-options.php#cforms_success','onclick="setshow(1)"','?page=' . $plugindir . '/cforms-options.php#cforms_failure','onclick="setshow(1)"','<code>|err:XXX</code>'); ?></p>
			<p class="ex"><?php echo sprintf(__('Please note the order of these special attributes, first %s (if applicable), then %s.', 'cforms2'),'<code>|title:XXX</code>','<code>|err:XXX</code>');?></p>

			<table class="hf">
				<tr>
					<td class="bleft"><span class="abbr" title="<?php _e('Extended entry format for the Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
					<td class="bright"><?php echo sprintf(__('field name %1$s your title here %3$s %2$s your error message %3$s', 'cforms2'),'<span style="color:red; font-weight:bold;">|title:<em>','<span style="color:red; font-weight:bold;">|err:<em>','</em></span>'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><?php _e('Example 1:', 'cforms2'); ?></td><td class="bright">
						<code><?php _e('Your Name|title:Only alphabetic characters allowed!', 'cforms2'); ?></code></td>
				</tr>
				<tr>
					<td class="bleft"><?php _e('Example 2:', 'cforms2'); ?></td><td class="bright">
						<code><?php _e('Your Name|title:Please provide your first and last name!|err:Please enter your full name.', 'cforms2'); ?></code></td>
				</tr>
				<tr>
					<td class="bleft"><?php _e('Example 3:', 'cforms2'); ?></td><td class="bright">
						<code><?php _e('Your age#12-18|kiddo#19 to 30|young#31 to 45#45+ |older', 'cforms2'); ?><?php _e('|err: your age is &lt;strong&gt;important&lt;/strong&gt; to us.', 'cforms2'); ?></code></td>
				</tr>
				<tr>
					<td class="ball" colspan="2">
						<strong><?php _e('Note:', 'cforms2'); ?></strong>
					</td>
				</tr>
				<tr>
					<td class="ball" colspan="2">
						<?php _e('<strong>Custom error messages</strong> can be applied to any input field that can be flagged "<strong>Required</strong>", <strong>titles</strong> to any input field.', 'cforms2'); ?>
					</td>
				</tr>
			</table>
		</div>


		<div class="cflegend op-closed" id="p21" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
        	<a id="hook" class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Advanced: cforms APIs &amp; (Post-)Processing of submitted data', 'cforms2')?>
        </div>

		<div class="cf-content" id="o21">
			<br/>
			<table class="hf">
				<tr>
					<td class="apiH" colspan="2"><span class="abbr"><?php _e('API Function', 'cforms2'); ?></span> &nbsp;&nbsp;&nbsp; <strong>get_cforms_entries(&nbsp;$fname,&nbsp;$from,&nbsp;$to,&nbsp;$sort,&nbsp;$limit&nbsp;,$sortdir&nbsp;)</strong></td>
				</tr>
				<tr>
					<td class="bright" colspan="2"><span class="abbr"><?php _e('Description', 'cforms2'); ?>:</span> &nbsp;&nbsp;&nbsp; <?php _e('This function allows to conveniently retrieve submitted data from the cforms tracking tables.', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bright" colspan="2"><span class="abbr"><?php _e('Parameters', 'cforms2'); ?>:</span></td>
				</tr>
				<tr>
					<td class="bleft"><strong><code class="codehighlight">$fname&nbsp;::&nbsp;<?php _e('[text]', 'cforms2'); ?></code></strong></td>
					<td class="bright"><?php _e('text string (regexp pattern), e.g. the form name', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><strong><code class="codehighlight">$from,&nbsp;$to&nbsp;::&nbsp;<?php _e('[date]', 'cforms2'); ?></code></strong></td>
					<td class="bright"><?php _e('DATETIME string (format: Y-m-d H:i:s). Date &amp; time defining the target period, e.g.', 'cforms2'); ?><strong style="color:red;"> 2008-09-17 15:00:00</strong></td>
				</tr>
				<tr>
					<td class="bleft"><strong><code class="codehighlight">$sort&nbsp;::&nbsp;<?php _e('[text]', 'cforms2'); ?></code></strong></td>
					<td class="bright"><strong style="color:red;">'form'</strong>, <strong style="color:red;">'id'</strong>, <strong style="color:red;">'date'</strong>, <strong style="color:red;">'ip'</strong> <?php _e('or', 'cforms2'); ?> <strong style="color:red;">'email'</strong><?php _e(' or any other form input field, e.g. \'Your Name\'', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><strong><code class="codehighlight">$limit&nbsp;::&nbsp;<?php _e('[number]', 'cforms2'); ?></code></strong></td>
					<td class="bright"><?php _e('limiting the number of results, \'\' (empty or false) = no limits!', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><strong><code class="codehighlight">$sortdir&nbsp;::&nbsp;<?php _e('[text]', 'cforms2'); ?></code></strong></td>
					<td class="bright"><strong style="color:red;">asc</strong>, <strong style="color:red;">desc</strong></td>
				</tr>
				<tr><td class="bright" colspan="2"><span class="abbr"><?php _e('Output', 'cforms2'); ?>:</span></td></tr>
				<tr><td class="bright" colspan="2"><?php _e('This function will return a set of stored form submissions in a multi-dimensional array.', 'cforms2'); ?></td></tr>
				<tr><td class="ball" colspan="2"><span class="abbr"><?php _e('Examples', 'cforms2'); ?></span></td></tr>
				<tr><td class="ball" colspan="2"><code>$array = get_cforms_entries();   /* all data, no filters */</code></td></tr>
				<tr><td class="ball" colspan="2"><code>$array = get_cforms_entries('contact',false,false,'date',5,'desc');   /* last 5 submissions of "my contact form", order by date */</code></td></tr>
				<tr><td class="ball" colspan="2"><code>$array = get_cforms_entries(false,date ("Y-m-d H:i:s", time()-(3600*2)));   /* all submissions in the last 2 hours */</code></td></tr>
				<tr><td class="ball" colspan="2">
                <span class="abbr"><?php _e('Example: Table Output', 'cforms2'); ?></span><br /><br />
                <pre style="font-size: 11px; background:#EAEAEA;">$array = get_cforms_entries();   /* all data, no filters */

echo '&lt;table&gt;';
echo '&lt;tr&gt;&lt;th&gt;Name&lt;/th&gt;&lt;th&gt;Email&lt;/th&gt;&lt;th&gt;Website&lt;/th&gt;&lt;/tr&gt;';
foreach( $array as $e ){
	echo '&lt;tr&gt;&lt;td&gt;' . $e['data']['Your Name'] . '&lt;/td&gt;&lt;td&gt;' . $e['data']['Email'] . '&lt;/td&gt;&lt;td&gt;' . $e['data']['Website'] . '&lt;/td&gt;&lt;tr&gt;';
}
echo '&lt;/table&gt;';</pre></td></tr>
			</table>

		</div>


		<div class="cflegend op-closed" id="p22" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
        	<a id="dynamicforms" class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Advanced: Real-time creation of dynamic forms', 'cforms2')?>
        </div>

		<div class="cf-content" id="o22">
			<p><?php _e('Again, this is for the advanced user who requires ad-hoc creation of forms.', 'cforms2'); ?></p>

			<p><strong><?php _e('A few things to note on dynamic forms:', 'cforms2'); ?></strong></p>
			<ol>
				<li><?php _e('Dynamic forms only work in <strong>non-Ajax</strong> mode.', 'cforms2');?></li>
				<li><?php _e('Each dynamic form references and thus requires <strong>a base form defined</strong> in the cforms form settings. All its settings will be used, except the form (&amp;field) definition.', 'cforms2');?></li>
				<li><?php _e('Any of the form fields described in the plugins\' <strong>HELP!</strong> section can be dynamically generated.', 'cforms2');?></li>
				<li><?php echo sprintf(__('Function call to generate dynamic forms: %s with', 'cforms2'),'<code>insert_custom_cform($fields:array,$form-no:int);</code> ');?>

	                <br /><br />
	                <code>$form-no</code>: <?php _e('empty string for the first (default) form and <strong>2</strong>,3,4... for any subsequent form', 'cforms2'); ?><br />
	                <code>$fields</code> :

	                <pre style="font-size: 11px;"><code style="background:none;">
	            $fields['label'][n]      = '<?php _e('field name', 'cforms2'); ?>';           <?php _e('<em>field name</em> format described above', 'cforms2'); ?>

	            $fields['type'][n]       = 'input field type';     default: 'textfield';
	            $fields['isreq'][n]      = true|false;             default: false;
	            $fields['isemail'][n]    = true|false;             default: false;
	            $fields['isclear'][n]    = true|false;             default: false;
	            $fields['isdisabled'][n] = true|false;             default: false;
	            $fields['isreadonly'][n] = true|false;             default: false;

	            n = 0,1,2...</code></pre></li>
	    	</ol>


	        <strong><?php _e('Form input field types (\'type\'):', 'cforms2'); ?></strong>
	        <ul style="list-style:none;">
	        <li>
	            <table class="cf_dyn_fields">
	                <tr><td><strong><?php _e('Basic fields', 'cforms2'); ?></strong></td><td></td></tr>
	                <tr><td><?php _e('Text paragraph', 'cforms2'); ?>:</td><td> <code>textonly</code></td></tr>
	                <tr><td><?php _e('Single input field', 'cforms2'); ?>:</td><td> <code>textfield</code></td></tr>
	                <tr><td><?php _e('Multi line field', 'cforms2'); ?>:</td><td> <code>textarea</code></td></tr>
	                <tr><td><?php _e('Hidden field', 'cforms2'); ?>:</td><td> <code>hidden</code></td></tr>
	                <tr><td><?php _e('Password field', 'cforms2'); ?>:</td><td> <code>pwfield</code></td></tr>
	                <tr><td><?php _e('Date picker field', 'cforms2'); ?>:</td><td> <code>datepicker</code></td><td class="cf-wh">&nbsp;</td><td><strong><?php _e('WP Comment Feature', 'cforms2'); ?></strong></td><td></td></tr>
	                <tr><td><?php _e('Check boxes', 'cforms2'); ?>:</td><td> <code>checkbox</code></td><td class="cf-wh">&nbsp;</td><td><?php _e('Comment Author', 'cforms2'); ?>:</td><td> <code>author</code></td></tr>
	                <tr><td><?php _e('Check boxes groups', 'cforms2'); ?>:</td><td> <code>checkboxgroup</code></td><td class="cf-wh">&nbsp;</td><td><?php _e('Author\'s Email', 'cforms2'); ?>:</td><td> <code>email</code></td></tr>
	                <tr><td><?php _e('Drop down fields', 'cforms2'); ?>:</td><td> <code>selectbox</code></td><td class="cf-wh">&nbsp;</td><td><?php _e('Author\'s URL', 'cforms2'); ?>:</td><td> <code>url</code></td></tr>
	                <tr><td><?php _e('Multi select boxes', 'cforms2'); ?>:</td><td> <code>multiselectbox</code></td><td class="cf-wh">&nbsp;</td><td><?php _e('Author\'s Comment', 'cforms2'); ?>:</td><td> <code>comment</code></td></tr>
	                <tr><td><?php _e('\'CC\' check box', 'cforms2'); ?> <sup>*)</sup>:</td><td> <code>ccbox</code></td></tr>
	                <tr><td><?php _e('Multi-recipients field', 'cforms2'); ?> <sup>*)</sup>:</td><td> <code>emailtobox</code></td></tr>
	                <tr><td><?php _e('Spam/Q&amp;A verification', 'cforms2'); ?> <sup>*)</sup>:</td><td> <code>cforms2_question_and_answer</code></td></tr>
	                <tr><td><?php _e('File upload fields', 'cforms2'); ?> <sup>*)</sup>:</td><td> <code>upload</code></td></tr>
	                <tr><td><?php _e('Begin of a fieldset', 'cforms2'); ?>:</td><td> <code>fieldsetstart</code></td></tr>
	                <tr><td><?php _e('End of a fieldset', 'cforms2'); ?>:</td><td> <code>fieldsetend</code></td></tr>
	            </table>
	        </li>
	        <li><sup>*)</sup> <em><?php _e('Should only be used <strong>once</strong> per generated form!', 'cforms2'); ?></em></li>
	        </ul>

        <br />

		<a id="ex1"></a>
        <strong><?php _e('Simple example:', 'cforms2'); ?></strong>
        <ul style="list-style:none;">
        <li>
        <pre style="font-size: 11px;"><code style="background:none;">
$fields = array();

$formdata = array(
		array('<?php _e('Your Name|Your Name', 'cforms2'); ?>','textfield',0,1,0,1,0),
		array('<?php _e('Your Email', 'cforms2'); ?>','textfield',0,0,1,0,0),
		array('<?php _e('Your Message', 'cforms2'); ?>','textarea',0,0,0,0,0)
		);

$i=0;
foreach ( $formdata as $field ) {
	$fields['label'][$i]        = $field[0];
	$fields['type'][$i]         = $field[1];
	$fields['isdisabled'][$i]   = $field[2];
	$fields['isreq'][$i]        = $field[3];
	$fields['isemail'][$i]      = $field[4];
	$fields['isclear'][$i]      = $field[5];
	$fields['isreadonly'][$i++] = $field[6];
}

insert_custom_cform($fields,'');    //<?php _e('Call default form with two defined fields', 'cforms2'); ?></code></pre>
        </li>
        </ul>

        <br />

		<a id="ex2"></a>
        <?php _e('<strong>More advanced example</strong> (file access)', 'cforms2'); ?><strong>:</strong>
        <ul style="list-style:none;">
        <li>
        <pre style="font-size:11px"><code style="background:none;">
$fields['label'][0]  ='<?php _e('Your Name|Your Name', 'cforms2'); ?>';
$fields['type'][0]   ='textfield';
$fields['isreq'][0]  ='1';
$fields['isemail'][0]='0';
$fields['isclear'][0]='1';
$fields['label'][1]  ='<?php _e('Email', 'cforms2'); ?>';
$fields['type'][1]   ='textfield';
$fields['isreq'][1]  ='0';
$fields['isemail'][1]='1';
$fields['label'][2]  ='<?php _e('Please pick a month for delivery:', 'cforms2'); ?>||font-size:14px; padding-top:12px; text-align:left;';
$fields['type'][2]   ='textonly';

$fields['label'][3]='<?php _e('Deliver on#Please pick a month', 'cforms2'); ?>|-#';

$fp = fopen(plugin_dir_path(__FILE__).'months.txt', "r"); // <?php _e('Need to put this file into your themes dir!', 'cforms2'); ?>

while ($nextitem = fgets($fp, 512))
	$fields['label'][3] .= $nextitem.'#';

fclose ($fp);

$fields['label'][3]  = substr( $fields['label'][3], 0, strlen($fields['label'][3])-1 );  //<?php _e('Remove the last \'#\'', 'cforms2'); ?>
$fields['type'][3]   ='selectbox';
$fields['isreq'][3]  ='1';
$fields['isemail'][3]='0';

insert_custom_cform($fields,5);    //<?php _e('Call form #5 with new fields', 'cforms2'); ?></code></pre>
        </li>
        </ul>

        <?php _e('With <code>month.txt</code> containing all 12 months of a year:', 'cforms2'); ?>
        <ul style="list-style:none;">
        <li>
        <pre><code style="background:none;">
<?php _e('January', 'cforms2'); ?>

<?php _e('February', 'cforms2'); ?>

<?php _e('March', 'cforms2'); ?>

...</code></pre>
        </li>
        </ul>

		</div>


		<div class="cflegend op-closed" id="p23" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
        	<a id="variables" class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Using variables in email subject and messages', 'cforms2')?>
        </div>

		<div class="cf-content" id="o23">
			<p>
				<?php echo sprintf(__('<strong>Subjects and messages</strong> for emails both to the <a href="%s" %s>form admin</a> as well as to the <a href="%s" %s>visitor</a> (auto confirmation, CC:) support insertion of pre-defined variables and/or any of the form input fields.', 'cforms2'),'?page=' . $plugindir . '/cforms-options.php#anchoremail','onclick="setshow(2)"','?page=' . $plugindir . '/cforms-options.php#autoconf','onclick="setshow(5)"'); ?>
			</p>
			<p class="ex"><?php _e('Note that the variable names are case sensitive!', 'cforms2'); ?></p>

			<table class="hf">
				<tr>
					<td class="bright" colspan="2"><span class="abbr" title="<?php _e('Case sensitive!', 'cforms2'); ?>"><strong><?php _e('Predefined variables:', 'cforms2'); ?></strong></span></td>
				</tr>
				<tr>
					<td class="bleft"><code>{BLOGNAME}</code></td>
					<td class="bright"><?php _e('Inserts the Blog\'s name.', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><code>{Form Name}</code></td>
					<td class="bright"><?php _e('Inserts the form name (per your configuration).', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><code>{PostID}</code></td>
					<td class="bright"><?php _e('Inserts the ID of the post the form is shown in.', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><code>{Referer}</code></td>
					<td class="bright"><?php _e('Inserts the HTTP referer information (if available).', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><code>{Page}</code></td>
					<td class="bright"><?php _e('Inserts the WP page the form was submitted from.', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><code>{Date}</code></td>
					<td class="bright"><?php _e('Inserts the date of form submission (per your general WP settings).', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><code>{Time}</code></td>
					<td class="bright"><?php _e('Inserts the time of form submission (per your general WP settings).', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><code>{IP}</code></td>
					<td class="bright"><?php _e('Inserts visitor IP address.', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><code>{ID}</code></td>
					<td class="bright"><?php _e('Inserts a unique and referenceable form ID (provided that DB Tracking is enabled!)', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><code>{CurUserID}</code></td>
					<td class="bright"><?php _e('Inserts the ID of the currently logged-in user.', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><code>{CurUserName}</code></td>
					<td class="bright"><?php _e('Inserts the Name of the currently logged-in user.', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><code>{CurUserEmail}</code></td>
					<td class="bright"><?php _e('Inserts the Email Address of the currently logged-in user.', 'cforms2'); ?></td>
				</tr>
                <tr>
                    <td class="bleft"><code>{CurUserFirstName}</code></td>
                    <td class="bright"><?php _e('Inserts the First Name of the currently logged-in user.', 'cforms2'); ?></td>
                </tr>
                <tr>
                    <td class="bleft"><code>{CurUserLastName}</code></td>
                    <td class="bright"><?php _e('Inserts the Last Name of the currently logged-in user.', 'cforms2'); ?></td>
                </tr>

				<tr>
					<td class="bleft"><em><?php _e('Special:', 'cforms2'); ?></em></td>
					<td class="bright"><?php echo sprintf(__('A single %s (period) on a line inserts a blank line.', 'cforms2'),'"<code>.</code>"'); ?></td>
				</tr>

				<tr>
					<td class="bright" colspan="2">&nbsp;</td>
				</tr>

				<tr id="extravariables">
					<td class="bleft"><code>{Permalink}</code></td>
					<td class="bright"><?php _e('Inserts the URL of the WP post/page.', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><code>{Author}</code></td>
					<td class="bright"><?php _e('Inserts the Author\'s name (<em>Nickname</em>).', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><code>{Title}</code></td>
					<td class="bright"><?php _e('Inserts the WP post or page title.', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><code>{Excerpt}</code></td>
					<td class="bright"><?php _e('Inserts the WP post or page excerpt.', 'cforms2'); ?></td>
				</tr>

				<tr>
					<td class="bright" colspan="2">&nbsp;</td>
				</tr>

				<tr>
					<td class="bright" colspan="2">
						<span class="abbr" title="<?php _e('Case sensitive!', 'cforms2'); ?>"><strong><?php _e('Custom variables (referencing input fields):', 'cforms2'); ?></strong></span>
					</td>
				</tr>
				<tr>
					<td class="bright" colspan="2">
						<?php echo sprintf(__('Alternatively to the cforms predefined variables, you can also reference data of any of your form\'s input fields by one of the 3 ways described below.', 'cforms2')); ?>
					</td>
				</tr>
				<tr>
					<td class="bleft"><code>{<em><?php _e('field label', 'cforms2'); ?></em>}</code></td>
					<td class="bright"><?php _e('With <em>field label</em> being the <u>exact</u> field label as it is being tracked and sent in the admin email!', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><code>{<em><?php _e('XYZ', 'cforms2'); ?></em>}</code></td>
					<td class="bright"><?php _e('In case you\'re using the <u>custom input field NAMES &amp; ID\'s</u>, the reference is the <u>id:</u> of the field.', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="bleft"><code>{<em>_field<strong>NN</strong></em>}</code></td>
					<td class="bright"><?php _e('With <em>NN</em> being the position of the field on the form configuration page.', 'cforms2'); ?></td>
				</tr>

				<tr>
					<td class="ball" colspan="2">
						<?php _e('Example:', 'cforms2'); ?><br />
						<?php echo sprintf(__('Suppose this is the input field definition string: %sYour Website%s', 'cforms2'),'<span style="padding:0 4px; font-family:monospace; background:#f2f2f2;">','[id:homepage]|http://</span>'); ?><br />
						<?php _e('The corresponding variables would be:', 'cforms2'); ?>
						<?php echo sprintf(__('%1$s{Your Website}%2$s , %1$s{homepage}%2$s, or %1$s%3$s%2$s (assuming it is on the 4th position) respectively.', 'cforms2'),'<span style="padding:0 4px; font-family:monospace; background:#f2f2f2;">','</span>','{_field4}'); ?>
					</td>
				</tr>
				
				<tr>
					<td class="bright" colspan="2">&nbsp;</td>
				</tr>

				<tr>
					<td class="bright" colspan="2">
						<span class="abbr" title="<?php _e('Important!', 'cforms2'); ?>"><strong><?php _e('Custom variables in MULTI-PAGE forms:', 'cforms2'); ?></strong></span>
					</td>
				</tr>
				<tr>
					<td class="ball" colspan="2">
						<?php echo sprintf(__('Referencing form values in multi-page forms require specification of the actual form the field is on, eg. {%scf_form6_%sYour Name} would reference the field labeled "Your Name" on form #6.', 'cforms2'), '<span style="color:red; font-weight:bold;">','</span>'); ?>
					</td>
				</tr>
				
			</table>
			<br />
			<table class="hf">
				<tr>
					<td class="bright" style="padding:10px; background:#fdcbaa;"><?php echo '<strong>'.__('Important:','cforms2').'</strong> '; _e('If you are using multiple input fields with <strong>the same</strong> recorded field label (you can always check the "Tracking" menu tab for how the fields are stored), e.g:', 'cforms2'); ?><br />
<pre style="font-size:11px"><code style="background:none">
<strong>Size</strong>#250gr.#500gr#1kg circa
<strong>Size</strong>#450gr.#700gr#1.2kg circa
<strong>Size</strong>#650gr.#800gr#1.5kg circa
</code></pre>
					<br />

					<?php echo sprintf(__('Results in the first field labeled %1$s to be addressed with %2$s. The second instance of %1$s can be addressed by %3$s, and so on...', 'cforms2'),'\'<strong>Size</strong>\'','<code class="codehighlight">{Size}</code>','<code class="codehighlight">{Size__2}</code>'); ?>
					</td>
				</tr>
			</table>
			<br />
			<table class="hf">
				<tr>
					<td class="bright"><?php echo sprintf(__('Here is an example for a simple <a href="%s" %s>Admin HTML message</a> <em>(you can copy and paste the below code or change to your liking)</em>:', 'cforms2'),'?page=' . $plugindir . '/cforms-options.php#cforms_header_html','onclick="setshow(3)"'); ?></td>
				</tr>

				<tr>
					<td class="ball">
						<strong><?php _e('HTML code:', 'cforms2'); ?></strong><br />
						<?php echo '<p>&lt;p style="background:#fafafa; text-align:center; font:10px arial"&gt;' . sprintf(__('a form has been submitted on %s, via: %s [IP %s]', 'cforms2'),'{Date}','{Page}','{IP}') . '&lt;/p&gt;</p>'; ?>
					</td>
				</tr>
				<tr>
					<td class="ball">
						<strong><?php _e('Output:', 'cforms2'); ?></strong><br />
						<?php echo '<p style="background:#fafafa; text-align:center; font:10px arial">' . __('a form has been submitted on June 13, 2007 @ 9:38 pm, via: / [IP 184.153.91.231]', 'cforms2') . '</p>'; ?>
					</td>
				</tr>
				<tr>
					<td class="ball">
						<strong><?php _e('Note:', 'cforms2'); ?></strong> <?php _e('With this limited message you\'d want to enable the option "Include pre formatted form data table in HTML part"', 'cforms2'); ?><br />
					</td>
				</tr>
			</table>
			<br />
			<table class="hf">
				<tr>
					<td class="bright"><?php echo sprintf(__('Here is another example for a more detailed <a href="%s" %s>Admin HTML message</a>:', 'cforms2'),'?page=' . $plugindir . '/cforms-options.php#cforms_header_html','onclick="setshow(3)"'); ?></td>
				</tr>

				<tr>
					<td class="ball">
						<strong><?php _e('HTML code:', 'cforms2'); ?></strong><br />
						<?php echo '<p>&lt;p&gt;'.__('{Your Name} just submitted {Form Name}. You can get in touch with him/her via &lt;a href="mailto:{Email}"&gt;{Email}&lt;/a&gt; and might want to check out his/her web page at &lt;a href="{Website}"&gt;{Website}&lt;/a&gt;', 'cforms2') . '&lt;/p&gt;</p><p>&lt;p&gt;' .  __('The message is:', 'cforms2') . '&lt;br/ &gt;<br />'.__('{Message}', 'cforms2') . '&lt;/p&gt;</p>'; ?>
					</td>
				</tr>
				<tr>
					<td class="ball">
						<strong><?php _e('Output:', 'cforms2'); ?></strong><br />
						<?php echo '<p>' . __('John Doe just submitted MY NEW FORM. You can get in touch with him/her via <a href="mailto:#">john.doe@doe.com</a> and might want to check out his/her web page at <a href="#">http://website.com</a>', 'cforms2') . '</p>'; ?>
						<?php echo '<p>' . __('The message is:', 'cforms2') . '<br />'; ?>
						<?php echo  __('Hey there! Just wanted to get in touch. Give me a ring at 555-...', 'cforms2') . '</p>'; ?>
					</td>
				</tr>
				<tr>
					<td class="ball">
						<strong><?php _e('Note:', 'cforms2'); ?></strong> <?php _e('With this more detailed message you can disable the option "Include pre formatted form data table in HTML part" since you already have all fields covered in the actual message/header.', 'cforms2'); ?><br />
					</td>
				</tr>
			</table>
			<br />
			<table class="hf">
				<tr>
					<td class="bright"><?php echo sprintf(__('And a final example for a <a href="%s" %s>HTML auto confirmation message</a>:', 'cforms2'),'?page=' . $plugindir . '/cforms-options.php#cforms_cmsg_html','onclick="setshow(5)"'); ?></td>
				</tr>

				<tr>
					<td class="ball">
						<strong><?php _e('HTML code:', 'cforms2'); ?></strong><br />
						<?php echo '<p>&lt;div style="text-align:center; color:#aaa; border-bottom:1px solid #aaa"&gt; &lt;strong&gt;' . __('auto confirmation message', 'cforms2') . ', {Date}&lt;/strong&gt; &lt;/div&gt;&lt;br /&gt;</p>'; ?>
						<?php echo '&lt;p&gt;&lt;strong&gt;' . __('Dear {Your Name},', 'cforms2') . '&lt;/strong&gt;&lt;/p&gt;<br />'; ?>
						<?php echo '&lt;p&gt;' . __('Thank you for your note!', 'cforms2') . '&lt;/p&gt;<br />'; ?>
						<?php echo '&lt;p&gt;' . __('We will get back to you as soon as possible.', 'cforms2') . '&lt;/p&gt;<br />'; ?>
					</td>
				</tr>
				<tr>
					<td class="ball">
						<strong><?php _e('Output:', 'cforms2'); ?></strong><br />
						<?php echo '<div style="text-align:center; color:#aaa; border-bottom:1px solid #aaa"><strong>' . __('auto confirmation message', 'cforms2') . ', June 13, 2007 @ 5:03 pm</strong></div><br />'; ?>
						<?php echo '<p><strong>' . __('Dear John Doe,', 'cforms2') . '</strong></p>'; ?>
						<?php echo '<p>' . __('Thank you for your note!', 'cforms2') . '</p>'; ?>
						<?php echo '<p>' . __('We will get back to you as soon as possible.', 'cforms2') . '</p>'; ?>
					</td>
				</tr>
			</table>
		</div>


		<div class="cflegend op-closed" id="p30" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
        	<a id="multipage" class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Multi page forms', 'cforms2')?>
        </div>

		<div class="cf-content" id="o30">
			<p><?php echo sprintf(__('Multi-page-forms support chaining of several forms and gather user input across all linked forms. Inserting a multi page form is easy, simply insert the %s first form %s of the series into your post or page.', 'cforms2'),'<strong>','</strong>'); ?></p>

			<p style="margin: 20px 0px 20px 10px; float: right; width: 410px;"><img src="<?php echo plugin_dir_url(__FILE__); ?>images/example-mp.png" alt=""/></p>
			<table class="hf">
				<tr>
					<td class="bright"><strong><?php _e('Multi-part/-page form features:', 'cforms2'); ?></strong></td>
				</tr>
				<tr>
					<td class="ball"><?php _e('Defining first, next and last form via configuration', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="ball"><?php _e('Optionally send/suppress partial admin emails on a per form basis', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="ball"><?php _e('A form reset button', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="ball"><?php _e('A form back button', 'cforms2'); ?></td>
				</tr>
			</table>

			<table class="hf" style="margin-top:10px;">
				<tr>
					<td class="bright" colspan="2"><strong><?php _e('Example (eg. using 3 forms):', 'cforms2'); ?></strong></td>
				</tr>
				<tr>
					<td class="ball"><code><?php _e('form 1,2,3:', 'cforms2'); ?></code></td>
					<td class="ball"><?php _e('select main check box to enable as multi-part forms', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="ball"><code><?php _e('form 1:', 'cforms2'); ?></code></td>
					<td class="ball"><?php _e('(a) check "Suppress admin email.."', 'cforms2'); ?><br /><?php _e('(b) check "This is the first form.."', 'cforms2'); ?><br /><?php _e('(c) select "form 2" as next form', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="ball"><code><?php _e('form 2:', 'cforms2'); ?></code></td>
					<td class="ball"><?php _e('(a) check "Suppress admin email.."', 'cforms2'); ?><br /><?php _e('(b) select "form 3" as next form', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="ball"><code><?php _e('form 3:', 'cforms2'); ?></code></td>
					<td class="ball"><?php _e('(a) make sure to not! have "Suppress admin email.." selected', 'cforms2'); ?><br /><?php _e('(b) select "last form" to stop further routing', 'cforms2'); ?></td>
				</tr>
				<tr>
					<td class="ball" colspan="2"><?php _e('Optionally add Reset &amp; Back buttons where appropriate.', 'cforms2'); ?></td>
				</tr>
                <tr>
                    <td class="ball" colspan="2"><?php _e('Further, it makes sense to change "Submit Button" text (to e.g. "Continue") &amp; the "success message" to rather announce the next form.', 'cforms2'); ?></td>
                </tr>
		</table>

        <p class="ex"><strong><?php _e('Important Notes:', 'cforms2'); ?></strong></p>
        <ul style="margin-top:10px;">
            <li><?php echo sprintf(__('Accessing %1$s {custom variables} %2$s in the final form differs from how you would reference these in individual forms. Use the %1$s mail() %2$s example in my-functions.php.txt to examine the user data array; e.g. %1$s{Email}%2$s would become %1$s{cf_form_Email}%2$s (for the first form of the series).', 'cforms2'),'<strong>','</strong>'); ?></li>
            <li><?php echo sprintf(__('Once the multi page form support is enabled, %1$sAjax is being disabled%2$s for this form.', 'cforms2'),'<strong>','</strong>'); ?></li>
        </ul>

		</div>


		<div class="cflegend op-closed" id="p24" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
        	<a id="CSS" class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Styling Your Forms (CSS theme files)', 'cforms2')?>
        </div>

		<div class="cf-content" id="o24">
			<p><?php echo sprintf(__('Please see the <a href="%s">Styling page</a> for theme selection and editing options.', 'cforms2'),'?page=' . $plugindir . '/cforms-css.php'); ?></p>
			<p><?php echo sprintf(__('cforms comes with a few theme examples (some of them may require adjustments to work with <strong>your</strong> forms!) but you can of course create your own theme file -based on the default <strong>cforms.css</strong> file- and put it in the %s directory.', 'cforms2'), '<strong>WP_PLUGIN_DIR/cforms-custom</strong>'); ?></p>
			<p><?php echo sprintf(__('You might also want to study the <a href="%s">PDF guide on cforms CSS</a> I put together to give you a head start.', 'cforms2'),'http://www.deliciousdays.com/download/cforms-css-guide.pdf'); ?></p>
			<p class="ex"><?php _e('Your form <strong>doesn\'t</strong> look like the preview image, or your individual changes don\'t take effect, check your global WP theme CSS! It may overwrite some or many cforms CSS declarations. If you don\'t know how to trouble shoot, take a look at the Firefox extension "Firebug" - an excellent CSS troubleshooting tool!', 'cforms2'); ?></p>
		</div>


		<div class="cflegend op-closed" id="p25" title="<?php _e('Expand/Collapse', 'cforms2') ?>">
        	<a id="troubles" class="helptop" href="#top"><?php _e('top', 'cforms2'); ?></a><div class="blindplus"></div><?php _e('Need more help?', 'cforms2')?>
        </div>

		<div class="cf-content" id="o25">
			<p><?php echo sprintf(__('For up-to-date information first check the %sFAQs%s &amp; %scforms forum%s and comment section on the plugin homepage.', 'cforms2'),'<a href="https://wordpress.org/plugins/cforms2/faq/">','</a>','<a href="https://wordpress.org/support/plugin/cforms2">','</a>'); ?></p>
		</div>

	<?php cforms2_footer(); ?>
</div>
