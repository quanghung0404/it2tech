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
<div class="eb-authors-team">
	<?php if ($teams) { ?>
		<?php foreach ($teams as $team) { ?>
			<div class="eb-author eb-responsive" data-team-item data-id="<?php echo $team->id;?>">
				<div class="eb-authors-head row-table">
					<?php if ($this->config->get('layout_teamavatar', true)){ ?>
					<div class="col-cell cell-tight">
						<a href="<?php echo $team->getPermalink();?>">
							<img src="<?php echo $team->getAvatar(); ?>" class="eb-avatar eb-authors-avatar" width="60" height="60" alt="<?php echo $team->title;?>" />
						</a>
					</div>
					<?php } ?>

					<div class="col-cell">
						<div class="row-table">
							<div class="col-cell">
								<h2 class="eb-authors-name reset-heading">
									<a href="<?php echo $team->getPermalink();?>" class="text-inherit"><?php echo $team->title;?></a>
									<small class="eb-authors-featured eb-star-featured<?php echo !$team->isFeatured ? ' hide' : '';?>" data-featured-tag data-eb-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYBLOG_FEATURED_BLOGGER_FEATURED', true);?>">
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
					<?php if (!empty($team->description) && $this->config->get('main_includeteamblogdescription')) { ?>
						<?php echo nl2br($team->description); ?>
					<?php } ?>

					<div class="eb-authors-subscribe spans-seperator">
						<?php if (($team->access != EBLOG_TEAMBLOG_ACCESS_MEMBER || $team->isMember || EB::isSiteAdmin()) && $this->config->get('main_teamsubscription')) { ?>
							<?php if (!$team->isTeamSubscribed) { ?>
							<span>
								<a href="javascript:void(0);" data-blog-subscribe data-type="team" data-id="<?php echo $team->id; ?>"><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_TEAM');?></a>
							</span>
							<?php }  else { ?>
							<span>
								<a href="javascript:void(0);" data-blog-unsubscribe data-subscription-id="<?php echo $team->subscription_id; ?>"><?php echo JText::_('COM_EASYBLOG_UNSUBSCRIBE_TEAM');?></a>
							</span>
							<?php } ?>
						<?php } ?>

						<?php if (($team->access != EBLOG_TEAMBLOG_ACCESS_MEMBER || $team->isMember || EB::isSiteAdmin() ) && ($this->config->get('main_rss'))) { ?>
						<span>
							<a class="link-rss" href="<?php echo $team->getRssLink();?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?>">
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

				<div class="eb-authors-stats">
					<ul class="eb-stats-nav reset-list">
						<li class="active">
							<a class="btn btn-default btn-block" href="#team-posts-<?php echo $team->id;?>" data-bp-toggle="tab">
								<?php echo JText::_('COM_EASYBLOG_TEAMBLOG_TOTAL_POSTS');?>
								<b><?php echo $team->postCount; ?></b>
							</a>
						</li>
						<li>
							<a class="btn btn-default btn-block" href="#team-authors-<?php echo $team->id;?>" data-bp-toggle="tab">
								<?php echo JText::_('COM_EASYBLOG_TEAMBLOG_TOTAL_AUTHORS');?>
								<b><?php echo $team->memberCount;?></b>
							</a>
						</li>
					</ul>
					<div class="eb-stats-content">
						<div class="tab-pane eb-stats-posts active" id="team-posts-<?php echo $team->id;?>">
							<?php if ($team->blogs) { ?>
								<?php foreach ($team->blogs as $post) { ?>
								<div>
									<time><?php echo $post->getCreationDate()->format(JText::_('DATE_FORMAT_LC3'));?></time>

									<?php echo $post->getIcon('eb-post-type'); ?>

									<a href="<?php echo $post->getPermalink();?>"><?php echo $post->title;?></a>
								</div>
								<?php } ?>

                                <a href="<?php echo $team->getPermalink();?>" class="btn btn-default btn-block btn-show-all">
                                    <?php echo JText::_('COM_EASYBLOG_VIEW_ALL_POSTS');?> &nbsp;<i class="fa fa-chevron-right"></i>
                                </a>

							<?php } else { ?>
								<div class="eb-empty">
									<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_NO_POSTS_YET');?>
								</div>
							<?php } ?>
						</div>
						<div class="tab-pane eb-labels eb-stats-authors" id="team-authors-<?php echo $team->id;?>">

							<?php if ($team->members) { ?>
								<?php foreach ($team->members as $member) { ?>
									<div class="eb-stats-author row-table">
										<a class="col-cell" href="<?php echo $member->getPermalink();?>" class="eb-avatar">
											<img src="<?php echo $member->getAvatar(); ?>" width="50" height="50" alt="<?php echo $member->getName();?>" />
										</a>
										<div class="col-cell">
											<b>
												<a href="<?php echo $member->getPermalink();?>"><?php echo $member->getName();?></a>
											</b>
											<div>
												<?php $pCnt = isset($member->postCount) ? $member->postCount : $member->getTotalPosts() ; ?>
												<?php echo $this->getNouns('COM_EASYBLOG_AUTHOR_POST_COUNT', $pCnt, true); ?>
											</div>
										</div>
									</div>
								<?php } ?>

								<?php if ($team->memberCount > count($team->members)) { ?>
									<a href="javascript:void(0);" data-view-member class="btn btn-default btn-block btn-show-all"><?php echo JText::_('COM_EASYBLOG_TEAMBLOG_VIEW_ALL_MEMBERS');?></a>
								<?php } ?>


							<?php } else { ?>
								<div class="eb-empty">
									<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_NO_AUTHORS_YET');?>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } else { ?>
		<div class="eb-empty"><?php echo JText::_('COM_EASYBLOG_NO_TEAMBLOGS_FOUND'); ?></div>
	<?php } ?>

	<?php if ($pagination) { ?>
	<div class="eb-pagination clearfix">
		<?php echo $pagination; ?>
	</div>
	<?php } ?>
</div>
