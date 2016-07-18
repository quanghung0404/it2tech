EasySocial.module( 'site/albums/all' , function($){

	var module 				= this;

	EasySocial.require()
	.library( 'history' )
	.done(function($){

		EasySocial.Controller( 'Albums.All.Browser',
		{
			defaultOptions:
			{
				"{sort}"	: "[data-albums-sort]",
				"{contents}": "[data-albums-content]"
			}
		},
		function( self )
		{
			return{ 

				init: function() {
				},

				setActiveSort: function(el) {
					self.sort().removeClass('active');

					$(el).addClass('active');
				},

				"{sort} click" : function(el, event) {
					event.preventDefault();
						
					self.setActiveSort(el);

					$(el).route();

					// Set loading state for the content
					self.contents().addClass('is-loading');
					self.contents().html('&nbsp;');

					var sorting = el.data('albums-sort-type');

					// Run the ajax call now
					EasySocial.ajax('site/controllers/albums/getAlbums', {
						"sort": sorting
					}).done(function(contents) {
						self.contents().html(contents);
					});
				}
			}
		});

		module.resolve();
	
	});


});
