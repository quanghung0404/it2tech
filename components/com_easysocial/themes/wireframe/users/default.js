
EasySocial.require()
.script( 'site/users/users' )
.done(function($)
{
	$( '[data-users]' ).implement( EasySocial.Controller.Users );
});