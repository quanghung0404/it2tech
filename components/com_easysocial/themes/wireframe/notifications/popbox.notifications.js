EasySocial.require().script('site/toolbar/system').done(function($) {
	$('[data-notificationsystem]').addController(EasySocial.Controller.Notifications.System.Popbox);
});
