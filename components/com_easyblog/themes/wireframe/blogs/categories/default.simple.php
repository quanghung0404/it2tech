<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="eb-categories">
<?php if ($categories) { ?>
	<?php foreach ($categories as $category) { ?>
	<div class="eb-category-sm">
		<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$category->id); ?>" class="eb-category-sm-thumb">
			<img src="<?php echo $category->getAvatar();?>" align="top" class="eb-category-sm-avatar img-circle" width="50" height="50" alt="<?php echo $category->getTitle();?>" />
		</a>
		<div class="eb-category-sm-body">
			<h2 class="eb-category-sm-name reset-heading">
				<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
			</h2>

			<div class="eb-category-sm-subscribe">
			<?php if ($this->config->get('main_categorysubscription') && !$category->isCategorySubscribed ) { ?>
					<?php if( ($category->private && $this->my->id != 0 ) || ($this->my->id == 0 && $system->config->get( 'main_allowguestsubscribe' )) || $system->my->id != 0) { ?>
						<span class="eb-category-sm-subscription">
							<i class="fa fa-envelope text-muted"></i>
							<a href="javascript:void(0);" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_CATEGORY'); ?>" class="link-subscribe" data-blog-subscribe data-type="category" data-id="<?php echo $category->id;?>">
								<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_CATEGORY'); ?>
							</a>
						</span>
					<?php } ?>
				<?php } else { ?>
						<span class="eb-category-sm-subscription">
							<i class="fa fa-envelope text-muted"></i>
							<a href="javascript:void(0);" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE_CATEGORY'); ?>" class="link-subscribe" data-blog-unsubscribe data-type="category" data-id="<?php echo $category->id;?>" data-email="<?php echo $this->my->email;?>">
								<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE_CATEGORY'); ?>
							</a>
						</span>
				<?php } ?>

				<?php if ($this->config->get('main_rss')) { ?>
					<span class="eb-category-sm-rss">
						<i class="fa fa-rss text-muted"></i>
						<a href="<?php echo $category->getRssLink();?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS', false); ?>" class="link-rss"><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?></a>
						</a>
					</span>
				<?php } ?>
				<span class="eb-category-sm-total">
					<i class="fa fa-file-text text-muted"></i>
					<a class="link-rss" href="#posts-<?php echo $category->id; ?>" data-bp-toggle="tab">
						<?php echo $category->cnt; ?>
						<?php echo JText::_('COM_EASYBLOG_BLOGGERS_TOTAL_POSTS');?>
					</a>
				</span>
			</div>
			<?php if(! empty($category->nestedLink)) { ?>
				<div class="eb-category-subs">
					<p>
						<b><?php echo JText::_('COM_EASYBLOG_CATEGORIES_SUBCATEGORIES'); ?></b>
					</p>
					<?php echo $category->nestedLink; ?>
				</div>
			<?php } ?>
		</div>
	</div>

	<?php } ?>
<?php } else { ?>
	<div class="eb-empty"><?php echo JText::_('COM_EASYBLOG_NO_RECORDS_FOUND'); ?></div>
<?php } ?>

<?php if ($pagination) { ?>
	<div class="pagination clearfix">
		<?php echo $pagination; ?>
	</div>
<?php } ?>
</div>
