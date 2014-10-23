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

add_action( 'wp_ajax_cforms2_insertmcedialog', 'cforms2_insertmcedialog' );

function cforms2_insertmcedialog () {

$cformsSettings = get_option('cforms_settings');

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>cforms</title>
	<link type="text/css" rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>js/css/insertdialog25.css"></link>
	<?php wp_enqueue_script( 'tiny_mce_popup.js', includes_url( 'js/tinymce/tiny_mce_popup.js' ) ); wp_print_scripts('tiny_mce_popup.js'); ?>

	<script type="text/javascript">
	tinyMCEPopup.onInit.add( function(){window.setTimeout(function(){document.getElementById('nodename').focus();},500);} );

	<?php
	$fns = ''; $options = '';
	$forms = $cformsSettings['global']['cforms_formcount'];
	for ($i=0;$i<$forms;$i++) {
		$no = ($i==0)?'':($i+1);
		$options .= '<option value="'.($i+1).'">'.stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_fname']).'</option>';
		$fns .= '"'.$cformsSettings['form'.$no]['cforms'.$no.'_fname'].'",';
	}
	$fns = substr($fns,0,-1);
	echo 'var formnames=new Array('.$fns.');';
	?>

	function init() {
		mcTabs.displayTab('tab', 'panel');
	}

	function insertSomething() {
		no  = document.forms[0].nodename.value;
		html = '<span title="'+formnames[no-1]+'" class="mce_plugin_cforms_img">'+formnames[no-1]+'</span>';

		tinyMCEPopup.execCommand("mceBeginUndoLevel");
		tinyMCEPopup.execCommand('mceInsertContent', false, html);
	 	tinyMCEPopup.execCommand("mceEndUndoLevel");
	   	tinyMCEPopup.close();
	}
	</script>
	<base target="_self" />
</head>
<body id="cforms" onLoad="tinyMCEPopup.executeOnLoad('init();');" style="display: none">
	<form onSubmit="insertSomething();" action="#">
	<div class="tabs">
		<ul>
			<li id="tab"><span><a href="javascript:mcTabs.displayTab('tab','panel');"><?php  _e('Pick a form','cforms'); ?></a></span></li>
		</ul>
	</div>
	<div class="panel_wrapper">
		<div id="panel" class="panel current">
			<table border="0" cellspacing="0" cellpadding="2">
				<tr>
					<td class="cflabel"><label for="nodename"><?php  _e('Your forms:','cforms'); ?></label></td>
					<td class="cfinput"><select id="nodename" name="nodename"/><?php  echo $options; ?></select>
				</tr>
			</table>
		</div>

	</div>
	<div class="mceActionPanel">
		<div style="float: left">
				<input type="button" id="insert" name="insert" value="<?php  _e('Insert','cforms'); ?>" onClick="insertSomething();" />
		</div>
		<div style="float: right">
			<input type="button" id="cancel" name="cancel" value="<?php  _e('Cancel','cforms'); ?>" onClick="tinyMCEPopup.close();" />
		</div>
	</div>
</form>
</body>
</html>
<?php die();
}
