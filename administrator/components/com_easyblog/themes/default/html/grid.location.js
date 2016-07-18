EasySocial.require()
	.script("locations")
	.done(function($){
		$('<?php echo $selector; ?>')
			.addController("EasySocial.Controller.Locations");
	});
