<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-pager eb-responsive">

	<a href="<?php echo $data->start->link ? $data->start->link : 'javascript:void(0);';?>" class="eb-pager__fast-first-link <?php echo !$data->start->link ? ' disabled' : '';?>">
		<i class="fa fa-fast-backward"></i>
	</a>

	<?php if($data->previous->link) { ?>
		<a href="<?php echo EB::uniqueLinkSegments($data->previous->link); ?>" rel="prev" class="eb-pager__pre-link">
			<i class="fa fa-chevron-left"></i> <?php echo JText::_('COM_EASYBLOG_PAGINATION_PREVIOUS');?>
		</a>
	<?php } else { ?>
		<a href="javascript:void(0);" class="disabled eb-pager__pre-link">
			<i class="fa fa-chevron-left"></i> <?php echo JText::_('COM_EASYBLOG_PAGINATION_PREVIOUS');?>
		</a>
	<?php } ?>
	
	<a href="<?php echo $data->end->link ? $data->end->link : 'javascript:void(0);';?>" class="eb-pager__fast-last-link <?php echo !$data->end->link ? ' disabled' : '';?>">
		<i class="fa fa-fast-forward"></i>
	</a>

	<?php if($data->next->link) { ?>
		<a href="<?php echo EB::uniqueLinkSegments( $data->next->link ); ?>" rel="next" class="eb-pager__next-link">
			<?php echo JText::_('COM_EASYBLOG_PAGINATION_NEXT'); ?> <i class="fa fa-chevron-right"></i>
		</a>
	<?php } else { ?>
		<a href="javascript:void(0);" class="disabled eb-pager__next-link">
			<?php echo JText::_('COM_EASYBLOG_PAGINATION_NEXT');?><i class="fa fa-chevron-right"></i>
		</a>
	<?php } ?>
	
	

	<div class="eb-pager__link-list">
		
		<?php foreach ($data->pages as $page) { ?>
			<?php if ($page->link) { ?>
				<a href="<?php echo EB::uniqueLinkSegments($page->link); ?>"><?php echo $page->text;?></a>
			<?php } else { ?>
				<a class="disabled active"><?php echo $page->text;?></a>
			<?php 	} ?>
		<?php } ?>
		
	</div>
</div>
