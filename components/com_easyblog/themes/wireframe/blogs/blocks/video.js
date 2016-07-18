EasyBlog.require()
.library('videojs')
.done(function() {
    videojs('<?php echo $uid;?>', {
            "controls": true,
            "autoplay": <?php echo $autoplay ? 'true' : 'false';?>,
            "loop": <?php echo $loop ? 'true' : 'false';?>
        }, function(){
            <?php if ($muted) { ?>
            this.muted(true);
            <?php } ?>
        });
});