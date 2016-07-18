EasySocial.require().script("admin").done();


EasySocial.ready(function($){
    // Fix the header for mobile view
    $('.container-nav').appendTo($('.header'));

    $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
            $('.header').addClass('header-stick');
        } else if ($(this).scrollTop() < 50) {
            $('.header').removeClass('header-stick');
        }
    });

    $('.nav-sidebar-toggle').click(function(){
        $('html').toggleClass('show-easysocial-sidebar');
        $('.subhead-collapse').removeClass('in').css('height', 0);
    });

    $('.nav-subhead-toggle').click(function(){
        $('html').removeClass('show-easysocial-sidebar');
        $('.subhead-collapse').toggleClass('in').css('height', 'auto');
    });
});
