
EasyBlog.require()
.script('teamblogs','posts/posts')
.done(function($){

	$('[data-team-item]').implement(EasyBlog.Controller.TeamBlogs.Item);

	$('[data-team-posts]').implement(EasyBlog.Controller.Posts);


	$(document).on('click.teamblog.join', '[data-team-join]', function() {

		var id = $(this).data('id');

		EasyBlog.dialog({
			content: EasyBlog.ajax('site/views/teamblog/join', {
				"id": id,
				"return": "<?php echo base64_encode(JRequest::getURI());?>"
			})
		});

	});

	$(document).on('click.teamblog.join', '[data-team-leave]', function() {

		var id = $(this).data('id');

		EasyBlog.dialog({
			content: EasyBlog.ajax('site/views/teamblog/leave', {
				"id": id,
				"return": "<?php echo base64_encode(JRequest::getURI());?>"
			})
		});

	});

});
