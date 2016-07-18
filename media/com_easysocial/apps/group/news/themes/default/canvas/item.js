
EasySocial.require()
.script( 'site/groups/item' )
.done(function($)
{
	$( '[data-group-news-item]' ).implement( EasySocial.Controller.Groups.Item.News );

})