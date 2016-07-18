EasySocial.module('admin/access/discover', function($) {
	var module = this;

	EasySocial
		.require()
		.script('progress/progress')
		.language('COM_EASYSOCIAL_SCAN_COMPLETED')
		.done(function($) {
			EasySocial.Controller('Access.Discover', {
				defaultOptions: {
					files: [],

					progressController: null,

					'{progressBar}': '.discoverProgress',

					'{results}': '[data-access-discovery-result]'
				}
			}, function(self) {
				return {
					init: function() {
						self.options.progressController = self.progressBar().addController(EasySocial.Controller.Progress);

						$.Joomla('submitbutton', function(task) {

							if (task == 'discover') {
								self.startDiscovering();
							}
						});
					},

					reset: function() {
						self.results().html('');

						self.options.progressController.reset();
					},

					addLog: function(msg) {
						$('<tr></tr>').append($('<td></td>').html(msg)).appendTo(self.results());
					},

					startDiscovering: function() {
						self.reset();

						// Discover the list of files.
						EasySocial.ajax('admin/controllers/access/scanFiles').done(function(files, message) {
							self.reset();

							// Set the files to the properties.
							self.options.files 	= files;

							if (self.options.files.length > 0) {
								// Begin progress.
								self.options.progressController.begin(self.options.files.length);

								// Add logging
								self.addLog(message);

								// Begin to loop through each files.
								self.startIterating();
							} else {
								// Update once.
								self.options.progressController.begin(1);
								self.options.progressController.completed('Discover Completed');

								// Append message to the result list.
								self.addLog($.language('COM_EASYSOCIAL_SCAN_COMPLETED'));
							}
						});
					},

					startIterating: function() {
						// Get the file from the shelf
						var file = self.options.files.shift();

						EasySocial.ajax('admin/controllers/access/installFile',
						{
							"file": file
						})
						.always(function(message){

							// As long as the files list are not empty yet, we still need to process it.
							if (self.options.files.length > 0) {
								// Update once.
								self.options.progressController.touch('...');

								// Append message to the result list.
								self.addLog(message);

								// Run this again.
								self.startIterating();
							} else {
								// Update once.
								self.options.progressController.touch('...');

								// Append message to the result list.
								self.addLog(message);

								// Append message to the result list.
								self.addLog($.language('COM_EASYSOCIAL_SCAN_COMPLETED'));
							}
						});
					}
				}
			});

			module.resolve();
		});
});
