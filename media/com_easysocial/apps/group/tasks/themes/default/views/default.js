
EasySocial.require()
.script( 'apps/group/tasks' )
.done(function($)
{
	$( '[data-group-tasks-milestones]' ).implement( EasySocial.Controller.Groups.Apps.Tasks.Milestones.Browse )
});
