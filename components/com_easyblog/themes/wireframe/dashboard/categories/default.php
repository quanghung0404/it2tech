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
defined('_JEXEC') or die('Unauthorized Access');
?>
<form method="post" action="<?php echo JRoute::_('index.php');?>" data-eb-dashboard-categories>

	<div class="eb-head">
		<h2 class="eb-head-title reset-heading pull-left">
			<i class="fa fa-folder-open-o"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DASHBOARD_HEADING_CATEGORIES');?>
		</h2>
		<div class="eb-head-form form-inline pull-right">
			<div class="input-group pull-left" style="width: 250px">
				<input type="text" class="form-control" name="search" placeholder="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_SEARCH_CATEGORY');?>" value="<?php echo ($search) ? $search: '';?>">
				<span class="input-group-btn">
					<a class="btn btn-default" href="javascript:void(0);" data-eb-form-search>
						<i class="fa fa-search"></i>
					</a>
				</span>
			</div>

			<a class="btn btn-default ml-5" data-eb-categories-create><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_CREATE');?></a>
		</div>
	</div>

	<?php echo EB::info()->html();?>

	<?php if ($categories) { ?>
	<div class="eb-table eb-box">
		<div class="eb-table-body">
			<?php foreach ($categories as $category) { ?>
			<div class="row-table align-top" data-eb-category-item data-id="<?php echo $category->id;?>">
				<div class="col-cell">
					<div class="media">
						<img src="<?php echo $category->getAvatar();?>" class="pull-left mr-15" width="50" height="50" />

						<div class="media-body">
							<b class="eb-table-title">
								<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
							</b>

							<div class="eb-table-meta">
								<span><?php echo $this->getNouns('COM_EASYBLOG_DASHBOARD_CATEGORIES_POST_COUNT', $category->getPostCount(), true); ?></span>
								&middot;
								<span><?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_CATEGORIES_CHILD_COUNT' , $category->getChildCount()); ?></span>
								&middot;
								<span>
									<?php if(! $showAuthorName) { ?>
									<?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_CATEGORIES_CREATED_ON' , $this->formatDate(JText::_('DATE_FORMAT_LC1'), $category->created));?>
									<?php } else { ?>
									<?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_CATEGORIES_CREATED_BY_ON' , EB::user($category->created_by)->getName(), $this->formatDate(JText::_('DATE_FORMAT_LC1'), $category->created));?>
									<?php } ?>
								</span>
							</div>

							<div class="eb-table-toolbar btn-toolbar">
								<div class="btn-group btn-group-sm">
									<?php echo $this->html('dashboard.action', 'COM_EASYBLOG_EDIT', 'site/views/dashboard/categoryForm'); ?>

									<?php if($category->getPostCount() <= 0 && $category->getChildCount() <= 0 && $this->acl->get('delete_category') ) { ?>
										<?php echo $this->html('dashboard.action', 'COM_EASYBLOG_DELETE', 'site/views/dashboard/confirmDeleteCategory'); ?>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php } ?>

	<div class="eb-box empty text-center<?php echo !$categories ? ' is-empty' : '';?>">
		<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_EMPTY'); ?></b>
	</div>

	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="categories" />
	<?php echo $this->html('form.action'); ?>
</form>
