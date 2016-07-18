EasySocial.require()
.script( 'admin/privacy/discover' )
.done(function($){

	// Implement discover controller.
	$( '.privacyForm' ).implement( EasySocial.Controller.Privacy.Discover );

});
