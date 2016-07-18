

<?php if ($this->config->get('notifications.broadcast.popup') && !$this->my->guest) { ?>
EasySocial.require()
.script('site/system/broadcast')
.done(function($) {

	$('[data-es-structure]').implement(EasySocial.Controller.System.Broadcast, {
        interval: "<?php echo $this->config->get('notifications.broadcast.interval');?>",
        period: "<?php echo $this->config->get('notifications.broadcast.period');?>",
        sticky: <?php echo $this->config->get('notifications.broadcast.sticky') ? 'true' : 'false'; ?>
    });
});
<?php } ?>
