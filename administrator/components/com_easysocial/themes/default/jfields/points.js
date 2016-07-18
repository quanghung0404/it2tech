
EasySocial.require()
.library( 'dialog' )
.done(function($)
{
	<?php if( FD::version()->getVersion() < 3 ){ ?>
		$('body').addClass( 'com_easysocial25' );
	<?php } ?>

	window.selectPoints 	= function( obj )
	{
		$( '[data-jfield-points-title]' ).val( obj.title );

		$( '[data-jfield-points-value]' ).val( obj.alias );

		// Close the dialog when done
		EasySocial.dialog().close();
	}

	$( '[data-jfield-points]' ).on( 'click', function()
	{
		EasySocial.dialog(
		{
			content 	: EasySocial.ajax( 'admin/views/points/browse' , { 'jscallback' : 'selectPoints' })
		});
	});

});
