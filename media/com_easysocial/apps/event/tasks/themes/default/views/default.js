EasySocial.require()
.script('apps/event/tasks')
.done(function($) {
    $('[data-tasks-milestones]').implement(EasySocial.Controller.Events.Apps.Tasks.Milestones.Browse)
});
