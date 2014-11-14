/*
 * Copyright (c) 2007-2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
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

function reset_captcha(no){
    no = no || '';
    document.getElementById('cf_captcha_img' + no).src = cforms2_ajax.url
        + '?action=cforms2_reset_captcha&_wpnonce='+cforms2_ajax.nonces['reset_captcha']
        + '&ts=' + no + '&rnd=' + Math.round( Math.random() * 999999 );
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

    var doInnerXHTML = function (elementId, stringXHTML) {

        try {
            if ( document.getElementById(elementId+'a') )
                document.getElementById(elementId+'a').innerHTML = stringXHTML;
            if ( document.getElementById(elementId+'b') )
                document.getElementById(elementId+'b').innerHTML = stringXHTML;
            return true;
        }
        catch(ee) {
            return false;
        }

    };

    var stripslashes = function (str) {
        str = str.replace(/\\'/g,'\'');
        str = str.replace(/\\"/g,'"');
        str = str.replace(/\\\\/g,'\\');
        str = str.replace(/\\0/g,'\0');
        return str;
    };

    var set_customerr = function (id, parent_el) {
        var gotone = all_custom_error[id];
        if ( all_custom_error[id] && gotone !='' ){

            if( show_err_ins=='y' ){
                insert_err_p[insert_err_count] = parent_el.id;

                var ul = document.createElement('UL');
                var li = document.createElement('LI');
                li.innerHTML = stripslashes(gotone);

                ul.appendChild(li);
                ul.setAttribute('class', 'cf_li_text_err');

                insert_err[insert_err_count++] = ul;
            }

            if ( parent_el.id != '' )
                return custom_error + '<li><a href="#'+parent_el.id+'">' + gotone + ' &raquo;</li></a>';
            else
                return custom_error + '<li>' + gotone + '</li>';

        }
        else
            return custom_error;
    };

    // track and store all errors until end
    var check_for_customerr = function (id) {

        var parent_el = document.getElementById(id).parentNode;
        if( show_err_li=='y' ) {
            parent_el.className = "cf_li_err";
        }

        return set_customerr(id, parent_el);
    };

    var check_for_customerr_radio = function (id, cerr) {
        var parent_el = document.getElementById( id.substr(0, id.length - 5) );
        if ( show_err_li == 'y' ) {
            parent_el.className = "cf-box-title cf_li_err";
        }

        return set_customerr(cerr, parent_el);
    };

    var isParentChkBoxGroup = function (el){
        while( el.parentNode ){
            if ( el.parentNode.className=='cf-box-group' )
                return true;
            else
                el = el.parentNode;
        }
        return false;
    };

    var cforms_submitcomment = function (no) {
        var regexp = new RegExp('[$][#][$]', ['g']);
        var prefix = '$#$';

        var params;
        if ( no=='' ) params = '1'; else params = no;

        var objColl = document.getElementById('cforms'+no+'form').getElementsByTagName('*');

        for (var i = 0, j = objColl.length; i < j; i++) {

            var fld = objColl[i].nodeName.toLowerCase();
            var typ = objColl[i].type;

            if ( fld == "input" || fld == "textarea" || fld == "select" ) {

                if ( typ == "checkbox" ) {

                    if ( objColl[i].name.match(/\[\]/) ){
                        var group='';

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
                    }
                    else
                        params = params + prefix + (objColl[i].checked?( (objColl[i].value!="")?objColl[i].value:"X"):"");

                } else if ( typ == "radio" ) {

                    var group = objColl[i].checked ? ( (objColl[i].value!="")?objColl[i].value:"X" ) : '' ;

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


                } else if ( typ == "select-multiple" ) {
                    var all_child_obj='';
                    for (z=0;z<objColl[i].childNodes.length; z++) {
                        if ( objColl[i].childNodes[z].nodeName.toLowerCase()=='option' && objColl[i].childNodes[z].selected ) {
                            all_child_obj = all_child_obj + objColl[i].childNodes[z].value.replace(regexp, '$') + ',';
                        }
                    }
                    params = params + prefix + all_child_obj.substring(0,all_child_obj.length-1);
                } else if ( typ == "hidden" && objColl[i].name.match(/comment_post_ID/) ) {
                    params = params + '+++' + objColl[i].value;
                } else if ( typ == "hidden" && objColl[i].name.match(/cforms_pl/) ) {
                    params = params + '+++' + objColl[i].value;
                } else if ( typ == "hidden" && objColl[i].name.match(/comment_parent/) ) {
                    params = params + '+++' + objColl[i].value;
                } else if ( typ == "hidden" && objColl[i].className.match(/cfhidden/) ) {
                    params = params + prefix + objColl[i].value;
                } else if ( typ != "hidden" && typ != "submit" && typ != "radio") {
                    params = params + prefix + objColl[i].value.replace(regexp, '$');
                }
            }
        }

        var post_data = 'action=submitcomment&_wpnonce='
            + cforms2_ajax.nonces['submitcomment']
            + "&rsargs="
            + encodeURIComponent(params);
        jQuery.post(
            cforms2_ajax.url,
            post_data,
            function( data ) {cforms_setsuccessmessage(data);}
        );
    };

    var cforms_setsuccessmessage = function (message) {

        var hide = false;
        var end = message.match(/|/) ? message.indexOf('|') : message.length;
        end = (end < 0) ? message.length : end;

        var result;
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


        var stringXHTML = message.substring(offset+4,end);


        // Is it a WP comment?
        if ( stringXHTML.match(/\$#\$/) ) {
            var newcomment = stringXHTML.split('$#$');
            var commentParent  = newcomment[0];
            var newcommentText = newcomment[1];
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
        var isA = false;
        var ucm = ( parseInt(no)>1 ) ? ' '+result+no : '';
        if ( document.getElementById('usermessage'+no+'a') ) {
            document.getElementById('usermessage'+no+'a').className = "cf_info "+result+ucm;
            isA = true;
        }
        if ( document.getElementById('usermessage'+no+'b') && !(hide && isA) ) {
            document.getElementById('usermessage'+no+'b').className = "cf_info "+result+ucm;
        }

        doInnerXHTML('usermessage'+no, stringXHTML);

        if ( hide ) {
            document.getElementById('cforms'+no+'form').style.display = 'none';
            if ( !message.match(/>>>/) )
                location.hash = '#usermessage' + no + 'a';
        }

        if (pop == 'y') {
            stringXHTML = stringXHTML.replace(/<br.?\/>/g,'\r\n');
            stringXHTML = stringXHTML.replace(/(<.?strong>|<.?b>)/g,'*');
            stringXHTML = stringXHTML.replace(/(<([^>]+)>)/ig, '');
            alert( stringXHTML );  //debug
        }
        
        if ( message.match(/>>>/) ) {
            location.href = message.substring( message.indexOf('|>>>') + 4, message.length );
        }
    };

    var write_customerr = function () {
        for (var n = 0; n < insert_err_p.length; n++) {
            if (document.getElementById( insert_err_p[n] )) {
                document.getElementById( insert_err_p[n] ).insertBefore(
                    insert_err[n],
                    document.getElementById( insert_err_p[n] ).firstChild
                );
            }
        }
    };

    if (!no) no='';

    var msgbox = 'usermessage' + no;
    if( document.getElementById(msgbox+'a') ){
        document.getElementById(msgbox+'a').className = "cf_info waiting";
    }
    if( document.getElementById(msgbox+'b') ){
        document.getElementById(msgbox+'b').className = "cf_info waiting";
    }

    var waiting = decodeURI(document.getElementById('cf_working' + no).value);
    waiting = waiting.replace(/\\/g,"");

    var insert_err = new Array();
    var insert_err_p = new Array();
    var insert_err_count = 0;

    var all_custom_error = new Array();

    var rest = document.getElementById('cf_customerr' + no).value.substr(3);
    var show_err_li  = document.getElementById('cf_customerr' + no).value.substr(0,1);
    var show_err_ins = document.getElementById('cf_customerr' + no).value.substr(1,1);
    var jump_to_err = document.getElementById('cf_customerr' + no).value.substr(2,1);

    var error_container = decodeURIComponent( rest );
    error_container = error_container.split('|');

    for ( i=0; i<error_container.length; i++ ) {
        var keyvalue = error_container[i].split('$#$');
        all_custom_error[keyvalue[0]] = keyvalue[1];
    }

    var custom_error = '';


    if( !doInnerXHTML(msgbox, waiting) ) {
        return true;
    }

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
    while ( objColl.length > 0 ) {
        objColl[0].parentNode.removeChild( objColl[0] );
    }


    objColl = document.getElementById('cforms'+no+'form').getElementsByTagName('*');
    var last_one = false;

    for (var i = 0, j = objColl.length; i < j; i++) {

        var temp = objColl[i].className;

        var newclass = '';
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

        var fld = objColl[i].nodeName.toLowerCase();
        var typ = objColl[i].type;

        if ( (fld == "input" || fld == "textarea" || fld == "select") && !( typ=="hidden" || typ=="submit") ) {

            if ( temp.match(/required/) && !temp.match(/email/) && typ != "radio" ) {

                newclass = newclass + ' fldrequired';

                var n = objColl[i].nextSibling;
                var p = objColl[i].previousSibling;

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
                    } else {
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
                        if (!last_one && objColl[i].id != '')
                            last_one=objColl[i].id;

                        custom_error = check_for_customerr(objColl[i].id);

                    }

                } else if ( objColl[i].value=='' ) {

                    newclass = newclass + ' cf_error';
                    all_valid=false;
                    if (!last_one && objColl[i].id != '')
                        last_one=objColl[i].id;

                    custom_error = check_for_customerr(objColl[i].id);

                }

            }

            if ( temp.match(/email/) ) {
                newclass = newclass + ' fldemail';
                if ( !(objColl[i].value=='' && !temp.match(/required/)) ) {
                    if ( !regexp_e.test(objColl[i].value) ) {
                        newclass = newclass + ' fldrequired cf_error';
                        all_valid=false;
                        if (!last_one) last_one=objColl[i].name; // TODO fix?: if (!last_one && objColl[i].id != '') last_one=objColl[i].id;

                        custom_error = check_for_customerr(objColl[i].id);

                    }
                    else
                        newclass = newclass + ' fldrequired';
                }

            }

            if ( temp.match(/required/) && temp.match(/cf-box-b/) && typ.match(/radio/) ) {
                var temp_i = i;
                var radio_valid = false;

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
        var regexp = 1;
        if ( objColl[i] && document.getElementById(objColl[i].id+'_regexp') ) {

            var obj_regexp = document.getElementById( objColl[i].id+'_regexp' );
            var INPval;
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
    if ( document.getElementById('cforms_q' + no)
      && document.getElementById('cforms_a' + no).value
      != jQuery.md5( encodeURI( document.getElementById('cforms_q' + no).value.toLowerCase() ) )
    ) {
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

        var readcookie = function (no) {
            var nameEQ = "turing_string_"+no+"=";
            var ca = document.cookie.split(';');
            for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ')
                    c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0)
                    return decodeURIComponent(c.substring(nameEQ.length,c.length));
            }
            return '';
        };

        // for captcha!
        var read_cookie = readcookie    (no);
        var cookie_part = read_cookie.split('+');

        var a = cookie_part[1];
        var b = document.getElementById('cforms_captcha'+no).value;

        if ( cookie_part[0]=='i' ) // case insensitive?
            b = b.toLowerCase();
        b = jQuery.md5(b);

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

    var call_err = function (no,err,custom_error,popFlag){

        //temp. turn send button back on
        document.getElementById('sendbutton'+no).style.cursor = "auto";
        document.getElementById('sendbutton'+no).disabled = false;

        if ( custom_error!='' ) custom_error = '<ol>'+custom_error+'</ol>';

        err = decodeURI( err.value ) + custom_error;

        var stringXHTML = err.replace(/(\r\n)/g, '<br />');

        var msgbox = 'usermessage'+no;
        var ucm = ( parseInt(no)>1 ) ? ' failure' + no : '';

        if( document.getElementById(msgbox+'a') )
            document.getElementById(msgbox+'a').className = "cf_info failure" + ucm;
        if( document.getElementById(msgbox+'b') )
            document.getElementById(msgbox+'b').className = "cf_info failure" + ucm;

        doInnerXHTML(msgbox, stringXHTML.replace(/\\/g,""));

        //popup error
        err = err.replace(/\\/g,"");
        if ( document.getElementById('cf_popup'+no).value.charAt(popFlag) == 'y'){
            err = err.replace(/<li>/g,"\r\n");
            err = err.replace(/<.?strong>/g,'*');
            err = err.replace(/(<([^>]+)>)/ig, '');
            err = err.replace(/&raquo;/ig, '');
            alert( err );
        }
    };

    if ( !all_valid && !code_err ){
        call_err(no,document.getElementById('cf_failure'+no),custom_error,1);
        return false;
    }

    if ( !all_valid ){
        call_err(no,document.getElementById('cf_codeerr'+no),custom_error,1);
        return false;
    }

    return false;

}
