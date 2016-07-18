EasySocial.module( 'admin/alerts/discover' , function($) {

	var module = this;

	EasySocial.require()
	.script( 'progress/progress' )
	.language( 'COM_EASYSOCIAL_SCAN_COMPLETED' )
	.done(function($){

		EasySocial.Controller('Alerts.Discover', {

				// A list of selectors we define
				// and expect template makers to follow.
				defaultOptions: {
					// Controller Properties.
					files 			: [],

					// Progress bar controller
					progressController : null,

					// Progress Bar
					"{progressBar}" : "[data-alerts-discovery-progress]",

					// Logging results
					"{results}"		: "[data-alerts-discovery-result]"
				}
			}, function(self) {

				return {

					init: function() {
						// Initialize progress bar.
						self.initProgressBar();

						$.Joomla('submitbutton', function(task) {
							if (task == 'discover') {
								self.startDiscovering();
							}
						})
					},

					startDiscovering: function() {

						self.reset();

						// Discover the list of files.
						EasySocial.ajax('admin/controllers/alerts/discoverFiles' , {
						}).done(function(files, message) {
							self.reset();

							// Set the files to the properties.
							self.options.files 	= files;

							if (self.options.files.length > 0) {
								// Begin progress.
								self.options.progressController.begin( self.options.files.length );

								// Add logging
								self.addLog(message);

								// Begin to loop through each files.
								self.startIterating();
							}
						});
					},

					// Resets the scan.
					reset: function() {
						// Reset the logs
						self.results().html('');

						// Reset progress bar.
						self.options.progressController.reset();
					},

					initProgressBar: function() {
						// Implement progressbar
						self.progressBar().implement( EasySocial.Controller.Progress );

						// Set this to the options so that we can easily access the controller.
						self.options.progressController	= self.progressBar().controller();
					},

					addLog: function(message) {
						$( '<tr>' ).append( $( '<td>' ).html( message ) ).appendTo( self.results() );
					},

					startIterating: function() {
						// Get the file from the shelf
						var file 	= self.options.files.shift();

						EasySocial.ajax( 'admin/controllers/alerts/scan' ,
						{
							"file"	: file
						})
						.always(function( data , message , completeMessage ){

							// As long as the files list are not empty yet, we still need to process it.
							if( self.options.files.length > 0 )
							{
								// Update once.
								self.options.progressController.touch( '...' );

								// Append message to the result list.
								self.addLog( message );

								// Run this again.
								self.startIterating();
							}
							else
							{
								// Update once.
								self.options.progressController.touch( '...' );

								// Append message to the result list.
								self.addLog( $.language( 'COM_EASYSOCIAL_SCAN_COMPLETED' ) );

							}
						});
					}
				}

			}
		);

		module.resolve();
	});

});
