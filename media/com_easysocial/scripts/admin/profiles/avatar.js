EasySocial.module( 'admin/profiles/avatar' , function($){

	var module	= this;

	EasySocial.require()
	.script( 'uploader/uploader' )
	.done( function(){

		EasySocial.Controller(
			'Profiles.Avatar',
			{
				defaultOptions:
				{
					// Properties
					token 				: null,

					// Elements
					"{fileUploader}"		: "[data-profile-avatars-uploader]",
					"{startUploadButton}"	: "[data-profile-avatars-startupload]",
					"{avatarList}"			: "[data-profile-avatars-list]",
					"{avatarEmpty}"			: "[data-profile-avatars-empty]",
					"{avatarItem}"			: "[data-profile-avatars-item]",
					"{messagePlaceholder}"	: "[data-profile-avatars-message]",
					"{removeFile}"			: ".removeFile",
					"{clearUploadedItems}"	: "[data-uploader-clear]"
				}
			},
			function(self)
			{
				return {

					init: function()
					{
						// Get the current profile id
						self.options.id 	= self.element.data( 'id' );

						// Initialize upload controller
						self.initUploader();

						// Initialize avatar controller
						self.initAvatar();
					},

					initUploader: function()
					{
						// Apply uploader controller on the file uploader.
						self.fileUploader().implement( EasySocial.Controller.Uploader,
							{
								url : $.indexUrl + '?option=com_easysocial&namespace=admin/controllers/profiles/uploadDefaultAvatars&' + self.options.token + '=1&tmpl=component&format=ajax&uid=' + self.options.id
							});
					},

					initAvatar: function()
					{
						// Apply controller to avatar items.
						self.avatarItem().implement( 'EasySocial.Controller.Profiles.Avatar.Item',
						{
							"{parent}"	: self,
							items		: self.avatarItem
						});
					},

					addMessage: function( message )
					{
						// Clear previous messages first
						self.clearMessage();

						self.setMessage( message );
					},
					/**
					 * Override the file removal click event.
					 */
					"{removeFile} click" : function( el , event )
					{
						var id 	= $(el).parents( 'li' ).attr( 'id' );

						self.fileUploader().controller().removeItem( id );
					},

					/**
					 * Bind the click event on the start upload button.
					 */
					"{startUploadButton} click" : function()
					{
						var controller	= self.fileUploader().controller();

						controller.startUpload();
					},

					/**
					 * Track the progress of the uploaded item.
					 */
					"{fileUploader} UploadProgress" : function( el , event , file )
					{
						// Get the upload progress.
						var progress	= file.percent,
							elementId	= '#' + file.id,
							progressBar	= $( elementId ).find( '.progressBar' );

						// Show the progress bar.
						progressBar.show();

						// Update the width of the progress bar.
						progressBar.find( '.bar' ).css( 'width' , progress + '%' );
					},

					// Bind the UploadComplete method provided by uploader
					"{fileUploader} FileUploaded" : function( el, event, file, response )
					{
						if( response[ 0 ] != undefined )
						{
							var contents 	= response[0].data[ 0 ];

							// Hide empty if any
							self.avatarEmpty().hide();

							// Prepend the item
							self.avatarList().prepend( contents );

							self.clearUploadedItems().show();

							// Apply the controller
							self.initAvatar();
						}
					},

					"{clearUploadedItems} click" : function()
					{
						var controller 	= self.fileUploader().controller();

						// Reset the queue
						controller.reset();

						// Hide itself since there's no history now.
						self.clearUploadedItems().hide();
					}
				}
			}
		);

		/**
		 * Avatar item controller.
		 */
		EasySocial.Controller(
			'Profiles.Avatar.Item',
			{
				defaultOptions:
				{
					// Properties.
					id 		: null,

					"{deleteLink}"			: "[data-avatar-delete]",
					"{setDefaultAvatar}"	: "[data-avatar-default]"
				}
			},
			function( self )
			{
				return {

					init : function()
					{
						self.options.id 	= self.element.data( 'id' );
					},

					/**
					 * Sets an avatar as the default avatar.
					 */
					"{setDefaultAvatar} click" : function(el , event )
					{
						EasySocial.ajax(
						'admin/controllers/avatars/setDefault',
						{
							"id" : self.options.id
						})
						.done(function( message )
						{
							// Remove all default class
							self.parent.avatarItem().removeClass( 'default' );

							// Add a default class to itself
							self.element.addClass( 'default' );

							self.parent.addMessage( message );
						});
					},

					"{deleteLink} click": function()
					{
						EasySocial.dialog(
						{
							content 	: EasySocial.ajax( 'admin/views/profiles/confirmDeleteAvatar' ),
							bindings	: 
							{
								"{deleteButton} click" : function( el , event )
								{
									$( el ).addClass( 'btn-loading' );
									
									EasySocial.ajax( 'admin/controllers/avatars/delete' , 
									{
										"id" : self.options.id
									})
									.done(function( message )
									{										
										// Remove the element
										self.element.remove();

										if( self.parent.avatarList().children().length == 0 )
										{
											self.parent.avatarEmpty().show();
										}

										self.parent.addMessage( message );

										// Hide the dialog
										EasySocial.dialog().close();										
									});
								}
							}
						})
					}
				}
			});

		module.resolve();

	});

});


