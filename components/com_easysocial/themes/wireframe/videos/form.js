
EasySocial.require()
.script('videos/form')
.done(function($) {

    $('[data-videos-form]').implement(EasySocial.Controller.Videos.Form, {

    <?php if ($tagItemList) { ?>
    "tagsExclusion": <?php echo FD::json()->encode($tagItemList); ?>
    <?php } ?>

    });
});
