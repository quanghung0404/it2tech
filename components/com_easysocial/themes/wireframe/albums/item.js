<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die('Unauthorized Access');

$uploadUrl = FRoute::raw('index.php?option=com_easysocial&controller=photos&task=upload&format=json&tmpl=component'
						. '&albumId=' . $album->id
						. '&layout=' . $options['layout']
						. '&uid=' . $lib->uid
						. '&type=' . $lib->type
						. '&createStream=1'
						. '&' . FD::token() . '=1');
?>

EasySocial.require()
	.script("albums/item")
	.done(function($){

		$("[data-album-item=<?php echo $album->uuid(); ?>]")
			.addController(
				"EasySocial.Controller.Albums.Item",
				{
					uid: "<?php echo $lib->uid;?>",
					type: "<?php echo $lib->type; ?>",
					rtl: <?php echo $rtl ? "true" : "false";?>,
					<?php if( $lib->editable( $album ) ){ ?>
						editable: true,
						plugin:
						{
							editor:
							{
								canUpload: <?php echo ($options['canUpload']) ? 'true' : 'false' ?>
							}
						},
						<?php if ($options['canUpload']) { ?>
						uploader: {
							settings: {
								url: "<?php echo $uploadUrl ?>",
								max_file_size: "<?php echo $lib->getUploadLimit(); ?>",
								camera: "image"
							}
						}
						<?php } ?>
					<?php } ?>
				});
	});
