
EasyBlog.require()
.script('dashboard/posts')
.done(function($) {
	$('[data-eb-dashboard-posts]').implement(EasyBlog.Controller.Dashboard.Posts);

    <?php if ($isWrite) { ?>
        EasyBlog.ComposerLauncher.open('<?php echo EB::_('index.php?option=com_easyblog&view=composer&tmpl=component' . $defaultCategory, false); ?>');
    <?php } ?>
});
