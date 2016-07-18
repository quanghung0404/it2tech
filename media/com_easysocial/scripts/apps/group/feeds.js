EasySocial.module('apps/group/feeds', function($) {

    var module  = this;

    EasySocial.Controller('Groups.Apps.Feeds',
        {
            defaultOptions: {
                "{browser}": "[data-feeds-browser]",
                "{sources}": "[data-feeds-sources]",
                "{item}": "[data-feeds-list-item]",
                "{create}" : "[data-feeds-create]",
                "{list}": "[data-feeds-list]",
                "{remove}": "[data-feeds-remove]"
            }
        }, function(self) {

            return {

                init: function()
                {
                    self.options.id = self.element.data('groupid');
                    self.options.appId = self.element.data('appid');

                    self.list().implement(EasySocial.Controller.Groups.Apps.Feeds.List);
                },

                "{remove} click": function(el)
                {
                    var feedId = $(el).data('id'),
                        item = self.item.of(el);

                    EasySocial.dialog({
                        content: EasySocial.ajax('apps/group/feeds/controllers/feeds/confirmDelete', {"groupId" : self.options.id}),
                        bindings: {
                            "{deleteButton} click": function() {

                                EasySocial.ajax('apps/group/feeds/controllers/feeds/delete', {
                                    "appId": self.options.appId,
                                    "feedId": feedId,
                                    "groupId": self.options.id
                                })
                                .done(function(){

                                    // Remove the feed source
                                    item.remove();

                                    // Determine if there's no more item to be displayed
                                    if (self.sources().children().length == 0) {
                                        self.browser().addClass('is-empty');
                                    }

                                    EasySocial.dialog().close();
                                });
                            }
                        }
                    });
                },

                "{create} click": function()
                {
                    EasySocial.dialog({
                        content: EasySocial.ajax('apps/group/feeds/controllers/feeds/create', {"id" : self.options.id}),
                        bindings: {
                            "{saveButton} click": function() {


                                var title = this.title().val(),
                                    url = this.url().val();

                                var notice = $('[data-feeds-form-notice]');

                                // first remove all the alert styling.
                                notice.removeClass('alert alert-error');
                                notice.addClass('hide');


                                if (title.trim().length == 0) {
                                    notice.text( $.language('Please enter title.') );
                                    notice.addClass('alert alert-error');
                                    notice.removeClass('hide');
                                    return;
                                }

                                if (url.trim().length == 0) {
                                    notice.text( $.language('Please enter URL.') );
                                    notice.addClass('alert alert-error');
                                    notice.removeClass('hide');
                                    return;
                                }


                                EasySocial.ajax('apps/group/feeds/controllers/feeds/save', {
                                    "title": this.title().val(),
                                    "url": this.url().val(),
                                    "appId": self.options.appId,
                                    "groupId": self.options.id
                                })
                                .done(function(output){

                                    // Whenever a new feed item is created, it should never be empty.
                                    self.browser().removeClass('is-empty');

                                    // Append output to the list
                                    self.sources().append(output);

                                    EasySocial.dialog().close();
                                });
                            }
                        }
                    });
                }
            }
        });

    EasySocial.Controller('Groups.Apps.Feeds.List', {
        defaultOptions: {
            "{item}": "[data-feed-item]",
            "{openPreview}" : "[data-feed-open]",
            "{preview}": "[data-feed-preview]"
        }
    }, function(self) {
        return {
            init: function()
            {
            },

            "{openPreview} click": function(el)
            {
                var item = self.item.of(el),
                    preview = item.find(self.preview.selector);

                // If it's already open, hide it
                if (!preview.hasClass('hide')) {
                    preview.addClass('hide');
                } else {
                    // Hide all items first
                    self.preview().addClass('hide');

                    // Only display the clicked item
                    preview.removeClass('hide');
                }
            }
        }
    });
    module.resolve();
});

