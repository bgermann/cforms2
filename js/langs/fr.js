// For Wordpress > 2.5x
if ( tinyMCE.addI18n ){
	tinyMCE.addI18n('fr.cforms',{
		desc : 'Ins\351rer un formulaire'
	});
}
else
{
	// For Wordpress <= 2.3x
	tinyMCE.addToLang('cforms', {
		desc : 'Ins\351rer un formulaire'
	});
}
