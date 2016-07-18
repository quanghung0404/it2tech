EasySocial.module('cover', function($) {

var module = this;

EasySocial.require()
    .library("image")
    .done(function(){

        EasySocial.Controller('Cover', {
            defaultOptions: {
                uid                 : null,
                type                : null,
                "{image}"           : "[data-cover-image]",
                "{editButton}"      : "[data-cover-edit-button]",
                "{doneButton}"      : "[data-cover-done-button]",
                "{cancelButton}"    : "[data-cover-cancel-button]",
                "{uploadButton}"    : "[data-cover-upload-button]",
                "{selectButton}"    : "[data-cover-select-button]",
                "{removeButton}"    : "[data-cover-remove-button]",
                "{menu}"            : "[data-cover-menu]",

                // Hack to disable click that causes photo popup on the flyout
                "{flyout}"          : ".es-flyout-content"
            }
        },
        function(self, opts, base) { return {

            init: function() {

                // Automatically enable cover editing if not manually disabled
                // if (!self.options.disabled) { self.start("url"); }

                self.setLayout();

                if (self.element.hasClass("editing")) {
                    self.enable();
                }
            },

            "{editButton} click": function() {
                self.enable();

                // Mark a flag as repositioning only
                self.reposition = true;

                // After enabling, manually get the position from self.element and apply it
                self.image().css('backgroundPosition', self.element.css('backgroundPosition'));
            },

            "{cancelButton} click": function(el, ev) {
                self.disable();
            },

            reposition: false,

            ready: false,

            disabled: true,

            toggle: function() {
                self[(self.disabled) ? "enable" : "disable"]();
            },

            enable: function() {
                self.setLayout();
                self.disabled = false;
                self.element.addClass("editing");
                base.attr("data-es-photo-disabled", 1);
            },

            disable: function() {
                self.reposition = false;
                self.disabled = true;
                self.element.removeClass("editing");

                // Allow some time before enabling photo popup
                setTimeout(function(){
                    base.attr("data-es-photo-disabled", 0);
                }, 1);

                var profileUrl =
                    $.uri(window.location.href)
                        .deleteQueryParam("cover_id")
                        .toString();

                History.pushState({state: 0}, window.title, profileUrl);
            },

            imageLoaders: {},

            setLayout: function() {

                var cover = self.image(),
                    image = self.setLayout.image;

                if (!image) {

                    // Extract image url from cover
                    var url = $.uri(cover.css("backgroundImage")).extract(0);

                    // If no url given, stop.
                    if (!url) return;

                    // Load image
                    var imageLoaders = self.imageLoaders,
                        imageLoader =
                            (imageLoaders[url] || (imageLoaders[url] = $.Image.get(url)))
                                .done(function(image) {

                                    // Set it as current image
                                    self.setLayout.image = image;

                                    // Then set layout again
                                    self.setLayout();
                                });

                        return;
                }

                // Get measurements
                var imageWidth  = image.data("width"),
                    imageHeight = image.data("height"),
                    coverWidth  = cover.width(),
                    coverHeight = cover.height(),
                    size = $.Image.resizeProportionate(
                        imageWidth, imageHeight,
                        coverWidth, coverHeight,
                        "outer"
                    );

                self.availableWidth  = coverWidth  - size.width;
                self.availableHeight = coverHeight - size.height;
            },

            setCover: function(id, url) {

                // Show loading indicator
                self.element.addClass("loading");

                // Make sure the image has been properly loading
                $.Image.get(url)
                    .done(function(){

                        self.image()
                            .data("photoId", id)
                            .css({
                                backgroundImage: $.cssUrl(url),
                                backgroundPosition: "50% 50%"
                            });

                        // Reset position
                        self.x = 0.5;
                        self.y = 0.5;

                        self.enable();
                    })
                    .fail(function(){
                        self.disable();
                    })
                    .always(function(){

                        self.element.removeClass("loading");
                    });
            },

            drawing: false,

            moveCover: function(dx, dy, image) {

                // Optimization: Pass in reference to image
                // so we don't have to query all the time.
                if (!image) { image = self.image(); }

                var w = self.availableWidth,
                    h = self.availableHeight,
                    x = (w==0) ? 0 : self.x + ((dx / w) || 0),
                    y = (h==0) ? 0 : self.y + ((dy / h) || 0);

                // Always stay within 0 to 1.
                if (x < 0) x = 0; if (x > 1) x = 1;
                if (y < 0) y = 0; if (y > 1) y = 1;

                // Set position on cover
                image.css("backgroundPosition",
                    ((self.x = x) * 100) + "% " +
                    ((self.y = y) * 100) + "% "
                );
            },

            x: 0.5,

            y: 0.5,

            "{image} click": function(el, event) {
                if (!self.disabled) {
                    event.stopPropagation();
                }
            },

            "{image} mousedown": function(selection, event) {

                if (self.disabled || self.drawing) return;

                if (event.target === self.image()[0]) {
                    event.preventDefault();
                }

                self.drawing = true;
                base.addClass("active");

                // Initial cover position
                var image = self.image(),
                    position = self.image().css("backgroundPosition").split(" ");
                    self.x = parseInt(position[0]) / 100;
                    self.y = parseInt(position[1]) / 100;

                // Initial cursor position
                var x = event.pageX,
                    y = event.pageY;

                $(document)
                    .on("mousemove.movingCover mouseup.movingCover", function(event) {

                        if (!self.drawing) return;

                        self.moveCover(
                            (x - (x = event.pageX)) * -1,
                            (y - (y = event.pageY)) * -1,
                            image
                        );
                    })
                    .on("mouseup.movingCover", function(event) {
                        $(document).off("mousemove.movingCover mouseup.movingCover");

                        base.removeClass("active");

                        self.drawing = false;
                    });
            },

            "{image} touchstart": function(selection, event) {

                if (self.disabled || self.drawing) return;

                self.drawing = true;
                base.addClass("active");

                // Initial cover position
                var image = self.image(),
                    position = self.image().css("backgroundPosition").split(" ");
                    self.x = parseInt(position[0]) / 100;
                    self.y = parseInt(position[1]) / 100;

                // Initial touch position
                var touch = event.originalEvent.targetTouches[0],
                    x = event.pageX,
                    y = event.pageY;

                $(document)
                    .on("touchmove.movingCover", function(event) {

                        if (!self.drawing) return;

                        var touch = event.originalEvent.targetTouches[0];

                        self.moveCover(
                            (x - (x = touch.pageX)) * -1,
                            (y - (y = touch.pageY)) * -1,
                            image
                        );

                        event.preventDefault();
                    })
                    .on("touchend.movingCover", function(){

                        $(document).off("touchstart.movingCover touchend.movingCover");

                        base.removeClass("active");

                        self.drawing = false;
                    });
            },

            save: function() {

                var photoId = self.image().data("photoId");

                var task =
                    EasySocial.ajax(
                        "site/controllers/cover/create",
                        {
                            uid: self.options.uid,
                            type: self.options.type,
                            id: photoId,
                            x: self.x,
                            y: self.y,
                            reposition: self.reposition ? 1 : 0
                        }
                    )
                    .done(function(cover)
                    {
                        // Set cover
                        base
                            .attr("data-es-photo", photoId)
                            .css({
                                backgroundImage: $.cssUrl(cover.url),
                                backgroundPosition: cover.position
                            })
                            .removeClass("no-cover");

                        // Disable editing
                        self.disable();
                    });

                return task;
            },

            "{doneButton} click": function(el, event) {
                self.save();
            },

            "{menu} shown.bs.dropdown": function() {
                 self.element.addClass("show-all");
            },

            "{menu} hidden.bs.dropdown": function() {
                 self.element.removeClass("show-all");
            },

            "{selectButton} click": function() {

                base.attr('data-es-photo-disabled', 1);

                EasySocial.photos.selectPhoto(
                {
                    uid     : self.options.uid,
                    type    : self.options.type,
                    bindings:
                    {
                        "{self} photoSelected": function(el, event, photos) {

                            // Photo selection dialog returns an array,
                            // so just pick the first one.
                            var photo = photos[0];

                            // If no photo selected, stop.
                            if (!photo) return;

                            // Set it as cover to reposition
                            self.setCover(photo.id, photo.sizes.large);

                            this.parent.close();

                            base.attr('data-es-photo-disabled', 0);
                        },

                        "{cancelButton} click": function(el, event) {
                            this.parent.close();

                            base.attr('data-es-photo-disabled', 0);
                        }
                    }
                });
            },

            "{uploadButton} click": function() {
                base.attr('data-es-photo-disabled', 1);

                EasySocial.dialog({
                    content: EasySocial.ajax("site/views/cover/uploadDialog", { "uid" : self.options.uid , "type" : self.options.type }),
                    bindings: {
                        "{self} upload": function(el, event, task, filename) {

                            task.done(function(photo){
                                // Set cover
                                self.setCover(photo.id, photo.sizes.large.url);
                            });
                        },

                        "{cancelButton} click": function() {
                            this.parent.close();

                            base.attr('data-es-photo-disabled', 0);
                        }
                    }
                });
            },

            "{removeButton} click": function() {
                // We should check if there's anything to delete.

                EasySocial.ajax("site/controllers/cover/remove", { "uid" : self.options.uid , "type" : self.options.type })
                    .done(function(defaultCoverUrl)
                    {
                        base.css({
                                backgroundImage: $.cssUrl(defaultCoverUrl),
                                backgroundPosition: "50% 50%"
                            })
                            .addClass("no-cover")
                            .removeAttr("data-es-photo");

                        self.disable();
                    });
            },

            "{flyout} click": function(el, ev) {
                if (el.is($(ev.target))) {
                    ev.stopPropagation();
                }
            }
        }});

        module.resolve();

    });
});
