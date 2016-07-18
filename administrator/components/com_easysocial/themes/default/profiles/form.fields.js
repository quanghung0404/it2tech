EasySocial.require()
.script('admin/profiles/fields')
.done(function($){

	$('.profileFieldForm').addController('EasySocial.Controller.Fields', {
		group: '<?php echo $fieldGroup; ?>'
	});

});
