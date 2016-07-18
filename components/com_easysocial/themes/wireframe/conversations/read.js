<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
EasySocial
.require()
.script( 'site/conversations/read' )
.done(function($){

	$( '[data-readConversation]' ).implement( EasySocial.Controller.Conversations.Read ,
	{
		attachments			: <?php echo $this->config->get( 'conversations.attachments.enabled' ) ? 'true' : 'false' ?>,
		extensionsAllowed	: "<?php echo FD::makeString( $this->config->get( 'conversations.attachments.types' ) , ',' );?>",
		maxSize				: "<?php echo $this->config->get( 'conversations.attachments.maxsize' , 3 );?>mb"
	});
});
