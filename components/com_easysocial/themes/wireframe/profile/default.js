
EasySocial.require()
	.script("site/profile/profile")
	.done(function($){
		$('[data-profile]').addController("EasySocial.Controller.Profile");
	});
