EasySocial.module("photos/navigation", function($){


	$.fn.intersectsWith = function(top, left, width, height) {

		var offset = this.offset(),

			reference = {
				top   : offset.top,
				left  : offset.left,
				bottom: offset.top  + (sourceHeight = this.height()),
				right : offset.left + (sourceWidth  = this.width()),
				width : sourceWidth,
				height: sourceHeight
			},

			subject = {
				top   : top,
				left  : left,
				bottom: top  + (height || (height = 0)),
				right : left + (width  || (width  = 0)),
				width : width,
				height: height
			},

			intersects = (
				reference.left <= subject.right    &&
				subject.left   <= reference.right  &&
	          	reference.top  <= subject.bottom   &&
	          	subject.top    <= reference.bottom
			);

		return (intersects) ? {reference: reference, subject: subject} : false;
	};

	var module = this;

	var Controller =

		EasySocial.Controller("Photos.Navigation",
		{
			hostname: "navigation",

			defaultOptions: {
				"{navButton}" : ".es-photo-nav-button",
				"{nextButton}": "[data-photo-next-button]",
				"{prevButton}": "[data-photo-prev-button]"
			}
		},
		function(self, opts, base) { return {

			init: function() {

				self.setDirection(window.esPhotosNavigationLastDirection);
			},

			disabled: false,

			disable: function() {
				self.disabled = true;
				self.navButton().addClass("disabled");
			},

			enable: function() {
				self.disabled = false;
				self.navButton().removeClass("disabled");
			},

			setDirection: function(direction) {

				self.navButton().removeClass("active");

				window.esPhotosNavigationLastDirection = self.currentDirection = direction;

				if (direction) {
					// Show button
					self[direction + "Button"]().addClass("active");
				}
			},

			"{window} mousemove": function(el, event) {

				// If navigation is disabled, don't do anything.
				if (self.disabled) return;

				self.setDirection(null);
				self.photo.trigger("directionstop");

				// If user is not moving within the photo content, stop.
				if ($(event.target).parents().filter(self.photo.content.selector).length < 1) return;

				var offset =
						self.photo.content()
							.intersectsWith(event.pageY, event.pageX);

				if (offset) {

					var direction =
						(offset.subject.left < (offset.reference.right - (offset.reference.width / 2))) ?
							"prev" : "next";

					self.setDirection(direction);

					self.photo.trigger("directionmove", [offset, direction]);
				}
			},

			"{self} tagEnter": function() {
				self.disable();
			},

			"{self} tagLeave": function() {
				self.enable();
			},

			"{self} click": function(el, event) {

				if (self.disabled) return;

				// If user is not clicking within the photo content, stop.
				if ($(event.target).parents().filter(self.photo.content.selector).length < 1) return;

				var direction = self.currentDirection;

				if (!direction) return;

				self.trigger("photo" + $.String.capitalize(direction), [self.photo]);
			},

			"{self} photoNext": function() {
				// Photo browser handles this
			},

			"{self} photoPrev": function() {
				// Photo browser handles this
			}

		}});

	module.resolve(Controller);

});
