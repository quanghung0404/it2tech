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
		<i class="fa fa-dashboard"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DASHBOARD_HEADING_STATISTICS');?>
	</h2>
</div>

<div class="eb-box">
	<div class="eb-box-head"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_STATISTICS_PAGE_HEADING');?></div>
	<div class="eb-box-body">

		<?php if ($this->acl->get('manage_pending') && $pending) { ?>
		<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=moderate');?>" class="eb-box-lead notice">
			<?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_STATS_REQUIRE_MODERATION', $pending);?>
		</a>
		<?php } ?>

		<div class="row">
			<div class="col-lg-6">
				<p style="margin-bottom: 15px; text-transform: uppercase;" class="text-small text-muted"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_STATISTICS_RECENT_POST_HEADING');?></p>
				<div class="eb-stats-listing">
				<?php if ($latest) { ?>
					<?php foreach ($latest as $latestPost) { ?>
					<div>
						<b>
							<a href="<?php echo $latestPost->getPermalink();?>"><?php echo $latestPost->title;?></a>
						</b>
						<div class="text-small text-muted">
							<?php echo $latestPost->getCreationDate()->format(JText::_('COM_EASYBLOG_DATE_FORMAT_STATISTICS')); ?>
						</div>
					</div>
					<?php } ?>
				<?php } else { ?>
				<div class="text-small text-muted">
					<?php echo JText::_('COM_EASYBLOG_STATS_NO_POST_CREATED_YET');?>
				</div>
				<?php } ?>
				</div>

				<hr>

				<p style="margin-bottom: 15px; text-transform: uppercase;" class="text-small text-muted"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_POST_BY_CATEGORIES');?></p>
				<div class="eb-stats-listing">

					<?php foreach ($categories as $category) { ?>
					<div>
						<span class="text-small text-muted pull-right">
							<?php echo $this->getNouns('COM_EASYBLOG_DASHBOARD_CATEGORY_POSTS', $category->getCount(), true); ?>
						</span>
						<b>
							<i class="fa fa-folder text-muted"></i>&nbsp;
							<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
						</b>
					</div>
					<?php } ?>
				</div>
			</div>

			<div class="col-lg-6">
				<p style="margin-bottom: 15px; text-transform: uppercase;" class="text-small text-muted"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOP_POSTS');?></p>

				<div class="eb-stats-listing">
				<?php if ($posts) { ?>
					<?php foreach ($posts as $post) { ?>
					<div>
						<span class="text-small text-muted pull-right">
							<?php echo $this->getNouns('COM_EASYBLOG_DASHBOARD_POST_HITS', $post->hits, true); ?>
						</span>
						<b>
							<i class="fa fa-file-text text-muted"></i>&nbsp;
							<a href="<?php echo $post->getPermalink();?>"><?php echo $post->title;?></a>
						</b>
					</div>
					<?php } ?>
				<?php } else { ?>
					<div class="text-small text-muted">
						<?php echo JText::_('COM_EASYBLOG_STATS_NO_POST_CREATED_YET');?>
					</div>
				<?php } ?>
				</div>

				<hr>
				<p style="margin-bottom: 15px; text-transform: uppercase;" class="text-small text-muted"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOTAL_HITS');?>
					<span class="text-small text-muted pull-right" style="text-transform: lowercase;">
						<?php echo $this->getNouns('COM_EASYBLOG_DASHBOARD_POST_HITS', $totalHits, true); ?>
					</span>
				</p>
			</div>
		</div>
	</div>
</div>

<?php if ($this->config->get('comment_easyblog')) { ?>
<div class="eb-box">
	<div class="eb-box-head"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_STATISTICS_PAGE_HEADING_COMMENTS');?></div>
	<div class="eb-box-body">
		<div class="row">
			<div class="col-lg-6">
				<p style="margin-bottom: 15px; text-transform: uppercase;" class="text-small text-muted"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_STATISTICS_HEADING_RECENT_COMMENTS');?></p>

				<div id="eb-box-carousel-comments" class="eb-box-carousel-comments carousel slide" data-bp-ride="carousel">
					<?php if ($recentComments) { ?>
					<ol class="carousel-indicators">
						<?php for($i = 0; $i < count($recentComments); $i++) { ?>
						<li data-target="#eb-box-carousel-comments" data-bp-side-to="<?php echo $i;?>" class="<?php echo $i == 0 ? 'active' : '';?>"></li>
						<?php } ?>
					</ol>
					<?php } else { ?>
					<div class="text-muted text-small">
						<?php echo JText::_('COM_EASYBLOG_DASHBOARD_STATS_NO_COMMENTS_POSTED_YET');?>
					</div>
					<?php } ?>

					<div class="carousel-inner">
						<?php foreach ($recentComments as $comment) { ?>
						<div class="item <?php echo !isset($active) ? ' active' : '';?>">
						    <div class="media">
								<img src="<?php echo $comment->getAuthor()->getAvatar();?>" width="40" height="40" class="img-circle pull-right" />
								<div class="media-body">
									<b class="media-title"><?php echo $comment->getAuthor()->getName();?></b>
									<p class="media-content"><?php echo $comment->getContent();?></p>
									<p class="media-meta text-small text-muted"><?php echo $comment->getCreated()->format(JText::_('COM_EASYBLOG_DATE_FORMAT_STATISTICS')); ?></p>
									<a href="<?php echo $comment->getPermalink();?>" class="btn btn-default btn-xs"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_STATISTICS_VIEW_COMMENT');?></a>
								</div>
							</div>
							<?php $active = true; ?>
						</div>
						<?php } ?>

					</div>
				</div>

				<hr>

				<p style="margin-bottom: 15px; text-transform: uppercase;" class="text-small text-muted">
					<?php echo JText::_('COM_EASYBLOG_DASHBOARD_STATISTICS_MOST_COMMENTED_POSTS');?>
				</p>
				<div class="eb-stats-listing">
				<?php if ($mostCommentedPosts) { ?>
					<?php foreach ($mostCommentedPosts as $post) { ?>
					<div>
						<span class="text-small text-muted pull-right">
							<?php echo $this->getNouns('COM_EASYBLOG_DASHBOARD_STATISTICS_TOTAL_COMMENTS', $post->totalcomments, true); ?>
						</span>
						<b>
							<i class="fa fa-file-text text-muted"></i>&nbsp;
							<a href="<?php echo $post->getPermalink();?>"><?php echo $post->title;?></a>
						</b>
					</div>
					<?php } ?>
				<?php } else { ?>
					<div class="text-small text-muted"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_STATS_NO_COMMENTS_POSTED_ON_YOUR_POST_YET');?></div>
				<?php } ?>
				</div>
			</div>

			<?php if ($topCommenters) { ?>
			<div class="col-lg-6">
				<p style="margin-bottom: 15px; text-transform: uppercase;" class="text-small text-muted"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_STATISTICS_TOP_COMMENTERS');?></p>
				<div style="border-radius: 4px; border: 1px solid #e5e5e5; padding: 15px;">
					<div class="eb-stats-listing">
						<?php if ($topCommenters) { ?>
							<?php foreach ($topCommenters as $topCommenter) { ?>
								<div class="media">
									<img src="<?php echo $topCommenter->author->getAvatar();?>" width="40" height="40" class="img-circle pull-left" />
									<div class="media-body">
										<b><?php echo $topCommenter->author->getName();?></b>
										<div class="text-small text-muted">
											<?php echo $this->getNouns('COM_EASYBLOG_DASHBOARD_STATISTICS_TOTAL_COMMENTS', $topCommenter->total, true); ?>
										</div>
									</div>
								</div>
							<?php } ?>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
<?php } ?>