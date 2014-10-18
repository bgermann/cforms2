/*
  Copyright (c) 2007-2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
  Copyright (c) 2014      Bastian Germann

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

function reset_captcha(no){
	no = no || '';
	document.getElementById('cf_captcha_img'+no).src = cforms2_ajax.url+'?action=cforms2_reset_captcha&_wpnonce='+cforms2_ajax.nonces['reset_captcha']+'&ts='+no+'&rnd='+Math.round(Math.random()*999999);
}


function call_err(no,err,custom_error,popFlag){

	//temp. turn send button back on
	document.getElementById('sendbutton'+no).style.cursor = "auto";
	document.getElementById('sendbutton'+no).disabled = false;

	if ( custom_error!='' ) custom_error = '<ol>'+custom_error+'</ol>';

	err = unescape(decodeURI( err.value )) + custom_error;

	stringXHTML = err.replace(/(\r\n)/g, '<br />');

	var msgbox = 'usermessage'+no;
    ucm = ( parseInt(no)>1 )? ' failure'+no : '';

	if( document.getElementById(msgbox+'a') )
		document.getElementById(msgbox+'a').className = "cf_info failure"+ucm;
	if( document.getElementById(msgbox+'b') )
		document.getElementById(msgbox+'b').className = "cf_info failure"+ucm;

	doInnerXHTML(msgbox, stringXHTML.replace(/\\/g,""), '');

	//popup error
    err = err.replace(/\\/g,"");
	if ( document.getElementById('cf_popup'+no).value.charAt(popFlag) == 'y'){
		err = err.replace(/<li>/g,"\r\n");
		err = err.replace(/<.?strong>/g,'*');
		err = err.replace(/(<([^>]+)>)/ig, '');
		err = err.replace(/&raquo;/ig, '');
		alert( err );
	}
}


function clearField(thefield) {
  if ( thefield.defaultValue == thefield.value )
  		thefield.value = '';
};

function setField(thefield) {
	if ( thefield.value == '' )
		thefield.value = thefield.defaultValue;
};


function cforms_validate(no, upload) {

	if (!no) no='';

	var msgbox = 'usermessage'+no;
	if( document.getElementById(msgbox+'a') ){
		document.getElementById(msgbox+'a').className = "cf_info waiting";
	}
	if( document.getElementById(msgbox+'b') ){
		document.getElementById(msgbox+'b').className = "cf_info waiting";
	}

	var waiting = unescape(decodeURI(document.getElementById('cf_working'+no).value));
	waiting = waiting.replace(/\\/g,"");

	//dothemagic
	function getStyle(oElm, strCssRule){
		var strValue = "";
			try {
				if(document.defaultView && document.defaultView.getComputedStyle){
					strValue = document.defaultView.getComputedStyle(oElm, "").getPropertyValue(strCssRule);
				} else if(oElm.currentStyle){
					strCssRule = strCssRule.replace(/\-(\w)/g, function (strMatch, p1){ return p1.toUpperCase(); });
					strValue = oElm.currentStyle[strCssRule];
				}
			}
			catch(ee) {
				strValue="";
			}
		if( strValue && (strValue.match(/px/) || strValue.match(/em/)) )
			return strValue.substring(0,strValue.length-2);
		else
			return strValue;
	}
	//dothemagic
	function sameParentBG(col, el){
		if( el ){
			var colStyle = getStyle(el, 'background-color');

			if ( colStyle == col)
				return true;
			else if ( el.parentNode && el.parentNode.tagName.toLowerCase() != "html" )
				return sameParentBG(col, el.parentNode);
		}
		return false;
	}

	var insert_err = new Array();
	var insert_err_p = new Array();
	var insert_err_count = 0;

	var all_custom_error = new Array();

	var rest = document.getElementById('cf_customerr'+no).value.substr(3);
	show_err_li  = document.getElementById('cf_customerr'+no).value.substr(0,1);
	show_err_ins = document.getElementById('cf_customerr'+no).value.substr(1,1);
	var jump_to_err  = document.getElementById('cf_customerr'+no).value.substr(2,1);

	var error_container = decodeURIComponent( rest );
		error_container = error_container.split('|');

	for ( i=0; i<error_container.length; i++ ) {
		 var keyvalue = error_container[i].split('$#$');
		 all_custom_error[keyvalue[0]] = keyvalue[1];
	}

	custom_error = '';
	var regexp_field_id = new RegExp('^.*field_([0-9]{1,3})$');


	if( doInnerXHTML(msgbox, waiting) ) {

		var all_valid = true;
		var code_err  = false;

		var regexp_e = new RegExp('^[_a-z0-9+-]+(\\.[_a-z0-9+-]+)*@[a-z0-9-]+(\\.[a-z0-9-]+)*(\\.[a-z]{2,4})$','i');  // email regexp


		//clean enhanced error if present
		var objColl = document.getElementById('cforms'+no+'form').getElementsByTagName('li');
		for (var i=0; i<objColl.length; i++) {
			if(objColl[i].className.match(/cf_li_err/)) {
				if(objColl[i].className.match(/cf-box-title/))
					objColl[i].className='cf-box-title';
				else
					objColl[i].className='';
			}
		}

		objColl = document.getElementById('cforms'+no+'form').getElementsByTagName('ul');
		while ( objColl.length > 0 )
			objColl[0].parentNode.removeChild( objColl[0] );


		objColl = document.getElementById('cforms'+no+'form').getElementsByTagName('*');
		last_one=false;

		for (var i = 0, j = objColl.length; i < j; i++) {

				var temp = objColl[i].className;

				if ( temp.match(/secinput/) )
					newclass = 'secinput';
				else if ( temp.match(/cf-box-./) )
					newclass = temp.match(/cf-box-./);
				else if ( temp.match(/cformselect/) )
					newclass = 'cformselect';
				else if ( temp.match(/upload/) )
					newclass = 'cf_upload';
				else if ( temp.match(/cf_date/) )
					newclass = 'single cf_date';
				else if ( temp.match(/single/) )
					newclass = 'single';
				else if ( temp.match(/area/) )
					newclass = 'area';
				else if ( temp.match(/cfselectmulti/) )
					newclass = 'cfselectmulti';
				else
					newclass = '';

				fld = objColl[i].nodeName.toLowerCase();
				var typ = objColl[i].type;

				if ( (fld == "input" || fld == "textarea" || fld == "select") && !( typ=="hidden" || typ=="submit") ) {

				    if ( temp.match(/required/) && !temp.match(/email/) && typ != "radio" ) {

								newclass = newclass + ' fldrequired';

								n = objColl[i].nextSibling;
								p = objColl[i].previousSibling;

								if ( temp.match(/cf-box-./) ) {

											if ( objColl[i].checked==false ) {

														custom_error = check_for_customerr(objColl[i].id);

														newclass = newclass + ' cf_error';

														// we can't change the checkbox much but the text on the side!
														if( n && n.nodeName.toLowerCase()=="label" && !n.className.match(/errortxt/) )
																n.className = n.className + " cf_errortxt";
														else if ( p && p.nodeName.toLowerCase()=="label" && !p.className.match(/errortxt/) )
																p.className = p.className + " cf_errortxt";


														all_valid=false;
													    if (!last_one && objColl[i].id != '') last_one=objColl[i].id;
											}else{
													// we can't change the checkbox much but the text on the side!
													if( n && n.nodeName.toLowerCase()=="label" && n.className.match(/cf_errortxt/) )
															n.className = n.className.substr(0,n.className.search(/ cf_errortxt/));
													else if ( p && p.nodeName.toLowerCase()=="label" && p.className.match(/cf_errortxt/) )
															p.className = p.className.substr(0,p.className.search(/ cf_errortxt/));

											}


								} else if ( temp.match(/cformselect/) ) {

											if ( objColl[i].value=='' || objColl[i].value=='-' ){
														newclass = newclass + ' cf_error';
														all_valid=false;
													    if (!last_one && objColl[i].id != '') last_one=objColl[i].id;

														custom_error = check_for_customerr(objColl[i].id);

											}

								} else if ( objColl[i].value=='' ) {

											newclass = newclass + ' cf_error';
											all_valid=false;
										    if (!last_one && objColl[i].id != '') last_one=objColl[i].id;

											custom_error = check_for_customerr(objColl[i].id);

								}

					}

					if ( temp.match(/email/) ) {
								newclass = newclass + ' fldemail';
								if ( objColl[i].value=='' && !temp.match(/required/) )
									; //dummy
								else if ( !regexp_e.test(objColl[i].value) ) {
										newclass = newclass + ' fldrequired cf_error';
										all_valid=false;
									    if (!last_one) last_one=objColl[i].name; // TODO fix?: if (!last_one && objColl[i].id != '') last_one=objColl[i].id;

										custom_error = check_for_customerr(objColl[i].id);

					 			}
								else
									newclass = newclass + ' fldrequired';

					}

					if ( temp.match(/required/)&&temp.match(/cf-box-b/)&&typ.match(/radio/) ) {
						var temp_i = i;
						radio_valid = false;

						while ( objColl[i].parentNode.className.match(/cf-box-group/)
						    || objColl[i].parentNode.parentNode.className.match(/cf-box-group/) ) {
							temp = objColl[i].className;
							if( temp.match(/cf-box-b/) && objColl[i].checked ) {
								radio_valid = true;
							}
							i++;
						}

						if ( !radio_valid ) {
							all_valid = false;
							if ( !last_one ) last_one=objColl[temp_i].parentNode.id;
							custom_error = check_for_customerr_radio(
							    objColl[temp_i].parentNode.id,
							    objColl[temp_i].id.substr( 0, objColl[temp_i].id.length - 2 )
							);
						}
					}
					else objColl[i].className=newclass;


				} // if fields



			//
			//if regexp provided use it!
			//
			regexp = 1;
			if ( objColl[i] && document.getElementById(objColl[i].id+'_regexp') ){

				obj_regexp = document.getElementById( objColl[i].id+'_regexp' );
				if (typ=='textarea') INPval = objColl[i].value.replace( /\n\r?/g, ' ' );
				else INPval = objColl[i].value;

				if ( obj_regexp && obj_regexp.value != '' ) {

					if ( document.getElementById(obj_regexp.value) ){
						if ( INPval != document.getElementById(obj_regexp.value).value )
							regexp = null;
					}else{
							if ( INPval != '' ) { //overrule: normal field, normal regexp, left empty
								regexp = new RegExp(obj_regexp.value, ['g']); // normal regexp
								regexp = INPval.match(regexp);
							}
					}

					if ( regexp == null ){
						newclass = newclass + ' cf_error';
						all_valid=false;
						if (!last_one && objColl[i].id != '') last_one=objColl[i].id;
						custom_error = check_for_customerr(objColl[i].id);
					}

				}
			}


		} // for


		//normal visitor verification turned on?
		if ( document.getElementById('cforms_q'+no) && (document.getElementById('cforms_a'+no).value != hex_md5(encodeURI(document.getElementById('cforms_q'+no).value.toLowerCase()) )) ) {
			document.getElementById('cforms_q'+no).className = "secinput cf_error";
			if ( all_valid ) {
				all_valid = false;
				code_err = true;
			    if (!last_one) last_one='cforms_q'+no;
			}
			custom_error = check_for_customerr('cforms_q'+no);
		}

		//captcha verification turned on?
		if ( document.getElementById('cforms_captcha'+no) ) {

			// for captcha!
			var read_cookie = readcookie	(no);
			var cookie_part = read_cookie.split('+');

			//a = document.getElementById('cforms_cap'+no).value;
			a = cookie_part[1];
			b = document.getElementById('cforms_captcha'+no).value;

			if ( cookie_part[0]=='i' ) // case insensitive?
				b = b.toLowerCase();
			b = hex_md5(b);

			if ( a != b ) {

				document.getElementById('cforms_captcha'+no).className = "secinput cf_error";
				if ( all_valid ) {
					all_valid = false;
					code_err = true;
				    if (!last_one) last_one='cforms_captcha'+no;
				}
				custom_error = check_for_customerr('cforms_captcha'+no);

			}
		}

		//write out all custom errors
		if( show_err_ins=='y' ) write_customerr();

		//set focus to last erroneous input field
		if ( last_one!='' && jump_to_err=='y' ){
			location.hash='#'+last_one;
			document.getElementById(last_one).focus();
		}

		//all good?  if "upload file" field included, don't do ajax
		if ( all_valid && upload ){
			document.getElementById('sendbutton'+no).disabled=true;
			var newSENDBUTTON=document.createElement('input');
			newSENDBUTTON.type='hidden';
			newSENDBUTTON.name='sendbutton'+no;
			newSENDBUTTON.value='1';
			document.getElementById('cf_working'+no).parentNode.appendChild(newSENDBUTTON);
			document.getElementById('sendbutton'+no).style.cursor = "progress";
			document.getElementById('cforms'+no+'form').submit();
			return true;
		}
		else if ( all_valid ) {
			document.getElementById('sendbutton'+no).style.cursor = "progress";
			document.getElementById('sendbutton'+no).disabled = true;
			cforms_submitcomment(no);
			}

		if ( !all_valid && !code_err ){
			call_err(no,document.getElementById('cf_failure'+no),custom_error,1);
			return false
		}

		if ( !all_valid ){
			call_err(no,document.getElementById('cf_codeerr'+no),custom_error,1);
			return false
		}



		return false;

	} else	// if do_inner
		return true;


	//
	// track and store all errors until end
	function check_for_customerr(id) {

		parent_el = document.getElementById(id).parentNode;
		if( show_err_li=='y' ) {
			parent_el.className = "cf_li_err";
		}

		if ( all_custom_error[id] && (gotone=all_custom_error[id]) !='' ){

			if( show_err_ins=='y' ){
				insert_err_p[insert_err_count]=parent_el.id;

				ul	= document.createElement('UL');
				li	= document.createElement('LI');
				err	= document.createTextNode('');
				li.innerHTML = stripslashes(gotone);

				cl	= document.createAttribute('class');
				cl.nodeValue  = 'cf_li_text_err';

				ul.appendChild(li);
				ul.setAttributeNode(cl);

				insert_err[insert_err_count++] = ul;
			}

			if ( parent_el.id != '' )
				return custom_error + '<li><a href="#'+parent_el.id+'">' + gotone + ' &raquo;</li></a>';
			else
				return custom_error + '<li>' + gotone + '</li>';

		}
		else
			return custom_error;
	}

	function check_for_customerr_radio(id, cerr) {
		parent_el = document.getElementById( id.substr(0, id.length - 5) );
		if ( show_err_li == 'y' ) {
			parent_el.className = "cf-box-title cf_li_err";
		}

		if ( all_custom_error[cerr] && (gotone = all_custom_error[cerr]) != '' ) {
			if ( show_err_ins == 'y') {
				insert_err_p[insert_err_count] = parent_el.id;
				ul = document.createElement('UL');
				li = document.createElement('LI');
				err = document.createTextNode('');
				li.innerHTML = stripslashes(gotone);
				cl = document.createAttribute('class');
				cl.nodeValue = 'cf_li_text_err';
				ul.appendChild(li);
				ul.setAttributeNode(cl);
				insert_err[insert_err_count++] = ul;
			}
			if ( parent_el.id != '' )
				return custom_error + '<li><a href="#' + parent_el.id + '">' + gotone + ' &raquo;</li></a>';
			else
				return custom_error + '<li>' + gotone + '</li>';
		}
		else return custom_error;
	}

	//
	// at the end, spit it out
	function write_customerr() {
		for (n=0; n<insert_err_p.length;n++){
			if ( document.getElementById( insert_err_p[n] ) )
				document.getElementById( insert_err_p[n] ).insertBefore(insert_err[n],document.getElementById( insert_err_p[n] ).firstChild) ;
		}
	}

}

function stripslashes(str) {
	str=str.replace(/\\'/g,'\'');
	str=str.replace(/\\"/g,'"');
	str=str.replace(/\\\\/g,'\\');
	str=str.replace(/\\0/g,'\0');
	return str;
}

function doInnerXHTML(elementId, stringXHTML, stringDOM) {

	try {
	 	// alert("debug innerhtml");  //debug
	  	if ( document.getElementById(elementId+'a') )
			document.getElementById(elementId+'a').innerHTML = stringXHTML;
	  	if ( document.getElementById(elementId+'b') )
			document.getElementById(elementId+'b').innerHTML = stringXHTML;
		return true;
	}
	catch(ee) {
		return false;
	}

}



function cforms_submitcomment(no) {
		var regexp = new RegExp('[$][#][$]', ['g']);
		var prefix = '$#$';

		if ( no=='' ) params = '1'; else params = no;

		var objColl = document.getElementById('cforms'+no+'form').getElementsByTagName('*');

		for (var i = 0, j = objColl.length; i < j; i++) {

		    fld = objColl[i].nodeName.toLowerCase();
 			var typ = objColl[i].type;

				if ( fld == "input" || fld == "textarea" || fld == "select" ) {

						if ( typ == "checkbox" ) {

							if ( objColl[i].name.match(/\[\]/) ){
								group='';

								while ( i<j && isParentChkBoxGroup(objColl[i]) ){
									if ( objColl[i].type == 'checkbox' && objColl[i].name.match(/\[\]/) && objColl[i].checked ) {
										group = group + objColl[i].value + ',';
									}
									i++;
								}

								if ( group.length > 1 )
									params = params + prefix + group.substring(0,group.length-1);
								else
									params = params + prefix + "";

								//i=i-1;
							}
							else
								params = params + prefix + (objColl[i].checked?( (objColl[i].value!="")?objColl[i].value:"X"):"");

				 		} else
						if ( typ == "radio" ) {

								group = objColl[i].checked ? ( (objColl[i].value!="")?objColl[i].value:"X" ) : '' ;

								while ( i<j && isParentChkBoxGroup(objColl[i+1]) ){

									if ( objColl[i+1].type == 'radio' && objColl[i+1].checked ){
										group = group + ',' + objColl[i+1].value;
									}

									i++;
								}
								if ( group.charAt(0)==',' )
									params = params + prefix + group.substring(1,group.length);
								else
									params = params + prefix + group;


					 	} else
						if ( typ == "select-multiple" ) {
        						all_child_obj='';
						        for (z=0;z<objColl[i].childNodes.length; z++) {
						              if ( objColl[i].childNodes[z].nodeName.toLowerCase()=='option' && objColl[i].childNodes[z].selected ) {
            						        all_child_obj = all_child_obj + objColl[i].childNodes[z].value.replace(regexp, '$') + ','
            						  }
								}
						        params = params + prefix + all_child_obj.substring(0,all_child_obj.length-1);

					 	} else
						if ( typ == "hidden" && objColl[i].name.match(/comment_post_ID/) ) {
								params = params + '+++' + objColl[i].value;

					 	} else
						if ( typ == "hidden" && objColl[i].name.match(/cforms_pl/) ) {
								params = params + '+++' + objColl[i].value;

					 	} else
						if ( typ == "hidden" && objColl[i].name.match(/comment_parent/) ) {
								params = params + '+++' + objColl[i].value;

					 	} else
						if ( typ == "hidden" && objColl[i].className.match(/cfhidden/) ) {
								params = params + prefix + objColl[i].value;

					 	} else
						if ( typ != "hidden" && typ != "submit" && typ != "radio") {
								params = params + prefix + objColl[i].value.replace(regexp, '$');
					 	}

		 		}
		}
		if ( document.getElementById('cforms'+no+'form').action.match('lib_WPcomment.php') )
			params = params + '***';

		var post_data = 'action=submitcomment&_wpnonce=' + cforms2_ajax.nonces['submitcomment'] + "&rsargs=" + encodeURIComponent(params);
		jQuery.post( cforms2_ajax.url, post_data, function( data ) {cforms_setsuccessmessage(data);});
}

function isParentChkBoxGroup(el){
	while( el.parentNode ){
		if ( el.parentNode.className=='cf-box-group' )
			return true;
		else
			el = el.parentNode;
	}
	return false;
}

function cforms_setsuccessmessage(message) {

		hide = false;
		end = message.match(/|/) ? message.indexOf('|') : message.length;
		end = (end < 0) ? message.length : end;

		if ( message.match(/---/) ) {
			result = " failure";
		}
		else if ( message.match(/!!!/) ) {
			result = " mailerr";
		}
		else if ( message.match(/~~~/) ) {
			result = "success";
			hide = true;
		}
		else {
			result = "success";
		}

		var offset = message.indexOf('*$#');
		var no = message.substring(0,offset);
		var pop = message.charAt(offset+3); // check with return val from php call!

		if ( no == '1' ) no='';

		if ( !document.getElementById('cforms' + no + 'form').className.match(/cfnoreset/) )
			document.getElementById('cforms'+no+'form').reset();

		document.getElementById('sendbutton'+no).style.cursor = "auto";
		document.getElementById('sendbutton'+no).disabled = false;


		stringXHTML = message.substring(offset+4,end);


		// Is it a WP comment?
		if ( stringXHTML.match(/\$#\$/) ) {
			newcomment = stringXHTML.split('$#$');
			commentParent  = newcomment[0];
			newcommentText = newcomment[1];
			stringXHTML    = newcomment[2];

			if ( document.getElementById(commentParent) ){
				var alt = '';
				var allLi = document.getElementById(commentParent).childNodes.length - 1;
				for (i=allLi; i>=0; i--){
					var elLi = document.getElementById(commentParent).childNodes[i];
					if ( elLi.nodeType!='3' && elLi.tagName.toLowerCase() == 'li' ) {
						if ( elLi.className.match(/alt/) )
							alt='alt';
						i=-1;
					}
				}

				if( alt=='alt' )
					newcommentText = newcommentText.replace('class="alt"', '');

				document.getElementById(commentParent).innerHTML = document.getElementById(commentParent).innerHTML + newcommentText;

				//wp ajax edit support
				if( window.AjaxEditComments )
					AjaxEditComments.init();
			}

			// ajax comment plugin?
			var dEl = newcommentText.match(/edit-comment-(user|admin)-link(s|-)[^" ]+/);
			if ( dEl!=null && dEl[0]!='' && document.getElementById( dEl[0] ) ){
				document.getElementById( dEl[0] ).style.display = 'block';
			}
		}



		// for both message boxes
		isA = false;
        ucm = ( parseInt(no)>1 )? ' '+result+no : '';
	  	if ( document.getElementById('usermessage'+no+'a') ){
			document.getElementById('usermessage'+no+'a').className = "cf_info "+result+ucm;
			isA = true;
		}
	  	if ( document.getElementById('usermessage'+no+'b') && !(hide && isA) )
			document.getElementById('usermessage'+no+'b').className = "cf_info "+result+ucm;

		doInnerXHTML('usermessage'+no, stringXHTML, '');

		if ( hide ) {
			document.getElementById('cforms'+no+'form').style.display = 'none';
			document.getElementById('ll'+no).style.display = 'none';
			if ( !message.match(/>>>/) )
				location.hash = '#usermessage' + no + 'a';
		}

		if (pop == 'y'){
			stringXHTML = stringXHTML.replace(/<br.?\/>/g,'\r\n');
			stringXHTML = stringXHTML.replace(/(<.?strong>|<.?b>)/g,'*');
			stringXHTML = stringXHTML.replace(/(<([^>]+)>)/ig, '');
			alert( stringXHTML );  //debug
		}
		
		if ( message.match(/>>>/) ) {
			location.href = message.substring( message.indexOf('|>>>') + 4, message.length );
		}
}



/*
 * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
 * Digest Algorithm, as defined in RFC 1321.
 * Version 2.1 Copyright (C) Paul Johnston 1999 - 2002.
 * Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet
 * Distributed under the BSD License
 * See http://pajhome.org.uk/crypt/md5 for more info.
 */
