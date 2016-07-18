EasySocial.module('groups/suggest', function($) {
	var module = this;


	EasySocial.require()
	.view('site/groups/suggest.item')
	.library('textboxlist')
	.done(function($) {

		EasySocial.Controller('Groups.Suggest', {
			defaultOptions: {
				max: null,
				exclusive: true,
				exclusion: [],
				minLength: 1,
				highlight: true,
				name: "uid[]",
				type: "",

				view: {
					suggestItem: "site/groups/suggest.item"
				}
			}
		}, function(self) { return {
			init: function() {

				var options = self.options;

				// Implement the textboxlist on the current element.
				self.element
					.textboxlist({
						component: 'es',
						name: options.name,
						max: options.max,
						plugin: {
							autocomplete: {
								exclusive: options.exclusive,
								minLength: options.minLength,
								highlight: options.highlight,
								showLoadingHint: true,
								showEmptyHint: true,

								query: function(keyword) {

									// Run an ajax call to retrieve suggested groups
									var result = EasySocial.ajax('site/controllers/groups/suggest', {
													"search": keyword,
													"exclusion": options.exclusion
												});

									return result;
								}
							}
						}
					})
					.textboxlist("enable");
			},

			"{self} filterItem": function(el, event, item) {

				var html =
					self.view.suggestItem(true, {
						item: item,
						name: self.options.name
					});

				item.menuHtml = html;
				item.html     = html;

				return item;
			},

			"{self} filterMenu": function(el, event, menu, menuItems, autocomplete, textboxlist) {
				// If this suggest searches for friend list, we don't want to format the item result here.
				if( self.options.friendList )
				{
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