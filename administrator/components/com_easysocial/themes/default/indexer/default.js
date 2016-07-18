
EasySocial.require()
.script( 'admin/grid/grid' )
.done(function($)
{
	$( '[data-table-grid]' ).implement( EasySocial.Controller.Grid );

	$.Joomla( 'submitbutton' , function( action ) 
	{
		if( action == 'remove' || action == 'purge')
		{
			var title = ( action == 'remove' ) ? "<?php echo JText::_( 'COM_EASYSOCIAL_INDEXER_CONFIRM_DELETE_DIALOG_TITLE' );?>" : "<?php echo JText::_( 'COM_EASYSOCIAL_INDEXER_CONFIRM_PURGE_DIALOG_TITLE' );?>";
			var content = ( action == 'remove' ) ? "<?php echo JText::_( 'COM_EASYSOCIAL_INDEXER_CONFIRM_DELETE_INFO' );?>" : "<?php echo JText::_( 'COM_EASYSOCIAL_INDEXER_CONFIRM_PURGE_INFO' );?>";


			EasySocial.dialog(
			{
				title 	: title,
				content	: "<p>" + content + "</p>",
				width	: 400,
				height 	: 100,
				buttons	:
				[
					{
						name 	: "<?php echo JText::_( 'COM_EASYSOCIAL_CANCEL_BUTTON' );?>",
						classNames : "btn btn-sm btn-es",
						click 	: function()
						{
							EasySocial.dialog().close();
						}
					},
					{
						name 	: "<?php echo JText::_( 'COM_EASYSOCIAL_SUBMIT_BUTTON' );?>",
						classNames : "btn btn-sm btn-es-danger",
						click 	: function()
						{
							$.Joomla( 'submitform' , [ action ] );

							return false;
						}
					}
				]
			});

			return false;
		}

		$.Joomla( 'submitform' , [ action ] );
	});
});