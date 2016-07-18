EasySocial.module("admin", function($){

	var module = this;

	EasySocial.require()
		.library(
			"uniform",
			"chosen",
			"flot"
		)
		.done(function($){

			// Once uniform.js is implemented, we want to apply uniform to the elements.
			$(".uniform, .check :checkbox, .radio :radio, input:file[data-uniform], .usergroups :checkbox").uniform();

			// Apply chosen
			$('[data-chosen]').chosen({
				disable_search: true
			});

			$('[data-chosen-search]').chosen({
				disable_search 	: false
			});

			$('[data-sidebar-menu-toggle]').on('click' , function() {
				var parent 		= $( this ).parent( 'li' ),
					child 		= parent.find( 'ul' ),
					isActive 	= $( this ).parent( 'li' ).hasClass( 'active' );

				if( isActive )
				{
					parent.removeClass( 'active' );
					child.removeClass( 'in' );
				}
				else
				{
					parent.addClass( 'active' );
					child.addClass( 'in' );
				}
			});

			module.resolve();
		});
});
