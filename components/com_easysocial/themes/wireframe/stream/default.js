
EasySocial.require()
.script('site/stream/stream')
.done(function($){

	$('[data-streams]').implement('EasySocial.Controller.Stream', {
		checknew: <?php echo $this->config->get( 'stream.updates.enabled' ) ? 'true' : 'false'; ?>,
		interval: "<?php echo ES::config()->get('stream.updates.interval'); ?>",
		source: "<?php echo JRequest::getVar('view', ''); ?>",
		sourceId: "<?php echo JRequest::getVar('id', ''); ?>",
		autoload: <?php echo $this->config->get('stream.pagination.autoload') ? 'true' : 'false'; ?>
	});
});
