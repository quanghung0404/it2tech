<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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
	<div class="es-widget-head">
		<div class="pull-left widget-title">
			<?php echo JText::_( 'APP_FOLLOWERS_WIDGET_TITLE_SUGGESTIONS' ); ?>
		</div>

		<?php if ($users) { ?>
			<a href="<?php echo FRoute::followers(array('filter' => 'suggest')); ?>" class="fd-small pull-right"><?php echo JText::_('APP_FOLLOWERS_WIDGET_VIEW_ALL'); ?></a>
		<?php } ?>
	</div>
	<div class="es-widget-body<?php echo !$users ? ' is-empty' : '';?>">
		<?php if( $users ){ ?>
		<ul class="widget-list friends-suggestion-list fd-reset-list">
			<?php foreach( $users as $user ){ ?>
			<li class="friends-suggestion-item" data-friend-suggest-list
				data-uid="<?php echo $user->id; ?>"
			>
				<div class="widget-main-link">
					<div class="media-object pull-left es-avatar-wrap">
						<a href="<?php echo $user->getPermalink();?>"
							class="es-avatar es-avatar-sm "
						>
							<img alt="<?php echo $this->html( 'string.escape' , $user->getName() );?>" src="<?php echo $user->getAvatar();?>" />
							<?php echo $this->loadTemplate( 'site/utilities/user.online.state' , array( 'online' => $user->isOnline() , 'size' => 'mini' ) ); ?>
						</a>
					</div>

					<div class="media-body pl-10 fd-small">
						<a href="<?php echo $user->getPermalink();?>">
							<span class="widget-main-link"><?php echo $user->getName(); ?></span>
						</a>
						<div class="fd-small total-no" data-widget-follower-add>
							<a href="javascript:void(0);" class="btn btn-es-primary btn-mini" data-es-followers-follow data-es-followers-id="<?php echo $user->id;?>" data-es-show-popbox="false">
								<?php echo JText::_('APP_FOLLOWERS_WIDGET_SUGGESTON_FOLLOW');?>
							</a>
						</div>
					</div>

				</div>
			</li>
			<?php } ?>
		</ul>
		<?php } else { ?>
		<div class="fd-small empty">
			<?php echo JText::_('APP_FOLLOWERS_WIDGET_NO_SUGGESTIONS_CURRENTLY'); ?>
		</div>
		<?php } ?>
	</div>
</div>
