EasySocial.module( 'toggle' , function($) {

var module = this;

EasySocial.Controller(
	'Toggle',
	{
		// A list of selectors we define
		// and expect template makers to follow.
		defaultOptions:
		{
			view			:{

			},
			'{selector}'	: ""
		}
	},
	function(self){

		return {

			init: function()
			{
			},

			'{selector} click' : function( element ){
				$( element ).next().toggle();
				$( element ).toggleClass('this-closed');
			}

		}
	}
);

module.resolve();

});
