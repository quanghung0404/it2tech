EasySocial.require().script('site/dashboard/dashboard.guest.login').done(function($) {
	$('[data-guest-register]').addController('EasySocial.Controller.Dashboard.Guest.Login');
});