/*
 * Configurable variables. You may need to tweak these to be compatible with
 * the server-side, but the defaults work in most cases.
 */
var hexcase = 0;  /* hex output format. 0 - lowercase; 1 - uppercase        */
var b64pad  = ""; /* base-64 pad character. "=" for strict RFC compliance   */
var chrsz   = 8;  /* bits per input character. 8 - ASCII; 16 - Unicode      */
/*
 * These are the functions you'll usually want to call
 * They take string arguments and return either hex or base-64 encoded strings
 */
function hex_md5(s){ return binl2hex(core_md5(str2binl(s), s.length * chrsz));}
function b64_md5(s){ return binl2b64(core_md5(str2binl(s), s.length * chrsz));}
function str_md5(s){ return binl2str(core_md5(str2binl(s), s.length * chrsz));}
function hex_hmac_md5(key, data) { return binl2hex(core_hmac_md5(key, data)); }
function b64_hmac_md5(key, data) { return binl2b64(core_hmac_md5(key, data)); }
function str_hmac_md5(key, data) { return binl2str(core_hmac_md5(key, data)); }
/*
 * Perform a simple self-test to see if the VM is working
 */
function md5_vm_test()
{
  return hex_md5("abc") == "900150983cd24fb0d6963f7d28e17f72";
}
/*
 * Calculate the MD5 of an array of little-endian words, and a bit length
 */
