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

$plugindir = dirname(plugin_basename(__FILE__));
?>
<div class="wrap">

    <h2><?php _e('Help', 'cforms2') ?></h2>

    <p>
        <?php _e('Here you\'ll find plenty of examples and documentation that should help you configure <strong>cforms</strong>.', 'cforms2'); ?>
    </p>

    <p class="cftoctitle"><?php _e('Table of Contents', 'cforms2'); ?></p>
    <ul class="cftoc">
        <li><a href="#guide"><?php _e('Basic steps, a small guide', 'cforms2'); ?></a> &raquo;</li>
        <li><a href="#inserting"><?php _e('Inserting a form', 'cforms2'); ?></a> &raquo;</li>
        <li><a href="#fields"><?php _e('Configuring form input fields', 'cforms2'); ?></a> &raquo;</li>
        <li><a href="#customerr"><?php _e('Custom error messages &amp; input field titles', 'cforms2'); ?></a> &raquo;</li>
        <li><a href="#variables"><?php _e('Using variables in email subject and messages', 'cforms2'); ?></a> &raquo;</li>
        <li><a href="#multipart"><?php _e('Multi-part forms', 'cforms2'); ?></a> &raquo;</li>
        <li><a href="#css"><?php _e('Styling your forms', 'cforms2'); ?></a> &raquo;</li>
    </ul>


    <h3 class="cflegend" id="guide"><?php _e('Basic steps, a small guide', 'cforms2') ?></h3>

    <div class="cf-content">
        <p><?php _e('Admittedly, <strong>cforms</strong> is not the easiest form mailer plugin but it may be the most flexible. The below outline should help you get started with the default form.', 'cforms2'); ?></p>
        <ol style="margin:10px 0 0 100px;">
            <li><?php printf(__('First take a look at the <a href="%s">default form</a>', 'cforms2'), '?page=' . $plugindir . '/cforms-options.php#anchorfields'); ?>
                <ul style="margin:10px 0 0 30px;">
                    <li><?php _e('Verify that it contains all the fields you need, are they in the right order', 'cforms2'); ?> <img style="vertical-align:middle;" src="<?php echo plugin_dir_url(__FILE__); ?>images/move.png" alt="" title=""/>?</li>
                    <li><?php _e('Check the field labels (field names), if needed make your adjustments', 'cforms2'); ?> &nbsp;<button type="button" name="wrench" style="vertical-align:middle;" disabled="disabled" class="wrench">&#xF111;</button> </li>
                    <li><?php _e('Check the flags for each field (check boxes to the right).', 'cforms2'); ?></li>
                    <li><?php printf(__('Want to include SPAM protection? Choose between <a href="%s" %s>Q&amp;A</a>, <a href="%s" %s>captcha</a> add an input field accordingly and configure <a href="%s" %s>here</a>.', 'cforms2'), '#qa', '', 'https://wordpress.org/plugins/cforms2-really-simple-captcha/', '', '?page=' . $plugindir . '/cforms-global-settings.php#visitorv', ''); ?></li>
                </ul>
            </li>
            <li><?php printf(__('Check if the <a href="%s" %s>email admin</a> for your form is configured correctly.', 'cforms2'), '?page=' . $plugindir . '/cforms-options.php#anchoremail', ''); ?></li>
            <li><?php printf(__('Decide if you want the visitor to receive an <a href="%s" %s>auto confirmation message</a> upon form submission.', 'cforms2'), '?page=' . $plugindir . '/cforms-options.php#autoconf', ''); ?></li>
            <li><?php printf(__('<a href="%s" %s>Add the default form</a> to a post or page.', 'cforms2'), '#inserting', ''); ?></li>
        </ol>
    </div>


    <h3 class="cflegend" id="inserting"><?php _e('Inserting a form', 'cforms2') ?></h3>

    <div class="cf-content">
        <p><?php printf(__('If you like to do it the \'code\' way, make sure to use %1s to include them in your <em>Pages/Posts</em>. With %2s being <u>your form NAME</u>.', 'cforms2'), 'shortcode <code>[cforms name="XYZ"]</code>', '<code>XYZ</code>'); ?></p>
        <p><?php printf(__('A more elegant and safer way is to use the <strong>TinyMCE Button</strong> (double check if <a href="%3s" %s>Button Support</a> is enabled!).', 'cforms2'), '?page=' . $plugindir . '/cforms-global-settings.php#wpeditor', ''); ?></p>
    </div>


    <h3 class="cflegend" id="fields"><?php _e('Configuring form input fields', 'cforms2') ?></h3>

    <div class="cf-content">
        <p><?php _e('All supported input fields are listed below, highlighting the expected formats for their associated Field Names.', 'cforms2'); ?></p>

        <p class="fieldtitle" id="textonly">
            <span class="h4ff"><?php _e('form<br />field', 'cforms2'); ?></span>
            <?php _e('Text only elements (no input)', 'cforms2'); ?>
        </p>
        <img class="helpimg" src="<?php echo plugin_dir_url(__FILE__); ?>images/example-text.png"  alt=""/>
        <table class="hf">
            <tr>
                <td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
                <td class="bright"><?php printf(__('text paragraph %1$s css class %1$s optional style', 'cforms2'), '<span style="color:red; font-weight:bold;">|</span>'); ?></td>
            </tr>
            <tr>
                <td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright"><code><?php _e('Please make sure...', 'cforms2'); ?>|mytextclass|font-size:9x; font-weight:bold;</code></td>
            </tr>
            <tr>
                <td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright"><code><?php printf(__('Check %s here %s for more info. %s', 'cforms2'), '&lt;a href="http://mysite.com"&gt;', '&lt;/a&gt;', '||font-size:9x;'); ?></code></td>
            </tr>
            <tr>
                <td class="ball" colspan="2"><?php _e('HTML: the <code>text paragraph</code> supports HTML. If you need actual &lt;, &gt; in your text please use the proper HTML entity.', 'cforms2'); ?></td>
            </tr>
            <tr>
                <td class="ball" colspan="2"><?php _e('The above expression applies the custom class "<code>mytextclass</code>" <strong>AND</strong> the specific styles "<code>font-size:9x; font-weight:bold;</code>" to the paragraph.', 'cforms2'); ?></td>
            </tr>
            <tr>
                <td class="ball" colspan="2"><?php printf(__('If you specify a <code>css class</code>, you also need to define it in your current form theme file, <a href="%s">here</a>.', 'cforms2'), '?page=' . $plugindir . '/cforms-css.php'); ?></td>
            </tr>
        </table>

        <br style="clear:both;"/>

        <p class="fieldtitle" id="datepicker">
            <span class="h4ff"><?php _e('form<br />field', 'cforms2'); ?></span>
            <?php _e('Javascript Date Picker', 'cforms2'); ?>
        </p>
        <table class="hf">
            <tr>
                <td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
                <td class="bright"><?php printf(__('field name %1$s default value %1$s <a href="%2$s">regular expression</a>', 'cforms2'), '<span style="color:red; font-weight:bold;">|</span>', '#regexp'); ?></td>
            </tr>
            <tr>
                <td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright">
                    <code><?php
                        _e('Arrival Date', 'cforms2');
                        echo '|';
                        echo cforms2_admin_date_format();
                        ?>|^[0-9][0-9]/[0-9][0-9]/[0-9][0-9][0-9][0-9]$</code></td>
            </tr>
            <tr>
                <td class="ball" colspan="2">
                    <?php printf(__('The example above will set a <em>default value</em> of "%s" so users know the expected format. The <strong>regexp</strong> at the end ensures that only this format is accepted. <strong>NOTE:</strong> You also need to <a href="%s" %s>configure the date picker options</a> to match the date format ("%s")!', 'cforms2'), cforms2_admin_date_format(), '?page=' . $plugindir . '/cforms-global-settings.php#datepicker', '', cforms2_admin_date_format()); ?>
                </td>
            </tr>
        </table>

        <br style="clear:both;"/>

        <p class="fieldtitle" id="single">
            <span class="h4ff"><?php _e('form<br />field', 'cforms2'); ?></span>
            <?php _e('Single, Password &amp; Multi line input fields', 'cforms2'); ?>
        </p>
        <img class="helpimg" src="<?php echo plugin_dir_url(__FILE__); ?>images/example-single.png"  alt=""/>
        <table class="hf">
            <tr>
                <td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
                <td class="bright"><?php printf(__('field name %1$s default value %1$s <a href="%2$s">regular expression</a>', 'cforms2'), '<span style="color:red; font-weight:bold;">|</span>', '#regexp'); ?></td>
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
                    <code>&lt;acronym title="<?php printf(__('We need your email address for confirmation."%sYour EMail', 'cforms2'), '&gt;'); ?>&lt;/acronym&gt;</code></td>
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
            <?php _e('Select boxes &amp; radio buttons', 'cforms2'); ?>
        </p>
        <img class="helpimg" src="<?php echo plugin_dir_url(__FILE__); ?>images/example-dropdown.png"  alt=""/>
        <table class="hf">
            <tr>
                <td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
                <td class="bright"><?php printf(__('field name %1$s option1 %2$s value1 %1$s option2 %2$s value2 %1$s option3...', 'cforms2'), '<span style="color:red; font-weight:bold;">#</span>', '<span style="color:red; font-weight:bold;">|</span>'); ?></td>
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
                    <?php _e('Note:', 'cforms2'); ?><br /><?php printf(__('<strong>Select box marked "Required":</strong> Using a minus symbol %1$s for the value (after %2$s), will mark an option as invalid! Example:<br /><code>Your age#Please pick your age group|-#12 to 18|kiddo#19 to 30|young#31 to 45#45+ |older</code>. <br />"Please pick..." is shown but not considered a valid value.', 'cforms2'), '<code>-</code>', '<span style="color:red; font-weight:bold;">|</span>'); ?>
                </td>
            </tr>
            <tr>
                <td class="ball" colspan="2">
                    <?php _e('Note:', 'cforms2'); ?><br /><?php printf(__('<strong>Radio buttons marked "Required":</strong> You can choose to not preselect a radio button upon form load, yet make a user selection mandatory for the form to validate.', 'cforms2')); ?>
                </td>
            </tr>
        </table>


        <br style="clear:both;"/>

        <p class="fieldtitle" id="multiselect">
            <span class="h4ff"><?php _e('form<br />field', 'cforms2'); ?></span>
            <?php _e('Multi select boxes', 'cforms2'); ?>
        </p>
        <img class="helpimg" src="<?php echo plugin_dir_url(__FILE__); ?>images/example-ms.png"  alt=""/>
        <table class="hf">
            <tr>
                <td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
                <td class="bright"><?php printf(__('field name %1$s option1 %2$s value1 %1$s option2 %2$s value2 %1$s option3...', 'cforms2'), '<span style="color:red; font-weight:bold;">#</span>', '<span style="color:red; font-weight:bold;">|</span>'); ?></td>
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
            <?php _e('Check boxes', 'cforms2'); ?>
        </p>
        <img class="helpimg" src="<?php echo plugin_dir_url(__FILE__); ?>images/example-checkbox.png"  alt=""/>
        <table class="hf">
            <tr>
                <td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
                <td class="bright"><?php printf(__('field name <u>left</u> %s field name <u>right</u>', 'cforms2'), '<span style="color:red; font-weight:bold;">#</span>'); ?></td>
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
            <?php _e('Check box groups', 'cforms2'); ?>
        </p>
        <img class="helpimg" src="<?php echo plugin_dir_url(__FILE__); ?>images/example-grp.png"  alt=""/>
        <table class="hf">
            <tr>
                <td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
                <td class="bright"><?php printf(__('field name %1$s chk box1 label%2$schk box1 value %1$s chk box2 label %3$s chk box3...', 'cforms2'), '<span style="color:red; font-weight:bold;">#</span>', '<span style="color:red; font-weight:bold;">|</span>', '<span style="color:red; font-weight:bold;">##</span>'); ?></td>
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
                    <?php printf(__('If no explicit <strong>value</strong> (text after the pipe symbol \'%1$s\') is specified, the provided check box label is both label &amp; submitted value.', 'cforms2'), '<span style="color:red; font-weight:bold;">|</span>'); ?>
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
            <?php _e('CC: option for visitors', 'cforms2'); ?>
        </p>
        <img class="helpimg" src="<?php echo plugin_dir_url(__FILE__); ?>images/example-cc.png"  alt=""/>
        <table class="hf">
            <tr>
                <td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
                <td class="bright"><?php printf(__('field name <u>left</u> %s field name <u>right</u>', 'cforms2'), '<span style="color:red; font-weight:bold;">#</span>'); ?></td>
            </tr>
            <tr>
                <td class="bleft"><?php _e('Example:', 'cforms2'); ?></td><td class="bright">
                    <code><?php _e('#please cc: me', 'cforms2'); ?></code></td>
            </tr>
            <tr>
                <td class="ball" colspan="2">
                    <?php printf(__('If the visitor chooses to be CC\'ed, <strong>no</strong> additional auto confirmation email (<a href="%s" %s>if configured</a>) is sent out!', 'cforms2'), '?page=' . $plugindir . '/cforms-options.php#autoconf', ''); ?>
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
            <?php _e('Multiple form mail recipients', 'cforms2'); ?>
        </p>


        <img class="helpimg" src="<?php echo plugin_dir_url(__FILE__); ?>images/example-multi.png"  alt=""/>
        <table class="hf">
            <tr>
                <td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
                <td class="bright"><?php printf(__('field name %1$s Name1 | email address(es) %1$s Name2 | email address(es)%1$s Name3...', 'cforms2'), '<span style="color:red; font-weight:bold;">#</span>'); ?></td>
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
            <?php _e('Hidden input fields', 'cforms2'); ?>
        </p>
        <table class="hf">
            <tr>
                <td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
                <td class="bright"><?php printf(__('field name %1$s default value', 'cforms2'), '<span style="color:red; font-weight:bold;">|</span>'); ?></td>
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
                    <?php printf(__('No <code>field name</code> required, the field has no configurable label per se, as it is determined at run-time from the list of <strong>Question &amp; Answers</strong> provided <a href="%s" %s>here</a>.', 'cforms2'), '?page=' . $plugindir . '/cforms-global-settings.php#visitorv', ''); ?>
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
                    <?php printf(__('Please double-check the <a href="%s" %s>Global Settings</a> for proper configuration of the <code>File Upload</code> functionality (allowed extensions, file size etc.).', 'cforms2'), '?page=' . $plugindir . '/cforms-global-settings.php#upload', ''); ?>
                </td>
            </tr>
        </table>


        <p class="fieldtitle" id="hfieldsets">
            <span class="h4ff"><?php _e('form<br />field', 'cforms2'); ?></span>
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
            <?php _e('Using regular expressions with form fields', 'cforms2'); ?>
        </p>

        <p style="margin:10px 30px;"><?php _e('A regular expression (regex or regexp for short) is a special text string for describing a search pattern, according to certain syntax rules. Many programming languages support regular expressions for string manipulation, you can use them here to validate user input. Single/Multi line input fields:', 'cforms2'); ?></p>

        <table class="hf">
            <tr>
                <td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
                <td class="bright"><?php printf(__('field name %1$s default value %1$s regular expression', 'cforms2'), '<span style="color:red; font-weight:bold;">|</span>'); ?></td>
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
                    <?php echo '<strong style="color:red">' . __('Important:', 'cforms2') . '</strong>'; ?><br />
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
                    <?php printf(__('More information can be found <a href="%s">here</a>, a great regexp repository <a href="%s">here</a>.', 'cforms2'), 'http://php.net/manual/en/book.regex.php', 'http://regexlib.com'); ?>
                </td>
            </tr>
        </table>
    </div>


    <h3 class="cflegend" id="customerr"><?php _e('Custom error messages &amp; input field titles', 'cforms2') ?></h3>

    <div class="cf-content">
        <p><?php printf(__('On top of their labels, input fields can have titles, too. Simply append a %s to a given field configuration string.', 'cforms2'), '<code>|title:XXX</code>'); ?></p>
        <p><?php printf(__('If you like to add custom error messages (next to your generic <a href="%s" %s>success</a> and <a href="%s" %s>error</a> messages) for your input fields, simply append a %s to a given <em>definition string/field name</em>. HTML is supported.', 'cforms2'), '?page=' . $plugindir . '/cforms-options.php#cforms_success', '', '?page=' . $plugindir . '/cforms-options.php#cforms_failure', '', '<code>|err:XXX</code>'); ?></p>
        <p class="ex"><?php printf(__('Please note the order of these special attributes, first %s (if applicable), then %s.', 'cforms2'), '<code>|title:XXX</code>', '<code>|err:XXX</code>'); ?></p>

        <table class="hf">
            <tr>
                <td class="bleft"><span class="abbr" title="<?php _e('Extended entry format for the Field Name', 'cforms2'); ?>"><?php _e('Format:', 'cforms2'); ?></span></td>
                <td class="bright"><?php printf(__('field name %1$s your title here %3$s %2$s your error message %3$s', 'cforms2'), '<span style="color:red; font-weight:bold;">|title:<em>', '<span style="color:red; font-weight:bold;">|err:<em>', '</em></span>'); ?></td>
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


    <h3 class="cflegend" id="variables"><?php _e('Using variables in email subject and messages', 'cforms2') ?></h3>

    <div class="cf-content">
        <p>
            <?php printf(__('<strong>Subjects and messages</strong> for emails both to the <a href="%s" %s>form admin</a> as well as to the <a href="%s" %s>visitor</a> (auto confirmation, CC:) support insertion of pre-defined variables and/or any of the form input fields.', 'cforms2'), '?page=' . $plugindir . '/cforms-options.php#anchoremail', '', '?page=' . $plugindir . '/cforms-options.php#autoconf', ''); ?>
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
                <td class="bright"><?php printf(__('A single %s (period) on a line inserts a blank line.', 'cforms2'), '"<code>.</code>"'); ?></td>
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
                    <?php printf(__('Alternatively to the cforms predefined variables, you can also reference data of any of your form\'s input fields by one of the 3 ways described below.', 'cforms2')); ?>
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
                    <?php printf(__('Suppose this is the input field definition string: %sYour Website%s', 'cforms2'), '<span style="padding:0 4px; font-family:monospace; background:#f2f2f2;">', '[id:homepage]|http://</span>'); ?><br />
                    <?php _e('The corresponding variables would be:', 'cforms2'); ?>
                    <?php printf(__('%1$s{Your Website}%2$s , %1$s{homepage}%2$s, or %1$s%3$s%2$s (assuming it is on the 4th position) respectively.', 'cforms2'), '<span style="padding:0 4px; font-family:monospace; background:#f2f2f2;">', '</span>', '{_field4}'); ?>
                </td>
            </tr>

            <tr>
                <td class="bright" colspan="2">&nbsp;</td>
            </tr>

            <tr>
                <td class="bright" colspan="2">
                    <span class="abbr" title="<?php _e('Important!', 'cforms2'); ?>"><strong><?php _e('Custom variables in multi-part forms:', 'cforms2'); ?></strong></span>
                </td>
            </tr>
            <tr>
                <td class="ball" colspan="2">
                    <?php printf(__('Referencing form values in multi-part forms require specification of the actual form the field is on, eg. {%scf_form6_%sYour Name} would reference the field labeled "Your Name" on form #6.', 'cforms2'), '<span style="color:red; font-weight:bold;">', '</span>'); ?>
                </td>
            </tr>

        </table>
        <br />
        <table class="hf">
            <tr>
                <td class="bright" style="padding:10px; background:#fdcbaa;"><?php
                    echo '<strong>' . __('Important:', 'cforms2') . '</strong> ';
                    _e('If you are using multiple input fields with <strong>the same</strong> recorded field label, e.g:', 'cforms2');
                    ?><br />
                    <pre style="font-size:11px"><code style="background:none">
