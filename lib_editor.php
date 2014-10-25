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

### adding TinyMCE button
function cforms2_addbuttons() {

    if ( 'true' == get_user_option('rich_editing') ) {
        add_filter( 'mce_external_plugins', 'cforms2_plugin');
        add_filter( 'mce_buttons', 'cforms2_button');
    }
}



### used to insert button in editor
function cforms2_button($buttons) {
    array_push($buttons, "separator", "cforms");
    return $buttons;
}



### adding to TinyMCE
function cforms2_plugin($plugins) {
	$plugins['cforms'] = plugin_dir_url( __FILE__ ).'js/editor_plugin25.js';
	return $plugins;
}

### Load the Script for the Button
function cforms2_insert_script() {
    global $cformsSettings;

    $options = '';
    $forms = $cformsSettings['global']['cforms_formcount'];
    for ($i=0;$i<$forms;$i++) {
        $no = ($i==0)?'':($i+1);
        $options .= '<option value=\"'.sanitize_title_with_dashes($cformsSettings['form'.$no]['cforms'.$no.'_fname']).'\">'.$cformsSettings['form'.$no]['cforms'.$no.'_fname'].'</option>';
    }

    ?>
<style>
#cformsins{
	font-size:11px;
	margin:2px 4px 5px 4px;
	text-align:center;
	padding:2px 0;
	border-top:2px solid #247FAB;
	border-bottom:2px solid #247FAB;
}
#cformsins form{
	background: #F0F0EE url(<?php echo plugin_dir_url( __FILE__ ) ?>images/cfii_code_ed.jpg) no-repeat top right;
	overflow:hidden;
	padding:2px 0;
	}
#cformsins label{
	font-variant:small-caps;
	font-size:14px;
	padding-right:10px;
	line-height:25px;
}
#cfselect {
	font-size:12px;
	width:210px;
}
#cancel,
#insert{
	font-size:11px;
	margin-left:10px;
	width:120px!important;
}
</style>
<?php
}



### only insert buttons if enabled!
if($cformsSettings['global']['cforms_show_quicktag'] == true) {

	add_action('init', 'cforms2_addbuttons');

    ### TinyMCE error fix
	if( !$cformsSettings['global']['cforms_show_quicktag_js'] ) {
		add_action('edit_page_form', 'cforms2_insert_script');
		add_action('edit_form_advanced', 'cforms2_insert_script');
	} else
		add_action('admin_head', 'cforms2_insert_script');

}