function core_md5(x, len)
{
  /* append padding */
  x[len >> 5] |= 0x80 << ((len) % 32);
  x[(((len + 64) >>> 9) << 4) + 14] = len;
  var a =  1732584193;
  var b = -271733879;
  var c = -1732584194;
  var d =  271733878;
  for(var i = 0; i < x.length; i += 16)
  {
    var olda = a;
    var oldb = b;
    var oldc = c;
    var oldd = d;
    a = md5_ff(a, b, c, d, x[i+ 0], 7 , -680876936);
    d = md5_ff(d, a, b, c, x[i+ 1], 12, -389564586);
    c = md5_ff(c, d, a, b, x[i+ 2], 17,  606105819);
    b = md5_ff(b, c, d, a, x[i+ 3], 22, -1044525330);
    a = md5_ff(a, b, c, d, x[i+ 4], 7 , -176418897);
    d = md5_ff(d, a, b, c, x[i+ 5], 12,  1200080426);
    c = md5_ff(c, d, a, b, x[i+ 6], 17, -1473231341);
    b = md5_ff(b, c, d, a, x[i+ 7], 22, -45705983);
    a = md5_ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
    d = md5_ff(d, a, b, c, x[i+ 9], 12, -1958414417);
    c = md5_ff(c, d, a, b, x[i+10], 17, -42063);
    b = md5_ff(b, c, d, a, x[i+11], 22, -1990404162);
    a = md5_ff(a, b, c, d, x[i+12], 7 ,  1804603682);
    d = md5_ff(d, a, b, c, x[i+13], 12, -40341101);
    c = md5_ff(c, d, a, b, x[i+14], 17, -1502002290);
    b = md5_ff(b, c, d, a, x[i+15], 22,  1236535329);
    a = md5_gg(a, b, c, d, x[i+ 1], 5 , -165796510);
    d = md5_gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
    c = md5_gg(c, d, a, b, x[i+11], 14,  643717713);
    b = md5_gg(b, c, d, a, x[i+ 0], 20, -373897302);
    a = md5_gg(a, b, c, d, x[i+ 5], 5 , -701558691);
    d = md5_gg(d, a, b, c, x[i+10], 9 ,  38016083);
    c = md5_gg(c, d, a, b, x[i+15], 14, -660478335);
    b = md5_gg(b, c, d, a, x[i+ 4], 20, -405537848);
    a = md5_gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
    d = md5_gg(d, a, b, c, x[i+14], 9 , -1019803690);
    c = md5_gg(c, d, a, b, x[i+ 3], 14, -187363961);
    b = md5_gg(b, c, d, a, x[i+ 8], 20,  1163531501);
    a = md5_gg(a, b, c, d, x[i+13], 5 , -1444681467);
    d = md5_gg(d, a, b, c, x[i+ 2], 9 , -51403784);
    c = md5_gg(c, d, a, b, x[i+ 7], 14,  1735328473);
    b = md5_gg(b, c, d, a, x[i+12], 20, -1926607734);
    a = md5_hh(a, b, c, d, x[i+ 5], 4 , -378558);
    d = md5_hh(d, a, b, c, x[i+ 8], 11, -2022574463);
    c = md5_hh(c, d, a, b, x[i+11], 16,  1839030562);
    b = md5_hh(b, c, d, a, x[i+14], 23, -35309556);
    a = md5_hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
    d = md5_hh(d, a, b, c, x[i+ 4], 11,  1272893353);
    c = md5_hh(c, d, a, b, x[i+ 7], 16, -155497632);
    b = md5_hh(b, c, d, a, x[i+10], 23, -1094730640);
    a = md5_hh(a, b, c, d, x[i+13], 4 ,  681279174);
    d = md5_hh(d, a, b, c, x[i+ 0], 11, -358537222);
    c = md5_hh(c, d, a, b, x[i+ 3], 16, -722521979);
    b = md5_hh(b, c, d, a, x[i+ 6], 23,  76029189);
    a = md5_hh(a, b, c, d, x[i+ 9], 4 , -640364487);
    d = md5_hh(d, a, b, c, x[i+12], 11, -421815835);
    c = md5_hh(c, d, a, b, x[i+15], 16,  530742520);
    b = md5_hh(b, c, d, a, x[i+ 2], 23, -995338651);
    a = md5_ii(a, b, c, d, x[i+ 0], 6 , -198630844);
    d = md5_ii(d, a, b, c, x[i+ 7], 10,  1126891415);
    c = md5_ii(c, d, a, b, x[i+14], 15, -1416354905);
    b = md5_ii(b, c, d, a, x[i+ 5], 21, -57434055);
    a = md5_ii(a, b, c, d, x[i+12], 6 ,  1700485571);
    d = md5_ii(d, a, b, c, x[i+ 3], 10, -1894986606);
    c = md5_ii(c, d, a, b, x[i+10], 15, -1051523);
    b = md5_ii(b, c, d, a, x[i+ 1], 21, -2054922799);
    a = md5_ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
    d = md5_ii(d, a, b, c, x[i+15], 10, -30611744);
    c = md5_ii(c, d, a, b, x[i+ 6], 15, -1560198380);
    b = md5_ii(b, c, d, a, x[i+13], 21,  1309151649);
    a = md5_ii(a, b, c, d, x[i+ 4], 6 , -145523070);
    d = md5_ii(d, a, b, c, x[i+11], 10, -1120210379);
    c = md5_ii(c, d, a, b, x[i+ 2], 15,  718787259);
    b = md5_ii(b, c, d, a, x[i+ 9], 21, -343485551);
    a = safe_add(a, olda);
    b = safe_add(b, oldb);
    c = safe_add(c, oldc);
    d = safe_add(d, oldd);
  }
  return Array(a, b, c, d);
}
/*
 * These functions implement the four basic operations the algorithm uses.
 */
