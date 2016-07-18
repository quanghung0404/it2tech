EasySocial.module( 'uploader/item' , function($){

	var module 	= this;

	EasySocial.require()
	.view( 'site/uploader/preview' )
	.done( function($){

		EasySocial.Controller(
			'Uploader.Item',
			{
				defaults:
				{
					"{uploadItem}" : ".uploadItem",
					"{uploadItemPreview}" : ".uploadItem.preview a.itemLink",

					// Actions
					"{itemLink}"		: '.itemLink',
					"{itemDelete}"		: '.itemDelete',

					view: {

						preview : 'site/uploader/preview'

					}
				}
			},
			function( self ){ return {

				init: function(){

				},

				"{itemDelete} click": function( el ){

					var id 		= $( el ).data( 'id' );

					EasySocial.ajax( 'site:/controllers/uploader/delete' , {
						'id'	: id
					}, function(){

						// Remove the item from the list
						$( el ).parents( 'li.uploadItem' ).remove();
					})
				},

				"{uploadItemPreview} click" : function( el ){

					var uri 	= $( el ).data( 'uri' ),
						title 	= $( el ).data( 'title' );

					$.dialog({
						title: title,
						content: $.Image.get(uri)
					});





					// $.dialog({
					// 	'title'		: title,
					// 	'content'	: content,
					// 	afterShow	: function(){

					// 		$.dialog().update();

					// 	}
					// });
				}

			} }
		);
	});

	module.resolve();
});
