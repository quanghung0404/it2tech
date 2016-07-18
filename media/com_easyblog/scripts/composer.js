EasyBlog.module("composer", function($){

var module = this;

// Document selectors
var EBD = window["EBD"] = {};
EBD.root                 = ".ebd",
EBD.block                = ".ebd-block" + ":not(is-helper)" + ":not(.is-placeholder)" + ":not(.is-dropzone)";
EBD.childBlock           = "> " + EBD.block;
EBD.nest                 = ".ebd-nest",
EBD.nestedBlock          = EBD.block + ".is-nested";
EBD.immediateNestedBlock = EBD.nestedBlock + ":not(" + EBD.nest + " " + EBD.nest + " " + EBD.nestedBlock + ")";
EBD.standaloneBlock      = EBD.block + ".is-standalone";
EBD.isolatedBlock        = EBD.block + ".is-isolated";

// Workarea Selectors
EBD.workarea = "[data-ebd-workarea]";
EBD.dropzone = "[data-ebd-dropzone]";
EBD.blockToolbar = "[data-ebd-block-toolbar]";
EBD.blockSortHandle = "[data-ebd-block-sort-handle]";
EBD.blockViewport = "[data-ebd-block-viewport]";
EBD.blockContent = "[data-ebd-block-content]";
EBD.immediateBlockSortHandle = "> " + EBD.blockToolbar + " > " + EBD.blockSortHandle;
EBD.immediateBlockViewport = "> " + EBD.blockViewport;
EBD.immediateBlockContent = EBD.immediateBlockViewport + "> " + EBD.blockContent;
EBD.editableContent = EBD.block + ".is-editable [contenteditable=true]";

EBD.selectors = {
    "{workarea}": EBD.workarea,
    "{root}"    : EBD.workarea + " " + EBD.root,
    "{nest}"    : EBD.workarea + " " + EBD.root + " " + EBD.nest,
    "{block}"   : EBD.workarea + " " + EBD.root + " " + EBD.block,
    "{dropzone}": EBD.workarea + " " + EBD.root + " " + EBD.dropzone

};

// Post states
var POST_BLANK       = 9,
    POST_DRAFT       = 3,
    POST_PENDING     = 4,
    POST_PUBLISHED   = 1,
    POST_SCHEDULED   = 2,
    POST_UNPUBLISHED = 0;

EasyBlog.require()
.library(
    "scrolly",
    "history"
)
.script(
    "composer/debugger",

    "composer/document",
    "composer/blocks",
    "composer/category",
    "composer/tags",
    "composer/revisions",

    // Sidebar
    "composer/media",
    "composer/posts",
    "composer/templates",

    // Artboard
    "composer/blogimage",
    "composer/location",

    // Panels
    "composer/panels",
    "composer/panels/post",
    "composer/panels/authorship",
    "composer/panels/category",
    "composer/panels/seo",
    "composer/panels/autopost"
)
.done(function(){

    EasyBlog.Controller("Composer", {
        hostname: "composer",
        pluginExtendsInstance: true,
        elements: [
            "[data-eb-composer-{frame|manager|actions|form|ghosts|alerts|saving-redirect-message|saving-message|saving-info-message|saving-progress-bar|saving-close-button|saving-entry-button|apply-post-button|publish-post-button|update-post-button|submit-post-button|reject-post-button|approve-post-button|preview-post-button|save-post-button|unpublish-post-button|delete-post-button|published-field}]",
            "[data-eb-composer-{views|view|viewport|viewport-content}]",
            "[data-eb-composer-{autosave|autosave-message}]",
            "[data-eb-{alert-template}]",
            "[data-eb-composer-toolbar-{messages}]",
            "[data-eb-composer-{close-message}]"
        ],

        defaultOptions: {

            templates: {},

            // Basic post attributes
            postUid: null,

            // Determines the current author id
            authorId: null,

            "{retryButton}": "[data-eb-composer-instance-entry-button]"
        }
    },
    function(self, opts, base, frame) { return {

        saveOptions: {
            autosave: false,
            showSaveMessage: true,
            updateRevisionStatus: true,
            updateAddressBar: true
        },

        init: function() {

            // Tell parent launcher that we're almost ready
            // so loading indicator can go away. Playing
            // tricks with user perceived performance.
            // Using try..catch because it is less work.
            try {
                window.parent.EasyBlog.ComposerLauncher.ready();
            } catch(e) {};

            // Prevent user from going to another page
            $(window).on('beforeunload', function(event) {
                event.preventDefault();
                return false;
            });

            // Detach the alert template
            opts.templates.alert = self.alertTemplate().detach().html();

            // Get frame
            frame = self.frame();

            // Get the author id
            opts.authorId = frame.data('author-id');

            // Disable scrollbar on body
            base.noscroll();

            // Prevent browser from remember last scroll position
            // self.desktop()[0].scrollTop = 0;

            // Expose Composer
            EasyBlog.Composer = self;

            // Extend options with options from inline data attributes
            $.extend(opts, frame.htmlData("eb-composer"));

            // Install plugins
            self.installPlugins([
                "debugger",
                "media",
                "templates",
                "blocks",
                "posts",
                "panels",
                "document",
                "blogimage",
                "location",
                "artboard",
                "tags",
                "category",
                "revisions"
            ]);

            // Misc

            // Start the keepAlive if it's currently not displaying post templates.
            if (opts.keepalive.enabled == 1 && !self.frame().hasClass('show-templates')) {
                self.keepalive.start();
            }

            // Start the auto save if it's currently not displaying post templates.
            if (opts.autosave.enabled == 1 && !self.frame().hasClass('show-templates')) {
                self.autosave.start();
            }

            // Debug when EasyBlog.debug is on.
            EasyBlog.debug && self.debugger.activate();

            // Trigger composerReady event
            self.trigger("composerReady");
        },

        "{self} composerDocumentReady": function() {

            self.frame()
                .removeClass("is-loading");
        },

        installPlugins: function(plugins) {

            $.each(plugins, function(i, plugin){
                self.addPlugin(plugin);
            });
        },

        settings: {

            get: function(key) {

                return base.find("input[name='" + key + "']").val();
            },

            set: function(key, val) {

                base.find("input[name='" + key + "']").val(val);

                self.trigger("composerSettingsChange", [key, val]);

                return val;
            }
        },

        getPostId: function() {
            return frame.attr("data-post-id");
        },

        getPostUid: function() {
            return frame.attr("data-post-uid");
        },

        getRevisionId: function() {
            return self.getPostUid().split(".")[1];
        },

        getDoctype: function() {
            return frame.attr("data-post-doctype");
        },

        //
        // Views
        //
        views: {

            show: function(name) {

                frame.switchClass("view-" + name);

                self.view()
                    .removeClass("active")
                    .where("name", name)
                    .addClass("active");

                // Monkey fix
                if (name=="revisions") {
                    self.manager().removeClass("has-messages");
                }

                self.trigger("composerViewShow", [name]);
            },

            hide: function(name) {

                self.trigger("composerViewHide", [name]);

                // Revert to document view
                self.views.show("document");
            },
        },

        //
        // Keep Alive
        //
        keepalive: {

            timer: null,

            start: function(interval) {

                var keepalive = self.keepalive;
                var interval = interval || opts.keepalive.interval;

                // Stop existing timer
                keepalive.stop();

                // If interval is 0, don't run keepalive.
                if (interval < 1) {
                    return;
                }

                // Start new timer
                // keepalive.timer = $.delay(function(){
                //     keepalive.run(interval);
                // }, interval);
                //
                keepalive.timer = setTimeout(function() { keepalive.run(interval); }, interval);
            },

            run: function(interval) {

                EasyBlog.ajax('site/views/composer/keepAlive')
                    .always(function() {
                        self.keepalive.start(interval);
                    });
            },

            stop: function() {
                clearTimeout(self.keepalive.timer);
            }
        },

        //
        // Autosave
        //
        autosave: {

            timer: null,
            counter: 0,

            start: function(interval) {

                var autosave = self.autosave;
                var interval = interval || opts.autosave.interval;

                // Stop existing timer
                autosave.stop();

                // If interval is 0, don't run autosave
                if (interval < 1 || !interval) {
                    return;
                }
                // Start new timer
                // autosave.timer = $.delay(function(){
                //     autosave.run(interval);
                // }, interval);

                autosave.timer = setTimeout(function() { autosave.run(interval); }, interval);
            },

            run: function(interval) {

                // Autosave
                self.autosave.save();

                // Restart the autosave checking
                self.autosave.start(interval);
            },

            stop: function() {
                clearTimeout(self.autosave.timer);
            },

            save: function() {

                if (self.saving) {
                    return;
                }

                // We need to set the state to draft if this is executed the first time
                if (self.autosave.counter == 0) {
                    self.publishedField().val(POST_DRAFT);
                }

                // Increment the counter
                self.autosave.counter++;

                self.save({
                    autosave: true,
                    showSaveMessage: false
                }).done(function(data, exception) {

                    // Only display message if the state is success
                    if (exception.code == 200) {
                        // We should be running the save differently otherwise it would obstruct the user experience
                        // if we imitate the save for later button.
                        self.autosave().removeClass('hide');

                        // Update the autosave message.
                        self.autosaveMessage().html(exception.message);
                    }

                });
            }
        },

        validate: function() {

            var validator = $.Task();

            // Trigger composerValidate event
            self.trigger("composerValidate", [validator]);

            validator.process()
                .done(function(){
                })
                .fail(function(){
                    var taskList = validator.list;
                    var exceptions = [];

                    $.each(taskList, function(i, task) {

                        if (task.state()=="rejected") {
                            task.fail(function(exception){
                                exceptions.push(exception);
                            });
                        }
                    });

                    self.setMessage(exceptions);
                });

            return validator;
        },

        "{self} composerValidate": function(el, event, validator) {

            // // Resolve the validator
            // validator.resolve();

            // return validator;
        },

        saving: false,

        getSaveData: function(saveData) {

            // Composer scans through every form element
            // with data-eb-composer-form attribute on it,
            // serializes the form into an object, and then
            // merge all the objects into save data.
            self.form().each(function(){
                var data = $(this).serializeObject();

                $.extend(saveData, data);
            });

        },

        save: function(options) {

            if (self.saving) {
                return;
            }

            // return;

            self.saving = true;

            var fakeAjax = $.Deferred();

            base.addClass("is-saving");

            // Add saving class on the manager
            // self.manager().addClass('is-saving');

            // Run validation first
            self.validate()
                .done(function(){

                    options = $.extend(self.saveOptions, options);

                    var save = $.Task();

                    if (options.autosave) {
                        save.data.autosave = options.autosave;
                    } else {
                        save.data.autosave = 0;
                    }

                    self.initSaving(options.autosave);
                    self.updateProgressBar('15');

                    if (options.isapply) {
                        save.data.isapply = options.isapply;
                    } else {
                        save.data.isapply = 0;
                        options.updateAddressBar = 0;
                    }

                    // Get the save data
                    self.getSaveData(save.data);

                    // Trigger composerSave event
                    // Any handler that is listening to this event
                    // should decorate the save data or create a
                    // save task if it needs more time to decorate
                    // save data, e.g. if an image is still uploading
                    // but user already clicked save.
                    self.trigger("composerSave", [save, self]);

                    self.updateProgressBar('25');

                    save.process()
                        .done(function(){

                            self.updateProgressBar('35');

                            EasyBlog.ajax("site/controllers/posts/save", save.data)
                                .done(function(data, exception, revisionHTML, editLink){

                                    self.updateProgressBar('65');

                                    // Set the message
                                    if (!options.isapply && options.showSaveMessage) {
                                        self.setMessage(exception);
                                    }

                                    // Trigger the success state to everyone
                                    self.trigger("composerSaveSuccess", data);

                                    // Update the revision html codes
                                    if (options.updateRevisionStatus) {

                                        var revisionsFieldset = base.find('[data-eb-revisions-fieldset]');

                                        revisionsFieldset.children().html(revisionHTML);

                                        composer.revisions.preventParentScrolling();
                                    }

                                    // Update the address bar url so that if the user refreshes the page, the contents stay intact
                                    if (options.isapply && options.updateAddressBar) {
                                        History.pushState({state:1}, "", editLink);
                                    }

                                    // Update the state of the document so that it shows "update instead"
                                    if (self.publishedField().val() == POST_PUBLISHED) {
                                        self.manager()
                                            .removeClass('revision-draft')
                                            .addClass('revision-finalized');
                                    }

                                    self.updateProgressBar('85');

                                    // Quick hack
                                    base.find(".eb-composer-actions input[name=uid]").val(data.uid);
                                    base.find(".eb-composer-actions input[name=revision_id]").val(data.revision_id);
                                    frame.attr("data-post-uid", data.uid);

                                    self.doneSaving(options.isapply, options.autosave, exception.message);

                                })
                                .fail(function(exception){

                                    self.manager().removeClass('is-saving is-auto-saving');

                                    self.setMessage(exception);
                                    self.trigger("composerSaveError", [exception]);
                                })
                                .always(function(){

                                    // Remove saving class on the manager
                                    // self.manager().removeClass('is-saving');

                                    self.saving = false;
                                })
                                .done(fakeAjax.resolve)
                                .fail(fakeAjax.reject);
                        })
                        .fail(function(){

                            self.manager().removeClass('is-saving is-auto-saving');

                            self.trigger("composerSaveError");
                            self.saving = false;
                        })
                        .always(function(){
                            base.removeClass("is-saving is-auto-saving");
                        })
                })
                .fail(function() {
                    self.saving = false;
                    base.removeClass("is-saving");
                });

            return fakeAjax;
        },

        trash: function() {

            EasyBlog.dialog({
                content: EasyBlog.ajax('site/views/composer/confirmDelete', {"uid" : self.getPostUid()})
            });
        },

        setMessage: function(exceptions) {

            // Normalize arguments
            // Also accept array of exceptions
            if (!$.isArray(exceptions)) {
                exceptions = [exceptions];
            }


            $.each(exceptions, function(i, exception){

                // Show the messages toolbar set
                self.manager()
                    .addClass('has-messages');

                // Get the toolbar
                var color = 'green';

                /error|danger/.test(exception.type) && (color = "red");
                /success/.test(exception.type) && (color = "green");

                self.messages()
                    .switchClass('style-' + color);

                self.messages()
                    .find('[data-message]')
                    .html(exception.message);
            });
        },

        "{closeMessage} click": function() {
            self.manager()
                .removeClass("has-messages");
        },

        "{previewPostButton} click": function() {

            var curPublishState = self.publishedField().val();

            if (curPublishState != POST_PENDING) {
                // We need to save the post first to ensure that their contents are up to date.
                self.publishedField()
                    .val(POST_DRAFT);
            }

            // console.log(self.publishedField().val());
            // return;

            self.save({autosave: 0,isapply: 1})
                .done(function(data, exception, revisionHTML, editLink, previewLink){
                    window.open(previewLink);
                })
                .always(function() {

                });
        },

        "{savePostButton} click": function(saveButton) {

            var curPublishState = self.publishedField().val();

            if (curPublishState != POST_PENDING) {
                // We need to set the state to "draft"
                self.publishedField().val(POST_DRAFT);
            }

            saveButton.addClass('is-saving');

            self.save({autosave: 1, isapply: 0})
                .done(function(){

                })
                .always(function(){
                    saveButton.removeClass('is-saving');
                });
        },

        "{submitPostButton} click": function() {

            self.disableLeavePrompt();

            self.publishedField().val(POST_PENDING);
            self.save({autosave: 0, isapply: 0});
        },

        "{approvePostButton} click": function(approveButton) {
            self.publishedField().val(POST_PUBLISHED);

            approveButton.addClass('is-saving');

            self.disableLeavePrompt();

            self.save({autosave: 0,isapply: 0})
                .done(function() {

                })
                .always(function() {
                    approveButton.removeClass('is-saving');
                })
        },

        "{rejectPostButton} click": function(rejectButton) {

            // rejecting this post and set the published back to draft so that user will have to edit the post again.
            self.publishedField().val(POST_DRAFT);

            self.disableLeavePrompt();

            self.save({autosave: 0,isapply: 0})
                .done(function() {

                });
        },

        "{publishPostButton} click": function(publishButton) {
            self.publishedField().val(POST_PUBLISHED);

            publishButton.addClass('is-saving');

            self.disableLeavePrompt();

            self.save({autosave: 0,isapply: 0})
                .done(function() {

                })
                .always(function() {
                    publishButton.removeClass('is-saving');
                });
        },

        "{applyPostButton} click": function(applyButton) {
            self.publishedField().val(POST_PUBLISHED);

            applyButton.addClass('is-saving');

            self.save({autosave: 0,isapply: 1})
                .done(function() {

                })
                .always(function() {
                    applyButton.removeClass('is-saving');
                });
        },

        "{updatePostButton} click": function(updateButton) {
            self.publishedField().val(POST_PUBLISHED);

            self.disableLeavePrompt();

            updateButton.addClass('is-saving');

            self.save({autosave: 0, isapply: 0})
                .done(function() {

                })
                .always(function() {
                    updateButton.removeClass('is-saving');
                })
        },

        "{unpublishPostButton} click": function() {
            self.publishedField().val(POST_UNPUBLISHED);

            self.save({autosave: 0, isapply: 0});
        },

        "{deletePostButton} click": function() {
            self.trash();
        },

        "{self} composerSelectTemplate": function(composer, event, templateId) {

            // Give a buffer of 5 seconds before starting autosave.
            setTimeout(function() {
                self.autosave.start();
            }, 5000);
        },

        "{savingEntryButton} click": function () {

            // unbind the window event so that it will not prompt user
            // to choose 'stay' or leave.
            self.disableLeavePrompt();


            // simulate the click event
            var url = self.savingEntryButton().attr('href');
            EasyBlog.ComposerLauncher.redirect(url);

        },

        "{savingCloseButton} click": function () {
            self.manager().removeClass("is-saving");
        },

        "initSaving": function(isAutoSave) {

            if (isAutoSave) {
                self.manager().addClass("is-auto-saving");

            } else {
                self.manager().removeClass("is-auto-saving");

                self.savingEntryButton().addClass('hide');
                self.savingCloseButton().addClass('hide');

                // remove progress bar
                self.savingProgressBar().removeClass('hide');

                //remove info message.
                self.savingInfoMessage().text('');
                self.savingInfoMessage().addClass('hide');

                self.savingMessage().removeClass('hide');
                self.manager().addClass("is-saving");
            }
        },

        "doneSaving": function(isapply, isautosave, message) {

            self.updateProgressBar('100');

            self.savingMessage().addClass('hide');

            self.savingInfoMessage().text(message);
            self.savingInfoMessage().removeClass('hide');

            if (isautosave) {
                self.manager().removeClass("is-auto-saving");
            } else {

                if (isapply) {

                    self.savingEntryButton().removeClass('hide');
                    self.savingCloseButton().removeClass('hide');

                    self.savingMessage().addClass('hide');
                } else {

                    self.disableLeavePrompt();

                    self.savingRedirectMessage().removeClass('hide');

                    // simulate the click event
                    var url = self.savingEntryButton().attr('href');
                    EasyBlog.ComposerLauncher.redirect(url);
                }

            }
        },

        disableLeavePrompt: function() {

            // unbind the window event so that it will not prompt user
            // to choose 'stay' or leave.
            $(window).off('beforeunload');

            // some Joomla editor has the saving prompt feature. lets try to disable it.
            $(window).unbind('beforeunload');

            // for tinymce - cheap hack
            window.onbeforeunload = function() {};
        },


        "updateProgressBar": function(percentage) {
            self.savingProgressBar().children('.progress-bar').css("width", percentage + "%")
        }

    }});

    $("body").addController("EasyBlog.Controller.Composer");

    module.resolve();
});

});