<strong>Size</strong>#250gr.#500gr#1kg circa
<strong>Size</strong>#450gr.#700gr#1.2kg circa
<strong>Size</strong>#650gr.#800gr#1.5kg circa
</code></pre>
                    <br />

                    <?php printf(__('Results in the first field labeled %1$s to be addressed with %2$s. The second instance of %1$s can be addressed by %3$s, and so on...', 'cforms2'), '\'<strong>Size</strong>\'', '<code class="codehighlight">{Size}</code>', '<code class="codehighlight">{Size__2}</code>'); ?>
                </td>
            </tr>
        </table>
        <br />
        <table class="hf">
            <tr>
                <td class="bright"><?php printf(__('Here is an example for a simple <a href="%s" %s>Admin HTML message</a> <em>(you can copy and paste the below code or change to your liking)</em>:', 'cforms2'), '?page=' . $plugindir . '/cforms-options.php#cforms_header_html', ''); ?></td>
            </tr>

            <tr>
                <td class="ball">
                    <strong><?php _e('HTML code:', 'cforms2'); ?></strong><br />
                    <?php echo '<p>&lt;p style="background:#fafafa; text-align:center;"&gt;' . sprintf(__('a form has been submitted on %s, via: %s [IP %s]', 'cforms2'), '{Date}', '{Page}', '{IP}') . '&lt;/p&gt;</p>'; ?>
                </td>
            </tr>
            <tr>
                <td class="ball">
                    <strong><?php _e('Output:', 'cforms2'); ?></strong><br />
                    <?php echo '<p style="background:#fafafa; text-align:center; font-size:10px;">' . __('a form has been submitted on June 13, 2007 @ 9:38 pm, via: / [IP 184.153.91.231]', 'cforms2') . '</p>'; ?>
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
                <td class="bright"><?php printf(__('Here is another example for a more detailed <a href="%s" %s>Admin HTML message</a>:', 'cforms2'), '?page=' . $plugindir . '/cforms-options.php#cforms_header_html', ''); ?></td>
            </tr>

            <tr>
                <td class="ball">
                    <strong><?php _e('HTML code:', 'cforms2'); ?></strong><br />
                    <?php echo '<p>&lt;p&gt;' . __('{Your Name} just submitted {Form Name}. You can get in touch with him/her via &lt;a href="mailto:{Email}"&gt;{Email}&lt;/a&gt; and might want to check out his/her web page at &lt;a href="{Website}"&gt;{Website}&lt;/a&gt;', 'cforms2') . '&lt;/p&gt;</p><p>&lt;p&gt;' . __('The message is:', 'cforms2') . '&lt;br/ &gt;<br />' . __('{Message}', 'cforms2') . '&lt;/p&gt;</p>'; ?>
                </td>
            </tr>
            <tr>
                <td class="ball">
                    <strong><?php _e('Output:', 'cforms2'); ?></strong><br />
                    <?php echo '<p>' . __('John Doe just submitted MY NEW FORM. You can get in touch with him/her via <a href="mailto:#">john.doe@doe.com</a> and might want to check out his/her web page at <a href="#">http://website.com</a>', 'cforms2') . '</p>'; ?>
                    <?php echo '<p>' . __('The message is:', 'cforms2') . '<br />'; ?>
                    <?php echo __('Hey there! Just wanted to get in touch. Give me a ring at 555-...', 'cforms2') . '</p>'; ?>
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
                <td class="bright"><?php printf(__('And a final example for a <a href="%s" %s>HTML auto confirmation message</a>:', 'cforms2'), '?page=' . $plugindir . '/cforms-options.php#cforms_cmsg_html', ''); ?></td>
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


    <h3 class="cflegend" id="multipart"><?php _e('Multi-part forms', 'cforms2') ?></h3>

    <div class="cf-content">
        <p><?php printf(__('Multi-part forms support chaining of several forms and gather user input across all linked forms. Inserting a multi-part form is easy, simply insert the %s first form %s of the series into your post or page.', 'cforms2'), '<strong>', '</strong>'); ?></p>

        <table class="hf">
            <tr>
                <td class="bright"><strong><?php _e('Multi-part form features:', 'cforms2'); ?></strong></td>
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
            <li><?php printf(__('Accessing %1$s {custom variables} %2$s in the final form differs from how you would reference these in individual forms; e.g. %1$s{Email}%2$s would become %1$s{cf_form_Email}%2$s (for form 1) or %1$s{cf_formN_Email}%2$s (for form N).', 'cforms2'), '<strong>', '</strong>'); ?></li>
            <li><?php printf(__('Once the multi-part form support is enabled, %1$sAJAX is being disabled%2$s for this form.', 'cforms2'), '<strong>', '</strong>'); ?></li>
        </ul>

    </div>


    <h3 class="cflegend" id="css"><?php _e('Styling your forms', 'cforms2') ?></h3>

    <div class="cf-content">
        <p><?php printf(__('Please see the <a href="%s">Styling page</a> for theme selection.', 'cforms2'), '?page=' . $plugindir . '/cforms-css.php'); ?></p>
        <p><?php printf(__('cforms comes with a few theme examples (some of them may require adjustments to work with <strong>your</strong> forms!) but you can of course create your own theme file -based on the default <strong>cforms.css</strong> file- and put it in the %s directory.', 'cforms2'), '<strong>WP_PLUGIN_DIR/cforms-custom</strong>'); ?></p>
        <p><?php printf(__('You might also want to study <a href="%s">A Brief cforms CSS Guide</a>.', 'cforms2'), 'http://www.deliciousdays.com/download/cforms-css-guide.pdf'); ?></p>
        <p class="ex"><?php _e('Your form does not look like the preview image or your individual changes do not take effect? Check your global WP theme CSS! It may overwrite some or many cforms CSS declarations.', 'cforms2'); ?></p>
    </div>

    <?php cforms2_footer(); ?>
</div>
