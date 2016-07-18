EasySocial.module( 'admin/migrators/migrator' , function($) {

	var module = this;

	EasySocial.require()
	.script( 'progress/progress' )
	.done(function($){

		EasySocial.Controller(
			'Migrators.Migrator',
			{
				// A list of selectors we define
				// and expect template makers to follow.
				defaultOptions:
				{
					// Controller Properties.
					component 			: null,

					processState 		: 0,

					// Progress bar controller
					progressController : null,

					mapping 			: null,

					updateconfig 		: 0,


					"{initiateButton}"	: "[data-initiate-migration]",
					"{progressBar}" 	: ".discoverProgress",
					"{results}"			: ".scannedResult",
					"{viewLog}"			: ".viewLog",
					"{customFieldsMap}" : "[data-custom-fields-map]",

					"{resultForm}"		: "[data-migration-result]",

					"{startWidget}"		: "[data-start-widget]",
					"{fieldItem}"		: "[data-field-item]",

					"{startMigrationButton}" : "[data-start-migration]",

					"{rows}"			: "[data-row-item]",
					"{selection}"		: "[data-field-item]",

					"{jomsocialBackButton}" : "[data-jomsocial-back-button]"
				}
			},
			function(self){

				return {

					init: function()
					{
						// Initialize progress bar.
						self.initProgressBar();

						// Initialize the logging area.
						self.initLogging();
					},

					showCustomFields: function()
					{
						// Hide the initial section
						self.startWidget().slideUp();

						//Show the custom fields map.
						self.customFieldsMap().slideDown();
					},

					showResultForm: function()
					{
						self.customFieldsMap().slideUp();

						if( self.options.component != 'com_community' )
						{
							self.startWidget().slideUp();
						}

						self.resultForm().slideDown();
					},

					startMigration: function()
					{
						// Disable start button.
						// self.startButton().attr( 'disabled' , 'disabled' );

						self.showResultForm();

						// to prevent user click multiple times.
						if( self.options.processState == 1 )
						{
							return;
						}
						else
						{
							self.options.processState = 1;
						}

						self.reset();


						// Discover the list of files.
						EasySocial.ajax( 'admin/controllers/migrators/check' ,
						{
							'component' : self.options.component
						})
						.done(function( data )
						{

							if( data.isvalid )
							{
								// Begin progress.
								self.options.progressController.begin( data.count );

								// Begin to loop through each files.
								self.startIterating('');
							}
							else
							{
								// Ensure results is always hidden.
								self.results().show();

								// Add logging
								self.addLog( 'Error: ' + data.message );

								// reopen the process state.
								self.options.processState = 0;
							}

						});
					},

					// Resets the scan.
					reset: function()
					{
						// Reset the logs
						self.results().html('');

						// Hide the viewlog button
						self.initLogging();

						// Reset progress bar.
						self.options.progressController.reset();
					},

					initLogging: function()
					{
						// Ensure view log button is always hidden.
						self.viewLog().hide();
					},

					initProgressBar: function()
					{
						// Implement progressbar
						self.progressBar().implement( EasySocial.Controller.Progress );

						// Set this to the options so that we can easily access the controller.
						self.options.progressController	= self.progressBar().controller();
					},

					addLog: function( message )
					{
						$( '<li>' ).html( message )
							.appendTo( self.results() );
					},

					startIterating: function( item )
					{

						if( self.options.mapping == null )
						{
							if( self.selection().length > 0 )
							{
								self.options.mapping = $('#adminForm').serializeArray();
							}
						}

						EasySocial.ajax( 'admin/controllers/migrators/process' ,
						{
							"component"	: self.options.component,
							"item" 		: item,
							"mapping"	: self.options.mapping,
							"updateconfig"	: self.options.updateconfig,
						})
						.always(function( data, updateConfig )
						{

							self.options.updateconfig = updateConfig;

							// As long as the files list are not empty yet, we still need to process it.
							if( data["continue"] )
							{
								// Update once.
								self.options.progressController.touch( 'Discovering...' );

								// Append message to the result list.
								self.addLog( data.message );

								// Run this again.
								self.startIterating( data.item );
							}
							else
							{
								// Update once.
								self.options.progressController.touch( 'Discover Completed' );

								// Append message to the result list.
								self.addLog( data.message );

								// Append completed message to the result list since we know this is the last item.
								self.addLog( 'migration process completed.' );

								// Show view log button.
								self.viewLog().show();

								// Make the scan button work again.
								self.jomsocialBackButton().show();

								// reopen the process state.
								self.options.processState = 0;
							}
						});
					},

					"{fieldItem} change" : function( el )
					{
						var value 	= $( el ).val();

						// Add error class on row
						if( value == '' )
						{
							$( el ).parents( '[data-row-item]' ).removeClass( 'success' ).addClass( 'error' );
						}
						else
						{
							$( el ).parents( '[data-row-item]' ).removeClass( 'error' ).addClass( 'success' );
						}
					},

					"{startMigrationButton} click" : function()
					{
						// If there's error, show dialog and confirm that the user doesn't want to migrate
						// selected fields.
						if( self.selection().length > 0 )
						{
							self.selection().each( function( i, el ) {

								if( $( el ).val() == "" )
								{
									$( el ).parents( '[data-row-item]' ).removeClass( 'success' ).addClass( 'error' );
								}
								else
								{
									$( el ).parents( '[data-row-item]' ).removeClass( 'error' ).addClass( 'success' );
								}
							});
						}

						var hasError = self.rows().hasClass( 'error' );

						if( hasError )
						{
							EasySocial.dialog(
							{
								content 	: EasySocial.ajax( 'admin/views/migrators/confirmMigration' ),
								bindings 	:
								{
									"{submitButton} click" : function()
									{
										self.startMigration();

										EasySocial.dialog().close();
									}
								}
							});
						}
						else
						{
							// do lets this.
							self.startMigration();

						}

					},

					"{initiateButton} click" : function( element )
					{
						self.showCustomFields();
					},

					"{viewLog} click" : function()
					{
						self.results().toggle();
					}
				}

			}
		);

		module.resolve();
	});

});