function md5_cmn(q, a, b, x, s, t)
{
  return safe_add(bit_rol(safe_add(safe_add(a, q), safe_add(x, t)), s),b);
}
function md5_ff(a, b, c, d, x, s, t)
{
  return md5_cmn((b & c) | ((~b) & d), a, b, x, s, t);
}
function md5_gg(a, b, c, d, x, s, t)
{
  return md5_cmn((b & d) | (c & (~d)), a, b, x, s, t);
}
function md5_hh(a, b, c, d, x, s, t)
{
  return md5_cmn(b ^ c ^ d, a, b, x, s, t);
}
function md5_ii(a, b, c, d, x, s, t)
{
  return md5_cmn(c ^ (b | (~d)), a, b, x, s, t);
}
/*
 * Calculate the HMAC-MD5, of a key and some data
 */
function core_hmac_md5(key, data)
{
  var bkey = str2binl(key);
  if(bkey.length > 16) bkey = core_md5(bkey, key.length * chrsz);
  var ipad = Array(16), opad = Array(16);
  for(var i = 0; i < 16; i++)
  {
    ipad[i] = bkey[i] ^ 0x36363636;
    opad[i] = bkey[i] ^ 0x5C5C5C5C;
  }
  var hash = core_md5(ipad.concat(str2binl(data)), 512 + data.length * chrsz);
  return core_md5(opad.concat(hash), 512 + 128);
}
/*
 * Add integers, wrapping at 2^32. This uses 16-bit operations internally
 * to work around bugs in some JS interpreters.
 */
