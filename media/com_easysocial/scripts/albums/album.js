EasySocial.module("albums/album", function($){

	var module = this;

	EasySocial.require()
		.library(
			"tinyscrollbar"
		)
		.done(function(){

			EasySocial.Controller("Albums.Album",
			{
				defaultOptions: {
					"{title}"        : "[data-album-title]",
					"{count}"        : "[data-album-count]",
					"{privacy}"      : "[data-album-privacy]",
					"{cover}"        : "[data-album-cover]",
					"{coverImage}"   : "[data-album-cover-image]",
					"{link}"         : "[data-album-link]",
					"{menu}"         : "[data-album-menu]",
					"{menuActions}"  : "[data-album-menu-actions]",
					"{shareButton}"  : "[data-album-share-button]",
					"{shareContent}" : "[data-sharing]",
					"{followButton}" : "[data-album-follow-button]",
					"{reportButton}" : "[data-album-report-button]",
					"{deleteButton}" : "[data-album-delete-button]",

					"{likeButton}"        : "[data-album-like-button]",
					"{commentButton}"     : "[data-album-comment-button]",

					"{countsButton}"      : "[data-album-counts-button]",
					"{commentCount}"      : "[data-album-comment-count]",
					"{likeCount}"         : "[data-album-like-count]",

					"{actions}"           : "[data-item-actions]",
					"{actionContent}"     : "[data-item-action-content]",
					"{actionCloseButton}" : "[data-item-action-close-button]",
					"{actionsMenu}"       : "[data-item-actions-menu]",

					"{likesHolder}"       : "[data-album-likes-holder]",
					"{commentsHolder}"    : "[data-album-comments-holder]",
					"{responseHolder}"    : "[data-album-response-holder]",

					"{comments}": "[data-comments]"
				}
			},
			function(self) { return {

				init: function()
				{
					self.id = self.element.data("album-id");

					self.actionContent()
						.tinyscrollbar();

					if (self.actions().hasClass("open")) {
						self.loadResponse();
						self.element.addClass("show-all");
					}
				},

				remove: function()
				{
					self.element.remove();
				},

				"{coverImage} click": function() {

					window.location = self.link().attr("href");
				},

				"{shareButton} click": function()
				{
					self.shareContent().show();
				},

				"{deleteButton} click": function()
				{
					EasySocial.dialog(
					{
						content: EasySocial.ajax( "site/views/albums/confirmDelete", { id: self.id })
					});
				},

				like: function() {

					EasySocial.ajax(
						"site/controllers/albums/like",
						{
							id: self.id
						}
					)
					.done(function(like) {

						// TODO: Update like count
						self.likeCount().html( like.count );

						// TODO: Change like text
						if( like.state )
						{
							self.likeButton().addClass( "liked" );
						}
						else
						{
							self.likeButton().removeClass("liked");
						}

						// TODO: Update like summary
						self.likesHolder().html( like.html );

						// To determine whether or not to like or unlike
						// self.likeButton().hasClass("liked")
					});
				},

				loadResponse: function() {

					var loader = self.loadResponse.loader;

					if (!loader || loader.state()=="rejected") {

						self.loadResponse.loader =
							EasySocial.ajax(
								"site/views/albums/response",
								{
									id: self.id
								}
							)
							.done(function(html) {

								self.responseHolder().html(html);

								self.actionContent()
									.removeClass("loading")
									.tinyscrollbar_update();
							});
					}
				},

				getButton: function(toggle) {

					var toggle = $(toggle),
						countsButton = self.countsButton(),
						commentButton = self.commentButton();

						if (toggle.is(countsButton) ||
							toggle.parents().filter(countsButton).length > 0) {
							return countsButton;
						}

						if (toggle.is(commentButton) ||
							toggle.parents().filter(commentButton).length > 0)
							return commentButton;

						return $();
				},

				lastButton: $(),

				"{actions} shown.bs.dropdown": function(actions, event, toggle) {

					// Show likes & comments
					self.loadResponse();

					// Make dropdown persistent even when hovered away
					self.element
						.addClass("show-all");

					var actionContent = self.actionContent(),
						button = self.lastButton = self.getButton(toggle),
						offset = (button.position().left + (button.width() / 2)) - (actionContent.width() / 2);

						actionContent
							.css("margin-left", offset)
							.tinyscrollbar_update();
				},

				"{actions} hide.bs.dropdown": function(actions, event, toggle) {

					self.element.removeClass("show-all");

					var button = self.getButton(toggle),
						lastButton = self.lastButton;

					if (!button.is(lastButton)) {
						setTimeout(function(){button.trigger("click")}, 0);
					}
				},

				"{actionCloseButton} click": function(el) {

					self.hideActionContent();
				},

				"{likeButton} click": function() {
					self.like();
				},

				"{comments} newCommentSaved": function() {

					var stat = self.comments().controller("EasySocial.Controller.Comments.Stat");
					self.commentCount().html(stat.total());

					self.actionContent()
						.tinyscrollbar_update("bottom");
				},

				"{comments} commentDeleted": function() {

					var stat = self.comments().controller("EasySocial.Controller.Comments.Stat");
					self.commentCount().html(stat.total());

					self.actionContent()
						.tinyscrollbar_update();
				},

				"{actionsMenu} shown.bs.dropdown": function() {
					self.element.addClass("show-all");
				},

				"{actionsMenu} hidden.bs.dropdown": function() {
					self.element.removeClass("show-all");
				}

			}});

			module.resolve();

		});
});

