EasyBlog.module('posts/tools', function($) {

var module = this;

EasyBlog.Controller('Posts.Tools', {
    defaultOptions: {

        "{delete}": "[data-entry-delete]",
        "{publish}": "[data-entry-publish]",
        "{unpublish}": "[data-entry-unpublish]",
        "{feature}": "[data-entry-feature]",
        "{unfeature}": "[data-entry-unfeature]",
        "{unarchive}": "[data-entry-unarchive]",
        "{archive}": "[data-entry-archive]"
    }
}, function(self) { return {

    init: function() {
    },

    "{unarchive} click": function(button)
    {
        var item = self.parent.item.of(button);
        var id = item.data('id');
        var returnUrl = button.data('return');

        EasyBlog.dialog({
            content: EasyBlog.ajax('site/views/entry/confirmUnarchive', {
                "id": id,
                "return": returnUrl
            })
        });
    },

    "{archive} click": function(archiveButton)
    {
        var item = self.parent.item.of(archiveButton),
            id = item.data('id'),
            returnUrl = $(archiveButton).data('return');

        EasyBlog.dialog({
            content: EasyBlog.ajax('site/views/entry/confirmArchive', {
                "id": id,
                "return": returnUrl
            })
        });
    },

    "{delete} click": function(deleteButton)
    {
        var item = self.parent.item.of(deleteButton),
            itemId = item.data("id"),
            returnUrl = $(deleteButton).data('return');

        EasyBlog.dialog({
            content: EasyBlog.ajax('site/views/entry/confirmDelete', {"id" : itemId, "return": returnUrl})
        });
    },

    "{feature} click": function(featureButton)
    {
        var item = self.parent.item.of(featureButton),
            itemId = item.data("id"),
            returnUrl = $(featureButton).data('return');


        EasyBlog.dialog({
            content: EasyBlog.ajax('site/views/entry/featurePost', {"id": itemId, "return": returnUrl})
        });
    },

    "{unfeature} click": function(unfeatureButton) {
        var item = self.parent.item.of(unfeatureButton),
            id = item.data("id"),
            returnUrl = $(unfeatureButton).data('return');


        EasyBlog.dialog({
            content: EasyBlog.ajax('site/views/entry/unfeaturePost', {"id": id, "return": returnUrl})
        });
    },

    "{publish} click": function(button) {
        var item = self.parent.item.of(button);
        var id = item.data('id');
        var returnUrl = button.data('return');

        EasyBlog.dialog({
            content: EasyBlog.ajax('site/views/entry/confirmPublish', {"id" : id, "return": returnUrl})
        });
    },

    "{unpublish} click": function(button) {
        var item = self.parent.item.of(button);
        var id = item.data('id');
        var returnUrl = button.data('return');

        EasyBlog.dialog({
            content: EasyBlog.ajax('site/views/entry/confirmUnpublish', {"id" : id, "return": returnUrl})
        });
    }
}
});

module.resolve();

});