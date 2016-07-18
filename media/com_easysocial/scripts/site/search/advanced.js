EasySocial.module( 'site/search/advanced' , function(){
	var module	= this;

	EasySocial.require()
	.script( 'site/search/advanced.criteria' )
	.view( 'site/loading/small')
	.language( 'COM_EASYSOCIAL_STREAM_LOAD_PREVIOUS_STREAM_ITEMS' )
	.done( function($){

		EasySocial.Controller(
		'Search.Advanced',
		{
			defaultOptions:
			{
				// Elements
				"{sidebar}"       	: "[data-advsearch-sidebar]",
				"{sidebarItem}"		: "[data-sidebar-item]",

				"{item}"			: "[data-adv-search-item]",
				"{list}" 			: "[data-advsearch-list]",

				"{searchForm}" 		: "[data-adv-search-form]",
				"{savefilterBtn}" 	: "[data-advsearch-savefilter]",

				"{deletefilterBtn}" : "[data-advsearch-deletefilter]",

				"{content}" 		: "[data-advsearch-content]",

				"{addCriteria}"		: "[data-adv-search-add-criteria]",

				"{criteriaTemplate}"	: "[data-adv-search-criteria-template]",

				"{resultlist}" 		: "[data-advsearch-result-list]",

				// loading gif
				view :
				{
					loadingContent 	: "site/loading/small"
				}
			}
		},
		function( self ){
			return {

				init : function()
				{
					// Implement sidebar item controller.
					self.sidebarController = self.sidebar().addController( EasySocial.Controller.Search.Advanced.Sidebar,
												{
													"{parent}" : self
												});

					// implement search criteria item controller.
					self.item()
						.addController( EasySocial.Controller.Search.Advanced.Criteria,
							{
								"{parent}" : self
							});

				},

				/**
				 * Add a loading icon on the content layer.
				 */
				updatingContents: function()
				{
					self.element.addClass("loading");
				},

				/**
				 * Responsible to update the content area in the dashboard.
				 */
				updateContents : function( contents )
				{
					self.element.removeClass("loading");

					// Hide the content first.
					self.content().html( contents );
				},


				"{deletefilterBtn} click": function( el ) {

					var id = $(el).data( "id" );

					EasySocial.dialog({
						content		: EasySocial.ajax( 'site/views/search/confirmFilterDelete', { "fid": id } ),
						bindings	:
						{
							"{deleteButton} click" : function()
							{
								EasySocial.ajax( 'site/controllers/search/deleteFilter',
								{
									"fid"		: id
								})
								.done(function()
								{
									// delete the sidebar item
									$('[data-search-filter-' + id + ']').remove();

									//set active to default filter.
									$('[data-search-filter-0]').addClass( 'active' );

									//reset content
									self.content().html("");
									self.updatingContents();

									EasySocial.ajax( 'site/controllers/search/getFilterResults' ,
									{
										"fid"	: '0',
									})
									.done(function( contents )
									{
										var contents = $.buildHTML(contents);

										contents
											.addController( "EasySocial.Controller.Search.Advanced.Criteria",
												{
													"{parent}" : self
												});

										self.updateContents( contents );

									})
									.fail( function( messageObj )
									{
										return messageObj;
									})


									EasySocial.dialog().close();
								});
							}
						}
					});

				},


				"{savefilterBtn} click" : function()
				{
					var data 		= self.searchForm().serializeJSON();

					EasySocial.dialog({
						content		: EasySocial.ajax( 'site/views/search/confirmSaveFilter' ),
						bindings	:
						{
							"{saveButton} click" : function()
							{
								this.inputWarning().hide();

								filterName = this.inputTitle().val();
								filterSitewide = this.inputSitewide().is(':checked') ? 1 : 0;

								if( filterName == '' )
								{
									this.inputWarning().show();
									return;
								}

								EasySocial.ajax( 'site/controllers/search/addFilter',
								{
									"title"		: filterName,
									"sitewide"	: filterSitewide,
									"data"		: data,
								})
								.done(function( html, msg )
								{
									var item = $.buildHTML( html );
									self.sidebarController.addFilterItem( item );

									// show message
									EasySocial.dialog( msg );

									// auto close the dialog
									setTimeout(function() {
										EasySocial.dialog().close();
									}, 2000);

								});
							}
						}
					});
				},

				"{addCriteria} click" : function( el , event )
				{
					// Duplicate the template
					var tmpl	= self.criteriaTemplate().clone();

					// Remove any unecessary attributes for the template
					$( tmpl )
						.removeClass( 'hide' )
						.removeAttr( 'data-adv-search-criteria-template' )
						.addController(
							EasySocial.Controller.Search.Advanced.Criteria ,
							{
								"{parent}" : self
							}
						);

					// Append the template to the list now.
					self.list().append( tmpl );
				}
			}
		});

		EasySocial.Controller(
		'Search.Advanced.Sidebar',
		{
			defaultOptions:
			{
				"{item}" : "[data-sidebar-item]",

				// loading gif
				view :
				{
					loadingContent 	: "site/loading/small"
				}
			}
		},
		function( self ){
			return {

				init : function()
				{
					// Implement each feed links.
					self.item().implement( EasySocial.Controller.Search.Advanced.Sidebar.Item ,
					{
						"{parent}"		: self,
						"{root}"		: self.parent
					});
				},


				"addFilterItem" : function( item )
				{

					//item.find('[data-sidebar-item]').implement( EasySocial.Controller.Search.Advanced.Sidebar.Item ,
					item.implement( EasySocial.Controller.Search.Advanced.Sidebar.Item ,
					{
						"{parent}"		: self,
						"{root}"		: self.parent
					});

					item.appendTo( $('[data-advsearch-sidebar-ul]') );

				},


			} //return
		});

		EasySocial.Controller(
		'Search.Advanced.Sidebar.Item',
		{
			defaultOptions:
			{
				"{filterDeleteBtn}" : "[data-search-filter-delete]",
				"{filterItem}" 		: "[data-search-filter-item]",

				"{item}"			: "[data-adv-search-item]",


				// loading gif
				view :
				{
					loadingContent 	: "site/loading/small"
				}
			}
		},
		function( self ){
			return {

				init : function()
				{

				},

				"{filterItem} click" : function( el , event )
				{
					// Prevent event bubbling
					event.preventDefault();

					$( '[data-sidebar-item]' ).removeClass( 'active loading' );
					self.element.addClass( 'active');

					var id		= self.element.data( 'id' ),
						url 	= self.element.data( 'url' ),
						title 	= self.element.data( 'title' );

					// Update browser's URL
					$( el ).route();

					// Notify the dashboard that it's starting to fetch the contents.
					self.root.content().html("");
					self.root.updatingContents();

					self.element.addClass( 'loading' );

					EasySocial.ajax( 'site/controllers/search/getFilterResults' ,
					{
						"fid"	: id,
					})
					.done(function( contents )
					{
						var contents = $.buildHTML(contents);

						contents
							.find( self.item.selector )
							.addController( "EasySocial.Controller.Search.Advanced.Criteria",
								{
									"{parent}" : self.root
								});

						self.root.updateContents( contents );

					})
					.fail( function( messageObj )
					{
						return messageObj;
					})
					.always(function()
					{
						self.element.removeClass( 'loading' );
					});


				}


			} //return
		});

		module.resolve();

	});

});
