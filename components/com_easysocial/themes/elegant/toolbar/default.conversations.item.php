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
<?php if( $conversations ){ ?>
	<?php foreach( $conversations as $conversation ){ ?>
		<li class="<?php echo $conversation->isNew() ? ' is-unread' : ' is-read';?>">
			<div class="media notice-message">
				<a href="<?php echo FRoute::conversations( array( 'layout' => 'read' , 'id' => $conversation->id ) );?>">
				<div class="media-object pull-left">
					<div class="es-avatar">
						<?php if( $conversation->type == SOCIAL_CONVERSATION_MULTIPLE ){ ?>
							<img src="<?php echo $conversation->getLastMessage( $this->my->id )->getCreator()->getAvatar();?>" title="<?php echo $this->html( 'string.escape' ,  $conversation->getLastMessage( $this->my->id )->getCreator()->getName() );?>" />
						<?php } else { ?>
							<?php if( $conversation->getLastParticipant( array( $this->my->id ) ) ){ ?>
								<img src="<?php echo $conversation->getLastParticipant( array( $this->my->id ) )->getAvatar();?>" title="<?php echo $this->html( 'string.escape' , $conversation->getLastParticipant( array( $this->my->id ) )->getName() );?>" />
							<?php } ?>
						<?php } ?>
					</div>
				</div>
				<div class="media-body">
					<?php if( $conversation->getLastMessage( $this->my->id ) ){ ?>
						<?php echo $this->loadTemplate( 'site/toolbar/default.conversations.item.' . $conversation->getLastMessage( $this->my->id )->type , array( 'conversation' => $conversation , 'message' => $conversation->getLastMessage( $this->my->id ) ) ); ?>
					<?php } ?>
				</div>
				</a>
			</div>
		</li>
	<?php } ?>
<?php } else { ?>
	<li class="requestItem empty center">
		<div class="mt-20 pt-20 small">
			<i class="ies-mail-3 ies-small mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_CONVERSATIONS_NO_CONVERSATIONS_YET' ); ?>
		</div>
	</li>
<?php } ?>
