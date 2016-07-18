EasySocial.module("story/blog", function($){

    var module = this;

    EasySocial.require()
        .done(function(){

            EasySocial.Controller("Story.Blog",
                {
                    defaultOptions: {
                        "{title}" : "[data-blog-title]",
                        "{content}" : "[data-blog-content]",
                        "{category}": "[data-story-blog-category]"
                    }
                },
                function(self)
                {
                    return {

                    init: function()
                    {
                    },

                    "{story} save": function(element, event, save)
                    {
                        // Determines which profile we should broadcast to
                        var categoryId = self.category().val(),
                            title = self.title().val(),
                            content = self.content().val();


                        var data = {"categoryId" : categoryId, "title" : title, "content" : content};

                        save.addData(self, data);
                    }
                }}
            );

            // Resolve module
            module.resolve();

        });
});
