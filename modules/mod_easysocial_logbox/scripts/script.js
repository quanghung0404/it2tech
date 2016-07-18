EasySocial.require().script('site/dashboard/dashboard.guest.login').done(function($) {
	$('[data-logbox-miniform]').addController('EasySocial.Controller.Dashboard.Guest.Login');
});
