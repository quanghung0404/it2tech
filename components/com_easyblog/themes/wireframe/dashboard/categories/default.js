

EasyBlog.require()
.script('dashboard/categories')
.done(function($)
{
	$('[data-eb-dashboard-categories]').implement(EasyBlog.Controller.Dashboard.Categories);

	$('.eb-head-popover').popover();
});