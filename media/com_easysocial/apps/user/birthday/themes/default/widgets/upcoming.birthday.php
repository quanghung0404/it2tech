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
<div class="es-widget">
	<div class="es-widget-head"><?php echo JText::_('APP_BIRTHDAYS_TITLE_UPCOMING_BIRTHDAYS'); ?></div>

	<div class="es-widget-body">
		<div class="fd-small">
			<?php if( $today ){ ?>
			<div>
				<span class="label label-info"><?php echo JText::_('APP_BIRTHDAYS_TODAY'); ?></span><br />
				<ul class="widget-list es-nav es-nav-stacked mt-5">
					<?php foreach( $today as $item ){
						$user = $item->user;
					?>
					<li data-upcoming-birthday-list
						data-id="<?php echo $user->id;?>"
						data-name="<?php echo $user->getName();?>"
						data-avatar="<?php echo $user->getAvatar();?>"
					>
						<div class="widget-main-link">
							<div class="media-object pull-left">
								<a href="<?php echo $user->getPermalink();?>"
									class="es-avatar es-avatar-sm"
								>
									<img alt="<?php echo $this->html( 'string.escape' , $user->getName() );?>" src="<?php echo $user->getAvatar();?>" />
								</a>
							</div>

							<div class="media-body pl-10">
								<span class="widget-main-link"><?php echo $user->getName(); ?></span>

								<?php if( FD::privacy( $this->my->id )->validate( 'profiles.post.message' , $user->id ) && $this->config->get( 'conversations.enabled' ) ){ ?>

								<div class="fd-small total-no" data-upcoming-birthday-button >
									<a href="javascript:void(0);" data-upcoming-birthday-message-button >
										<i class="icon-es-aircon-mail"></i>
										<span><?php echo JText::_( 'APP_BIRTHDAYS_SEND_MESSAGE' ); ?></span>
									</a>
								</div>

								<?php } ?>
							</div>

						</div>
					</li>
					<?php } ?>
				</ul>
			</div>
			<?php } ?>

			<?php if( $otherDays ){ ?>
			<div class="mt-5">
				<span class="fd-small"><b><?php echo JText::sprintf( 'APP_BIRTHDAYS_NEXT_OTHER_DAYS', '7');?></b></span>
				<br />
				<ul class="widget-list es-nav es-nav-stacked mt-5">
					<?php foreach( $otherDays as $item ){
						$user = $item->user;
					?>
					<li data-upcoming-birthday-list
						data-id="<?php echo $user->id;?>"
						data-name="<?php echo $user->getName();?>"
						data-avatar="<?php echo $user->getAvatar();?>"
					>
						<div class="widget-main-link">
							<div class="media-object pull-left">
								<a href="<?php echo $user->getPermalink();?>"
									class="es-avatar es-avatar-sm"
								>
									<img alt="<?php echo $this->html( 'string.escape' , $user->getName() );?>" src="<?php echo $user->getAvatar();?>" />
								</a>
							</div>

							<div class="media-body pl-5">
								<?php echo $this->html('html.user', $user->id); ?>

								<div>
									<i class="icon-es-cake mr-5"></i>
									<?php echo $item->display; ?>
								</div>

								<?php if( FD::privacy( $this->my->id )->validate( 'profiles.post.message' , $user->id ) && $this->config->get( 'conversations.enabled' ) ){ ?>

								<div class="fd-small total-no" data-upcoming-birthday-button >
									<a href="javascript:void(0);" data-upcoming-birthday-message-button>
										<span><?php echo JText::_( 'APP_BIRTHDAYS_SEND_MESSAGE' ); ?></span>
									</a>
								</div>

								<?php } ?>

							</div>

						</div>
					</li>
					<?php } ?>
				</ul>
			</div>
			<?php } ?>

			<?php if( empty( $ids ) ){ ?>
			<div class="mt-5">
				<span><?php echo JText::_( 'APP_BIRTHDAYS_NO_BIRTHDAY');?></span>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
