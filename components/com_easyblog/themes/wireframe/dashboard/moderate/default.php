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
<form method="post" action="<?php echo JRoute::_('index.php');?>" data-eb-dashboard-moderate>
	<div class="eb-head">
		<h2 class="eb-head-title reset-heading pull-left">
			<i class="fa fa-check-square"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DASHBOARD_HEADING_MODERATE');?>
		</h2>
		<div class="eb-head-form form-inline pull-right">
			<div class="input-group pull-left" style="width: 200px">
				<input type="text" class="form-control" name="post-search" placeholder="<?php echo JText::_('COM_EASYBLOG_SEARCH_FOR_POSTS');?>" value="<?php echo $this->html('string.escape', $search);?>" />
				<span class="input-group-btn">
					<a class="btn btn-default" href="javascript:void(0);" data-eb-form-search title="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_SEARCH_POSTS');?>">
						<i class="fa fa-search"></i>
					</a>
				</span>
			</div>
		</div>
	</div>

	<?php if ($posts) { ?>
	<div class="eb-box eb-table">
		<div class="eb-table-head align-top">
			<div class="col-cell cell-check">
				<div class="eb-checkbox">
					<input id="check-all" type="checkbox" data-eb-form-checkall />
					<label for="check-all"></label>
				</div>
			</div>
			<div class="col-cell">
				<div class="row-table-form pull-left hide" data-eb-form-actions>
					<div class="col-cell">
						<select class="form-control" data-eb-form-task>
							<option value=""><?php echo JText::_('COM_EASYBLOG_BULK_ACTIONS');?></option>
							<option value="moderate.approve"><?php echo JText::_('COM_EASYBLOG_APPROVE');?></option>
							<option value="moderate.reject"><?php echo JText::_('COM_EASYBLOG_REJECT');?></option>
						</select>
					</div>

					<div class="col-cell">
						<a class="btn btn-default" href="javascript:void(0);" data-eb-form-apply>
							<i class="fa fa-save"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_APPLY_BUTTON');?>
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="eb-table-body">
			<?php foreach ($posts as $post) { ?>
			<div class="row-table align-top" data-eb-moderate-item data-id="<?php echo $post->uid;?>">
				<div class="col-cell cell-check">
					<div class="eb-checkbox">
						<input id="<?php echo $post->uid;?>" type="checkbox" name="ids[]" value="<?php echo $post->uid;?>" data-eb-form-checkbox />
						<label for="<?php echo $post->uid;?>"></label>
					</div>
				</div>

				<div class="col-cell">
					<b class="eb-table-title">
						<a href="<?php echo $post->getEditLink();?>" target="_blank" data-eb-composer><?php echo $post->title;?></a>
					</b>

					<div class="eb-table-meta text-muted text-small mt-5">
						<span>
							<i class="fa fa-calendar"></i>&nbsp; <?php echo $post->getCreationDate()->format(JText::_('DATE_FORMAT_LC1'));?>
						</span>
					</div>

					<?php if ($post->posttype) { ?>
					<span>
						<b class="item-type item-type-<?php echo strtolower($post->posttype);?>" title="<?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_' . strtoupper($post->posttype)); ?>">
							<?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_' . strtoupper($post->posttype)); ?>
						</b>
					</span>
					<?php } ?>

					<div class="eb-table-content mt-10 mb-10 pb-10">
						<?php echo $this->html('string.truncater', strip_tags($post->getIntro()), 250);?> <?php echo JText::_('COM_EASYBLOG_ELLIPSES');?>
					</div>


					<div class="eb-table-relations text-small text-muted mt-5">
						<span class="eb-table-categories type-1">
							<i class="fa fa-folder-open"></i>
							<span>
								<a href="<?php echo $post->getPrimaryCategory()->getPermalink();?>">
									<?php echo $post->getPrimaryCategory()->getTitle();?>
								</a>
							</span>
						</span>
						<?php if ($post->tags) { ?>
						<span class="eb-table-tags type-2">
							<i class="fa fa-tags"></i>
							<?php foreach ($post->tags as $tag) { ?>
							<span><a href="javascript:void(0);"><?php echo $tag;?></a></span>
							<?php } ?>
						</span>
						<?php } ?>
					</div>

					<div class="eb-table-toolbar btn-toolbar mt-15">
						<div class="btn-group btn-group-sm">
							<?php if ($this->acl->get('manage_pending') && $this->acl->get('add_entry') || EB::isSiteAdmin()) { ?>
								<a href="<?php echo $post->getEditLink(); ?>" class="btn btn-default" target="_blank" data-eb-composer>
									<?php echo JText::_('COM_EASYBLOG_DASHBOARD_PENDING_REVIEW_POST'); ?>
								</a>

								<?php echo $this->html('dashboard.action', 'COM_EASYBLOG_DASHBOARD_PENDING_APPROVE_POST', 'site/views/dashboard/confirmApproveBlog'); ?>
							<?php } ?>

							<?php if ($this->acl->get('manage_pending') ) { ?>
								<?php echo $this->html('dashboard.action', 'COM_EASYBLOG_DASHBOARD_PENDING_REJECT_POST', 'site/views/dashboard/confirmRejectBlog'); ?>
							<?php } ?>
						</div>
					</div>
				</div>

				<div class="col-cell cell-thumb">
					<img src="<?php echo $post->getImage('thumbnail');?>" width="120" height="90" />
				</div>

			</div>
			<?php } ?>
		</div>
	</div>
	<?php } ?>

	<div class="eb-box text-center empty<?php echo !$posts ? ' is-empty' : '';?>">
		<i class="fa fa-send-o"></i>
		<?php echo JText::_('COM_EASYBLOG_NO_POST_PENDING_MODERATION_YET');?>
	</div>

	<?php if ($pagination) { ?>
	<div class="eb-box-pagination pagination text-center">
		<?php echo $pagination->getPagesLinks(); ?>
	</div>
	<?php } ?>

	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="entries" />
	<?php echo $this->html('form.action'); ?>
</form>
