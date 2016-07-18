
EasyBlog.require()
.library('audiojs')
.done(function($) {
    $.audiojs.events.ready(function(){
        var element = $('#<?php echo $uid;?>');
        var settings = {
            "autoplay": <?php echo $autoplay ? 'true' : 'false'; ?>,
            "loop": <?php echo isset($loop) && $loop ? 'true' : 'false';?>
        };

        $.audiojs.create(element, settings);
    });
});