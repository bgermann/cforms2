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

var focusedFormControl = null;

jQuery(function() {

    window.setshow = function (el) {
        var val=readcookie();
        var c = 'cformsshowui=';
        if ( document.getElementById('p'+el) && document.getElementById('o'+el) && val.charAt(el)==1 ) {
            jQuery("#p" + el).attr("class", "cflegend");
            jQuery("div", "#p" + el).attr("class", "blindminus");
            jQuery("#o" + el).show();
        }
        var a, b;
        if ( el>0 ) a = val.slice(0,el); else a='';
        if ( el<val.length ) b = val.slice((el+1),val.length); else b='';
        document.cookie=c+a+0+b+";expires="+timeout.toGMTString()+";";
        return false;
    };

    var readcookie = function () {
        var nameEQ = "cformsshowui=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ')
                c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0)
                return c.substring(nameEQ.length,c.length);
        }
        return null;
    };

    var val = readcookie();
    var timeout = new Date(jQuery.now() + 3 * 86400000);

    if( !val || !(val.length===35) ) {
        document.cookie="cformsshowui=11111111111111111111111111111111111;expires="+timeout.toGMTString()+";";
    }

    // moving dialog box options
    var cfmoveup = function () {
        var prevEl = jQuery(this).parent().prev();
        if ( prevEl.attr('id') != undefined )
            prevEl.insertAfter( jQuery(this).parent() );
        return false;
    };

    // moving dialog box options
    var cfmovedown = function () {
        var nextEl = jQuery(this).parent().next();
        if ( nextEl.attr('id') != undefined )
            nextEl.insertBefore( jQuery(this).parent() );
        return false;
    };

    var trackChg = false;

    jQuery('.wrap').click(function(e) {
        if(e.target.className.match(/allchk/)) {
            jQuery(e.target).focus();
        };
    } );

    /* INFO BUTTONS */
    jQuery('.infotxt').css({display:'none'});
    jQuery('a.infobutton').css({display:'inline'});
    jQuery('a.infobutton').click(function(){
        if ( jQuery('#'+this.name).css('display')=='none' )
            jQuery('#'+this.name).css({display:''});
        else
            jQuery('#'+this.name).css({display:'none'});
        return false;
    });


    /* GLOBAL VARIABLES */
    var hasht, groupcount, totalcount;

    /* MODIFY THE OK BUTTON CLICK EVENT */
    jQuery('a#ok').click(function() {

        var l_label=jQuery('#cf_edit_label').val();
        if ( l_label == null)
            l_label='';
        var l_label_group=jQuery('#cf_edit_label_group').val();
        if ( l_label_group == null)
            l_label_group='';
        var l_label_select=jQuery('#cf_edit_label_select').val();
        if ( l_label_select == null)
            l_label_select='';
        var l_left=jQuery('#cf_edit_label_left').val();
        if ( l_left == null)
            l_left='';

        var line = l_left + l_label + l_label_group + l_label_select;

        var l_css=jQuery('#cf_edit_css').val();
        if ( l_css == null)
            l_css='';
        else
            l_css='|'+l_css;
        var l_style=jQuery('#cf_edit_style').val();
        if ( l_style == null) l_style=''; else l_style='|'+l_style;

        line += l_css + l_style;

        var l_default=jQuery('#cf_edit_default').val();
        if ( l_default == null) l_default=''; else l_default='|'+l_default;
        var l_regexp=jQuery('#cf_edit_regexp').val();
        if ( l_regexp == null) l_regexp=''; else l_regexp='|'+l_regexp;

        line += l_default + l_regexp;

        var l_right=jQuery('#cf_edit_label_right').val();
        if ( l_right == null) l_right=''; else l_right='#'+l_right;

        line += l_right;

        var l_chkstate=jQuery('#cf_edit_checked').is(':checked');

        if(!l_chkstate)l_chkstate=''; else { if (l_chkstate) l_chkstate='|set:true'; }

        var l_title=jQuery('#cf_edit_title').val();
        if ( l_title == null) l_title=''; else { if (l_title!='') l_title='|title:'+l_title; }

        var l_cerr=jQuery('#cf_edit_customerr').val();
        if ( l_cerr == null) l_cerr=''; else { if (l_cerr!='') l_cerr='|err:'+l_cerr; }

        var autocomplete = jQuery('#cf_edit_checked_autocomplete').is(':checked') ? '1' : '0';
        var autofocus = jQuery('#cf_edit_checked_autofocus').is(':checked') ? '1' : '0';
        var min = jQuery('#cf_edit_min').length ? jQuery('#cf_edit_min').val() : '';
        var max = jQuery('#cf_edit_max').length ? jQuery('#cf_edit_max').val() : '';
        var pattern = jQuery('#cf_edit_pattern').length ? jQuery('#cf_edit_pattern').val() : '';
        var step = jQuery('#cf_edit_step').length ? jQuery('#cf_edit_step').val() : '';
        var placeholder = jQuery('#cf_edit_placeholder').length ? jQuery('#cf_edit_placeholder').val() : '';
        var sep = '\u00A4';
        var l_html5;
        if(jQuery('#html5formfields').length)
            l_html5 = '|html5:'+autocomplete+sep+autofocus+sep+min+sep+max+sep+pattern+sep+step+sep+placeholder;
        else l_html5 = '';

        jQuery('.cf_edit_group_new').each( function (index, domEle) {
            var temp_o=jQuery('#cf_edit_group_o'+domEle.id.substr(10)).val();
            if ( temp_o == null)
                temp_o=''; else temp_o='#'+temp_o;
            var temp_v=jQuery('#cf_edit_group_v'+domEle.id.substr(10)).val();
            if ( temp_v == null)
                temp_v='';
            else {
                if (temp_v!='') temp_v='|'+temp_v;
            }
            var temp_chk = jQuery('#cf_edit_group_chked'+domEle.id.substr(10)).is(':checked');
            if ( !temp_chk ) temp_chk=''; else { if (temp_chk) temp_chk='|set:true'; }
            var temp_br = jQuery('#cf_edit_group_br'+domEle.id.substr(10)).is(':checked');
            if ( !temp_br ) temp_br=''; else { if (temp_br) temp_br='#'; }
            line += temp_o + temp_v + temp_chk + temp_br;

        } );

        hasht.parentNode.previousSibling.value = line + l_chkstate + l_title + l_cerr + l_html5;
    });

    /* LAUNCHED AFTER AJAX */
    var load = function() {

        /* GET CURRENT CONFIG */
        var line = hasht.parentNode.previousSibling.value;

        var content;
        if ( line.indexOf('|html5:') > 0 ) {
            content = line.split('|html5:');
            line = content[0];
            var sep = '\u00A4';
            content = content[1].split(sep);
            if ( content[0]=='1' ) jQuery('#cf_edit_checked_autocomplete').attr('checked', 'checked');
            if ( content[1]=='1' ) jQuery('#cf_edit_checked_autofocus').attr('checked', 'checked');
            if ( content[2]!=''  ) jQuery('#cf_edit_min').val(content[2]);
            if ( content[3]!=''  ) jQuery('#cf_edit_max').val(content[3]);
            if ( content[4]!=''  ) jQuery('#cf_edit_pattern').val(content[4]);
            if ( content[5]!=''  ) jQuery('#cf_edit_step').val(content[5]);
            if ( content[6]!=''  ) jQuery('#cf_edit_placeholder').val(content[6]);
        }

        if ( document.getElementById('cf_edit_customerr') ){
            content = line.split('|err:');
            jQuery('#cf_edit_customerr').val( content[1] );
            line = content[0];
        }

        if ( document.getElementById('cf_edit_title') ){
            content = line.split('|title:');
            jQuery('#cf_edit_title').val( content[1] );
            line = content[0];
        }

        if ( document.getElementById('cf_edit_checked') ){
            content = line.split('|set:');
            if(console) console.log(content[0]);
            if(console) console.log(content[1]);

            if( content[1] != undefined && content[1].match(/true/) )
                jQuery('#cf_edit_checked').attr( 'checked', 'checked' );
            else
                jQuery('#cf_edit_checked').removeAttr( 'checked' );
            line = content[0];
        }

        if ( document.getElementById('cf_edit_css') ){
            content = line.split('|');
            jQuery('#cf_edit_label').val( content[0] );
            jQuery('#cf_edit_css').val( content[1] );
            jQuery('#cf_edit_style').val( content[2] );
            line = '';
        }
        else if ( document.getElementById('cf_edit_regexp') || document.getElementById('cf_edit_default') ){
            var regexpval;
            content = line.split('|');
            jQuery('#cf_edit_label').val( content[0] );
            jQuery('#cf_edit_default').val( content[1] );
            if ( content[1]==null )
                content[1] = '';
            regexpval = line.substr(content[0].length+content[1].length+2);
            jQuery('#cf_edit_regexp').val( regexpval );
            line = '';
        }
        else if ( document.getElementById('cf_edit_label_left') ){
            content = line.split('#');
            jQuery('#cf_edit_label_left').val( content[0] );
            jQuery('#cf_edit_label_right').val( content[1] );
            line = '';
        }
        else if ( document.getElementById('cf_edit_label_group') ){

            content = line.split('#');
            totalcount = groupcount = 0;

            jQuery('a#add_group_button').click(function() {
                groupcount++; totalcount++;
                jQuery('<div class="cf_edit_group_new" id="edit_group'+groupcount+'">'+
                    '<a href="#" id="rgi_'+groupcount+'" class="cf_edit_minus dashicons dashicons-dismiss"></a>'+
                    '<input type="text" id="cf_edit_group_o'        + groupcount
                        + '" name="cf_edit_group_o'    + groupcount + '" value=""/>'+
                    '<input type="text" id="cf_edit_group_v'        + groupcount
                        + '" name="cf_edit_group_v'    + groupcount + '" value="" class="inpOpt"/>'+
                    '<input type="checkbox" id="cf_edit_group_chked'+ groupcount
                        + '" name="cf_edit_group_chked'+ groupcount + '" class="allchk cf_chked"/>'+
                    '<input type="checkbox" id="cf_edit_group_br'   + groupcount
                        + '" name="cf_edit_group_br'   + groupcount + '" value="lbr" class="allchk cf_br"/>'+
                    '<a href="javascript:void(0);" class="cf_edit_move_up dashicons dashicons-arrow-up-alt"></a>'+
                    '<a href="javascript:void(0);" class="cf_edit_move_down dashicons dashicons-arrow-down-alt"></a></div>'
                ).appendTo("#cf_edit_groups");

                jQuery('a.cf_edit_move_up','#edit_group'+groupcount).bind("click", cfmoveup);
                jQuery('a.cf_edit_move_down','#edit_group'+groupcount).bind("click", cfmovedown);

                jQuery('#rgi_'+groupcount).bind("click", function(){
                    jQuery(this).parent().remove();
                    totalcount--;
                    if ( totalcount <= 5 ) { jQuery('#cf_edit_groups').css( { height:"" } ); }
                    return false; });

                if ( totalcount > 5 )
                    jQuery('#cf_edit_groups').css( { height:"9em", overflowY:"auto" } );

                return false;

             });

            jQuery('#cf_edit_label_group').val( content[0] );

            for( var i=1 ; i<content.length ; i++ ) {

                var tmp, chk;
                if ( content[i]!='' && content[i].indexOf('|set:')!=-1 ){
                    tmp = content[i].split('|set:');
                    chk = tmp[1].match(/true/) ? ' checked="checked"':'';
                    tmp = tmp[0];
                }else{
                    tmp = content[i];
                    chk='';
                }

                if ( tmp!='' && tmp.indexOf('|')!=-1 )
                    defval = tmp.split('|');
                else {
                    var defval = new Array(2); // dummy array
                    defval[0]=tmp;
                    defval[1]='';
                }
                var lbr='';
                if ( content[i+1]=='' ){
                    lbr    = ' checked="checked"'; //
                    i++;
                }
                groupcount++; totalcount++;

                jQuery('<div class="cf_edit_group_new" id="edit_group'+groupcount+'">'+
                    '<a href="#" id="rgi_'+groupcount+'" class="cf_edit_minus dashicons dashicons-dismiss"></a>'+
                    '<input type="text" id="cf_edit_group_o'+groupcount+'" name="cf_edit_group_o'+groupcount+'" value="'+defval[0].replace(/"/g,'&quot;')+'"/>'+
                    '<input type="text" id="cf_edit_group_v'+groupcount+'" name="cf_edit_group_v'+groupcount+'" value="'+defval[1].replace(/"/g,'&quot;')+'" class="inpOpt"/>'+
                    '<input class="allchk cf_chked" type="checkbox" id="cf_edit_group_chked'+groupcount+'" name="cf_edit_group_chked'+groupcount+'" '+chk+'/>'+
                    '<input class="allchk cf_br" type="checkbox" id="cf_edit_group_br'+groupcount+'" name="cf_edit_group_br'+groupcount+'" value="lbr" '+lbr+'/>'+
                    '<a href="javascript:void(0);" class="cf_edit_move_up dashicons dashicons-arrow-up-alt"></a>'+
                    '<a href="javascript:void(0);" class="cf_edit_move_down dashicons dashicons-arrow-down-alt"></a>'+
                '</div>').appendTo("#cf_edit_groups");

            }

            if ( groupcount > 5 )
                jQuery('#cf_edit_groups').css( { height:"9em", overflowY:"auto" } );

            jQuery('.cf_edit_group_new > a.cf_edit_minus').bind("click", function(){
                jQuery(this).parent().remove();
                if ( totalcount-- < 5 )
                    jQuery('#cf_edit_groups').css( { height:"" } );
                return false;
            });

            line = '';

        }
        else if ( document.getElementById('cf_edit_label_select') ){

            content = line.split('#');
            totalcount = groupcount = 0;

            jQuery('a#add_group_button').click(function() {
                groupcount++; totalcount++;
                jQuery('<div class="cf_edit_group_new" id="edit_group'+groupcount+'">'+
                    '<a href="#" id="rgi_'+groupcount+'" class="cf_edit_minus dashicons dashicons-dismiss"></a>'+
                    '<input type="text" id="cf_edit_group_o'+groupcount+'" name="cf_edit_group_o'+groupcount+'" value=""/>'+
                    '<input type="text" id="cf_edit_group_v'+groupcount+'" name="cf_edit_group_v'+groupcount+'" value="" class="inpOpt"/>'+
                    '<input class="allchk cf_chked" type="checkbox" id="cf_edit_group_chked'+groupcount+'" name="cf_edit_group_chked'+groupcount+'"/>'+
                    '<a href="javascript:void(0);" class="cf_edit_move_up dashicons dashicons-arrow-up-alt"></a>'+
                    '<a href="javascript:void(0);" class="cf_edit_move_down dashicons dashicons-arrow-down-alt"></a>'+
                '</div>').appendTo("#cf_edit_groups");

                jQuery('a.cf_edit_move_up','#edit_group'+groupcount).bind("click", cfmoveup);
                jQuery('a.cf_edit_move_down','#edit_group'+groupcount).bind("click", cfmovedown);

                jQuery('#rgi_'+groupcount).bind("click", function(){
                    jQuery(this).parent().remove();
                    totalcount--;
                    if ( totalcount <= 5 ) { jQuery('#cf_edit_groups').css( { height:"" } ); }
                    return false; });

                if ( totalcount > 5 )
                    jQuery('#cf_edit_groups').css( { height:"9em", overflowY:"auto" } );

                return false;

             });

            jQuery('#cf_edit_label_select').val( content[0] );

            for( i=1 ; i<content.length ; i++ ) {

                if ( content[i]!='' && content[i].indexOf('|set:')!=-1 ){
                    tmp = content[i].split('|set:');
                    chk = tmp[1].match(/true/) ? ' checked="checked"':'';
                    tmp = tmp[0];
                }else{
                    tmp = content[i];
                    chk='';
                }

                if ( tmp!='' && tmp.indexOf('|')!=-1 )
                    defval = tmp.split('|');
                else {
                    var defval = new Array(2);
                    defval[0]=tmp;
                    defval[1]='';
                }

                lbr='';
                if ( content[i+1]=='' ){
                    lbr    = ' checked="checked"'; //
                    i++;
                }
                else {
                    groupcount++; totalcount++;
                }

                jQuery(
                    '<div class="cf_edit_group_new" id="edit_group'+groupcount+'">'+
                    '<a href="#" id="rgi_'+groupcount+'" class="cf_edit_minus dashicons dashicons-dismiss"></a>'+
                    '<input type="text" id="cf_edit_group_o'+groupcount+'" name="cf_edit_group_o'+groupcount+'"'
                        + ' value="'+defval[0].replace(/"/g,'&quot;')+'"/>'+
                    '<input type="text" id="cf_edit_group_v'+groupcount+'" name="cf_edit_group_v'+groupcount+'"'
                        + ' value="'+defval[1].replace(/"/g,'&quot;')+'" class="inpOpt"/>'+

                    '<input type="checkbox" id="cf_edit_group_chked'+groupcount+'" name="cf_edit_group_chked'+groupcount+'"'
                        + ' ' + chk + ' class="allchk cf_chked"/>'+

                    '<a href="javascript:void(0);" class="cf_edit_move_up dashicons dashicons-arrow-up-alt"></a>'+
                    '<a href="javascript:void(0);" class="cf_edit_move_down dashicons dashicons-arrow-down-alt"></a></div>'
                ).appendTo("#cf_edit_groups");

            }

            if ( groupcount > 5 )
                jQuery('#cf_edit_groups').css( { height:"9em", overflowY:"auto" } );

            jQuery('.cf_edit_group_new > a.cf_edit_minus').bind("click", function(){
                jQuery(this).parent().remove();
                if ( totalcount-- < 5 )
                    jQuery('#cf_edit_groups').css( { height:"" } );
                return false;
            });

            line = '';

        }
        else if ( document.getElementById('cf_edit_label') )
            jQuery('#cf_edit_label').val( line );

        // up click
        jQuery('.cf_edit_group_new > a.cf_edit_move_up').bind('click', cfmoveup);
        jQuery('.cf_edit_group_new > a.cf_edit_move_down').bind('click', cfmovedown);

        jQuery('#cf_target').on('change',':input',function() {
            if ( !trackChg ) {
                trackChg = true;
                jQuery('#wp-admin-bar-cforms-SubmitOptions').addClass('hiLightBar');
            }
        });

        jQuery('.cf_ed_main').addClass('ajaxloaded');

    };

    /* LAUNCHED BEFORE AJAX */
    var open = function(hash) {
        hash.w.css('opacity',1).show();
        hasht = hash.t;
        var type = hash.t.parentNode.nextSibling.value;
        jQuery('#cf_target').load(
            ajaxurl,
            {
                limit    : 25,
                type     : type,
                action   : 'cforms2_field',
                _wpnonce : cforms2_nonces['cforms2_field']
			}, load
        );
    };

    /* LAUNCHED WHEN BOX CLOSED */
    var close = function(hash){
        jQuery('.cf_ed_main').removeClass('ajaxloaded');
        hash.w.hide();
        jQuery('#cf_target').html('');
        hash.o.remove();
    };

    /* ASSSOCIATE DIALOG */
    jQuery('#cf_editbox').jqm({ modal: true, overlay: 30, onShow: open, onHide: close }).draggable();

    /* INSTALL PRESET FUNCTIONS */
    jQuery('a#okInstall').click( function(){document.installpreset.submit();} );

    var oldDesc;
    var loadInstall = function() {
        oldDesc=0;
        jQuery('select#formpresets').keypress( showDesc );
        jQuery('select#formpresets').change( showDesc );
        jQuery('.cf_ed_main').addClass('ajaxloaded');
        jQuery('select#formpresets').focus();
    };

    var showDesc = function() {
        jQuery('span#descInstall' + oldDesc).toggle();
        jQuery('span#descInstall' + this.selectedIndex).toggle();
        oldDesc = this.selectedIndex;
    };

    /* LAUNCHED BEFORE AJAX */
    var openInstall=function(hash) {
        hash.w.css('opacity',1).show();
        hasht = hash.t;
        jQuery('#cf_installtarget').load(
            ajaxurl,
            {
                limit: 25,
                action: 'cforms2_installpreset',
                _wpnonce: cforms2_nonces.installpreset
            },
            function(){ loadInstall(); }
        );
    };

    /* LAUNCHED WHEN BOX CLOSED */
    var closeInstall = function(hash){
        jQuery('span','p#descPreset').hide();
        jQuery('.cf_ed_main').removeClass('ajaxloaded');
        hash.w.hide();
        jQuery('#cf_installtarget').html('');
        hash.o.remove();
    };

    /* ASSSOCIATE DIALOG */
    jQuery('#cf_installbox').jqm({
        trigger : '.jqModalInstall',
        modal   : true,
        overlay : 30,
        onShow  : openInstall,
        onHide  : closeInstall
    }).draggable();
    jQuery('#cf_backupbox'    ).jqm({ trigger: '.jqModalBackup', modal:true, overlay:30 }).draggable();
    jQuery('#cf_delall_dialog').jqm({ trigger: '.jqModalDelAll', modal:true, overlay:30 }).draggable();


    /* DELETE RECORDS DIALOG */
    var open_data=function(hash) { hash.w.css('opacity',1).show(); jQuery('.cf_ed_main').addClass('ajaxloaded'); };
    var close_data=function(hash){ hash.w.hide(); hash.o.remove(); };
    jQuery('#cf_delete_dialog').jqm({
        modal   : true,
        overlay : 30,
        onShow  : open_data,
        onHide  : close_data
    }).draggable();

    jQuery('a#okDelete').click( function() {
        var getString='';
        jQuery('.trSelected','#flex1').each( function (){
            getString = getString + jQuery('td:first > div', this).html() + ',';
        } );
        if ( getString=='' )
            getString = 'all';
        var query = jQuery('.qsbox','.sDiv').attr('value');
        var qtype = jQuery('select','.sDiv').attr('value');
        jQuery.post(
            ajaxurl,
            {
                ids      : getString,
                query    : query,
                qtype    : qtype,
                action   : 'database_deleteentries',
                _wpnonce : cforms2_nonces.deleteentries
            },
            function(ret){
                jQuery('#ctrlmessage').show();
                jQuery('#ctrlmessage').html(ret);
                jQuery('.pReload').trigger('click');
                jQuery('#ctrlmessage').fadeOut(5000);
            }
        );
    } );

    /* DOWNLOAD RECORDS DIALOG */
    jQuery('#cf_dl_dialog').jqm({ modal: true, overlay: 30, onShow: open_data, onHide: close_data }).draggable();
    jQuery('a#okDL').click( function() {
        var getString='';
        jQuery('.trSelected','#flex1').each( function (){
            getString = getString + jQuery('td:first > div',this).html() + ',';
        } );
        if ( getString=='' )
            getString = 'all';

        var sortBy    = jQuery('.sorted','#flex1').attr('abbr');
        var sortOrder = jQuery('.sorted > div:first','#flex1').attr('class');
        var query     = jQuery('.qsbox','.sDiv').attr('value');
        var qtype     = jQuery('select','.sDiv').attr('value');
        var format    = jQuery('#pickDLformat').attr('value');
        var enc       = jQuery("input:radio:checked[name='enc']").val();
        var header    = jQuery('#header').is(':checked');
        var addIP     = jQuery('#addip').is(':checked');
        var addURL    = jQuery('#addurl').is(':checked');

        location.href = ajaxurl
            + '?addurl='   + addURL + '&addip=' + addIP     + '&header=' + header + '&enc='       + enc
            + '&format='   + format + '&ids='   + getString + '&sorted=' + sortBy + '&sortorder=' + sortOrder
            + '&query='    + query  + '&qtype=' + qtype     + '&action=database_dlentries'
            + '&_wpnonce=' + cforms2_nonces.dlentries;
        } );


    /* MAKE FORM FIELDS SORTABLE */
    if (jQuery('.groupWrapper')) {
        jQuery('.groupWrapper').sortable(
            {
                items       : '> .groupItem',
                handle      : 'span.itemHeader',
                tolerance   : 'pointer',
                opacity     : 0.5,
                axis        : 'y',
                stop        : function() {
                    document.getElementById('cformswarning').style.display = '';
                    document.mainform.field_order.value = jQuery('.groupWrapper').sortable('serialize');
                }
            }
        );
    }

    jQuery('#anchorfields').show();

    /* MANAGE COOKIES & BLINDS */
    val=readcookie();
    var toggleui = function (th) {
        var val=readcookie();
        var c = 'cformsshowui=';
        var el = parseInt( jQuery(th).attr("id").substr(1) );
        var x = val.charAt(el) ^ 1;
        jQuery("div", th).attr("class", ((x)?'blindplus':'blindminus'));
        jQuery(th).attr('class', ((x)?'cflegend op-closed':'cflegend'));
        jQuery("#o" + el).toggle();

        var a;
        if ( el>0 ) a = val.slice(0,el); else a='';
        if ( el<val.length ) b = val.slice((el+1),val.length); else b='';
        document.cookie=c+a+x+b+";expires="+timeout.toGMTString()+";";
    };

    for( var i=0 ; i<35 ; i++ ) {
        var el = document.getElementById('o'+i);
        var elp = document.getElementById('p'+i);
        if( el && val.charAt(i) == 0 ) {
            jQuery(el).show();
            if(elp) {
                jQuery("div", elp).attr('class', 'blindminus');
                elp.className = 'cflegend';
            }
        }
        if(elp)jQuery(elp).click( function() {toggleui(this);} );
    }

    if( this.location.href.indexOf('#')>0 )
        this.location.href = this.location.href.substring(this.location.href.indexOf('#'),this.location.href.length);

    jQuery('#wp-admin-bar-cforms-bar').appendTo('#wp-admin-bar-root-default');
    jQuery('#wp-admin-bar-cforms-SubmitOptions').appendTo('#wp-admin-bar-root-default');
    jQuery('#go').hide();
    jQuery('#pickform').change( function() { jQuery('#go').trigger('click'); } );
    jQuery('#cformsdata').on('change', ':input', function() {
        focusedFormControl = this;
        if( !trackChg ) {
            trackChg = true;
            jQuery('#wp-admin-bar-cforms-SubmitOptions').addClass('hiLightBar');
        }
    });

    jQuery('.colorpicker').wpColorPicker();

} );

function getFieldset (t) {
    if ( !t || t == null )
        return '';

    while ( t.parentNode&&!t.parentNode.className.match(/wrap/) ) {
        t = t.parentNode;
        if ( t.tagName.toUpperCase().match(/FIELDSET/) )
            return t.id;
    }
    return '';
}

/* TRACKING RECORDS ROUTINES */
function cf_tracking_view(com,grid){
    var getString='';
    jQuery('.trSelected',grid).each( function (){ getString = getString + jQuery('td:first > div',this).html() + ',';} );
    if ( getString=='' )
        getString = 'all';
    var sortBy    = jQuery('.sorted',grid).attr('abbr');
    var sortOrder = jQuery('.sorted > div:first',grid).attr('class');
    var query     = jQuery('.qsbox','.sDiv').attr('value');
    var qtype     = jQuery('select','.sDiv').attr('value');
    jQuery('#entries').load(
        ajaxurl,
        {
            showids: getString,
            sorted: sortBy,
            sortorder: sortOrder,
            query: query,
            qtype: qtype,
            action: 'database_getentries',
            _wpnonce: cforms2_nonces.getentries
        },
        function () {
            jQuery('.cdatabutton','#entries').bind("click", function () {
                var eid = this.id.substr(7,this.id.length);
                jQuery('#entry'+eid).fadeOut(500, function(){ jQuery(this).remove(); } );
                return false;
            } );
            jQuery('.xdatabutton','#entries').bind("click", function () {
                var eid = this.id.substr(7,this.id.length);
                jQuery('#entry'+eid).fadeOut(500, function(){ jQuery(this).remove(); } );
                jQuery.post(
                    ajaxurl,
                    {
                        id       : eid,
                        action   : 'database_deleteentry',
                        _wpnonce : cforms2_nonces.deleteentry
                    },
                    function () { jQuery('.pReload').trigger('click'); }
                );
                return false;
            } );

            location.href = '#entries';
        }
    );
}
