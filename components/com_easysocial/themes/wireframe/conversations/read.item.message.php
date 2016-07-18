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

$msgPosition = ( $this->my->id == $message->getCreator()->id ) ? 'right' : 'left';
?>
<li class="message-item message-<?php echo $msgPosition; ?>" data-readConversation-item data-id="<?php echo $message->id;?>">
	<div class="media">

		<div class="media-object">
			<div class="es-avatar-wrap">
				<a class="es-avatar es-avatar es-stream-avatar " href="<?php echo ($message->getCreator()->isBlock()) ? 'javascript:void(0);' : $message->getCreator()->getPermalink();?>">
					<img src="<?php echo $message->getCreator()->getAvatar();?>" class="" />
				</a>
				<?php if(!$message->getCreator()->isBlock() && $message->getCreator()->hasCommunityAccess()) { ?>
				<?php echo $this->loadTemplate( 'site/utilities/user.online.state' , array( 'online' => $message->getCreator()->isOnline() , 'size' => 'mini' ) ); ?>
				<?php } ?>
			</div>
		</div>

		<div class="media-body">
			<div class="es-message-bubble">
				<div class="fd-cf">
					<div class="message-user-name pull-left">
						<?php if (!$message->getCreator()->isBlock() && $message->getCreator()->hasCommunityAccess()) { ?>
							<a href="<?php echo $message->getCreator()->getPermalink();?>"><?php echo $message->getCreator()->getStreamName();?></a>
						<?php } else { ?>
							<?php echo $message->getCreator()->getStreamName();?>
						<?php } ?>
					</div>
					
				</div>

				<div class="mail-content mt-10">
					<p><?php echo $message->getContents();?></p>
				</div>

				<!-- Render location's output -->
				<?php if( $message->getLocation() ){ ?>
					<?php echo $this->loadTemplate( 'site/conversations/read.item.location' , array( 'location' => $message->getLocation() ) ); ?>
				<?php } ?>

				<div class="">
					<!-- Render attachment's output -->
					<?php echo $this->loadTemplate( 'site/conversations/read.item.attachment' , array( 'attachments' => $message->getAttachments() ) ); ?>
				</div>

				<div class="message-time-wrap">
					<span class="message-time">
						<?php
							$msgDate 		= FD::date( $message->created );
							$msgDateText 	= ( $message->day > 0 ) ? $msgDate->toFormat('H:i') : $msgDate->toLapsed();
							$msgDateTitle 	= ( $message->day > 0 ) ? $msgDate->toLapsed() . JText::_('COM_EASYSOCIAL_CONVERSATIONS_AT') . $msgDateText : $msgDateText;

						?>
						<time title="<?php echo $msgDateTitle; ?>">
							<?php echo $msgDateText; ?>
						</time>
					</span>
				</div>
			</div>
		</div>
	</div>

</li>
