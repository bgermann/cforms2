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

$cformsSettings = get_option('cforms_settings');
$s = DIRECTORY_SEPARATOR;

### CSS styles
$style		= $cformsSettings['global']['cforms_css'];
$stylefile	= plugin_dir_path(__FILE__)."styling{$s}".$style;

### Check Whether User Can Manage Database
cforms2_check_access_priv();

### if all data has been erased quit
if ( $cformsSettings['global']['cforms_formcount'] == '' ){
	?>
	<div class="wrap">
		<div id="icon-cforms-css" class="icon32"><br/></div><h2><?php _e('Styling your forms','cforms2')?></h2>

	<h2><?php _e('All cforms data has been erased!', 'cforms2') ?></h2>
	<p><?php _e('Please go to your <strong>Plugins</strong> tab and either disable the plugin, or toggle its status (disable/enable) to revive cforms!', 'cforms2') ?></p>
	</div>
	<?php
	die;
}

###
### Enable/Disable LabelIDs ?
###

if(isset($_POST['label-ids'])){
	$cformsSettings['global']['cforms_labelID'] = $cformsSettings['global']['cforms_labelID']?'0':'1';
	update_option('cforms_settings',$cformsSettings);
}
else if(isset($_POST['li-ids'])){
	$cformsSettings['global']['cforms_liID'] = $cformsSettings['global']['cforms_liID']?'0':'1';
	update_option('cforms_settings',$cformsSettings);
}
else if(isset($_POST['no-css'])){
	$cformsSettings['global']['cforms_no_css'] = $cformsSettings['global']['cforms_no_css']?'0':'1';
	update_option('cforms_settings',$cformsSettings);
}

###
### Select new CSS?
###
if ( isset($_POST['chg_css']) ){

	$uitheme = $_POST['jqueryuitheme'];
	$cformsSettings['global']['cforms_jqueryuitheme'] = $uitheme;

	$newstyle = $_POST['style'];
	if ( strpos($newstyle,'cforms-custom/')!==false)
		$newstyle = '../../' . $newstyle;
	$cformsSettings['global']['cforms_css'] = $newstyle;
	update_option('cforms_settings',$cformsSettings);

	$style = $cformsSettings['global']['cforms_css'];
	$stylefile  = plugin_dir_path(__FILE__)."styling{$s}".$style;
	echo ' <div id="message" class="updated fade"><p><strong>'. __('New theme selected.', 'cforms2') .'</strong></p></div>'."\n";
}

