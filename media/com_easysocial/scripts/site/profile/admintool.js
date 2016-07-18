EasySocial.module( 'site/profile/admintool' , function($){

	var module 				= this;

	EasySocial.require()
	.done(function($){

		EasySocial.Controller('Profile.Admin', {
				defaultOptions: {
					"{deleteUser}": "[data-admintool-delete]",
					"{banUser}": "[data-admintool-ban]",
					"{unban}": "[data-admintool-unban]"
				}
			}, function(self) { return {

					init: function() {
					},

					"{unban} click": function(el) {
						var uid = el.data('id');

						EasySocial.dialog({
							content: EasySocial.ajax('site/views/profile/confirmUnban', {id: uid}),
							bindings: {
								"{unbanButton} click": function() {
									
									EasySocial.ajax('site/controllers/profile/unbanUser', {
										"id": uid
									}).done(function(html) {
										EasySocial.dialog({
											content: html
										});
									});

								},
								
								"{closeButton} click": function() {
									EasySocial.dialog().close();
								}
							}
						});
					},

					"{deleteUser} click" : function(el) {

						var uid = el.data('id');

						EasySocial.dialog({
							content: EasySocial.ajax( 'site/views/profile/confirmDeleteUser', {id: uid}),
							bindings: {
								"{deleteButton} click": function() {
									EasySocial.ajax('site/controllers/profile/deleteUser', {
										"id": uid
									})
									.done(function(html) {
										EasySocial.dialog({
											content: html
										});
									});
								},
								"{closeButton} click": function() {
									EasySocial.dialog().close();
								}
							}
						});
					},

					"{banUser} click" : function(el) {
						var uid = el.data('id');

						EasySocial.dialog({
							content: EasySocial.ajax( 'site/views/profile/confirmBanUser', {id: uid}),
							bindings: {
								
								"{banButton} click": function() {
									var period = $('[data-ban-period]').val();

									EasySocial.ajax( 'site/controllers/profile/banUser' ,
									{
										"id"	: uid,
										"period": period
									})
									.done( function(html)
									{
										EasySocial.dialog({
											content: html
										});

									});
								},
								"{closeButton} click": function() {
									EasySocial.dialog().close();
								}
							}
						});
					}
				}
		});

		module.resolve();
	});

});
