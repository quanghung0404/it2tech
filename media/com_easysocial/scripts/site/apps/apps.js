EasySocial.module( 'site/apps/apps' , function($){

	var module 				= this;

	EasySocial.require()
	.library( 'history' )
	.view(
		'site/loading/small'
	)
	.done(function($){

		EasySocial.Controller(
			'Apps',
			{
				defaultOptions :
				{
					requireTerms	: true,
					"{content}"	: "[data-apps-listing]",
					"{sort}"	: "[data-apps-sort]",
					"{filter}"	: "[data-apps-filter]",
					"{filterLink}" : "[data-apps-filter-link]",
					"{item}"	: "[data-apps-item]",
					"{sorting}"	: "[data-apps-sorting]",
					"{title}"	: "[data-page-apps-title]",

					view :
					{
						loading 	: 'site/loading/small'
					}
				}
			},
			function( self )
			{
				return {

					init : function()
					{
						// Implement apps item controller.
						self.initAppItem();
					},

					initAppItem : function()
					{
						self.item().implement( EasySocial.Controller.Apps.Item ,
						{
							requireTerms 	: self.options.requireTerms
						});
					},

					"{filterLink} click": function( el , event )
					{
						event.preventDefault();
					},

					"{filter} click" : function( el , event )
					{
						// Remove all active classes on the left
						self.sort().removeClass('active');
						self.filter().removeClass('active');

						// Add active class to the current filter item.
						el.addClass( 'active' );

						el.find( 'a' ).route();

						// Get the sort type.
						var filter 	= el.data( 'apps-filter-type' );

						// Get the sort group
						var group = el.data( 'apps-filter-group' );

						// Set the title.
						var title 	= el.find( 'a' ).attr( 'title' );
						self.title().html( title );

						// Set the current active filter
						self.options.filter 	= filter;

						// If the filter is 'mine' , we don't want to show the sorting options
						if (filter == 'mine') {
							self.sorting().hide();
						} else {
							self.sorting().show();
						}

						EasySocial.ajax( 'site/controllers/apps/getApps', {
							"filter": filter,
							"group": group
						}, {
							
							beforeSend: function() {
								// Set the default sorting type to alphabetically ordered.
								self.sort('.alphabetical').addClass('active');

								self.content().html( self.view.loading() );
							}
						}).done(function(output) {

							// Set is-empty class on the content so the empty message will be displayed
							self.content().toggleClass("is-empty", $(output).hasClass("empty"));

							// Append the output back.
							self.content().html(output);

							// Reapply the item controller
							self.initAppItem();
						});
					},

					"{sort} click" : function( el , event )
					{
						// Get the sort type and filter type.
						var type = el.data('apps-sort-type'),
							url = el.data('apps-sort-url'),
							group = el.data('apps-sort-group');

						History.pushState({state:1}, '' , url);

						// Add the active state on the current element.
						self.sort().removeClass( 'active' );

						el.addClass( 'active' );

						EasySocial.ajax( 'site/controllers/apps/getApps',
						{
							"sort"	: type,
							"filter": self.options.filter,
							"group"	: group
						},
						{
							beforeSend: function()
							{
								self.content().html( self.view.loading() );
							}
						})
						.done( function( output )
						{

							// Append the output back.
							self.content().html( output );

							// Reapply the item controller
							self.initAppItem();
						});
					}
				}
			});

		EasySocial.Controller(
			'Apps.Item',
			{
				defaultOptions :
				{
					id				: null,
					requireTerms 	: true,

					"{install}"		: "[data-apps-item-install]",
					"{installed}"	: "[data-apps-item-installed]",
					"{settings}"	: "[data-apps-item-settings]",

					view :
					{
						installAppForm : "site/apps/dialog.install",
						uninstallAppForm: "site/apps/dialog.uninstall"
					}
				}
			},
			function( self ) {
				return {

					init : function() {
						if(self.element.data('id')) {
							self.options.id = self.element.data('id');
						}
					},

					"{install} click" : function( el )
					{
						EasySocial.dialog({
							content: EasySocial.ajax('site/views/apps/getTnc' ),
							bindings:
							{
								'{cancelButton} click': function() {
									EasySocial.dialog().close();
								},

								'{installButton} click': function(el)
								{
									var agreed = !self.options.requireTerms || this.agreeCheckbox().is(':checked');

									if( agreed )
									{
										this.termsError().hide();

										self.installApp();
									}
									else
									{
										this.termsError().show();
									}
								}
							}
						});
					},

					installApp: function()
					{

						var installing = EasySocial.ajax('site/controllers/apps/installApp', {
							id: self.options.id
						});

						EasySocial.dialog({
							content: installing,
							bindings:
							{
								"{closeButton} click" : function(){
									EasySocial.dialog().close();
								}
							}
						});

						installing.done(function()
						{
							self.install().enabled(true);

							self.install().hide();

							self.installed().show();

							self.settings().hide();
						});
					},

					"{settings} click" : function( el , event )
					{
						EasySocial.dialog(
						{
							content 	: EasySocial.ajax( "site/views/apps/settings" , { "id" : self.options.id } ),
							bindings	:
							{
							}
						})
					},

					'{installed} click': function(el) {
						if(el.enabled()) {

							el.disabled(true);

							EasySocial.dialog({
								content		: EasySocial.ajax('site/views/apps/confirmUninstall'),
								bindings	:
								{
									'{parent.closeButton} click': function() {
										self.installed().enabled(true);
									},

									'{cancelButton} click': function() {
										self.installed().enabled(true);

										EasySocial.dialog().close();
									},

									'{uninstallButton} click': function()
									{
										self.uninstallApp();
									}
								}
							});
						}
					},

					uninstallApp: function() {
						var uninstalling = EasySocial.ajax('site/controllers/apps/uninstallApp', {
							id: self.options.id
						});

						EasySocial.dialog({
							content: uninstalling,
							bindings:
							{
								'{closeButton} click' : function()
								{
									EasySocial.dialog().close();
								}
							}
						});

						uninstalling.done(function()
						{
							self.installed().enabled(true);

							self.installed().hide();

							self.settings().hide();

							self.install().show();
						});
					}
				}
			});

		module.resolve();
	});


});
