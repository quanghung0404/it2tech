
EasySocial
.require()
.script('site/groups/groups')
.done(function($){
    $('[data-es-groups]').implement(EasySocial.Controller.Groups.Browser);
});
