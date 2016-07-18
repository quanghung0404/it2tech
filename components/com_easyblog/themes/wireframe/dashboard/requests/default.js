EasyBlog.ready(function(){

    $('[data-reject-request]').on('click', function() {
        var id = $(this).data('id');

        EasyBlog.dialog({
            content: EasyBlog.ajax('site/views/dashboard/confirmRejectTeamMember', {"id": id })
        });

    });

    $('[data-approve-request]').on('click', function() {
        var id = $(this).data('id');

        EasyBlog.dialog({
            content: EasyBlog.ajax('site/views/dashboard/confirmApproveTeamMember', {"id": id })
        });

    });

});