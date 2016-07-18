
EasySocial.require()
.language('APP_FOLLOWERS_WIDGET_SUGGESTON_FOLLOWING')
.done( function($){
    $('[data-widget-follower-add]').click(function(ev) {
        var button = $(this);
        button.html($.language('APP_FOLLOWERS_WIDGET_SUGGESTON_FOLLOWING'));
    });
});
