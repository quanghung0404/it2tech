
EasyBlog.require()
.script('posts/posts')
.done(function($) {

    <?php if ($preview) { ?>
        // prevent all anchor from click when this is a preview page.
        $("a:not([data-blog-preview-publish])").prop('onclick', null);

        $("a[data-post-print]")
            .removeAttr("data-post-print")
            .attr("href", 'javascript:void(0);');

        $("a:not([data-blog-preview-publish])").click(function (e) {
            e.preventDefault();
            e.stopPropagation();
        });

    <?php } ?>

    // Implement post library
    $('[data-blog-post]').implement(EasyBlog.Controller.Posts);
});
