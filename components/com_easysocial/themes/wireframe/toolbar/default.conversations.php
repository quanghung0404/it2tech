<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<li class="toolbarItem<?php echo $newConversations > 0 ? ' has-notice' : '';?>"
	data-popbox="module://easysocial/conversations/popbox"
	data-popbox-toggle="click"
	data-popbox-position="<?php echo $popboxPosition;?>"
	data-popbox-collision="<?php echo $popboxCollision;?>"
	data-notifications-conversations
>
	<a href="javascript:void(0);"
		data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_RECENT_CONVERSATIONS' , true );?>"
		data-placement="top"
		data-es-provide="tooltip"
	>
		<i class="fa fa-envelope"></i>

		<span class="visible-phone"><?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_CONVERSATIONS' );?></span>
		<span class="label label-notification" data-notificationConversation-counter><?php if( $newConversations > 0 ){ ?><?php echo $newConversations;?><?php } ?></span>
	</a>

</li>
