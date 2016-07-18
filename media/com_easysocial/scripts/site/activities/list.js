EasySocial.module( 'site/activities/list' , function($){

	var module	= this;

	EasySocial.require()
	.view( 'site/loading/small', 'site/activities/loadbutton' )
	.language( 'COM_EASYSOCIAL_ACTIVITY_LOG_LOAD_PREVIOUS_STREAM_ITEMS' )
	.script('site/activities/item')
	.done(function($){

		EasySocial.Controller(
			'Activities.List',
			{
				defaultOptions:
				{
					// Elements
					"{item}"	: "[data-activity-item]",


					"{pagination}"  : "[data-activity-pagination]",

					// loading gif
					view :
					{
						loadingContent 	: "site/loading/small",
						loadmoreContent :"site/activities/loadbutton"
					}

				}
			},
			function( self ){
				return {

					init : function()
					{
						// self.item()
						// 	.addController(
						// 		"EasySocial.Controller.Activities.Item"
						// 	);

						self.item().implement( EasySocial.Controller.Activities.Item );

						self.on("scroll.activities", window, $._.debounce(function(){

							if (self.loading) return;

							if (self.pagination().visible()) {

								self.loadMore();
							}

						}, 250));
					},

					"{pagination} click" : function() {
						self.loadMore();
					},

					loadMore: function() {

						var type 		= $("[data-sidebar-menu].active").data( 'type' );
						var startlimit 	= self.pagination().data('startlimit');

						if( startlimit == '')
						{
							return;
						}

						self.loading = true;

						self.pagination().html( self.view.loadingContent({content: ""}) );

						EasySocial.ajax( 'site/controllers/activities/getActivities' ,
						{
							"limitstart" : startlimit,
							"loadmore" : '1',
							"type" : type
						})
						.done(function( contents, startlimit ) {
							// update next start date
							self.pagination().data('startlimit', startlimit );

							// append stream into list.
							self.pagination().before( contents );

							//re-implement controller on new items
							self.item().implement( EasySocial.Controller.Activities.Item );

							if (startlimit=="") {
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
