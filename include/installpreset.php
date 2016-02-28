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

add_action( 'wp_ajax_cforms2_installpreset', 'cforms2_installpreset' );

function cforms2_installpreset() {
check_admin_referer( 'cforms2_installpreset' );

?>

	<p>
		<label for="cf_edit_label_select"><?php _e('Please select a predefined form:', 'cforms2'); ?></label>
		<?php echo cforms2_get_form_presets(); ?>
	</p>
	<p class="ex installNote"><?php _e('By accepting and choosing OK, you will <strong>replace</strong> all your existing input fields with this new preset! If you\'re unsure about this, make a backup copy of the form first.', 'cforms2'); ?></p>
<?php die();
}

### read all presets from the dir
function cforms2_get_form_presets(){
	$presetsdir		= plugin_dir_path(__FILE__).'../formpresets/';

	$list = $title	= '';
	$alldesc 		= '';
	$alldesc_i		= 0;
	$allfiles		= array();

	if ($handle = opendir($presetsdir)) {
	    while (false !== ($file = readdir($handle))) {
	        if (stripos(strrev($file), "txt.") === 0 && filesize($presetsdir.$file) > 0)
				array_push($allfiles,$file);
	    }
	    closedir($handle);
	}
	sort($allfiles);

	$prefix =''; $prefix_i=1;
    $disp = 'block';
	foreach( $allfiles as $file ){
		if ( $fhandle = fopen($presetsdir.$file, "r") ) {
		    if (!feof($fhandle)){
		        preg_match( '/^[^"]+"(.*)"[^"]+$/', fgets($fhandle, 4096), $title );
		        preg_match( '/^[^"]+"(.*)"[^"]+$/', fgets($fhandle, 4096), $desc );
		        $alldesc .= '<span id="descInstall'.($alldesc_i++).'" style="display:'.$disp.';">'.__($desc[1],'cforms2').'</span>';
		        $disp = 'none';
		    }
		    fclose($fhandle);
		}

		$newprefix = substr( __($title[1],'cforms2'), 0, strpos(__($title[1],'cforms2'),':') );

		if ( $newprefix <> $prefix ){
			switch( $prefix_i++ ){
				case '1': $optstyle = ' style="color:#b84141"'; break;
				case '2': $optstyle = ' style="color:#528d47"'; break;
				case '3': $optstyle = ' style="color:#435f7c"'; break;
				default: $optstyle =''; break;
			}
			$prefix = $newprefix;
		}

		$list .= '<option value="'.$file.'" '.$optstyle.'>' .__($title[1],'cforms2'). '</option>';
	}
	$fullstring = '<select name="formpresets" id="formpresets">'.$list.'</select></p><p class="descPreset">'.$alldesc;
    return ($list=='')?'<select><li>'.__('Not available','cforms2').'</select></li>':$fullstring;
}
