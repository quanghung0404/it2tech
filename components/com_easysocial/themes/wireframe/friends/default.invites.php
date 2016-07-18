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
defined('_JEXEC') or die('Unauthorized Access');
?>
<div data-friends-content>
	<div class="es-snackbar row-table">
		<div class="col-cell">
			<?php echo JText::_('COM_EASYSOCIAL_FRIENDS_INVITES_HEADING'); ?>
		</div>
		<div class="col-cell text-right fd-small">
			<a href="<?php echo FRoute::friends(array('layout' => 'invite'));?>">
				<?php echo JText::_('COM_EASYSOCIAL_INVITE_FRIEND_BUTTON');?>
			</a>
		</div>
	</div>

	<ul class="es-item-grid friend-items es-item-grid_1col<?php echo !$friends ? ' is-empty' : '';?>" data-friends-items>
		<?php if($friends){ ?>
			<?php foreach ($friends as $user) { ?>
				<?php echo $this->loadTemplate('site/friends/default.invites.item' , array('user' => $user, 'filter' => $filter) ); ?>
			<?php } ?>
		<?php } ?>

		<li class="empty center mt-20" data-friends-emptyItems>
			<i class="icon-es-empty-friends mb-10"></i>
			<div>
				<?php echo JText::_('COM_EASYSOCIAL_FRIENDS_NO_INVITES_SENT'); ?>
			</div>
		</li>
	</ul>

	<div class="es-pagination-footer">
		<?php echo $pagination->getListFooter('site');?>
	</div>
</div>
