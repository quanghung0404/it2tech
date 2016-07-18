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
<li class="message-item" data-readConversation-item data-id="<?php echo $message->id;?>">
	<div class="fd-cf fd-small">

		<div class="pull-left">
			<i class="fa fa-users "></i>

			<?php echo JText::sprintf('COM_EASYSOCIAL_CONVERSATIONS_INVITED_INTO_CONVERSATION_MESSAGE', $this->html('html.user', $message->getCreator()->id), $this->html('html.user', $message->getTarget()->id)); ?>
		</div>

		<div class="pull-right">
			<span class="message-time">
				<time>
					<i class="fa fa-clock-o-2 "></i> <?php echo FD::date( $message->created )->toLapsed();?>
				</time>
			</span>
		</div>

	</div>

</li>
