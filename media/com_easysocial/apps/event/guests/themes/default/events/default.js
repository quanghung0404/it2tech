EasySocial.require()
.script('apps/event/guests')
.done(function($)
{
    $('[data-event-guests]').implement(EasySocial.Controller.Events.Item.Guests);
})
