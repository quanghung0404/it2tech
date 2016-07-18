
EasySocial.require()
.script('site/toolbar/friends','site/toolbar/conversations','site/toolbar/system')
.done(function($)
{
	var intervalSystem 	= $('[data-module-notifications-system]').data('interval'),
		intervalFriends = $('[data-module-notifications-friends]').data('interval'),
		intervalConversations = $('[data-module-notifications-conversations]').data('interval');
		
	$('[data-module-notifications-system]').implement(EasySocial.Controller.Notifications.System,
		{
			"interval"	: intervalSystem
		});

	$('[data-module-notifications-friends]').implement(EasySocial.Controller.Notifications.Friends,
		{
			"interval"	: intervalFriends
		});

	$('[data-module-notifications-conversations]').implement(EasySocial.Controller.Notifications.Conversations,
		{
			"interval"	: intervalConversations
		});

});
