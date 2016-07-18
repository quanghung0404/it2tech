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
<form method="post" action="<?php echo JRoute::_('index.php');?>" data-eb-dashboard-comments>
	<div class="eb-head">
		<h2 class="eb-head-title reset-heading pull-left">
			<i class="fa fa-comments"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DASHBOARD_HEADING_COMMENTS');?>
		</h2>
		<div class="eb-head-form form-inline pull-right">
			<div class="input-group pull-left" style="width: 250px">
				<input type="text" class="form-control" placeholder="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_SEARCH_COMMENTS');?>">
				<span class="input-group-btn">
					<a class="btn btn-default" href="javascript:void(0);" data-eb-form-search>
						<i class="fa fa-search"></i>
					</a>
				</span>
			</div>
		</div>
	</div>

	<?php echo EB::info()->html();?>

	<?php if ($comments) { ?>
		<div class="eb-box eb-table">
			<div class="eb-table-head row-table align-middle">
				<div class="col-cell cell-check">
					<?php echo $this->html('dashboard.checkall'); ?>
				</div>
				<div class="col-cell clearfix">
					<div class="row-table-form pull-left hide" data-eb-form-actions>
						<div class="col-cell">
							<select class="form-control input-sm" data-eb-form-task>
								<option value=""><?php echo JText::_('COM_EASYBLOG_BULK_ACTIONS');?></option>
								<?php if( $this->acl->get('manage_comment') ){ ?>
								<option value="comments.publish" data-confirmation="site/views/dashboard/confirmPublishComment"><?php echo JText::_('COM_EASYBLOG_PUBLISH');?></option>
								<option value="comments.unpublish" data-confirmation="site/views/dashboard/confirmUnpublishComment"><?php echo JText::_('COM_EASYBLOG_UNPUBLISH');?></option>
								<?php } ?>

								<?php if( $this->acl->get('delete_comment')){ ?>
								<option value="comments.delete" data-confirmation="site/views/dashboard/confirmDeleteComment"><?php echo JText::_('COM_EASYBLOG_DELETE');?></option>
								<?php } ?>
							</select>
						</div>
						<div class="col-cell">
							<a class="btn btn-default btn-sm" href="javascript:void(0);" data-eb-form-apply>
								<i class="fa fa-save"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_APPLY_BUTTON');?>
							</a>
						</div>
					</div>

					<div class="row-table-form pull-right">
						<div class="col-cell">
							<select class="form-control input-sm" name="filter">
								<option value="all"<?php echo $filter == 'all' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_ALL');?></option>
								<option value="published"<?php echo $filter == 'published' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_PUBLISHED');?></option>
								<option value="unpublished"<?php echo $filter == 'unpublished' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_UNPUBLISHED');?></option>
								<option value="pending"<?php echo $filter == 'pending' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_PENDING');?></option>
							</select>
						</div>
						<div class="col-cell">
							<a class="btn btn-default btn-sm" data-eb-form-filter>
								<i class="fa fa-filter"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_FILTER');?>
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="eb-table-body">
				<?php if ($comments) { ?>
					<?php foreach ($comments as $comment) { ?>
					<div class="row-table layout-fixed align-top" data-eb-comment-item data-id="<?php echo $comment->id;?>">
						<div class="col-cell cell-check">
							<?php echo $this->html('dashboard.id', 'ids[]', $comment->id);?>
						</div>

						<div class="col-cell">
							<div class="media">
								<img src="<?php echo $comment->getAuthorAvatar();?>" class="pull-left mr-15" width="50" height="50" />

								<div class="media-body">
									<div class="eb-table-headlines">
										<b><?php echo $comment->getAuthorName();?></b>
										<small>
											<?php echo JText::sprintf($comment->getCreated()->format(JText::_('DATE_FORMAT_LC2')));?>
										</small>
									</div>

									<div class="eb-table-comment">
										<?php echo $comment->getContent();?>
									</div>
								</div>
							</div>
						</div>

						<div class="col-cell cell-option">
							<div class="dropdown_">
								<a class="dropdown-toggle_" data-bp-toggle="dropdown">
									<i class="fa fa-bars"></i>
								</a>

								<ul class="dropdown-menu">
									<?php if ($this->acl->get('edit_comment') ) { ?>
									<li>
										<?php echo $this->html('dashboard.action', 'COM_EASYBLOG_EDIT', 'site/views/dashboard/editComment'); ?>
									</li>
									<li class="divider"></li>
									<?php } ?>

									<?php if ($this->acl->get('manage_comment') && $comment->isPublished()) { ?>
									<li>
										<?php echo $this->html('dashboard.action', 'COM_EASYBLOG_UNPUBLISH', 'site/views/dashboard/confirmUnpublishComment'); ?>
									</li>
									<?php } ?>

									<?php if ($this->acl->get('manage_comment') && $comment->isUnpublished()) { ?>
									<li>
										<?php echo $this->html('dashboard.action', 'COM_EASYBLOG_PUBLISH', 'site/views/dashboard/confirmPublishComment'); ?>
									</li>
									<?php } ?>

									<?php if ($this->acl->get('delete_comment') ) { ?>
									<li>
										<?php echo $this->html('dashboard.action', 'COM_EASYBLOG_DELETE', 'site/views/dashboard/confirmDeleteComment'); ?>
									</li>
									<li class="divider"></li>
									<?php } ?>

									<li>
										<a href="<?php echo $comment->getPermalink();?>"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_VIEW_COMMENT');?></a>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
	<?php } ?>

	<div class="eb-box empty text-center<?php echo !$comments ? ' is-empty' : '';?>">
		<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_COMMENTS_EMPTY'); ?></b>
	</div>

	<?php if ($pagination) { ?>
	<div class="eb-box-pagination pagination text-center">
		<?php echo $pagination->getPagesLinks(); ?>
	</div>
	<?php } ?>

	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="comments" />
	<?php echo $this->html('form.action'); ?>
</form>
