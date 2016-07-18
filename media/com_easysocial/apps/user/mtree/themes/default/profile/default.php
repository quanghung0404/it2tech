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
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="app-mtree">
	<div class="mtree-listings">
		<h4><?php echo JText::_('APP_USER_MTREE_HEADING_MY_LISTINGS');?></h4>
		<hr />

		<?php foreach ($items as $item) { ?>
			<?php echo $this->output('apps:/user/mtree/themes/default/profile/item', array('item' => $item, 'Itemid' => $Itemid, 'params' => $params)); ?>
		<?php } ?>

		<div class="text-right">
			<a href="<?php echo JRoute::_('index.php?option=com_mtree&task=viewowner&user_id=' . $item->user_id); ?>" class="btn btn-es btn-sm"><?php echo JText::_('APP_USER_MTREE_VIEW_ALL_LISTINGS'); ?></a>
		</div>
	</div>

	<div class="mtree-listings">
		<h4><?php echo JText::_('APP_USER_MTREE_HEADING_MY_FAVORITES');?></h4>
		<hr />

		<?php foreach ($favorites as $favorite) { ?>
			<?php echo $this->output('apps:/user/mtree/themes/default/profile/item', array('item' => $favorite, 'Itemid' => $Itemid, 'params' => $params)); ?>
		<?php } ?>

		<div class="text-right">
			<a href="<?php echo JRoute::_('index.php?option=com_mtree&task=viewusersfav&user_id=' . $favorite->user_id); ?>" class="btn btn-es btn-sm"><?php echo JText::_('APP_USER_MTREE_VIEW_ALL_FAVORITES'); ?></a>
		</div>
	</div>

	<div class="mtree-listings">
		<h4><?php echo JText::_('APP_USER_MTREE_HEADING_MY_REVIEWS');?></h4>
		<hr />

		<?php foreach ($reviews as $review) { ?>
			<?php echo $this->output('apps:/user/mtree/themes/default/profile/item', array('item' => $review, 'Itemid' => $Itemid, 'params' => $params)); ?>
		<?php } ?>

		<div class="text-right">
			<a href="<?php echo JRoute::_('index.php?option=com_mtree&task=viewusersreview&user_id=' . $favorite->user_id); ?>" class="btn btn-es btn-sm"><?php echo JText::_('APP_USER_MTREE_VIEW_ALL_REVIEWS'); ?></a>
		</div>
	</div>
</div>