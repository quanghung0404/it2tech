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
<?php echo EB::renderModule('easyblog-before-team-header'); ?>

<div class="eb-author eb-author-teamblog" data-team-item data-id="<?php echo $team->id;?>">
	<div class="eb-authors-head row-table">
		<?php if ($this->config->get('layout_avatar')) { ?>
		<div class="col-cell cell-tight">
			<a class="pull-left" href="<?php echo $team->getPermalink();?>">
				<img src="<?php echo $team->getAvatar(); ?>" class="eb-avatar eb-authors-avatar" width="100" height="100" alt="<?php echo $team->getTitle();?>" />
			</a>
		</div>
		<?php } ?>

		<div class="col-cell">
			<div class="row-table">
				<div class="col-cell">
					<h2 class="eb-authors-name reset-heading">
						<a href="<?php echo $team->getPermalink();?>" class="text-inherit"><?php echo $team->getTitle();?></a>
						<small class="eb-authors-featured eb-star-featured<?php echo !$team->isFeatured() ? ' hide' : '';?>" data-featured-tag data-eb-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYBLOG_FEATURED_BLOGGER_FEATURED', true);?>">
							<i class="fa fa-star"></i>
						</small>
					</h2>
				</div>

				<?php if (EB::isSiteAdmin()) { ?>
				<div class="col-cell text-right">
					<a href="javascript:void(0);" class="btn btn-default<?php echo !$team->isFeatured ? ' hide' : '';?>" data-team-unfeature data-id="<?php echo $team->id;?>">
						<i class="fa fa-star-o"></i>&nbsp; <?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_REMOVE_TEAM'); ?>
					</a>
					<a href="javascript:void(0);" class="btn btn-default<?php echo $team->isFeatured ? ' hide' : '';?>" data-team-feature data-id="<?php echo $team->id;?>">
						<i class="fa fa-star"></i>&nbsp; <?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_THIS_TEAM'); ?>
					</a>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="eb-authors-bio">
		<?php echo $team->getDescription();?>

		<div class="eb-authors-subscribe spans-seperator">

			<?php if (($team->access != EBLOG_TEAMBLOG_ACCESS_MEMBER || $team->isMember || EB::isSiteAdmin()) && $this->config->get('main_teamsubscription')) { ?>
				<?php if (!$isTeamSubscribed) { ?>
				<span>
					<a class="link-subscribe" href="javascript:void(0);" data-blog-subscribe data-type="team" data-id="<?php echo $team->id; ?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_TEAM'); ?>">
						<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_TEAM'); ?>
					</a>
				</span>
				<?php } else { ?>
				<span>
					<a href="javascript:void(0);" data-blog-unsubscribe data-type="team" data-id="<?php echo $team->id; ?>" data-email="<?php echo $this->my->email; ?>"><?php echo JText::_('COM_EASYBLOG_UNSUBSCRIBE_TEAM');?></a>
				</span>
				<?php } ?>
			<?php } ?>

			<?php if (($team->access != EBLOG_TEAMBLOG_ACCESS_MEMBER || $team->isMember || EB::isSiteAdmin()) && $this->config->get('main_rss')) { ?>
			<span>
				<a class="link-rss" href="<?php echo  EB::feeds()->getFeedURL( 'index.php?option=com_easyblog&view=teamblog&id=' . $team->id );?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?>">
					<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?>
				</a>
			</span>
			<?php } ?>


			<?php if ($this->config->get('teamblog_allow_join') && $team->allow_join && !$team->isActualMember && !$this->my->guest) { ?>
			<span>
				<a class="link-jointeam" href="javascript:void(0);" data-team-join data-id="<?php echo $team->id;?>">
					<span><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOG_JOIN_TEAM' );?></span>
				</a>
			</span>
			<?php } else if ($team->isActualMember && !$this->my->guest) { ?>
			<span>
				<a class="link-jointeam" href="javascript:void(0);" data-team-leave data-id="<?php echo $team->id;?>">
					<span><?php echo JText::_('COM_EASYBLOG_TEAMBLOG_LEAVE_TEAM');?></span>
				</a>
			</span>
			<?php } ?>

		</div>
	</div>
</div>

<?php echo EB::renderModule('easyblog-after-team-header'); ?>

<div class="eb-posts eb-masonry eb-responsive" data-team-posts>
	<?php if ($posts) { ?>
		<?php foreach ($posts as $post) { ?>
			<?php if (!EB::isSiteAdmin() && $this->config->get('main_password_protect') && !empty($post->blogpassword) && !$post->verifyPassword()) { ?>
				<!-- Password protected theme files -->
				<?php echo $this->fetch('blog.item.protected.php', array('post' => $post)); ?>
			<?php } else { ?>
				<?php echo $this->output('site/blogs/latest/default.main', array('post' => $post)); ?>
			<?php } ?>
		<?php } ?>
	<?php } else { ?>
	<div class="eb-empty">
		<i class="fa fa-info-circle"></i>
		<?php echo JText::_('COM_EASYBLOG_NO_BLOG_ENTRY');?>
	</div>
	<?php } ?>
</div>
