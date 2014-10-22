/********************************************************************************************/
/********************************************************************************************/
/*                                  init                                                    */
/********************************************************************************************/
/********************************************************************************************/

var now=new Date();
var timeout=new Date(now.getTime()+3*86400000);
var currentInput=null;
var focusedFormControl=null;
var trackChg=false;
var trackingEditTempVal='';
var val=readcookie();
//last=30
if( !val || !(val.length==35) )	{
		document.cookie="cformsshowui=11111111111111111111111111111111111;expires="+timeout.toGMTString()+";";
}

jQuery(document).ready(

	function () {

		jQuery('.wrap').click(function(e) {
			if(e.target.className.match(/allchk/)) {
				jQuery(e.target).focus();
			};
		} );

		/* INFO BUTTONS */
		jQuery('.infotxt').css({display:'none'});
		jQuery('a.infobutton').css({display:'inline'});
		jQuery('a.infobutton').click(function(){ if ( jQuery('#'+this.name).css('display')=='none' ) jQuery('#'+this.name).css({display:''}); else jQuery('#'+this.name).css({display:'none'}); return false; });


		/* GLOBAL VARIABLES */
		var hasht, groupcount, totalcount;

		/* MODIFY THE OK BUTTON CLICK EVENT */
		jQuery('a#ok').click(function() {

			if ( (l_label=jQuery('#cf_edit_label').val()) == null) l_label='';
			if ( (l_label_group=jQuery('#cf_edit_label_group').val()) == null) l_label_group='';
			if ( (l_label_select=jQuery('#cf_edit_label_select').val()) == null) l_label_select='';
			if ( (l_left=jQuery('#cf_edit_label_left').val()) == null) l_left='';

			line = l_left + l_label + l_label_group + l_label_select;

			if ( (l_css=jQuery('#cf_edit_css').val()) == null) l_css=''; else l_css='|'+l_css;
			if ( (l_style=jQuery('#cf_edit_style').val()) == null) l_style=''; else l_style='|'+l_style;

			line += l_css + l_style;

			if ( (l_default=jQuery('#cf_edit_default').val()) == null) l_default=''; else l_default='|'+l_default;
			if ( (l_regexp=jQuery('#cf_edit_regexp').val()) == null) l_regexp=''; else l_regexp='|'+l_regexp;

			line += l_default + l_regexp;

			if ( (l_right=jQuery('#cf_edit_label_right').val()) == null) l_right=''; else l_right='#'+l_right;

			line += l_right;

			l_chkstate=jQuery('#cf_edit_checked').is(':checked');

			if(!l_chkstate)l_chkstate=''; else { if (l_chkstate) l_chkstate='|set:true'; }

			if ( (l_title=jQuery('#cf_edit_title').val()) == null) l_title=''; else { if (l_title!='') l_title='|title:'+l_title; }

			if ( (l_cerr=jQuery('#cf_edit_customerr').val()) == null) l_cerr=''; else { if (l_cerr!='') l_cerr='|err:'+l_cerr; }

			var autocomplete = jQuery('#cf_edit_checked_autocomplete').is(':checked') ? '1' : '0';
			var autofocus = jQuery('#cf_edit_checked_autofocus').is(':checked') ? '1' : '0';
			var min = jQuery('#cf_edit_min').length ? jQuery('#cf_edit_min').val() : '';
			var max = jQuery('#cf_edit_max').length ? jQuery('#cf_edit_max').val() : '';
			var pattern = jQuery('#cf_edit_pattern').length ? jQuery('#cf_edit_pattern').val() : '';
			var step = jQuery('#cf_edit_step').length ? jQuery('#cf_edit_step').val() : '';
			var placeholder = jQuery('#cf_edit_placeholder').length ? jQuery('#cf_edit_placeholder').val() : '';
			var sep = '\u00A4';
			if(jQuery('#html5formfields').length)var l_html5 = '|html5:'+autocomplete+sep+autofocus+sep+min+sep+max+sep+pattern+sep+step+sep+placeholder;
			else var l_html5 = '';

			temp='';
			jQuery('.cf_edit_group_new').each( function (index, domEle) {
				if ( (temp_o=jQuery('#cf_edit_group_o'+domEle.id.substr(10)).val()) == null) temp_o=''; else temp_o='#'+temp_o;
				if ( (temp_v=jQuery('#cf_edit_group_v'+domEle.id.substr(10)).val()) == null) temp_v=''; else { if (temp_v!='') temp_v='|'+temp_v; }
				temp_chk = jQuery('#cf_edit_group_chked'+domEle.id.substr(10)).is(':checked');
				if ( !temp_chk ) temp_chk=''; else { if (temp_chk) temp_chk='|set:true'; }
				temp_br = jQuery('#cf_edit_group_br'+domEle.id.substr(10)).is(':checked');
				if ( !temp_br ) temp_br=''; else { if (temp_br) temp_br='#'; }
				line += temp_o + temp_v + temp_chk + temp_br;

			} );

			hasht.parentNode.previousSibling.value = line + l_chkstate + l_title + l_cerr + l_html5;
			return false;
		});

		/* LAUNCHED AFTER AJAX */
		var load = function()	{

			/* GET CURRENT CONFIG */
			line = hasht.parentNode.previousSibling.value;

			if ( line.indexOf('|html5:') > 0 ) {
				content = line.split('|html5:');
				line = content[0];
				var sep = '\u00A4';
				content = content[1].split(sep);
				if ( content[0]=='1' ) jQuery('#cf_edit_checked_autocomplete').attr('checked', 'checked');
				if ( content[1]=='1' ) jQuery('#cf_edit_checked_autofocus').attr('checked', 'checked');
				if ( content[2]!='' ) jQuery('#cf_edit_min').val(content[2]);
				if ( content[3]!='' ) jQuery('#cf_edit_max').val(content[3]);
				if ( content[4]!='' ) jQuery('#cf_edit_pattern').val(content[4]);
				if ( content[5]!='' ) jQuery('#cf_edit_step').val(content[5]);
				if ( content[6]!='' ) jQuery('#cf_edit_placeholder').val(content[6])
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
				console.log(content[0]);
				console.log(content[1]);

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
						'<a href="#" id="rgi_'+groupcount+'" class="cf_edit_minus"></a>'+
						'<input type="text" id="cf_edit_group_o'+groupcount+'" name="cf_edit_group_o'+groupcount+'" value=""/>'+
						'<input type="text" id="cf_edit_group_v'+groupcount+'" name="cf_edit_group_v'+groupcount+'" value="" class="inpOpt"/>'+
						'<input class="allchk cf_chked" type="checkbox" id="cf_edit_group_chked'+groupcount+'" name="cf_edit_group_chked'+groupcount+'"/>'+
						'<input class="allchk cf_br" type="checkbox" id="cf_edit_group_br'+groupcount+'" name="cf_edit_group_br'+groupcount+'" value="lbr"/>'+
						'<a href="javascript:void(0);" class="cf_edit_move_up"></a>'+
						'<a href="javascript:void(0);" class="cf_edit_move_down"></a>'+
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

				jQuery('#cf_edit_label_group').val( content[0] );

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
						var defval = new Array(2); // dummy array
						defval[0]=tmp;
						defval[1]='';
					}
					lbr='';
					if ( content[i+1]=='' ){
						lbr    = ' checked="checked"'; //
						i++;
					}
					groupcount++; totalcount++;

					jQuery('<div class="cf_edit_group_new" id="edit_group'+groupcount+'">'+
						'<a href="#" id="rgi_'+groupcount+'" class="cf_edit_minus"></a>'+
						'<input type="text" id="cf_edit_group_o'+groupcount+'" name="cf_edit_group_o'+groupcount+'" value="'+defval[0].replace(/"/g,'&quot;')+'"/>'+
						'<input type="text" id="cf_edit_group_v'+groupcount+'" name="cf_edit_group_v'+groupcount+'" value="'+defval[1].replace(/"/g,'&quot;')+'" class="inpOpt"/>'+
						'<input class="allchk cf_chked" type="checkbox" id="cf_edit_group_chked'+groupcount+'" name="cf_edit_group_chked'+groupcount+'" '+chk+'/>'+
						'<input class="allchk cf_br" type="checkbox" id="cf_edit_group_br'+groupcount+'" name="cf_edit_group_br'+groupcount+'" value="lbr" '+lbr+'/>'+
						'<a href="javascript:void(0);" class="cf_edit_move_up"></a>'+
						'<a href="javascript:void(0);" class="cf_edit_move_down"></a>'+
					'</div>').appendTo("#cf_edit_groups");

				}

				if ( groupcount > 5 )
					jQuery('#cf_edit_groups').css( { height:"9em", overflowY:"auto" } );

				jQuery('.cf_edit_group_new > a.cf_edit_minus').bind("click", function(){ jQuery(this).parent().remove(); if ( totalcount-- < 5 ) jQuery('#cf_edit_groups').css( { height:"" } ); return false; });

				line = '';

			}
			else if ( document.getElementById('cf_edit_label_select') ){

				content = line.split('#');
				totalcount = groupcount = 0;

				jQuery('a#add_group_button').click(function() {
					groupcount++; totalcount++;
					jQuery('<div class="cf_edit_group_new" id="edit_group'+groupcount+'">'+
						'<a href="#" id="rgi_'+groupcount+'" class="cf_edit_minus"></a>'+
						'<input type="text" id="cf_edit_group_o'+groupcount+'" name="cf_edit_group_o'+groupcount+'" value=""/>'+
						'<input type="text" id="cf_edit_group_v'+groupcount+'" name="cf_edit_group_v'+groupcount+'" value="" class="inpOpt"/>'+
						'<input class="allchk cf_chked" type="checkbox" id="cf_edit_group_chked'+groupcount+'" name="cf_edit_group_chked'+groupcount+'"/>'+
						'<a href="javascript:void(0);" class="cf_edit_move_up"></a>'+
						'<a href="javascript:void(0);" class="cf_edit_move_down"></a>'+
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

					jQuery('<div class="cf_edit_group_new" id="edit_group'+groupcount+'">'+
						'<a href="#" id="rgi_'+groupcount+'" class="cf_edit_minus"></a>'+
						'<input type="text" id="cf_edit_group_o'+groupcount+'" name="cf_edit_group_o'+groupcount+'" value="'+defval[0].replace(/"/g,'&quot;')+'"/>'+
						'<input type="text" id="cf_edit_group_v'+groupcount+'" name="cf_edit_group_v'+groupcount+'" value="'+defval[1].replace(/"/g,'&quot;')+'" class="inpOpt"/>'+

						'<input class="allchk cf_chked" type="checkbox" id="cf_edit_group_chked'+groupcount+'" name="cf_edit_group_chked'+groupcount+'" '+chk+'/>'+

						'<a href="javascript:void(0);" class="cf_edit_move_up"></a>'+
						'<a href="javascript:void(0);" class="cf_edit_move_down"></a>'+
					'</div>').appendTo("#cf_edit_groups");

				}

				if ( groupcount > 5 )
					jQuery('#cf_edit_groups').css( { height:"9em", overflowY:"auto" } );

				jQuery('.cf_edit_group_new > a.cf_edit_minus').bind("click", function(){ jQuery(this).parent().remove(); if ( totalcount-- < 5 ) jQuery('#cf_edit_groups').css( { height:"" } ); return false; });

				line = '';

			}
			else if ( document.getElementById('cf_edit_label') )
				jQuery('#cf_edit_label').val( line );

		// up click
		jQuery('.cf_edit_group_new > a.cf_edit_move_up').bind("click", cfmoveup);
		jQuery('.cf_edit_group_new > a.cf_edit_move_down').bind("click", cfmovedown);

		jQuery('#cf_target').on('change',':input',function() {
			if ( !trackChg ) {
				trackChg = true;
				jQuery('#wp-admin-bar-cforms-SubmitOptions').addClass('hiLightBar')
			}
		});

		jQuery('.cf_ed_main').addClass('ajaxloaded');

		};


		/* ASSOCIATE PROPER POPUP BOX / PHP FILE */
		var files = {
			'fieldsetstart'       :'fieldsetstart',
			'textonly'            :'textonly',
			'textfield'           :'textfield',
			'textarea'            :'textfield',
			'checkbox'            :'checkbox',
			'checkboxgroup'       :'checkboxgroup',
			'radiobuttons'        :'checkboxgroup',
			'selectbox'           :'selectbox',
			'multiselectbox'      :'selectbox',
			'upload'              :'textfield',
			'datepicker'          :'textfield',
			'pwfield'             :'textfield',
			'hidden'              :'textfield',
			'fieldsetend'         :'fieldsetstart',
			'ccbox'               :'checkbox',
			'emailtobox'          :'selectbox',
			'verification'        :'textfield',
			'captcha'             :'textfield',
			'yourname'            :'textfield',
			'youremail'           :'textfield',
			'friendsname'         :'textfield',
			'friendsemail'        :'textfield',
			'cauthor'             :'textfield',
			'email'               :'textfield',
			'url'                 :'textfield',
			'comment'             :'textfield',
			'send2author'         :'checkboxgroup',
			'subscribe'           :'checkbox',
			'luv'                 :'checkbox',

			'html5color'          :'html5field',
			'html5date'           :'html5field',
			'html5datetime'       :'html5field',
			'html5datetime-local' :'html5field',
			'html5email'          :'html5field',
			'html5month'          :'html5field',
			'html5number'         :'html5field',
			'html5range'          :'html5field',
			'html5search'         :'html5field',
			'html5tel'            :'html5field',
			'html5time'           :'html5field',
			'html5url'            :'html5field',
			'html5week'           :'html5field',
			'html5tel'            :'html5field'
		};

		/* LAUNCHED BEFORE AJAX */
		var open=function(hash)	{ hash.w.css('opacity',1).show();
								  hasht = hash.t;
								  var type = hash.t.parentNode.nextSibling.value;
								  jQuery('#cf_target').load(ajaxurl, {limit: 25, type: type, action: 'cforms2_field_' + files[type], _wpnonce: cforms2_nonces[files[type]]}, function(){ load(); } );
								};

		/* LAUNCHED WHEN BOX CLOSED */
		var close=function(hash){ jQuery('.cf_ed_main').removeClass('ajaxloaded'); hash.w.hide(); jQuery('#cf_target').html(''); hash.o.remove(); };

		/* ASSSOCIATE DIALOG */
	  	jQuery('#cf_editbox').jqm({ modal: true, overlay: 30, onShow: open, onHide: close }).jqDrag('.jqDrag');

		/* INSTALL PRESET FUNCTIONS */
		jQuery('a#okInstall').click( function() { document.installpreset.submit(); } );

		var oldDesc;
		var loadInstall = function() {
				oldDesc=0;
				jQuery('select#formpresets').keypress( showDesc );
				jQuery('select#formpresets').change( showDesc );
				jQuery('.cf_ed_main').addClass('ajaxloaded');
				jQuery('select#formpresets').focus();
				};

		var showDesc = function() { jQuery('span#descInstall'+oldDesc).toggle(); jQuery('span#descInstall'+this.selectedIndex).toggle(); oldDesc=this.selectedIndex; };

		/* LAUNCHED BEFORE AJAX */
		var openInstall=function(hash)	{
				hash.w.css('opacity',1).show();
				hasht = hash.t;
				jQuery('#cf_installtarget').load( ajaxurl, {limit: 25, action: 'cforms2_installpreset', _wpnonce: cforms2_nonces.installpreset}, function(){ loadInstall(); } );
				};

		/* LAUNCHED WHEN BOX CLOSED */
		var closeInstall=function(hash){ jQuery('span','p#descPreset').hide(); jQuery('.cf_ed_main').removeClass('ajaxloaded'); hash.w.hide(); jQuery('#cf_installtarget').html(''); hash.o.remove(); };

		/* ASSSOCIATE DIALOG */
	  	jQuery('#cf_installbox').jqm({ trigger: '.jqModalInstall', modal: true, overlay: 30, onShow: openInstall, onHide: closeInstall }).jqDrag('.jqDrag');
		jQuery('#cf_backupbox').jqm({ trigger: '.jqModalBackup', modal: true, overlay:30 }).jqDrag('.jqDrag');
		jQuery('#cf_delall_dialog').jqm({ trigger: '.jqModalDelAll', modal:true, overlay:30 }).jqDrag('.jqDrag');


		/* DELETE RECORDS DIALOG */
		var open_data=function(hash) { hash.w.css('opacity',1).show(); jQuery('.cf_ed_main').addClass('ajaxloaded'); };
		var close_data=function(hash){ hash.w.hide(); hash.o.remove(); };
		jQuery('#cf_delete_dialog').jqm({ modal: true, overlay: 30, onShow: open_data, onHide: close_data }).jqDrag('.jqDrag');
		jQuery('a#okDelete').click( function() {
			var getString='';
			jQuery('.trSelected','#flex1').each( function (){ getString = getString + jQuery('td:first > div',this).html() + ','} );
			if ( getString=='' )
			 	getString = 'all';
			var query	  = jQuery('.qsbox','.sDiv').attr('value');
			var qtype	  = jQuery('select','.sDiv').attr('value');
			jQuery.post(ajaxurl, {ids: getString, query: query, qtype: qtype, action: 'database_deleteentries', _wpnonce: cforms2_nonces.deleteentries}, function(ret,stat){ jQuery('#ctrlmessage').show(); jQuery('#ctrlmessage').html(ret); jQuery('.pReload').trigger('click'); jQuery('#ctrlmessage').fadeOut(5000); } );
			} );

		/* DOWNLOAD RECORDS DIALOG */
		jQuery('#cf_dl_dialog').jqm({ modal: true, overlay: 30, onShow: open_data, onHide: close_data }).jqDrag('.jqDrag');
		jQuery('a#okDL').click( function() {
			var getString='';
			jQuery('.trSelected','#flex1').each( function (){ getString = getString + jQuery('td:first > div',this).html() + ','} );
			if ( getString=='' )
			 	getString = 'all';

			var sortBy    = jQuery('.sorted','#flex1').attr('abbr');
			var sortOrder = jQuery('.sorted > div:first','#flex1').attr('class');
			var query	  = jQuery('.qsbox','.sDiv').attr('value');
			var qtype	  = jQuery('select','.sDiv').attr('value');
			var format    = jQuery('#pickDLformat').attr('value');
			var enc       = jQuery("input:radio:checked[name='enc']").val();
			var header    = jQuery('#header').is(':checked');
			var addIP     = jQuery('#addip').is(':checked');
			var addURL    = jQuery('#addurl').is(':checked');
			location.href = ajaxurl+'?addurl='+addURL+'&addip='+addIP+'&header='+header+'&enc='+enc+'&format='+format+'&ids='+getString+'&sorted='+sortBy+'&sortorder='+sortOrder+'&query='+query+'&qtype='+qtype+'&action=database_dlentries&_wpnonce='+cforms2_nonces.dlentries;
			} );


		/* MAKE FORM FIELDS SORTABLE */
		if	(jQuery('.groupWrapper')) {
			jQuery('.groupWrapper').Sortable(
				{
					accept: 		'groupItem',
					helperclass:	'sortHelper',
					activeclass : 	'sortableactive',
					hoverclass : 	'sortablehover',
					handle:			'span.itemHeader',
					tolerance:		'pointer',
					opacity:		0.5,
					axis:			'vertically',
					onStop : function()
					{
						serial = jQuery.SortSerialize('allfields');
						document.getElementById('cformswarning').style.display = '';
						document.mainform.field_order.value = serial.hash;
					}
				}
			);
		}

		/* TEXTAREAS resize */
        jQuery('textarea.resizable:not(.processed)').TextAreaResizer();
		jQuery('#anchorfields').show();
		jQuery('.cf-content','#selectcss').show();
		jQuery('#csspicker').bind('change', function() { jQuery('#selectcss').submit() });


		/* MANAGE COOKIES & BLINDS */
		val=readcookie();
		for( i=0 ; i<35 ; i++ ) {
			el = document.getElementById('o'+i);
			elp = document.getElementById('p'+i);
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
	}
);

function checkIfFormEl (t) {
	return ( t.tagName.toUpperCase().match(/(INPUT|SELECT|TEXTAREA)/) );
}

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

/* global settings captcha reset */
function resetAdminCaptcha (){

	i = jQuery('#cforms_cap_i').val();
	w = jQuery('#cforms_cap_w').val();
	h = jQuery('#cforms_cap_h').val();
	c = jQuery('#inputID2').val();
	l = jQuery('#inputID1').val();
	bg= jQuery('#cforms_cap_b').val();
	f = jQuery('#cforms_cap_f').val();
	fo= jQuery('#cforms_cap_fo').val();
	foqa= jQuery('#cforms_cap_foqa').val();
	f1= jQuery('#cforms_cap_f1').val();
	f2= jQuery('#cforms_cap_f2').val();
	a1= jQuery('#cforms_cap_a1').val();
	a2= jQuery('#cforms_cap_a2').val();
	c1= jQuery('#cforms_cap_c1').val();
	c2= jQuery('#cforms_cap_c2').val();
	ac= jQuery('#cforms_cap_ac').val();

	data = ajaxurl+"?action=cforms2_reset_captcha&_wpnonce="+cforms2_nonces.reset_captcha+"&ts=0&c1="+c1+"&c2="+c2+"&ac="+ac+"&i="+i+"&w="+w+"&h="+h+"&c="+c+"&l="+l+"&f="+f+"&a1="+a1+"&a2="+a2+"&f1="+f1+"&f2="+f2+"&b="+bg+"&rnd="+Math.round(Math.random()*999999);

	if ( jQuery('#cf_captcha_img').length>0 )
	    jQuery('#cf_captcha_img').attr('src',data);
	else
	    jQuery('#adminCaptcha').prepend('<img id="cf_captcha_img" class="captcha" src="'+data+'" alt=""/>');

    jQuery('#pnote').show();
}

// moving dialog box optionsfunction cfmoveup(){
function cfmoveup(){
	prevEl = jQuery(this).parent().prev();
	if ( prevEl.attr('id') != undefined )
		prevEl.insertAfter( jQuery(this).parent() );
	return false;
}

// moving dialog box options
function cfmovedown(){
	nextEl = jQuery(this).parent().next();
	if ( nextEl.attr('id') != undefined )
		nextEl.insertBefore( jQuery(this).parent() );
	return false;
}

/* TRACKING RECORDS ROUTINES */
function cf_tracking_view(com,grid){
	var getString='';
	jQuery('.trSelected',grid).each( function (){ getString = getString + jQuery('td:first > div',this).html() + ','} );
	if ( getString=='' )
	 	getString = 'all';
	var sortBy    = jQuery('.sorted',grid).attr('abbr');
	var sortOrder = jQuery('.sorted > div:first',grid).attr('class');
	var query	  = jQuery('.qsbox','.sDiv').attr('value');
	var qtype	  = jQuery('select','.sDiv').attr('value');
	jQuery('#entries').load(ajaxurl, {showids: getString, sorted: sortBy, sortorder: sortOrder, query: query, qtype: qtype, action: 'database_getentries', _wpnonce: cforms2_nonces.getentries}, function(){ submissions_loaded(); } );
}
function submissions_loaded(){
	jQuery('.cdatabutton','#entries').bind("click", close_submission );
	jQuery('.xdatabutton','#entries').bind("click", delete_submission );
	jQuery(".editable").editInPlace( {
		bg_out : '#dddddd',
		bg_over : '#f7f7f7',
		use_html : true,
		field_type : 'textarea',
		url : ajaxurl,
		params : 'action=database_savedata&_wpnonce=' + cforms2_nonces.savedata,
		saving_image : jQuery('#geturl').attr('title')+'../css/images/load.gif',
		textarea_cols : '30',
		textarea_rows : '4',
		delegate : {
			shouldOpenEditInPlace : function (el, s) {
				trackingEditTempVal = jQuery(el).html();
				return true;
			},
			didOpenEditInPlace : function (el, s) {
				console.log( jQuery(el).find('.inplace_field').length );
				jQuery(el).find('.inplace_field').val(trackingEditTempVal.replace(/<br[^>]*>/ig,"\n"));
				return true;
			}
		}
	} );

	location.href = '#entries';
}
function delete_submission(){
	eid = this.id.substr(7,this.id.length);
	jQuery('#entry'+eid).fadeOut(500, function(){ jQuery(this).remove(); } );
	jQuery.post(ajaxurl, {id: eid, action: 'database_deleteentry', _wpnonce: cforms2_nonces.deleteentry}, function(){ jQuery('.pReload').trigger('click'); } );
	return false;
}
function close_submission(){
	eid = this.id.substr(7,this.id.length);
	jQuery('#entry'+eid).fadeOut(500, function(){ jQuery(this).remove(); } );
	return false;
}

var alwaysOnTop = {

	dsettings: {
		targetid:'',
		orientation:2,
		position:[10,30],
		externalsource:'',
		frequency:1,
		hideafter:0,
		fadeduration:[500,500],
		display:0
	},

	settingscache: {},

	positiontarget: function ($target, settings) {
		var fixedsupport = !document.all || document.all && document.compatMode == "CSS1Compat" && window.XMLHttpRequest;
		var posoptions = { position : fixedsupport ? 'fixed' : 'absolute', visibility : 'visible' };
		if ( settings.fadeduration[0] > 0 )
			posoptions.opacity = 0;

		posoptions[(/^[13]$/.test(settings.orientation))?'left':'right'] = settings.position[0];
		posoptions[(/^[12]$/.test(settings.orientation))?'top':'bottom'] = settings.position[1];
		if(document.all&&!window.XMLHttpRequest)posoptions.width=$target.width();
		$target.css(posoptions);
		if ( !fixedsupport ) {
			this.keepfixed($target,settings);
			var evtstr='scroll.'+settings.targetid+' resize.'+settings.targetid;
			jQuery(window).bind(evtstr,function(){alwaysOnTop.keepfixed($target,settings);});
		}
		this.revealdiv($target, settings, fixedsupport);
	},

	keepfixed: function($target, settings) {
		var $window=jQuery(window);
		var is1or3=/^[13]$/.test(settings.orientation);
		var is1or2=/^[12]$/.test(settings.orientation);
		var x=$window.scrollLeft()+(is1or3?settings.position[0]:$window.width()-$target.outerWidth()-settings.position[0]);
		var y=$window.scrollTop()+(is1or2?settings.position[1]:$window.height()-$target.outerHeight()-settings.position[1]);
		$target.css({left:x+'px',top:y+'px'});
	},

	revealdiv:function($target,settings){$target.show()},

	init:function(options) {
		var settings={};
		settings=jQuery.extend(settings,this.dsettings,options);
		this.settingscache[settings.targetid]=settings;
		settings.display=1;
		jQuery(document).ready(function(jQuery) {
			var $target=jQuery('#'+settings.targetid);
			alwaysOnTop.positiontarget($target,settings);
		});
	}
};

/********************************************************************************************/
/********************************************************************************************/
/*                                  rest                                                    */
/********************************************************************************************/
/********************************************************************************************/

function checkentry(el) {
  if ( document.getElementById(el).checked == 0 )
	document.getElementById(el).checked = 1;
  else
	document.getElementById(el).checked = 0;
};

function checkonoff(formno,chkName) {
  if ( document.forms[formno].checkflag.value == 0 ) {
    document.forms[formno].checkflag.value =1;
    document.forms[formno].allchktop.checked = 1;
    document.forms[formno].allchkbottom.checked = 1;
    SetChecked (formno,1,chkName);
  }
  else {
    document.forms[formno].checkflag.value =0;
    document.forms[formno].allchktop.checked = 0;
    document.forms[formno].allchkbottom.checked = 0;
    SetChecked (formno,0,chkName);
  }
}

function SetChecked(formno,val,chkName) {
  dml=document.forms[formno];
  len = dml.elements.length;
  var i=0;
  for( i=0 ; i<len ; i++) {
    if (dml.elements[i].name==chkName) {
      dml.elements[i].checked=val;
    }
  }
}

function sort_entries(field) {
	if( document.form.order.value==field ) {
		if ( document.form.orderdir.value=='DESC' )
			document.form.orderdir.value='ASC';
		else
			document.form.orderdir.value='DESC';
	}
	document.form.order.value=field;
	document.form.submit();
}


function toggleui(th) {
	var val=readcookie();
	var c = 'cformsshowui=';
	el = parseInt( jQuery(th).attr("id").substr(1) );
	x = val.charAt(el) ^ 1;
	jQuery("div", th).attr("class", ((x)?'blindplus':'blindminus'));
	jQuery(th).attr('class', ((x)?'cflegend op-closed':'cflegend'));
	jQuery("#o" + el).toggle();

	if ( el>0 )	a = val.slice(0,el); else a='';
	if ( el<val.length ) b = val.slice((el+1),val.length); else b='';
	document.cookie=c+a+x+b+";expires="+timeout.toGMTString()+";";
}
function setshow(el) {
	var val=readcookie();
	var c = 'cformsshowui=';
	if ( document.getElementById('p'+el) && document.getElementById('o'+el) && val.charAt(el)==1 ) {
		jQuery("#p" + el).attr("class", "cflegend");
		jQuery("div", "#p" + el).attr("class", "blindminus");
		jQuery("#o" + el).show();
	}
	if ( el>0 )	a = val.slice(0,el); else a='';
	if ( el<val.length ) b = val.slice((el+1),val.length); else b='';
	document.cookie=c+a+0+b+";expires="+timeout.toGMTString()+";";
	return false;
}
function showui(el) {
	var val=readcookie();
	if( val )
		return val.substr(el,1);
	return false;
}
function readcookie() {
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
}

(function( $ ) {

    $(function() {
        $('.colorpicker').wpColorPicker();
    });

})( jQuery );
