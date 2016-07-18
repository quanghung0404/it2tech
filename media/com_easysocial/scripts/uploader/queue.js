EasySocial.module( 'uploader/queue' , function($){

	var module 	= this;

	EasySocial.require()
	.view( 'site/uploader/queue.item' )
	.done( function($){

		EasySocial.Controller(
			'Uploader.Queue',
			{
				defaults:
				{
					"{item}"	: "[data-uploaderQueue-item]"
				}
			},
			function( self ){

				return {

					init: function()
					{
						self.item().implement( EasySocial.Controller.Uploader.Queue.Item );
					}
				}
			}
		);

		EasySocial.Controller( 
			'Uploader.Queue.Item',
			{
				defaultOptions:
				{
					"{delete}"	: "[data-uploaderQueue-remove]",
					"{progress}": "[data-uploaderQueue-progress]",
					"{progressBar}" : "[data-uploaderQueue-progressBar]",
					"{status}"		: "[data-uploaderQueue-status]",
					"{id}"			: "[data-uploaderQueue-id]"
				}
			},
			function( self ){
				return {
					init : function()
					{

						if( self.uploader.options.temporaryUpload )
						{						
							// Store it as template and remove it
							self.idTemplate = self.id().toHTML();
							self.id().remove();
						}
					},

					"{delete} click" : function()
					{
						self.uploader.removeItem( self.element.attr( 'id' ) );
					},

					"{self} FileUploaded" : function( el , event , file , response )
					{
						// var response	= response[0];

						if( self.uploader.options.temporaryUpload )
						{
							// Create a hidden input containing the id
							$.buildHTML(self.idTemplate)
								.val(response.id)
								.appendTo(self.element);
						}

						if( file.status == 5 )
						{
							self.element.addClass( 'is-done' );
							self.status().html( 'Done' );
						}
					},

					"{self} UploadProgress" : function( el , event , progress )
					{
						// Set the progress.
						self.status().html( progress.percent + '%' );

						self.progressBar().css( 'width' , progress.percent + '%');
					},

					"{self} FileError": function()
					{
						self.element.removeClass("is-done is-queue").addClass("is-error");

						self.progress()
							.removeClass("progress-danger progress-success progress-info progress-warning")
							.addClass("progress-danger");

						self.status().html( 'Error' );
					}
				}
			}
		);

		module.resolve();
	});
});
