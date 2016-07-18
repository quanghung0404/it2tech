
EasySocial.module("photos/avatar", function($){

	var module = this;

	EasySocial.require()
		.library(
			"imgareaselect"
		)
		.stylesheet(
			"imgareaselect/default"
		)
		.done(function(){

			EasySocial.Controller("Photos.Avatar",
			{
				defaultOptions:
				{
					view:
					{
						selection: "site/photos/avatar.selection"
					},

					uid 		: null,
					type 		: null,
					redirect	: true,
					redirectUrl	: "",
					"{image}"   : "[data-photo-image]",
					"{viewport}": "[data-photo-avatar-viewport]",
					"{photoId}" : "[data-photo-id]",
					"{userId}"  : "[data-user-id]",
					"{createButton}": "[data-create-button]",
					"{selection}"   : "[data-selection-box]",
					"{loadingIndicator}": "[data-photos-avatar-loading]"
				}
			},
			function(self) { return {

				init: function() {

					self.setLayout();
				},

				data: function()
				{

					var viewport = self.viewport(),

						width  = viewport.width(),

						height = viewport.height(),

						selection =
							viewport
								.imgAreaSelect({instance: true})
								.getSelection(),

						data = {
									id    	: self.photoId().val(),
									uid 	: self.options.uid,
									type 	: self.options.type,
									top   	: selection.y1 / height,
									left  	: selection.x1 / width,
									width 	: selection.width / width,
									height	: selection.height / height
								};

					return data;
				},

				imageLoaders: {},

				setLayout: function() {

					var imageHolder   = self.image(),
						// Using this instead of the other one above for urls that may have /*/ in it.
					    // imageUrl      = $.uri(imageHolder.css("backgroundImage")).extract(0),
					    imageUrl      = imageHolder.css("backgroundImage").replace(/^url\(['"]?/,'').replace(/['"]?\)$/,''),
					    imageLoaders  = self.imageLoaders,
					    imageLoader   = imageLoaders[imageUrl] || (self.imageLoaders[imageUrl] = $.Image.get(imageUrl));


					imageLoader
					    .done(function(imageEl, image){

							var size = $.Image.resizeWithin(
									image.width,
									image.height,
									imageHolder.width(),
									imageHolder.height()
								),
								min = Math.min(size.width, size.height),
								x1  = Math.floor((size.width  - min) / 2),
								y1  = Math.floor((size.height - min) / 2),
								x2  = x1 + min,
								y2  = y1 + min;

							self.createButton().enabled(true);

							self.viewport()
								.css(size)
								.imgAreaSelect({
									handles: true,
									aspectRatio: "1:1",
									parent: self.image(),
									x1: x1,
									y1: y1,
									x2: x2,
									y2: y2,
									onSelectEnd: function(viewport, selection) {
										var hasSelection = !(selection.width=="0" && selection.height=="0");
										self.createButton().enabled(hasSelection);
									}
								});
					    });
				},

				"{createButton} click": function( createButton )
				{
					var data = self.data(),

						task =
							EasySocial.ajax(
								"site/controllers/photos/createAvatar",
								data
								)
								.done(function( photo, user )
								{
									if (self.options.redirect)
									{
										window.location = self.options.redirectUrl;
									}
								})
								.fail(function(message, type)
								{
									self.setMessage(message, type);
								});

					self.trigger("avatarCreate", [task, data, self]);
				}

			}});

			module.resolve();

		});
});
