EasySocial.module("story/friends", function($){

var module = this;

EasySocial.require()
.library("textboxlist")
.language(
	"COM_EASYSOCIAL_STREAM_STORY_WITH",
	"COM_EASYSOCIAL_STREAM_STORY_WITH_JOINER",
	"COM_EASYSOCIAL_STREAM_STORY_WITH_LAST_JOINER",
	"COM_EASYSOCIAL_AND"
)
.done(function(){

	EasySocial.Controller("Story.Friends",
	{
		defaultOptions: {
			"{friendList}": ".es-story-friends-textbox",
			"{textField}": ".es-story-friends-textbox [data-textboxlist-textfield]"
		}
	},
	function(self){ return {

		init: function() {

			self.textField().placeholder();

			// Friend tagging
			self.friendList()
				.textboxlist({
					component: 'es',
					plugin: {
						autocomplete: {
							exclusive : true,
							cache     : false,
							query     : self.search,
							filterItem: self.createMenuItem,

							component: "es",
							modifier: "es-story-friends-autocomplete",
							sticky: true,
							shadow: true
						}
					}
				});
		},

		search: function(keyword) {

			var users = self.getTaggedUsers();

			return EasySocial.ajax(
					   "site/controllers/friends/suggest",
					   {
					   	   "search": keyword,
					   	   "exclude": users
					   });
		},

		getTaggedUsers: function() {

			var users = [];
			var items = $( "[data-textboxlist-item]" );
			if( items.length > 0 )
			{
				$.each( items, function( idx, element ) {
					users.push( $( element ).data('id') );
				});
			}

			return users;
		},

		//
		// Tagging
		//
		createMenuItem: function(item, keyword) {

			item.title = item.screenName;

			var avatar = $(new Image())
				.addClass("textboxlist-menu-avatar")
				.attr({
					src: item.avatar
				}).toHTML();

			item.html     = avatar + ' ' + item.title;
			item.menuHtml = avatar + ' ' + item.title;

			return item;
		},

		//
		// Mentions
		//
		mention: function(mode, query, callback) {

			self.search(query)
				.done(function(users){

					var friends = [];

					$.each(users, function(i, user)
					{
						friends.push(
						{
							id: user.id,
							name: user.screenName,
							avatar: user.avatar,
							type: 'contact'
						});
					});

					callback(friends);
				});
		},

		//
		// Meta
		//
		updateMeta: function() {

			var friendList = self.friendList().controller("textboxlist"),
				friends = friendList.getAddedItems();

			if (friends.length < 1) {
				self.story.setMeta("friends", "");
				return;
			}

			var last = friends.length - 1,
				caption = $.language("COM_EASYSOCIAL_STREAM_STORY_WITH") + " ";

			$.each(friends, function(i, friend){

				// TODO: Get user permalink
				caption += '<a href="' + friend.permalink + '" target="_blank">' + friend.screenName + "</a>";

				// TODO: This is not the right way to join language
				if (i!=last) {
					var joiner = $.language("COM_EASYSOCIAL_STREAM_STORY_WITH_JOINER");

					if (i==last-1) {
						joiner = $.language("COM_EASYSOCIAL_STREAM_STORY_WITH_LAST_JOINER");
					}

					caption += joiner;
				}
			});

			self.story.setMeta("friends", caption);
		},

		"{friendList} addItem": function() {
			self.updateMeta();
		},

		"{friendList} removeItem": function() {
			self.updateMeta();
		},

		"{story} activateMeta": function(el, event, meta) {

			if (meta.name==="friends") {
				setTimeout(function(){
					self.textField().focus();
				}, 1);
			}
		},

		"{story} save": function(el, event, save) {

			var friendList = self.friendList().controller("textboxlist");

			var tags = friendList.getAddedItems().map(function(friend){
				return friend.id;
			});

			save.data['friends_tags'] = tags;
		},

		"{story} clear": function() {

			var friendList = self.friendList().controller("textboxlist");
			friendList.clearItems();
		}

	}});

	// Resolve module
	module.resolve();

});

});