?>
<div class="wrap" id="top">
		<div id="icon-cforms-css" class="icon32"><br/></div><h2><?php _e('Styling your forms','cforms2')?></h2>

	<p><?php _e('Please select a theme file that comes closest to what you\'re looking for.', 'cforms2') ?></p>

	<form id="selectcss" method="post">
			 <fieldset class="cformsoptions">

				<table>
				<tr>

					<td>
						<table>
							<tr>
								<td class="cssHint"><?php _e('Please choose a theme file to style your forms' , 'cforms2') ?></td>
								<td class="cssStyles">
									<?php ### include all css files
										$d   = plugin_dir_path(__FILE__)."styling";
										$dCustom = plugin_dir_path(__FILE__)."..{$s}cforms-custom";

										$exists = file_exists($d);
										if ( $exists == false )
											echo '<p><strong>' . __('Please make sure that the <code>/styling</code> folder exists in the cforms plugin directory!', 'cforms2') . '</strong></p>';

										else {
											?>
											<select name="style"><?php


												if (file_exists($dCustom)){
													echo '<option disabled="disabled" style="background:#e4e4e4">&nbsp;&nbsp;*** '.__('custom css files','cforms2').' ***&nbsp;&nbsp;</option>';

													### customer CSS files
													$allcustomCSS = array();
													$dir = opendir($dCustom);
													while ( $dir && ($f = readdir($dir)) ) {
														if( preg_match("/\.css$/i", $f) ){
														
															array_push($allcustomCSS, $f);
														}
													}
													sort($allcustomCSS);
													foreach ( $allcustomCSS as $f ) {
														if( strpos($style,$f)!==false )
														    	echo '<option style="background:#fbd0d3" selected="selected" value="cforms-custom/'.$f.'">'.$f.'</option>'."\n";
														else
																echo '<option value="cforms-custom/'.$f.'">'.$f.'</option>';
													}

													echo '<option disabled="disabled" style="background:#e4e4e4">&nbsp;&nbsp;*** '.__('cform css files','cforms2').' ***&nbsp;&nbsp;</option>';
												}

												### core CSS files
												$allCSS = array();
												$dir = opendir($d);
												while ( $dir && ($f = readdir($dir)) ) {
													if ( preg_match("/\.css$/i", $f) ) {
														array_push($allCSS, $f);
													}
												}
												sort($allCSS);
												foreach ( $allCSS as $f ) {
													if( $f==$style )
													    	echo '<option style="background:#fbd0d3" selected="selected" value="'.$f.'">'.$f.'</option>'."\n";
													else
															echo '<option value="'.$f.'">'.$f.'</option>';
												}

											?>
											</select>
									<?php } ?>
								</td>
							</tr>
							<tr>
								<td><label for="jqueryuitheme"><a href="http://jqueryui.com/themeroller/#themeGallery"><?php _e('jQuery UI theme', 'cforms2'); ?> </a></label></td>
								<td><input type="text" name="jqueryuitheme" id="jqueryuitheme" value="<?php echo $cformsSettings['global']['cforms_jqueryuitheme']; ?>" /></td>
							</tr>
							<tr>
								<td></td>
								<td><input type="submit" name="chg_css" class="allbuttons stylebutton" value="<?php _e('Select Style &raquo;', 'cforms2'); ?>" /></td>
							</tr>
							<tr>
								<td colspan="2">
									<p><?php _e('For comprehensive customization support you may choose to turn on <strong>label &amp; list element ID\'s</strong>. This way each input field &amp; label can be specifically addressed via CSS styles.', 'cforms2') ?> </p>

									<input type="submit" name="label-ids" id="label-ids" class="allbuttons" value="<?php if ( $cformsSettings['global']['cforms_labelID']=='' || $cformsSettings['global']['cforms_labelID']=='0' ) _e('Activate Label IDs', 'cforms2'); else  _e('Deactivate Label IDs', 'cforms2'); ?>" />
									<?php if ( $cformsSettings['global']['cforms_labelID']=='1' ) _e('Currently turned on ', 'cforms2'); ?>
									<br />
									<input type="submit" name="li-ids" id="li-ids" class="allbuttons" value="<?php if ( $cformsSettings['global']['cforms_liID']=='' || $cformsSettings['global']['cforms_liID']=='0' ) _e('Activate List Element IDs', 'cforms2'); else  _e('Deactivate List Element IDs', 'cforms2'); ?>" />
									<?php if ( $cformsSettings['global']['cforms_liID']=='1' ) _e('Currently turned on ', 'cforms2'); ?>
									<br />
									<br />
									<input type="submit" name="no-css" id="no-css" class="allbuttons deleteall" style="height:30px" value="<?php if ( $cformsSettings['global']['cforms_no_css']=='' || $cformsSettings['global']['cforms_no_css']=='0' ) _e('Deactivate CSS styling altogether!', 'cforms2'); else  _e('Reactivate CSS styling!', 'cforms2'); ?>" />
									<?php if ( $cformsSettings['global']['cforms_no_css']=='1' ) _e('No styles are being used', 'cforms2'); ?>

								</td>
							</tr>
							<tr>
								<td colspan="2">
										<p><?php echo sprintf(__('You might also want to study the <a href="%s">PDF guide on cforms CSS</a> I put together to give you a head start.', 'cforms2'),'http://www.deliciousdays.com/download/cforms-css-guide.pdf'); ?></p>
								</td>
							</tr>

						</table>
					</td>

					<td>
						<?php if ( $exists ) {

								$existsjpg = file_exists($d.'/'.$style.'.jpg');
								if ( $existsjpg )
									echo __('PREVIEW:', 'cforms2').'<br /><img height="228" width="300" src="' . plugin_dir_url(__FILE__).'styling/'.$style.'.jpg' . '" alt="' . __('Theme Preview', 'cforms2') . '" title="' . __('Theme Preview', 'cforms2').': ' . $style .'"/>';

						}?>
					</td>

				</tr>
				</table>
			</fieldset>
	 </form>
</div>
