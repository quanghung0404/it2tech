
EasySocial.require()
.library( 'dialog' )
.done(function($)
{
	<?php if( FD::version()->getVersion() < 3 ){ ?>
		$('body').addClass( 'com_easysocial25' );
	<?php } ?>

	window.selectProfile 	= function( obj )
	{
		$( '[data-jfield-profile-title]' ).val( obj.title );

		$( '[data-jfield-profile-value]' ).val( obj.id );

		// Close the dialog when done
		EasySocial.dialog().close();
	}

	$( '[data-jfield-profile]' ).on( 'click', function()
	{
		EasySocial.dialog(
		{
			content 	: EasySocial.ajax( 'admin/views/profiles/browse' ,
							{
								'dialogTitle'	: '<?php echo JText::_( 'COM_EASYSOCIAL_USERS_BROWSE_USERS_DIALOG_TITLE' );?>',
								'jscallback'	: 'selectProfile'
							})
		});
	});

});
