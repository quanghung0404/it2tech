
EasySocial.require()
.script('site/groups/item')
.done(function($) {
    $('[data-group-members]').implement(EasySocial.Controller.Groups.Item.Members);
})