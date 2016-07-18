
EasySocial.require()
.library( 'dialog' )
.done(function($)
{
	<?php if( FD::version()->getVersion() < 3 ){ ?>
		$('body').addClass( 'com_easysocial25' );
	<?php } ?>

	window.selectGroupCategory 	= function( obj )
	{
		$( '[data-jfield-groupcategory-title]' ).val( obj.title );

		$( '[data-jfield-groupcategory-value]' ).val( obj.id + ':' + obj.alias );

		// Close the dialog when done
		EasySocial.dialog().close();
	}

	$( '[data-jfield-groupcategory]' ).on( 'click', function()
	{
		EasySocial.dialog(
		{
			content 	: EasySocial.ajax( 'admin/views/groups/browseCategory' , { 'jscallback' : 'selectGroupCategory' })
		});
	});

});
