EasySocial.require()
.script("story/links")
.done(function($) {
	var plugin = story.addPlugin("links", {
                            validateUrl: <?php echo $this->config->get('links.parser.validate') ? 'true' : 'false';?>
                });
});
