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
	<div class="es-widget-head">
		<div class="pull-left widget-title">
			<?php echo JText::_( 'APP_FRIENDS_SUGGEST_FRIENDS' ); ?>
		</div>
	</div>
	<div class="es-widget-body<?php echo !$friends ? ' is-empty' : '';?>">
		<?php if( $friends ){ ?>
		<ul class="widget-list es-friends-suggestion-list">
			<?php foreach( $friends as $item ){

				$cntText = '';
				if( $item->count )
				{
					$cntPluralize 	= FD::get( 'Language' )->pluralize( $item->count, true )->getString();
					$cntText 		= JText::sprintf( 'APP_FRIENDS_SUGGEST_FRIENDS_MUTUAL' . $cntPluralize, $item->count );
				}
			?>
			<li class="es-friends-suggestion-item" data-friend-suggest-list
				data-uid="<?php echo $item->friend->id; ?>"
			>
				<div class="widget-main-link">
					<div class="media-object pull-left es-avatar-wrap">
						<a href="<?php echo $item->friend->getPermalink();?>"
							class="es-avatar es-avatar-sm "
						>
							<img alt="<?php echo $this->html( 'string.escape' , $item->friend->getName() );?>" src="<?php echo $item->friend->getAvatar();?>" />
							<?php echo $this->loadTemplate( 'site/utilities/user.online.state' , array( 'online' => $item->friend->isOnline() , 'size' => 'mini' ) ); ?>
						</a>
					</div>

					<div class="media-body pl-10">
						<a href="<?php echo $item->friend->getPermalink();?>">
							<span class="widget-main-link"><?php echo $item->friend->getName(); ?></span>
						</a>
						<div class="total-no">
							<?php echo $cntText; ?>
						</div>
						<?php if( $this->my->getPrivacy()->validate( 'friends.request' , $item->friend->id ) ) { ?>
						<div class="mb-10" data-friend-suggest-button>
							<a href="javascript:void(0);" data-friend-suggest-add class="btn btn-mini btn-es-primary">
								<?php echo JText::_( 'APP_FRIENDS_SUGGEST_FRIENDS_ADD_FRIEND' ); ?>
							</a>
						</div>
						<?php } ?>
					</div>

				</div>
			</li>
			<?php } ?>
		</ul>
		<div class="fd-small pull-right pr-10">
			<a href="<?php echo FRoute::friends( array( 'filter' => 'suggest' ) ); ?>" class=""><?php echo JText::_( 'APP_FRIENDS_SUGGEST_FRIENDS_VIEW_ALL' ); ?></a>
		</div>
		<?php } else { ?>
		<div class="fd-small empty">
			<?php echo JText::_( 'APP_FRIENDS_SUGGEST_FRIENDS_NO_FRIENDS_SUGGESTION' ); ?>
		</div>
		<?php } ?>
	</div>
</div>
