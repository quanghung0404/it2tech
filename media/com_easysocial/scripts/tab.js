EasySocial.module( 'tab' , function($) {

var module = this;

EasySocial.Controller(
	'Tab',
	{
		// A list of selectors we define
		// and expect template makers to follow.
		defaultOptions:
		{
			view			:{

			},
			"{tabs}"		: '',
			'{tabsContent}'	: '',
			'{defaultActive}': ''
		}
	},
	function(self){

		return {

			init: function()
			{
				// @task: If defaultActive exists, we make this element with the active class.
				self.defaultActive().click();

			},

			'{tabs} click' : function( element ){

				// If the element has class of inactive, we shouldn't do anything here.
				if( $( element ).hasClass( 'inactive' ) )
				{
					return false;
				}

				// Remove active tab.
				self.tabs( '.active' ).removeClass( 'active' );

				// @task: Add active class to itself.
				$( element ).addClass( 'active' );

				// @task: Hide all contents
				self.tabsContent().hide();

				// @task: Find the current element's id.
				var activeContent		= '.tab-' + $( element ).attr( 'id' );

				// @task: Show active content
				self.tabsContent( activeContent ).show();
			}

		}
	}
);

module.resolve();

});
