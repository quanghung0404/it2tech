EasySocial.require()
.script("story/videos")
.done(function($){
	var plugin =
		story.addPlugin("videos", {
			uploader: {
				settings: {
					url: "<?php echo FRoute::raw('index.php?option=com_easysocial&controller=videos&task=uploadStory&uid=' . $uid. '&type=' . $type . '&format=json&tmpl=component&' . ES::token() . '=1' ); ?>",
                    max_file_size: "<?php echo $uploadLimit; ?>",
                    camera: "video"
				}
			},
            video: {
                "uid": "<?php echo $uid;?>",
                "type": "<?php echo $type;?>"
            },
            errors: {
                "-600": "<?php echo JText::sprintf('COM_EASYSOCIAL_VIDEOS_FILESIZE_ERROR', $uploadLimit);?>"
            }
		});
});
