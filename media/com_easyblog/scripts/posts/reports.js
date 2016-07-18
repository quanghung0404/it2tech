EasyBlog.module('posts/reports', function($) {

var module = this;

EasyBlog.Controller('Posts.Reports', {
    defaultOptions: {
        "{report}" : "[data-blog-report]"
    }
}, function(self) {
    return {
        init: function()
        {
        },

        "{report} click": function(el)
        {
            var item = self.parent.item.of(el),
                id = item.data('id');

            EasyBlog.dialog({
                content: EasyBlog.ajax('site/views/reports/form', {"id" : id, "type": "post"}),
                bindings: {

                }
            });
        }
    }
});

module.resolve();

});