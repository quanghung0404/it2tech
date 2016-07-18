
EasyBlog.ready(function($){

	var selector = "[data-truncater-<?php echo $uid;?>]";

	$(selector).find('a')
		.bind('click', function(){
			$(selector).find('[data-truncater-ellipses]')
				.hide();

			$(selector).find('[data-truncater-balance]')
				.show();

			$(this).hide();
		});
});