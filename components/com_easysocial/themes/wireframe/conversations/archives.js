<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<?php defined( '_JEXEC' ) or die( 'Unauthorized Access' ); ?>

EasySocial
.require()
.script( 'site/conversations' )
.done(function($){

	$( '#conversationList' ).implement(
		EasySocial.Controller.Conversations.List,
		{
		},
		function(){
		}
	);

});
