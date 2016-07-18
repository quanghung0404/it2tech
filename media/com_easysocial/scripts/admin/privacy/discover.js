EasySocial.module( 'admin/privacy/discover' , function($) {

	var module = this;

	EasySocial.require()
	.script( 'progress/progress' )
	.done(function($){

		EasySocial.Controller(
			'Privacy.Discover',
			{
				// A list of selectors we define
				// and expect template makers to follow.
				defaultOptions:
				{
					// Controller Properties.
					files 			: [],

					// Progress bar controller
					progressController : null,

					// Start button
					"{startButton}"	: ".scanRules",

					// Progress Bar
					"{progressBar}" : ".discoverProgress",

					// Logging results
					"{results}"		: ".scannedResult",

					// View logs button.
					"{viewLog}"		: ".viewLog",

					// View items.
					view			:
					{
					}
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

					startIterating: function()
					{
						// Get the file from the shelf
						var file 	= self.options.files.shift();

						EasySocial.ajax( 'admin/controllers/privacy/scan' ,
						{
							"file"	: file
						})
						.always(function( data ){

							// As long as the files list are not empty yet, we still need to process it.
							if( self.options.files.length > 0 )
							{
								// Update once.
								self.options.progressController.touch( 'Discovering...' );

								// Append message to the result list.
								self.addLog( 'Scanned ' + data.file + ' : ' + data.rules.length + ' rules installed.' );

								// Run this again.
								self.startIterating();
							}
							else
							{
								// Update once.
								self.options.progressController.touch( 'Discover Completed' );

								// Append message to the result list.
								self.addLog( 'Scanned ' + data.file + ' : ' + data.rules.length + ' rules installed.' );

								// Append completed message to the result list since we know this is the last item.
								self.addLog( 'Scanning completed.' );

								// Show view log button.
								self.viewLog().show();

								// Make the scan button work again.
								self.startButton().removeAttr( 'disabled' );
							}
						});
					},

					"{startButton} click" : function( element )
					{
						self.reset();

						// Disable start button.
						self.startButton().attr( 'disabled' , 'disabled' );

						// Discover the list of files.
						EasySocial.ajax( 'admin/controllers/privacy/discoverFiles' , {})
						.done(function( files ){

							// Set the files to the properties.
							self.options.files 	= files;

							if( self.options.files.length > 0 )
							{
								// Begin progress.
								self.options.progressController.begin( self.options.files.length );

								// Ensure results is always hidden.
								self.results().hide();

								// Add logging
								self.addLog( 'Found a total of ' + files.length + ' rules file in the site.' );

								// Begin to loop through each files.
								self.startIterating();
							}
							else
							{
								// Update once.
								self.options.progressController.begin( 1 );
								self.options.progressController.completed( 'Discover Completed' );

								// Append message to the result list.
								self.addLog( $.language( 'COM_EASYSOCIAL_SCAN_COMPLETED' ) );

								// Make the scan button work again.
								self.startButton().removeAttr( 'disabled' );
							}

						});
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
