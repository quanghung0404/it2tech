
EasyBlog.require()
.script('authors', 'posts/posts')
.done(function($) {
	$('[data-authors]').implement(EasyBlog.Controller.Authors.Listing);
});