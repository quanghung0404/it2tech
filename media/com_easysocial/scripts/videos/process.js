EasySocial.module('videos/process', function($) {

	var module = this;

	EasySocial.Controller('Videos.Process', {
		defaultOptions: {
			"{progressBar}": "[data-video-progress-bar]",
			"{progressResult}": "[data-video-progress-result]"
		}
	}, function(self, opts, base) { return {
		
		init: function() {

			// Set the global options
			opts.id = base.data('id');

			self.processVideo();
		},

		processVideo: function() {

			// Initialize the video processing here
			EasySocial.ajax('site/controllers/videos/process', {
				"id": opts.id
			}).done(function() {

				// Run check status
				self.status(opts.id);
			});
		},

		status: function(videoId) {
			// Initialize the video processing here
			EasySocial.ajax('site/controllers/videos/status', {
				"id": videoId
			}).done(function(permalink, progress) {
				
				if (progress == 'done') {
					self.progressBar().css('width', '100%');
					self.progressResult().html('100%');

					// Redirect the user upon completion
					window.location = permalink;
					
					return;
				}

				var percentage = progress + '%';

				// Reiterate the same method again until it's completed.
				self.progressBar().css('width', percentage);
				self.progressResult().html(percentage);

				self.status(videoId);

				return;
				// // Set the progress bar to at least 10%
				// self.progressBar().css('width', '10%');

				// // Run check status
				// self.status(logFile);
			});
		}

	}});

	module.resolve();
});
