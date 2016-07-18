EasyBlog.module('comments/comments', function($) {

    var module = this;

    EasyBlog.require()
    .library('markitup')
    .script('comments/form', 'comments/list')
    .done(function($) {

        EasyBlog.Controller('Comments', {
            defaultOptions: {
                "{item}" : "[data-comment-item]"
            }
        }, function(self) {
            return {
                init: function()
                {
                    self.commentForm = self.addPlugin('form');
                    self.commentList = self.addPlugin('list');
                }
            }
        });

        module.resolve();
    });
});