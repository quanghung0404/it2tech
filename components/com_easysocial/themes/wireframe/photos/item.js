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
	.script("photos/item")
	.done(function($){

		$("[data-photo-item=<?php echo $photo->uuid(); ?>]")
			.addController(
				"EasySocial.Controller.Photos.Item",
				{
					<?php if ($options['showForm']) { ?>
					editable: <?php echo ($album->editable()) ? 1 : 0; ?>,
					taggable: <?php echo ($photo->taggable()) ? 1 : 0; ?>,
					<?php } ?>
					<?php if ($options['showNavigation']) { ?>
					navigation: true
					<?php } ?>
				});
	});
