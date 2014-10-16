(function() {

	var Event = tinymce.dom.Event;
	var DOM = tinymce.DOM;
	var cfNODE = null;

	tinymce.PluginManager.requireLangPack('cforms');
	
	tinymce.create('tinymce.plugins.cformsPlugin', {
		
		init : function(ed, url) {

			ed.onInit.add(function() { 
				ed.dom.loadCSS(purl + "insertdialog25.css");
			});

			ed.addCommand('mcecforms', function(ui,v) {
				ed.windowManager.open({
					file : ajaxurl+'?action=cforms2_insertmcedialog',
					width : 400,
					height : 125 + (tinyMCE.isNS7 ? 20 : 0) + (tinyMCE.isMSIE ? 0 : 0),
					inline : 1
				}, {
					plugin_url:purl,
					some_custom_arg : 'custom arg' // Custom argument
				});
								
			});

			ed.onMouseDown.addToTop(function(ed, e) {
				n = e.target;
				if ( ed.dom.hasClass(n, 'mce_plugin_cforms_img') )
					return Event.cancel(e);
			});

			ed.onNodeChange.add(function(ed, cm, n, co) {
				cm.setDisabled('cforms', (n.nodeName === 'SPAN' && ed.dom.hasClass(n, 'mce_plugin_cforms_img')) );
				cm.setActive('cforms', (n.nodeName === 'SPAN' && ed.dom.hasClass(n, 'mce_plugin_cforms_img')) );
			});
			
			ed.addButton('cforms', { title : 'cforms.desc', cmd : 'mcecforms', image : purl+'../images/button.gif' }); //???
			ed.addShortcut('ctrl+f', ed.getLang('cforms.desc'), 'mcecforms');

			// Replace morebreak with images
			ed.onBeforeSetContent.add(function(ed, o) {

					var startPos = 0;
	
					while ((startPos = o.content.indexOf('<!--cforms', startPos)) != -1) {
	
						var endPos = o.content.indexOf('-->', startPos) + 3;
	
						var no = o.content.substring(startPos + 10, endPos - 3);
						
						if ( no.match(/name=/) ){
							var formName = /name="([^"]+)"/;
							fname = formName.exec(no);
							fname = fname[1];
							no = no.replace('name','title');	
						}else{
							fname = formnames[(no==''?1:no)-1];
							if ( no == '' )
								no=' id="cf1"';
							else
								no=' id="cf'+no+'"';	
						}
						
						var contentAfter = o.content.substring(endPos);
						o.content = o.content.substring(0, startPos);
						o.content += '<span'+no+' class="mce_plugin_cforms_img">'+fname+'</span>';
						o.content += contentAfter;
	
						startPos++;
					}
	
			});


			// Replace images
			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = o.content.replace(/<span [^\/]+\/span>/g, function(im) {
						if (im.indexOf('class="mce_plugin_cforms_img') !== -1) {

							if ( im.match(/title=/) ){
	                            var m;
	                            var cf_name = (m = im.match(/title="([^"]+)"/)) ? m[1] : '';
	                            im = '<!--cforms name="'+cf_name+'"-->';								
							}else{
	                            var m;
	                            var cf_id = (m = im.match(/id="cf(.*?)"/)) ? m[1] : '';
	                            im = '<!--cforms'+cf_id+'-->';
	                        }
	                        
                        }		
                        return im;
					});
			});

			/* noneditable */
			var t = this;

			t.editor = ed;
			nonEditClass = 'mce_plugin_cforms_img';

			ed.onNodeChange.addToTop(function(ed, cm, n) {
				var sc, ec;

				// Block if start or end is inside a non editable element
				sc = ed.dom.getParent(ed.selection.getStart(), function(n) {
					return ed.dom.hasClass(n, nonEditClass);
				});

				ec = ed.dom.getParent(ed.selection.getEnd(), function(n) {
					return ed.dom.hasClass(n, nonEditClass);
				});

				// Block or unblock
				if (sc || ec) {
					cfNODE=n;
					t._setDisabled(1);
					return false;
				} else
					t._setDisabled(0);
			});
			/* noneditable */

			
		}, // init

		getInfo : function() {
			return {
				longname : 'cforms',
				author : 'Oliver Seidel',
				authorurl : 'http://www.deliciousdays.com',
				infourl : 'http://www.deliciousdays.com',
				version : "8.0"
			};
		},
		
		/* noneditable */
		_block : function(ed, e) {
			
			if ( e.type == 'keypress' ){
						
				if( e.keyCode == 8 || e.keyCode == 46  ){ 	// del || bksp				
					ed.dom.remove( cfNODE );
					ed.nodeChanged();
				}
				else if( e.keyCode == 13 ){ 				// CR
					ed.selection.select( ed.dom.select('body')[0] );
					ed.selection.setNode( ed.dom.create('p') );
					ed.nodeChanged();
				}
				
			}
			return Event.cancel(e);
			
		},

		_setDisabled : function(s,n) {
			var t = this, ed = t.editor;

			tinymce.each(ed.controlManager.controls, function(c) {
				c.setDisabled(s);
			});

			if (s !== t.disabled) {
				if (s) {
					ed.onKeyDown.addToTop(t._block);
					ed.onKeyPress.addToTop(t._block);
					ed.onKeyUp.addToTop(t._block);
					ed.onPaste.addToTop(t._block);
				} else {
					ed.onKeyDown.remove(t._block);
					ed.onKeyPress.remove(t._block);
					ed.onKeyUp.remove(t._block);
					ed.onPaste.remove(t._block);
				}

				t.disabled = s;
			}
		}
		/* noneditable */

	});

	// Register plugin
	tinymce.PluginManager.add('cforms', tinymce.plugins.cformsPlugin);
})();
