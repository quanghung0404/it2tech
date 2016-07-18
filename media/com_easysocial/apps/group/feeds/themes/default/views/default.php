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
<div class="app-feeds app-groups" data-group-feeds data-groupid="<?php echo $group->id;?>" data-appid="<?php echo $appId;?>">

	<div class="es-filterbar row-table">
		<div class="col-cell filterbar-title"><?php echo JText::_('APP_GROUP_FEEDS_TITLE'); ?></div>

		<?php if ($group->isMember()) { ?>
		<div class="col-cell cell-tight">
			<a href="javascript:void(0);" class="btn btn-es-primary btn-sm pull-right" data-feeds-create>
				<?php echo JText::_('APP_GROUP_FEEDS_NEW_FEED'); ?>
			</a>
		</div>
		<?php } ?>
	</div>

	<div class="app-contents-wrap">
		<div class="feeds-browser app-contents<?php echo !$feeds ? ' is-empty' : '';?>" data-feeds-browser>
			<ul class="list-unstyled" data-feeds-sources>
			<?php foreach ($feeds as $feed) { ?>
				<?php echo $this->loadTemplate('apps/group/feeds/views/default.item', array('rss' => $feed, 'totalDisplayed' => $totalDisplayed)); ?>
			<?php } ?>
			</ul>

			<div class="empty empty-hero">
				<i class="fa fa-rss-square"></i>
				<div>
					<?php echo JText::_('APP_GROUP_FEEDS_EMPTY_FEEDS'); ?>
				</div>
			</div>
		</div>
	</div>

</div>
