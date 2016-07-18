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
<form method="post" action="<?php echo JRoute::_('index.php');?>" data-eb-dashboard-revisions>
	<div class="eb-head">
		<h2 class="eb-head-title reset-heading pull-left">
			<i class="fa fa-files-o"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DASHBOARD_HEADING_REVISIONS');?>
		</h2>
	</div>

	<?php if ($activePost) { ?>
	<div class="eb-box">
		<div class="eb-box-body">
			<h3 class="reset-heading" style="font-size: 16px;">
				<?php echo $activePost->title; ?>
			</h3>

			<div class="mt-5" style="font-size: 12px;">
				<span class="text-muted"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_REVISIONS_TOTAL'); ?></span>
				<span><?php echo $this->getNouns( 'COM_EASYBLOG_DASHBOARD_ENTRIES_NO_OF_REVISION' , $activePost->getRevisionCount('all') , true ); ?></span>
				&middot;
				<span class="text-muted"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_REVISIONS_LAST_UPDATE'); ?></span>
				<span><?php echo $activePost->getModifiedDate()->toFormat(JText::_('DATE_FORMAT_LC1')); ?></span>
			</div>
		</div>
	</div>
	<?php } ?>

	<?php if ($posts) { ?>
	<div class="eb-box eb-table">
		<div class="eb-table-head row-table align-midle">
			<div class="col-cell cell-check">
				<div class="eb-checkbox">
					<input id="check-all" class="check-clear" type="checkbox" data-eb-form-checkall />
					<label for="check-all">&nbsp;</label>
				</div>
			</div>

			<div class="col-cell">
				<div class="row-table-form pull-left hide" data-eb-form-actions>
					<div class="col-cell">
						<select class="form-control input-sm" data-eb-form-task>
							<option value=""><?php echo JText::_('COM_EASYBLOG_BULK_ACTIONS');?></option>
							<option value="posts.deleteRevisions" data-confirmation="site/views/dashboard/confirmRevisionDelete"><?php echo JText::_('COM_EASYBLOG_DELETE');?></option>
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
						<select class="form-control input-sm" name="state">
							<option value="all"<?php echo $state == 'all' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_ALL');?></option>
							<option value="<?php echo EASYBLOG_REVISION_FINALIZED;?>"<?php echo $state == EASYBLOG_REVISION_FINALIZED ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_REVISION_FINALIZED');?></option>
							<option value="<?php echo EASYBLOG_REVISION_DRAFT;?>"<?php echo $state == EASYBLOG_REVISION_DRAFT ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_REVISION_DRAFT');?></option>
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
			<div class="row-table layout-fixed align-top" data-eb-moderate-item data-id="<?php echo $post->revision_id;?>">
				<div class="col-cell cell-check">
					<div class="eb-checkbox">
						<input id="<?php echo $post->revision_id;?>" type="checkbox" class="check-clear" name="ids[]" value="<?php echo $post->revision_id;?>" data-eb-form-checkbox />
						<label for="<?php echo $post->revision_id;?>">&nbsp;</label>
					</div>

					<?php if ($post->isFinalized()) { ?>
					<b class="check-post-type">
						<i class=" fa fa-check-circle text-success" data-eb-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_REVISIONS_FINALIZED_VERSION'); ?>" data-placement="bottom"></i>
					</b>
					<?php } ?>

					<?php if ($post->isDraft()) { ?>
					<b class="check-post-type">
						<i class=" fa fa-clock-o text-muted" data-eb-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_REVISIONS_DRAFT_VERSION'); ?>" data-placement="bottom"></i>
					</b>
					<?php } ?>
				</div>

				<div class="col-cell cell-clear-right">
					<div class="eb-table-revision mb-5">
						<a href="<?php echo $post->getEditLink(); ?>" style="text-transform: uppercase; font-size: 11px; color: #999;" data-eb-composer><?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_REVISION_NUMBER', $post->revisionOrdering);?></a>
					</div>

					<div class="eb-table-title">
						<a href="<?php echo $post->getEditLink(); ?>" data-eb-composer><?php echo $post->title;?></a>
					</div>

					<div class="eb-table-meta text-muted text-small mt-5">
						<span>
							<i class="fa fa-calendar"></i>&nbsp; <?php echo $post->getCreationDate()->toFormat(JText::_('DATE_FORMAT_LC1')); ?>
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
						<?php echo $this->html('string.truncater', strip_tags($post->getIntro()), 250); ?>
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
		<?php echo JText::_('COM_EASYBLOG_NO_REVISIONS_CREATED_YET');?>
	</div>

	<?php if ($pagination) { ?>
	<div class="eb-box-pagination pagination text-center">
		<?php echo $pagination->getPagesLinks(); ?>
	</div>
	<?php } ?>

	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="revisions" />
	<?php echo $this->html('form.action'); ?>
</form>
