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
<div class="conversations-result">
	<div class="popbox-header">
		<div class="es-title">
			<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_CONVERSATIONS' );?>
		</div>
		<div class="es-action">
			<?php if( $this->access->allowed( 'conversations.create' ) ){ ?>
			<a href="<?php echo FRoute::conversations( array( 'layout' => 'compose' ) );?>">
				<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_CONVERSATIONS_COMPOSE' );?>
			</a>
			|
			<?php } ?>

			<a href="<?php echo FRoute::conversations();?>"><?php echo JText::_( 'COM_EASYSOCIAL_VIEW_ALL' );?></a>
		</div>
	</div>

	<div class="popbox-body">
		<ul class="fd-reset-list<?php echo !$conversations ? ' is-empty' : '';?>">
		<?php if( $conversations ){ ?>
			<?php foreach( $conversations as $conversation ){ ?>
				<li class="<?php echo $conversation->isNew() ? ' is-unread' : ' is-read';?>">
					<div class="media notice-message">
						<a href="<?php echo FRoute::conversations( array( 'layout' => 'read' , 'id' => $conversation->id ) );?>">
						<div class="media-object pull-left">
							<div class="es-avatar">
								<?php foreach( $conversation->getParticipants( $this->my->id ) as $participant ){ ?>
								<img src="<?php echo $participant->getAvatar();?>" title="<?php echo $this->html( 'string.escape' , $participant->getName() );?>" />
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
				<div class="mt-20 pl-10 pr-10 fd-small">
					<i class="fa fa-envelope  mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_CONVERSATIONS_NO_CONVERSATIONS_YET' ); ?>
				</div>
			</li>
		<?php } ?>
		</ul>
	</div>
</div>
