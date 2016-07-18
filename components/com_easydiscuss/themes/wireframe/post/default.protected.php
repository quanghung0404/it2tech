<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-entry
	<?php echo $post->isLocked() ? ' is-locked' : '';?>
	<?php echo $post->isFeatured() ? ' is-featured' : '';?>
	<?php echo $post->isResolved() ? ' is-resolved' : '';?>
    <?php echo $post->isPrivate() ? ' is-private' : '';?>
	"
	data-ed-post-wrapper
>
    <div data-ed-post-notifications></div>

	<?php echo $adsense->header; ?>

    <div class="ed-post-item has-body">

        <div class="ed-post-item__hd">

            <div class="o-grid">
                <div class="o-grid__cell">
                    <h2 class="ed-post-item__title t-lg-mb--md">

                        <?php if ($this->config->get('post_priority') && $post->getPriority()) { ?>
                        <i class="fa fa-file"
                            style="<?php echo $post->getPriority() ? 'color:' . $post->getPriority()->color : '';?>"
                            data-ed-provide="tooltip"
                            data-original-title="<?php echo JText::_($post->getPriority()->title);?>"
                        ></i>
                        <?php } ?>

                        <a href="<?php echo $post->getPermalink();?>"><?php echo $post->getTitle();?></a>
                    </h2>

                    <div class="ed-post-item__status t-lg-mr--md">
                        <i class="fa fa-star ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_FEATURED_DESC');?>"></i>

                        <i class="fa fa-lock ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_LOCKED_DESC');?>"></i>

                        <i class="fa fa-key ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PROTECTED_DESC');?>"></i>

                        <i class="fa fa-eye ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PRIVATE_DESC');?>"></i>
                    </div>

                    <div class="t-mt--sm">
                        <ol class="g-list-inline ed-post-item__post-meta">
                            <?php if ($post->isResolved()) { ?>
                            <li>
                                <span class="o-label o-label--success-o ed-state-resolved"><?php echo JText::_('COM_EASYDISCUSS_RESOLVED');?></span>
                            </li>
                            <?php } ?>
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
                            <?php if ($post->getPostType()) { ?>
                                <li><span class="o-label o-label--clean-o <?php echo $post->getTypeSuffix(); ?>"><?php echo $post->getPostType(); ?></span></li>
                            <?php } ?>
                        </ol>
                    </div>
                </div>

            </div>
        </div>

        <div class="ed-post-item__sub-hd">
            <ol class="g-list-inline g-list-inline--dashed">
                <li>
                    <?php if ($post->isAnonymous()) { ?>

                        <?php if ($this->config->get('layout_avatar_in_post')) { ?>
                            <span class="o-avatar o-avatar--xs">
                               <img src="<?php echo ED::getDefaultAvatar();?>" width="24" height="24" />
                            </span>
                        <?php } ?>

                        <?php if ($this->isSiteAdmin) { ?>
                            <a class="ed-user-name" href="<?php echo $post->getOwner()->getPermalink();?>">
                                <?php echo JText::_('COM_EASYDISCUSS_ANONYMOUS_USER');?> (<?php echo $post->getOwner()->getName($post->poster_name);?>)
                            </a>
                        <?php } else { ?>
                            <span class="ed-user-name"><?php echo JText::_('COM_EASYDISCUSS_ANONYMOUS_USER');?></span>
                        <?php } ?>
                    <?php } ?>

                    <?php if (!$post->isAnonymous()) { ?>
                        <?php if ($this->config->get('layout_avatar_in_post')) { ?>
                            <span class="o-avatar o-avatar--xs">
                               <?php echo $this->html('user.avatar', $post->getOwner(), array('rank' => false, 'status' => false, 'size' => 'xs')); ?>
                            </span>
                        <?php } ?>

                        <a class="ed-user-name" href="<?php echo $post->getOwner()->getPermalink();?>"><?php echo $post->getOwner()->getName($post->poster_name);?></a>
                    <?php } ?>
                </li>

                <?php if ($post->isAnonymous() && $this->isSiteAdmin) { ?>
                <li>
                    <span><?php echo JText::_('COM_EASYDISCUSS_POSTED_ANONYMOUSLY');?>
                </li>
                <?php } ?>

                <li>
                    <a href="<?php echo $post->getCategory()->getPermalink();?>"><?php echo $post->getCategory()->getTitle();?></a>
                </li>
                <li>
                    <span><?php echo $post->date;?></span>
                </li>

            </ol>
        </div>

        <div class="ed-post-item__bd <?php echo $poll && $poll->isLocked() ? ' is-lockpoll' : '';?>" data-ed-post-item>
            <div class="ed-post-content t-lg-pt--lg t-lg-pb--lg">
               <?php echo $post->getProtectedContent('content'); ?>
            </div>
        </div>
    </div>

    <div class="ed-post-who-view t-lg-mt--lg t-lg-mb--lg">
        <?php echo ED::getWhosOnline();?>
    </div>
	<?php echo $adsense->footer; ?>
</div>
