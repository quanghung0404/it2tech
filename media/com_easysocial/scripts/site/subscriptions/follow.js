EasySocial.module( 'site/subscriptions/follow' , function(){

	var module	= this;

	EasySocial.require()
	.language(
		'COM_EASYSOCIAL_SUBSCRIPTION_INFO')
	.done(function($){

		EasySocial.Controller(
		'Follow',
		{
			defaultOptions:
			{

			}
		},
		function( self )
		{
			return {

				init: function()
				{
				},

				"{self} click" : function()
				{
					EasySocial.ajax( 'site/controllers/subscriptions/toggle' ,
					{
						"uid"	: self.element.data('id'),
						"type"	: self.element.data('type'),
						"notify": "1"
					})
					.done(function( content , label )
					{
						// update the label
						self.element.text( label );

						EasySocial.dialog({
							title: $.language('COM_EASYSOCIAL_SUBSCRIPTION_INFO'),
							content: content
						});

					})
					.fail( function( message ){
						self.setMessage( message, 'error' );
					});

				}
			}
		});

		module.resolve();
	});
});
