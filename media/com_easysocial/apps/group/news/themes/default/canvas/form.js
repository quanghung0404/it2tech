
EasySocial.ready(function($)
{
	window.toggleSave	= function()
	{
		<?php echo $editor->save( 'news_content' ); ?>
	}

	window.getContent 	= function()
	{
		<?php echo 'return ' . $editor->getContent( 'news_content' ); ?>
	}

	$( '[data-news-save-button]' ).on( 'click' , function()
	{
		// Ensure that it is always toggled back.
		window.toggleSave();

		var contents 	= window.getContent();

		if( contents == '' )
		{
			EasySocial.dialog(
			{
				content 	: EasySocial.ajax( 'apps/group/news/controllers/news/emptyContent' )
			});

			return false;
		}

		return true;
	});
});