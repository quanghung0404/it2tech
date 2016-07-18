<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php echo $video->getMiniHeader();?>

<div class="es-container es-videos" data-video-item data-id="<?php echo $video->id;?>">
    <div class="es-content">

        <?php echo $this->render('module' , 'es-videos-before-video'); ?>

        <div class="es-video-single es-responsive">
            <div class="es-video-manage row-table mb-15 mt-15">
                <div class="col-cell">
                    <a href="<?php echo $backLink;?>">&larr; <?php echo JText::_('COM_EASYSOCIAL_VIDEOS_BACK_TO_VIDEOS'); ?></a>
                </div>

                <div class="col-cell">
                    <?php if ($video->canFeature() || $video->canUnfeature() || $video->canDelete() || $video->canEdit()) { ?>
                    <span class="es-video-manage dropdown_ pull-right pl-10">
                        <a href="javascript:void(0);" class="dropdown-toggle_" data-bs-toggle="dropdown"><?php echo JText::_('COM_EASYSOCIAL_MANAGE');?></a>
                        <ul class="dropdown-menu dropdown-arrow-topright">
                            <?php if ($video->canFeature()) { ?>
                            <li>
                                <a href="javascript:void(0);" data-video-feature><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_FEATURE_VIDEO');?></a>
                            </li>
                            <?php } ?>

                            <?php if ($video->canUnfeature()) { ?>
                            <li>
                                <a href="javascript:void(0);" data-video-unfeature><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_UNFEATURE_VIDEO');?></a>
                            </li>
                            <?php } ?>

                            <?php if ($video->canEdit()) { ?>
                            <li>
                                <a href="<?php echo $video->getEditLink();?>"><?php echo JText::_('COM_EASYSOCIAL_EDIT'); ?></a>
                            </li>
                            <?php } ?>

                            <?php if ($video->canDelete()) { ?>
                            <li>
                                <a href="javascript:void(0);" data-video-delete><?php echo JText::_('COM_EASYSOCIAL_DELETE');?></a>
                            </li>
                            <?php } ?>
                        </ul>
                    </span>
                    <?php } ?>
                </div>
            </div>

            <div class="es-video-content-body">
                <?php if ($video->isPendingProcess()) { ?>
                <div class="alert alert-info">
                    <?php echo JText::_('COM_EASYSOCIAL_VIDEOS_ITEM_PENDING_INFO');?>
                </div>
                <?php } ?>

                <div class="video-container">
                    <?php echo $video->getEmbedCodes(); ?>
                </div>
            </div>

            <?php echo $this->render('module' , 'es-videos-after-video'); ?>

            <div class="es-video-content-brief">
                <div class="mt-20">
                    <h2 class="es-video-title single"><?php echo $video->getTitle();?></h2>
                    <div class="es-video-meta mt-10 mb-10">
                        <?php if ($video->table->isFeatured()) { ?>
                        <span>
                            <i class="fa fa-star mr-5"></i><b class="text-success"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_FEATURED');?></b>
                        </span>
                        <?php } ?>
                        <span>
                            <i class="fa fa-user mr-5"></i><?php echo $this->html('html.user', $video->getAuthor());?>
                        </span>
                        <span>
                            <i class="fa fa-folder mr-5"></i>
                            <a href="<?php echo $video->getCategory()->getPermalink(true, $video->uid, $video->type);?>"><?php echo $video->getCategory()->title;?></a>
                        </span>
                        <span>
                            <i class="fa fa-clock-o"></i> <?php echo $video->getCreatedDate()->format(JText::_('COM_EASYSOCIAL_VIDEOS_DATE_FORMAT'));?>
                        </span>
                    </div>

                    <?php echo $this->render('module' , 'es-videos-before-video-description'); ?>

                    <div class="es-video-brief mt-10"><?php echo $video->getDescription();?></div>

                    <?php echo $this->render('module' , 'es-videos-after-video-description'); ?>

                    <?php if ($this->config->get('video.layout.item.duration')) { ?>
                    <div class="es-video-duration mt-10">
                        <?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_DURATION');?>
                        <?php echo $video->getDuration();?>
                    </div>
                    <?php } ?>

                    <?php if ($video->hasLocation()) { ?>
                    <div class="es-video-location mt-20">
                        <?php echo $this->html('html.map', $video->getLocation(), true);?>
                    </div>
                    <?php } ?>

                    <?php if ($this->config->get('video.layout.item.tags')) { ?>
                        <hr class="es-hr">

                        <div class="es-video-tagging">
                            <b><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_PEOPLE_IN_THIS_VIDEO');?></b>
                            <?php if ($video->canAddTag()) { ?>
                            <span class="ml-5 text-muted">
                                &ndash;
                                <a href="javascript:void(0);" data-video-tag><?php echo JText::_('COM_EASYSOCIAL_TAG_PEOPLE');?></a>
                            </span>
                            <?php } ?>
                            <ul class="es-video-tag-friends fd-reset-list<?php echo !$tags ? ' is-empty' : '';?>" data-video-tag-wrapper>
                                <?php echo $this->output('site/videos/tags'); ?>
                                <li class="empty" data-tags-empty>
                                    <?php echo JText::_('COM_EASYSOCIAL_VIDEOS_NO_TAGS_AVAILABLE'); ?>
                                </li>
                            </ul>
                        </div>
                    <?php } ?>

                    <?php echo $this->render('module' , 'es-videos-after-video-tags'); ?>
                </div>
            </div>

            <hr class="es-hr" />

            <div class="es-video-response">
                <div class="es-action-wrap pr-20">
                    <ul class="fd-reset-list es-action-feedback">
                        <li>
                            <?php echo $likes->button();?>
                        </li>

                        <?php if ($this->config->get('video.layout.item.hits')) { ?>
                        <li>
                            <span>
                                <?php echo JText::sprintf('COM_EASYSOCIAL_VIDEOS_HITS', $video->getHits()); ?>
                            </span>
                        </li>
                        <?php } ?>

                        <li class="video-reports">
                            <?php echo $reports->html();?>
                        </li>

                        <li class="video-sharing">
                            <?php echo $sharing->html(false); ?>
                        </li>

                        <li class="es-action-privacy">
                            <?php echo $privacyButton; ?>
                        </li>
                    </ul>
                </div>

                <div class="es-stream-actions video-likes">
                    <?php echo $likes->html();?>
                </div>

                <div class="es-stream-actions">
                    <?php echo $comments->getHTML();?>
                </div>
            </div>

            <?php echo $this->render('module' , 'es-videos-before-other-videos'); ?>

            <?php if ($this->config->get('video.layout.item.recent') && $otherVideos) { ?>
            <div class="es-video-other">
                <div class="es-snackbar"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_OTHER_VIDEOS');?></div>

                <div class="es-related-videos es-video-list">
                    <?php foreach ($otherVideos as $otherVideo) { ?>
                    <div class="es-video-item">
                        <div class="es-video-thumbnail">
                            <a href="<?php echo $otherVideo->getPermalink();?>">
                                <div class="es-video-cover" style="background-image: url('<?php echo $otherVideo->getThumbnail();?>')"></div>

                                <div class="es-video-time">
                                    <?php echo $otherVideo->getDuration();?>
                                </div>
                            </a>
                        </div>

                        <div class="es-video-content">
                            <div class="es-video-title">
                                <a href="<?php echo $video->getPermalink();?>"><?php echo $otherVideo->getTitle();?></a>
                            </div>

                            <div class="es-video-meta mt-5">
                                <div>
                                    <a href="<?php echo $otherVideo->getAuthor()->getPermalink();?>">
                                        <i class="fa fa-user mr-5"></i> <?php echo $otherVideo->getAuthor()->getName();?>
                                    </a>
                                </div>

                                <div>
                                    <a href="<?php echo $otherVideo->getCategory()->getPermalink();?>">
                                        <i class="fa fa-folder mr-5"></i> <?php echo $otherVideo->getCategory()->title;?>
                                    </a>
                                </div>
                            </div>

                            <div class="es-video-stat mt-10">
                                <?php if ($this->config->get('video.layout.item.hits')) { ?>
                                <div>
                                    <i class="fa fa-eye"></i> <?php echo $otherVideo->getHits();?>
                                </div>
                                <?php } ?>

                                <div>
                                    <i class="fa fa-heart"></i> <?php echo $otherVideo->getLikesCount();?>
                                </div>

                                <div>
                                    <i class="fa fa-comment"></i> <?php echo $otherVideo->getCommentsCount();?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>

            <?php echo $this->render('module' , 'es-videos-after-other-videos'); ?>
        </div>
    </div>
</div>
