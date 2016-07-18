
EasyBlog.ready(function($){

	$('[data-location-integration]').on('change', function(){
		var value = $(this).val();

		// Hide everything
		$('[data-panel-integration]').addClass('hide');

		// Show only what we want the user to see
		$('[data-panel-' + value + ']').removeClass('hide');
	});
});