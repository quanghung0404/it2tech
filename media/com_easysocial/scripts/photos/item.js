EasySocial.module("photos/item", function($){

	var module = this;

	// Non-essential dependencies
	EasySocial.require()
		.script(
			"photos/tags",
			"photos/editor",
			"photos/tagger",
			"photos/navigation"
		)
		.done();

	// Essential dependencies
	EasySocial.require()
		.library(
			"image"
		)
		.done(function(){

			var Controller =
			EasySocial.Controller("Photos.Item",
			{
				hostname: "photo",

				defaultOptions: {

					editable: false,
					taggable: false,
					navigation: false,

					"{header}"            : "[data-photo-header]",
					"{content}"           : "[data-photo-content]",
					"{footer}"            : "[data-photo-footer]",
					"{viewport}"          : "[data-photo-viewport]",

					"{info}"              : "[data-photo-info]",
					"{title}"             : "[data-photo-title]",
					"{titleLink}"         : "[data-photo-title-link]",
					"{caption}"           : "[data-photo-caption]",

					"{image}"             : "[data-photo-image]",
					"{imageCss}"          : "[data-photo-image-css]",
					"{imageLink}"         : "[data-photo-image-link]",

					"{menu}"              : "[data-photo-menu]",
					"{actions}"           : "[data-item-actions]",
					"{actionsMenu}"       : "[data-item-actions-menu]",

					"{comments}"          : "[data-comments]",
					"{share}"			  : "[data-repost-action]",
					"{likes}"			  : "[data-likes-action]",
					"{likeContent}" 	  : "[data-likes-content]",
					"{repostContent}" 	  : "[data-repost-content]",
					"{counterBar}"	  	  : "[data-stream-counter]",

					"{privacy}"           : "[data-es-privacy-container]",

					"{likeCount}"    : "[data-photo-like-count]",
					"{commentCount}" : "[data-photo-comment-count]",
					"{tagCount}"     : "[data-photo-tag-count]"
				}
			},
			function(self) { return {

				init: function() {

					self.id = self.element.data("photoId");

					// Also implement tags when it is available
					EasySocial.module("photos/tags")
						.done(function(TagsController){
							self.tags = self.addPlugin("tags", TagsController);
						});

					// If this photos is editable, load & implement editor.
					if (self.options.editable) {
						EasySocial.module("photos/editor")
							.done(function(EditorController){
								self.editor = self.addPlugin("editor", EditorController);
							});
					}

					if (self.options.taggable) {
						EasySocial.module("photos/tagger")
							.done(function(TaggerController){
								self.tagger = self.addPlugin("tagger", TaggerController);
							});
					}

					if (self.options.navigation) {
						EasySocial.module("photos/navigation")
							.done(function(NavigationController){
								self.navigation = self.addPlugin("navigation", NavigationController);
							});
					}
				},

				data: function() {

					var image = self.image();

					return {
						id        : self.id,
						title     : $.trim(self.title().text()),
						caption   : $.trim(self.caption().text()),
						sizes: {
							thumbnail: image.data("thumbnailSrc"),
							featured : image.data("featuredSrc"),
							large    : image.data("largeSrc")
						}
					}
				},

				setLayout: function(layoutName) {

					// Switch layout
					self.element
						.data("photoLayout", layoutName)
						.switchClass("layout-" + layoutName);

					// Trigger layout change event
					self.trigger("layoutChange", [layoutName, self]);
				},

				"{self} click": function(el, event) {

					// If using photo popup, stop.
					if (!el.data("esPhotoDisabled")) return;

					var target = $(event.target),
						menu = self.menu();

					// If the area being click is the photo menu, stop.
					if (target.parents().andSelf().filter(menu).length > 0) return;

					// Activate item
					self.trigger("activate", [self]);
				},

				"{self} photoSave": function(el, event, task) {

					task
						.done(function(photo, html){
							self.info().replaceWith(html);
						});
				},

				"{self} photoDelete": function(el, event, task) {

					task
						.done(function(){
						})
						.fail(function(message, type){
							self.setMessage(message, type);
						});
				},

				"{imageLink} click": function(imageLink, event) {

					event.preventDefault();
				},

				"{titleLink} click": function(titleLink, event) {

					// event.preventDefault();
				},

                "{self} shown.bs.dropdown": function() {
                     self.element.addClass("show-all");
                },

                "{self} hidden.bs.dropdown": function() {
                     self.element.removeClass("show-all");
                },

                "{share} create": function(el, event, itemHTML) {
                	self.counterBar().removeClass('hide');
                },

 				"{likes} onLiked": function(el, event, data) {

					//need to make the data-stream-counter visible
					self.counterBar().removeClass( 'hide' );
					self.count("like", 1, true);
				},

				"{likes} onUnliked": function(el, event, data) {

					var isLikeHide 		= self.likeContent().hasClass('hide');
					var isRepostHide 	= self.repostContent().hasClass('hide');

					if( isLikeHide && isRepostHide )
					{
						self.counterBar().addClass( 'hide' );
					}

					self.count("like", -1, true);
				},

				"{self} tagAdd": function() {
					self.count("tag", 1, true);
				},

				"{self} tagRemove": function() {
					self.count("tag", -1, true);
				},

				"{comments} newCommentSaved": function() {
					self.count("comment", 1, true);
				},

				"{comments} commentDeleted": function() {
					self.count("comment", -1, true);
				},

				"{privacy} activate": function() {
					setTimeout(function(){
						self.element.addClass("show-all")
					}, 0);
				},

				"{privacy} deactivate": function() {
					self.element.removeClass("show-all");
				},

				count: function(subject, val, append) {

					var statSelector = self[subject + "Count"];

					if (!$.isFunction(statSelector)) return;

					// Get stat element
					var stat = statSelector();

					// If no stat element found, stop.
					if (stat.length < 0) return;

					// Get current stat count
					var statCount;

					if (append) {
						statCount = (parseInt(stat.text()) || 0) + (parseInt(val) || 0);
					} else {
						statCount = val;
					}

					// Always stays at 0 if less than that
					if (statCount < 0) statCount = 0;

					// Update stat count
					stat.text(statCount);
				}

			}});

			module.resolve(Controller);

		});
});
