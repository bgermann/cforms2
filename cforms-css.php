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

$style = $cformsSettings['global']['cforms_css'];

cforms2_check_access_priv();

// if all data has been erased quit
if (count(Cforms2\FormSettings::forms()) === 0) {
    ?>
    <div class="wrap">
        <h2><?php _e('Styling your forms', 'cforms2') ?></h2>

        <h2><?php _e('All cforms data has been erased!', 'cforms2') ?></h2>
        <p><?php _e('Please go to your <strong>Plugins</strong> tab and either disable the plugin, or toggle its status (disable/enable) to revive cforms!', 'cforms2') ?></p>
    </div>
    <?php
    die();
}

if (isset($_POST['no-css'])) {
    $cformsSettings['global']['cforms_no_css'] = $cformsSettings['global']['cforms_no_css'] ? '0' : '1';
    update_option('cforms_settings', $cformsSettings);
}

// Select new CSS?
if (isset($_POST['chg_css'])) {

    $newstyle = $_POST['style'];
    if (strpos($newstyle, 'cforms-custom/') !== false)
        $newstyle = '../../' . $newstyle;
    $cformsSettings['global']['cforms_css'] = $newstyle;
    update_option('cforms_settings', $cformsSettings);

    $style = $cformsSettings['global']['cforms_css'];
    echo ' <div id="message" class="updated fade"><p><strong>' . __('New theme selected.', 'cforms2') . '</strong></p></div>' . "\n";
}
?>
<div class="wrap">
    <h2><?php _e('Styling your forms', 'cforms2') ?></h2>

    <p><?php _e('Please select a theme file that comes closest to what you\'re looking for.', 'cforms2') ?></p>

    <form id="selectcss" method="post">
        <fieldset class="cformsoptions">

            <table>
                <tr>

                    <td>
                        <table>
                            <tr>
                                <td class="cssHint"><?php _e('Please choose a theme file to style your forms', 'cforms2') ?></td>
                                <td class="cssStyles">
                                    <?php
                                    // include all css files
                                    $d = plugin_dir_path(__FILE__) . "styling";
                                    $dCustom = plugin_dir_path(__FILE__) . ".." . DIRECTORY_SEPARATOR . "cforms-custom";

                                    if (!file_exists($d))
                                        echo '<p><strong>' . __('Please make sure that the <code>/styling</code> folder exists in the cforms plugin directory!', 'cforms2') . '</strong></p>';

                                    else {
                                        ?>
                                        <select name="style"><?php
                                            if (file_exists($dCustom)) {
                                                echo '<option disabled="disabled" style="background:#e4e4e4">&nbsp;&nbsp;*** ' . __('custom css files', 'cforms2') . ' ***&nbsp;&nbsp;</option>';

                                                // customer CSS files
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
                                                        echo '<option style="background:#fbd0d3" selected="selected" value="cforms-custom/' . $f . '">' . $f . '</option>' . "\n";
                                                    else
                                                        echo '<option value="cforms-custom/' . $f . '">' . $f . '</option>';
                                                }

                                                echo '<option disabled="disabled" style="background:#e4e4e4">&nbsp;&nbsp;*** ' . __('cform css files', 'cforms2') . ' ***&nbsp;&nbsp;</option>';
                                            }

                                            // core CSS files
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
                                                    echo '<option style="background:#fbd0d3" selected="selected" value="' . $f . '">' . $f . '</option>' . "\n";
                                                else
                                                    echo '<option value="' . $f . '">' . $f . '</option>';
                                            }
                                            ?>
                                        </select>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><input type="submit" name="chg_css" class="allbuttons stylebutton" value="<?php _e('Select Style &raquo;', 'cforms2'); ?>" /></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <br />
                                    <input type="submit" name="no-css" id="no-css" class="allbuttons deleteall" style="height:30px" value="<?php
                                           if ($cformsSettings['global']['cforms_no_css'] == '' || $cformsSettings['global']['cforms_no_css'] == '0')
                                               _e('Deactivate CSS styling altogether!', 'cforms2');
                                           else
                                               _e('Reactivate CSS styling!', 'cforms2');
                                           ?>" />
                                           <?php if ($cformsSettings['global']['cforms_no_css'] == '1') _e('No styles are being used', 'cforms2'); ?>
                                </td>
                            </tr>

                        </table>
                    </td>

                </tr>
            </table>
        </fieldset>
    </form>
</div>
