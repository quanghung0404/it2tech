
(function($){

	EasySocial.require()
	.view( 'apps/user/tasks/dashboard/form' )
	.done( function($){

		EasySocial.Controller(
			'Apps.Tasks',
			{
				defaultOptions:
				{
					"{content}"	: "[data-app-contents]",
					"{create}"	: "[data-tasks-create]",
					"{lists}"	: "[data-tasks-lists]",
					"{item}"	: "[data-tasks-item]",
					"{filter}"	: "[data-tasks-filter]",
					"{filterLinks}" : "[data-tasks-filter] > a",
					"{forms}"	: "[data-tasks-form]",

					view:
					{
						form : "apps/user/tasks/dashboard/form"
					}
				}
			},
			function(self)
			{
				return {

					init : function()
					{
						// Implement each list item.
						self.item().implement( EasySocial.Controller.Apps.Tasks.Item ,
						{
							"{parent}" : self
						});

					},

					"removeItem" : function( el )
					{
						$( el ).remove();

						// Determines if there's any else left on the page
						if( self.item().length == 0 )
						{
							self.content().addClass( 'is-empty' );
						}
					},

					"insertItem" : function( item )
					{
						// Append the item.
						$( item ).implement( EasySocial.Controller.Apps.Tasks.Item,
						{
							"{parent}" : self
						})
						.prependTo( self.lists() );
					},

					"{create} click" : function()
					{
						// Remove empty state on the content.
						self.content().removeClass( 'is-empty' );

						self.view.form()
							.implement( 'EasySocial.Controller.Apps.Tasks.Form' ,
							{
								"{parent}"	: self
							})
							.insertBefore( self.lists() );
					},

					"{filter} click" : function( el , event )
					{
						var type 	= $( el ).data( 'filter' );

						// Remove all active classes on filters.
						self.filterLinks().removeClass( 'active' );

						// Add active class on itself.
						$( el ).find( 'a' ).addClass( 'active' );

						// Remove all pending forms.
						self.forms().remove();

						// Hide all items
						self.item().hide();

						// Show only specific types.
						self.item( '.' + type ).show();
					}
				}
		});

		EasySocial.Controller(
			'Apps.Tasks.Item',
			{
				defaultOptions:
				{
					"{checkbox}" : "[data-tasks-item-checkbox]",
					"{remove}"	 : "[data-tasks-item-remove]",

					view:
					{
						form : "apps:/user/tasks/dashboard/form"
					}
				}
			},
			function(self)
			{

				return {

					init : function()
					{
						self.options.id 	= self.element.data( 'id' );
					},

					"{checkbox} change" : function()
					{
						if( self.checkbox().is(':checked' ) )
						{
							self.element.removeClass( 'is-unresolved' ).addClass( 'is-resolved' );

							EasySocial.ajax( 'apps/user/tasks/controllers/tasks/resolve',
							{
								"id"	: self.options.id
							})
							.done(function(){

							})
							.fail(function(){

							});


							return true;
						}

						self.element.removeClass( 'is-resolved' ).addClass( 'is-unresolved' );

						EasySocial.ajax( 'apps/user/tasks/controllers/tasks/unresolve',
						{
							"id"	: self.options.id
						})
						.done(function(){
						})
						.fail(function(){

						});

					},

					"{remove} click" : function()
					{
						EasySocial.ajax( 'apps/user/tasks/controllers/tasks/remove' ,
						{
							"id"	: self.options.id
						})
						.done( function()
						{
							self.parent.removeItem( self.element );
						})
						.fail( function(){
							console.log( 'failed' );
						});
					}
				}
		});

		EasySocial.Controller(
			'Apps.Tasks.Form',
			{
				defaultOptions:
				{
					"{title}"	: "[data-tasks-form-title]",
					"{cancel}"	: "[data-tasks-form-cancel]",
					"{save}"	: "[data-tasks-form-save]"
				}
			},
			function(self)
			{

				return {

					"{save} click" : function()
					{
						EasySocial.ajax( 'apps/user/tasks/controllers/tasks/save' ,
						{
							"title"	: self.title().val()
						})
						.done(function( item )
						{
							self.parent.insertItem( item );

							// Reset the value.
							self.title().val( '' );
						})
						.fail( function( response )
						{
							self.setMessage( response );
						});

					},

					"{title} keyup" : function( el , event )
					{
						// Enter key
						if(event.keyCode == 13)
						{
							self.save().click();
						}

						// Escape key
						if( event.keyCode == 27 )
						{
							self.cancel().click();
						}
					},

					"{cancel} click" : function()
					{
						// Remove element from the list.
						self.element.remove();
					}
				}
		});

		// Implement the controller.
		$( '[data-tasks]' ).implement( EasySocial.Controller.Apps.Tasks );

	});
})();
