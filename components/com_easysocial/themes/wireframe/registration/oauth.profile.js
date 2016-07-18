<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>

EasySocial.ready(function($)
{
	$( '[data-oauth-profile-submit]' ).on( 'click' , function( event )
	{
		// Prevent the link from being accessed
		event.preventDefault();

		var id 	= $( this ).data( 'id' );

		$( '[data-oauth-profile-id]' ).val( id );

		$( '[data-oauth-profile]' ).submit();
	});
});
