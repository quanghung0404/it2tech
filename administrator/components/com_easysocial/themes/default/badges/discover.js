EasySocial.require()
.script( 'admin/badges/discover' )
.done(function($){

	// Implement discover controller.
	$( '[data-badges-discover]' ).implement( EasySocial.Controller.Badges.Discover );
	
});