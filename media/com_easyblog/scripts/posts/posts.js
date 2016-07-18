EasyBlog.module('posts/posts', function($) {

    var module = this;

    EasyBlog.require()
    .script('posts/tools', 'posts/reports', 'ratings')
    .done(function($) {

        EasyBlog.Controller('Posts', {
            defaultOptions: {

                "{item}": "[data-blog-posts-item]",

                // Moderation tools
                "{approvePost}": "[data-blog-moderate-approve]",
                "{rejectPost}": "[data-blog-moderate-reject]",

                // Preview tools
                "{publishPost}": "[data-blog-preview-publish]",
                "{useRevision}": "[data-blog-preview-userevision]",

                // Ratings
                "{ratings}": "[data-rating-form]"
            }
        }, function(self) {
            return {
                init: function() {
                    self.tools = self.addPlugin('tools');
                    self.reports = self.addPlugin('reports');
                    self.id = self.item().data('id');
                    self.uid = self.item().data('uid');

                    self.initializeRatings();
                },

                initializeRatings: function() {
                    self.ratings().implement('EasyBlog.Controller.Ratings');
                },

                "{approvePost} click": function(el, event) {
                    EasyBlog.dialog({
                        content: EasyBlog.ajax('site/views/entry/confirmApprove', {"id": self.id})
                    });
                },

                "{rejectPost} click": function(el, event) {
                    EasyBlog.dialog({
                        content: EasyBlog.ajax('site/views/entry/confirmReject', {"id": self.id})
                    });
                },

                "{publishPost} click": function(el, event) {
                    
                    // Display a confirmation
                    EasyBlog.dialog({
                        content: EasyBlog.ajax('site/views/entry/confirmPublish', { "id": self.id })
                    });
                },

                "{useRevision} click": function(el, event) {
                    // Display a confirmation
                    EasyBlog.dialog({
                        content: EasyBlog.ajax('site/views/entry/confirmUseRevision', { "uid": self.uid })
                    });
                }
            }
        });

        module.resolve();
    });
});