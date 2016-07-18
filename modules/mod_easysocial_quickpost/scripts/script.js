EasySocial
.require()
.script('story/quickpost')
.done(function($){
    $('[data-quickpost-module]').addController('EasySocial.Controller.Story.Quickpost');
});
