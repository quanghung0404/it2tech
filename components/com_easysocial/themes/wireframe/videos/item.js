
EasySocial.require()
.script('videos/item')
.done(function($) {

    $('[data-video-item]').implement(EasySocial.Controller.Videos.Item, {
        callbackUrl: "<?php echo base64_encode($video->getPermalink(false));?>"

        <?php if ($tagsList) { ?>
        ,"tagsExclusion": <?php echo $tagsList;?>
        <?php } ?>
    });
});