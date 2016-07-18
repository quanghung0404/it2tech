<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
(function() {
var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
po.src = 'https://apis.google.com/js/client:plusone.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();

EasySocial.require()
.done( function($)
{
	window.signInCallback = function( result )
	{
		// If there's an access token returned, we need to send this to the server to store the token
		if( result[ 'access_token' ] )
		{
			EasySocial.ajax( 'admin/controllers/oauth/googleplus' ,
			{
				code 	: result.code
			})
		}

		console.log( result );
	}

});



