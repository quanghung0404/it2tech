EasySocial.module( 'admin/groups/groups' , function($) {

	var module = this;

	EasySocial
	.require()
	.library( 'expanding' )
	.done( function($)
	{
		EasySocial.Controller(
			'Groups.Pending.Item',
			{
				defaultOptions:
				{
					"{approve}" : "[data-pending-approve]",
					"{reject}"	: "[data-pending-reject]"
				}
			},
			function(self) {
				return {
					init: function()
					{
						self.options.id 	= self.element.data('id');
					},

					"{approve} click" : function()
					{
						EasySocial.dialog(
						{
							content 	: EasySocial.ajax( 'admin/views/groups/approveGroup' , { "ids" : self.options.id } )
						});
					},

					"{reject} click" : function()
					{
						EasySocial.dialog(
						{
							content		: EasySocial.ajax( 'admin/views/groups/rejectGroup' , { "ids" : self.options.id } )
						});
					}
				}
			});

		module.resolve();
	});

});