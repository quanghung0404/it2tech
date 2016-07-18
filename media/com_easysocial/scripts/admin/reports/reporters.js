EasySocial.module( 'admin/reports/reporters' , function($) {

	var module = this;

	EasySocial.Controller(
		'Reports.Reporters',
		{
			defaultOptions : 
			{
				"{item}"		: "[data-reporters-item]"
			}
		},
		function( self )
		{
			return {
				init : function()
				{
					self.item().implement( EasySocial.Controller.Reports.Reporters.Item ,
						{
							"{parent}"	: self
						});
				}
			}
		}
	);

	EasySocial.Controller(
		'Reports.Reporters.Item',
		{
			defaultOptions :
			{
				"{removeItem}"	: "[data-remove-item]"			
			}
		},
		function( self )
		{
			return {
				init : function()
				{
					self.options.id 	= self.element.data( 'id' );
				},

				"{removeItem} click" : function()
				{
					// Remove any messages.
					self.parent.clearMessage();

					EasySocial.ajax( 'admin/controllers/reports/removeItem' ,
					{
						"id"	: self.options.id
					})
					.done(function( result )
					{
						self.parent.setMessage( result.message , result.type );

						self.element.remove();
					});
					
				}
			}
		}
	);

	module.resolve();

});