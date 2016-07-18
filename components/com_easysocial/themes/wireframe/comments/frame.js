
EasySocial.require()
.script('site/comments/frame')
.script('libraries/lightbox')
.done(function($) {

	var selector = '[data-comments="<?php echo $group; ?>-<?php echo $element; ?>-<?php echo $verb; ?>-<?php echo $uid; ?>"]';

	$(selector).addController('EasySocial.Controller.Comments', {
		'enterkey': '<?php echo $this->config->get('comments.enter'); ?>',
        'attachments': <?php echo $this->config->get('comments.attachments') ? 'true' : 'false';?>
	});

    lightbox.option({
        'albumLabel': '<?php echo JText::_('COM_EASYSOCIAL_COMMENTS_ATTACHMENTS_PHOTOS_ITEM');?>'
    });
});
