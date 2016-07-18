
EasySocial.require()
.library( 'dialog' )
.done(function($)
{
	<?php if( FD::version()->getVersion() < 3 ){ ?>
		$('body').addClass( 'com_easysocial25' );
	<?php } ?>

	window.selectGroup 	= function( obj )
	{
		$( '[data-jfield-group-title]' ).val( obj.title );

		$( '[data-jfield-group-value]' ).val( obj.id + ':' + obj.alias );

		// Close the dialog when done
		EasySocial.dialog().close();
	}

	$( '[data-jfield-group]' ).on( 'click', function()
	{
		EasySocial.dialog(
		{
			content 	: EasySocial.ajax( 'admin/views/groups/browse' , { 'jscallback' : 'selectGroup' })
		});
	});

});
