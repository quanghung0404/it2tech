EasySocial.module( 'site/search/list' , function($){

	var module	= this;

	EasySocial.require()
	.view( 'site/loading/small', 'site/search/loadbutton' )
	.script('site/search/item')
	.language( 'COM_EASYSOCIAL_SEARCH_LOAD_MORE_ITEMS' )
	.done(function($){


		// TODO: Move this away from here
		// $.fn.visible = function(partial){

		// 	var $t	= $(this),
		// 		$w	= $(window),
		// 	viewTop	= $w.scrollTop(),
		// 	viewBottom	= viewTop + $w.height(),
		// 	_top		= $t.offset().top,
		// 	_bottom		= _top + $t.height(),
		// 	compareTop	= partial === true ? _bottom : _top,
		// 	compareBottom	= partial === true ? _top : _bottom;

		// 	return ((compareBottom <= viewBottom) && (compareTop >= viewTop));
	 //    };


		EasySocial.Controller(
			'Search.List',
			{
				defaultOptions:
				{
					// Elements
					"{item}"	: "[data-search-item]",


					"{pagination}"  : "[data-search-pagination]",
					"{loadmorebutton}" : "[data-search-loadmore-button]",

					// loading gif
					view :
					{
						loadingContent 	: "site/loading/small",
						loadmoreContent : "site/search/loadbutton"
					}

				}
			},
			function( self ){
				return {

					init : function()
					{
						self.item().implement( EasySocial.Controller.Search.Item );

						self.on("scroll.search", window, $._.debounce(function(){

							if (self.loading) return;

							if (self.pagination().visible()) {

								self.loadMore();
							}

						}, 250));
					},

					"{loadmorebutton} click": function(){
						self.loadMore();
					},


					loadMore: function() {

						var query 		= $("[data-search-query]").val();
						var type 		= $("[data-sidebar-menu].active").data( 'type' );
						var next_limit 	= self.pagination().data('last-limit');
						var last_type 	= self.pagination().data('last-type');

						var filters = [];
						$("[data-search-filtertypes]:checked").each( function(idx, ele) {
							filters.push($(ele).val());
						});

						if( next_limit == '-1')
						{
							self.loadmorebutton().hide();
							return;
						}

						self.loading = true;

						EasySocial.ajax( 'site/controllers/search/getItems' ,
						{
							"next_limit" : next_limit,
							"last_type" : last_type,
							"type" : type,
							"q" : query,
							"loadmore" : '1',
							'filtertypes' : filters
						},
						{
							beforeSend: function()
							{
								self.pagination().html( self.view.loadingContent() );
							}
						})
						.done(function( contents, next_type, next_limit )
						{
							// update next last-update and last-type
							self.pagination().data('last-limit', next_limit );
							self.pagination().data('last-type', next_type );


							// append stream into list.
							self.pagination().before( contents );

							//re-implement controller on new items
							self.item().implement( EasySocial.Controller.Search.Item );

							if ( next_limit == '-1') {
								self.pagination().html('');
							} else {
								//append the anchor link.
								self.pagination().html( self.view.loadmoreContent() );
							}


						})
						.fail( function( messageObj ){

							return messageObj;
						})
						.always(function(){

							self.loading = false;
							//self.pagination().html('');
						});
					}




				}
			});

		module.resolve();
	});

});
