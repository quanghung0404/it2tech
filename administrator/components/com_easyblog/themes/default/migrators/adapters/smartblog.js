EasyBlog.ready(function($) {

$('[data-migrate-smartblog]').on('click', function() {

		// Disable the button from being clicked twice
		$(this).attr('disabled', "true");

		// Update the buttons message
		$(this).html('<i class="fa fa-cog fa-spin"></i> <?php echo JText::_('COM_EASYBLOG_UPDATING', true);?>');

		// Hide the no progress message
		$('[data-progress-empty]').addClass('hide');

		// Ensure that the progress is always reset to empty just in case the user runs it twice.
		$('[data-progress-status]').html('');

		// clear the stats.
		$('[data-progress-stat]').html('');

		//show the loading icon
		$('[data-progress-loading]').removeClass('hide');

		// //process the migration
		window.migrateArticle();

	});

	window.migrateArticle = function() {

		// Get the values from the form
		var migrateComment = $( "#smartblog_comment" ).val(),
			migrateImage = $( "#smartblog_image" ).val(),
			imagepath = $('[data-image-path]').val();

		EasyBlog.ajax('admin/views/migrators/migrateArticle',
		{
			"component"	: "com_blog",
			"migrateComment"	: migrateComment,
			"migrateImage" : migrateImage,
			"imagepath": imagepath
		},
		{
			append: function(selector, message) {
				$(selector).append(message);
			}
		})
		.done(function(hasMore)
		{
			// If there's still items to render, run a recursive loop until it doesn't have any more items;
			if (hasMore == true) {
				window.migrateArticle();
				return;
			}

			//remove loading icon.
			$('[data-progress-loading]').addClass('hide');

			$('[data-migrate-smartblog]').removeAttr('disabled');
			$('[data-migrate-smartblog]').html('<i class="fa fa-check"></i> <?php echo JText::_('COM_EASYBLOG_COMPLETED', true);?>');

			if (hasMore == '0'){
				$('[data-migrate-smartblog]').removeAttr('disabled');
				$('[data-migrate-smartblog]').html('<?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW', true);?>');
				$('[data-progress-status]').html('<?php echo JText::_('COM_EASYBLOG_NO_ITEM', true);?>');
			}
			if (hasMore == 'notinstalled'){
				$('[data-migrate-smartblog]').removeAttr('disabled');
				$('[data-migrate-smartblog]').html('<?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW', true);?>');
				$('[data-progress-status]').html('<?php echo JText::_('COM_EASYBLOG_NOT_INSTALLED', true);?>');
			}
		});
	}
});
