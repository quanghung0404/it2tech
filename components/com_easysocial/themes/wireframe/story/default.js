<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
EasySocial.require()
.script('story')
.done(function($){

	// Story controller
	$("[data-story=<?php echo $story->id; ?>]")
		.addController(
			"EasySocial.Controller.Story",
			{
			<?php
			if ($story->plugins)
			{
				$length = count($story->plugins);
				$i = 0;
			?>
				plugin: {
					<?php foreach($story->plugins as $plugin) { ?>
					<?php echo $plugin->name; ?>: {
						id: '<?php echo $plugin->id; ?>',
						type: '<?php echo $plugin->type; ?>',
						name: '<?php echo $plugin->name; ?>'
					}<?php if (++$i < $length) { echo ','; }; ?>
					<?php } ?>
				},
			<?php } ?>
				enterToSubmit: <?php echo FD::config()->get('stream.story.entertosubmit') ? 'true' : 'false' ?>,
				sourceView: "<?php echo JRequest::getCmd('view',''); ?>"
			}
		);

	// Story plugins
	$.module("<?php echo $story->moduleId; ?>")
		.done(function(story) {
			<?php foreach($story->plugins as $plugin) { ?>
				<?php echo $plugin->script; ?>
			<?php } ?>
		});
});
