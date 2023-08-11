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

$cformsSettings = get_option('cforms_settings');

$plugindir = dirname(plugin_basename(__FILE__));

cforms2_check_access_priv();

if (cforms2_check_erased())
    return;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!check_admin_referer("cformsglobal"))
        return;
}

$style = $cformsSettings['global']['cforms_css'];

// Update Settings.
if (isset($_POST['SubmitOptions']))
    if (isset($_POST['raw_cforms_settings'])) {
        $raw_cforms_settings = stripslashes(cforms2_get_from_request('raw_cforms_settings'));
        if (!empty($raw_cforms_settings)) {
            $raw_cforms_settings = json_decode($raw_cforms_settings, true);
            if ($raw_cforms_settings === null) {
                echo '<div id="message" class="updated fade"><p>' . __('Error:', 'cforms2') . ' ';
                echo json_last_error_msg() . '</p></div>';
            } elseif (is_array($raw_cforms_settings)) {
                update_option('cforms_settings', $raw_cforms_settings);
            }
        }
    } else {

        $cformsSettings['global']['cforms_show_quicktag'] = cforms2_get_boolean_from_request('cforms_show_quicktag');
        $cformsSettings['global']['cforms_sec_qa'] = cforms2_get_from_request('cforms_sec_qa');
        $cformsSettings['global']['cforms_codeerr'] = cforms2_get_from_request('cforms_codeerr');

        $cformsSettings['global']['cforms_inexclude']['ex'] = '';

        if (cforms2_get_from_request('cforms_inc-or-ex') == 'exclude')
            $cformsSettings['global']['cforms_inexclude']['ex'] = '1';

        $cformsSettings['global']['cforms_inexclude']['ids'] = cforms2_get_from_request('cforms_include');

        unset($cformsSettings['global']['cforms_smtp']);

        $cformsSettings['global']['cforms_upload_err1'] = cforms2_get_from_request('cforms_upload_err1');
        $cformsSettings['global']['cforms_upload_err2'] = cforms2_get_from_request('cforms_upload_err2');
        $cformsSettings['global']['cforms_upload_err3'] = cforms2_get_from_request('cforms_upload_err3');
        $cformsSettings['global']['cforms_upload_err4'] = cforms2_get_from_request('cforms_upload_err4');
        $cformsSettings['global']['cforms_upload_err5'] = cforms2_get_from_request('cforms_upload_err5');

        $cformsSettings['global']['cforms_captcha_def']['foqa'] = cforms2_get_from_request('cforms_cap_foqa');

        $newstyle = cforms2_get_from_request('style');
        if ($newstyle === 'no-css')
            $newstyle = null;
        elseif (strpos($newstyle, 'cforms-custom/') !== false)
            $newstyle = '../../' . $newstyle;
        $cformsSettings['global']['cforms_css'] = $newstyle;
        $style = $cformsSettings['global']['cforms_css'];

        update_option('cforms_settings', $cformsSettings);
    }
?>

