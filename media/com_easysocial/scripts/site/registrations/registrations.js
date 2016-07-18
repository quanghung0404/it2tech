EasySocial.module( 'site/registrations/registrations' , function($){

	var module 				= this;

	EasySocial.require()
	.script( 'validate', 'field' )
	.view( 'site/registration/dialog.error' )
	.language( 'COM_EASYSOCIAL_CLOSE_BUTTON' , 'COM_EASYSOCIAL_REGISTRATION_ERROR_DIALOG_TITLE' )
	.done(function($){

		EasySocial.Controller(
			'Registrations.Form',
			{
				defaultOptions:
				{
					// passed in by caller
					previousLink	 : null,

					"{submit}"		: "[data-registration-submit]",
					"{field}"		: "[data-registration-fields-item ]",
					"{previous}"	: "[data-registration-previous]",

					view :
					{
						formError 	: "site/registration/dialog.error"
					}
				}
			},
			function(self)
			{

				return{

					init: function()
					{
						self.field().addController('EasySocial.Controller.Field.Base', {
							mode: 'register'
						});
					},

					"{previous} click" : function( el , event )
					{
						event.preventDefault();


						window.location.href	= self.options.previousLink;

						return false;
					},

					"{submit} click" : function( el , event )
					{
						event.preventDefault();

						// Apply loading class on button
						$( el ).addClass( 'btn-loading' );

						$( self.element ).validate()
						.fail( function()
						{
							// Remove loading class
							$( el ).removeClass( 'btn-loading' );

							EasySocial.dialog(
							{
								"title"		: $.language( 'COM_EASYSOCIAL_REGISTRATION_ERROR_DIALOG_TITLE' ),
								"content"	: self.view.formError(true),
								"width"		: 400,
								"height"	: 150,
								"buttons"	:
								[
									{
										"name"	: $.language( 'COM_EASYSOCIAL_CLOSE_BUTTON' ),
										"classNames"	: "btn btn-es-primary",
										"click"	: function()
										{
											EasySocial.dialog().close();
										}
									}
								]

							});
						})
						.done( function()
						{
							self.element.submit();
						});

						return false;
					}
				}
			});

		module.resolve();
	});

});
