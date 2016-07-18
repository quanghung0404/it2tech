<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div id="fd" class="es mod-es-videos module-social<?php echo $suffix;?>">
    <?php if ($videos) { ?>
    <ul class="es-item-list">
        <?php foreach ($videos as $video) { ?>
        <li class="mb-15">
            <div class="es-video-item">
                
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
                        <a href="<?php echo $video->getPermalink();?>" alt="<?php echo $modules->html('string.escape', $video->getTitle());?>">
                            <?php echo $modules->html('string.escape', $video->getTitle());?>
                        </a>
                    </div>
                    <div class="es-video-meta mt-5">
                        <div>
                            <a href="<?php echo $video->getAuthor()->getPermalink();?>">
                                <i class="fa fa-user"></i> <?php echo $video->getAuthor()->getName();?>
                            </a>
                        </div>

                        <div>
                            <a href="<?php echo $video->getCategory()->getPermalink();?>">
                                <i class="fa fa-folder"></i> <?php echo $video->getCategory()->title;?>
                            </a>
                        </div>
                    </div>
                    <div class="es-video-stat mt-10">
                        <div>
                            <i class="fa fa-eye"></i> <?php echo $video->getHits();?>
                        </div>

                        <div>
                            <i class="fa fa-heart"></i> <?php echo $video->getLikesCount();?>
                        </div>

                        <div>
                            <i class="fa fa-comment"></i> <?php echo $video->getCommentsCount();?>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <?php } ?>
    </ul>
    <?php } else { ?>
        <div class="is-empty">
            <div class="empty">
                <?php echo JText::_('MOD_EASYSOCIAL_VIDEOS_EMPTY'); ?>
            </div>
        </div>
    <?php } ?>

</div>
