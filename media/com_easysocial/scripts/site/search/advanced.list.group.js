EasySocial.module( 'site/search/advanced.list.group' , function($){

	var module	= this;

	EasySocial.require()
	.view( 'site/loading/small', 'site/search/loadbutton' )
	.script('site/groups/groups')
	.language( 'COM_EASYSOCIAL_SEARCH_LOAD_MORE_ITEMS' )
	.done(function($){


		EasySocial.Controller(
			'Search.Advanced.List.Group',
			{
				defaultOptions:
				{
					// Elements
					"{item}" : "[data-search-item]",
					"{pagination}" : "[data-search-pagination]",
					"{loadmorebutton}" : "[data-search-loadmore-button]",

					"{searchForm}" : "[data-adv-search-form]",

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
						self.item().implement(EasySocial.Controller.Groups.Browser.Item);

						self.on("scroll.advsearch", window, $._.debounce(function(){

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

						var next_limit 	= self.pagination().data('last-limit');
						var data 		= self.searchForm().serializeJSON();

						// console.log( next_limit );
						// console.log( data );


						if( next_limit == '-1')
						{
							self.loadmorebutton().hide();
							return;
						}

						self.loading = true;

						EasySocial.ajax( 'site/controllers/search/loadmore' ,
						{
							"data" : data,
							"nextlimit" : next_limit
						},
						{
							beforeSend: function()
							{
								self.pagination().html( self.view.loadingContent() );
							}
						})
						.done(function( contents, next_limit )
						{
							// update next last-update
							self.pagination().data('last-limit', next_limit );

							// append stream into list.
							self.pagination().before( contents );

							//re-implement controller on new items
							self.item().implement(EasySocial.Controller.Groups.Browser.Item);

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

						});
					}




				}
			});

		module.resolve();
	});

});
