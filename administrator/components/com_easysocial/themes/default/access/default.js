EasySocial
	.require()
	.script('admin/grid/grid')
	.done(function($) {
		$('[data-table-grid]').addController('EasySocial.Controller.Grid');
	});
