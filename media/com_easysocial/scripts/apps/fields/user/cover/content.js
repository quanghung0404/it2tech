EasySocial.module('apps/fields/user/cover/content', function($) {
    var module = this;

    EasySocial
        .require()
        .library('image')
        .language('PLG_FIELDS_COVER_VALIDATION_REQUIRED')
        .done(function() {
            EasySocial.Controller('Field.Cover', {
                defaultOptions: {
                    id              : 0,
                    group           : null,
                    required        : false,
                    hasCover        : true,
                    defaultCover    : null,

                    ratio           : 3,

                    '{image}'       : '[data-field-cover-image]',

                    '{data}'        : '[data-field-cover-data]',
                    '{position}'    : '[data-field-cover-position]',
                    '{file}'        : '[data-field-cover-file]',

                    '{note}'        : '[data-field-cover-note]',

                    '{loader}'      : '[data-field-cover-loader]',
                    '{error}'       : '[data-field-cover-error]',

                    '{removeButton}': '[data-field-cover-remove-button]',

                    '{revertFrame}' : '[data-field-cover-revert]',
                    '{revertButton}': '[data-field-cover-revert-button]'
                }
            },
            function(self) {
                return {
                    init : function() {
                        self.setFrame();

                        self.setLayout();

                        self.origCover = self.options.hasCover ? $.uri(self.image().css('backgroundImage')).extract(0) : null;

                        self.origPosition = self.options.hasCover ? self.image().css('backgroundPosition') : null;
                    },

                    '{self} onShow': function() {
                        setTimeout(function() {
                            self.setLayout();
                        }, 1);
                    },

                    setFrame: function() {
                        var frameWidth = self.image().width(),
                            frameHeight = frameWidth / self.options.ratio;

                        self.image().css('height', frameHeight);
                    },

                    '{window} resize': $.debounce(function() {
                        self.setFrame();
                    }, 250),

                    imageLoaders: {},

                    '{file} change' : function( el , event ) {
                        if($.isEmpty(el.val())) {
                            return;
                        }

                        var label = el.val().replace(/\\/g, '/').replace(/.*\//, '');

                        el.parents('.input-group').find(':text').val(label);

                        self.loader().show();

                        self.error().hide();

                        self.image().hide();

                        self.note().hide();

                        self.file().hide();

                        EasySocial.ajax( 'fields/' + self.options.group + '/cover/upload' , {
                            id: self.options.id,
                            files   : el
                        }, {
                            type    : 'iframe'
                        }).done(function(result){

                            var resultString    = JSON.stringify(result);

                            // Set the result in a string format
                            self.data().val(resultString);

                            var positionString = JSON.stringify({
                                x: 0.5,
                                y: 0.5
                            });

                            // Set the position in string format defaulting to 50 50
                            self.position().val(positionString);

                            // Set the position to 50 50 by default
                            self.position().val()

                            var url = result.large.uri,
                                imageLoaders = self.imageLoaders,
                                imageLoader = (imageLoaders[url] || (imageLoaders[url] = $.Image.get(url))).done(function(image) {
                                    self.setLayout.image = image;

                                    self.file().show();

                                    self.image().show();

                                    self.note().show();

                                    self.loader().hide();

                                    self.revertFrame().hide();

                                    self.removeButton().show();

                                    self.setCover(result.large.uri);
                                });

                        }).fail(function(msg) {

                            self.loader().hide();

                            self.file().show();

                            self.error().show().html(msg);
                        });
                    },

                    setLayout: function() {
                        var cover = self.image(),
                            image = self.setLayout.image;

                        if(!image) {
                            var url = $.uri(cover.css('backgroundImage')).extract(0);

                            if(!url) return;

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

                        self.setFrame();
                    },

                    setCover: function(url, position) {
                        position = position || '50% 50%';

                        self.image()
                            .css({
                                backgroundImage: $.cssUrl(url),
                                backgroundPosition: position
                            });

                        self.setLayout();

                        self.image().addClass('cover-move');

                        self.note().show();
                    },

                    x: 0.5,
                    y: 0.5,

                    moveCover: function(dx, dy, image) {

                        if (!image) {
                            image = self.image();
                        }

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

                        var position = {
                            x: self.x,
                            y: self.y
                        }

                        self.position().val(JSON.stringify(position));
                    },

                    '{image} mousedown': function(selection, event) {
                        if (event.target === self.image()[0]) {
                            event.preventDefault();
                        }

                        // Initial cover position
                        var image = self.image(),
                            position = image.css("backgroundPosition").split(" ");
                            self.x = parseInt(position[0]) / 100;
                            self.y = parseInt(position[1]) / 100;

                        // Initial cursor position
                        var x = event.pageX,
                            y = event.pageY;

                        $(document)
                            .on("mousemove.movingCover mouseup.movingCover", function(event) {

                                self.moveCover(
                                    (x - (x = event.pageX)) * -1,
                                    (y - (y = event.pageY)) * -1,
                                    image
                                );
                            })
                            .on("mouseup.movingCover", function() {

                                $(document).off("mousemove.movingCover mouseup.movingCover");
                            });
                    },

                    '{removeButton} click': function(el) {
                        var data = self.data().val();

                        if($.isEmpty(data)) {
                            if(self.options.hasCover) {
                                self.setCover(self.options.defaultCover);

                                // Mark the data as delete

                                self.data().val('delete');

                                el.hide();

                                self.revertFrame().show();
                            }
                        } else {
                            // Backup the data first
                            self.origData = data;
                            self.origPosition = self.position().val();

                            self.data().val('');
                            self.position().val('');
                            self.file().val('');
                            self.file().parents('.input-group').find(':text').val('');

                            if(self.options.hasCover) {
                                self.setCover(self.origCover);
                            } else {
                                self.setCover(self.options.defaultCover);

                                // Mark the data as delete
                                self.data().val('delete');

                                el.hide();
                            }
                        }
                    },

                    '{revertButton} click': function() {
                        self.setCover(self.origCover, self.origPosition);

                        self.revertFrame().hide();

                        self.removeButton().show();

                        self.data().val('');

                        self.position().val('');

                        self.file().val('');
                        self.file().parents('.input-group').find(':text').val('');
                    },

                    '{self} onSubmit': function(el, ev, register) {
                        if(self.options.required && !self.options.hasCover && $.isEmpty(self.data().val()))
                        {
                            self.raiseError($.language('PLG_FIELDS_COVER_VALIDATION_REQUIRED'));
                            register.push(false);
                        }
                    },

                    raiseError: function(msg) {
                        self.trigger('error', [msg]);
                    }
                }
            });

            module.resolve();
        });
});
