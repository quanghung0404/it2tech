
EasySocial.ready(function($)
{
	var selector 	= "[data-truncater-<?php echo $uid;?>]";

	$( selector ).find( 'a' ).bind( 'click' , function(){

		// Remove ellipses
		$( selector ).find( '[data-truncater-ellipses]' ).hide();

		// Show the balance of the content
		$( selector ).find( '[data-truncater-balance]' ).show();

		// Hide the more button after the full content is displayed.
		$( this ).hide();
	});
	
});