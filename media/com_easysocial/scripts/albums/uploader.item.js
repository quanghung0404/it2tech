EasySocial.module("albums/uploader.item", function($) {

	var module = this;

	EasySocial.Controller("Albums.Uploader.Item",

	    {
	        defaultOptions: {
	        	"{status}"       : ".upload-status",
	            "{filename}"     : ".upload-filename",
	            "{progressBar}"  : ".upload-progress-bar",
	            "{percentage}"   : ".upload-percentage",
	            "{filesizeTotal}": ".upload-filesize-total",
	            "{filesizeLeft}" : ".upload-filesize-left",
	            "{details}"      : ".upload-details",
	            "{detailsButton}": ".upload-details-button",
	            "{removeButton}" : ".upload-remove-button",
	            "{message}"      : ".upload-message"
	        }
	    },

		// Instance properties
		function(self) { return {

			init: function() {

				self.id = self.element.attr("id");

				var file = self.file();

				// Set filename
				self.filename().html(file.name);

				// Set state
				self.setState("pending");

				// Set progress & filesize
				self.setProgress();

				var html4 = self.uploader.plupload.runtime=="html4";

				if ($.IE < 10 || html4) {
					// So upload item will display with indefinite progressbar
					self.element.addClass("indefinite-progress");
				}

				if (html4) {
					self.element.addClass("no-filesize");
				}
			},

	        file: function() {

	            var file = self.uploader.plupload.getFile(self.id) || self.options.file;

	            if (file) {
	            	var noFilesize = (file.size===undefined || file.size=="N/A");
	            	file.percentage = file.percent + "%";
	                file.filesize   = (noFilesize) ? "" : $.plupload.formatSize(file.size);
	                file.remaining  = (noFilesize) ? "" : $.plupload.formatSize(file.size - (file.loaded || 0));
	            }

	            return file;
	        },

	        setProgress: function() {

				var file = self.file(),
					percentage = file.percentage;

				// Never use 100% because users might think
				// the photo is completely uploaded when it might
				// still be working.
				// - Thanks Alex Heil.
				if (percentage=="100%") percentage = "99%";
				if (percentage=="0%") percentage = "1%";

				// Progress bar width
				self.progressBar()
					.width(percentage);

				// Progress bar percentage
				self.percentage()
					.html(percentage);

				// Total filesize
				self.filesizeTotal()
					.html(file.filesize);

				// Remaining filesize
				self.filesizeLeft()
					.html(file.remaining);
	        },

	        setState: function(state) {

				self.element
					.removeClass("pending preparing uploading failed done")
					.addClass(state);

				self.state = state;
	        },

			setMessage: function(message) {

			   	self.detailsButton()
			   		.attr("data-popbox", message);
			},

			"{removeButton} click": function(el, event) {

			    self.uploader.removeItem(self.id);
			}

	    }}
	);

	module.resolve();

});
