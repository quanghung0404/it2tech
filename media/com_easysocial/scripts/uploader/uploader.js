EasySocial.module( 'uploader/uploader' , function($){

	var module 	= this;

	EasySocial.require()
	.library( 'plupload' )
	.view('site/uploader/queue.item')
	.script( 'uploader/queue' )
	.done( function(){

		EasySocial.Controller('Uploader', {
				defaults: {
					url: $.indexUrl + '?option=com_easysocial&controller=uploader&task=uploadTemporary&format=json&tmpl=component&' + EasySocial.token() + '=1',
					uploaded: [],

					// Allows caller to define their custom query.
					query: "",

					plupload: '',
					dropArea: 'uploaderDragDrop',
					extensionsAllowed: 'jpg,jpeg,png,gif',

					// Determines if we should upload the file first or not
					temporaryUpload: false,

					// Contains a list of files in the queue so others can manipulate this.
					files: [],

					'{uploaderForm}': '[data-uploader-form]',
					'{uploadButton}': '[data-uploader-browse]',
					'{uploadArea}': '.uploadArea',

					// This contains the file list queue.
					'{queue}': '[data-uploaderQueue]',

					// The queue item.
					'{queueItem}': '[data-uploaderQueue-item]',

					// When the queue doesn't have any item, this is the container.
					'{emptyFiles}': '[data-uploader-empty]',

					// This is the file removal link.
					'{removeFile}': '[data-uploaderQueue-remove]',
					'{uploadCounter}': '.uploadCounter',

					view : {
						queueItem : "site/uploader/queue.item"
					}
				}
			}, function(self, opts, base){ return {

				init: function(){

					// Implement the uploader queue.
					self.queue().implement(EasySocial.Controller.Uploader.Queue);

					if (opts.temporaryUpload) {
						opts.url = $.indexUrl + '?option=com_easysocial&controller=uploader&task=uploadTemporary&format=json&tmpl=component&' + EasySocial.token() + '=1';
					}

					if (opts.query != '') {
						opts.url = opts.url + '&' + opts.query;
					}

					// Implement the plupload controller on the upload form
					self.uploaderForm().implement('plupload', {
						settings: {
							url: opts.url,
							drop_element: opts.dropArea,
							filters: [{
								title: 'Allowed File Type',
								extensions: opts.extensionsAllowed
							}]
						},
						'{uploader}': '[data-uploader-form]',
						'{uploadButton}': '[data-uploader-browse]'
					}, function() {
						// Get the plupload options
						opts.plupload = this.plupload;
					});
				},


				createFileItem: function(files) {
					
					$.each(files, function(index, file) {

						// Get the file size.
						file.size = self.formatSize(file.size);

						// Get the upload queue content.
						var content = self.view.queueItem({
												"file": file,
												"temporaryUpload" : self.options.temporaryUpload
									});

						// Implement the queue item controller.
						$(content).implement(EasySocial.Controller.Uploader.Queue.Item, {
							"{uploader}": self
						});

						// Add this item into our own queue.
						opts.files.push(file);

						// Hide the "No files" value
						self.emptyFiles().hide();

						// Append the queue item into the queue
						self.queue().append( content );
					});
				},

				/**
				 * Formats the size in bytes into kilobytes.
				 */
				formatSize: function(bytes) {

					// @TODO: Currently this only converts bytes to kilobytes.
					var val = parseInt( bytes / 1024 );

					return val;
				},

				/**
				 * Clears the list of upload items in the queue.
				 */
				reset: function() {
					// Remove the item from the list.
					self.queueItem().remove();
				},

				removeItem: function(id) {
					var element = $('#' + id);

					// When an item is removed, we need to send an ajax call to the server to delete this record
					var uploaderId = $(element).find('input[name=upload-id\\[\\]]').val();

					EasySocial.ajax( 'site/controllers/uploader/delete' , { "id" : uploaderId } )
					.done(function() {
						// Remove the item from the attachment list.
						$( '#' + id ).remove();

						// Now remove the item from the plupload queue.
						self.options.plupload.removeFile( self.options.plupload.getFile( id ) );
					});
				},

				startUpload: function() {
					self.upload();
				},

				upload: function() {
					
					if(self.options.plupload.files.length > 0) {
						self.options.uploading 	= true;
						self.options.plupload.start();
					}
				},

				 hasFiles: function(){
				 	return self.options.files.length > 0;
				 },

				"{uploaderForm} FilesAdded": function(el, event, uploader, files ) {
					
					// Add a file to the queue when files are selected.
					self.createFileItem(files);

					// Begin the upload immediately if needed
					if (opts.temporaryUpload) {
						self.startUpload();
					}
				},

				"{uploaderForm} UploadProgress" : function(el, event, uploader, file) {

					// Trigger upload progress on the queue item.
					if (file) {				
						self.queueItem('#' + file.id)
							.trigger('UploadProgress', file);
					}

				},

				'{uploaderForm} FileUploaded' : function( el , event, uploader, file , response ){
					// Trigger upload progress on the queue item.
					self.queueItem( '#' + file.id ).trigger( 'FileUploaded' , [file , response] );
				},

				"{uploaderForm} UploadComplete" : function(el, event , uploader , files) {
					self.options.uploading 	= false;
				},

				'{uploaderForm} Error': function(el, event, uploader, error) {
					// Clear previous message
					self.clearMessage();

					var obj = { 'message' : error.message , 'type' : 'error' };

					self.setMessage( obj );
				},

				'{uploaderForm} FileError': function(el, event, uploader, file, response) {
					var obj = { 'message' : response.message , 'type' : 'error' };

					self.setMessage(obj);

					self.queueItem( '#' + file.id ).trigger('FileError', [file, response]);
				}
			} }
		);

		module.resolve();
	});


});
