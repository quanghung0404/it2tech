EasySocial.module("photos/tags", function($){

	var module = this;

	// Non essential dependencies
	EasySocial.require()
		.library("scrollTo")
		.done();

	EasySocial.require()
		.done(function(){

			var Controller =
			EasySocial.Controller("Photos.Tags",
			{
				hostname: "tags",

				defaultOptions: {

					"{viewport}"    : "[data-photo-tag-viewport]",
					"{tagItem}"     : "[data-photo-tag-item]",
					"{tagButton}"   : "[data-photo-tag-button]",
					"{tagLink}"     : "[data-photo-tag-link]",

					"{infoTagListItemGroup}": "[data-photoinfo-tag-list-item-group]",
					"{infoTagListItem}": "[data-photo-tags-user]",

					"{tagListItemGroup}": "[data-photo-tag-list-item-group]",
					"{tagListItem}"     : "[data-photo-tag-list-item]",

					"{descTagItem}": "[data-photo-tags-user]"
				}
			},
			function(self) { return {

				init: function() {
					self.setLayout();
				},

				imageLoaders: {},

				setLayout: function(callback) {

					var viewport     = self.photo.viewport(),
						image        = self.photo.image(),
						imageUrl     = image.attr("src"),
						imageLoaders = self.imageLoaders,
						imageLoader  = imageLoaders[imageUrl] || (self.imageLoaders[imageUrl] = $.Image.get(imageUrl));

					imageLoader
						.done(function(){

							var imageOffset = image.offset(),
								viewportOffset = viewport.offset();

							self.viewport()
								.css({
									top: imageOffset.top - viewportOffset.top,
									left: imageOffset.left - viewportOffset.left,
									width: image.width(),
									height: image.height()
								});

							callback && callback();
						});
				},

				"{window} resize": $.debounce(function(){

					self.setLayout();

				}, 1000),

				getTagItem: function(tagId) {
					return self.tagItem().filterBy("photoTagId", tagId);
				},

				getTagListItem: function(tagId) {
					return self.tagListItem().filterBy("photoTagId", tagId);
				},

				getInfoTagListItem: function(tagId) {
					return self.infoTagListItem().filterBy("photoTagId", tagId);
				},

				getTaggedUsers: function() {

					var users = [];

					self.tagListItem("[data-photo-tag-uid]")
						.each(function(){
							users.push($(this).data("photoTagUid"));
						});

					return $.uniq(users);
				},

				activateTag: function(tagId) {

					self.getTagItem(tagId)
						.addClass("active");

					self.getTagListItem(tagId)
						.addClass("active");
				},

				deactivateTag: function(tagId) {

					self.getTagItem(tagId)
						.removeClass("active");

					self.getTagListItem(tagId)
						.removeClass("active");
				},

				"{tagLink} click": function(el, event) {

					event.stopPropagation();
				},

				"{tagListItem} click": function(el) {

					var method = (el.hasClass('active') ? "deactivate" : "activate") + "Tag",
						tagId  = el.data("photoTagId");

					// Toggle tag
					self[method](tagId);
				},

				"{tagListItem} mouseover": function(el) {

					self.getTagItem(el.data("photoTagId"))
						.addClass("focus");
				},

				"{tagListItem} mouseout": function(el) {

					self.getTagItem(el.data("photoTagId"))
						.removeClass("focus");
				},

				"{descTagItem} mouseover": function(el) {
					self.getTagItem(el.data("photoTagId"))
						.addClass("focus");
				},

				"{descTagItem} mouseout": function(el) {
					self.getTagItem(el.data("photoTagId"))
						.removeClass("focus");
				},

				"{self} tagCreate": function(el, event, task) {

					task.done(function(){

					})
					.always(function(){
						setTimeout(function(){
							self.tagListItemGroup()
								.toggleClass("empty-tags", self.tagListItem().length < 1);
						}, 1);
					});
				},

				"{self} tagRemove": function(el, event, task, tagId) {

					task.done(function(){

						// Remove tag item
						self.getTagItem(tagId).remove();

						// Remove tag list item
						self.getTagListItem(tagId).remove();

						// Remove info's tag list item
						self.getInfoTagListItem(tagId).remove();

						// if length is zero, lets clear the html content.
						var taglen = self.infoTagListItemGroup().children().length;

						if (taglen == 0) {
							self.infoTagListItemGroup().html('');
						}

						if (taglen == 1) {
							var tag = self.infoTagListItemGroup().children().first();
							var tagcontent = tag.text();
							tagcontent = tagcontent.replace(',', '');

							tag.text(tagcontent);
						}

					})
					.always(function(){

						self.tagListItemGroup()
							.toggleClass("empty-tags", self.tagListItem().length < 1);
					});
				},

				"{self} photoRotate": function(el, event, task, angle, photo) {

					task.done(function(photoObj, tags){

						setTimeout(function(){

							self.setLayout(function(){

								var tagItems = self.tagItem();

								$.each(tags, function(i, tag){

									var tagItem = tagItems.filterBy("photoTagId", tag.id);

									tagItem
										.css({
											width : (tag.width  * 100) + "%",
											height: (tag.height * 100) + "%",
											top   : (tag.top    * 100) + "%",
											left  : (tag.left   * 100) + "%"
										});
								});

							});

						}, 1);

					});

				}
			}});

			module.resolve(Controller);

		});
});
