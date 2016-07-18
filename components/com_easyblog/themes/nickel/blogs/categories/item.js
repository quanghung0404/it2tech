EasyBlog.require()
.library('masonry', 'imagesloaded')
.script('posts/posts')
.done(function($) {
	$('[data-blog-posts]').implement(EasyBlog.Controller.Posts);

	// MASONRY
    var container = $('.eb-posts-masonry');

	$('img').load(function(){
		container.imagesLoaded(function(){
			container.masonry({
				itemSelector : '.eb-post',
				isRTL: false
			});
		});
	});


	$('.eb-masonry').imagesLoaded( function(){
		$('.eb-masonry').masonry({
			itemSelector: '.eb-masonry-post'
		});
	});

	$('.eb-masonry').masonry({
		itemSelector: '.eb-masonry-post'
	});

	setTimeout(function() {
		// Cheap fix to fix social buttons
		$('.eb-masonry').masonry();
	}, 5000);	
});

EasyBlog.ready(function($){

	$('[data-show-all-authors]').on('click', function()
	{
		$('[data-author-item]').each(function()
		{
			$(this).find('img').attr('src', $(this).data('src'));

			$(this).removeClass('hide');
		});

		// Hide the button block
		$(this).addClass('hide');
	});

	$('[data-more-categories-link]').on('click', function() {
		$(this).hide();
		$('[data-more-categories]').css('display', 'inline-block');
	});
});