function safe_add(x, y)
{
  var lsw = (x & 0xFFFF) + (y & 0xFFFF);
  var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
  return (msw << 16) | (lsw & 0xFFFF);
}
/*
 * Bitwise rotate a 32-bit number to the left.
 */
function bit_rol(num, cnt)
{
  return (num << cnt) | (num >>> (32 - cnt));
}
/*
 * Convert a string to an array of little-endian words
 * If chrsz is ASCII, characters >255 have their hi-byte silently ignored.
 */
function str2binl(str)
{
  var bin = Array();
  var mask = (1 << chrsz) - 1;
  for(var i = 0; i < str.length * chrsz; i += chrsz)
    bin[i>>5] |= (str.charCodeAt(i / chrsz) & mask) << (i%32);
  return bin;
}
/*
 * Convert an array of little-endian words to a string
 */
function binl2str(bin)
{
  var str = "";
  var mask = (1 << chrsz) - 1;
  for(var i = 0; i < bin.length * 32; i += chrsz)
    str += String.fromCharCode((bin[i>>5] >>> (i % 32)) & mask);
  return str;
}
/*
 * Convert an array of little-endian words to a hex string.
 */
function binl2hex(binarray)
{
  var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
  var str = "";
  for(var i = 0; i < binarray.length * 4; i++)
  {
    str += hex_tab.charAt((binarray[i>>2] >> ((i%4)*8+4)) & 0xF) +
           hex_tab.charAt((binarray[i>>2] >> ((i%4)*8  )) & 0xF);
  }
  return str;
}
/*
 * Convert an array of little-endian words to a base-64 string
 */
function binl2b64(binarray)
{
  var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
  var str = "";
  for(var i = 0; i < binarray.length * 4; i += 3)
  {
    var triplet = (((binarray[i   >> 2] >> 8 * ( i   %4)) & 0xFF) << 16)
                | (((binarray[i+1 >> 2] >> 8 * ((i+1)%4)) & 0xFF) << 8 )
                |  ((binarray[i+2 >> 2] >> 8 * ((i+2)%4)) & 0xFF);
    for(var j = 0; j < 4; j++)
    {
      if(i * 8 + j * 6 > binarray.length * 32) str += b64pad;
      else str += tab.charAt((triplet >> 6*(3-j)) & 0x3F);
    }
  }
  return str;
}

function readcookie(no) {
	var nameEQ = "turing_string_"+no+"=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ')
			c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0)
			return unescape(c.substring(nameEQ.length,c.length));
	}
	return '';
}
