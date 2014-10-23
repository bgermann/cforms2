<?php

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



### retrieve all form names
function cforms2_getAllformNames() {
    global $cformsSettings;
    $fns = '';
    $forms = $cformsSettings['global']['cforms_formcount'];
    for ($i=0;$i<$forms;$i++) {
        $no = ($i==0)?'':($i+1);
        $fns .= '"'.$cformsSettings['form'.$no]['cforms'.$no.'_fname'].'",';
    }
    return substr($fns,0,-1);
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

    $fns = cforms2_getAllformNames();
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
<script type="text/javascript">
var placeholder = "<?php _e('placeholder for:','cforms') ?>";
var formnames = new Array(<?php echo $fns; ?>);

function closeInsert(){
    var el = document.getElementById("quicktags");
    el.removeChild(document.getElementById("cformsins"));
}
function insertSomething(){
    buttonsnap_settext('<!--cforms name="'+document.getElementById("cfselect").value+'"-->');
    closeInsert();
}
function cforms_buttonscript() {
        if ( document.getElementById("cformsins") ) {
            return closeInsert();
        }

        function edInsertContent(myField, myValue) {
            //IE support
            if (document.selection) {
                myField.focus();
                sel = document.selection.createRange();
                sel.text = myValue;
                myField.focus();
            }
            //MOZILLA/NETSCAPE support
            else if (myField.selectionStart || myField.selectionStart == '0') {
                var startPos = myField.selectionStart;
                var endPos = myField.selectionEnd;
                myField.value = myField.value.substring(0, startPos)
                              + myValue
                              + myField.value.substring(endPos, myField.value.length);
                myField.focus();
                myField.selectionStart = startPos + myValue.length;
                myField.selectionEnd = startPos + myValue.length;
            } else {
                myField.value += myValue;
                myField.focus();
            }
        }

    var rp = document.createElement("div");
    var el = document.getElementById("quicktags");

    rp.setAttribute("id","cformsins");

    rp.innerHTML =  "<form onSubmit=\"insertSomething();\" action=\"#\"><label for=\"nodename\"><?php _e('Your forms:','cforms')?></label>"+
            "<select id=\"cfselect\" name=\"nodename\"/><?php echo $options ?></select>"+
            "<input type=\"button\" id=\"insert\" name=\"insert\" value=\"<?php _e('Insert','cforms') ?>\" onclick=\"javascript:insertSomething()\" />"+
            "<input type=\"button\" id=\"cancel\" name=\"cancel\" value=\"<?php _e('Cancel','cforms') ?>\" onclick=\"javascript:closeInsert()\" />"+
            "</form>";

    el.appendChild(rp);

}
</script>
<?php
		return;
}



### only insert buttons if enabled!
if($cformsSettings['global']['cforms_show_quicktag'] == true) {

	add_action('init', 'cforms2_addbuttons');

    ### TinyMCE error fix
	if( !$cformsSettings['global']['cforms_show_quicktag_js'] ) {
		add_action('edit_page_form', 'cforms2_insert_script');
		add_action('edit_form_advanced', 'cforms2_insert_script');
	}else
		add_action('admin_head', 'cforms2_insert_script');

}
