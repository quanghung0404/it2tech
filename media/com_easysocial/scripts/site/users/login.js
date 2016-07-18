EasySocial.module('site/users/login', function($){

	var module = this;

	EasySocial.require()
		.library('dialog')
		.script('site/dashboard/dashboard.guest.login')
		.view('site/loading/small')
		.done(function(){

			EasySocial.login = function() {
				EasySocial.ajax('site/views/login/form')
					.done(function(content) {
						var dialog = EasySocial.dialog({
							content: content,
							afterShow: function() {
								this.element.find('[data-guest-login]').addController('EasySocial.Controller.Dashboard.Guest.Login');
							}
						});
					});
			}

			module.resolve();
		});
});
