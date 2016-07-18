EasySocial.module( 'admin/sidebar/sidebar' , function($) {

	var module = this;

	EasySocial.require()
	.done(function($){

		EasySocial.Controller(
				'Sidebar.Sidebar', {
					defaultOptions: {
						intervalPendingUsers: 5000,
						"{usersBadge}": ".menu-user > a .badge",
						"{pendingUsersBadge}"	: ".menu-user .menu-ies-vcard > .badge"
					}
				}, function(self) {

					return {

						init: function() {
							// Check for pending users.
							self.checkPendingUsers();
						},

						monitorPendingUsers: function() {
							self.options.state	= setTimeout(self.checkPendingUsers, self.options.intervalPendingUsers);
						},

						checkPendingUsers: function() {

							// Stop monitoring so that there wont be double calls at once.
							self.stopMonitorPendingUsers();

							// Needs to run in a loop since we need to keep checking for new notification items.
							setTimeout( function(){

								EasySocial.ajax('admin/controllers/users/getTotalPending')
								.done(function(total) {

									if (total > 0) {
										self.usersBadge().html(total);
										self.pendingUsersBadge().html(total);
									} else {
										self.usersBadge().html('');
									}

									// Continue monitoring.
									self.monitorPendingUsers();
								});

							}, self.options.intervalPendingUsers );

						},

						stopMonitorPendingUsers: function() {
							clearTimeout(self.options.state);
						},
					}
				}
		);

		module.resolve();
	});

});
