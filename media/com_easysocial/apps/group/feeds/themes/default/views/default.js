
EasySocial.require()
.script('apps/group/feeds')
.done(function($) {

	$('[data-group-feeds]').implement(EasySocial.Controller.Groups.Apps.Feeds);

});
