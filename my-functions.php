<?php

### Find below examples for your custom routines. Do not change the function names.
###
### my_cforms_logic() : gets triggered throughout cforms, supporting real-time configuration
### my_cforms_action() : gets triggered after user input validation and processing
### my_cforms_filter() : after validation, before processing (nonAJAX)
### my_cforms_ajax_filter() : after validation, before processing (AJAX)

### un-comment if you require custom processing and modify the below examples
### to meet your requirements


/*

###
### Your custom application logic
###
### Settings supported for now:
###
### "redirection"	$cformsdata = cforms datablock
### "filename"  	$cformsdata = $_REQUEST
### "adminTO"  		$cformsdata = cforms datablock
### "nextForm"  	$cformsdata = cforms datablock
###

function my_cforms_logic($cformsdata,$oldvalue,$setting) {

	### example: changes the next form to be form ID 5 (which is multi form page enabled)


    if ( $setting == "nextForm" ){

    	### the below only triggers when the configured "next form" would have been 4
        ### and the user did not check extended option checkbox
		if ( $oldvalue=='4' && $cformsdata['data']['extended options']=='on' )
            return 5;

    }

	### example: changes the admin email address to "test123 <my@dif..." if placeholder 'placeholder' is found

    if ( $setting == "adminTO" ){

		if ( $oldvalue=='placeholder' )
			return 'test123 <my@different-email.com>';

    }

	### example: changes the name of the uploaded file in the email (adding a prefix taken form a form field)

    if ( $setting == "filename" ){
		return $cformsdata['filetype'] . $oldvalue;
	}

	### example: changes redirection address based on user input field

    if ( $setting == "redirection" ){

		### note: '$$$mypick' references the ID of the HTML element and has been assigned
        ### to the drop down field in the form configuration, with [id:mypick] !

		$userfield = $cformsdata['data'][$cformsdata['data']['$$$mypick']];

		if ( $userfield == 'abc' )
	        return 'http://my.new.url.com';

		if ( $userfield == 'def' )
	        return 'http://my.other.url.com';
	}

	return $oldvalue;
}




###
### Your custom user data input filter
###
function my_cforms_action($cformsdata) {

	### Extract Data
	### Note: $formID = '' (empty) for the first form!

	$formID = $cformsdata['id'];
	$form   = $cformsdata['data'];

	### triggers on your third form
	if ( $formID == '3' ) {

		### Do something with the data or not, up to you
		$form['Your Name'] = 'Mr./Mrs. '.$form['Your Name'];

	}

	### Send to 3d party or do something else
	@mail('your@email.com', 'cforms my_action test', print_r($form,1), 'From: your@blog.com');

}




###
### Your custom user data input filter (non ajax)
###
function my_cforms_filter($POSTdata) {

	### triggers on your third form
	if ( isset($POSTdata['sendbutton3']) ) {

			### do something with field name 'cf3_field_3'
			### (! check you HTML source to properly reference your form fields !)
			$POSTdata['cf3_field_3'] = 'Mr./Mrs. '.$POSTdata['cf3_field_3'];

			### perhaps send an email or do somethign different
			@mail('your@email.com', 'cforms my_filter_nonAjax test', 'Form data array (nonAjax):'.print_r($POSTdata,1), 'From: your@blog.com');
	}
	return $POSTdata;

}




###
### Your custom user data input filter (ajax)
###
function my_cforms_ajax_filter($params) {

	### triggers on your third form
	if ( $params['id']=='3' ) {

			### do something with field #1
			### (! for ajax, all form fields are counted sequentially! !)
			$params['field_1'] = 'Mr./Mrs. '.$params['field_1'];

			### perhaps send an email or do somethign different
			@mail('your@email.com', 'cforms my_filter_Ajax test', 'Form data array (Ajax):'.print_r($params,1), 'From: your@blog.com');

	}
	return $params;

}

*/

?>