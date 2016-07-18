EasySocial.require()
.script( 'admin/alerts/discover' )
.done(function($){

	// Implement discover controller.
	$( '[data-alerts-discovery]' ).implement( EasySocial.Controller.Alerts.Discover );
	
});