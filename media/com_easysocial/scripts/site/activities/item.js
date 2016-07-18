EasySocial.module( 'site/activities/item' , function($){

	var module	= this;

	EasySocial.require()
	.script()
	.done(function($){

		EasySocial.Controller(
			'Activities.Item',
			{
				defaultOptions:
				{
					// Elements
					"{toggle}"		: "[data-activity-toggle]",
					"{deleteBtn}"	: "[data-activity-delete]"

				}
			},
			function( self ){
				return {

					init : function()
					{
						// Implement sidebar controller.
					},

					"{toggle} click" : function( el , event )
					{
						EasySocial.ajax( 'site/controllers/activities/toggle',
						{
							"id"		: self.element.data('id'),
							"curState" 	: self.element.data('current-state')
						})
						.done(function( lbl, isHidden)
						{
							$( el ).text( lbl );
							self.element.data('current-state', isHidden);

							if( isHidden )
							{
								self.element.children( "div.es-stream" ).addClass( 'isHidden' );
							}
							else
							{
								self.element.children( "div.es-stream" ).removeClass( 'isHidden' );
							}
						})
						.fail(function( message ){

							console.log( message );
						});
					},

					"{deleteBtn} click" : function()
					{
						var uid = self.element.data('id');

						EasySocial.dialog({
							content		: EasySocial.ajax( 'site/views/activities/confirmDelete' ),
							bindings	:
							{
								"{deleteButton} click" : function()
								{
									EasySocial.ajax( 'site/controllers/activities/delete',
									{
										"id"		: uid,
									})
									.done(function( html )
									{
										self.element.fadeOut();

										// close dialog box.
										EasySocial.dialog().close();
									});
								}
							}
						});

					}


				}
			});

		module.resolve();
	});

});
