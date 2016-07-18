
EasySocial
.require()
.script('site/toolbar/notifications', 'site/search/toolbar', 'site/layout/responsive')
.done(function($){

	// Implement controller on friend requests.
	$('[data-notifications]').implement(EasySocial.Controller.Notifications, {
		friendsInterval: <?php echo $this->config->get('notifications.friends.polling');?>,
		systemInterval: <?php echo $this->config->get('notifications.system.polling');?>,
        conversationsInterval: <?php echo $this->config->get('notifications.conversation.polling');?>,
        guest: <?php echo $this->my->guest ? 'true' : 'false';?>
	});

	$('[data-nav-search]').implement(EasySocial.Controller.Search.Toolbar);
});
