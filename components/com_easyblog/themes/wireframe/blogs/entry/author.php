<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-entry-author" itemprop="author" itemscope itemtype="http://schema.org/Person">
    <h4 class="eb-section-heading reset-heading"><?php echo JText::_('COM_EASYBLOG_ABOUT_THE_AUTHOR');?></h4>
    
    <div class="eb-entry-author-bio media">
        <?php if ($this->entryParams->get('post_author_box_avatar', true)) { ?>
        <div class="col-cell">
            <a href="<?php echo $post->author->getProfileLink();?>" class="eb-entry-author-avatar eb-avatar col-cell">
                <img itemprop="image" src="<?php echo $post->author->getAvatar();?>" width="50" height="50" alt="<?php echo $post->author->getName();?>" />
            </a>
        </div>
        <?php } ?>

        <div class="col-cell">
            <?php if ($this->entryParams->get('post_author_box_title', true)) { ?>
            <h3 itemprop="name" class="eb-author-name reset-heading">
                <a href="<?php echo $post->author->getProfileLink();?>"><?php echo $post->author->getName();?></a>
            </h3>
            <?php } ?>

            <?php if (EB::points()->hasIntegrations()) { ?>
            <div class="eb-points">
                <?php echo EB::points()->html($post->author); ?>
            </div>
            <?php } ?>

            <?php if ($post->author->getWebsite() && $this->entryParams->get('post_author_box_website', true)) { ?>
            <div>
                <i class="fa fa-globe"></i>&nbsp; <a href="<?php echo $this->escape($post->author->getWebsite()); ?>" target="_blank" class="author-url" rel="nofollow"><?php echo $this->escape($post->author->getWebsite()); ?></a>
            </div>
            <?php } ?>

            <div class="eb-entry-author-meta spans-seperator muted">
                <?php if ($this->entryParams->get('post_author_box_view_profile', true)) { ?>
                <span>
                    <a href="<?php echo $post->author->getProfileLink();?>"><?php echo JText::_('COM_EASYBLOG_ABOUT_AUTHOR_VIEW_PROFILE');?></a>
                </span>
                <?php } ?>

                <?php if ($this->entryParams->get('post_author_box_more_posts', true)) { ?>
                <span>
                    <a href="<?php echo $post->author->getPermalink();?>"><?php echo JText::_('COM_EASYBLOG_ABOUT_AUTHOR_VIEW_MORE_POSTS');?></a>
                </span>
                <?php } ?>

                <?php if (!$this->my->guest && EB::followers()->hasIntegrations($post->author)) { ?>
                <span>
                    <?php echo EB::followers()->html($post->author);?>
                </span>
                <?php } ?>

                <?php if (!$this->my->guest && EB::friends()->hasIntegrations($post->author)) { ?>
                <span>
                    <?php echo EB::friends()->html($post->author);?>
                </span>
                <?php } ?>

                <?php if (!$this->my->guest && EB::messaging()->hasMessaging($post->author->id)) { ?>
                <span>
                    <?php echo EB::messaging()->html($post->author);?>
                </span>
                <?php } ?>
            </div>
        </div>

        <?php if (EB::achievements()->hasIntegrations()) { ?>
        <div class="eb-achievements mt-10">
            <?php echo EB::achievements()->html($post->author); ?>
        </div>
        <?php } ?>

        <?php if ($post->author->getBioGraphy() && $this->entryParams->get('post_author_box_biography', true)) { ?>
        <div class="eb-entry-author-details" itemprop="description">
            <?php echo $post->author->getBioGraphy(); ?>
        </div>
        <?php } ?>
    </div>

    <?php if ($this->entryParams->get('post_author_recent', true) && $recent) { ?>
    <div class="eb-entry-author-recents">
        <h5 class="reset-heading"><?php echo JText::_('COM_EASYBLOG_AUTHOR_RECENT_POSTS');?></h5>
        
        <?php foreach ($recent as $recentPost) { ?>
        <div>
            <a href="<?php echo $recentPost->getPermalink();?>">
                <i class="fa fa-file-o pull-left mr-10"></i>
                <time class="pull-right"><?php echo $recentPost->getDisplayDate($this->entryParams->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')); ?></time>
                <span><?php echo $recentPost->title;?></span>
            </a>
        </div>
        <?php } ?>
    </div>
    <?php } ?>
</div>
