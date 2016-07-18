EasySocial.module("site/explorer", function($) {

	var module = this;

	var CLASS_SELECTED = "is-selected",
		STATE_SELECTED = ".is-selected",
		CLASS_CHECKED = "is-checked",
		STATE_CHECKED = ".is-checked",
		STATE_NOT_SELECTED = ":not(.is-selected)",
		CLASS_ACTIVE = "is-active",
		STATE_ACTIVE = ".is-active",
		EVENT_FILE_SELECT = "fileSelect",
		EVENT_FILE_DESELECT = "fileDeselect",
		EVENT_FOLDER_ACTIVATE = "folderActivate",
		EVENT_FOLDER_DEACTIVATE = "folderDeactivate",
		EVENT_FILE_INSERT = "fileInsert",
		EVENT_FOLDER_INSERT = "folderInsert",
		EVENT_FILE_REMOVE = "fileRemove",
		EVENT_FOLDER_REMOVE = "folderRemove",
		EVENT_FILE_USE = "fileUse",
		EVENT_SERVICE_REQUEST = "serviceRequest";

	$.template("explorer/folder", '<div class="fd-explorer-folder" data-id="[%== data.id %]">[%== data.name %]<a href="javascript: void(0);" class="fd-folder-remove-button" data-fd-explorer-delete-folder-button><i class="fa fa-remove"></i></a></div>');

	$.template("explorer/fileGroup", '<div class="fd-explorer-file-group" data-folder="[%== data.id %]" data-plupload-dropsite></div>');

	$.template("explorer/file", '<div class="fd-explorer-file" data-id="[%== data.id %]">[%== data.name %]</div>');

	// TODO: Move this to Foundry
	EasySocial.require()
		.script("site/explorer/uploader")
		.done();

	$.Controller("Explorer",
	{
		pluginName: "explorer",
		hostname: "explorer",

		defaultOptions: {
			view: {
				folder: "explorer/folder",
				fileGroup: "explorer/fileGroup",
				file: "explorer/file"
			},

			layout: {
				fileItemHeight: 52
			},

			uid: null,
			type: null,
			disableValidation: false,
			mockError: false,
			controllerName: null,
			
			"{browser}"    : ".fd-explorer-browser",
			"{viewport}"   : ".fd-explorer-viewport",
			"{folderGroup}": ".fd-explorer-folder-group",
			"{folder}"     : ".fd-explorer-folder",
			"{fileGroup}"  : ".fd-explorer-file-group",
			"{file}"       : ".fd-explorer-file",
			"{button}"     : "[data-fd-explorer-button]",

			"{mockError}"  : "[name=mock_error]",
			"{disableValidation}": "[name=disable_validation]",
			"{fileInput}"  : "[name=file]",
			"{logs}"       : "[data-alertlog]",
			"{togglefunky}": ".togglefunky",
			"{funky}": ".fd-explorer-funky",
			"{serviceState}": ".service-state",


			"{selectAllCheckbox}": "[data-fd-explorer-select-all]",
			"{selectCheckbox}"   : "[data-fd-explorer-select]",

			"{deleteButton}": "[data-fd-explorer-delete-button]",
			"{deleteFolderButton}": "[data-fd-explorer-delete-folder-button]"
		}
	},
	function(self, opts, base) { return {

		init: function() {

			// Set the type and uid
			self.options.type = self.element.data('type');
			self.options.uid  = self.element.data('uid');
			self.options.controllerName = self.element.data('controller-name');

			// Extend with uploader plugin
			// TODO: Move this to Foundry
			$.module("easysocial/site/explorer/uploader")
				.done(function(controller){
					self.addPlugin("uploader", controller);
				});

			// Get folder list
			self.services.getFolders()
				.done(function(data){

					// Generate folder list
					self.insertFolder(data);

					// Activate first folder
					self.folder(":first").click();
				});

			self.viewport().on("scroll", $.debounce(self.viewportScroll, 250));
		},

		baseParams: function() {

			return {
				uid: base.data("uid"),
				type: base.data("type")
			};
		},

		exception: function(ex) {

			var logs = self.logs();

			if (!$.isPlainObject(ex)) {
				ex = {
					message: ex,
					type: "error"
				};
			};

			switch (ex.type) {

				case "error":
					logs.switchClass("alert-error")
						.html(ex.message);
					EasySocial.debug && console.error(ex.message);
					break;

				case "warning":
					logs.switchClass("alert-warning")
						.html(ex.message);
					EasySocial.debug && console.warn(ex.message);
					break;

				case "success":
					logs.switchClass("alert-success")
						.html(ex.message);
					EasySocial.debug && console.log(ex.message);
					break;

				case "info":
				default:
					logs.switchClass("alert-info")
						.html(ex.message);
					EasySocial.debug && console.log(ex.message);
					break;
			}

			return ex;
		},

		library: {
			file: {},
			folder: {}
		},

		data: function(type, id) {

			return self.library[type][id];
		},

		addData: function(type, data) {

			// Normalize arguments
			if ($.isPlainObject(data)) data = [data];

			if (!$.isArray(data)) {
				return self.exception("Unable to add " + type + " to library due to invalid data given.");
			}

			$.each(data, function(i){

				if (!$.isPlainObject(this)) {
					self.exception("Skipping invalid " + type + " data at index " + i + ".");
				}

				// TODO: Server should return proper 0 value.
				if (this.id===null) this.id = 0;

				/*
				var existing = self.library[type][this.id];
				if (existing) {
					self.exception("Replacing existing " + type + " in library for id " + this.id + ".");
				}
				*/

				self.library[type][this.id] = this;
			});
		},

		removeData: function(type, id) {

			delete self.library[type][id];
		},

		// Navigation
		"{folder} click": function(folder) {

			self.activateFolder(folder.data("id"));
		},

		currentFolder: function() {

			return self.folder(STATE_ACTIVE).data("id");
		},

		activateFolder: function(id) {

			var folder = self.folder().filterBy("id", id),
				data = self.data("folder", id);

			if (folder.length < 1) return;

			// Deactivate currently active folder
			self.deactivateFolder(self.folder(STATE_ACTIVE).data("id"));

			// Activate this folder
			folder.addClass(CLASS_ACTIVE);
			self.trigger(EVENT_FOLDER_ACTIVATE, [id, folder, data]);
		},

		deactivateFolder: function(id) {

			var folder = self.folder().filterBy("id", id),
				data = folder.data("folder");

			if (folder.length < 1) return;

			folder.removeClass(CLASS_ACTIVE);
			self.trigger(EVENT_FOLDER_DEACTIVATE, [id, folder, data]);
		},

		selectedFolder: function() {

			return self.currentFolder();
		},

		"{file} click": function(file, event) {

			var multiple = event.metaKey || event.ctrlKey;

			// Deselect existing selection if we're not
			// selecting multiple selection.
			if (!multiple) self.deselectAllFiles();

			// Select or deselect file
			self.toggleFile(file.data("id"));
		},

		toggleFile: function(id) {

			var file = self.file().filterBy("id", id),
				method = file.hasClass(CLASS_SELECTED) ? "deselectFile" : "selectFile";

			if (file.length < 1) return;

			self[method](id);
		},

		selectFile: function(id) {

			var file = self.file().filterBy("id", id),
				data = self.data("file", id);

			if (file.length < 1) return;

			file.addClass(CLASS_SELECTED);
			// file.find(self.selectCheckbox.selector).prop("checked", true);

			self.trigger(EVENT_FILE_SELECT, [id, file, data]);
		},

		deselectFile: function(id) {

			var file = self.file().filterBy("id", id),
				data = file.data("file", id);

			if (file.length < 1) return;

			file.removeClass(CLASS_SELECTED);
			// file.find(self.selectCheckbox.selector).prop("checked", false);

			self.trigger(EVENT_FILE_DESELECT, [id, file, data]);
		},

		selectAllFiles: function() {

			self.file(STATE_NOT_SELECTED).each(function(){
				self.selectFile($(this).data("id"));
			});
		},

		deselectAllFiles: function() {

			self.file(STATE_SELECTED).each(function(){
				self.deselectFile($(this).data("id"));
			});
		},

		selectedFile: function() {

			return self.selectedFiles()[0];
		},

		selectedFiles: function() {

			var files = [],
				selectedFiles;

			// TODO: Need to rethink this.
			// Prefer checked files over selected files
			var selectedFiles = self.file(STATE_CHECKED);

			if (selectedFiles.length < 1) {
				selectedFiles = self.file(STATE_SELECTED);
			}

			selectedFiles
				.each(function(){
					var id = $(this).data("id");
					files.push(id);
				});

			return files;
		},

		insertFolder: function(data) {

			// Normalize arguments.
			if (!$.isArray(data)) data = [data];

			// Validate data.
			var sample = data[0];

			if (!$.isPlainObject(sample)) {
				return self.exception("Invalid folder data given to be inserted into the folder group.");

			}

			// Find folder group.
			var folderGroup = self.folderGroup();

			if (folderGroup.length < 1) {
				return self.exception("Could not locate folder group element.");
			}

			// Generate folders html in a bulk to speeed up DOM insertion.
			var folders = "";
			$.each(data, function(){

				// TODO: Server should return proper 0 value.
				if (this.id===null) this.id = 0;

				folders += self.view.folder(true, {data: this});
			});

			// Insert folders into folder group.
			folders = $.buildHTML(folders).appendTo(folderGroup);

			// Trigger folder insert event.
			self.trigger(EVENT_FOLDER_INSERT, [folders, data]);
		},

		prependFile: function(data) {

			if (!$.isPlainObject(data)) {
				return self.exception("Invalid file data given to be inserted.");
			}

			// Find file group
			var fileGroup = self.fileGroup().filterBy("folder", data.folder);

			if (fileGroup.length < 1) {
				return self.exception("Could not locate file group element for folder id " + data.folder + ".");
			}

			var html = data.html || self.view.file(true, {data: data});

			$.buildHTML(html)
				.data("finalized", true)
				.prependTo(fileGroup);
		},

		insertFile: function(data, id) {

			// Normalize arguments.
			if (!$.isArray(data)) data = [data];

			if (id===undefined) {

				// Validate data.
				var sample = data[0];

				if (!$.isPlainObject(sample)) {
					return self.exception("Invalid file data given to be inserted.");
				}

				id = sample.folder;
			}

			// Find file group
			var fileGroup = self.fileGroup().filterBy("folder", id);

			if (fileGroup.length < 1) {
				return self.exception("Could not locate file group element for folder id " + data.folder + ".");
			}

			var files = [];

			$.each(data, function(){

				var filedata = this;

				self.file().filterBy("id", this.id).each(function(){

					var file = $(this);

					if (file.data("finalized")) return;

					var html = $.buildHTML(filedata.html || self.view.file(true, {data: filedata})).data("finalized", true);

					file.replaceWith(html);

				});
			});

			// Generate files html in bulk to speed up DOM insertion.
			// var files = "";

			// $.each(data, function(){
			// 	files += self.view.file(true, {data: this});
			// });

			// Insert files into file group.
			// files = $.buildHTML(files).appendTo(fileGroup);

			// Trigger file insert event.
			self.trigger(EVENT_FILE_INSERT, [files, data]);
		},

		removeFolder: function(id) {

			// Remove folder.
			var folder = self.folder().filterBy("id", id), failed;

			if (folder.length < 1) {
				failed = self.exception("Could not locate to remove folder element for folder id " + id + ".");
			}

			folder.remove();

			// Remove file group of this folder.
			var fileGroup = self.fileGroup().filterBy("folder", id);

			if (fileGroup.length < 1) {
				failed = self.exception("Could not locate to remove file group element for folder id " + id + ".");
			}

			fileGroup.remove();

			self.trigger(EVENT_FOLDER_REMOVE, [id, failed]);
		},

		removeFile: function(id) {

			// Remove file.
			var file = self.file().filterBy("id", id), failed;

			if (file.length < 1) {
				failed = self.exception("Could not locate to remove file element for file id " + id + ".");
			}

			file.remove();

			self.trigger(EVENT_FILE_REMOVE, [id, failed]);
		},

		// Service
		service: function(hook, params, ajaxOptions) {

			self.serviceState().switchClass("state-busy");

			var task =
				EasySocial.ajax(
						base.data("url"),
						$.extend({
							hook: hook,
							error: opts.mockError
						}, params),
						ajaxOptions
					)
					.always(function(){
						// self.exception({
						// 	type: "info",
						// 	message: "Log message will show here."
						// })
						self.serviceState().switchClass("state-idle");
					})
					.fail(function(ex){
						self.exception(ex);
					});

			// Trigger an event for this service request
			self.trigger(EVENT_SERVICE_REQUEST, [hook, task, params]);

			return task;
		},

		"{button} click": function(button) {

			var hook = button.attr('data-fd-explorer-button'),
				method = self.services[hook];

			// Execute hook
			method && method();
		},

		services: {

			getFolders: function(params) {

				var defaultParams = {
					start: 0,
					limit: 100
				};

				var task =
					self.service("getFolders",
							$.extend(
								self.baseParams(),
								defaultParams,
								params
							)
						)
						.done(function(data){
							self.addData("folder", data);
						});

				return task;
			},

			addFolder: function(params) {

				if (!params) {
					params = {name: prompt($.language("COM_EASYSOCIAL_EXPLORER_ENTER_FOLDER_NAME"))};
				}

				var defaultParams = {
					name: ''
				};

				params = $.extend(self.baseParams() , defaultParams, params);

				if (!params.name && !opts.disableValidation) {
					self.exception({
						message: $.language("COM_EASYSOCIAL_EXPLORER_INVALID_FOLDER_NAME"),
						type: "error"
					});
					return;
				}

				var task =
					self.service("addFolder", params)
						.done(function(data){
							self.addData("folder", data);
							self.insertFolder(data);
						});

				return task;
			},

			removeFolder: function(params) {

				var defaultParams = {
					id: self.selectedFolder()
				};

				if (!opts.disableValidation && self.selectedFolder()===undefined) {
					self.exception("No folder selected");
					return;
				}

				var task =
					self.service("removeFolder", $.extend( self.baseParams() , defaultParams, params))
						.done(function(id){
							self.removeFolder(id);
						});

				return task;
			},

			getFiles: function(params) {

				var defaultParams = {
					start: 0,
					limit: 100
				};

				var task =
					self.service("getFiles", $.extend(self.baseParams() , defaultParams, params))
						.done(function(data){
							self.addData("file", data);
						});

				return task;
			},

			addFile: function(params) {

				// params: {id: id, file: file}
				var defaultParams = {
					id: self.currentFolder(),
					files: self.fileInput()
				};

				if (!opts.disableValidation && !self.fileInput().val()) {
					self.exception("No file chosen yet.");
					return;
				}

				var task =
					self.service("addFile", $.extend( self.baseParams(), defaultParams, params), {type: 'iframe'})
						.done(function(data){
							console.log("Upload returned data:", data);
							self.addData("file", data);
							self.prependFile(data);

							self.exception({
								message: "Added file " + data.name,
								type: "success"
							});
						});

				return task;
			},

			removeFile: function(params) {

				var defaultParams = {
					id: self.selectedFiles()
				};

				var params = $.extend(self.baseParams(), defaultParams, params);

				if (!opts.disableValidation && $.isArray(params.id) && params.length < 1) {
					self.exception("No file selected");
					return;
				}

				var task =
					self.service("removeFile", params)
						.done(function(id){

							if ($.isArray(id)) {
								$.each(id, function(){
									self.removeFile(this);
								});
							} else {
								self.removeFile(id);
							}

							self.selectAllCheckbox().prop("checked", false);
						});

				return task;
			},

			useFile: function() {

				var id = self.selectedFile(),
				 	file = self.file().filterBy("id", id),
					data = self.data("file", id);

				if (file.length < 1) return;

				self.trigger(EVENT_FILE_USE, [id, file, data]);
			},

			previewFile: function() {

				var id = self.selectedFile(),
				 	file = self.file().filterBy("id", id),
					data = self.data("file", id);

				var url = file.data("previewUri");
				if (!url) return;

				window.open(url, "_blank");
			}
		},

		// UI
		"{self} folderActivate": function(explorer, event, id, folder, data) {

			// Find file group.
			var fileGroups = self.fileGroup(),
				fileGroup = fileGroups.filterBy("folder", id);

			// Deactivate other groups
			fileGroups.removeClass(CLASS_ACTIVE);

			// If file group hasn't been created before
			if (fileGroup.length < 1) {

				// TODO: Server should return an empty map.
				if (!data.map) data.map = [];

				var map = '<div class="fd-explorer-file" data-id="' + data.map.join('">&nbsp;</div><div class="fd-explorer-file" data-id="') + '">&nbsp;</div>';

				// Create file group
				fileGroup =
					self.view.fileGroup({data: data})
						.html(map)
						.appendTo(self.viewport());

				// Get files from server
				self.services.getFiles({id: id})
					.done(function(data){

						// and insert file into file group
						self.insertFile(data, id);
					});
			}

			// Activate group
			fileGroup.addClass("is-active");
		},

		// "{viewport} scroll": function(viewport) {

			// TODO: Determine position of list

			// cosole.log("scrolling");
		// }

		viewportScroll: function() {

			var viewport = self.viewport()[0],
				viewportHeight = self.viewport().height(),
				scrollHeight = viewport.scrollHeight,
				scrollTop = viewport.scrollTop,
				top = scrollTop - viewportHeight,
				itemHeight = self.file(":nth(2)").outerHeight(true);

			// console.log(viewport.scrollHeight);

			var index = Math.floor(scrollTop / itemHeight),
				tolerance = 3,
				fileGroup = self.fileGroup(".is-active"),
				id = fileGroup.data("folder");

			// self.browser().addClass("is-loading");

			self.services.getFiles({id: id, start: index - tolerance})
				.done(function(data){
					self.insertFile(data, id);
				})
				.always(function(){
					// self.browser().removeClass("is-loading");
				});
		},

		"{togglefunky} click": function() {
			self.funky().toggle();
		},

		"{disableValidation} change": function(input) {

			opts.disableValidation = !!input.prop("checked");

			self.exception({
				message: "Client-side validation is " + ((!opts.disableValidation) ? "ON!" : "OFF!"),
				type: "info"
			});
		},

		"{mockError} change": function(input) {

			opts.mockError = !!input.prop("checked");
			self.exception({
				message: "Error mocking is " + ((opts.mockError) ? "ON!" : "OFF!"),
				type: "info"
			});
		},

		"{selectAllCheckbox} click": function(checkbox) {

			var checked = checkbox.prop("checked");

			self.fileGroup(STATE_ACTIVE)
				.find(self.selectCheckbox.selector)
				.prop("checked", checked)
				.trigger("change");
		},

		"{selectCheckbox} change": function(checkbox) {

			var file = self.file.of(checkbox),
				id = file.data("id"),
				checked = checkbox.prop("checked");

			file.toggleClass("is-checked", checked);
		},

		"{deleteButton} click": function(deleteButton) {

			var file = self.file.of(deleteButton),
				id   = file.data("id");

			// Request the user for confirmation before deleting
			EasySocial.dialog(
			{
				content : EasySocial.ajax( 'site/views/explorer/confirmDeleteFile' ,  { "id" : id } ),
				bindings:
				{
					"{deleteButton} click" : function()
					{
						EasySocial.dialog().close();
						self.services.removeFile({id: id});
					}
				}
			});

		},

		"{deleteFolderButton} click": function(deleteFolderButton , event ) {

			event.preventDefault();

			var folder = self.folder.of(deleteFolderButton),
				id = folder.data("id");

			// Core folder cannot be deleted
			if (id===0) return;

			// Request the user for confirmation before deleting
			EasySocial.dialog(
			{
				content : EasySocial.ajax( 'site/views/explorer/confirmDeleteFolder' ,  { "id" : id } ),
				bindings:
				{
					"{deleteButton} click" : function()
					{
						EasySocial.dialog().close();
						self.services.removeFolder({id: id});
					}
				}
			});

		}
	}});

	EasySocial.require()
		.language(
			"COM_EASYSOCIAL_EXPLORER_ENTER_FOLDER_NAME",
			"COM_EASYSOCIAL_EXPLORER_INVALID_FOLDER_NAME"
		)
		.done(function(){
			module.resolve();
		});
});
