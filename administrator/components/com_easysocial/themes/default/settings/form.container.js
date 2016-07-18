
EasySocial.require()
.done( function($){

	$.Joomla( 'submitbutton' , function( task ){

		if( task == 'reset' )
		{
			EasySocial.dialog(
			{
				content 	: EasySocial.ajax( "admin/views/settings/confirmReset", { "section" : "<?php echo $page;?>"} ),
				bindings	:
				{
					"{resetButton} click" : function()
					{
						this.resetForm().submit();
					}
				}
			});

			return false;
		}

		if( task == 'export' )
		{
			$.download( '<?php echo JURI::root();?>administrator/index.php?option=com_easysocial&view=settings&format=raw&layout=export&tmpl=component' );
			return false;
		}

		if( task == 'import' )
		{
			EasySocial.dialog(
			{
				content		: EasySocial.ajax( "admin/views/settings/import" , { "page" : "<?php echo $page;?>"}),
				bindings	: 
				{
					"{submitButton} click" : function()
					{
						this.importForm().submit();
					}
				}
			});
		}

		if( task == 'apply' )
		{
			$.Joomla( 'submitform' , [task] );
		}

		return false;
	});

});
