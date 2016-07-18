
EasySocial.require()
.done(function($)
{
	$( '[data-<?php echo str_ireplace( array( '[' , '.' , ']' ) , '' , $field->inputName );?>]' ).on( 'change' , function()
	{
		var value 	= $( this ).val(),
			element	= $( this ).parents( '[data-limit-form]' ).find( '[data-limit-limited]' ),
			intervalEle = $( this ).parents( '[data-limit-form]' ).find( '[data-limit-interval]' );

		// If the state is "Limited" , we want to display the input
		if( value == 0 )
		{
			element.removeClass( 'hide' );
			intervalEle.removeClass( 'hide' );
		}
		else
		{
			element.addClass( 'hide' );
			intervalEle.addClass( 'hide' );

			element.find( '[data-limit-input]' ).val( '0' );
			intervalEle.find( '[data-interval-input]' ).prop('selectedIndex', 0);
		}
	});
});
