EasySocial.require()
	.library('ui/timepicker')
	.done(function($){

		// Apply datepicker on created date
		$( '[data-badge-created]' ).datetimepicker(
		{
			timeFormat		: "HH:mm:ss",
			dateFormat		: "yy-mm-dd",
			changeMonth		: true,
			changeYear 		: true,
			lang: "<?php echo JFactory::getDocument()->getLanguage();?>"
		});

		$.Joomla( 'submitbutton' , function( task )
		{
			if( task == 'cancel' )
			{
				window.location.href	= '<?php echo JURI::root();?>administrator/index.php?option=com_easysocial&view=badges';

				return false;
			}

			$.Joomla( 'submitform' , [task] );
		});
	});