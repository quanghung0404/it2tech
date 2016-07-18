EasyBlog.module('admin/sort' , function($) {

	var module = this;

	EasyBlog.Controller(
		'Grid.Sort',
		{
			defaultOptions : 
			{
				items 	: "[data-grid-sort-item]"
			}
		},
		function( self )
		{
			return {

				init : function()
				{
				},

				"{self} click": function()
				{
					var direction 	= self.element.data( 'direction' ),
						column 		= self.element.data( 'sort' );

					// Set the ordering
					self.parent.setOrdering( column );

					// Set the direction
					self.parent.setDirection( direction );

					// Remove any task associated to the form.
					self.parent.setTask( '' );
					
					// Submit the form.
					self.parent.submitForm();
				}
			}
		}
	);
		
	module.resolve();

});