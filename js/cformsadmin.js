/********************************************************************************************/
/********************************************************************************************/
/*                                  init                                                    */
/********************************************************************************************/
/********************************************************************************************/

var now=new Date();
var timeout=new Date(now.getTime()+3*86400000);

var focusedFormControl=null;
var trackChg=false;

var trackingEditTempVal='';

var val=readcookie();
//last=31  ( 16 is free : was "delete all", "backup")

if( !val || !(val.length==35) )	{
		document.cookie="cformsshowui=11111111111111111111111111111111111;expires="+timeout.toGMTString()+";";
}

jQuery(document).ready(

	function () {

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
			if ( !l_chkstate ) l_chkstate=''; else { if (l_chkstate) l_chkstate='|set:true'; }
				
			if ( (l_title=jQuery('#cf_edit_title').val()) == null) l_title=''; else { if (l_title!='') l_title='|title:'+l_title; }

			if ( (l_cerr=jQuery('#cf_edit_customerr').val()) == null) l_cerr=''; else { if (l_cerr!='') l_cerr='|err:'+l_cerr; }

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

			hasht.parentNode.previousSibling.value = line + l_chkstate + l_title + l_cerr;
			return false;
		});

		/* LAUNCHED AFTER AJAX */
		var load = function()	{

			/* GET CURRENT CONFIG */
			line = hasht.parentNode.previousSibling.value;

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
			else if ( document.getElementById('cf_edit_regexp') ){
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

		// change handler
		jQuery('#cf_target').on('change', ':input', function () { 
			if( !trackChg ){
				trackChg = true;
				jQuery('#wp-admin-bar-cforms-SubmitOptions').addClass('hiLightBar');
				//console.log("change @ "+this.id); 
			}
		});
		
		jQuery('.cf_ed_main').addClass('ajaxloaded');

		};


		/* ASSOCIATE PROPER POPUP BOX / PHP FILE */
		var files = new Array(	'fieldsetstart',	// fieldsetstart
								'textonly',			// textonly
								'textfield',		// textfield
								'textfield',		// textarea
								'checkbox',			// checkbox
								'checkboxgroup',	// checkboxgroup
								'checkboxgroup',	// radiobuttons
								'selectbox',		// selectbox
								'selectbox',		// multiselectbox
								'textfield',		// upload
								'textfield',		// datepicker
								'textfield',		// pwfield
								'textfield',		// hidden
								'fieldsetstart',	// fieldsetend

								'textfield',		// dummy
								'checkbox',			// ccbox
								'selectbox',		// emailtobox
								'textfield',		// verification
								'textfield',		// captcha

								'textfield',		// dummy
								'textfield',		// yourname
								'textfield',		// youremail
								'textfield',		// friendsname
								'textfield',		// friendsemail

								'textfield',		// dummy
								'textfield',		// cauthor
								'textfield',		// email
								'textfield',		// url
								'textfield',		// comment
								'checkboxgroup',	// send2author
								'checkbox',			// subscribe
								'checkbox');		// comment luv

		/* LAUNCHED BEFORE AJAX */
		var open=function(hash)	{ hash.w.css('opacity',1).show();
								  hasht = hash.t;
								  jQuery('#cf_target').load(hash.t.parentNode.title + files[hash.t.parentNode.nextSibling.selectedIndex] + '.php', {limit: 25}, function(){ load(); } );
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
				jQuery('#cf_installtarget').load( hash.t.name+'installpreset.php', {limit: 25}, function(){ loadInstall(); } );
				};

		/* LAUNCHED WHEN BOX CLOSED */
		var closeInstall=function(hash){ jQuery('span','p#descPreset').hide(); jQuery('.cf_ed_main').removeClass('ajaxloaded'); hash.w.hide(); jQuery('#cf_installtarget').html(''); hash.o.remove(); };

		/* ASSSOCIATE DIALOG */
	  	jQuery('#cf_installbox').jqm({ trigger: '.jqModalInstall', modal: true, overlay: 30, onShow: openInstall, onHide: closeInstall }).jqDrag('.jqDrag');

		/* ASSSOCIATE DIALOG */
	  	jQuery('#cf_backupbox').jqm({ trigger: '.jqModalBackup', modal: true, overlay: 30 }).jqDrag('.jqDrag');
	  	jQuery('#cf_delall_dialog').jqm({ trigger: '.jqModalDelAll', modal: true, overlay: 30 }).jqDrag('.jqDrag');

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
			jQuery.post(jQuery('#geturl').attr('title')+'lib_database_deleteentries.php', {ids: getString, query: query, qtype: qtype}, function(ret,stat){ jQuery('#ctrlmessage').show(); jQuery('#ctrlmessage').html(ret); jQuery('.pReload').trigger('click'); jQuery('#ctrlmessage').fadeOut(5000); } );
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
			location.href = jQuery('#geturl').attr('title')+'lib_database_dlentries.php?addurl='+addURL+'&addip='+addIP+'&header='+header+'&enc='+enc+'&format='+format+'&ids='+getString+'&sorted='+sortBy+'&sortorder='+sortOrder+'&query='+query+'&qtype='+qtype;
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
        jQuery('.resizable:not(.processed)').TextAreaResizer();

		/* Show main field windows */
        jQuery('#anchorfields').show();
        jQuery('.cf-content','#selectcss').show();

		/* CSS Page Style Picker */
		jQuery('#csspicker').bind('change', function(){
			jQuery('#selectcss').submit();
		});

		
		/* MANAGE COOKIES & BLINDS */
		val=readcookie();
		for( i=0 ; i<35 ; i++ ) {
			el  = document.getElementById('o'+i);
	        elp = document.getElementById('p'+i);

            if ( el && val.charAt(i)==0 ){

				jQuery(el).show();

	            if ( elp ){
	                jQuery("div",elp).attr( 'class', 'blindminus' );
	                elp.className = 'cflegend';
	            }
            }

	    	if ( elp )
				jQuery(elp).click ( function () { toggleui( this ); } );

		}
		if( this.location.href.indexOf('#')>0 )
			this.location.href = this.location.href.substring(this.location.href.indexOf('#'),this.location.href.length);


		/* ADMIN BAR cforms MENU & NAVIGATION CTRLS*/
		
		//alwaysOnTop.init({ targetid: 'cf_actions', orientation: 2, position: [0,85],fadeduration: [0, 0] });
        //jQuery('#cf_actions').show();
		
		/* Reposition */
		jQuery('#wp-admin-bar-cforms-bar').appendTo('#wp-admin-bar-root-default');
		jQuery('#wp-admin-bar-cforms-SubmitOptions').appendTo('#wp-admin-bar-root-default');
		
        jQuery('#go').hide();
        jQuery('#pickform').change( function(){ jQuery('#go').trigger('click'); }  );

		/* ADD onfocus event to FORM FIELDS */

		//if(  ! jQuery.browser.safari ){
		jQuery('#cformsdata').on('change', ':input', function () { 
			focusedFormControl = this;
			if( !trackChg ){
				trackChg = true;
				jQuery('#wp-admin-bar-cforms-SubmitOptions').addClass('hiLightBar');
				//console.log("change @ "+this.id); 
			}
		});
			
				 
        //}

	}
);

/* global settings captcha reset */
function checkIfFormEl (t){
    return ( t.tagName.toUpperCase().match(/(INPUT|SELECT|TEXTAREA)/) );
}

/* global settings captcha reset */
function getFieldset (t){
	if ( !t || t==null )
		return '';
	while( t.parentNode && !t.parentNode.className.match(/wrap/) ){
    	t=t.parentNode;
		//console.log("trackback..."+t.tagName); 
        if( t.tagName.toUpperCase().match(/FIELDSET/) )
			return t.id;
    }
    return '';
}

/* global settings captcha reset */
function resetAdminCaptcha (path){

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

	data = path+"/cforms-captcha.php?ts=0&c1="+c1+"&c2="+c2+"&ac="+ac+"&i="+i+"&w="+w+"&h="+h+"&c="+c+"&l="+l+"&f="+f+"&a1="+a1+"&a2="+a2+"&f1="+f1+"&f2="+f2+"&b="+bg+"&rnd="+Math.round(Math.random()*999999);

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
	jQuery('#entries').load(jQuery('#geturl').attr('title')+'lib_database_getentries.php', {showids: getString, sorted: sortBy, sortorder: sortOrder, query: query, qtype: qtype}, function(){ submissions_loaded(); } );
}
function submissions_loaded(){
	jQuery('.cdatabutton','#entries').bind("click", close_submission );
	jQuery('.xdatabutton','#entries').bind("click", delete_submission );

/*
	jQuery(".editable").editable(jQuery('#geturl').attr('title')+'lib_database_savedata.php',
		{	type : 'textarea',
			submit : '<div class="divimg"><img src="'+jQuery('#geturl').attr('title')+'../css/images/ok.png"></div>',
			indicator : '<img src="'+jQuery('#geturl').attr('title')+'../css/images/indicator.gif">',
			placeholder: '<img src="'+jQuery('#geturl').attr('title')+'../css/images/edit.png">',
			width: '85%',
			height: 60,
			loadurl: jQuery('#geturl').attr('title')+'lib_database_loaddata.php',
			loadtype: 'POST',
			cssclass : "jQeditField" } );
*/
	jQuery(".editable").editInPlace({
		bg_out:'#dddddd',
		bg_over:'#f7f7f7',
		//on_blur: 'save',
		use_html: true,
		field_type: 'textarea',		
		url: jQuery('#geturl').attr('title')+'lib_database_savedata.php',
		//show_buttons: false,
		//cancel_button: 'c',
		//save_button: 's',
		saving_image: jQuery('#geturl').attr('title')+'../css/images/load.gif',
		textarea_cols:'30',
		textarea_rows:'4',
		delegate: {
			shouldOpenEditInPlace: function(el, s){ 
				trackingEditTempVal = jQuery(el).html();
				return true; 
			},		
			didOpenEditInPlace: function(el, s){ 
				console.log( jQuery(el).find('.inplace_field').length );
				jQuery(el).find('.inplace_field').val( trackingEditTempVal.replace(/<br[^>]*>/ig,"\n") );
				return true; 
			}
			/*,
			willCloseEditInPlace: function(el, s){ 
				return jQuery(el).find('.inplace_field').text().replace(/\n/,"<br/>")+'***';
				/*
				jQuery(el).html( jQuery(el).html().replace(/\n/,"<br/>") );
				alert();
				if ( 1 )
					return trackingEditTempVal;
				else	
					return true;
					
			}*/
		}
	});

	location.href = '#entries';
}
function delete_submission(){
	eid = this.id.substr(7,this.id.length);
	jQuery('#entry'+eid).fadeOut(500, function(){ jQuery(this).remove(); } );
	jQuery.post(jQuery('#geturl').attr('title')+'lib_database_deleteentry.php', {id: eid}, function(){ jQuery('.pReload').trigger('click'); } );
	return false;
}
function close_submission(){
	eid = this.id.substr(7,this.id.length);
	jQuery('#entry'+eid).fadeOut(500, function(){ jQuery(this).remove(); } );
	return false;
}







//** Stay on Top content script- (c) Dynamic Drive DHTML code library: http://www.dynamicdrive.com.
//** Script available at usage terms at http://www.dynamicdrive.com
//** v1.0 (May 12th, 09')

