EasySocial.require()
	.script("site/explorer")
	.done(function($){
		$("[data-fd-explorer=<?php echo $uuid;?>]").explorer()
			.on("fileUse", function(event , id , file , data) {});
	});