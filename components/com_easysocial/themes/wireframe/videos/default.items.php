<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<?php if (isset($filter) && $filter == 'pending') { ?>
    <div class="es-snackbar"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_PENDING_TITLE');?></div>
    <p class="pending-info"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_PENDING_INFO');?></p>
    <hr />
<?php } ?>

<?php if ((isset($isFeatured) && $isFeatured) || $filter == 'featured') { ?>
    <div class="es-snackbar">
        <?php echo JText::_("COM_EASYSOCIAL_VIDEOS_FEATURED_VIDEOS");?>
    </div>
<?php } else { ?>
    <div class="es-snackbar">
        <?php echo JText::_("COM_EASYSOCIAL_VIDEOS_FILTERS_RECENT_VIDEOS");?>
    </div>
<?php } ?>
    

<div class="es-video-list clearfix<?php echo !$videos ? ' is-empty' : '';?>">
    <?php if ($videos) { ?>
        <?php foreach ($videos as $video) { ?>
        <div class="es-video-item" data-video-item
            data-id="<?php echo $video->id;?>"
        >
            <?php if ($video->canFeature() || $video->canUnfeature() || $video->canDelete() || $video->canEdit()) { ?>
            <div class="es-video-item-action">
                <div class="pull-right dropdown_">
                    <a href="javascript:void(0);" class="btn btn-es btn-sm dropdown-toggle_" data-bs-toggle="dropdown"><i class="fa fa-cog"></i></a>
                    <ul class="dropdown-menu">
                        <?php if ($video->canFeature()) { ?>
                        <li>
                            <a href="javascript:void(0);" data-video-feature data-return="<?php echo $returnUrl;?>"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_FEATURE_VIDEO');?></a>
                        </li>
                        <?php } ?>

                        <?php if ($video->canUnfeature()) { ?>
                        <li>
                            <a href="javascript:void(0);" data-video-unfeature data-return="<?php echo $returnUrl;?>"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_UNFEATURE_VIDEO');?></a>
                        </li>
                        <?php } ?>

                        <?php if ($video->canEdit()) { ?>
                        <li>
                            <a href="<?php echo $video->getEditLink();?>"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_EDIT_VIDEO'); ?></a>
                        </li>
                        <?php } ?>

                        <?php if ($video->canDelete()) { ?>
                        <li class="divider"></li>

                        <li>
                            <a href="javascript:void(0);" data-video-delete><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_DELETE_VIDEO');?></a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <?php } ?>

            <?php if ($video->table->isFeatured()) { ?>
            <div class="es-video-featured-label">
                <span><?php echo JText::_('COM_EASYSOCIAL_FEATURED');?></span>
            </div>
            <?php } ?>

            <div class="es-video-thumbnail">
                <a href="<?php echo $video->getPermalink();?>">
                    <div class="es-video-cover" style="background-image: url('<?php echo $video->getThumbnail();?>')"></div>
                    <div class="es-video-time"><?php echo $video->getDuration();?></div>
                </a>
            </div>
            <div class="es-video-content">
                <div class="es-video-title">
                    <a href="<?php echo $video->getPermalink();?>"><?php echo $video->getTitle();?></a>
                </div>

                <div class="es-video-meta mt-5">
                    <div>
                        <a href="<?php echo $video->getAuthor()->getPermalink();?>">
                            <i class="fa fa-user mr-5"></i> <?php echo $video->getAuthor()->getName();?>
                        </a>
                    </div>

                    <div>
                        <a href="<?php echo $video->getCategory()->getPermalink();?>">
                            <i class="fa fa-folder mr-5"></i> <?php echo $video->getCategory()->title;?>
                        </a>
                    </div>
                </div>

                <div class="es-video-stat mt-10">
                    <?php if ($this->config->get('video.layout.item.hits')) { ?>
                    <div>
                        <i class="fa fa-eye"></i> <?php echo $video->getHits();?>
                    </div>
                    <?php } ?>

                    <div>
                        <i class="fa fa-heart"></i> <?php echo $video->getLikesCount();?>
                    </div>

                    <div>
                        <i class="fa fa-comment"></i> <?php echo $video->getCommentsCount();?>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

    <?php } else { ?>
    <div class="empty empty-hero">
        <i class="fa fa-film"></i>
        <div><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_NO_VIDEOS_AVAILABLE_CURRENTLY');?></div>
    </div>
    <?php } ?>
</div>

<?php if ($videos && isset($pagination)) { ?>
<div class="mt-20 text-center es-pagination">
    <?php echo $pagination->getListFooter('site');?>
</div>
<?php } ?>
