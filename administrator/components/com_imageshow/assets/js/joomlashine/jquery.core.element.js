if (typeof JoomlaShine != 'undefined' && typeof JoomlaShine.jQuery != 'undefined') {
	// Backup current jQuery instance
	JoomlaShine.jQueryBackup = jQuery;

	// Restore jQuery instance loaded by JoomlaShine script
	jQuery = JoomlaShine.jQuery;
}