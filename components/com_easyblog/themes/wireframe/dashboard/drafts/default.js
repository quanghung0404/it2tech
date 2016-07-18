
EasyBlog.require()
.script('dashboard/posts')
.done(function($)
{
	$('[data-eb-dashboard-posts]').implement(EasyBlog.Controller.Dashboard.Posts);

	$('.eb-head-popover').popover();
});