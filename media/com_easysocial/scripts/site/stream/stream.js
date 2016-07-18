EasySocial.module('site/stream/stream', function(){

	var module = this;

	EasySocial.require()
	.script('site/stream/item')
	.view('site/loading/small', 'site/stream/loadbutton')
	.language('COM_EASYSOCIAL_STREAM_LOAD_PREVIOUS_STREAM_ITEMS')
	.done(function($) {

		EasySocial.Controller('Stream', {

			defaultOptions: {
				// Check every 30 seconds by default.
				interval	: 30,

				// Properties
				checknew	: null,
				source      : null,
				sourceId    : null,
				autoload	: true,

				// Elements
				"{story}": "[data-story]",
				"{share}": "[data-repost-action]",
				"{list}": "[data-stream-list]",
				"{newNotiBar}": "[data-stream-notification-bar]",
				"{newNotiButton}": "[data-stream-notification-button]",

				"{item}": "[data-streamItem]",
				"{pagination}": "[data-stream-pagination]",
				"{paginationGuest}": "[data-stream-pagination-guest]",
				"{paginationCluster}": "[data-stream-pagination-cluster]",

				view: {
					loadingContent: "site/loading/small",
					loadmoreContent: "site/stream/loadbutton"
				}
			}
		}, function(self) {
			return {

				init : function()
				{
					// Implement stream item controller.
					self.item().addController(EasySocial.Controller.Stream.Item, {
						"{parent}": self
					});

					// Do not run updates checking when viewing single stream page.
					if (self.options.source != 'stream' && self.options.source != 'unity') {

						// Run the checking on new updates
						if( self.options.checknew == true ) {
							self.startMonitoring();
						}
					}

					if (self.options.autoload == true) {

						self.on("scroll.stream", window, $._.debounce(function(){

							if (self.loading) {
								return;
							}

							if (self.options.source == 'unity') {
								if (self.paginationGuest().visible()) {
									self.loadMoreGuest();
								}
							} else {
								iscluster = (self.options.source == 'dashboard' || self.options.source == 'profile') ? false : true;
								var pagination = (iscluster === true) ? self.paginationCluster() : self.pagination();

								if (pagination.visible()) {
									self.loadMore(iscluster);
								}
							}

						}, 250));

					}

					self.setLayout();
				},

				setLayout: function() {
					// Does nothing for now
				},

				"{window} resize": $.debounce(function() {

					self.setLayout();

				}, 500),

				"{story} create": function(el, event, itemHTML, ids ) {

					if (ids != '') {
						self.updateExcludeIds(ids);
					}

					// update the current date so that the next new stream notification will not include this item.
					self.updateCurrentDate();

					$.buildHTML(itemHTML)
						.prependTo(self.list())
						.addController("EasySocial.Controller.Stream.Item");

					self.list().children( "li.empty" ).remove();
				},

				"{share} create": function(el, event, itemHTML) {

					$.buildHTML(itemHTML)
						.prependTo(self.list())
						.addController("EasySocial.Controller.Stream.Item");

					self.list().children( "li.empty" ).remove();

					// update the current date so that the next new stream notification will not include this item.
					self.updateCurrentDate();
				},

				updateCurrentDate: function()
				{

					EasySocial.ajax( 'site/controllers/stream/getCurrentDate' ,
					{
					})
					.done(function( currentdate )
					{
						// console.log( currentdate );

						// update next start date
						self.element.data('currentdate', currentdate );

					})
					.fail( function( messageObj ){

					});

				},

				updateExcludeIds: function( id )
				{
					ids = self.element.data('excludeids' );
					newIds = '';

					if( ids != '' && ids != undefined )
					{
						newIds = ids + ',' + id;
					}
					else
					{
						newIds = id;
					}

					self.element.data('excludeids', newIds );
				},

				clearExcludeIds: function()
				{
					self.element.data('excludeids', '' );
				},

				/**
				 * Start running checks.
				 */
				startMonitoring: function()
				{
					if (self._destroyed) return self.stopMonitoring();

					var interval 	= self.options.interval * 1000;

					// Debug
					if( EasySocial.debug )
					{
						// console.info( 'Start monitoring new stream requests at interval of ' + self.options.interval + ' seconds.' );
					}

					self.options.state	= setTimeout( self.check , interval );
					// self.check();
				},

				/**
				 * Stop running any checks.
				 */
				stopMonitoring: function()
				{
					clearTimeout( self.options.state );
				},

				"{self} destroyed": function() {

					self.stopMonitoring();
				},

				/**
				 * Check for new updates
				 */
				check: function(){

					// Stop monitoring so that there wont be double calls at once.
					self.stopMonitoring();

					var interval 	= self.options.interval * 1000;

					var type 		= $("[data-dashboardSidebar-menu].active").data( 'type' );
					var id 			= $("[data-dashboardSidebar-menu].active").data( 'id' );
					var currentdate = self.element.data('currentdate');

					// console.log( currentdate );

					var excludeIds  = self.element.data('excludeids');

					// console.log( excludeIds );

					var pageNottiContent = $.trim( self.newNotiBar().html() );

					// debug code. do not remove!
					// console.log( 'currentdate: ' + currentdate, excludeIds );


					if( type == undefined && id == undefined )
					{
						if( self.options.source == 'profile' )
						{
							type = 'me';
							id 	 = self.options.sourceId;
						}
					}

					// console.log( type );
					// console.log( id );
					// console.log( excludeIds );


					EasySocial.ajax( 'site/controllers/stream/checkUpdates' ,
					{
						"type"		  : type,
						"id"		  : id,
						"currentdate" : currentdate,
						"exclude" 	  : excludeIds,
						"source"	  : self.options.source,
						"view"	  	  : self.options.source
					})
					.done( function( data, contents, nextupdate )
					{
						if (self._destroyed) {
							return self.stopMonitoring();
						}

						// update current date
						self.element.data('currentdate', nextupdate );

						if( data.length > 0 )
						{

							for( var i = 0 ; i < data.length; i++ )
							{
								item = data[ i ];

								if( item.cnt > 0 )
								{
									var key = '[data-stream-counter-' + item.type + ']';

									curCount = $( key ).text();
									newCount = ( curCount == '' ) ? item.cnt : parseInt( curCount, 10 ) + parseInt( item.cnt, 10 );

									$( key ).html( newCount );
									$( key ).parents('li').addClass('has-notice');
								}
							}

							contents 	= $.trim( contents );
							curContents = $.trim( self.newNotiBar().text() );

							// display the 'new feed bar' when there is new counter and this new feed bar is not display before.
							if( contents.length > 0 && curContents.length == 0 )
							{
								// append notification into list.
								self.newNotiBar().html( contents );
							}

						}

						// Continue monitoring.
						self.startMonitoring();
					});

				},

				"{newNotiButton} click" : function(el, event) {

					var type 		= $(el).data( 'type' );
					var id 			= $(el).data( 'uid' );
					var currentdate = $(el).data( 'since' );

					EasySocial.ajax( 'site/controllers/stream/getUpdates' ,
					{
						"type"		  : type,
						"id"		  : id,
						"currentdate" : currentdate,
						"source"	  : self.options.source,
						"view"	  	  : self.options.source
					})
					.done( function( contents, nextupdate, streamIds )
					{
						// clear the stream counter on the currect active filter bar.
						var key = '[data-stream-counter-';

						if( type == 'list' )
						{
							key = key + type + '-' + id;
						}
						else
						{
							key = key + type;
						}

						key = key + ']';

						$( key ).parents('li').removeClass('has-notice');

						// clear the counter value
						$( key ).html( '0' );

						// lets remove the stream items from the page if there is any
						$.each(streamIds, function(idx, uid) {
							self.item().where('id',uid).remove();
						});

						var itemCount = streamIds.length;

						// we need to update the pagination next limit start
						var pagination = '';

						if (self.paginationGuest().length > 0) {
							pagination = self.paginationGuest();
						} else {
							pagination = self.pagination();
						}

						var	startlimit = pagination.data("nextlimit");
						startlimit = startlimit + itemCount;

						pagination.data({
							nextlimit: startlimit
						});

						//clear the new feeds notification.
						self.newNotiBar().html('');

						// append stream into list.
						$.buildHTML(contents)
						 	.prependTo( self.list() )
						 	.addController("EasySocial.Controller.Stream.Item");

						 // lets clear the exclude ids
						 self.clearExcludeIds();

						 // update the next update date
						 self.element.data('currentdate', nextupdate );

					});

				},


				"{paginationGuest} click" : function() {

					self.loadMoreGuest();
				},

				loadMoreGuest: function() {


					var pagination = self.paginationGuest(),
						startlimit = pagination.data("nextlimit");

					var view = self.options.source;

					if (!startlimit) return;


					self.loading = true;

					pagination.html( self.view.loadingContent({content: ""}) );

					EasySocial.ajax(
						"site/controllers/stream/loadmoreGuest",
						{
							startlimit: startlimit,
							view: view
						})
						.done(function(contents, nextlimit ) {

							// Update start & end date
							pagination.data({
								nextlimit: nextlimit
							});

							var contents = $.buildHTML(contents);

								contents
									.insertBefore(pagination)
									.filter(self.item.selector)
									.addController("EasySocial.Controller.Stream.Item");

							// add support to kunena [tex] replacement.
							try { MathJax && MathJax.Hub.Queue(["Typeset",MathJax.Hub]); } catch( err ) {};

							//if (self.options.autoload || nextlimit=="") {
							if ( nextlimit=="" ) {
								pagination.html('');
							} else {
								//append the anchor link.
								// link = '<a class="btn btn-es-primary btn-stream-updates" href="javascript:void(0);"><i class="fa fa-repeat"></i> ' + $.language('COM_EASYSOCIAL_STREAM_LOAD_PREVIOUS_STREAM_ITEMS') + '</a>';
								// pagination.html( link );
								pagination.html( self.view.loadmoreContent() );

							}
						})
						.fail( function( messageObj ){

							return messageObj;
						})
						.always(function(){

							self.loading = false;
						});
				},

				"{paginationCluster} click" : function() {
					self.loadMore(true);
				},

				"{pagination} click" : function() {
					self.loadMore();
				},

				loadMore: function( iscluster ) {

					var currentSidebarMenu = $("[data-dashboardSidebar-menu].active"),
						type = currentSidebarMenu.data('type'),
						id   = currentSidebarMenu.data('id');
						tag  = currentSidebarMenu.data('tag');
						fid  = currentSidebarMenu.data('fid'); // this is the support group hashtag filtering.

					var pagination = (iscluster === true) ? self.paginationCluster() : self.pagination(),
						startlimit = pagination.data("nextlimit"),
						context		= pagination.data('context');

					var view = self.options.source;

					if (!startlimit) return;

					// if (profileid != undefined && !profileid) {
					// 	type = 'profile';
					// 	id 	 = profileid;
					// }

					// console.log(type, id);

					if( type == undefined && id == undefined )
					{
						if( self.options.source == 'profile' )
						{
							type = 'me';
							id 	 = self.options.sourceId;
						}
					}

					self.loading = true;

					pagination.html( self.view.loadingContent({content: ""}) );

					EasySocial.ajax(
						"site/controllers/stream/loadmore",
						{
							"id"		: id,
							"type"		: type,
							"startlimit": startlimit,
							"view"		: view,
							"tag"		: tag,
							"filterId"	: fid,
							"context"	: context,
							"iscluster" : iscluster
						})
						.done(function(contents, nextlimit) {

							// Update start & end date
							pagination.data({
								nextlimit: nextlimit
							});

							var contents = $.buildHTML(contents);

								contents
									.insertBefore(pagination)
									.filter(self.item.selector)
									.addController("EasySocial.Controller.Stream.Item");

							self.setLayout();

							// add support to kunena [tex] replacement.
							try { MathJax && MathJax.Hub.Queue(["Typeset",MathJax.Hub]); } catch( err ) {};

							if ( nextlimit=="" ) {
								pagination.html('');
							} else {
								//append the anchor link.
								// link = '<a class="btn btn-es-primary btn-stream-updates" href="javascript:void(0);"><i class="fa fa-repeat"></i> ' + $.language('COM_EASYSOCIAL_STREAM_LOAD_PREVIOUS_STREAM_ITEMS') + '</a>';
								// pagination.html( link );
								pagination.html( self.view.loadmoreContent() );
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
