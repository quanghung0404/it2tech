
EasyBlog.require()
.script('teamblogs')
.done(function($){
	$('[data-team-item]').implement(EasyBlog.Controller.TeamBlogs.Item);


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
