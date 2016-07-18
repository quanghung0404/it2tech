EasySocial.module( 'site/search/sidebar' , function($){

	var module	= this;

	EasySocial.require()
	.done(function($){

		EasySocial.Controller(
			'Search.Sidebar',
			{
				defaultOptions:
				{
					"{menuItem}"	: "[data-sidebar-menu]"
				}
			},
			function( self ){
				return {

					init: function()
					{
					},

					"{menuItem} click" : function( el , event )
					{
						// Remove all active class.
						self.menuItem().removeClass( 'active' );

						// Add active class on this item.
						$( el ).addClass( 'active' );
					}
				}
			});


		EasySocial.Controller(
			'Search.Sidebar.Item',
			{
				defaultOptions:
				{
				}
			},
			function( self ){
				return {

					init: function()
					{
					},

					"{self} click" : function( el , event )
					{

						var type 	= self.element.data( 'type' ),
							url 	= self.element.data( 'url' );

						var query = $("[data-search-query]").val();
						var filters = [];

						// clear all filters
						$("[data-search-filtertypes]").each( function(idx, ele) {
							$(ele).prop('checked', false);
						});

						if (type != "") {
							filters.push(type);

							$("[data-search-filtertypes]").each( function(idx, ele) {
								if ($(ele).val() == type) {
									$(ele).prop('checked', true);
								}
							});
						}

						// If this is an embedded layout, we need to play around with the push state.
						History.pushState( {state:1} , '' , url );

						self.parent.updatingContents();

						// console.log( query );
						//return;

						//ajax call here.
						EasySocial.ajax( 'site/controllers/search/getItems',
						{
							"type"		: type,
							"q" 		: query,
							"filtertypes" : filters
						})
						.done(function( html )
						{
							self.parent.updateContent( html );
						})
						.fail(function( message ){
							console.log( message );
						});

						self.parent.updateContent();
					}
				}
			});

		module.resolve();
	});

});
