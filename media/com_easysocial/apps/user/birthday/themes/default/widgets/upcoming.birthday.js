

EasySocial.require()
.script( 'site/friends/friends' )
.done( function($){

	// Implement main stream controller.
	$( '[data-upcoming-birthday-list]' ).implement(
			"EasySocial.Controller.Friends.Birthday");
});
