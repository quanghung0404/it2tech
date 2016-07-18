EasySocial.require()
.script('admin/access/discover')
.done(function($){
	$('[data-access-discover]').implement(EasySocial.Controller.Access.Discover);
});
