
EasySocial.require()
.script( 'site/groups/item' )
.done(function($)
{
	$( '[data-group-discussions]' ).implement( EasySocial.Controller.Groups.Item.Discussions );

})