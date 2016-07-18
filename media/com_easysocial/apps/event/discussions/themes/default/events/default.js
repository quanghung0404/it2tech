EasySocial.require()
.script('apps/event/discussions')
.done(function($)
{
    $('[data-event-discussions]').implement(EasySocial.Controller.Events.Item.Discussions);

})
