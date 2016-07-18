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
<form method="post" action="<?php echo JRoute::_('index.php');?>" data-eb-dashboard-posts>
	<div class="eb-head">
		<h2 class="eb-head-title reset-heading pull-left">
			<i class="fa fa-files-o"></i>&nbsp;

			<?php echo JText::_('COM_EASYBLOG_DASHBOARD_HEADING_DRAFTS');?>

			<a href="javascript:void(0);" class="eb-head-popover"
				rel="popover"
				data-placement="bottom"
				data-content="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_HEADING_DRAFTS_DESC');?>"
				>
				<i class="fa fa-info-circle"></i>
			</a>
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

			<?php if ($drafts) { ?>
			<a class="btn btn-default btn-danger" href="javascript:void(0);">
				<i class="fa fa-close"></i>&nbsp;
				<?php echo JText::_('COM_EASYBLOG_DISCARD_DRAFTS_BUTTON');?>
			</a>
			<?php } ?>
		</div>
	</div>

	<?php echo EB::info()->html();?>

	<?php if ($drafts) { ?>
	<div class="eb-box eb-table">
			<div class="eb-table-head row-table align-top">
				<div class="col-cell">
					<div class="col-cell cell-check">
						<div class="eb-checkbox">
							<input id="check-all" type="checkbox" data-eb-form-checkall />
							<label for="check-all">&nbsp;</label>
						</div>
					</div>
					<div class="col-cell">
						<div class="row-table-form pull-left hide" data-eb-form-actions>
							<div class="col-cell">
								<select class="form-control" data-eb-form-task>
									<option value=""><?php echo JText::_('COM_EASYBLOG_BULK_ACTIONS');?></option>
									<option value="drafts.discard"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_DRAFTS_DISCARD');?></option>
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
			</div>

			<div class="eb-table-body">
				<?php foreach ($drafts as $post) { ?>
				<div class="row-table align-top"> data-eb-post-item data-id="<?php echo $post->id;?>">
					<div class="col-ecell cell-check">
						<div class="eb-checkbox">
							<input id="<?php echo $post->id;?>" type="checkbox" name="ids[]" value="<?php echo $post->id;?>" data-eb-form-checkbox />
							<label for="<?php echo $post->id;?>">&nbsp;</label>
						</div>
					</div>
					<div class="col-cell">
						<?php if ($post->rejected) : ?>
						<div class="eb-draft-reject mb-15 pb-15">
							<div class="reject-note">
								<?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_DRAFTS_REJECTED' , $post->rejected->getAuthor()->getName() );?>
								<?php if ($post->rejected->message) : ?>
								<a href="javascript:void(0);" onclick="eblog.dashboard.toggle(this);" class="btn btn-sm btn-default"><?php echo JText::_('COM_EASYBLOG_SHOW_MESSAGE');?></a>
								<?php endif; ?>
							</div>

							<?php if ($post->rejected->message) : ?>
							<div class="reject-message media" style="display: none;">
								<img class="pull-left" src="<?php echo $post->rejected->getAuthor()->getAvatar();?>" width="32" height="32" />
								<div class="media-body">
									<?php echo $post->rejected->message; ?>
								</div>
							</div>
							<?php endif; ?>
						</div>
						<?php endif; ?>

						<b class="eb-table-title">
							<a href="<?php echo $post->getEditLink(); ?>"><?php echo $post->getTitle();?></a>
						</b>

						<div class="eb-table-meta text-muted text-small mt-5">
							<span>
								<i class="fa fa-calendar"></i>&nbsp; <?php echo $post->getCreated()->format(JText::_('DATE_FORMAT_LC1'));?>
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
							<?php echo JString::substr(strip_tags($post->content), 0, 250);?> <?php echo JText::_('COM_EASYBLOG_ELLIPSES');?>
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
					</div>

					<div class="col-cell cell-thumb">
						<img src="<?php echo $post->getBlogImage('thumbnail');?>" width="120" height="90" />
					</div>

					<div class="col-cell cell-option">
						<div class="dropdown_">
							<a class="dropdown-toggle_" data-bp-toggle="dropdown">
								<i class="fa fa-bars"></i>
							</a>

							<ul class="dropdown-menu">
								<?php if ($this->acl->get('add_entry') || EB::isSiteAdmin()) { ?>
								<li>
									<a href="<?php echo $post->getEditLink(); ?>">
										<?php echo JText::_('COM_EASYBLOG_EDIT'); ?>
									</a>
								</li>
								<?php } ?>

								<?php if ($this->acl->get('delete_entry') || EB::isSiteAdmin()) { ?>
								<li>
									<a href="javascript:void(0);" data-post-delete>
										<?php echo JText::_('COM_EASYBLOG_DASHBOARD_DRAFTS_DISCARD');?>
									</a>
								</li>
								<?php } ?>

								<li>
									<a target="_blank" href="<?php echo EB::_('index.php?option=com_easyblog&view=entry&layout=preview&draftid='.$post->id);?>">
										<?php echo JText::_('COM_EASYBLOG_PREVIEW'); ?>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
	</div>
	<?php } ?>

	<div class="eb-box empty text-center<?php echo !$drafts ? ' is-empty' : '';?>">
		<i class="fa fa-send-o"></i>
		<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_NO_DRAFTS_YET');?></b>
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
