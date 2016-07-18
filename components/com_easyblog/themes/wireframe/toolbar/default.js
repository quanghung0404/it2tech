
EasyBlog.require()

<?php if ($this->config->get('layout_responsive')) { ?>
.script('layout/responsive')
<?php } ?>

.done(function($){

    if ($('#more-settings li').length == 0) {
        $('#more-settings').parent('li').addClass('hide');
    }

	$(document).on('click', '[data-blog-toolbar-logout]', function(event) {
		$('[data-blog-logout-form]').submit();
	});

    $('.btn-eb-navbar').click(function() {
        $('.eb-navbar-collapse').toggleClass("in");
        return false;
    });

	$('#ezblog-head #ezblog-search').bind('focus', function(){

        $(this).animate({
            width: '170'
        });
	});

	$('#ezblog-head #ezblog-search').bind( 'blur' , function(){
		$(this).animate({ width: '120'});
	});

    <?php if ($this->config->get('layout_responsive')) { ?>
	$('#ezblog-menu').responsive({at: 540, switchTo: 'narrow'});
	$('.eb-nav-collapse').responsive({at: 560, switchTo: 'nav-hide'});

	$('.btn-eb-navbar').click(function() {
		$('.eb-nav-collapse').toggleClass("nav-show");
		return false;
	});
    <?php } ?>
});
