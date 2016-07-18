
EasyBlog.require()
.script('dashboard/comments')
.done(function($)
{
	$('[data-eb-dashboard-comments]').implement(EasyBlog.Controller.Dashboard.Comments);

	$('.eb-head-popover').popover();
});