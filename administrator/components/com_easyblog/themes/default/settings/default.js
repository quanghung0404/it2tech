
EasyBlog.ready(function($){

	$.Joomla("submitbutton", function(task) {

		$('#submenu li').children().each( function(){
			if( $(this).hasClass( 'active' ) )
			{
				$( '#active' ).val( $(this).attr('id') );
			}
		});

		$('dl#subtabs').children().each(function(){
			
			if($(this).hasClass('open')) {
				$( '#activechild' ).val( $(this).attr('class').split(" ")[0] );
			}
		});

		if (task == 'export') {
			window.location.href 	= '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=settings&format=raw&layout=export&tmpl=component';
			return;
		}

		if( task == 'import' )
		{
			admin.settings.importSettings();
			return;
		}

		$.Joomla("submitform", [task]);
	});

	window.switchFBPosition = function()
	{
		if( $('#main_facebook_like_position').val() == '1' )
		{
		    $('#fb-likes-standard').hide();
		    if( $('#standard').attr('checked') == true)
		    	$('#button_count').attr('checked', true);
		}
		else
		{
		    $('#fb-likes-standard').show();
		}
	}

});