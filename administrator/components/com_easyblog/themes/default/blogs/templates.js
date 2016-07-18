
EasyBlog.require()
.script('admin/grid')
.done(function($)
{
	// Implement controller on the form
	$('[data-grid-eb]').implement(EasyBlog.Controller.Grid);
});
