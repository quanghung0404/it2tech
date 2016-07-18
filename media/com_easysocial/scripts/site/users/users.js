EasySocial.module( 'site/users/users' , function($){

	var module 				= this;

	EasySocial.require()
	.library( 'history' )
	.view(
		'site/loading/small',
		'site/users/button.following'
	)
	.done(function($){

		EasySocial.Controller(
			'Users',
			{
				defaultOptions :
				{
					"{content}"		: "[data-users-content]",
					"{listing}"		: "[data-users-listing]",
					"{sort}"		: "[data-users-sort]",
					"{filter}"		: "[data-users-filter]",
					"{items}"		: "[data-users-item]",
					"{pagination}" 	: "[data-users-pagination]",
					"{profile}": "[data-users-filter-profile]",
					"{search}": "[data-users-filter-search]",

					view :
					{
						loading 			: 'site/loading/small'
					}
				}
			},
			function( self )
			{
				return {

					init : function()
					{
						// Implement user item controller
						self.initUserController();
					},

					initUserController : function()
					{
						self.items().implement( EasySocial.Controller.Users.Item ,
						{
							"{parent}"	: self
						});
					},

					removeActiveFilter: function()
					{
						self.filter().each(function(){
							$(this).parent().removeClass('active');
						});

						self.profile().each(function(){
							$(this).parent().removeClass('active');
						})

						self.search().each(function(){
							$(this).parent().removeClass('active');
						})
					},

					"{profile} click": function(el, event)
					{
						event.preventDefault();

						var id = $(el).data('id');

						self.removeActiveFilter();

						// add active class
						$(el).parent().addClass('active');

						// Route the current url
						$(el).route();

						// Update contents with loading
						self.listing().html(self.view.loading());

						EasySocial.ajax('site/controllers/users/getUsersByProfile',
						{
							"id": id
						})
						.done(function(output){

							self.content().html(output);

							// Re-apply controller
							self.initUserController();
						});
					},

					"{search} click": function(el, event)
					{
						event.preventDefault();

						var id = $(el).data('id');

						self.removeActiveFilter();

						// add active class
						$(el).parent().addClass('active');

						// Route the current url
						$(el).route();

						// Update contents with loading
						self.listing().html(self.view.loading());

						EasySocial.ajax('site/controllers/users/getUsersByFilter',
						{
							"id": id
						})
						.done(function(output){

							self.content().html(output);

							// Re-apply controller
							self.initUserController();
						});
					},

					"{filter} click" : function( el , event )
					{
						event.preventDefault();

						// Remove any active states for filters and sort items
						self.sort().removeClass( 'active' );

						self.removeActiveFilter();

						// Add active class to the current filter item.
						$( el ).parent().addClass( 'active' );

						// Get the sort type.
						var filter 	= $( el ).data( 'filter' );

						self.options.filter 	= filter;
						$( el ).route();

						// Add loading state to the content.
						self.listing().html( self.view.loading() );

						// Set the first sort item as the active item
						self.sort( ':first' ).addClass( 'active' );

						// Perform the ajax call to retrieve the new users listing
						EasySocial.ajax( 'site/controllers/users/getUsers',
						{
							"filter" 			: filter,
							"showpagination"	: 1
						})
						.done(function( output )
						{
							self.content().html( output );

							// Re-apply controller
							self.initUserController();
						});
					},

					"{sort} click" : function( el , event )
					{
						event.preventDefault();

						// Get the sort type
						var type 	= $( el ).data( 'type' );

						$( '<a>' ).attr( { title : document.title , href : $(el).attr( 'href' ) }).route();

						// Add the active state on the current element.
						self.sort().removeClass( 'active' );

						$( el ).addClass( 'active' );

						// Add loading state to the content.
						self.listing().html( self.view.loading() );

						// Remove pagination
						self.pagination().remove();

						EasySocial.ajax( 'site/controllers/users/getUsers' ,
						{
							"sort"				: type,
							"filter"			: self.options.filter,
							"isSort" 			: true,
							"showpagination" 	: 1
						})
						.done(function(contents)
						{
							// Update the contents on the page.
							self.listing().html( contents );

							// Re-apply controller
							self.initUserController();
						});

					}
				}
			});

		EasySocial.Controller('Users.Item', {
			
			defaultOptions: {
				id: null,
				"{followUser}": "[data-es-followers-follow]",
				"{addFriend}": "[data-users-add-friend]",
				"{friendsButton}": "[data-users-friends-button]",
				"{compose}": "[data-users-friends-compose]",
				"{unfriend}": "[data-users-friends-unfriend]",

				view: {
					followingButton: 'site/users/button.following'
				}
			}
		}, function(self) {

			return {

				init: function() {
					self.options.id = self.element.data('id');
				},

				"{followUser} following": function(el, event) {
					// Hide the previous popbox
					$(el).popbox('hide');

					// Replace the button
					$(el).replaceWith( self.view.followingButton() );
				},

				"{addFriend} click": function(addButton, event) {
					
					// Add a loading state to the button
					$(addButton).addClass( 'btn-loading' );

					// Append loading state on the button
					EasySocial.ajax( 'site/controllers/friends/request' , {
						"viewCallback": "usersRequest",
						"id": self.options.id
					})
					.done(function(pendingButton) {
						// Replace the button
						$(addButton).replaceWith(pendingButton);

						// Remove the loading state from the button
						$(addButton).removeClass('btn-loading');
					})
					.fail(function(obj) {
						EasySocial.dialog({
							content: obj.message
						});

						$(addButton).removeClass('btn-loading');
					})
				}

			}
		});

		module.resolve();
	});


});
