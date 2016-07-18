
EasySocial.require()
.script( 'site/stream/stream' )
.done(function($) {

	// Implement main stream controller.
	$( '[data-streams]' ).implement(
			"EasySocial.Controller.Stream",
			{
				source : "<?php echo JRequest::getVar('view', ''); ?>",
				sourceId : "<?php echo JRequest::getVar('id', ''); ?>",
				loadmore : false
			} );

});
