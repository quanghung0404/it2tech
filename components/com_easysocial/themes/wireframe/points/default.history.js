
EasySocial.require()
.script( 'site/points/history' )
.done(function( $ )
{
	$( '[data-points-history]' ).implement( EasySocial.Controller.Points.History ,
	{
		"id"	: "<?php echo $user->id;?>"
	});
});