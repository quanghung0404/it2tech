EasySocial.module('site/friends/suggest', function($){

	var module 	= this;

	EasySocial.require()
	.view('site/friends/suggest.item')
	.library('textboxlist')
	.language('COM_EASYSOCIAL_FRIENDS_REQUEST_SENT')
	.done(function($) {


		EasySocial.Controller('Friends.Suggest.User', {
			defaultOptions: {
				"{addButton}": "[data-friend-suggest-add]",
				"{button}": "[data-friend-suggest-button]"
			}
		}, function(self, opts, base) { return {

			init: function() {
				opts.uid = self.element.data('uid');
			},

			"{addButton} click" : function(el) {

				// Implement controller on add friend.
				EasySocial.ajax('site/controllers/friends/request', {
					"id": opts.uid
				}).done(function(friendId) {
					// replace the button with done message.
					self.button().html($.language('COM_EASYSOCIAL_FRIENDS_REQUEST_SENT'));

				}).fail(function(obj) {

					EasySocial.dialog({
						width: 450,
						height: 180,
						content: obj.message
					});
				});
			}

			}
		});

		EasySocial.Controller('Friends.Suggest', {
			defaultOptions: {
				max: null,
				exclusive: true,
				exclusion: [],
				minLength: 1,
				highlight: true,
				name: "uid[]",
				type: "",

				// Search for friend list as well
				friendList: false,
				friendListName: "",

				includeSelf: false,
				showNonFriend: false,

				view: {
					suggestItem: "site/friends/suggest.item"
				}
			}
		}, function(self, opts, base) { return {

			init: function() {

				// Implement the textbox list on the implemented element.
				self.element
					.textboxlist({
						component: 'es',
						name: opts.name,
						max: opts.max,
						plugin: {
							autocomplete: {
								exclusive: opts.exclusive,
								minLength: opts.minLength,
								highlight: opts.highlight,
								showLoadingHint	: true,
								showEmptyHint	: true,

								query: function(keyword) {

									var suggestOptions = {
															"search": keyword,
															"type": opts.type,
															"showNonFriend": opts.showNonFriend
														};

									if (opts.includeSelf) {
										suggestOptions.includeme = true;
									}

									if (!opts.friendList) {
										return EasySocial.ajax('site/controllers/friends/suggest', suggestOptions);
									}

									return EasySocial.ajax('site/controllers/friends/suggestWithList', {
										"search": keyword,
										"inputName": opts.name,
										"friendListName": opts.friendListName,
										"showNonFriend": opts.showNonFriend
									});
								}
							}
						}
					})
					.textboxlist("enable");
			},

			"{self} filterItem": function(el, event, item) {
				
				// If this suggest searches for friend list, we don't want to format the item result here.
				if (opts.friendList) {
					return;
				}

				var html = self.view.suggestItem(true, {
							item: item,
							name: self.options.name
						});

				item.title = item.screenName;
				item.menuHtml = html;
				item.html = html;

				return item;
			},

			"{self} filterMenu": function(el, event, menu, menuItems, autocomplete, textboxlist) {
				
				// If this suggest searches for friend list, we don't want to format the item result here.
				if (opts.friendList) {
					return;
				}

				// Get list of excluded users
				var items = textboxlist.getAddedItems(),
					users = $.pluck(items, "id"),
					users = users.concat(self.options.exclusion);

				menuItems.each(function(){

					var menuItem = $(this),
						item = menuItem.data("item");

					// If this user is excluded, hide the menu item
					menuItem.toggleClass("hidden", $.inArray(item.id.toString(), users) > -1);
				});
			}

		}});

		module.resolve();
	});

});

