EasyBlog.module("mediamanager", function($) {

var module = this;

var isSearching = "is-searching";
var isSearchResult = "is-search-result";
var hasSearchResults = "has-search-results";
var isNotFound = "is-notfound";

var isLoading = "is-loading";
var isFailed = "is-failed";

EasyBlog.require()
.library(
	"pageslide",
	"plupload2",
	"ui/draggable"
)
.script(
	"mediamanager/uploader"
).done(function() {

var controller = EasyBlog.Controller("MediaManager", {

	hostname: "mediaManager",

	pluginExtendsInstance: true,

	elements: [
		"[data-eb-mm-{frame|close-button}]",
		"[data-eb-mm-{pages|places|place|hints}]",
		"[data-eb-mm-{folder|filegroup|filelist}]",
		"[data-eb-mm-{folder-back-button|folder-upload-button|folder-upload-dropzone}]",

		"[data-eb-mm-{file|filegroup|filegroup-header|file-title}]",
		"[data-eb-mm-{info-viewport|info-container|info|info-back-button|info-filename}]",
		"^media [data-eb-mm-{workarea|document}]",
		"[data-eb-mm-{show-new-files-button|show-all-files-button}]",

		"[data-eb-mm-{search-panel|search-input|search-toggle-button}]",
		"[data-eb-mm-{upload-thumbnail|upload-name-upload-size}]",
		"[data-eb-mm-{folder-upload-current-file|folder-upload-stat|folder-upload-completed|folder-upload-total|folder-upload-progress-bar}]",
		"[data-eb-mm-{new-file-count}]",

		"[data-eb-mm-{filegroup-show-all-button}]",
		"[data-eb-mm-{file-remove-button|file-insert-button|file-rename-button|file-move-button}]",

		"[data-eb-mm-{folder-content-panel}]",
		"[data-eb-mm-{foldertree|tree|tree-item}]",

		"[data-eb-mm-{show-move-dialog-button|move-filename}]",
		"[data-eb-mm-{folder-rename-button|folder-move-button|folder-remove-button}]",
		"[data-eb-mm-{open-button}]",
		"[data-eb-mm-{upload-template}]",

		"[data-eb-mm-{folder-title}]",

		"[data-eb-mm-{browse-button}]",
		"[data-eb-mm-{selecting-cancel}]",
		"[data-eb-mm-{go-to-folder|close-message-button}]",

		"[data-eb-mm-{insert-gallery}]",

		// Users pagination
		"[data-eb-mm-{pagination-previous|pagination-next|pagination-current|pagination-total}]"
	],

	defaultOptions: {

		types: {
			image: ["jpg","jpeg","png","gif"],
			video: ["3gp","m4v","mp4", "flv", "wmv", "mp3", "webm", "swf"],
			audio: ["mp3","m4a","aac","ogg"]
		},

		templates: {},

       	"{thumbnail}": "[data-eb-mm-file].type-image i[data-thumbnail]",
        "{flickrButton}": "[data-flickr-login]",
        "{createFolder}": "[data-eb-mm-create-folder]"
	}
}, function(self, opts, base, composer) { return {

	init: function() {

		// Get the upload template
		opts.templates.upload = self.uploadTemplate().detach().html();

		// Remap extension to type for fast lookup
		$.each(opts.types, function(key, values){
			var i = 0, value;
			while (value = values[i++]) {
				self.types[value] = key;
			}
		});

		// Add core plugins
		self.addPlugin("uploader");

		// Keep references to these elements
		self.places.node = self.places()[0];
		self.hints.node  = self.hints().detach()[0];

		// Expose myself
		window.ebmm = self;
		EasyBlog.MediaManager = self;
	},

	"{self} composerReady": function() {

		composer = EasyBlog.Composer;
	},

	//
	// URIs
	//

	types: {"": "file"},

	getExtension: function(uri) {

		var uri = self.getUri(uri),
			last = uri.lastIndexOf('.');

		return last > -1 ? uri.slice(last + 1).toLowerCase() : "";
	},

	getType: function(uri) {
		return self.types[self.getExtension(uri)] || "file";
	},

	getKey: function(uri) {

		// Key given, just return key.
		return uri.substring(0,1)=="_" ? uri :
			// Else convert to uri to key
			'_' + $.moxie.btoa(uri)
			.replace(/\+/g, ".")
			.replace(/\=/g, "-")
			.replace(/\//g, "~");
	},

	// getUri(key)
	// getUri(folder)
	getUri: function(key) {

		if (key instanceof $) {
			var folder = key;
			key = folder.data("key") || "";
		}

		// Uri given, just return uri.
		return key.substring(0,1)!=="_" ? key :
			// Else convert key to uri
			$.moxie.atob(key
				.slice(1)
				.replace(/\./g, "+")
				.replace(/\-/g, "="));
	},

	getParent: function(uri) {

		var uri = self.getUri(uri),
			last = uri.lastIndexOf("/");

		return last > -1 ? uri.slice(0, last) : null;
	},

	getFilename: function(uri) {

		var uri = self.getUri(uri);
		var last = uri.lastIndexOf("/");

		return last > -1 ? uri.slice(last + 1) : uri;
	},

	isRoot: function(uri) {

		return self.getUri(uri).indexOf("/") < 0;
	},

	getCurrentPostUri: function() {
		return "post:" + EasyBlog.Composer.getPostId();
	},

	//
	// Library
	//

	medias: {},

	mediaLoaders: {},

	getMedia: function(uri) {

		var key = self.getKey(uri);
		var mediaLoader = self.mediaLoaders[uri];

		if (!mediaLoader) {

			mediaLoader =
				EasyBlog.ajax('site/views/mediamanager/media', {key: key})
					.done(function(media) {
						var uri = media.uri;
						self.setMedia(uri, media);
					});
		}

		return mediaLoader;
	},

	setMedia: function(uri, media) {

		self.medias[uri] = media;
		self.mediaLoaders[uri] = $.Deferred().resolve(media);
	},

	getVariations: function(uri) {

		var media = self.medias[uri];

		if (!media) return;

		return media.meta.variations || {};
	},

	getVariation: function(uri, candidates) {

		// Convert presets into candidates
		if ($.isString(candidates)) {

			var variationKey = candidates;

			switch (variationKey) {

				case "icon":
					candidates = [
						"system/icon", // EB
						"system/small", // EB
						"system/small 320", // FK
						"system/thumbnail", // ES, JS
						"system/original"
					];
					break;

				case "small":
					candidates = [
						"system/small", // EB
						"system/small 320", // FK
						"system/thumbnail", // ES, JS
						"system/original"
					];
					break;

				case "medium":
					candidates = [
						"system/medium",
						"system/large",
						"system/original"
					];
					break;

				case "thumbnail":
					candidates = [
						"system/thumbnail", // EB, ES, JS, FK
						"system/original"
					];
					break;

				case "large":
					candidates = [
						"system/large", // EB, ES, FK
						"system/original" // JS
					];
					break;

				case "original":
					candidates = [
						"system/original"
					];
					break;

				default:
					candidates = [
						variationKey,
						"system/original"
					];
					break;
			}
		}

		// Get variation
		var variations = self.getVariations(uri);
		var variation;

		$.each(candidates, function(i, variationKey){

			if ($.has(variations, variationKey)) {
				variation = variations[variationKey];
				return false;
			}
		});

		return variation;
	},

	createVariation: function(uri, name, width, height) {

		var key = self.getKey(uri);

		var task =
			EasyBlog.ajax("site/views/mediamanager/createVariation", {
				key: key,
				name: name,
				width: width,
				height: height
			})
			.done(function(media){

				// Update cache with update media object
				self.setMedia(uri, media);
			});

		return task;
	},

	removeVariation: function(uri, name) {

		var key = self.getKey(uri);

		var task =
			EasyBlog.ajax("site/views/mediamanager/deleteVariation", {
				"key": key,
				"name": name
			})
			.done(function(media){

				// Update media cache
				self.setMedia(uri, media);
			});

		return task;
	},

	mode: 'standard',
	currentFilterType: 'all',
	currentBrowseButton: null,
	currentFolderUri: null,

	closeMediaManager: function() {

		composer.views.hide("media");
		composer.frame().removeClass("layout-media");

		// Always reset the current filter type
		self.currentFilterType = 'all';
		self.mode = 'standard';
		self.currentBrowseButton = null;

		// Remove any is-selecting from the container
		self.frame().removeClass('is-selecting');

		// Set the filter type
		self.frame().switchClass('filter-' + self.currentFilterType);
	},

	openMediaManager: function(uri, filter) {

		composer.views.show("media");

		// Set the filter
		if (filter) {
			self.currentFilterType = filter;
		}

		// If a start folder is provided, navigate to the respective url
		if (uri) {
			self.navigate(uri);
		}
	},

	//
	// Navigation
	//

	currentUri: "places",

	places: {
		node: null
	},

	"{place} click": function(place) {
		var id = place.data('id');
		var key = place.data('key');
		var uri = self.getUri(key);

		if (id == 'post') {
			uri = self.getCurrentPostUri();
		}

		self.navigate(uri);
	},

	open: function(uri) {

		self.navigate(uri);
	},

	navigate: function(uri) {

		// If currentUri is the uri to be opened, stop.
		var currentUri = self.currentUri;

		if (currentUri === uri) {
			return;
		}

		var content;

		// If we are going out from a subfolder, the direction is reversed.
		var direction = currentUri.indexOf(uri) === 0 ? "prev" : "next";

		// When a uri is opened from post or user list, it needs a referer.
		var referer = /post(s)*|user(s)*/.test(currentUri) ? currentUri : null;

		switch (uri) {

			case "places":
				content = self.places.node;
				direction = "prev";
				break;

			case "posts":

				content = self.getHint('loading');

				// If the user is coming from the users listings, direction should be previous
				if (/post\:(.*)/.test(referer)) {
					direction = "prev";
				}

				EasyBlog.ajax('site/views/mediamanager/posts', {})
					.done(function(html) {
						content.replaceWith(html);
					});
				break;

			case "users":
				content = self.getHint('loading');

				// If the user is coming from the users listings, direction should be previous
				if (/user\:(.*)/.test(referer)) {
					direction = "prev";
				}

				EasyBlog.ajax('site/views/mediamanager/users', {})
					.done(function(html){
						content.replaceWith(html);
					});

				break;

			case "Flickr":
			case "flickr":

				content = self.getHint('loading');

				EasyBlog.ajax('site/views/mediamanager/flickr', {})
					.done(function(html){
						content.replaceWith(html);

						self.revealThumbnails();
					});
				break;

			case "easysocial":
			case "jomsocial":

				// Get the loader
				content = self.getHint('loading');

				EasyBlog.ajax('site/views/mediamanager/folder', {
					"key": self.getKey(uri)
				}).done(function(html) {

					content.replaceWith(html);

					var folder = self.getFolder(uri);

					self.trigger("mediaFolderDisplay", [folder]);
				});
				break;

			// easysocial:1
			// jomsocial:1
			// post:32
			// user:64
			// shared
			default:

				// Get content from existing nodes
				var content = self.getFolder(uri);

				// If content does not exist, load it first.
				if (content.length < 1) {

					// Set loading hint as content to show.
					content = self.getHint("loading");

					contentNode = content[0];

					// Load folder content
					self.loadFolder(uri)
						.done(function(folder){
							// Replace hint with folder content
							$(contentNode).replaceWith(folder);

							self.revealThumbnails();

							// Mark that this content is
							// opened from a referer.
							if (referer) {
								folder.data("referer", referer);
							}
						});

				} else {
					self.refreshFolder(content);
				}

				break;
		}

		// Don't display anything if there's no content
		if (!content) {
			return;
		}

		// Set uri as current uri
		self.currentUri = uri;

		// Display content
		self.display(content, direction);

		// mediaNavigate (uri, fromUri, content)
		self.trigger("mediaNavigate", [uri, currentUri, content]);
	},

	display: function(content, direction) {

		self.pages()
			.pageslide(content, direction);

		// This shouldn't be here.
		// Fixes layout not reverted when going into move dialog
		self.destroyInfo();
	},

	"{folderBackButton} click": function(button) {

		// Get the folder
		var folder = self.folder.of(button);

		// Get the current folder's uri
		var uri = self.getUri(folder.data("key"));

		// If there is a referer, go to referer.
		var referer = folder.data("referer");

		// Always removed the moved message when user navigates to another location
		folder.removeClass('folder-moved')
			.removeClass('file-moved');

		if (referer) {
			targetUri = referer;

		} else if (self.isRoot(uri)) {
			// If this is root folder, go to places.
			targetUri = "places";
		} else {

			// If this is a subfolder, go to parent.
			targetUri = self.getParent(uri);
		}

		self.navigate(targetUri);
	},

	"{infoBackButton} click": function(button) {

		var info = self.info.of(button)

		var uri = self.getUri(info.data("key"));
		var parentUri = self.getParent(uri);

		// Destroy info
		self.destroyInfo();

		// Navigate to parent uri
		self.navigate(parentUri);
	},

	//
	// Hints
	//
	hints: {
		node: null
	},

	getHint: function(name) {
		var hint = $(self.hints.node).find(".hint-" + name).clone();

		// Monkey patch
		if (name=="loading" || name=="error") {
			self.folderBackButton.inside(hint)
				.attr("data-key", self.getKey(self.currentUri));
		}

		return hint;
	},

	getUploadTemplate: function(file) {
		var template = $(opts.templates.upload);

		template.attr('data-id', file.id);
		template.find('[data-eb-mm-upload-name]').html(file.name);


		return template[0];
	},

	//
	// Folder
	//
	folder: {

		// This object cache folder nodes
		nodes: {},

		// Fast alternative to self.folder().where("key", key);
		get: function(key) {
			var key = self.getKey(key);
			var selector = self.folder.selector + "[data-key='" + key + "']";
			var folder = base.find(selector);
			return folder;
		},

		insert: function(folder, html) {

            // Display folders on the current folder view.
            folder.removeClass("is-empty")
                .find(".eb-mm-filegroup.type-folder")
                .removeClass("is-empty");

			// Prepend the new folder html structure
			self.getFilelist(folder, 'folder')
				.prepend(html);
		}
	},

	getCurrentFolder: function() {

	},

	getFolder: function(uri) {
		return $(self.folder.nodes[uri]);
	},

	setFolder: function(uri, folder) {

		self.folder.nodes[uri] = folder[0];
	},

	folderLoaders: {},

	loadFolder: function(uri) {

		var folderLoader =
			self.getMedia(uri)
				.then(function(media){

					// There might be whitespace in folder html,
					// which we'll end up with a jquery element with 2 nodes,
					// one is the folder node, one is the text node,
					// this ensure we're really pointing to the folder node.
					var folder = $(media.folder).filter(self.folder.selector);

					// Store folder
					self.setFolder(uri, folder);

					// Init folder
					self.initFolder(folder);

					return folder;
				});

		// Cache folder loader
		self.folderLoaders[uri] = folderLoader;

		return folderLoader;
	},

	initFolder: function(folder) {

		var uri = self.getUri(folder);

		var initScript = $.Script(function(){

			// If this folder has been initialized, stop.
			if (folder.data("inited")) {
				return;
			}

			// If user can upload to this folder,
			// initialize uploader on this folder.
			if (folder.hasClass("can-upload")) {
				self.uploader.register(folder);
			}

			// Set inited flag
			folder.data("inited", true);

			// Trigger mediaFolderInit event.
			self.trigger("mediaFolderInit", [uri, folder]);

			// Refresh content
			self.refreshFolder(folder);
		});

		// When the folder is appended,
		// initialize the folder.
		folder.append(initScript);
	},

	renameFolder: function(key, newFileName) {

		EasyBlog.ajax("site/views/mediamanager/rename", {
				"key": key,
				"filename": newFileName
			}).done(function(fileHtml, infoHtml, folderHtml) {

				// When the folder is renamed, it's contents needs to be replaced.
				var oldFolder = self.folder.get(key);

				var folder = $(folderHtml);

				self.initFolder(folder);

				oldFolder.replaceWith(folder);

				var uri = self.getUri(folder);
				self.currentUri = uri;

				var parentUri = self.getParent(uri);
				var parentFolder = self.getFolder(parentUri);
				var oldFile = parentFolder.find(self.file.getSelector(key));
				oldFile.replaceWith(fileHtml);

			}).fail(function(file) {

			});
	},

	refreshFolder: function(folder) {

		// Sync upload items
		self.syncUploadItems(folder);

		self.trigger("mediaFolderRefresh", [folder]);
	},

	//file:
	// Upload Items
	//

	uploadItems: {},

	createUploadItem: function(file) {

		var uploadItem = self.getUploadTemplate(file);

		self.uploadItems[file.id] = uploadItem;

		// If file is an image
		if (file.type.match("image")) {

			var image = new $.moxie.Image();

			image.onload = function() {

				// Resize to ~100px
				image.downsize(100);

				// Set as thumbnail
				self.uploadThumbnail.inside(uploadItem)
					.css("backgroundImage", $.cssUrl(image.getAsDataURL()));
			}

			image.load(file.getSource());
		}

		// Register item to uploader so uploader
		// for automatic file to item data binding.
		self.uploader.addItem(file, uploadItem);

		return uploadItem;
	},

	addUploadItem: function(folder, file) {

		// Create item
		var uploadItem = self.uploadItems[file.id] || self.createUploadItem(file);
		var type = self.getType(file.name);

		// Add item to filelist
		// This will always add to the beginning.
		self.getFilelist(folder, type)
			.prepend(uploadItem);

		self.filegroup.inside(folder)
			.filter(".type-" + type)
			.addClass("has-new-files");
	},

	syncUploadItems: function(folder) {

		var uri = folder.data("uri"),
			files = self.uploader.getFiles(uri),
			uploadItems = {image: [], video: [], audio: [], file: []},
			uploadItem,
			i = 0, type;

		// This will populate an array of upload items.
		while (file = files[i++]) {

			uploadItem = self.uploadItems[file.id];

			if (!uploadItem) {
				uploadItem = self.createUploadItem(file);
			}

			type = self.getType(file.name);

			items[type].push(uploadItem);
		}

		// This will insert upload items in bulk
		// into the proper filelist on the folder.
		for (type in uploadItems) {

			var items = uploadItems[type],
				empty = items.length < 1,
				// Get filelist
				filelist =
					// Toggle is-uploading state
					self.getFilelist(folder, type)
						.toggleClass("is-uploading", !empty);

			if (!empty) {
				// Add upload items to filelist
				filelist.prepend(items);
			}
		}
	},

	"{selectingCancel} click": function(el, event) {

		// Hide the media manager
		self.closeMediaManager();
	},

	"{browseButton} click": function(browseButton, event) {
		var filter = browseButton.data('eb-mm-filter');
		var startKey = browseButton.data('eb-mm-start-uri');
		var startUri = self.getUri(startKey);
		var places = browseButton.data('eb-mm-browse-place');
		
		// We need to know what is the purpose of this browse button is for.
		// If this is for post cover, we should let the media manager know
		// that there shouldn't be any image properties.
		var browseType = browseButton.data('eb-mm-browse-type');

        if (startUri == 'post') {
            startUri = self.getCurrentPostUri();
        }

        // Let the world know that the current mode is selecting an image
        self.mode = 'select';
        self.currentBrowseType = browseType;
        self.currentBrowseButton = browseButton;

        // Trigger an event so listeners could bind their event when selecting image starts
        self.currentBrowseButton.trigger("mediaSelectStart", [self.currentBrowseButton]);

		// When this is invoked, load up the mediamanager
		self.openMediaManager(startUri, filter);

		// The media manager could already be opened at this state of time so it's safer to filter it again
		self.frame().switchClass('filter-' + self.currentFilterType);

		// Apply is-selecting class on the container
		self.frame().addClass('is-selecting');

		if (places == 'local') {
			self.frame().addClass('show-local');
		} else {
			self.frame().removeClass('show-local');
		}

	},

	"{self} mediaUploaderFileFiltered": function(base, event, uploader, file) {

		var uri = uploader.uri,
			folder = self.getFolder(uri);

		if (folder.length < 1) return;

		// Add is-uploading class;
		folder.addClass("is-uploading");

        // Cheap hack
        folder.removeClass("is-empty")
            .find(".eb-mm-filegroup")
            .removeClass("is-empty");

		// If this folder is the active folder,
		// add upload item straight away.
		if (uri==self.currentUri) {
			self.addUploadItem(folder, file);

			// Show only upload items
			// folder.addClass("filter-new");
		}
	},

	"{self} mediaUploaderFileUploaded": function(base, event, uploader, file, data) {

		var response = data.response;
		var media = response.media;

		// Store media in library
		self.setMedia(media.uri, media);

		// Delay replacing on file item so user can see the progress bar moving
		setTimeout(function(){

			var uploadItem = self.uploadItems[file.id];

			// If upload item exists, replace it with file item
			if (uploadItem) {

				// Create file item
				var fileItem = $(media.file).addClass("is-new");

				// Replace upload item with file item
				$(uploadItem).replaceWith(fileItem);
			}

		}, 600);
	},

	"{self} mediaUploaderUploadComplete": function(base, event, uploader) {
	},

	"{self} mediaUploaderChange": function(base, event, uploader) {


		var Plupload = $.plupload2,
			uri = uploader.uri,
			folder = self.getFolder(uri),
			files = self.uploader.getFiles(uri),
			currentFile,
			completed = 0,
			total = 0,
			percent = 0;

		// Get currentFile, total, and percent.
		$.each(files, function(i, file){

			// Skip files that have failed or finalized
			if (file.finalized || file.status==Plupload.FAILED) return;

			// If this file is being uploaded, mark as current file.
			if (file.status==Plupload.UPLOADING) {
				currentFile = file;
			}

			// If this file is done uploading, increase completed count.
			if (file.status==Plupload.DONE) {
				completed++;
			}

			// Accumulate progress
			percent += file.percent;

			// Increase total count
			total++;
		});

		// If upload is in progress
		if (total > 0 && total!==completed) {

			// Activate folder upload bar
			folder.addClass("is-uploading");

			if (currentFile) {
				self.folderUploadCurrentFile()
					.html(currentFile.name);
			}

			self.folderUploadTotal()
				.html(total);

			self.folderUploadCompleted()
				.html(completed);

			self.folderUploadProgressBar()
				.width((percent / (total * 100)) * 100 + "%");

		} else {

			// Deactivate folder upload bar
			folder.removeClass("is-uploading");

			$.each(files, function(i, file){
				file.finalized = true;
			});
		}

		// Update recent file count
		self.newFileCount()
			.html(self.file(".is-new").length);
	},

	getFilelist: function(folder, type) {

		var filelist =
			self.filegroup.inside(folder)
				.filter(".type-" + type)
				.find(self.filelist);

		return filelist;
	},

	//
	// Filegroup & Files
	//

	file: {
		active: $(),

		// This is a faster alternative than doing self.file().where("key", key);
		get: function(key) {

			var key = self.getKey(key),
				selector = self.file.getSelector(key);

			return base.find(selector);
		},

		getSelector: function(key) {

			return self.file.selector + "[data-key='" + key + "']";
		},

		activate: function(file) {

			// Remove any active file
			self.file.deactivate();

			// Activate the file and set the current active file
			self.file.active = file.addClass('active');
		},

		deactivate: function() {
			// Remove the current active file
			self.file.active.removeClass('active');
		},

		rename: function(key, filename) {

			EasyBlog.ajax("site/views/mediamanager/rename", {key: key, filename: filename})
				.done(function(fileHtml, infoHtml){

					// TODO: Renamed files needs to be deleted from cached filelist html.
					self.file.get(key)
						.replaceWith(fileHtml);

					self.info.get(key)
						.replaceWith(infoHtml);
				})
				.fail(function(file){

				});
		}
	},

	"{file} click": function(file) {

		var key = file.data("key");
		var uri = self.getUri(key);

		// Deactivate current activate file
		self.file.activate(file);

		// If this is a folder, navigate to the folder.
		if (file.hasClass("type-folder")) {
			self.navigate(uri);
			return;
		}

		// If this is a file, show info.
		var folder = self.folder.of(file);

		self.showInfo(uri);
	},

	"{filegroupHeader} click": function(filegroupHeader) {

		var filegroup = self.filegroup.of(filegroupHeader);

		filegroup[
			filegroup.hasClass("is-collapsed") ?
				"removeTransitionClass" :
				"addTransitionClass"
		]("is-collapsed", 500);
	},

	"{showNewFilesButton} click": function(button) {

		self.folder.of(button)
			.addClass("filter-new");
	},

	"{showAllFilesButton} click": function(button) {

		self.folder.of(button)
			.removeClass("filter-new");
	},

	//
	// Info
	//
	info: {

		get: function(uri) {

			// Unlike file.get, no optimization is necessary here.
			var key = self.getKey(uri);

			return self.info().where("key", key);
		}
	},

	showInfo: function(uri) {

		// Destroy existing info if necessary
		self.destroyInfo();

		// Trigger mediaInfoShow event
		self.trigger("mediaInfoShow", [uri]);

		// Get loading hint
		var loadingHint = self.getHint("loading");

		// Get the parent folder to set it on the
		// back button of the loading hint.
		var parentUri = self.getParent(uri);
		var parentKey = self.getKey(parentUri);

		self.folderBackButton.inside(loadingHint)
			.attr("data-key", parentKey);

		self.pages()
			.pageslide(loadingHint, "next");

		self.currentUri = uri;

		// When media is loaded
		self.getMedia(uri)
			.done(function(media){

				// Replace loading hint with info
				var info = $(media.info);
				loadingHint.replaceWith(info);

				// If we are browsing for post cover, media info shouldn't be displayed
				if (self.currentBrowseType != 'cover') {
					// Trigger mediaInfoDisplay event
					self.trigger("mediaInfoDisplay", [info, media]);
				}

			})
			.fail(function(){

				// Show error hint
				var errorHint = self.getHint("error");
				loadingHint.replaceWith(errorHint);
			});
	},

	destroyInfo: function() {

		// Get info and info viewport
		var info = self.info();

		// Get uri
		var uri = info.data("uri");

		// Trigger mediaInfoHide event
		self.trigger("mediaInfoHide", [uri]);

		// If no uri, nothing to destroy.
		if (!uri) return;

		// Get media
		var media = self.medias[uri];

		// Trigger mediaInfoDestroy event
		self.trigger("mediaInfoDestroy", [info, media]);
	},

	//
	// Sign in with Flickr button
	//

	"{flickrButton} click": function(button) {
		var url = button.data('url');
		var folder = self.folder.of(button);

		var width = 960;
		var height = 650;
        var left = (screen.width/2)-(width /2);
        var top = (screen.height/2)-(height /2);

		window.doneLogin = function() {

			var loading = self.getHint('loading');

			// Display the loading screen
			self.display(loading, 'next');

			// Display a loading screen
			EasyBlog.ajax('site/views/mediamanager/flickr')
				.done(function(output) {

					// Display flickr's contents
					self.display(output, 'next');
				});
		}

		// Open the sign in popup
        window.open(url, "", 'scrollbars=no,resizable=no, width=' + width + ',height=' + height + ',left=' + left + ',top=' + top);
	},

	//
	// Search
	//
	toggleSearch: function(folder) {
		if (folder.hasClass(isSearching)) {
			self.deactivateSearch(folder);
		} else {
			self.activateSearch(folder);
		}
	},

	activateSearch: function(folder) {

		folder.addClass(isSearching);

		// Clear search input
		self.searchInput.inside(folder)
			.val("")
			.focus();
	},

	deactivateSearch: function(folder) {

		folder.removeClass(isSearching);

		self.resetSearch(folder);
	},

	resetSearch: function(folder)  {

		// Clear search input
		self.searchInput.inside(folder)
			.val("");

		// Show everything
		self.search(folder, "");
	},

	search: function(folder, keyword) {

		var keyword = keyword.toUpperCase();
		var isSearching = keyword!=="";

		// If keyword is empty, show all results.
		folder
			.removeClass(isNotFound)
			.toggleClass(hasSearchResults, isSearching);

		// Get filegroups and files
		var filegroups = self.filegroup.inside(folder);
		var files = self.file.inside(folder);

		// If we're not searching, remove search classnames.
		if (!isSearching) {
			files.removeClass(isSearchResult);
			filegroups.removeClass(hasSearchResults);
			return;
		}

		// Search for files
		var types = {};
		var count = 0;

		files.each(function(){

			var file = $(this);
			var found = file.text().toUpperCase().indexOf(keyword) >= 0;

			// If this files matches, remember the occurence of this file type,
			// and also increase the result count.
			if (found) {
				types[file.data("type")] = true;
				count++;
			}

			file.toggleClass(isSearchResult, found);
		});

		// Display filegroups that has file matches
		$.each(types, function(type) {
			filegroups.filter(".type-" + type)
				.addClass(hasSearchResults);
		});

		// If no results found, show notfound hint.
		folder.toggleClass(isNotFound, count < 1);
	},

	"{searchToggleButton} click": function(button) {

		// Get folder of button
		var folder = self.folder.of(button);
		self.toggleSearch(folder);
	},

	"{searchInput} keyup": function(searchInput, event) {

		// Escape
		if (event.keyCode===27) {

			var folder = self.folder.of(searchInput);
			var keyword = searchInput.val();

			// Second ESC, deactivate search.
			if (keyword=="") {
				self.deactivateSearch(folder);

			// First ESC, reset search.
			} else {
				self.resetSearch(folder);
			}
		}
	},

	"{searchInput} input": $.debounce(function(searchInput){

		var folder = self.folder.of(searchInput),
			keyword = $.trim(searchInput.val()).toUpperCase();

		self.search(folder, keyword);

	}, 150),


	//
	// Actions
	//
	"{createFolder} click": function(button) {
		var folder = self.folder.of(button);
		var key = self.getKey(self.currentUri);

		EasyBlog.dialog({
			"content": EasyBlog.ajax("site/views/mediamanager/createFolderDialog"),
			bindings: {
				"{submitButton} click": function() {
					EasyBlog.ajax("site/views/mediamanager/createFolder", {
						"key": key,
						"folder": this.folderName().val()
					}).done(function(html){

						// Insert a new folder code
						self.folder.insert(folder, html);

						// Hide the dialog now
						EasyBlog.dialog().close();
					});
				}
			}
		});
	},

	"{self} mediaFolderDisplay": function(base, event, folder) {

	},

	"{self} mediaRemove": function(base, event, uri) {

		var key = self.getKey(uri);

		self.file()
			.where("key", key)
			.remove();

		self.infoViewport()
			.removeClass("active");

		var parentUri = self.getParent(uri);
		var folder = self.getFolder(parentUri);
		var detachedFile = folder.find("[data-eb-mm-file][data-key='" + key  + "']");

		detachedFile.remove();

		setTimeout(function(){
			self.info().where("key", key)
				.detach();
		}, 500);
	},

	"{filegroupShowAllButton} click": function(button) {

		self.filegroup.of(button)
			.addClass("show-all");

		self.revealThumbnails();
	},

	revealThumbnails: function() {

		var folderContentPanel = self.folderContentPanel();

		if (folderContentPanel.length < 1) return;

		var viewportTop = folderContentPanel.offset().top,
			viewportBottom = viewportTop + folderContentPanel.height();

		self.thumbnail.inside(folderContentPanel)
			.each(function(){

				var thumbnail = $(this);
				if (thumbnail.is(":hidden")) return;

				var thumbnailTop = thumbnail.offset().top,
					thumbnailBottom = thumbnailTop + thumbnail.height();


				// if (thumbnailBottom >= viewportTop && thumbnailTop <= viewportBottom) {
					thumbnail
						.css("backgroundImage", $.cssUrl(thumbnail.data("thumbnail")))
						.removeAttr("data-thumbnail");

					return true;
				// }
			});
	},

	"{pages} pageslidestop": function() {
		self.revealThumbnails();
	},

	"{folderContentPanel} scrolly": $.debounce(function(folderContentPanel) {
		self.revealThumbnails();
	}, 150),

	"{fileInsertButton} click": function(button) {

		// Get uri
		var info = self.info.of(button);
		var key = info.data("key");
		var uri = self.getUri(key);

		// Get block
		var mediaDocument = self.mediaDocument();
		var block = mediaDocument.find(EBD.standaloneBlock);

		// TODO: Show loading indicator

		// stop video player if there is any
		self.pauseVideoObject();

		// Get media
		self.getMedia(uri)
			.done(function(media){

				if (self.mode == 'select') {
					self.currentBrowseButton.trigger('mediaSelect', [media]);
					composer.blocks.panel.fieldgroup.hide();
				} else {
					// Trigger mediaInsert event
					self.trigger("mediaInsert", [media, block]);
				}

				// Close the media manager once it's selected
				self.closeMediaManager();
			})
			.fail(function() {

				// TODO: Show unable to insert media error
			})
			.always(function(media){

				// TODO: Hide loading indicator
			});
	},

	"{fileRemoveButton} click": function(button) {

		// Get uri
		var info = self.info.of(button);
		var key = info.data("key");
		var uri = self.getUri(key);

		// Get the media object
		self.getMedia(uri)
			.done(function(meta) {

				// Display confirmation to delete file first
				EasyBlog.dialog({
					"content": EasyBlog.ajax("site/views/mediamanager/deleteFileDialog", {
								"file": meta.title
								}),
					"bindings": {
						"{submitButton} click": function() {

							EasyBlog.ajax("site/views/mediamanager/delete", {key: key})
								.done(function() {

									// Hide the dialog
									EasyBlog.dialog().close();

									// Remove the media
									self.removeMedia(uri);

									// After removing the media, navigate to parent's folder.
									var parentUri = self.getParent(uri);
									self.navigate(parentUri);
								})
								.fail(function() {
								});
						}
					}
				});

			});

	},

	"{fileRenameButton} click": function(button) {

		var info = self.info.of(button);
		var key = info.data("key");
		var filename = $.trim(self.infoFilename.inside(info).text());

		EasyBlog.dialog({
			content: EasyBlog.ajax("site/views/mediamanager/renameFileDialog", {"current" : filename}),
			bindings: {
				"{submitButton} click": function() {
					// TODO: Prevent lousy renames (extension changed, or end up starting with dot)
					var newFilename = this.fileName().val();

					// If filename is empty, stop.
					if ($.trim(newFilename) == "") {
						return;
					}

					// Rename the file
					self.file.rename(key, newFilename);
				}
			}
		});
	},

	"{fileMoveButton} click": function(button) {

		var activeTreeItem = self.treeItem(".active");

		if (activeTreeItem.length < 1) {
			return;
		}


		var source = button.data("key");
		var sourceUri = self.getUri(source);

		var target = activeTreeItem.data("key");
		var targetUri = self.getUri(target);

		var parentUri = self.getParent(sourceUri);
		var folder = self.getFolder(parentUri);

		EasyBlog.dialog({
			content: EasyBlog.ajax('site/views/mediamanager/confirmMoveItem', {
				"source": source,
				"target": target
			}),
			bindings: {
				"{submitButton} click": function() {

					EasyBlog.ajax("site/views/mediamanager/move", {
						source: source,
						target: target
					}).done(function(fileHtml, infoHtml){

						// Remove the source item from the folder contents
						self.removeMedia(source);

						// Refresh the target folder
						self.refreshMedia(targetUri);

						// Hide the dialog now
						EasyBlog.dialog().close();

						// We need to test this media if it's a folder.
						var file = $(fileHtml);
						var type = file.data('type');

						// Add is-moved class on the folder
						if (type == 'folder') {
							folder.addClass('folder-moved');
							self.frame()
								.removeClass("show-foldermove-message show-filemove-message has-messages")
								.addClass("show-foldermove-message");
						} else {
							folder.addClass('file-moved');
							self.frame()
								.removeClass("show-foldermove-message show-filemove-message has-messages")
								.addClass("show-filemove-message");
						}

						// Allow user to quickly go into the target folder.
						self.goToFolder().on('click', function() {

							// Remove any moved class
							folder.removeClass('folder-moved')
								.removeClass('file-moved');

							// Navigate to the target folder.
							self.navigate(targetUri);
						});

						// Once a media item is removed, we need to navigate to the parent's folder
						self.navigate(parentUri);

						// // We should still display the info panel even after the file is moved
						// if (media.meta) {
						// 	self.showInfo(media.meta.uri);
						// }

					})
					.fail(function(){
						alert("Unable to move file.");
					});

				}
			}
		})
	},

	refreshMedia: function(uri) {

		// Ensure that the arguments is always an uri
		uri = self.getUri(uri);

		delete self.medias[uri];
		delete self.mediaLoaders[uri];
		delete self.folderLoaders[uri];
		delete self.folder.nodes[uri];
	},

	removeMedia: function(uri) {

		// Delete the item from the cache
		uri = self.getUri(uri);

		// Rrefresh the media item
		self.refreshMedia(uri);

		// Remove the media
		self.trigger('mediaRemove', [uri]);
	},

	"{showMoveDialogButton} click": function(button) {

		var moveDialog = self.getHint("move");
		var info = self.info.of(button);
		var folder = self.folder.of(button);
		var folderKey = folder.data("key");
		var fileKey = info.data("key");
		var fileUri = self.getUri(fileKey);
		var filename = self.infoFilename.inside(info).text();

		// Get the parent folder
		var parentUri = self.getParent(fileUri);
		var parentKey = self.getKey(parentUri);

		// Back button should open
		self.openButton.inside(moveDialog)
			.attr("data-key", parentKey);

		self.moveFilename.inside(moveDialog)
			.html(filename);

		self.fileMoveButton.inside(moveDialog)
			.attr("data-key", fileKey);

		self.pages()
			.pageslide(moveDialog, "next");

		self.destroyInfo();

		self.currentUri = fileUri + "/?move"
	},

	// Folder traversal
	"{treeItem} click": function(treeItem, event) {

		// Clicking on child item won't cause parent item to be clicked on.
		event.stopPropagation();

		// Highlight tree item
		self.treeItem().removeClass("active");

		treeItem.addClass("active").toggleClass("is-expanded");

		// If we're collapsing tree item, don't do anything.
		if (!treeItem.hasClass("is-expanded")) {
			return;
		}

		// If tree item already has child tree, don't load anymore.
		if (treeItem.data("childTree")) {
			return;
		}

		// Show loading indicator
		treeItem.addClass("is-loading");

		var key = treeItem.data("key");
		var uri = self.getUri(key);

		if (uri == "post") {
			uri = self.getCurrentPostUri();
			key = self.getKey(uri);

			treeItem.attr('data-key', key);
		}

		EasyBlog.ajax("site/views/mediamanager/tree", {key: key})
			.done(function(childTree){
				treeItem
					.append(childTree)
					.data("childTree", childTree);
			})
			.fail(function(content){
			})
			.always(function(){
				treeItem.removeClass("is-loading");
			});

		// TODO: If a folder was renamed/created, remove childTree.
	},

	"{openButton} click": function(button) {

		var key = button.data("key"),
			uri = self.getUri(key);

		self.navigate(uri);
	},

	"{insertGallery} click": function(insertGallery) {
		var folder = self.folder.of(insertGallery);
		var key = folder.data('key');
		var uri = self.getUri(key);
		var composer = EasyBlog.Composer;

		var obj = {"uri": uri};
		var output = "[embed=gallery]" + JSON.stringify(obj) + "[/embed]";

		// Insert the embedded contents into the editor
		composer.document.insertContent(output);

		// Close media manager
		self.closeMediaManager();
	},

	"{folderMoveButton} click": function(button) {

		var moveDialog = self.getHint("move"),
			folder = self.folder.of(button),
			key = folder.data("key"),
			uri = self.getUri(key),
			filename = self.getFilename(key);

		// Back button should open
		self.openButton.inside(moveDialog)
			.attr("data-key", key);

		// TODO: Update filename
		self.moveFilename.inside(moveDialog)
			.html(filename);

		// Set folder key
		self.fileMoveButton.inside(moveDialog)
			.attr("data-key", key);

		self.pages()
			.pageslide(moveDialog, "next");

		self.currentUri = uri + "/?move";
	},

	"{folderRenameButton} click": function(button) {

		var folder = self.folder.of(button);
		var key = folder.data("key");
		var filename = self.getFilename(key);

		EasyBlog.dialog({
			content: EasyBlog.ajax("site/views/mediamanager/renameFolderDialog", {"current" : filename}),
			bindings: {
				"{submitButton} click": function() {
					// TODO: Prevent lousy renames (extension changed, or end up starting with dot)
					var newFilename = this.folderName().val();

					// If filename is empty, stop.
					if ($.trim(newFilename) == "") {
						return;
					}

					// Rename the folder
					self.renameFolder(key, newFilename);

					// Close the dialog
					EasyBlog.dialog().close();
				}
			}
		});
	},

	"{folderRemoveButton} click": function(button) {
		var folder = self.folder.of(button);
		var key = folder.data('key');
		var uri = self.getUri(key);

		// Display a confirmation to delete this folder
		EasyBlog.dialog({
			content: EasyBlog.ajax("site/views/mediamanager/deleteFolderDialog"),
			bindings: {
				"{submitButton} click": function() {

					// Delete the folder now
					EasyBlog.ajax("site/views/mediamanager/delete", {key: key})
						.done(function(){

							// Close the dialog once it's completed.
							EasyBlog.dialog().close();

							// Navigate to the parent
							self.navigate(self.getParent(uri));

							// Go back to parent folder
							self.removeMedia(uri);
						})
						.fail(function(){
						});
				}
			}
		});
	},

	"{paginationPrevious} click, {paginationNext} click": function(el) {

		// Get the limitstart
		var page = el.data('page');
		var content = self.getHint('loading');

		EasyBlog.ajax('site/views/mediamanager/users', {
			"page": page
		}).done(function(html){
			content.replaceWith(html);
		});

		// Display content
		self.display(content);

		// mediaNavigate (uri, fromUri, content)
		// self.trigger("mediaNavigate", [uri, currentUri, content]);
	},

	//
	// Drag & Drop
	//
	"{file} mouseover": function(file) {

		// Not for folders
		if (file.data("type")=="folder") {
			return;
		}

		// Not for legacy document
		var composer = EasyBlog.Composer;
		if (composer.document.isLegacy()) return;

		// TODO: This should be loosely coupled from composer.
		// Only initialize dragable on mouseover
		if (!file.data("uiDraggable")) {

			file.draggable({

				helper: function() {

					// Prepare helper
					var helper = file.clone();

					if (file.data("type")=="image") {
						helper
							.addClass("layout-tile")
							.css({
								width: file.outerWidth(),
								height: file.outerHeight()
							});
					} else {
						helper.addClass("layout-list");
					}

					return helper;
				},

				start: function(event, ui) {

					var helper = ui.helper;

					if (helper.hasClass("layout-list")) {
						// Ensure helper show up in the middle of the cursor
						var offsetLeft = event.pageX - file.offset().left;
						var helperWidth = ui.helper.width();
						var helperLeft = offsetLeft - (helperWidth / 2);
						ui.helper.css("margin-left", helperLeft);
					}
				},

				appendTo: composer.ghosts(),
				connectToSortable: EBD.root
			});
		}
	},

	"{file} dragstart": function(file, ui) {

		// Preload media the moment user start dragging,
		// to speed up dropping of blocks.
		var key = file.data("key");
		var uri = self.getUri(key);

		self.getMedia(key);

		self.closeMediaManager();
	},

	"{closeButton} click": function() {

		self.pauseVideoObject();

		self.closeMediaManager();
	},


	"{closeMessageButton} click": function() {

		self.frame()
			.removeClass("show-foldermove-message show-filemove-message has-messages");
	},

	pauseVideoObject: function() {
		if ($("video").length > 0) {
			$("video").get(0).pause();
		}
	}


}});

$("body").addController(controller);

module.resolve(controller);
});

});

