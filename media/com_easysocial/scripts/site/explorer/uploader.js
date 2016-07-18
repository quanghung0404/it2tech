EasySocial.module('site/explorer/uploader', function($) {

var module = this;

$.require()
	.library("plupload")
	.done(function(){

		var Controller =
		$.Controller("Explorer/Uploader",
		{
			defaultOptions: {

				settings: {},

				"{uploadItemGroup}": "[data-fd-explorer-upload-item-group]",
				"{uploadItem}"     : "[data-fd-explorer-upload-item]"
			}
		},
		function(self, opts, base) { return {

			init: function() {

				base.plupload(opts.settings);

				// Set a reference to plupload
				self.plupload = base.plupload("controller").plupload;
			},

			settings: function(key, val) {

				var settings = self.plupload.settings;

				// Setter
				if (val!==undefined) {
					settings[key] = val;
				}

				// Getter
				return (key) ? settings[key] : settings;
			},

			"{self} folderActivate": function(explorer, event, id, folder, data) {


			},

			"{self} BeforeUpload": function(el, event, uploader, file) {

				var url =
					$.uri(EasySocial.ajaxUrl)
						.addQueryParam("controller", self.explorer.options.controllerName)
						.addQueryParam("task", "explorer")
						.addQueryParam("id", self.explorer.currentFolder())
						.addQueryParam("no_html", 1)
						.addQueryParam("format", "json")
						.addQueryParam("hook", "addFile")
						.addQueryParam("tmpl", "component")
						.addQueryParam("uid", base.data("uid"))
						.addQueryParam("type", base.data("type"))
						.addQueryParam(EasySocial.token(), 1)
						.toString();

				self.settings("url", url);
			},

			"{self} FilesAdded": function(el, event, uploader, file) {

				self.plupload.start();
			},

			"{self} UploadFile": function() {

				clearTimeout(self.loadTimer);
				base.addClass("is-loading");
			},

			"{self} FileUploaded": function(el, event, uploader, file, data) {

				self.loadTimer = setTimeout(function(){
					base.removeClass("is-loading");
				}, 1000);

				var explorer = self.explorer;

				// If the response is not a valid object
				if (!$.isPlainObject(data)) {
					self.setMessage("Server did not return proper data after uploading.", "error");
					return;
				}

				explorer.addData("file", data);
				explorer.prependFile(data);
			},

			"{self} FileError": function(el, event, uploader, file, response) {

				base.removeClass("is-loading");

				if ($.isPlainObject(response)) {
					self.setMessage(response.message, "error");
				}
			},

			"{self} Error": function(el, event, uploader, error) {

				base.removeClass("is-loading");

				self.setMessage(error.message, "error");
			}

		}});

		module.resolve(Controller);
	});

});
