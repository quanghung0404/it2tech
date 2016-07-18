
EasyBlog.require()
.script('dashboard/revisions')
.done(function($)
{
	$('[data-eb-dashboard-revisions]').implement(EasyBlog.Controller.Dashboard.Revisions);

	$('.eb-head-popover').popover();
});
