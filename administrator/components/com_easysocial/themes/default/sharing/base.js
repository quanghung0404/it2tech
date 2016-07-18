EasySocial.require().script('sharing').done(function($) {
	$("[data-sharing]").addController("EasySocial.Controller.Sharing");
});