<div class="wrap">
    <h2><?php _e('Global Settings', 'cforms2') ?></h2>
    <form enctype="multipart/form-data" id="cformsdata" name="mainform" method="post">
        <?php
        wp_nonce_field("cformsglobal");
        if (isset($_POST['showinfo'])) {
            echo '<p>' . __('All the global and per form settings are listed here as JSON. You can use this as a backup tool if you like.', 'cforms2') . '</p>';
            echo '<h2>' . __('Warning!', 'cforms2') . '</h2><p>' . __('Please do not change anything here unless you know what you are doing!', 'cforms2') . '</p>';
            echo '<textarea style="resize:both;" cols="100" rows="30" name="raw_cforms_settings">' . htmlspecialchars(json_encode($cformsSettings, JSON_PRETTY_PRINT)) . '</textarea>';
            echo '<input type="hidden" name="showinfo" value="" />';
        } else {
            ?>

            <p><?php _e('All settings and configuration options on this page apply to all forms.', 'cforms2') ?></p>

            <fieldset class="cformsoptions" id="selectcss">
                <h3 class="cflegend"><?php _e('Styling your forms', 'cforms2') ?></h3>

                <div class="cf-content">
                    <p><?php _e('Please select a theme file that comes closest to what you\'re looking for.', 'cforms2') ?></p>

                    <table class="form-table">
                        <tr class="ob">
                            <td class="obL"><?php _e('Please choose a theme file to style your forms', 'cforms2') ?></td>
                            <td class="obR">
                                <?php
                                // Include all css files.
                                $d = plugin_dir_path(__FILE__) . "styling";
                                $dCustom = plugin_dir_path(__FILE__) . ".." . DIRECTORY_SEPARATOR . "cforms-custom";

                                if (!file_exists($d))
                                    echo '<p><strong>' . __('Please make sure that the <code>/styling</code> folder exists in the cforms plugin directory!', 'cforms2') . '</strong></p>';

                                else {
                                    ?>
                                <select name="style"><?php
                                        $selected = $style ? '' : 'selected="selected" ';
                                        echo '<option ' . $selected . 'value="no-css">' . __('Deactivate CSS styling altogether!', 'cforms2') . '</option>';
                                        if (file_exists($dCustom)) {
                                            echo '<option disabled="disabled">&nbsp;&nbsp;*** ' . __('custom css files', 'cforms2') . ' ***&nbsp;&nbsp;</option>';

                                            // Customer CSS files.
                                            $allcustomCSS = array();
                                            $dir = opendir($dCustom);
                                            while ($dir && ($f = readdir($dir))) {
                                                if (preg_match("/\.css$/i", $f)) {

                                                    array_push($allcustomCSS, $f);
                                                }
                                            }
                                            sort($allcustomCSS);
                                            foreach ($allcustomCSS as $f) {
                                                if (strpos($style, $f) !== false)
                                                    echo '<option selected="selected" value="cforms-custom/' . $f . '">' . $f . '</option>' . "\n";
                                                else
                                                    echo '<option value="cforms-custom/' . $f . '">' . $f . '</option>';
                                            }

                                            echo '<option disabled="disabled">&nbsp;&nbsp;*** ' . __('cform css files', 'cforms2') . ' ***&nbsp;&nbsp;</option>';
                                        }

                                        // Core CSS files.
                                        $allCSS = array();
                                        $dir = opendir($d);
                                        while ($dir && ($f = readdir($dir))) {
                                            if (preg_match("/\.css$/i", $f)) {
                                                array_push($allCSS, $f);
                                            }
                                        }
                                        sort($allCSS);
                                        foreach ($allCSS as $f) {
                                            if ($f == $style)
                                                echo '<option selected="selected" value="' . $f . '">' . $f . '</option>' . "\n";
                                            else
                                                echo '<option value="' . $f . '">' . $f . '</option>';
                                        }
                                        ?>
                                    </select>
                                <?php } ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </fieldset>

            <fieldset class="cformsoptions" id="inandexclude">
                <h3 class="cflegend"><?php _e('Include cforms header data only on specific pages', 'cforms2') ?></h3>

                <div class="cf-content">
                    <p><?php _e('Specify the ID(s) of <strong>pages or posts</strong> separated by comma on which you\'d like to show or not show cforms. The cforms header will only be included specifically on those pages, helping to maintain all other pages neat.', 'cforms2') ?></p>

                    <table class="form-table">
                        <tr class="ob inexclude">
                            <td class="obL"><label for="cforms_include"><strong><?php
                                        _e('Page / Post ID(s)', 'cforms2');
                                        $ex = ($cformsSettings['global']['cforms_inexclude']['ex'] == '1');
                                        ?></strong></label></td>
                            <td class="obR">
                                <input class="allchk"<?php echo!$ex ? ' checked="checked"' : ''; ?> type="radio" id="include" value="include" name="cforms_inc-or-ex"/><label for="include"><?php _e('include', 'cforms2') ?></label>  <input class="allchk"<?php echo $ex ? ' checked="checked"' : ''; ?> type="radio" id="exclude" value="exclude" name="cforms_inc-or-ex"/><label for="exclude"><?php _e('exclude', 'cforms2') ?></label><br />
                                <input type="text" id="cforms_include" name="cforms_include" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['global']['cforms_inexclude']['ids'])); ?>"/><br />
                                <?php _e('Leave empty to include cforms header files throughout your blog', 'cforms2') ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </fieldset>

            <fieldset class="cformsoptions" id="upload">
                <h3 class="cflegend"><?php _e('Global File Upload Settings', 'cforms2') ?></h3>

                <div class="cf-content">
                    <p>
                        <?php printf(__('Configure and double-check these settings in case you are adding a "<code>File Upload Box</code>" to your form (also see the <a href="%s" %s>Help!</a> for further information).', 'cforms2'), '?page=' . $plugindir . '/cforms-help.php#upload', ''); ?>
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
                                <table><tr><td><textarea class="errmsgbox" name="cforms_upload_err2" id="cforms_upload_err2" ><?php echo stripslashes(htmlspecialchars($cformsSettings['global']['cforms_upload_err2'])); ?></textarea></td></tr></table>
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


            <fieldset class="cformsoptions" id="wpeditor">
                <h3 class="cflegend"><?php _e('WP Editor Button support', 'cforms2') ?></h3>

                <div class="cf-content">
                    <p><?php _e('If you would like to use editor buttons to insert your cforms please enable them below.', 'cforms2') ?></p>

                    <table class="form-table">
                        <tr class="ob">
                            <td class="obR"><input class="allchk" type="checkbox" id="cforms_show_quicktag" name="cforms_show_quicktag" <?php if ($cformsSettings['global']['cforms_show_quicktag'] == "1") echo "checked=\"checked\""; ?>/> <label for="cforms_show_quicktag"><strong><?php _e('Enable TinyMCE', 'cforms2') ?></strong> <?php _e('&amp; Code editor buttons', 'cforms2') ?></label></td>
                        </tr>
                    </table>
                </div>
            </fieldset>

            <fieldset class="cformsoptions" id="visitorv">
                <h3 class="cflegend"><?php _e('Visitor Verification Settings (Q&amp;A)', 'cforms2') ?></h3>

                <div class="cf-content">
                    <p><?php printf(__('Getting a lot of <strong>SPAM</strong>? Use these Q&amp;A\'s to counteract spam and ensure it\'s a human submitting the form. To use in your form, add the corresponding input field %s preferably in its own FIELDSET!', 'cforms2'), '<code>' . __('Visitor verification (Q&amp;A)', 'cforms2') . '</code>'); ?></p>

                    <table class="form-table">
                        <tr class="ob">
                            <td class="obL"><label for="cforms_cap_foqa"><strong><?php _e('Force display', 'cforms2') ?></strong></label></td>
                            <td class="obR"><input class="allchk" type="checkbox" id="cforms_cap_foqa" name="cforms_cap_foqa" value="1" <?php if ($cformsSettings['global']['cforms_captcha_def']['foqa']) echo "checked=\"checked\""; ?>/><label for="cforms_cap_foqa"><?php _e('Force Q&amp;A display for logged in users', 'cforms2') ?></label></td>
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


