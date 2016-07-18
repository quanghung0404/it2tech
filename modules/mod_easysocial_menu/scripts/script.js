
EasySocial.require()
.script('site/toolbar/friends','site/toolbar/conversations','site/toolbar/system')
.done(function($) {
	var intervalSystem = $('[data-module-esmenu-system]').data('interval'),
		intervalFriends = $('[data-module-esmenu-friends]').data('interval'),
		intervalConversations = $('[data-module-esmenu-conversations]').data('interval');
		
	$('[data-module-esmenu-system]').implement(EasySocial.Controller.Notifications.System, {
		"interval": intervalSystem
	});

	$('[data-module-esmenu-friends]').implement(EasySocial.Controller.Notifications.Friends, {
		"interval": intervalFriends
	});

	$('[data-module-esmenu-conversations]').implement(EasySocial.Controller.Notifications.Conversations, {
		"interval": intervalConversations
	});

});
