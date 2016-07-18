// module: start
EasyBlog.module("dashboard/blogimage", function($) {

var module = this;

EasyBlog.require()
	.library("image")
	.done(function(){

		// controller: start
		EasyBlog.Controller("Dashboard.BlogImage",

			{
				defaultOptions: {

					// Containers
					"{placeHolder}"	: ".blogImagePlaceHolder",
					"{imageData}"	: ".imageData",
					"{image}"       : ".image",

					// Actions
					"{selectBlogImageButton}": ".selectBlogImage",
					"{removeBlogImageButton}": ".removeBlogImage"
				}
			},

			// Instance properties
			function(self) { return {

				init: function() {

					EasyBlog.dashboard.registerPlugin("blogImage", self);

					var meta = $.trim(self.imageData().val());

					if (meta) {
						self.setImage($.parseJSON(meta));
					}
 				},

				"{selectBlogImageButton} click": function() {

					// Optional: For the first param which is null now,
					// by passing in the key of the previously selected blog image,
					// it will activate the item on the browser.
					EasyBlog.mediaManager.browse(null, "blogimage");
				},

				"{removeBlogImageButton} click" : function(el) {

					self.removeImage();

					el.blur();
				},

				removeImage: function() {

					self.image().remove();

					self.element.addClass("empty");

					self.imageData().val("");
				},

				setImage: function(meta) {

					if (!meta) return;

					self.removeImage();

					self.element
						.addClass("loading");

					clearTimeout(self.imageTimer);

					// Clone the meta
					var meta = $.extend({}, meta);

					// Delete metadata
					delete meta.data;

					$.Image.get(meta.thumbnail.url)
						.done(function(image) {

							var resize = function(){

								// Keep a copy of the placeholder's width & height
								var placeHolder = self.placeHolder();
								maxWidth      = placeHolder.width();
								maxHeight     = placeHolder.height();

								// Calculate size
								var size = 	$.Image.resizeWithin(
									image.data("width"),
									image.data("height"),
									maxWidth,
									maxHeight
								);

								size.top  = (maxHeight - size.height) / 2;
								size.left = (maxWidth - size.width) / 2;

								return size;
							};

							var size = resize();

							var checkDimension = function() {

								if (size.width===0 || size.height===0) {

									self.imageTimer = setTimeout(function(){

										size = resize();

										checkDimension();

									}, 1000);

								} else {

									image.css(size);
								}
							}

							checkDimension();

							// Resize and insert
							image
								.addClass("image")
								.css(size)
								.appendTo(self.placeHolder());

							self.imageData().val(JSON.stringify(meta));

							self.element.removeClass("empty");
						})
						.fail(function(){

							self.element.addClass("empty");
						})
						.always(function(){

							self.element.removeClass("loading");
						});
				}
			}}

		);
		// controller: end

		module.resolve();
	});
	// require: end

});
// module: end
