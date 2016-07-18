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
<div class="eb-head">
	<h2 class="eb-head-title reset-heading pull-left">
		<i class="fa fa-tags"></i> <?php echo JText::_('COM_EASYBLOG_DASHBOARD_TAGS');?>
	</h2>
</div>

<?php echo EB::info()->html();?>

<?php if ($this->acl->get('create_tag') || EB::isSiteAdmin()) { ?>
<form id="tags" method="post" action="<?php echo JRoute::_('index.php');?>">
	<div class="eb-box">
		<div class="eb-box-head">
			<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TAG_CREATE_NEW');?>
		</div>
		<div class="eb-box-body">
			<p class="eb-box-lead">
				<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TAG_CREATE_NEW_HELP');?>
			</p>
			<div class="input-group">
				<input type="text" name="tags" class="form-control" placeholder="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TAG_CREATE_NEW_PLACEHOLDER');?>" />
				<span class="input-group-btn">
					<button class="btn btn-primary"><?php echo JText::_('COM_EASYBLOG_ADD_TAG_BUTTON');?></button>
				</span>
			</div>
		</div>
	</div>
	<?php echo $this->html('form.action', 'tags.create'); ?>
</form>
<?php } ?>

<form id="tags" method="post" action="<?php echo JRoute::_('index.php');?>" data-tags-form>
	<div class="eb-box">
		<div class="eb-box-head">
			<div class="row">
				<div class="col-md-6">
					<div class="eb-tag-finder input-group">
						<input type="text" name="search" class="form-control" placeholder="<?php echo JText::_('COM_EASYBLOG_SEARCH_PLACEHOLDER');?>" value="<?php echo $this->html('string.escape', $search);?>" data-search-tag />
						<div class="input-group-btn">
							<button type="submit" class="btn btn-default"><?php echo JText::_('COM_EASYBLOG_SEARCH_BUTTON');?></button>
							<button type="button" class="btn btn-default" data-reset-search>
								<i class="fa fa-close"></i>
							</button>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="eb-tag-sorter btn-group pull-right">
						<button type="button" class="btn btn-default<?php echo $sort == 'asc' ? ' active' : '';?>" data-sort-item data-sort-type="asc">
							<?php echo JText::_('Ascending'); ?>
						</button>
						<button type="button" class="btn btn-default<?php echo $sort == 'desc' ? ' active' : '';?>" data-sort-item data-sort-type="desc">
							<?php echo JText::_('Descending');?>
						</button>
						<button type="button" class="btn btn-default<?php echo $sort == 'post' ? ' active' : '';?>" data-sort-item data-sort-type="post">
							<?php echo JText::_('Tag Weight');?>
						</button>
					</div>
				</div>
			</div>
		</div>
		<div class="eb-box-body">

			<?php if ($tags) { ?>
			<div class="eb-box-tags">
				<?php foreach ($tags as $tag) { ?>
				<div class="eb-box-tag">
					<i class="fa fa-tag"></i>
					<a class="eb-box-tag-remove" href="javascript:void(0);" data-tag-remove data-id="<?php echo $tag->id;?>">
						<i class="fa fa-close"></i>
					</a>
					<a class="eb-box-tag-link" href="<?php echo EB::_('index.php?option=com_easyblog&view=tags&layout=tag&id=' . $tag->id);?>"><?php echo $tag->title;?></a>
					<b><?php echo $tag->post_count;?></b>
				</div>
				<?php } ?>
			</div>
			<?php } else { ?>
				<div class="eb-empty text-center">
					<?php echo JText::_('COM_EASYBLOG_DASHBOARD_NO_TAGS_AVAILABLE'); ?>
				</div>
			<?php } ?>

		</div>
	</div>
	
	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="tags" />
	<input type="hidden" name="sort" value="<?php echo $this->html('string.escape', $sort);?>" data-sort-value />
	<?php echo $this->html('form.token'); ?>
</form>