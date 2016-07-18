EasyBlog.module("layout/image/gallery", function($){

var module = this;

// Gallery selectors
var galleryContainer_ = ".eb-gallery";
var galleryViewport_ = ".eb-gallery-viewport";
var galleryItem_ = ".eb-gallery-item";
var galleryStage_ = ".eb-gallery-stage";
var galleryNextButton_ = ".eb-gallery-next-button";
var galleryPrevButton_ = ".eb-gallery-prev-button";
var galleryButton_ = ".eb-gallery-button";
var galleryMenu_ = ".eb-gallery-menu";
var galleryMenuItem_ = ".eb-gallery-menu-item";

var self = EasyBlog.ImageGallery = {

	setLayout: function(galleryContainer) {

		// Get a list of items in the gallery
		var galleryItems = galleryContainer.find(galleryItem_);

		// Apply position to every gallery items
		galleryItems.each(function(i){
			var galleryItem = $(this);
			var left = 100 * i;
			galleryItem.css("left", left + "%");
		});

		// Determines if there's auto rotate
		var autoplay = galleryContainer.data('autoplay');

		if (autoplay) {
			this.autoplay.start(galleryContainer);
		}
	},

	autoplay: {
		start: function(galleryContainer) {
			var interval = galleryContainer.data('interval') * 1000;

			// Stop any existing autoplay first
			self.autoplay.stop(galleryContainer);

			var timerId = setTimeout(function() {

				self.next(galleryContainer);

				// Restart the autoplay again.
				self.autoplay.start(galleryContainer);
			}, interval);

			galleryContainer.data('timer', timerId);
		},

		stop: function(galleryContainer) {
			var timerId = galleryContainer.data('timer');

			clearTimeout(timerId);
		}
	},

	checkAutoplay: function(galleryContainer) {

		var interval = galleryContainer.data('interval') * 1000;

		// Clear the timer first
		this.stopMonitoringAutoplay(galleryContainer);

		setTimeout(function(){
			self.next(galleryContainer);

			self.startMonitoringAutoplay(galleryContainer);
		}, interval);
	},

	go: function(galleryContainer, index) {

		// If index exceeds max index, cycle back to 0.
		var maxIndex = self.getMenuItems(galleryContainer).length - 1;

		if (index < 0) index = maxIndex;
		if (index > maxIndex) index = 0;

		self.setActiveIndex(galleryContainer, index);

		var galleryViewport = galleryContainer.find(galleryViewport_);
		var left = 100 * -1 * index;
		galleryViewport.css("left", left + "%");
	},

	next: function(galleryContainer) {

		var activeIndex = self.getActiveIndex(galleryContainer);
		var nextIndex = activeIndex + 1;
		self.go(galleryContainer, nextIndex);
	},

	prev: function(galleryContainer) {

		var activeIndex = self.getActiveIndex(galleryContainer);
		var prevIndex = activeIndex - 1;
		self.go(galleryContainer, prevIndex);
	},

	setActiveIndex: function(galleryContainer, index) {

		var galleryMenuItems = self.getMenuItems(galleryContainer);
		galleryMenuItems
			.removeClass("active")
			.eq(index)
			.addClass("active");
	},

	getActiveIndex: function(galleryContainer) {
		var galleryMenuItems = self.getMenuItems(galleryContainer);
		var activeIndex = galleryMenuItems.filter(".active").index();
		if (activeIndex < 0) activeIndex = 0;
		return activeIndex;
	},

	getMenuItems: function(galleryContainer) {
		 return galleryContainer.find(galleryMenuItem_);
	}
};

$(document)
	.on("click.eb.gallery.button", galleryButton_, function(event){
		var galleryButton = $(this);
		var galleryContainer = galleryButton.closest(galleryContainer_);

		// If no gallery container found, stop.
		if (galleryContainer.length < 1) return;

		var direction = galleryButton.is(galleryNextButton_) ? "next" : "prev";

		self[direction](galleryContainer);
	})
	.on("click.eb.gallery.menuItem", galleryMenuItem_, function(event){

		var galleryMenuItem = $(this);
		var galleryContainer = galleryMenuItem.closest(galleryContainer_);

		// If no gallery container found, stop.
		if (galleryContainer.length < 1) return;

		// Get index from menu item
		var index =
			galleryContainer
				.find(galleryMenuItem_)
				.index(galleryMenuItem);

		// Go to gallery item
		self.go(galleryContainer, index);
	})
	.ready(function(event){

		$(galleryContainer_).each(function(){
			var galleryContainer = $(this);
			self.setLayout(galleryContainer);
		})
		// TODO: Autoplay on document ready?
	});

module.resolve();

});