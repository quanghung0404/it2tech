<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Restricted access');
?>
<div class="eb-mod-item">
	<div<?php echo ($params->get('layouttype') == 'tree')? ' style="padding-left: ' . $padding . 'px;"' : '';?>>

 	<?php if ($showCategoryAvatar) { ?>
		<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$category->id);?>" class="eb-mod-media-thumb">
			<img class="avatar" src="<?php echo modEasyBlogCategoriesHelper::getAvatar($category); ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" alt="<?php echo $category->title; ?>" />
		</a>
	<?php } else { ?>
		<i class="eb-mod-media-thumb fa fa-folder mod-muted"></i>
	<?php } ?>

		<div class="eb-mod-media-body">
			<a class="eb-mod-media-title" href="<?php echo EBR::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$category->id);?>"><?php echo JText::_($category->title); ?></a>
			<?php if ($params->get('showcount', true)) { ?>
				<?php if ($showCategoryAvatar) { ?>
				<div class="eb-mod-media-count mod-small mod-muted"> <?php echo JText::sprintf('MOD_EASYBLOGCATEGORIES_COUNT', $category->cnt);?></div>
				<?php } else { ?>
				<span class="eb-mod-media-count mod-small mod-muted"><?php echo JText::sprintf($category->cnt);?></span>
				<?php } ?>
			<?php } ?>

			<?php if ($params->get('showrss', true)) { ?>
			<div class="mod-small">
				<a class="eb-brand-rss" title="<?php echo JText::_('MOD_EASYBLOGCATEGORIES_SUBSCRIBE_FEEDS'); ?>" href="<?php echo EB::feeds()->getFeedURL('index.php?option=com_easyblog&view=categories&id=' . $category->id, false, 'category'); ?>">
					<i class="fa fa-rss-square"></i>
					<?php echo JText::_('MOD_EASYBLOGCATEGORIES_SUBSCRIBE_FEEDS'); ?>
				</a>
			</div>
			<?php } ?>	
		</div>
	</div>
</div>
