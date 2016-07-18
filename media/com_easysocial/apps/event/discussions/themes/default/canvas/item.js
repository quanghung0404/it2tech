EasySocial.require()
.script('apps/event/discussions', 'prism')
.done(function($) {

    $('[data-event-discussion-item]')
        .implement(EasySocial.Controller.Events.Item.Discussion);
});
