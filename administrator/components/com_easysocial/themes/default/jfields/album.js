
EasySocial
.require()
.library('dialog')
.done(function($) {

	<?php if (ES::version()->getVersion() < 3) { ?>
		$('body').addClass( 'com_easysocial25' );
	<?php } ?>

	var titleField = $('[data-jfield-album-title]');
	var valueField = $('[data-jfield-album-value]');
	var browseButton = $('[data-jfield-album]');
	var cancelButton = $('[data-jfield-album-cancel]');

	window.selectAlbum = function(obj) {
		titleField.val(obj.title);
		valueField.val(obj.alias);

		// Close the dialog when done
		EasySocial.dialog().close();
	}

	cancelButton.on('click', function() {
		titleField.val('<?php echo JText::_('COM_EASYSOCIAL_JFIELD_SELECT_ALBUM', true);?>');
		valueField.val('');
	});


	browseButton.on('click', function() {

		EasySocial.dialog({
			content: EasySocial.ajax('admin/views/albums/browse', {
								'jscallback' : 'selectAlbum'
					})
		});
	});
});
