
EasyBlog.require()
.script('admin/grid')
.done(function($) {
	// Implement controller on the form
	$('[data-grid-eb]').implement(EasyBlog.Controller.Grid);

	$('[data-post-autopost]').on('click', function() {

		var button = $(this);
		var id = button.data('id');
		var type = button.data('type');

		EasyBlog.dialog({
			"content": EasyBlog.ajax('admin/views/blogs/confirmAutopost', {"type":type, "id" : id})
		});
	});

	<?php if ($browse) { ?>
	$('[data-post-title]').on('click', function(){
		var item = $(this).parents('[data-item]'),
			title = item.data('title'),
			id = item.data('id');

		parent.<?php echo $browseFunction;?>(id, title);
	});
	<?php } ?>

	$.Joomla("submitbutton", function(action) {

		if (action == 'blogs.create') {
			EasyBlog.ComposerLauncher.open('<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=composer&tmpl=component');
			return false;
		}

		// Get selected list items.
		var selected 	= new Array;

		$('[data-table-grid]').find('input[name=cid\\[\\]]:checked').each(function(i , el ){
			selected.push($(el).val());
		});

		if (action == 'blogs.move') {

			EasyBlog.dialog({
					"content": EasyBlog.ajax('admin/views/blogs/move'),
					"bindings":
					{
						"{submitButton} click": function()
						{
							$('[data-move-category]').val($('#move_category').val());

							$.Joomla('submitform', ['blogs.move']);
						}
					}
				});

			return false;
		}

		if (action == 'blogs.changeAuthor') {

			EasyBlog.dialog({
					"content": EasyBlog.ajax('admin/views/blogs/authors'),
					"bindings":
					{
						"{submitButton} click": function()
						{
							$('[data-move-author]').val($('#move_author').val());

							$.Joomla('submitform', ['blogs.changeAuthor']);
						}
					}
				});

			return false;
		}

		if( action == 'moveCategory' )
		{
			$( '#adminForm input[name=move_category_id]' ).val( $('#move_category' ).val() );
		}

		if ( action != 'remove' || confirm('<?php echo JText::_('COM_EASYBLOG_ARE_YOU_SURE_CONFIRM_DELETE', true); ?>')) {
			$.Joomla("submitform", [action]);
		}
	});

	$('[data-notify-item]').on('click', function() {
		var id = $(this).data('blog-id');

		EasyBlog.dialog({
			"content": EasyBlog.ajax('admin/views/blogs/confirmNotify', {"id" : id})
		});
	});

});
