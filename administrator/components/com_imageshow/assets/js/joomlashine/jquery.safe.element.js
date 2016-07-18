if (typeof(JoomlaShine) != 'undefined' && typeof(JoomlaShine.jQueryBackup) != 'undefined')
{
	jQuery.noConflict();
	JoomlaShine.jQuery = jQuery;
	jQuery = JoomlaShine.jQueryBackup;
	
}

