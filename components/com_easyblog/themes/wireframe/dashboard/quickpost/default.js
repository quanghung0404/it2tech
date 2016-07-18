
EasyBlog.require()
.script('quickpost/form')
.done(function($) {

	$('[data-eb-quickpost]').implement(EasyBlog.Controller.Quickpost.Form);


	$(document).on('change.quickpost.autopost', '[data-autopost-item]', function() {

		var element = $(this),
			checked = element.is(':checked');

		if (checked) {
			element.parent().addClass('checked');
		} else {
			element.parent().removeClass('checked');
		}

	});
	
	$('.eb-head-popover').popover();
});