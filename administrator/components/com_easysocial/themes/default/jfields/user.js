
EasySocial
.require()
.library('dialog')
.done(function($) {

	<?php if (ES::version()->getVersion() < 3) { ?>
		$('body').addClass( 'com_easysocial25' );
	<?php } ?>

	var titleField = $('[data-jfield-user-title]');
	var valueField = $('[data-jfield-user-value]');
	var browseButton = $('[data-jfield-user]');
	var cancelButton = $('[data-jfield-user-cancel]');

	window.selectUser = function(obj) {

		titleField.val(obj.title);
		valueField.val(obj.alias);

		// Close the dialog when done
		EasySocial.dialog().close();
	};

	cancelButton.on('click', function() {
		titleField.val('<?php echo JText::_('COM_EASYSOCIAL_JFIELD_SELECT_USER', true);?>');
		valueField.val('');
	});

	browseButton.on('click', function() {

		EasySocial.dialog({
			content: EasySocial.ajax('admin/views/users/browse', {
								'dialogTitle': '<?php echo JText::_( 'COM_EASYSOCIAL_USERS_BROWSE_USERS_DIALOG_TITLE' );?>',
								'jscallback' : 'selectUser'
					})
		});
	});

});
