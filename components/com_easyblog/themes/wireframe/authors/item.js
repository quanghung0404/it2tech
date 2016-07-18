
EasyBlog.require()
.script('authors', 'posts/posts')
.done(function($){
    $('[data-author-item]').implement(EasyBlog.Controller.Authors.Item);

    // Implement posts
    $('[data-blog-posts]').implement(EasyBlog.Controller.Posts);
});
