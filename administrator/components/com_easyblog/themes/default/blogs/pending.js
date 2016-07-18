
EasyBlog.ready(function($){

	$.Joomla("submitbutton", function(action)
	{
		if( action == 'approve' )
		{
			$.Joomla( 'submitform' , [ action ] );
		}

		if( action == 'reject' )
		{
			var selected = Array();

			$( 'input[name=cid\\[\\]]' ).each(function()
			{
				selected.push( $( this ).val() );
			})

			ejax.load( 'Pending' , 'confirmRejectBlog' , selected );
			
			return false;
		}

		if ( action != 'remove' || confirm('<?php echo JText::_('COM_EASYBLOG_ARE_YOU_SURE_CONFIRM_DELETE', true); ?>'))
		{
			$.Joomla("submitform", [action]);
		}

	});

	$('[data-blog-accept]').on('click', function()
	{
		var id 	= $(this).data('id');

		EasyBlog.dialog(
		{
			content	: EasyBlog.ajax('admin/views/blogs/confirmAccept', {"id" : id})
		});
	});

	$('[data-blog-reject]').on('click', function()
	{
		var id 	= $(this).data('id');

		EasyBlog.dialog(
		{
			content	: EasyBlog.ajax('admin/views/blogs/confirmReject', {"id" : id})
		});
	});	
});
