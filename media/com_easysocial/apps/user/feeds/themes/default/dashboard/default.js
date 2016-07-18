
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/

EasySocial.require()
.library( 'dialog' )
.language('APP_FEEDS_TITLE_EMPTY', 'APP_FEEDS_URL_EMPTY')
.done(function($)
{

	$( '[data-feeds-create]' ).on( 'click' , function()
	{
		EasySocial.dialog({
			content	: EasySocial.ajax( "apps/user/feeds/views/feeds/form" , { 'id' : '<?php echo $app->id;?>' } ),
			bindings :
			{
				"{saveButton} click" : function()
				{
					// Get the feed title and feed url
					var title 	= $( '[data-feeds-form-title]' ).val(),
						url 	= $( '[data-feeds-form-url]' ).val();

					var notice = $('[data-feeds-form-notice]');


					// first remove all the alert styling.
					notice.removeClass('alert alert-error');
					notice.addClass('hide');


					if (title.trim().length == 0) {
						notice.text( $.language('APP_FEEDS_TITLE_EMPTY') );
						notice.addClass('alert alert-error');
						notice.removeClass('hide');
						return;
					}

					if (url.trim().length == 0) {
						notice.text( $.language('APP_FEEDS_URL_EMPTY') );
						notice.addClass('alert alert-error');
						notice.removeClass('hide');
						return;
					}

					EasySocial.ajax( 'apps/user/feeds/controllers/feeds/save' ,
					{
						"title"	: title,
						"url"	: url,
						"id"	: "<?php echo $app->id;?>"
					})
					.done(function( contents )
					{
						// Close dialog
						EasySocial.dialog().close();

						$( '[data-app-contents]' ).removeClass( 'is-empty' );

						$( '[data-feeds-lists]' ).append( contents );
					});
				}
			}
		});
	});

	$( '[data-feeds-lists]' ).on( 'click' , '[data-feeds-item-remove]' , function()
	{
		var id 		= $( this ).parents( '.feed-item' ).data( 'id' ),
			parent	= $( this ).parents( '.feed-item' );

		EasySocial.dialog(
		{
			content	: EasySocial.ajax( "apps/user/feeds/views/feeds/confirmDelete" , { 'id' : '<?php echo $app->id;?>' } ),
			bindings :
			{
				"{deleteButton} click" : function()
				{
					EasySocial.ajax( 'apps/user/feeds/controllers/feeds/delete' ,
					{
						"id"		: "<?php echo $app->id;?>",
						"feedId"	: id
					})
					.done(function()
					{
						EasySocial.dialog().close();

						$( parent ).remove();

						if( $( '[data-feeds-lists]' ).children().length == 0 )
						{
							$( '[data-app-contents]' ).addClass( 'is-empty' );
						}
					});
				}
			}
		});

	});

});
