
EasySocial.require()
.script( 'site/groups/item' , 'prism' )
.done(function($)
{
	$( '[data-group-discussion-item]' ).implement( EasySocial.Controller.Groups.Item.Discussion )
});