<?php } ?>

        <div id="cf_actions">
            <input id="cfbar-showinfo" class="allbuttons addbutton" type="submit" name="showinfo" value=""/>
            <input id="cfbar-deleteall" class="allbuttons deleteall" type="button" name="deleteallbutton" value=" "/>
            <input id="cfbar-SubmitOptions" type="submit" name="SubmitOptions" class="allbuttons updbutton" value="" />
        </div>

    </form>

</div>

<div title="<?php _e('Uninstalling / Removing cforms', 'cforms2'); ?>" id="cf_delall_dialog">
    <fieldset class="cf_ed_main">
        <form name="deleteform" method="post">
            <?php wp_nonce_field("cformsglobal"); ?>
            <div id="cf_target_del"><?php _e('Warning!', 'cforms2'); ?></div>
            <div class="controls">
                <p><?php _e('Generally, simple deactivation of cforms does <strong>not</strong> erase any of its data. If you like to quit using cforms for good, please erase all data before deactivating the plugin.', 'cforms2') ?></p>
                <p><strong><?php _e('This is irrevocable!', 'cforms2') ?></strong><br />
                     <input type="submit" name="cfdeleteall" title="<?php _e('Are you sure you want to do this?!', 'cforms2') ?>" class="allbuttons deleteall" value="<?php _e('DELETE *ALL* CFORMS DATA', 'cforms2') ?>" /></p>
            </div>
        </form>
    </fieldset>
</div>
