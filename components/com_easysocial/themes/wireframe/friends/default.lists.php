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
<div class="friends-list es-widget">
	<div class="es-widget-head">
		<div class="pull-left widget-title"><?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_YOUR_LIST' );?></div>
		<?php if( $this->my->id == $user->id && $this->access->allowed( 'friends.list.enabled' ) && !$this->access->exceeded( 'friends.list.limit' , $totalFriendsList ) ){ ?>
		<a href="<?php echo FRoute::friends( array( 'layout' => 'listForm' ) );?>" class="pull-right">
			<i class="icon-es-add"></i>
			<span><?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_NEW_LIST' ); ?></span>
		</a>
		<?php } ?>
	</div>
	<div class="es-widget-body" data-friends-list>
		<?php if( $lists ){ ?>
		<ul class="widget-list widget-list-with-count fd-nav fd-nav-stacked" data-friends-listItems>
			<?php foreach( $lists as $list ){ ?>
				<li class="filter-item item-<?php echo $list->id;?><?php echo $activeList->id == $list->id ? ' active' : '';?><?php echo $list->default ? ' default' : '';?>"
					 data-list-<?php echo $list->id;?>
					 data-id="<?php echo $list->id;?>"
					 data-title="<?php echo $this->html( 'string.escape' , $list->get( 'title' ) );?>"
					 data-url="<?php echo FRoute::friends( array( 'listId' => $list->id ) );?>"
					 data-friends-listItem
					>
					<a href="javascript:void(0);">
						<i class="fa fa-star  filter-item-default"></i>
						<?php echo $this->html( 'string.escape' , $list->get( 'title' ) ); ?>
					</a>


					<span class="es-count-no pull-right" data-list-counter><?php echo $list->getCount();?></span>
				</li>
			<?php } ?>
		</ul>
		<?php } else { ?>
		<div class="fd-small">
			<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_NO_LIST_CREATED_YET' ); ?>
		</div>
		<?php } ?>
	</div>
</div>
