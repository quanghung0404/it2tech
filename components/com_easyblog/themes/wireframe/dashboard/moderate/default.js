
EasyBlog.require()
.script('dashboard/moderate')
.done(function($){
	$('[data-eb-dashboard-moderate]').implement(EasyBlog.Controller.Dashboard.Moderate);

	$('.eb-head-popover').popover();
});