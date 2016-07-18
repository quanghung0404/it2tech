EasySocial.require()
.script( 'admin/points/discover' )
.done(function($){

	// Implement discover controller.
	$( '[data-points-discover]' ).implement( EasySocial.Controller.Points.Discover );
	
});