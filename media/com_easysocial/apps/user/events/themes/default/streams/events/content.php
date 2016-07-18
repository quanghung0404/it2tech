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
<div class="stream-apps-content mt-10 mb-10">
    <?php if ($event->hasCover()) { ?>
    <div class="media">
        <div class="es-photo es-cover">
            <a class="es-cover-container"
               href="<?php echo $event->getPermalink();?>">
                <u class="es-cover-viewport">
                    <b><img src="<?php echo $event->getCover(); ?>" /></b>
                    <em style="background-image: url('<?php echo $event->getCover(); ?>'); background-position: <?php echo $event->getCoverData()->getPosition(); ?>;"></em>
               </u>
            </a>
        </div>
    </div>
    <?php } ?>

    <div class="media">
        <div class="media-object pull-left">
            <img class="es-avatar es-avatar-md" src="<?php echo $event->getAvatar();?>" />
        </div>

        <div class="media-body">
            <h4 class="es-stream-content-title">
                <a href="<?php echo $event->getPermalink();?>"><?php echo $event->getName(); ?></a>

                <?php if ($event->isOpen()) { ?>
                <span class="label label-success" data-original-title="<?php echo FD::_('COM_EASYSOCIAL_EVENTS_OPEN_EVENT_TOOLTIP', true);?>" data-es-provide="tooltip" data-placement="bottom">
                    <i class="fa fa-globe"></i>
                    <?php echo JText::_('COM_EASYSOCIAL_EVENTS_OPEN_EVENT'); ?>
                </span>
                <?php } ?>

                <?php if ($event->isClosed()) { ?>
                <span class="label label-danger" data-original-title="<?php echo FD::_('COM_EASYSOCIAL_EVENTS_PRIVATE_EVENT_TOOLTIP', true);?>" data-es-provide="tooltip" data-placement="bottom">
                    <i class="fa fa-lock"></i>
                    <?php echo JText::_('COM_EASYSOCIAL_EVENTS_PRIVATE_EVENT'); ?>
                </span>
                <?php } ?>

                <?php if ($event->isInviteOnly()) { ?>
                <span class="label label-warning" data-original-title="<?php echo FD::_('COM_EASYSOCIAL_EVENTS_INVITE_EVENT_TOOLTIP', true);?>" data-es-provide="tooltip" data-placement="bottom">
                    <i class="fa fa-lock muted"></i>
                    <?php echo JText::_('COM_EASYSOCIAL_EVENTS_INVITE_EVENT'); ?>
                </span>
                <?php } ?>
            </h4>

            <p class="mb-10 mt-10 blog-description">
                <?php echo $this->html('string.truncater', strip_tags($event->getDescription()), 250);?>
            </p>

            <div class="stream-apps-meta mt-5">
                <i class="fa fa-calendar mr-5"></i>
                <?php echo $event->getStartEndDisplay(); ?>
            </div>

            <ul class="stream-apps-meta ml-0 pl-0">
                <li>
                    <span>
                        <a href="<?php echo FRoute::events(array('layout' => 'category' , 'id' => $event->getCategory()->getAlias()));?>">
                            <i class="fa fa-database"></i> <?php echo $event->getCategory()->get('title'); ?>
                        </a>
                    </span>
                </li>
                <li>
                    <span>
                        <a href="<?php echo FRoute::albums(array('uid' => $event->id, 'type' => SOCIAL_TYPE_EVENT));?>">
                            <i class="fa fa-photo"></i> <?php echo JText::sprintf(FD::string()->computeNoun('COM_EASYSOCIAL_EVENTS_TOTAL_ALBUMS', $event->getTotalAlbums()), $event->getTotalAlbums()); ?>
                        </a>
                    </span>
                </li>
                <li>
                    <span>
                        <i class="fa fa-users"></i> <?php echo JText::sprintf(FD::string()->computeNoun('COM_EASYSOCIAL_EVENTS_TOTAL_GUESTS', $event->getTotalGoing()), $event->getTotalGoing()); ?>
                    </span>
                </li>
                <li>
                    <span>
                        <i class="fa fa-eye"></i> <?php echo JText::sprintf(FD::string()->computeNoun('COM_EASYSOCIAL_EVENTS_TOTAL_VIEWS', $event->hits), $event->hits); ?>
                    </span>
                </li>
            </ul>

            <a href="<?php echo $event->getPermalink();?>"><?php echo JText::_('APP_USER_EVENTS_VIEW_EVENT'); ?> &rarr;</a>
        </div>
    </div>
</div>
