EasySocial.require()
.done(function($){

	// Set the task to the correct task.
	$( '.installDirectory' ).bind( 'click' , function()
	{
		$( '.installerForm input[name=task]' ).val( 'installFromDirectory' );
		$( '.installerForm' ).submit();
	});

	// Set the task to the correct task.
	$( '.installUpload' ).bind( 'click' , function()
	{
		$( '.installerForm input[name=task]' ).val( 'installFromUpload' );
		$( '.installerForm' ).submit();
	});

});
