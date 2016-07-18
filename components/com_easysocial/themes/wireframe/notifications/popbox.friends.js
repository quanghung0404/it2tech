
EasySocial.require()
.script( 'site/toolbar/friends' )
.done(function($)
{
	// Initialize friends controller.
	$( '[data-popbox-friends-item]' ).addController( EasySocial.Controller.Notifications.Friends.Item ,
	{
	});

});