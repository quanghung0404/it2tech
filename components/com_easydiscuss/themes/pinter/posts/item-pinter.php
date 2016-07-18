<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-post-item ed-post-item--flex__cell
    <?php echo $post->isSeen($this->my->id) ? ' is-read' : '';?>
    <?php echo $post->isFeatured() ? ' is-featured' : '';?>
    <?php echo $post->isLocked() ? ' is-locked' : '';?>
    <?php echo $post->isProtected() ? ' is-protected' : '';?>
    <?php echo $post->isPrivate() ? ' is-private' : '';?>
    <?php echo $this->config->get('layout_enableintrotext') || $post->getTags() ? ' has-body' : '';?>
    "
>
    <div class="ed-post-item__content">
    <div class="ed-post-item__hd">

        <!-- <div class="o-row"> -->
            <!-- <div class="o-col"> -->

                <h2 class="ed-post-item__title t-lg-mb--md">
                    <a href="<?php echo $post->getPermalink();?>"><?php echo $post->getTitle();?></a>

                    <?php if ($post->isFeatured() || $post->isLocked() || $post->isProtected() || $post->isPrivate()) { ?>
                    <div class="ed-post-item__status t-ml--sm">
                        <i class="fa fa-star ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_FEATURED_DESC');?>"></i>

                        <i class="fa fa-lock ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_LOCKED_DESC');?>"></i>

                        <i class="fa fa-key ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PROTECTED_DESC');?>"></i>

                        <i class="fa fa-eye ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PRIVATE_DESC');?>"></i>

                    </div>
                    <?php } ?>
                </h2>


                <ol class="g-list-inline ed-post-item__post-meta">

                    <?php if ($post->isResolved()) { ?>
                    <li><span class="o-label o-label--success-o"><?php echo JText::_('COM_EASYDISCUSS_RESOLVED');?></span></li>
                    <?php } ?>

                    <?php //if ($post->isStillNew()) { ?>
                        <!-- li><span class="o-label o-label--warning-o"><?php echo JText::_('COM_EASYDISCUSS_NEW');?></span></li -->
                    <?php // } ?>


                    <!-- post status here: accepted, onhold, working rejected -->
                    <?php if ($post->isPostRejected()) { ?>
                        <li><span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_POST_STATUS_REJECT');?></span></li>
                    <?php } ?>
                    <?php if ($post->isPostOnhold()) { ?>
                        <li><span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_POST_STATUS_ON_HOLD');?></span></li>
                    <?php } ?>
                    <?php if ($post->isPostAccepted()) { ?>
                        <li><span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_POST_STATUS_ACCEPTED');?></span></li>
                    <?php } ?>
                    <?php if ($post->isPostWorkingOn()) { ?>
                        <li><span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_POST_STATUS_WORKING_ON');?></span></li>
                    <?php } ?>

                    <!-- post type here -->
                    <?php if ($post->getTypeTitle()) { ?>
                        <li><span class="o-label o-label--clean-o <?php echo $post->getTypeSuffix(); ?>"><?php echo $post->getTypeTitle(); ?></span></li>
                    <?php } ?>
                </ol>

                <ol class="g-list-inline g-list-inline--delimited ed-post-meta-reply t-lg-mt--md">
                    <li><?php echo JText::sprintf('COM_EASYDISCUSS_LAST_ACTIVITY_TIMELAPSE', ED::date()->toLapsed($post->modified)); ?></li>
                    <?php if ($post->getLastReplier()) { ?>
                        <li data-breadcrumb="·">
                            <?php if (!$post->isLastReplyAnonymous()) { ?>
                                <a href="<?php echo EDR::_('view=post&id=' . $post->id . '&sort=latest'); ?>">
                                    <i class="fa fa-reply"></i> <?php echo JText::sprintf('COM_EASYDISCUSS_LAST_REPLIED_BY', $post->getLastReplier()->getName(), ED::date()->toLapsed($post->lastupdate)); ?>
                                </a>
                            <?php } else { ?>
                                <a href="<?php echo EDR::_('view=post&id=' . $post->id . '&sort=latest'); ?>">
                                    <i class="fa fa-reply"></i> <?php echo JText::sprintf('COM_EASYDISCUSS_LAST_REPLIED_BY', JText::_('COM_EASYDISCUSS_ANONYMOUS_USER'), ED::date()->toLapsed($post->lastupdate)); ?>
                                </a>
                            <?php } ?>
                        </li>
                    <?php } ?>
                </ol>

            <!-- </div> -->

            <!-- <div class="o-col"> -->
                <div class="ed-statistic t-lg-mt--md">
                    <div class="ed-statistic__item">
                        <a href="<?php echo $post->getPermalink();?>">
                            <span class="ed-statistic__item-count"><?php echo $post->getTotalReplies();?></span>
                            <span><?php echo JText::_('COM_EASYDISCUSS_REPLIES');?></span>
                        </a>
                    </div>
                    <div class="ed-statistic__item">
                        <a href="<?php echo $post->getPermalink();?>">
                            <span class="ed-statistic__item-count"><?php echo $post->getHits();?></span>
                            <span><?php echo JText::_('COM_EASYDISCUSS_VIEWS');?></span>
                        </a>
                    </div>
                    <?php if ($this->config->get('main_allowquestionvote')) { ?>
                    <div class="ed-statistic__item">
                        <a href="<?php echo $post->getPermalink();?>">
                            <span class="ed-statistic__item-count"><?php echo $post->getTotalVotes();?></span>
                            <span><?php echo JText::_('COM_EASYDISCUSS_VOTES');?></span>
                        </a>
                    </div>
                    <?php } ?>

                    <?php if ($this->config->get('main_likes_discussions')) { ?>
                    <div class="ed-statistic__item">
                        <a href="<?php echo $post->getPermalink();?>">
                            <span class="ed-statistic__item-count"><?php echo $post->getTotalLikes();?></span>
                            <span><?php echo JText::_('COM_EASYDISCUSS_LIKES');?></span>
                        </a>
                    </div>
                    <?php } ?>
                </div>
            <!-- </div> -->

        <!-- </div> -->
        <?php if ($this->config->get('layout_enableintrotext') || $post->getTags()) { ?>
        <!-- <div class="ed-post-item__bd"> -->
            <?php if ($this->config->get('layout_enableintrotext')) { ?>
            <div class="ed-post-content">
                <?php echo $post->getIntro();?>
            </div>
            <?php } ?>

            <?php if ($this->config->get('main_master_tags')) { ?>
                <?php if ($post->getTags()) { ?>
                <ol class="g-list-inline ed-post-meta-tag t-lg-mt--md">
                    <?php foreach ($post->getTags() as $tag) { ?>
                    <li>
                        <a href="<?php echo EDR::_('view=tags&id=' . $tag->id);?>">
                            <i class="fa fa-tag"></i>&nbsp; <?php echo $tag->title;?>
                        </a>
                    </li>
                    <?php } ?>
                </ol>
                <?php } ?>
            <?php } ?>

        <!-- </div> -->
        <?php } ?>
    </div>


    <div class="ed-post-item__ft">

        <div class="o-row">
            <?php if (!$post->isAnonymous()) { ?>
                <div class="o-col-sm">
                    <div class="o-flag">
                        <div class="o-flag__image o-flag--top">
                            <?php echo $this->html('user.avatar', $post->getOwner(), array('rank' => true, 'status' => true)); ?>
                        </div>
                        <div class="o-flag__body">
                            <div class="">
                                <a href="<?php echo $post->getOwner()->getLink();?>" class="ed-user-name"><?php echo $post->getOwner()->getName();?></a>
                                <?php if($this->config->get('layout_profile_roles') && $post->getOwner()->getRole() ) { ?>
                                    <span class="ed-user-role-label o-label o-label--<?php echo $post->getOwner()->getRoleLabelClassname()?>"><?php echo $post->getOwner()->getRole(); ?></span>
                                <?php } ?>
                            </div>

                            <?php if( $this->config->get('main_ranking')){ ?>
                            <div class="ed-user-rank"><?php echo $this->escape(ED::getUserRanks($post->getOwner()->id)); ?></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if ($post->isAnonymous()) { ?>
                <div class="o-col-sm">
                    <div class="o-flag">
                        <div class="o-flag__image o-flag--top">
                            <?php echo $this->output('site/html/user.anonymous') ?>
                        </div>
                        <div class="o-flag__body">
                            <div class="">
                                <a href="javascript:void(0);" class="ed-user-name"><?php echo JText::_('COM_EASYDISCUSS_ANONYMOUS_USER');?></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="o-col-sm">
                <ol class="g-list-inline g-list-inline--dashed pull-right ed-post-meta-cat">
                    <li><a href="<?php echo $post->getCategory()->getPermalink();?>" class=""><?php echo $post->getCategory()->title;?></a></li>
                    <?php if ($post->hasAttachments()) { ?>
                    <li><i class="fa fa-file"></i></li>
                    <?php } ?>
                    <?php if ($post->hasPolls()) { ?>
                    <li><i class="fa fa-bar-chart"></i></li>
                    <?php } ?>
                </ol>
            </div>
        </div>

    </div>

    </div>
</div>
