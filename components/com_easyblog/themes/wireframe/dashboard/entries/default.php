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
<form method="post" action="<?php echo JRoute::_('index.php');?>" data-eb-dashboard-posts>
	<div class="eb-head">
		<h2 class="eb-head-title reset-heading pull-left">
			<i class="fa fa-file-text-o"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DASHBOARD_HEADING_POSTS');?>
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

	<?php echo EB::info()->html();?>

	<div class="eb-box eb-table">
		<div class="eb-table-head row-table align-middle">
			<div class="col-cell cell-check">
				<div class="eb-checkbox checkbox-primary">
					<input id="tick-all" type="checkbox" class="check-clear" data-eb-form-checkall />
					<label for="tick-all">
						&nbsp;
					</label>
				</div>
			</div>

			<div class="col-cell clearfix">
				<div class="row-table-form pull-left hide" data-eb-form-actions>
					<div class="col-cell">
						<select class="form-control input-sm" data-eb-form-task>
							<option value=""><?php echo JText::_('COM_EASYBLOG_BULK_ACTIONS');?></option>
							<option value="posts.copy"><?php echo JText::_('COM_EASYBLOG_COPY_SELECTED');?></option>

							<?php if( $this->acl->get('publish_entry') ){ ?>
							<option value="posts.publish" data-confirmation="site/views/dashboard/confirmPublish"><?php echo JText::_('COM_EASYBLOG_PUBLISH');?></option>
							<option value="posts.unpublish" data-confirmation="site/views/dashboard/confirmUnpublish"><?php echo JText::_('COM_EASYBLOG_UNPUBLISH');?></option>
							<?php } ?>

							<?php if( $this->acl->get('delete_entry') ){ ?>
							<option value="posts.delete" data-confirmation="site/views/dashboard/confirmDelete"><?php echo JText::_('COM_EASYBLOG_DELETE');?></option>
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
							<option value="all"<?php echo $state == 'all' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_ALL');?></option>
							<option value="<?php echo EASYBLOG_POST_PUBLISHED;?>"<?php echo $state === EASYBLOG_POST_PUBLISHED ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_PUBLISHED');?></option>
							<option value="<?php echo EASYBLOG_POST_UNPUBLISHED;?>"<?php echo $state === EASYBLOG_POST_UNPUBLISHED ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_UNPUBLISHED');?></option>
							<option value="<?php echo EASYBLOG_POST_SCHEDULED;?>"<?php echo $state === EASYBLOG_POST_SCHEDULED ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_SCHEDULED');?></option>
						</select>
					</div>

					<div class="col-cell">
						<select class="form-control input-sm" name="category">
							<option value=""><?php echo JText::_('COM_EASYBLOG_FILTER_SELECT_CATEGORY');?></option>

							<?php foreach ($categories as $category) { ?>
							<option value="<?php echo $category->id;?>"<?php echo $category->id == $categoryFilter ? ' selected="selected"' : '';?>><?php echo $category->getTitle();?></option>
							<?php } ?>
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
			<?php foreach ($posts as $post) { ?>
			<div class="row-table align-top" data-eb-post-item data-id="<?php echo $post->id;?>">
				<div class="col-cell cell-check">
					<div class="eb-checkbox checkbox-primary">
						<input id="<?php echo $post->id;?>" class="check-clear" type="checkbox" name="ids[]" value="<?php echo $post->id;?>" data-eb-form-checkbox />
						<label for="<?php echo $post->id;?>">
							&nbsp;
						</label>
					</div>

					<?php if ($post->isFeatured) { ?>
					<b class="check-post-star">
						<i class="fa fa-star check-star active" data-eb-provide="tooltip" data-title="<?php echo JText::_('COM_EASYBLOG_BLOG_POST_IS_FEATURED');?>"></i>
					</b>
					<?php } ?>

					<?php if ($post->locked) { ?>
					<b class="check-post-lock">
						<i class="fa fa-lock check-lock active" data-eb-provide="tooltip" data-title="<?php echo JText::_('COM_EASYBLOG_BLOG_POST_IS_LOCKED');?>"></i>
					</b>
					<?php } ?>

					<?php if ($post->posttype) { ?>
					<b class="check-post-type">
						<?php echo $post->getIcon(); ?>
					</b>
					<?php } ?>
				</div>

				<div class="col-cell cell-clear-right">

					<b class="eb-table-title">
						<a href="<?php echo $post->getPermalink();?>"><?php echo $post->title;?></a>
					</b>

					<?php if ($post->hasRevisionWaitingForApproval()) { ?>
					<div class="text-muted small">
						<i class="fa fa-lock"></i>&nbsp; <?php echo JText::sprintf('COM_EASYBLOG_POST_HAS_REVISION_WAITING_TO_BE_APPROVED'); ?>
					</div>
					<?php } ?>

					<div class="eb-table-meta text-muted text-small mt-5">
						<span>
							<i class="fa fa-user"></i>&nbsp; <a href="<?php echo $post->author->getPermalink();?>"><?php echo $post->author->getName();?></a>
						</span>

						<span>
							<i class="fa fa-calendar"></i>&nbsp; <?php echo $post->getCreationDate()->format(JText::_('DATE_FORMAT_LC1'));?>
						</span>

						<?php if ( $post->getTotalComments() ) { ?>
						<span>
							<i class="fa fa-comment"></i> <?php echo $post->getTotalComments();?>
						</span>
						<?php } ?>

						<span>
							<i class="fa fa-eye"></i> <?php echo $post->hits;?>
						</span>

						<?php if ($post->language != '*' && $post->language) { ?>
						<span>
							<i class="fa fa-language"></i> <?php echo $post->language;?>
						</span>
						<?php } ?>
					</div>


					<div class="eb-table-content mt-10 mb-10 pb-10">
						<?php if (!empty($post->content)) { ?>
							<?php echo JString::substr(strip_tags($post->content), 0, 250);?> <?php echo JText::_('COM_EASYBLOG_ELLIPSES');?>
						<?php } else { ?>
							<?php echo JString::substr(strip_tags($post->intro), 0, 250);?> <?php echo JText::_('COM_EASYBLOG_ELLIPSES');?>
						<?php } ?>
						
						<?php if ($post->isScheduled() || $post->isDraft()) { ?>
						<div class="text-muted small mt-5">
							<?php if ($post->isScheduled()) { ?>
								<i class="fa fa-clock-o"></i>&nbsp; <?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_ENTRIES_POST_IS_SCHEDULED_DESC', $post->getPublishDate()->format(JText::_('DATE_FORMAT_LC2'))); ?>
							<?php } ?>

							<?php if ($post->isDraft()) { ?>
								<span><?php echo JText::_('COM_EASYBLOG_DRAFT');?></span>
							<?php } ?>

							<?php if ($post->isRejected) { ?>
								<p><span class="label label-danger"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_DRAFTS_REJECTED_TITLE');?></span></p>
								<b><u><?php echo JText::_('COM_EASYBLOG_DASHBOARD_DRAFTS_REJECTED_REASON');?></u></b>
								<div><?php echo $post->isRejected->message;?></div>
							<?php } ?>							
						</div>
						<?php } ?>

						<div class="text-muted mt-10">
							<span class="mr-10">
								<i class="fa fa-pencil"></i>
								<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=revisions&uid=' . $post->id . '&state=' . EASYBLOG_REVISION_DRAFT); ?>">
									<?php echo $this->getNouns( 'COM_EASYBLOG_DASHBOARD_ENTRIES_NO_OF_DRAFT' , $post->getRevisionCount(EASYBLOG_REVISION_DRAFT) , true ); ?>
								</a>
							</span>
							<span>
								<i class="fa fa-random"></i>
								<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=revisions&uid=' . $post->id . '&state=' . EASYBLOG_REVISION_FINALIZED); ?>">
									<?php echo $this->getNouns( 'COM_EASYBLOG_DASHBOARD_ENTRIES_NO_OF_REVISION' , $post->getRevisionCount(EASYBLOG_REVISION_FINALIZED) , true ); ?>
								</a>
							</span>
						</div>
					</div>

					<div class="eb-table-relations text-small text-muted mt-5">
						<?php if ($post->isPublished()) { ?>
						<span>
							<i class="fa fa-check-circle"></i>
							<?php echo JText::_('COM_EASYBLOG_PUBLISHED'); ?>
						</span>
						<?php } ?>

						<?php if ($post->isUnpublished()) { ?>
						<span>
							<i class="fa fa-exclamation-circle"></i>
							<?php echo JText::_('COM_EASYBLOG_UNPUBLISHED'); ?>
						</span>
						<?php } ?>


						<span class="eb-table-categories type-1">
							<i class="fa fa-folder-open"></i>
							<?php foreach ($post->categories as $category) { ?>
							<span><a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a></span>
							<?php } ?>
						</span>


						<?php if ($post->getTags()) { ?>
						<span class="eb-table-tags type-2">
							<i class="fa fa-tags"></i>
							<?php foreach ($post->getTags() as $tag) { ?>
							<span><a href="<?php echo $tag->getPermalink();?>"><?php echo $tag->getTitle();?></a></span>
							<?php } ?>
						</span>
						<?php } ?>
					</div>
				</div>

				<?php if ($post->getImage('thumbnail', false)) { ?>
				<div class="col-cell cell-thumb">
					<img src="<?php echo $post->getImage('thumbnail');?>" width="120" height="90" />
				</div>
				<?php } ?>

				<?php
					if (($post->isPublished()
						|| $post->isUnpublished()
						|| $post->isFeatured
						|| !$post->isFeatured
						|| $this->acl->get('add_entry')
						|| $this->acl->get('delete_entry')
						|| $this->acl->get('feature_entry')
						|| EB::isSiteAdmin()
						|| $oauthClients)
						&& !$post->hasRevisionWaitingForApproval()
					) {
				?>
				<div class="col-cell cell-option">
					<div class="dropdown_">
						<a class="dropdown-toggle_" data-bp-toggle="dropdown">
							<i class="fa fa-bars"></i>
						</a>

						<ul class="dropdown-menu">
							<?php if ($post->isPublished() && ($this->acl->get('publish_entry') || EB::isSiteAdmin())) { ?>
								<li>
									<a href="javascript:void(0);" data-post-unpublish>
										<?php echo JText::_('COM_EASYBLOG_UNPUBLISH'); ?>
									</a>
								</li>
							<?php } ?>

							<?php if ($post->isUnpublished() && ($this->acl->get('publish_entry') || EB::isSiteAdmin())) { ?>
								<li>
									<a href="javascript:void(0);" data-post-publish>
										<?php echo JText::_('COM_EASYBLOG_PUBLISH'); ?>
									</a>
								</li>
							<?php } ?>

							<?php if (($post->isPublished() || $post->isUnpublished()) && ($this->acl->get('publish_entry') || EB::isSiteAdmin())) { ?>
								<li class="divider"></li>
							<?php } ?>


							<?php if ((!$post->locked && $this->acl->get('add_entry') && !$post->hasRevisionWaitingForApproval()) || EB::isSiteAdmin()) { ?>
								<li>
									<a href="<?php echo $post->getEditLink();?>" target="_blank" data-eb-composer>
										<?php echo JText::_('COM_EASYBLOG_EDIT'); ?>
									</a>
								</li>
							<?php } ?>

								<?php if ($this->acl->get('delete_entry')) { ?>
								<li>
									<a href="javascript:void(0);" data-post-delete>
										<?php echo JText::_('COM_EASYBLOG_DELETE');?>
									</a>
								</li>
							<?php } ?>

							<?php if ($post->isFeatured && $this->acl->get('feature_entry')) { ?>
								<li>
									<a href="javascript:void(0);" data-post-unfeature>
										<?php echo JText::_('COM_EASYBLOG_FEATURED_UNFEATURE_POST'); ?>
									</a>
								</li>
							<?php } ?>

								<?php if (!$post->isFeatured && $this->acl->get('feature_entry')) { ?>
								<li>
									<a href="javascript:void(0);" data-post-feature>
										<?php echo JText::_('COM_EASYBLOG_FEATURED_FEATURE_POST'); ?>
									</a>
								</li>
							<?php } ?>

							<?php if ($oauthClients) { ?>
								<li class="divider"></li>
								<li class="dropdown-header mb-5"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_AUTOPOSTING');?></li>

								<?php foreach ($oauthClients as $oauth) { ?>
								<li class="dropdown-autopost">
									<a href="javascript:void(0);"
										class="<?php echo $oauth->isShared($post->id) ? ' active' : '';?>"
										data-eb-provide="tooltip"
										data-post-autopost
										data-autopost-type="<?php echo $oauth->type;?>"

										<?php if ($oauth->isShared($post->id)) { ?>
										data-original-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_TOOLTIP_' . strtoupper($oauth->type) . '_POSTED');?>"
										<?php } else { ?>
										data-original-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_TOOLTIP_' . strtoupper($oauth->type));?>"
										<?php } ?>
									>
										<i class="fa fa-check"></i>
										<?php echo $oauth->type;?>
									</a>
								</li>
								<?php } ?>
							<?php } ?>
						</ul>
					</div>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
	</div>

	<div class="eb-box empty text-center<?php echo !$posts ? ' is-empty' : '';?>">
		<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ENTRIES_EMPTY'); ?></b>
		<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=composer&tmpl=component');?>" class="btn btn-primary btn-sm" data-eb-composer><?php echo JText::_('COM_EASYBLOG_CREATE_BUTTON');?></a>
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
