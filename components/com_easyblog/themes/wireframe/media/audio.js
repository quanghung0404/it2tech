
EasyBlog.require()
.library('audiojs')
.done(function($) {
    $.audiojs.events.ready(function(){
        var element = $('#<?php echo $id;?>');
        var settings = {
            "autoplay": false,
            "loop": false
        };

        $.audiojs.create(element, settings);
    });
});