var alwaysOnTop={

	dsettings: {
		targetid: '',
		orientation: 2,
		position: [10, 30],
		externalsource: '',
		frequency: 1,
		hideafter: 0,
		fadeduration: [500, 500],
		display: 0
  },

	settingscache: {},

	positiontarget:function($target, settings){
		var fixedsupport=!document.all || document.all && document.compatMode=="CSS1Compat" && window.XMLHttpRequest;
		var posoptions={position:fixedsupport? 'fixed':'absolute', visibility:'visible'};
		if (settings.fadeduration[0]>0)
			posoptions.opacity=0;
		posoptions[(/^[13]$/.test(settings.orientation))? 'left' : 'right']=settings.position[0];
		posoptions[(/^[12]$/.test(settings.orientation))? 'top' : 'bottom']=settings.position[1];
		if (document.all && !window.XMLHttpRequest)
			posoptions.width=$target.width();
		$target.css(posoptions);
		if (!fixedsupport){
			this.keepfixed($target, settings);
			var evtstr='scroll.' + settings.targetid + ' resize.'+settings.targetid;
			jQuery(window).bind(evtstr, function(){alwaysOnTop.keepfixed($target, settings)});
		}
		this.revealdiv($target, settings, fixedsupport);
	},

	keepfixed:function($target, settings){
		var $window=jQuery(window);
		var is1or3=/^[13]$/.test(settings.orientation);
		var is1or2=/^[12]$/.test(settings.orientation);
		var x=$window.scrollLeft() + (is1or3? settings.position[0] : $window.width()-$target.outerWidth()-settings.position[0]);
		var y=$window.scrollTop() + (is1or2? settings.position[1] : $window.height()-$target.outerHeight()-settings.position[1]);
		$target.css({left:x+'px', top:y+'px'});
	},

	revealdiv:function($target, settings){
		$target.show();
	},

	init:function(options){
		var settings={};
  		settings = jQuery.extend(settings, this.dsettings, options);
		this.settingscache[settings.targetid] = settings;
		settings.display = 1;

		jQuery(document).ready(function(jQuery){
            var $target=jQuery('#'+settings.targetid);
            alwaysOnTop.positiontarget($target, settings);
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

	el = parseInt(jQuery(th).attr("id").substr(1));

	x = val.charAt(el) ^ 1;

	jQuery("div",th).attr("class", ((x)?'blindplus':'blindminus') );
	jQuery(th).attr('class', ((x)?'cflegend op-closed':'cflegend') );

	jQuery("#o"+el).toggle();

	if ( el>0 )
		a = val.slice(0,el);
	else
		a = '';

	if ( el<val.length )
		b = val.slice((el+1),val.length);
	else
		b = '';
	document.cookie=c+a+x+b+";expires="+timeout.toGMTString()+";";
}
function setshow(el) {
	var val=readcookie();
	var c = 'cformsshowui=';
	if ( document.getElementById('p'+el) && document.getElementById('o'+el) && val.charAt(el)==1 ) {
		jQuery("#p"+el).attr("class", "cflegend");
		jQuery("div","#p"+el).attr("class", "blindminus");
		jQuery("#o"+el).show();
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


/********************************************************************************************/
/********************************************************************************************/
/*                                  POPUP DIALOG                                            */
/********************************************************************************************/
/********************************************************************************************/

(function(jQuery) {
jQuery.fn.jqm=function(o){
var p={
overlay: 50,
overlayClass: 'jqmOverlay',
closeClass: 'jqmClose',
trigger: '.jqModal',
ajax: F,
ajaxText: '',
target: F,
modal: F,
toTop: F,
onShow: F,
onHide: F,
onLoad: F
};
return this.each(function(){if(this._jqm)return H[this._jqm].c=jQuery.extend({},H[this._jqm].c,o);s++;this._jqm=s;
H[s]={c:jQuery.extend(p,jQuery.jqm.params,o),a:F,w:jQuery(this).addClass('jqmID'+s),s:s};
if(p.trigger)jQuery(this).jqmAddTrigger(p.trigger);
});};

jQuery.fn.jqmAddClose=function(e){return hs(this,e,'jqmHide');};
jQuery.fn.jqmAddTrigger=function(e){return hs(this,e,'jqmShow');};
jQuery.fn.jqmShow=function(t){return this.each(function(){jQuery.jqm.open(this._jqm,t);});};
jQuery.fn.jqmHide=function(t){return this.each(function(){jQuery.jqm.close(this._jqm,t)});};

jQuery.jqm = {
hash:{},
open:function(s,t){var h=H[s],c=h.c,cc='.'+c.closeClass,z=(parseInt(h.w.css('z-index'))),z=(z>0)?z:3000,o=jQuery('<div></div>').css({height:'100%',width:'100%',position:'fixed',left:0,top:0,'z-index':z-1,opacity:c.overlay/100});if(h.a)return F;h.t=t;h.a=true;h.w.css('z-index',z);
 if(c.modal) {if(!A[0])L('bind');A.push(s);}
 else if(c.overlay > 0)h.w.jqmAddClose(o);
 else o=F;

 h.o=(o)?o.addClass(c.overlayClass).prependTo('body'):F;
 if(ie6){jQuery('html,body').css({height:'100%',width:'100%'});if(o){o=o.css({position:'absolute'})[0];for(var y in {Top:1,Left:1})o.style.setExpression(y.toLowerCase(),"(_=(document.documentElement.scroll"+y+" || document.body.scroll"+y+"))+'px'");}}

 if(c.ajax) {var r=c.target||h.w,u=c.ajax,r=(typeof r == 'string')?jQuery(r,h.w):jQuery(r),u=(u.substr(0,1) == '@')?jQuery(t).attr(u.substring(1)):u;
  r.html(c.ajaxText).load(u,function(){if(c.onLoad)c.onLoad.call(this,h);if(cc)h.w.jqmAddClose(jQuery(cc,h.w));e(h);});}
 else if(cc)h.w.jqmAddClose(jQuery(cc,h.w));

 if(c.toTop&&h.o)h.w.before('<span id="jqmP'+h.w[0]._jqm+'"></span>').insertAfter(h.o);
 (c.onShow)?c.onShow(h):h.w.show();e(h);return F;
},
close:function(s){var h=H[s];if(!h.a)return F;h.a=F;
 if(A[0]){A.pop();if(!A[0])L('unbind');}
 if(h.c.toTop&&h.o)jQuery('#jqmP'+h.w[0]._jqm).after(h.w).remove();
 if(h.c.onHide)h.c.onHide(h);else{h.w.hide();if(h.o)h.o.remove();} return F;
},
params:{}};
var s=0,H=jQuery.jqm.hash,A=[],ie6=jQuery.browser.msie&&(jQuery.browser.version == "6.0"),F=false,
i=jQuery('<iframe src="javascript:false;document.write(\'\');" class="jqm"></iframe>').css({opacity:0}),
e=function(h){if(ie6)if(h.o)h.o.html('<p style="width:100%;height:100%"/>').prepend(i);else if(!jQuery('iframe.jqm',h.w)[0])h.w.prepend(i); f(h);},
f=function(h){try{ if( jQuery(':input:visible',h.w).length ) jQuery(':input:visible',h.w)[0].focus();}catch(_){}},   // Oliver 26.01.2011
L=function(t){jQuery()[t]("keypress",m)[t]("keydown",m)[t]("mousedown",m);},
m=function(e){var h=H[A[A.length-1]],r=(!jQuery(e.target).parents('.jqmID'+h.s)[0]);if(r)f(h);return !r;},
hs=function(w,t,c){return w.each(function(){var s=this._jqm;jQuery(t).each(function() {
 if(!this[c]){this[c]=[];jQuery(this).click(function(){for(var i in {jqmShow:1,jqmHide:1})for(var s in this[i])if(H[this[i][s]])H[this[i][s]].w[i](this);return F;});}this[c].push(s);});});};
})(jQuery);

(function(jQuery){
jQuery.fn.jqDrag=function(h){return i(this,h,'d');};
jQuery.fn.jqResize=function(h){return i(this,h,'r');};
jQuery.jqDnR={dnr:{},e:0,
drag:function(v){
 if(M.k == 'd')E.css({left:M.X+v.pageX-M.pX,top:M.Y+v.pageY-M.pY});
 else E.css({width:Math.max(v.pageX-M.pX+M.W,0),height:Math.max(v.pageY-M.pY+M.H,0)});
  return false;},
stop:function(){E.css('opacity',M.o);jQuery().unbind('mousemove',J.drag).unbind('mouseup',J.stop);}
};
var J=jQuery.jqDnR,M=J.dnr,E=J.e,
i=function(e,h,k){return e.each(function(){h=(h)?jQuery(h,e):e;
 h.bind('mousedown',{e:e,k:k},function(v){var d=v.data,p={};E=d.e;
 // attempt utilization of dimensions plugin to fix IE issues
 if(E.css('position') != 'relative'){try{E.position(p);}catch(e){}}
 M={X:p.left||f('left')||0,Y:p.top||f('top')||0,W:f('width')||E[0].scrollWidth||0,H:f('height')||E[0].scrollHeight||0,pX:v.pageX,pY:v.pageY,k:d.k,o:E.css('opacity')};
 E.css({opacity:0.8});jQuery().mousemove(jQuery.jqDnR.drag).mouseup(jQuery.jqDnR.stop);
 return false;
 });
});},
f=function(k){return parseInt(E.css(k))||false;};
})(jQuery);

































/********************************************************************************************/
/********************************************************************************************/
/*                                  Color Picker                                            */
/********************************************************************************************/
/********************************************************************************************/

function $cf(v) { return(document.getElementById(v)); }
function $cfS(v) { return(document.getElementById(v).style); }
function absPos(e) { var r={x:e.offsetLeft,y:e.offsetTop}; if(e.offsetParent) { var v=absPos(e.offsetParent); r.x+=v.x; r.y+=v.y; } return(r); }
function agent(v) { return(Math.max(navigator.userAgent.toLowerCase().indexOf(v),0)); }
function isset(v) { return((typeof(v)=='undefined' || v.length==0)?false:true); }
function toggle(i,t,xy) { var v=$cfS(i); v.display=t?t:(v.display=='none'?'block':'none'); if(xy) { v.left=xy[0]; v.top=xy[1]; } }
function XY(e,v) {

	if (agent('msie') && document.documentElement && document.documentElement.scrollTop){
		theTop = document.documentElement.scrollTop;
		theLeft = document.documentElement.scrollLeft;
	}else if (agent('msie') && document.body){
		theTop = document.body.scrollTop;
		theLeft = document.body.scrollLeft;
	}

	if ( agent('msie') ){
		var z=Array(event.clientX+theLeft-8,event.clientY+theTop-15);
	} else if ( agent('pera') ){
 		var z=Array(e.pageX+1,e.pageY-4);
 	}
	else //FF
 		var z=Array(e.pageX-13,e.pageY-19);

	return (v==3?z:z[zero(v)]);

}
//function XYwin(v) { var z=agent('msie')?[document.body.clientHeight,document.body.clientWidth]:[window.innerHeight,window.innerWidth]; return(!isNaN(v)?z[v]:z); }
function zero(v) { v=parseInt(v); return(!isNaN(v)?v:0); }
function zindex(d) { d.style.zIndex=zINDEX++; }

var stop=1;

function cords(W) {

	var W2=W/2, rad=(hsv[0]/360)*(Math.PI*2), hyp=(hsv[1]+(100-hsv[2]))/100*(W2/2);

	$cfS('mCur').left=Math.round(Math.abs(Math.round(Math.sin(rad)*hyp)+W2+3))+'px';
	$cfS('mCur').top=Math.round(Math.abs(Math.round(Math.cos(rad)*hyp)-W2-21))+'px';

}

function coreXY(o,e,xy,z,fu) {

	function point(a,b,e) { eZ=XY(e,3); commit([eZ[0]+a,eZ[1]+b]); }
	function M(v,a,z) { return(Math.max(!isNaN(z)?z:0,!isNaN(a)?Math.min(a,v):v)); }

	function commit(v) { if(fu) fu(v);

		if(o=='mCur') { var W=parseInt($cfS('mSpec').width), W2=W/2, W3=W2/2;

			var x=v[0]-W2-3, y=W-v[1]-W2+15, SV=Math.sqrt(Math.pow(x,2)+Math.pow(y,2)), hue=Math.atan2(x,y)/(Math.PI*2);

			hsv=[hue>0?(hue*360):((hue*360)+360), SV<W3?(SV/W3)*100:100, SV>=W3?Math.max(0,1-((SV-W3)/(W2-W3)))*100:100];

			//$cf('mHEX').innerHTML=hsv2hex(hsv);
			$cfS('plugID'+currentEL).backgroundColor='#'+hsv2hex(hsv);
			cords(W);
			$cf('inputID'+currentEL).value=hsv2hex(hsv);

		}
		else if(o=='mSize') { var b=Math.max(Math.max(v[0],v[1])+oH,75); cords(b);

			$cfS('mini').height=(b+28)+'px';
			$cfS('mini').width=(b+20)+'px';
			$cfS('mSpec').height=b+'px';
			$cfS('mSpec').width=b+'px';

		}
		else {

			if(xy) v=[M(v[0],xy[0],xy[2]), M(v[1],xy[1],xy[3])]; // XY LIMIT

			if(!xy || xy[0]) d.left=v[0]+'px';
			if(!xy || xy[1]) d.top=v[1]+'px';

		}
	}

	if(stop) { stop=''; var d=$cfS(o), eZ=XY(e,3); if(!z) zindex($cf(o));

		if(o=='mCur') { var ab=absPos($cf(o).parentNode); point(-(ab['x']-5),-(ab['y']-28),e); }

		if(o=='mSize') { var oH=parseInt($cfS('mSpec').height), oX=-XY(e), oY=-XY(e,1); } else { var oX=parseInt(d.left)-eZ[0], oY=parseInt(d.top)-eZ[1]; }

		document.onmousemove=function(e){ if(!stop) point(oX,oY-7,e); };
		document.onmouseup=function(){ stop=1; document.onmousemove=''; document.onmouseup=''; };

	}
}

/* CONVERSIONS */

function toHex(v) { v=Math.round(Math.min(Math.max(0,v),255)); return("0123456789ABCDEF".charAt((v-v%16)/16)+"0123456789ABCDEF".charAt(v%16)); }
function rgb2hex(r) { return(toHex(r[0])+toHex(r[1])+toHex(r[2])); }
function hsv2hex(h) { return(rgb2hex(hsv2rgb(h))); }

function hsv2rgb(r) { // easyrgb.com/math.php?MATH=M21#text21

    var R,B,G,S=r[1]/100,V=r[2]/100,H=r[0]/360;

    if(S>0) { if(H>=1) H=0;

        H=6*H; F=H-Math.floor(H);
        A=Math.round(255*V*(1.0-S));
        B=Math.round(255*V*(1.0-(S*F)));
        C=Math.round(255*V*(1.0-(S*(1.0-F))));
        V=Math.round(255*V);

        switch(Math.floor(H)) {

            case 0: R=V; G=C; B=A; break;
            case 1: R=B; G=V; B=A; break;
            case 2: R=A; G=V; B=C; break;
            case 3: R=A; G=B; B=V; break;
            case 4: R=C; G=A; B=V; break;
            case 5: R=V; G=A; B=B; break;

        }

        return([R?R:0,G?G:0,B?B:0]);

    }
    else return([(V=Math.round(V*255)),V,V]);

}

/* GLOBALS */

var zINDEX=1000, hsv=[0,0,100], currentEL=1;








































/*
 * Flexigrid for jQuery -  v1.1
 *
 * Copyright (c) 2008 Paulo P. Marinas (code.google.com/p/flexigrid/)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 */
(function ($) {
	$.addFlex = function (t, p) {
		if (t.grid) return false; //return if already exist
		p = $.extend({ //apply default properties
			height: 200, //default height
			width: 'auto', //auto width
			striped: true, //apply odd even stripes
			novstripe: false,
			minwidth: 30, //min width of columns
			minheight: 80, //min height of columns
			resizable: true, //allow table resizing
			url: false, //URL if using data from AJAX
			method: 'POST', //data sending method
			dataType: 'xml', //type of data for AJAX, either xml or json
			errormsg: 'Connection Error',
			usepager: false,
			nowrap: true,
			page: 1, //current page
			total: 1, //total pages
			useRp: true, //use the results per page select box
			rp: 15, //results per page
			rpOptions: [10, 15, 20, 30, 50], //allowed per-page values 
			title: false,
			pagestat: 'Displaying {from} to {to} of {total} items',
			pagetext: 'Page',
			outof: 'of',
			findtext: 'Find',
			procmsg: 'Processing, please wait ...',
			query: '',
			qtype: '',
			nomsg: 'No items',
			minColToggle: 1, //minimum allowed column to be hidden
			showToggleBtn: true, //show or hide column toggle popup
			hideOnSubmit: true,
			autoload: true,
			blockOpacity: 0.5,
			preProcess: false,
			onDragCol: false,
			onToggleCol: false,
			onChangeSort: false,
			onSuccess: false,
			onError: false,
			onSubmit: false //using a custom populate function
		}, p);
		$(t).show() //show if hidden
			.attr({
				cellPadding: 0,
				cellSpacing: 0,
				border: 0
			}) //remove padding and spacing
			.removeAttr('width'); //remove width properties
		//create grid class
		var g = {
			hset: {},
			rePosDrag: function () {
				var cdleft = 0 - this.hDiv.scrollLeft;
				if (this.hDiv.scrollLeft > 0) cdleft -= Math.floor(p.cgwidth / 2);
				$(g.cDrag).css({
					top: g.hDiv.offsetTop + 1
				});
				var cdpad = this.cdpad;
				$('div', g.cDrag).hide();
				$('thead tr:first th:visible', this.hDiv).each(function () {
					var n = $('thead tr:first th:visible', g.hDiv).index(this);
					var cdpos = parseInt($('div', this).width());
					if (cdleft == 0) cdleft -= Math.floor(p.cgwidth / 2);
					cdpos = cdpos + cdleft + cdpad;
					if (isNaN(cdpos)) {
						cdpos = 0;
					}
					$('div:eq(' + n + ')', g.cDrag).css({
						'left': cdpos + 'px'
					}).show();
					cdleft = cdpos;
				});
			},
			fixHeight: function (newH) {
				newH = false;
				if (!newH) newH = $(g.bDiv).height();
				var hdHeight = $(this.hDiv).height();
				$('div', this.cDrag).each(
					function () {
						$(this).height(newH + hdHeight);
					}
				);
				var nd = parseInt($(g.nDiv).height());
				if (nd > newH) $(g.nDiv).height(newH).width(200);
				else $(g.nDiv).height('auto').width('auto');
				$(g.block).css({
					height: newH,
					marginBottom: (newH * -1)
				});
				var hrH = g.bDiv.offsetTop + newH;
				if (p.height != 'auto' && p.resizable) hrH = g.vDiv.offsetTop;
				$(g.rDiv).css({
					height: hrH
				});
			},
			dragStart: function (dragtype, e, obj) { //default drag function start
				if (dragtype == 'colresize') {//column resize
					$(g.nDiv).hide();
					$(g.nBtn).hide();
					var n = $('div', this.cDrag).index(obj);
					var ow = $('th:visible div:eq(' + n + ')', this.hDiv).width();
					$(obj).addClass('dragging').siblings().hide();
					$(obj).prev().addClass('dragging').show();
					this.colresize = {
						startX: e.pageX,
						ol: parseInt(obj.style.left),
						ow: ow,
						n: n
					};
					$('body').css('cursor', 'col-resize');
				} else if (dragtype == 'vresize') {//table resize
					var hgo = false;
					$('body').css('cursor', 'row-resize');
					if (obj) {
						hgo = true;
						$('body').css('cursor', 'col-resize');
					}
					this.vresize = {
						h: p.height,
						sy: e.pageY,
						w: p.width,
						sx: e.pageX,
						hgo: hgo
					};
				} else if (dragtype == 'colMove') {//column header drag
					$(g.nDiv).hide();
					$(g.nBtn).hide();
					this.hset = $(this.hDiv).offset();
					this.hset.right = this.hset.left + $('table', this.hDiv).width();
					this.hset.bottom = this.hset.top + $('table', this.hDiv).height();
					this.dcol = obj;
					this.dcoln = $('th', this.hDiv).index(obj);
					this.colCopy = document.createElement("div");
					this.colCopy.className = "colCopy";
					this.colCopy.innerHTML = obj.innerHTML;
					if ($.browser.msie) {
						this.colCopy.className = "colCopy ie";
					}
					$(this.colCopy).css({
						position: 'absolute',
						float: 'left',
						display: 'none',
						textAlign: obj.align
					});
					$('body').append(this.colCopy);
					$(this.cDrag).hide();
				}
				$('body').noSelect();
			},
			dragMove: function (e) {
				if (this.colresize) {//column resize
					var n = this.colresize.n;
					var diff = e.pageX - this.colresize.startX;
					var nleft = this.colresize.ol + diff;
					var nw = this.colresize.ow + diff;
					if (nw > p.minwidth) {
						$('div:eq(' + n + ')', this.cDrag).css('left', nleft);
						this.colresize.nw = nw;
					}
				} else if (this.vresize) {//table resize
					var v = this.vresize;
					var y = e.pageY;
					var diff = y - v.sy;
					if (!p.defwidth) p.defwidth = p.width;
					if (p.width != 'auto' && !p.nohresize && v.hgo) {
						var x = e.pageX;
						var xdiff = x - v.sx;
						var newW = v.w + xdiff;
						if (newW > p.defwidth) {
							this.gDiv.style.width = newW + 'px';
							p.width = newW;
						}
					}
					var newH = v.h + diff;
					if ((newH > p.minheight || p.height < p.minheight) && !v.hgo) {
						this.bDiv.style.height = newH + 'px';
						p.height = newH;
						this.fixHeight(newH);
					}
					v = null;
				} else if (this.colCopy) {
					$(this.dcol).addClass('thMove').removeClass('thOver');
					if (e.pageX > this.hset.right || e.pageX < this.hset.left || e.pageY > this.hset.bottom || e.pageY < this.hset.top) {
						//this.dragEnd();
						$('body').css('cursor', 'move');
					} else {
						$('body').css('cursor', 'pointer');
					}
					$(this.colCopy).css({
						top: e.pageY + 10,
						left: e.pageX + 20,
						display: 'block'
					});
				}
			},
			dragEnd: function () {
				if (this.colresize) {
					var n = this.colresize.n;
					var nw = this.colresize.nw;
					$('th:visible div:eq(' + n + ')', this.hDiv).css('width', nw);
					$('tr', this.bDiv).each(
						function () {
							$('td:visible div:eq(' + n + ')', this).css('width', nw);
						}
					);
					this.hDiv.scrollLeft = this.bDiv.scrollLeft;
					$('div:eq(' + n + ')', this.cDrag).siblings().show();
					$('.dragging', this.cDrag).removeClass('dragging');
					this.rePosDrag();
					this.fixHeight();
					this.colresize = false;
				} else if (this.vresize) {
					this.vresize = false;
				} else if (this.colCopy) {
					$(this.colCopy).remove();
					if (this.dcolt != null) {
						if (this.dcoln > this.dcolt) $('th:eq(' + this.dcolt + ')', this.hDiv).before(this.dcol);
						else $('th:eq(' + this.dcolt + ')', this.hDiv).after(this.dcol);
						this.switchCol(this.dcoln, this.dcolt);
						$(this.cdropleft).remove();
						$(this.cdropright).remove();
						this.rePosDrag();
						if (p.onDragCol) {
							p.onDragCol(this.dcoln, this.dcolt);
						}
					}
					this.dcol = null;
					this.hset = null;
					this.dcoln = null;
					this.dcolt = null;
					this.colCopy = null;
					$('.thMove', this.hDiv).removeClass('thMove');
					$(this.cDrag).show();
				}
				$('body').css('cursor', 'default');
				$('body').noSelect(false);
			},
			toggleCol: function (cid, visible) {
				var ncol = $("th[axis='col" + cid + "']", this.hDiv)[0];
				var n = $('thead th', g.hDiv).index(ncol);
				var cb = $('input[value=' + cid + ']', g.nDiv)[0];
				if (visible == null) {
					visible = ncol.hidden;
				}
				if ($('input:checked', g.nDiv).length < p.minColToggle && !visible) {
					return false;
				}
				if (visible) {
					ncol.hidden = false;
					$(ncol).show();
					cb.checked = true;
				} else {
					ncol.hidden = true;
					$(ncol).hide();
					cb.checked = false;
				}
				$('tbody tr', t).each(
					function () {
						if (visible) {
							$('td:eq(' + n + ')', this).show();
						} else {
							$('td:eq(' + n + ')', this).hide();
						}
					}
				);
				this.rePosDrag();
				if (p.onToggleCol) {
					p.onToggleCol(cid, visible);
				}
				return visible;
			},
			switchCol: function (cdrag, cdrop) { //switch columns
				$('tbody tr', t).each(
					function () {
						if (cdrag > cdrop) $('td:eq(' + cdrop + ')', this).before($('td:eq(' + cdrag + ')', this));
						else $('td:eq(' + cdrop + ')', this).after($('td:eq(' + cdrag + ')', this));
					}
				);
				//switch order in nDiv
				if (cdrag > cdrop) {
					$('tr:eq(' + cdrop + ')', this.nDiv).before($('tr:eq(' + cdrag + ')', this.nDiv));
				} else {
					$('tr:eq(' + cdrop + ')', this.nDiv).after($('tr:eq(' + cdrag + ')', this.nDiv));
				}
				if ($.browser.msie && $.browser.version < 7.0) {
					$('tr:eq(' + cdrop + ') input', this.nDiv)[0].checked = true;
				}
				this.hDiv.scrollLeft = this.bDiv.scrollLeft;
			},
			scroll: function () {
				this.hDiv.scrollLeft = this.bDiv.scrollLeft;
				this.rePosDrag();
			},
			addData: function (data) { //parse data
				if (p.dataType == 'json') {
					data = $.extend({rows: [], page: 0, total: 0}, data);
				}
				if (p.preProcess) {
					data = p.preProcess(data);
				}
				$('.pReload', this.pDiv).removeClass('loading');
				this.loading = false;
				if (!data) {
					$('.pPageStat', this.pDiv).html(p.errormsg);
					return false;
				}
				if (p.dataType == 'xml') {
					p.total = +$('rows total', data).text();
				} else {
					p.total = data.total;
				}
				if (p.total == 0) {
					$('tr, a, td, div', t).unbind();
					$(t).empty();
					p.pages = 1;
					p.page = 1;
					this.buildpager();
					$('.pPageStat', this.pDiv).html(p.nomsg);
					return false;
				}
				p.pages = Math.ceil(p.total / p.rp);
				if (p.dataType == 'xml') {
					p.page = +$('rows page', data).text();
				} else {
					p.page = data.page;
				}
				this.buildpager();
				//build new body
				var tbody = document.createElement('tbody');
				if (p.dataType == 'json') {
					$.each(data.rows, function (i, row) {
						var tr = document.createElement('tr');
						if (i % 2 && p.striped) {
							tr.className = 'erow';
						}
						if (row.id) {
							tr.id = 'row' + row.id;
						}
						$('thead tr:first th', g.hDiv).each( //add cell
							function () {
								var td = document.createElement('td');
								var idx = $(this).attr('axis').substr(3);
								td.align = this.align;
								// If the json elements aren't named (which is typical), use numeric order
								if (typeof row.cell[idx] != "undefined") {
									td.innerHTML = (row.cell[idx] != null) ? row.cell[idx] : '';//null-check for Opera-browser
								} else {
									td.innerHTML = row.cell[p.colModel[idx].name];
								}
								$(td).attr('abbr', $(this).attr('abbr'));
								$(tr).append(td);
								td = null;
							}
						);
						if ($('thead', this.gDiv).length < 1) {//handle if grid has no headers
							for (idx = 0; idx < cell.length; idx++) {
								var td = document.createElement('td');
								// If the json elements aren't named (which is typical), use numeric order
								if (typeof row.cell[idx] != "undefined") {
									td.innerHTML = (row.cell[idx] != null) ? row.cell[idx] : '';//null-check for Opera-browser
								} else {
									td.innerHTML = row.cell[p.colModel[idx].name];
								}
								$(tr).append(td);
								td = null;
							}
						}
						$(tbody).append(tr);
						tr = null;
					});
				} else if (p.dataType == 'xml') {
					var i = 1;
					$("rows row", data).each(function () {
						i++;
						var tr = document.createElement('tr');
						if (i % 2 && p.striped) {
							tr.className = 'erow';
						}
						var nid = $(this).attr('id');
						if (nid) {
							tr.id = 'row' + nid;
						}
						nid = null;
						var robj = this;
						$('thead tr:first th', g.hDiv).each(function () {
							var td = document.createElement('td');
							var idx = $(this).attr('axis').substr(3);
							td.align = this.align;
							td.innerHTML = $("cell:eq(" + idx + ")", robj).text();
							$(td).attr('abbr', $(this).attr('abbr'));
							$(tr).append(td);
							td = null;
						});
						if ($('thead', this.gDiv).length < 1) {//handle if grid has no headers
							$('cell', this).each(function () {
								var td = document.createElement('td');
								td.innerHTML = $(this).text();
								$(tr).append(td);
								td = null;
							});
						}
						$(tbody).append(tr);
						tr = null;
						robj = null;
					});
				}
				$('tr', t).unbind();
				$(t).empty();
				$(t).append(tbody);
				this.addCellProp();
				this.addRowProp();
				this.rePosDrag();
				tbody = null;
				data = null;
				i = null;
				if (p.onSuccess) {
					p.onSuccess(this);
				}
				if (p.hideOnSubmit) {
					$(g.block).remove();
				}
				this.hDiv.scrollLeft = this.bDiv.scrollLeft;
				if ($.browser.opera) {
					$(t).css('visibility', 'visible');
				}
			},
			changeSort: function (th) { //change sortorder
				if (this.loading) {
					return true;
				}
				$(g.nDiv).hide();
				$(g.nBtn).hide();
				if (p.sortname == $(th).attr('abbr')) {
					if (p.sortorder == 'asc') {
						p.sortorder = 'desc';
					} else {
						p.sortorder = 'asc';
					}
				}
				$(th).addClass('sorted').siblings().removeClass('sorted');
				$('.sdesc', this.hDiv).removeClass('sdesc');
				$('.sasc', this.hDiv).removeClass('sasc');
				$('div', th).addClass('s' + p.sortorder);
				p.sortname = $(th).attr('abbr');
				if (p.onChangeSort) {
					p.onChangeSort(p.sortname, p.sortorder);
				} else {
					this.populate();
				}
			},
			buildpager: function () { //rebuild pager based on new properties
				$('.pcontrol input', this.pDiv).val(p.page);
				$('.pcontrol span', this.pDiv).html(p.pages);
				var r1 = (p.page - 1) * p.rp + 1;
				var r2 = r1 + p.rp - 1;
				if (p.total < r2) {
					r2 = p.total;
				}
				var stat = p.pagestat;
				stat = stat.replace(/{from}/, r1);
				stat = stat.replace(/{to}/, r2);
				stat = stat.replace(/{total}/, p.total);
				$('.pPageStat', this.pDiv).html(stat);
			},
			populate: function () { //get latest data
				if (this.loading) {
					return true;
				}
				if (p.onSubmit) {
					var gh = p.onSubmit();
					if (!gh) {
						return false;
					}
				}
				this.loading = true;
				if (!p.url) {
					return false;
				}
				$('.pPageStat', this.pDiv).html(p.procmsg);
				$('.pReload', this.pDiv).addClass('loading');
				$(g.block).css({
					top: g.bDiv.offsetTop
				});
				if (p.hideOnSubmit) {
					$(this.gDiv).prepend(g.block);
				}
				if ($.browser.opera) {
					$(t).css('visibility', 'hidden');
				}
				if (!p.newp) {
					p.newp = 1;
				}
				if (p.page > p.pages) {
					p.page = p.pages;
				}
				var param = [{
					name: 'page',
					value: p.newp
				}, {
					name: 'rp',
					value: p.rp
				}, {
					name: 'sortname',
					value: p.sortname
				}, {
					name: 'sortorder',
					value: p.sortorder
				}, {
					name: 'query',
					value: p.query
				}, {
					name: 'qtype',
					value: p.qtype
				}];
				if (p.params) {
					for (var pi = 0; pi < p.params.length; pi++) {
						param[param.length] = p.params[pi];
					}
				}
				$.ajax({
					type: p.method,
					url: p.url,
					data: param,
					dataType: p.dataType,
					success: function (data) {
						g.addData(data);
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						try {
							if (p.onError) p.onError(XMLHttpRequest, textStatus, errorThrown);
						} catch (e) {}
					}
				});
			},
			doSearch: function () {
				p.query = $('input[name=q]', g.sDiv).val();
				p.qtype = $('select[name=qtype]', g.sDiv).val();
				p.newp = 1;
				this.populate();
			},
			changePage: function (ctype) { //change page
				if (this.loading) {
					return true;
				}
				switch (ctype) {
					case 'first':
						p.newp = 1;
						break;
					case 'prev':
						if (p.page > 1) {
							p.newp = parseInt(p.page) - 1;
						}
						break;
					case 'next':
						if (p.page < p.pages) {
							p.newp = parseInt(p.page) + 1;
						}
						break;
					case 'last':
						p.newp = p.pages;
						break;
					case 'input':
						var nv = parseInt($('.pcontrol input', this.pDiv).val());
						if (isNaN(nv)) {
							nv = 1;
						}
						if (nv < 1) {
							nv = 1;
						} else if (nv > p.pages) {
							nv = p.pages;
						}
						$('.pcontrol input', this.pDiv).val(nv);
						p.newp = nv;
						break;
				}
				if (p.newp == p.page) {
					return false;
				}
				if (p.onChangePage) {
					p.onChangePage(p.newp);
				} else {
					this.populate();
				}
			},
			addCellProp: function () {
				$('tbody tr td', g.bDiv).each(function () {
					var tdDiv = document.createElement('div');
					var n = $('td', $(this).parent()).index(this);
					var pth = $('th:eq(' + n + ')', g.hDiv).get(0);
					if (pth != null) {
						if (p.sortname == $(pth).attr('abbr') && p.sortname) {
							this.className = 'sorted';
						}
						$(tdDiv).css({
							textAlign: pth.align,
							width: $('div:first', pth)[0].style.width
						});
						if (pth.hidden) {
							$(this).css('display', 'none');
						}
					}
					if (p.nowrap == false) {
						$(tdDiv).css('white-space', 'normal');
					}
					if (this.innerHTML == '') {
						this.innerHTML = '&nbsp;';
					}
					tdDiv.innerHTML = this.innerHTML;
					var prnt = $(this).parent()[0];
					var pid = false;
					if (prnt.id) {
						pid = prnt.id.substr(3);
					}
					if (pth != null) {
						if (pth.process) pth.process(tdDiv, pid);
					}
					$(this).empty().append(tdDiv).removeAttr('width'); //wrap content
				});
			},
			getCellDim: function (obj) {// get cell prop for editable event
				var ht = parseInt($(obj).height());
				var pht = parseInt($(obj).parent().height());
				var wt = parseInt(obj.style.width);
				var pwt = parseInt($(obj).parent().width());
				var top = obj.offsetParent.offsetTop;
				var left = obj.offsetParent.offsetLeft;
				var pdl = parseInt($(obj).css('paddingLeft'));
				var pdt = parseInt($(obj).css('paddingTop'));
				return {
					ht: ht,
					wt: wt,
					top: top,
					left: left,
					pdl: pdl,
					pdt: pdt,
					pht: pht,
					pwt: pwt
				};
			},
			addRowProp: function () {
				$('tbody tr', g.bDiv).each(function () {
					$(this).click(function (e) {
						var obj = (e.target || e.srcElement);
						if (obj.href || obj.type) return true;
						$(this).toggleClass('trSelected');
						if (p.singleSelect) $(this).siblings().removeClass('trSelected');
					}).mousedown(function (e) {
						if (e.shiftKey) {
							$(this).toggleClass('trSelected');
							g.multisel = true;
							this.focus();
							$(g.gDiv).noSelect();
						}
					}).mouseup(function () {
						if (g.multisel) {
							g.multisel = false;
							$(g.gDiv).noSelect(false);
						}
					}).hover(function (e) {
						if (g.multisel) {
							$(this).toggleClass('trSelected');
						}
					}, function () {});
					if ($.browser.msie && $.browser.version < 7.0) {
						$(this).hover(function () {
							$(this).addClass('trOver');
						}, function () {
							$(this).removeClass('trOver');
						});
					}
				});
			},
			pager: 0
		};
		if (p.colModel) { //create model if any
			thead = document.createElement('thead');
			var tr = document.createElement('tr');
			for (var i = 0; i < p.colModel.length; i++) {
				var cm = p.colModel[i];
				var th = document.createElement('th');
				th.innerHTML = cm.display;
				if (cm.name && cm.sortable) {
					$(th).attr('abbr', cm.name);
				}
				$(th).attr('axis', 'col' + i);
				if (cm.align) {
					th.align = cm.align;
				}
				if (cm.width) {
					$(th).attr('width', cm.width);
				}
				if ($(cm).attr('hide')) {
					th.hidden = true;
				}
				if (cm.process) {
					th.process = cm.process;
				}
				$(tr).append(th);
			}
			$(thead).append(tr);
			$(t).prepend(thead);
		} // end if p.colmodel
		//init divs
		g.gDiv = document.createElement('div'); //create global container
		g.mDiv = document.createElement('div'); //create title container
		g.hDiv = document.createElement('div'); //create header container
		g.bDiv = document.createElement('div'); //create body container
		g.vDiv = document.createElement('div'); //create grip
		g.rDiv = document.createElement('div'); //create horizontal resizer
		g.cDrag = document.createElement('div'); //create column drag
		g.block = document.createElement('div'); //creat blocker
		g.nDiv = document.createElement('div'); //create column show/hide popup
		g.nBtn = document.createElement('div'); //create column show/hide button
		g.iDiv = document.createElement('div'); //create editable layer
		g.tDiv = document.createElement('div'); //create toolbar
		g.sDiv = document.createElement('div');
		g.pDiv = document.createElement('div'); //create pager container
		if (!p.usepager) {
			g.pDiv.style.display = 'none';
		}
		g.hTable = document.createElement('table');
		g.gDiv.className = 'flexigrid';
		if (p.width != 'auto') {
			g.gDiv.style.width = p.width + 'px';
		}
		//add conditional classes
		if ($.browser.msie) {
			$(g.gDiv).addClass('ie');
		}
		if (p.novstripe) {
			$(g.gDiv).addClass('novstripe');
		}
		$(t).before(g.gDiv);
		$(g.gDiv).append(t);
		//set toolbar
		if (p.buttons) {
			g.tDiv.className = 'tDiv';
			var tDiv2 = document.createElement('div');
			tDiv2.className = 'tDiv2';
			for (var i = 0; i < p.buttons.length; i++) {
				var btn = p.buttons[i];
				if (!btn.separator) {
					var btnDiv = document.createElement('div');
					btnDiv.className = 'fbutton';
					btnDiv.innerHTML = "<div><span>" + btn.name + "</span></div>";
					if (btn.bclass) $('span', btnDiv).addClass(btn.bclass).css({
						paddingLeft: 20
					});
					btnDiv.onpress = btn.onpress;
					btnDiv.name = btn.name;
					if (btn.onpress) {
						$(btnDiv).click(function () {
							this.onpress(this.name, g.gDiv);
						});
					}
					$(tDiv2).append(btnDiv);
					if ($.browser.msie && $.browser.version < 7.0) {
						$(btnDiv).hover(function () {
							$(this).addClass('fbOver');
						}, function () {
							$(this).removeClass('fbOver');
						});
					}
				} else {
					$(tDiv2).append("<div class='btnseparator'></div>");
				}
			}
			$(g.tDiv).append(tDiv2);
			$(g.tDiv).append("<div style='clear:both'></div>");
			$(g.gDiv).prepend(g.tDiv);
		}
		g.hDiv.className = 'hDiv';
		$(t).before(g.hDiv);
		g.hTable.cellPadding = 0;
		g.hTable.cellSpacing = 0;
		$(g.hDiv).append('<div class="hDivBox"></div>');
		$('div', g.hDiv).append(g.hTable);
		var thead = $("thead:first", t).get(0);
		if (thead) $(g.hTable).append(thead);
		thead = null;
		if (!p.colmodel) var ci = 0;
		$('thead tr:first th', g.hDiv).each(function () {
			var thdiv = document.createElement('div');
			if ($(this).attr('abbr')) {
				$(this).click(function (e) {
					if (!$(this).hasClass('thOver')) return false;
					var obj = (e.target || e.srcElement);
					if (obj.href || obj.type) return true;
					g.changeSort(this);
				});
				if ($(this).attr('abbr') == p.sortname) {
					this.className = 'sorted';
					thdiv.className = 's' + p.sortorder;
				}
			}
			if (this.hidden) {
				$(this).hide();
			}
			if (!p.colmodel) {
				$(this).attr('axis', 'col' + ci++);
			}
			$(thdiv).css({
				textAlign: this.align,
				width: this.width + 'px'
			});
			thdiv.innerHTML = this.innerHTML;
			$(this).empty().append(thdiv).removeAttr('width').mousedown(function (e) {
				g.dragStart('colMove', e, this);
			}).hover(function () {
				if (!g.colresize && !$(this).hasClass('thMove') && !g.colCopy) {
					$(this).addClass('thOver');
				}
				if ($(this).attr('abbr') != p.sortname && !g.colCopy && !g.colresize && $(this).attr('abbr')) {
					$('div', this).addClass('s' + p.sortorder);
				} else if ($(this).attr('abbr') == p.sortname && !g.colCopy && !g.colresize && $(this).attr('abbr')) {
					var no = (p.sortorder == 'asc') ? 'desc' : 'asc';
					$('div', this).removeClass('s' + p.sortorder).addClass('s' + no);
				}
				if (g.colCopy) {
					var n = $('th', g.hDiv).index(this);
					if (n == g.dcoln) {
						return false;
					}
					if (n < g.dcoln) {
						$(this).append(g.cdropleft);
					} else {
						$(this).append(g.cdropright);
					}
					g.dcolt = n;
				} else if (!g.colresize) {
					var nv = $('th:visible', g.hDiv).index(this);
					var onl = parseInt($('div:eq(' + nv + ')', g.cDrag).css('left'));
					var nw = jQuery(g.nBtn).outerWidth();
					var nl = onl - nw + Math.floor(p.cgwidth / 2);
					$(g.nDiv).hide();
					$(g.nBtn).hide();
					$(g.nBtn).css({
						'left': nl,
						top: g.hDiv.offsetTop
					}).show();
					var ndw = parseInt($(g.nDiv).width());
					$(g.nDiv).css({
						top: g.bDiv.offsetTop
					});
					if ((nl + ndw) > $(g.gDiv).width()) {
						$(g.nDiv).css('left', onl - ndw + 1);
					} else {
						$(g.nDiv).css('left', nl);
					}
					if ($(this).hasClass('sorted')) {
						$(g.nBtn).addClass('srtd');
					} else {
						$(g.nBtn).removeClass('srtd');
					}
				}
			}, function () {
				$(this).removeClass('thOver');
				if ($(this).attr('abbr') != p.sortname) {
					$('div', this).removeClass('s' + p.sortorder);
				} else if ($(this).attr('abbr') == p.sortname) {
					var no = (p.sortorder == 'asc') ? 'desc' : 'asc';
					$('div', this).addClass('s' + p.sortorder).removeClass('s' + no);
				}
				if (g.colCopy) {
					$(g.cdropleft).remove();
					$(g.cdropright).remove();
					g.dcolt = null;
				}
			}); //wrap content
		});
		//set bDiv
		g.bDiv.className = 'bDiv';
		$(t).before(g.bDiv);
		$(g.bDiv).css({
			height: (p.height == 'auto') ? 'auto' : p.height + "px"
		}).scroll(function (e) {
			g.scroll()
		}).append(t);
		if (p.height == 'auto') {
			$('table', g.bDiv).addClass('autoht');
		}
		//add td & row properties
		g.addCellProp();
		g.addRowProp();
		//set cDrag
		var cdcol = $('thead tr:first th:first', g.hDiv).get(0);
		if (cdcol != null) {
			g.cDrag.className = 'cDrag';
			g.cdpad = 0;
			g.cdpad += (isNaN(parseInt($('div', cdcol).css('borderLeftWidth'))) ? 0 : parseInt($('div', cdcol).css('borderLeftWidth')));
			g.cdpad += (isNaN(parseInt($('div', cdcol).css('borderRightWidth'))) ? 0 : parseInt($('div', cdcol).css('borderRightWidth')));
			g.cdpad += (isNaN(parseInt($('div', cdcol).css('paddingLeft'))) ? 0 : parseInt($('div', cdcol).css('paddingLeft')));
			g.cdpad += (isNaN(parseInt($('div', cdcol).css('paddingRight'))) ? 0 : parseInt($('div', cdcol).css('paddingRight')));
			g.cdpad += (isNaN(parseInt($(cdcol).css('borderLeftWidth'))) ? 0 : parseInt($(cdcol).css('borderLeftWidth')));
			g.cdpad += (isNaN(parseInt($(cdcol).css('borderRightWidth'))) ? 0 : parseInt($(cdcol).css('borderRightWidth')));
			g.cdpad += (isNaN(parseInt($(cdcol).css('paddingLeft'))) ? 0 : parseInt($(cdcol).css('paddingLeft')));
			g.cdpad += (isNaN(parseInt($(cdcol).css('paddingRight'))) ? 0 : parseInt($(cdcol).css('paddingRight')));
			$(g.bDiv).before(g.cDrag);
			var cdheight = $(g.bDiv).height();
			var hdheight = $(g.hDiv).height();
			$(g.cDrag).css({
				top: -hdheight + 'px'
			});
			$('thead tr:first th', g.hDiv).each(function () {
				var cgDiv = document.createElement('div');
				$(g.cDrag).append(cgDiv);
				if (!p.cgwidth) {
					p.cgwidth = $(cgDiv).width();
				}
				$(cgDiv).css({
					height: cdheight + hdheight
				}).mousedown(function (e) {
					g.dragStart('colresize', e, this);
				});
				if ($.browser.msie && $.browser.version < 7.0) {
					g.fixHeight($(g.gDiv).height());
					$(cgDiv).hover(function () {
						g.fixHeight();
						$(this).addClass('dragging')
					}, function () {
						if (!g.colresize) $(this).removeClass('dragging')
					});
				}
			});
		}
		//add strip
		if (p.striped) {
			$('tbody tr:odd', g.bDiv).addClass('erow');
		}
		if (p.resizable && p.height != 'auto') {
			g.vDiv.className = 'vGrip';
			$(g.vDiv).mousedown(function (e) {
				g.dragStart('vresize', e)
			}).html('<span></span>');
			$(g.bDiv).after(g.vDiv);
		}
		if (p.resizable && p.width != 'auto' && !p.nohresize) {
			g.rDiv.className = 'hGrip';
			$(g.rDiv).mousedown(function (e) {
				g.dragStart('vresize', e, true);
			}).html('<span></span>').css('height', $(g.gDiv).height());
			if ($.browser.msie && $.browser.version < 7.0) {
				$(g.rDiv).hover(function () {
					$(this).addClass('hgOver');
				}, function () {
					$(this).removeClass('hgOver');
				});
			}
			$(g.gDiv).append(g.rDiv);
		}
		// add pager
		if (p.usepager) {
			g.pDiv.className = 'pDiv';
			g.pDiv.innerHTML = '<div class="pDiv2"></div>';
			$(g.bDiv).after(g.pDiv);
			var html = ' <div class="pGroup"> <div class="pFirst pButton"><span></span></div><div class="pPrev pButton"><span></span></div> </div> <div class="btnseparator"></div> <div class="pGroup"><span class="pcontrol">' + p.pagetext + ' <input type="text" size="4" value="1" /> ' + p.outof + ' <span> 1 </span></span></div> <div class="btnseparator"></div> <div class="pGroup"> <div class="pNext pButton"><span></span></div><div class="pLast pButton"><span></span></div> </div> <div class="btnseparator"></div> <div class="pGroup"> <div class="pReload pButton"><span></span></div> </div> <div class="btnseparator"></div> <div class="pGroup"><span class="pPageStat"></span></div>';
			$('div', g.pDiv).html(html);
			$('.pReload', g.pDiv).click(function () {
				g.populate()
			});
			$('.pFirst', g.pDiv).click(function () {
				g.changePage('first')
			});
			$('.pPrev', g.pDiv).click(function () {
				g.changePage('prev')
			});
			$('.pNext', g.pDiv).click(function () {
				g.changePage('next')
			});
			$('.pLast', g.pDiv).click(function () {
				g.changePage('last')
			});
			$('.pcontrol input', g.pDiv).keydown(function (e) {
				if (e.keyCode == 13) g.changePage('input')
			});
			if ($.browser.msie && $.browser.version < 7) $('.pButton', g.pDiv).hover(function () {
				$(this).addClass('pBtnOver');
			}, function () {
				$(this).removeClass('pBtnOver');
			});
			if (p.useRp) {
				var opt = '',
					sel = '';
				for (var nx = 0; nx < p.rpOptions.length; nx++) {
					if (p.rp == p.rpOptions[nx]) sel = 'selected="selected"';
					else sel = '';
					opt += "<option value='" + p.rpOptions[nx] + "' " + sel + " >" + p.rpOptions[nx] + "&nbsp;&nbsp;</option>";
				}
				$('.pDiv2', g.pDiv).prepend("<div class='pGroup'><select name='rp'>" + opt + "</select></div> <div class='btnseparator'></div>");
				$('select', g.pDiv).change(function () {
					if (p.onRpChange) {
						p.onRpChange(+this.value);
					} else {
						p.newp = 1;
						p.rp = +this.value;
						g.populate();
					}
				});
			}
			//add search button
			if (p.searchitems) {
				$('.pDiv2', g.pDiv).prepend("<div class='pGroup'> <div class='pSearch pButton'><span></span></div> </div>  <div class='btnseparator'></div>");
				$('.pSearch', g.pDiv).click(function () {
					$(g.sDiv).slideToggle('fast', function () {
						$('.sDiv:visible input:first', g.gDiv).trigger('focus');
					});
				});
				//add search box
				g.sDiv.className = 'sDiv';
				var sitems = p.searchitems;
				var sopt = '', sel = '';
				for (var s = 0; s < sitems.length; s++) {
					if (p.qtype == '' && sitems[s].isdefault == true) {
						p.qtype = sitems[s].name;
						sel = 'selected="selected"';
					} else {
						sel = '';
					}
					sopt += "<option value='" + sitems[s].name + "' " + sel + " >" + sitems[s].display + "&nbsp;&nbsp;</option>";
				}
				if (p.qtype == '') {
					p.qtype = sitems[0].name;
				}
				$(g.sDiv).append("<div class='sDiv2'>" + p.findtext + 
						" <input type='text' value='" + p.query +"' size='30' name='q' class='qsbox' /> "+
						" <select name='qtype'>" + sopt + "</select></div>");
				//Split into separate selectors because of bug in jQuery 1.3.2
				$('input[name=q]', g.sDiv).keydown(function (e) {
					if (e.keyCode == 13) {
						g.doSearch();
					}
				});
				$('select[name=qtype]', g.sDiv).keydown(function (e) {
					if (e.keyCode == 13) {
						g.doSearch();
					}
				});
				$('input[value=Clear]', g.sDiv).click(function () {
					$('input[name=q]', g.sDiv).val('');
					p.query = '';
					g.doSearch();
				});
				$(g.bDiv).after(g.sDiv);
			}
		}
		$(g.pDiv, g.sDiv).append("<div style='clear:both'></div>");
		// add title
		if (p.title) {
			g.mDiv.className = 'mDiv';
			g.mDiv.innerHTML = '<div class="ftitle">' + p.title + '</div>';
			$(g.gDiv).prepend(g.mDiv);
			if (p.showTableToggleBtn) {
				$(g.mDiv).append('<div class="ptogtitle" title="Minimize/Maximize Table"><span></span></div>');
				$('div.ptogtitle', g.mDiv).click(function () {
					$(g.gDiv).toggleClass('hideBody');
					$(this).toggleClass('vsble');
				});
			}
		}
		//setup cdrops
		g.cdropleft = document.createElement('span');
		g.cdropleft.className = 'cdropleft';
		g.cdropright = document.createElement('span');
		g.cdropright.className = 'cdropright';
		//add block
		g.block.className = 'gBlock';
		var gh = $(g.bDiv).height();
		var gtop = g.bDiv.offsetTop;
		$(g.block).css({
			width: g.bDiv.style.width,
			height: gh,
			background: 'white',
			position: 'relative',
			marginBottom: (gh * -1),
			zIndex: 1,
			top: gtop,
			left: '0px'
		});
		$(g.block).fadeTo(0, p.blockOpacity);
		// add column control
		if ($('th', g.hDiv).length) {
			g.nDiv.className = 'nDiv';
			g.nDiv.innerHTML = "<table cellpadding='0' cellspacing='0'><tbody></tbody></table>";
			$(g.nDiv).css({
				marginBottom: (gh * -1),
				display: 'none',
				top: gtop
			}).noSelect();
			var cn = 0;
			$('th div', g.hDiv).each(function () {
				var kcol = $("th[axis='col" + cn + "']", g.hDiv)[0];
				var chk = 'checked="checked"';
				if (kcol.style.display == 'none') {
					chk = '';
				}
				$('tbody', g.nDiv).append('<tr><td class="ndcol1"><input type="checkbox" ' + chk + ' class="togCol" value="' + cn + '" /></td><td class="ndcol2">' + this.innerHTML + '</td></tr>');
				cn++;
			});
			if ($.browser.msie && $.browser.version < 7.0) $('tr', g.nDiv).hover(function () {
				$(this).addClass('ndcolover');
			}, function () {
				$(this).removeClass('ndcolover');
			});
			$('td.ndcol2', g.nDiv).click(function () {
				if ($('input:checked', g.nDiv).length <= p.minColToggle && $(this).prev().find('input')[0].checked) return false;
				return g.toggleCol($(this).prev().find('input').val());
			});
			$('input.togCol', g.nDiv).click(function () {
				if ($('input:checked', g.nDiv).length < p.minColToggle && this.checked == false) return false;
				$(this).parent().next().trigger('click');
			});
			$(g.gDiv).prepend(g.nDiv);
			$(g.nBtn).addClass('nBtn')
				.html('<div></div>')
				.attr('title', 'Hide/Show Columns')
				.click(function () {
					$(g.nDiv).toggle();
					return true;
				}
			);
			if (p.showToggleBtn) {
				$(g.gDiv).prepend(g.nBtn);
			}
		}
		// add date edit layer
		$(g.iDiv).addClass('iDiv').css({
			display: 'none'
		});
		$(g.bDiv).append(g.iDiv);
		// add flexigrid events
		$(g.bDiv).hover(function () {
			$(g.nDiv).hide();
			$(g.nBtn).hide();
		}, function () {
			if (g.multisel) {
				g.multisel = false;
			}
		});
		$(g.gDiv).hover(function () {}, function () {
			$(g.nDiv).hide();
			$(g.nBtn).hide();
		});
		//add document events
		$(document).mousemove(function (e) {
			g.dragMove(e)
		}).mouseup(function (e) {
			g.dragEnd()
		}).hover(function () {}, function () {
			g.dragEnd()
		});
		//browser adjustments
		if ($.browser.msie && $.browser.version < 7.0) {
			$('.hDiv,.bDiv,.mDiv,.pDiv,.vGrip,.tDiv, .sDiv', g.gDiv).css({
				width: '100%'
			});
			$(g.gDiv).addClass('ie6');
			if (p.width != 'auto') {
				$(g.gDiv).addClass('ie6fullwidthbug');
			}
		}
		g.rePosDrag();
		g.fixHeight();
		//make grid functions accessible
		t.p = p;
		t.grid = g;
		// load data
		if (p.url && p.autoload) {
			g.populate();
		}
		return t;
	};
	var docloaded = false;
	$(document).ready(function () {
		docloaded = true
	});
	$.fn.flexigrid = function (p) {
		return this.each(function () {
			if (!docloaded) {
				$(this).hide();
				var t = this;
				$(document).ready(function () {
					$.addFlex(t, p);
				});
			} else {
				$.addFlex(this, p);
			}
		});
	}; //end flexigrid
	$.fn.flexReload = function (p) { // function to reload grid
		return this.each(function () {
			if (this.grid && this.p.url) this.grid.populate();
		});
	}; //end flexReload
	$.fn.flexOptions = function (p) { //function to update general options
		return this.each(function () {
			if (this.grid) $.extend(this.p, p);
		});
	}; //end flexOptions
	$.fn.flexToggleCol = function (cid, visible) { // function to reload grid
		return this.each(function () {
			if (this.grid) this.grid.toggleCol(cid, visible);
		});
	}; //end flexToggleCol
	$.fn.flexAddData = function (data) { // function to add data to grid
		return this.each(function () {
			if (this.grid) this.grid.addData(data);
		});
	};
	$.fn.noSelect = function (p) { //no select plugin by me :-)
		var prevent = (p == null) ? true : p;
		if (prevent) {
			return this.each(function () {
				if ($.browser.msie || $.browser.safari) $(this).bind('selectstart', function () {
					return false;
				});
				else if ($.browser.mozilla) {
					$(this).css('MozUserSelect', 'none');
					$('body').trigger('focus');
				} else if ($.browser.opera) $(this).bind('mousedown', function () {
					return false;
				});
				else $(this).attr('unselectable', 'on');
			});
		} else {
			return this.each(function () {
				if ($.browser.msie || $.browser.safari) $(this).unbind('selectstart');
				else if ($.browser.mozilla) $(this).css('MozUserSelect', 'inherit');
				else if ($.browser.opera) $(this).unbind('mousedown');
				else $(this).removeAttr('unselectable', 'on');
			});
		}
	}; //end noSelect
})(jQuery);

















































/*

A jQuery edit in place plugin

Version 2.3.0

Authors:
	Dave Hauenstein
	Martin Häcker <spamfaenger [at] gmx [dot] de>

Project home:
	http://code.google.com/p/jquery-in-place-editor/

Patches with tests welcomed! For guidance see the tests  </spec/unit/>. To submit, attach them to the bug tracker.

License:
This source file is subject to the BSD license bundled with this package.
Available online: {@link http://www.opensource.org/licenses/bsd-license.php}
If you did not receive a copy of the license, and are unable to obtain it, 
learn to use a search engine.

*/

(function($){

$.fn.editInPlace = function(options) {
	
	var settings = $.extend({}, $.fn.editInPlace.defaults, options);
	assertMandatorySettingsArePresent(settings);
	preloadImage(settings.saving_image);
	
	return this.each(function() {
		var dom = $(this);
		// This won't work with live queries as there is no specific element to attach this
		// one way to deal with this could be to store a reference to self and then compare that in click?
		if (dom.data('editInPlace'))
			return; // already an editor here
		dom.data('editInPlace', true);
		
		new InlineEditor(settings, dom).init();
	});
};

/// Switch these through the dictionary argument to $(aSelector).editInPlace(overideOptions)
/// Required Options: Either url or callback, so the editor knows what to do with the edited values.
$.fn.editInPlace.defaults = {
	url:				"", // string: POST URL to send edited content
	bg_over:			"#ffc", // string: background color of hover of unactivated editor
	bg_out:				"transparent", // string: background color on restore from hover
	hover_class:		"",  // string: class added to root element during hover. Will override bg_over and bg_out
	show_buttons:		false, // boolean: will show the buttons: cancel or save; will automatically cancel out the onBlur functionality
	save_button:		'<button class="inplace_save">Save</button>', // string: image button tag to use as “Save” button
	cancel_button:		'<button class="inplace_cancel">Cancel</button>', // string: image button tag to use as “Cancel” button
	params:				"", // string: example: first_name=dave&last_name=hauenstein extra paramters sent via the post request to the server
	field_type:			"text", // string: "text", "textarea", or "select";  The type of form field that will appear on instantiation
	default_text:		"(Click here to add text)", // string: text to show up if the element that has this functionality is empty
	use_html:			false, // boolean, set to true if the editor should use jQuery.fn.html() to extract the value to show from the dom node
	textarea_rows:		10, // integer: set rows attribute of textarea, if field_type is set to textarea. Use CSS if possible though
	textarea_cols:		25, // integer: set cols attribute of textarea, if field_type is set to textarea. Use CSS if possible though
	select_text:		"Choose new value", // string: default text to show up in select box
	select_options:		"", // string or array: Used if field_type is set to 'select'. Can be comma delimited list of options 'textandValue,text:value', Array of options ['textAndValue', 'text:value'] or array of arrays ['textAndValue', ['text', 'value']]. The last form is especially usefull if your labels or values contain colons)
	text_size:			null, // integer: set cols attribute of text input, if field_type is set to text. Use CSS if possible though
	
	// Specifying callback_skip_dom_reset will disable all saving_* options
	saving_text:		undefined, // string: text to be used when server is saving information. Example "Saving..."
	saving_image:		"", // string: uses saving text specify an image location instead of text while server is saving
	saving_animation_color: 'transparent', // hex color string, will be the color the pulsing animation during the save pulses to. Note: Only works if jquery-ui is loaded
	
	value_required:		false, // boolean: if set to true, the element will not be saved unless a value is entered
	element_id:			"element_id", // string: name of parameter holding the id or the editable
	update_value:		"update_value", // string: name of parameter holding the updated/edited value
	original_value:		'original_value', // string: name of parameter holding the updated/edited value
	original_html:		"original_html", // string: name of parameter holding original_html value of the editable /* DEPRECATED in 2.2.0 */ use original_value instead.
	save_if_nothing_changed:	false,  // boolean: submit to function or server even if the user did not change anything
	on_blur:			"save", // string: "save" or null; what to do on blur; will be overridden if show_buttons is true
	cancel:				"", // string: if not empty, a jquery selector for elements that will not cause the editor to open even though they are clicked. E.g. if you have extra buttons inside editable fields
	
	// All callbacks will have this set to the DOM node of the editor that triggered the callback
	
	callback:			null, // function: function to be called when editing is complete; cancels ajax submission to the url param. Prototype: function(idOfEditor, enteredText, orinalHTMLContent, settingsParams, callbacks). The function needs to return the value that should be shown in the dom. Returning undefined means cancel and will restore the dom and trigger an error. callbacks is a dictionary with two functions didStartSaving and didEndSaving() that you can use to tell the inline editor that it should start and stop any saving animations it has configured. /* DEPRECATED in 2.1.0 */ Parameter idOfEditor, use $(this).attr('id') instead
	callback_skip_dom_reset: false, // boolean: set this to true if the callback should handle replacing the editor with the new value to show
	success:			null, // function: this function gets called if server responds with a success. Prototype: function(newEditorContentString)
	error:				null, // function: this function gets called if server responds with an error. Prototype: function(request)
	error_sink:			function(idOfEditor, errorString) { alert(errorString); }, // function: gets id of the editor and the error. Make sure the editor has an id, or it will just be undefined. If set to null, no error will be reported. /* DEPRECATED in 2.1.0 */ Parameter idOfEditor, use $(this).attr('id') instead
	preinit:			null, // function: this function gets called after a click on an editable element but before the editor opens. If you return false, the inline editor will not open. Prototype: function(currentDomNode). DEPRECATED in 2.2.0 use delegate shouldOpenEditInPlace call instead
	postclose:			null, // function: this function gets called after the inline editor has closed and all values are updated. Prototype: function(currentDomNode). DEPRECATED in 2.2.0 use delegate didCloseEditInPlace call instead
	delegate:			null // object: if it has methods with the name of the callbacks documented below in delegateExample these will be called. This means that you just need to impelment the callbacks you are interested in.
};

// Lifecycle events that the delegate can implement
// this will always be fixed to the delegate
var delegateExample = {
	// called while opening the editor.
	// return false to prevent editor from opening
	shouldOpenEditInPlace: function(aDOMNode, aSettingsDict, triggeringEvent) {},
	// return content to show in inplace editor
	willOpenEditInPlace: function(aDOMNode, aSettingsDict) {},
	didOpenEditInPlace: function(aDOMNode, aSettingsDict) {},
	
	// called while closing the editor
	// return false to prevent the editor from closing
	shouldCloseEditInPlace: function(aDOMNode, aSettingsDict, triggeringEvent) {},
	// return value will be shown during saving
	willCloseEditInPlace: function(aDOMNode, aSettingsDict) {},
	didCloseEditInPlace: function(aDOMNode, aSettingsDict) {},
	
	missingCommaErrorPreventer:''
};


function InlineEditor(settings, dom) {
	this.settings = settings;
	this.dom = dom;
	this.originalValue = null;
	this.didInsertDefaultText = false;
	this.shouldDelayReinit = false;
};

$.extend(InlineEditor.prototype, {
	
	init: function() {
		this.setDefaultTextIfNeccessary();
		this.connectOpeningEvents();
	},
	
	reinit: function() {
		if (this.shouldDelayReinit)
			return;
		
		this.triggerCallback(this.settings.postclose, /* DEPRECATED in 2.1.0 */ this.dom);
		this.triggerDelegateCall('didCloseEditInPlace');
		
		this.markEditorAsInactive();
		this.connectOpeningEvents();
	},
	
	setDefaultTextIfNeccessary: function() {
		if('' !== this.dom.html())
			return;
		
		this.dom.html(this.settings.default_text);
		this.didInsertDefaultText = true;
	},
	
	connectOpeningEvents: function() {
		var that = this;
		this.dom
			.bind('mouseenter.editInPlace', function(){ that.addHoverEffect(); })
			.bind('mouseleave.editInPlace', function(){ that.removeHoverEffect(); })
			.bind('click.editInPlace', function(anEvent){ that.openEditor(anEvent); });
	},
	
	disconnectOpeningEvents: function() {
	 	// prevent re-opening the editor when it is already open
		this.dom.unbind('.editInPlace');
	},
	
	addHoverEffect: function() {
		if (this.settings.hover_class)
			this.dom.addClass(this.settings.hover_class);
		else
			this.dom.css("background-color", this.settings.bg_over);
	},
	
	removeHoverEffect: function() {
		if (this.settings.hover_class)
			this.dom.removeClass(this.settings.hover_class);
		else
			this.dom.css("background-color", this.settings.bg_out);
	},
	
	openEditor: function(anEvent) {
		if ( ! this.shouldOpenEditor(anEvent))
			return;
		
		this.disconnectOpeningEvents();
		this.removeHoverEffect();
		this.removeInsertedDefaultTextIfNeccessary();
		this.saveOriginalValue();
		this.markEditorAsActive();
		this.replaceContentWithEditor();
		this.setInitialValue();
		this.workAroundMissingBlurBug();
		this.connectClosingEventsToEditor();
		this.triggerDelegateCall('didOpenEditInPlace');
	},
	
	shouldOpenEditor: function(anEvent) {
		if (this.isClickedObjectCancelled(anEvent.target))
			return false;
		
		if (false === this.triggerCallback(this.settings.preinit, /* DEPRECATED in 2.1.0 */ this.dom))
			return false;
		
		if (false === this.triggerDelegateCall('shouldOpenEditInPlace', true, anEvent))
			return false;
		
		return true;
	},
	
	removeInsertedDefaultTextIfNeccessary: function() {
		if ( ! this.didInsertDefaultText
			|| this.dom.html() !== this.settings.default_text)
			return;
		
		this.dom.html('');
		this.didInsertDefaultText = false;
	},
	
	isClickedObjectCancelled: function(eventTarget) {
		if ( ! this.settings.cancel)
			return false;
		
		var eventTargetAndParents = $(eventTarget).parents().andSelf();
		var elementsMatchingCancelSelector = eventTargetAndParents.filter(this.settings.cancel);
		return 0 !== elementsMatchingCancelSelector.length;
	},
	
	saveOriginalValue: function() {
		if (this.settings.use_html)
			this.originalValue = this.dom.html();
		else
			this.originalValue = trim(this.dom.text());
	},
	
	restoreOriginalValue: function() {
		this.setClosedEditorContent(this.originalValue);
	},
	
	setClosedEditorContent: function(aValue) {
		if (this.settings.use_html)
			this.dom.html(aValue);
		else
			this.dom.text(aValue);
	},
	
	workAroundMissingBlurBug: function() {
		// Strangely, all browser will forget to send a blur event to an input element
		// when another one is created and selected programmatically. (at least under some circumstances). 
		// This means that if another inline editor is opened, existing inline editors will _not_ close 
		// if they are configured to submit when blurred.
		
		// Using parents() instead document as base to workaround the fact that in the unittests
		// the editor is not a child of window.document but of a document fragment
		var ourInput = this.dom.find(':input');
		this.dom.parents(':last').find('.editInPlace-active :input').not(ourInput).blur();
	},
	
	replaceContentWithEditor: function() {
		var buttons_html  = (this.settings.show_buttons) ? this.settings.save_button + ' ' + this.settings.cancel_button : '';
		var editorElement = this.createEditorElement(); // needs to happen before anything is replaced
		/* insert the new in place form after the element they click, then empty out the original element */
		this.dom.html('<form class="inplace_form" style="display: inline; margin: 0; padding: 0;"></form>')
			.find('form')
				.append(editorElement)
				.append(buttons_html);
	},
	
	createEditorElement: function() {
		if (-1 === $.inArray(this.settings.field_type, ['text', 'textarea', 'select']))
			throw "Unknown field_type <fnord>, supported are 'text', 'textarea' and 'select'";
		
		var editor = null;
		if ("select" === this.settings.field_type)
			editor = this.createSelectEditor();
		else if ("text" === this.settings.field_type)
			editor = $('<input type="text" ' + this.inputNameAndClass() 
				+ ' size="' + this.settings.text_size  + '" />');
		else if ("textarea" === this.settings.field_type)
			editor = $('<textarea ' + this.inputNameAndClass() 
				+ ' rows="' + this.settings.textarea_rows + '" '
				+ ' cols="' + this.settings.textarea_cols + '" />');
		
		return editor;
	},
	
	setInitialValue: function() {
		var initialValue = this.triggerDelegateCall('willOpenEditInPlace', this.originalValue);
		var editor = this.dom.find(':input');
		editor.val(initialValue);
		
		// Workaround for select fields which don't contain the original value.
		// Somehow the browsers don't like to select the instructional choice (disabled) in that case
		if (editor.val() !== initialValue)
			editor.val(''); // selects instructional choice
	},
	
	inputNameAndClass: function() {
		return ' name="inplace_value" class="inplace_field" ';
	},
	
	createSelectEditor: function() {
		var editor = $('<select' + this.inputNameAndClass() + '>'
			+	'<option disabled="true" value="">' + this.settings.select_text + '</option>'
			+ '</select>');
		
		var optionsArray = this.settings.select_options;
		if ( ! $.isArray(optionsArray))
			optionsArray = optionsArray.split(',');
		
		for (var i=0; i<optionsArray.length; i++) {
			var currentTextAndValue = optionsArray[i];
			if ( ! $.isArray(currentTextAndValue))
				currentTextAndValue = currentTextAndValue.split(':');
			
			var value = trim(currentTextAndValue[1] || currentTextAndValue[0]);
			var text = trim(currentTextAndValue[0]);
			
			var option = $('<option>').val(value).text(text);
			editor.append(option);
		}
		
		return editor;
	},
	
	connectClosingEventsToEditor: function() {
		var that = this;
		function cancelEditorAction(anEvent) {
			that.handleCancelEditor(anEvent);
			return false; // stop event bubbling
		}
		function saveEditorAction(anEvent) {
			that.handleSaveEditor(anEvent);
			return false; // stop event bubbling
		}
		
		var form = this.dom.find("form");
		
		form.find(".inplace_field").focus().select();
		form.find(".inplace_cancel").click(cancelEditorAction);
		form.find(".inplace_save").click(saveEditorAction);
		
		if ( ! this.settings.show_buttons) {
				// TODO: Firefox has a bug where blur is not reliably called when focus is lost 
				//       (for example by another editor appearing)
			if ("save" === this.settings.on_blur)
				form.find(".inplace_field").blur(saveEditorAction);
			else
				form.find(".inplace_field").blur(cancelEditorAction);
			
			// workaround for msie & firefox bug where it won't submit on enter if no button is shown
			if ($.browser.mozilla || $.browser.msie)
				this.bindSubmitOnEnterInInput();
		}
		
		form.keyup(function(anEvent) {
			// allow canceling with escape
			var escape = 27;
			if (escape === anEvent.which)
				return cancelEditorAction();
		});
		
		// workaround for webkit nightlies where they won't submit at all on enter
		// REFACT: find a way to just target the nightlies
		if ($.browser.safari)
			this.bindSubmitOnEnterInInput();
		
		
		form.submit(saveEditorAction);
	},
	
	bindSubmitOnEnterInInput: function() {
		if ('textarea' === this.settings.field_type)
			return; // can't enter newlines otherwise
		
		var that = this;
		this.dom.find(':input').keyup(function(event) {
			var enter = 13;
			if (enter === event.which)
				return that.dom.find('form').submit();
		});
	 	
	},
	
	handleCancelEditor: function(anEvent) {
		// REFACT: remove duplication between save and cancel
		if (false === this.triggerDelegateCall('shouldCloseEditInPlace', true, anEvent))
			return;
		
		var enteredText = this.dom.find(':input').val();
		enteredText = this.triggerDelegateCall('willCloseEditInPlace', enteredText);
		
		this.restoreOriginalValue();
		//Oliver
		/*
		if ( hasContent(enteredText) 
			&& ! this.isDisabledDefaultSelectChoice())
			this.setClosedEditorContent(enteredText);
		*/
		//Oliver
		this.reinit();
	},
	
	handleSaveEditor: function(anEvent) {
		if (false === this.triggerDelegateCall('shouldCloseEditInPlace', true, anEvent))
			return;
		
		var enteredText = this.dom.find(':input').val();
		enteredText = this.triggerDelegateCall('willCloseEditInPlace', enteredText);
		
		if (this.isDisabledDefaultSelectChoice()
			|| this.isUnchangedInput(enteredText)) {
			this.handleCancelEditor(anEvent);
			return;
		}
		
		if (this.didForgetRequiredText(enteredText)) {
			this.handleCancelEditor(anEvent);
			this.reportError("Error: You must enter a value to save this field");
			return;
		}
		
		this.showSaving(enteredText);
		
		if (this.settings.callback)
			this.handleSubmitToCallback(enteredText);
		else
			this.handleSubmitToServer(enteredText);
	},
	
	didForgetRequiredText: function(enteredText) {
		return this.settings.value_required 
			&& ("" === enteredText 
				|| undefined === enteredText
				|| null === enteredText);
	},
	
	isDisabledDefaultSelectChoice: function() {
		return this.dom.find('option').eq(0).is(':selected:disabled');
	},
	
	isUnchangedInput: function(enteredText) {
		return ! this.settings.save_if_nothing_changed
			&& this.originalValue === enteredText;
	},
	
	showSaving: function(enteredText) {
		if (this.settings.callback && this.settings.callback_skip_dom_reset)
			return;
		
		var savingMessage = enteredText;
		if (hasContent(this.settings.saving_text))
			savingMessage = this.settings.saving_text;
		if(hasContent(this.settings.saving_image))
			// REFACT: alt should be the configured saving message
			savingMessage = $('<img />').attr('src', this.settings.saving_image).attr('alt', savingMessage);
		this.dom.html(savingMessage);
	},
	
	handleSubmitToCallback: function(enteredText) {
		// REFACT: consider to encode enteredText and originalHTML before giving it to the callback
		this.enableOrDisableAnimationCallbacks(true, false);
		var newHTML = this.triggerCallback(this.settings.callback, /* DEPRECATED in 2.1.0 */ this.id(), enteredText, this.originalValue, 
			this.settings.params, this.savingAnimationCallbacks());
		
		if (this.settings.callback_skip_dom_reset)
			; // do nothing
		else if (undefined === newHTML) {
			// failure; put original back
			this.reportError("Error: Failed to save value: " + enteredText);
			this.restoreOriginalValue();
		}
		else
			// REFACT: use setClosedEditorContent
			this.dom.html(newHTML);
		
		if (this.didCallNoCallbacks()) {
			this.enableOrDisableAnimationCallbacks(false, false);
			this.reinit();
		}
	},
	
	handleSubmitToServer: function(enteredText) {
		var data = this.settings.update_value + '=' + encodeURIComponent(enteredText) 
			+ '&' + this.settings.element_id + '=' + this.dom.attr("id") 
			+ ((this.settings.params) ? '&' + this.settings.params : '')
			+ '&' + this.settings.original_html + '=' + encodeURIComponent(this.originalValue) /* DEPRECATED in 2.2.0 */
			+ '&' + this.settings.original_value + '=' + encodeURIComponent(this.originalValue);
		
		this.enableOrDisableAnimationCallbacks(true, false);
		this.didStartSaving();
		var that = this;
		$.ajax({
			url: that.settings.url,
			type: "POST",
			data: data,
			dataType: "html",
			complete: function(request){
				that.didEndSaving();
			},
			success: function(html){
				var new_text = html || that.settings.default_text;
				
				/* put the newly updated info into the original element */
				// FIXME: should be affected by the preferences switch
				that.dom.html(new_text);
				// REFACT: remove dom parameter, already in this, not documented, should be easy to remove
				// REFACT: callback should be able to override what gets put into the DOM
				that.triggerCallback(that.settings.success, html);
			},
			error: function(request) {
				that.dom.html(that.originalHTML); // REFACT: what about a restorePreEditingContent()
				if (that.settings.error)
					// REFACT: remove dom parameter, already in this, not documented, can remove without deprecation
					// REFACT: callback should be able to override what gets entered into the DOM
					that.triggerCallback(that.settings.error, request);
				else
					that.reportError("Failed to save value: " + request.responseText || 'Unspecified Error');
			}
		});
	},
	
	// Utilities .........................................................
	
	triggerCallback: function(aCallback /*, arguments */) {
		if ( ! aCallback)
			return; // callback wasn't specified after all
		
		var callbackArguments = Array.prototype.slice.call(arguments, 1);
		return aCallback.apply(this.dom[0], callbackArguments);
	},
	
	/// defaultReturnValue is only used if the delegate returns undefined
	triggerDelegateCall: function(aDelegateMethodName, defaultReturnValue, optionalEvent) {
		// REFACT: consider to trigger equivalent callbacks automatically via a mapping table?
		if ( ! this.settings.delegate
			|| ! $.isFunction(this.settings.delegate[aDelegateMethodName]))
			return defaultReturnValue;
		
		var delegateReturnValue =  this.settings.delegate[aDelegateMethodName](this.dom, this.settings, optionalEvent);
		return (undefined === delegateReturnValue)
			? defaultReturnValue
			: delegateReturnValue;
	},
	
	reportError: function(anErrorString) {
		this.triggerCallback(this.settings.error_sink, /* DEPRECATED in 2.1.0 */ this.id(), anErrorString);
	},
	
	// REFACT: this method should go, callbacks should get the dom node itself as an argument
	id: function() {
		return this.dom.attr('id');
	},
	
	markEditorAsActive: function() {
		this.dom.addClass('editInPlace-active');
	},
	
	markEditorAsInactive: function() {
		this.dom.removeClass('editInPlace-active');
	},
	
	// REFACT: consider rename, doesn't deal with animation directly
	savingAnimationCallbacks: function() {
		var that = this;
		return {
			didStartSaving: function() { that.didStartSaving(); },
			didEndSaving: function() { that.didEndSaving(); }
		};
	},
	
	enableOrDisableAnimationCallbacks: function(shouldEnableStart, shouldEnableEnd) {
		this.didStartSaving.enabled = shouldEnableStart;
		this.didEndSaving.enabled = shouldEnableEnd;
	},
	
	didCallNoCallbacks: function() {
		return this.didStartSaving.enabled && ! this.didEndSaving.enabled;
	},
	
	assertCanCall: function(methodName) {
		if ( ! this[methodName].enabled)
			throw new Error('Cannot call ' + methodName + ' now. See documentation for details.');
	},
	
	didStartSaving: function() {
		this.assertCanCall('didStartSaving');
		this.shouldDelayReinit = true;
		this.enableOrDisableAnimationCallbacks(false, true);
		
		this.startSavingAnimation();
	},
	
	didEndSaving: function() {
		this.assertCanCall('didEndSaving');
		this.shouldDelayReinit = false;
		this.enableOrDisableAnimationCallbacks(false, false);
		this.reinit();
		
		this.stopSavingAnimation();
	},
	
	startSavingAnimation: function() {
		var that = this;
		this.dom
			.animate({ backgroundColor: this.settings.saving_animation_color }, 400)
			.animate({ backgroundColor: 'transparent'}, 400, 'swing', function(){
				// In the tests animations are turned off - i.e they happen instantaneously.
				// Hence we need to prevent this from becomming an unbounded recursion.
				setTimeout(function(){ that.startSavingAnimation(); }, 10);
			});
	},
	
	stopSavingAnimation: function() {
		this.dom
			.stop(true)
			.css({backgroundColor: ''});
	},
	
	missingCommaErrorPreventer:''
});



// Private helpers .......................................................

function assertMandatorySettingsArePresent(options) {
	// one of these needs to be non falsy
	if (options.url || options.callback)
		return;
	
	throw new Error("Need to set either url: or callback: option for the inline editor to work.");
}

/* preload the loading icon if it is configured */
function preloadImage(anImageURL) {
	if ('' === anImageURL)
		return;
	
	var loading_image = new Image();
	loading_image.src = anImageURL;
}

function trim(aString) {
	return aString
		.replace(/^\s+/, '')
		.replace(/\s+$/, '');
}

function hasContent(something) {
	if (undefined === something || null === something)
		return false;
	
	if (0 === something.length)
		return false;
	
	return true;
}

})(jQuery);
