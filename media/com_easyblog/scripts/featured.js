EasyBlog.module( "featured" , function($) {

var module = this;

// require: start
EasyBlog.require()
.done(function(){

// controller: start

EasyBlog.Controller(

	"Featured.Scroller",

	{
		defaultOptions: {

			elements: null,

			itemWidth: null,

			// Auto rotate option
			autorotate: {
				enabled		: false,
				interval	: 50
			},

			// Items
			"{placeHolder}"	: ".slider-holder",
			"{slider}"		: ".featured-entries",
			"{sliderItems}"	: ".slider-holder ul li",
			"{sliderNavigation}" : ".featured-navi .featured-a a"
		}
	},

	function(self) {return {

		/**
		 * Featured scroller object initialization happens here.
		 *
		 */
		init: function() {

			// Set the current holder width to a temporary location.
			self.options.itemWidth	= self.placeHolder().width() + 1;

			// Calculate the total width of the whole parent container as we need to multiply this by the number of child elements.
			var totalWidth 			= self.sliderItems().length * parseInt( self.options.itemWidth );

			// Now, we need to stretch the parent's width to match the total items.
			self.slider().css( 'width' , totalWidth );

			// Make sure the width of each child items has the same width as its parent.
			self.sliderItems().css( 'width' , self.options.itemWidth );

			if( self.options.autorotate.enabled )
			{
				setTimeout( function(){
					self.initAutoRotate();	
				}, parseInt( self.options.autorotate.interval ) * 1000 );
			}
		},


		nextItem : function( element )
		{

			var index 	= $( element ).data( 'slider' );
			var left 	= 0;

			// If the current index is 1, we can just leave left as 0
			if( index != 1 )
			{
				left 	= self.options.itemWidth * parseInt( index - 1 );
			}

			// Since any items after the first item is hidden by default, we need to show the current item.
			self.slider().children( ':nth-child(' + index + ')' ).show();

			// Now let's animate the placeholder.
			self.slider().animate( {
				left : '-' + left + 'px'
			}, 'slow' );

			// Remove active class from the navigation anchor link.
			self.sliderNavigation( '.active' ).removeClass( 'active' );

			// Set the active element on the current item.
			$( element ).addClass( 'active' );
		},

		"{sliderNavigation} click" : function( element ){
			self.nextItem( element );
		},

		/**
		 * This initializes the auto rotation for the featured items.
		 */
		initAutoRotate: function(){

			var set 	= false;

			self.sliderNavigation().each(function(){

				if( $( this ).hasClass( 'active' ) && set != true )
				{
					if( $( this ).next().length == 0 )
					{
						self.nextItem( self.sliderNavigation( ':first' ) );
					}
					else
					{
						self.nextItem( $(this).next() );
					}
					set	= true;
				}

			});

			setTimeout( function(){
				self.initAutoRotate();
			}, parseInt( self.options.autorotate.interval ) * 1000 );

		}

	} }
);

module.resolve();

// controller: end	
});

});
