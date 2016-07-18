EasySocial.module('apps/fields/user/avatar/content', function($) {
    var module = this;

    EasySocial
        .require()
        .library('image', 'imgareaselect')
        .stylesheet('imgareaselect/default')
        .language('PLG_FIELDS_AVATAR_VALIDATION_EMPTY_PROFILE_PICTURE')
        .done(function(){

            EasySocial.Controller('Field.Avatar', {
                defaultOptions: {
                    required        : false,

                    id              : 0,

                    group           : null,

                    origSource      : null,

                    defaultAvatar   : null,

                    hasAvatar       : null,

                    '{field}'       : '[data-field-avatar]',

                    '{gallery}'     : '[data-field-avatar-gallery]',
                    '{galleryList}' : '[data-field-avatar-gallery-items]',
                    '{galleryItem}' : '[data-field-avatar-gallery-item]',

                    '{frame}'       : '[data-field-avatar-frame]',
                    '{viewport}'    : '[data-field-avatar-viewport]',

                    '{avatarSource}': '[data-field-avatar-source]',
                    '{avatarData}'  : '[data-field-avatar-data]',
                    '{avatarPath}'  : '[data-field-avatar-path]',
                    '{avatarType}'  : '[data-field-avatar-type]',
                    '{avatarName}'  : '[data-field-avatar-name]',

                    '{file}'        : '[data-field-avatar-file]',
                    '{image}'       : '[data-field-avatar-selected]',

                    '{note}'        : '[data-field-avatar-note]',

                    '{actions}'     : '[data-field-avatar-actions]',

                    '{cancel}'      : '[data-field-avatar-actions-cancel]',
                    '{crop}'        : '[data-field-avatar-actions-crop]',

                    '{removeFrame}' : '[data-field-avatar-remove]',
                    '{remove}'      : '[data-field-avatar-remove-button]',
                    '{revertFrame}' : '[data-field-avatar-revert]',
                    '{revert}'      : '[data-field-avatar-revert-button]',

                    '{loader}'      : '[data-field-avatar-loader]'
                }
            }, function(self) {
                return {
                    init: function() {
                        // Store the original photo first
                        // self.options.origSource = $.uri(self.frame().css('backgroundImage')).extract(0);

                        self.state = !!self.options.hasAvatar;
                    },

                    state: true,

                    "{file} change": function(el, event) {

                        if($.isEmpty(el.val())) {
                            return;
                        }

                        // Compatibility with input-group
                        var label = el.val().replace(/\\/g, '/').replace(/.*\//, '');
                        el.parents('.input-group').find(':text').val(label);

                        // Set state to false
                        self.state = false;

                        // Show the loader
                        self.loader().show();

                        // Hide the previous picture
                        self.frame().hide();

                        // Hide the file upload field
                        self.file().hide();

                        // Hide the error frame
                        self.clearError();

                        EasySocial.ajax('fields/' + self.options.group + '/avatar/upload', {
                            id: self.options.id,
                            files: el
                        }, {
                            type: "iframe"
                        })
                        .done(function(raw, uri, path) {

                            // Set the name of the image
                            self.avatarName().val(raw.name);

                            // Set the source of the image
                            self.avatarSource().val(uri);

                            // Set the path of the image
                            self.avatarPath().val(path);

                            // Set the type as upload
                            self.avatarType().val('upload');

                            // Load the imgareaselect for cropping
                            self.setLayout(uri);

                            // Unset all gallery item
                            self.galleryItem().removeClass('active');

                            // Hide the remove button
                            self.removeFrame().hide();

                            // Hide the revert button
                            self.revertFrame().hide();

                            // Set state to true
                            self.state = true;
                        })
                        .fail(function(msg) {
                            self.loader().hide();

                            self.raiseError(msg);

                            self.file().show().val('');

                            self.frame().show();
                        });
                    },

                    setLayout: function(img) {
                        var loader = $.Image.get(img),
                            frame = self.frame();

                        loader.done(function(el, image) {
                            frame.css('background-image', $.cssUrl(img));

                            frame.addClass('avatar-frame-crop');

                            frame.show();

                            self.loader().hide();

                            self.actions().show();

                            self.note().show();

                            self.viewport().imgAreaSelect({remove: true});

                            self.viewport().show();

                            var size = $.Image.resizeWithin(
                                    image.width,
                                    image.height,
                                    frame.width(),
                                    frame.height()
                                ),
                                min = Math.min(size.width, size.height),
                                x1  = Math.floor((size.width  - min) / 2),
                                y1  = Math.floor((size.height - min) / 2),
                                x2  = x1 + min,
                                y2  = y1 + min;

                            self.viewport()
                                .css(size)
                                // .css('position', 'absolute')
                                .imgAreaSelect({
                                    handles: true,
                                    aspectRatio: '1:1',
                                    parent: frame,
                                    x1: x1,
                                    y1: y1,
                                    x2: x2,
                                    y2: y2,
                                    onSelectEnd: function(viewport, selection) {
                                        var hasSelection = !(selection.width=="0" && selection.height=="0");
                                        if(hasSelection) {
                                            var string = JSON.stringify(self.data());

                                            self.avatarData().val(string);
                                        }
                                    }
                                })
                        });
                    },

                    '{cancel} click': function() {
                        self.actions().hide();

                        self.note().hide();

                        self.frame().hide();

                        self.file().show();

                        self.file().val('');

                        self.file().parents('.input-group').find(':text').val('');

                        self.avatarSource().val('');

                        self.avatarPath().val('');

                        self.avatarData().val('');

                        self.avatarType().val('');

                        self.viewport()
                            .hide()
                            .imgAreaSelect({remove: true});

                        if(!$.isEmpty(self.options.origSource)) {
                            self.frame()
                                .css('background-image', $.cssUrl(self.options.origSource))
                                .removeClass('avatar-frame-crop')
                                .show();
                        }

                        if(self.options.hasAvatar) {
                            self.removeFrame().show();
                        }
                    },

                    data: function() {
                        var viewport = self.viewport(),

                            width  = viewport.width(),

                            height = viewport.height(),

                            selection =
                                viewport
                                    .imgAreaSelect({instance: true})
                                    .getSelection(),

                            data = {
                                // id    : self.photoId().val(),
                                // uid   : self.userId().val(),
                                top   : selection.y1 / height,
                                left  : selection.x1 / width,
                                width : selection.width / width,
                                height: selection.height / height
                            };

                        return data;
                    },

                    '{gallery} click': function() {
                        self.galleryList().toggle();
                    },

                    '{galleryItem} click': function(el, event) {
                        // If this item is not previously selected then only we proceed
                        if(!el.hasClass('active')) {


                            // Get the id
                            var id = el.data('id');

                            // Remove all other item selected state
                            self.galleryItem().removeClass('active');

                            // Set this item as selected
                            el.addClass('active');

                            // Set state to false
                            self.state = false;

                            // Show the loader
                            self.loader().show();

                            // Hide the previous picture
                            self.frame().hide();

                            // Hide the file upload field
                            self.file().hide();

                            // Clear the file input
                            self.file().val('');
                            self.file().parents('.input-group').find(':text').val('');

                            // Hide the error frame
                            self.clearError();

                            // Set the type as gallery
                            self.avatarType().val('gallery');

                            // Set the source id
                            self.avatarSource().val(id);

                            // Get the avatar source
                            EasySocial.ajax('fields/user/avatar/loadDefault', {
                                "avatarId": id
                            }).done(function(uri) {

                                // Set the image preview
                                self.frame().css('background-image', 'url(' + uri + ')');

                                // Show the image
                                self.frame().show();

                                // Remove crop class
                                self.frame().removeClass('avatar-frame-crop');

                                // Hide the loader
                                self.loader().hide();

                                // Hide the viewport
                                self.viewport().hide();

                                // Remove the imgareaselect from viewport
                                self.viewport().imgAreaSelect({remove: true});

                                // Hide the actions
                                self.actions().hide();

                                // Hide the note
                                self.note().hide();

                                // Show the file upload field
                                self.file().show();

                                // Show the revert button
                                self.revertFrame().show();

                                // Hide the remove button
                                self.removeFrame().hide();

                                // Set state to true
                                self.state = true;
                            });
                        }
                    },

                    '{remove} click': function() {
                        self.avatarType().val('remove');

                        self.frame().css('background-image', $.cssUrl(self.options.defaultAvatar));

                        self.removeFrame().hide();

                        if(self.options.hasAvatar) {
                            self.revertFrame().show();
                        }

                        self.state = false;
                    },

                    '{revert} click': function() {
                        self.avatarType().val('');

                        self.frame().css('background-image', $.cssUrl(self.options.origSource));

                        if(self.options.hasAvatar) {
                            self.removeFrame().show();
                        }

                        self.revertFrame().hide();

                        self.galleryItem().removeClass('active');

                        self.state = true;
                    },

                    '{self} onSubmit': function(el, event, register) {
                        if(self.options.required) {
                            if(!self.state) {
                                self.raiseError($.language('PLG_FIELDS_AVATAR_VALIDATION_EMPTY_PROFILE_PICTURE'));
                            }

                            register.push(self.state);
                        }
                    },

                    raiseError: function(msg) {
                        self.trigger('error', [msg]);
                    },

                    clearError: function() {
                        self.trigger('clear');
                    }
                }
            });

            module.resolve();
    });
});
