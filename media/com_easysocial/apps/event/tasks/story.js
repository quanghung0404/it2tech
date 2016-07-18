EasySocial.require()
    .script('story/tasks')
    .done(function($)
    {
        var plugin = story.addPlugin("tasks");
    });
