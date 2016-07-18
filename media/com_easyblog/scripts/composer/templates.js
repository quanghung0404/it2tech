EasyBlog.module("composer/templates", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Templates", {

        defaultOptions: {

            // Actions
            "{updateTemplate}": "[data-eb-composer-update-template-button]",
            "{templateId}": "[data-eb-composer-template-id]",
            "{saveNewTemplate}": "[data-eb-composer-save-template-button]",

            // Templates listings
            "{selectTemplate}": "[data-template-item]",
            "{templateBlocks}": "[data-template-blocks]",
            "{deleteTemplate}": "[data-template-delete]",

            "{posts}": "[data-eb-composer-posts]",
            "{searchTextfield}": "[data-eb-composer-posts-search-textfield]",
            "{itemGroup}": "[data-eb-composer-posts-item-group]",

            // List items
            "{list}": "[data-eb-composer-templates-list]"
        }
    },
    function(self, opts, base, composer) { return {

        init: function() {
            composer = self.composer;
        },

        //
        // When a template is selected, we need to update the manager accordingly.
        //
        selectedTemplate: function(templateId) {
            // If the user selected a proper template, we need to update the frame with the appropriate class
            composer.manager().addClass('is-editing-template');
            self.templateId().val(templateId);
        },

        "{selectTemplate} click": function(el) {

            // Get the template that the user clicked
            var uid = el.data('uid');
            var isBlank = el.data('blank');

            // If this is a blank template, don't do anything
            if (isBlank) {
                composer.frame().removeClass('show-templates');

                // Need to kick in auto save in blank template 
                composer.autosave.start();

                return;
            }

            EasyBlog.ajax('site/views/composer/renderTemplate', {
                "uid" : uid
            }).done(function(title, permalink, html) {

                // If the user selected a proper template, we need to update the frame with the appropriate class
                self.selectedTemplate(uid);

                // Need to kick in auto save in other template 
                composer.autosave.start();

                // Append the blocks html
                if (html) {
                    composer.document.loadDocument(html);
                }

                // Hide the templates browser
                composer.frame().removeClass('show-templates');

                // Trigger so that others can know what to do when template is selected
                self.trigger('composerSelectTemplate', [uid, title, permalink, html]);
            });
        },

        "{deleteTemplate} click": function(button, event) {
            // Prevent the template from being selected when the button is clicked
            event.stopPropagation();

            // Get the template id
            var parent = button.parents(self.selectTemplate.selector);
            var uid = parent.data('uid');

            // Display a dialog for confirmation
            EasyBlog.dialog({
                content: EasyBlog.ajax('site/views/templates/confirmDelete', {"id": uid, "deleteAction": "ajax"}),
                bindings: {
                    "{submitButton} click" : function() {
                        EasyBlog.ajax('site/controllers/templates/delete', {
                            "ids": [uid]
                        }).done(function() {
                            // Upon deleting the template, hide the dialog
                            EasyBlog.dialog().close();

                            // Remove the parent item
                            parent.remove();
                        });
                    }
                }
            });
        },

        save: function(options) {

            var save = $.Task();

            // Trigger the save
            composer.getSaveData(save.data);

            // Need to trigger composerSave because the document.js is relying on this trigger to decorate the save data
            self.trigger("composerSave", [save, composer]);

            // If there's a template id, we need to set it here
            if (options.template_id) {
                $.extend(save.data, {'template_id': options.template_id});
            }

            if (options.title) {
                $.extend(save.data, {'template_title' : options.title});
            }

            if (options.system) {
                $.extend(save.data, {'system': options.system});
            }

            save.process()
                .done(function(){

                    EasyBlog.ajax('site/views/templates/save', save.data)
                        .done(function(exception, template) {

                            // If the user selected a proper template, we need to update the frame with the appropriate class
                            self.selectedTemplate(template.id);

                            self.trigger("composerSave", [save, composer, template]);

                            composer.setMessage(exception);
                        });
                });
        },

        "{updateTemplate} click": function(el) {
            var id = self.templateId().val();

            self.save({
                'template_id': id
            });
        },

        "{saveNewTemplate} click": function() {

            EasyBlog.dialog({
                'content': EasyBlog.ajax('site/views/templates/saveTemplateDialog'),
                'bindings': {
                    "{submitButton} click": function() {
                        var systemTemplate = this.system().is(':checked') ? 1 : 0;

                        self.save({
                            'template_id': '0',
                            "title": this.title().val(),
                            "system": systemTemplate
                        });

                        EasyBlog.dialog().close();
                    }
                }
            });
        },

        "{composer} composerSidebarActivate": function(base, event, id) {

            if (id !== 'templates') {
                return;
            }

            EasyBlog.ajax('site/views/templates/listTemplates', {})
                .done(function(templates) {

                    if (templates.length == 0) {
                        self.posts().addClass('empty');
                        return;
                    }

                    // Insert the templates to the list
                    self.insertTemplates(templates);
                })
                .fail(function(){

                });
        },

        // Triggered when a save template occurs so that we can inject the item template into the templates list
        "{composer} composerSaveTemplate": function(el, event, save, composer, template) {
        },

        "{composer} sidebarDeactivate": function(base, event, id) {

            if (id!=="templates") {
                return;
            }
        }

    }});

    module.resolve();

});
