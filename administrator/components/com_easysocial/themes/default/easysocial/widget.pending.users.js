
EasySocial.require()
.script( 'admin/users/users' )
.done(function($)
{
	$( '[data-widget-pending-users]' ).implement( EasySocial.Controller.Users.Pending );
});