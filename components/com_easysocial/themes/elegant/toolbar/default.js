
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

    // disable the dropdown from closing when user click on the checkbox of the filter types
    $('[data-nav-search-filter] .dropdown-menu input, [data-nav-search-filter] .dropdown-menu label').on('click', function (e) {
        e.stopPropagation();
    });

    $("[data-toolbar-logout-button]").on('click', function() {
        $('[data-toolbar-logout-form]').submit();
    });

    $('[data-elegant-toggle-search]').on('click', function() {
        $('[data-notifications]').toggleClass('show-search');
    });
});
