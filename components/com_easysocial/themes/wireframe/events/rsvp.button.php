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
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="dropdown_ mt-10"
    data-event-rsvp-button-wapper
    data-id="<?php echo $event->id; ?>"
    data-allowmaybe="<?php echo (int) $event->getParams()->get('allowmaybe'); ?>"
    data-allownotgoingguest="<?php echo (int) $guest->isOwner() || $event->getParams()->get('allownotgoingguest'); ?>"
    data-hidetext="0"
>
<?php if (! $guest->isParticipant()) { ?>
    <?php if ($this->my->getAccess()->get('events.allow.join') && !$this->my->getAccess()->exceeded('events.join', $this->my->getTotalEvents()) && $event->seatsLeft() !== 0) { ?>
        <?php if ($event->isOpen()) { ?>
        <a class="btn btn-es btn-sm" href="javascript:void(0);"
            data-guest-action="attend"
            data-guest-state="going">
            <?php echo JText::_('COM_EASYSOCIAL_EVENTS_ATTEND_THIS_EVENT'); ?>
        </a>
        <?php } ?>

        <?php if ($event->isClosed()) { ?>
        <a class="btn btn-es btn-sm" href="javascript:void(0);"
            data-guest-action="request">
            <?php echo JText::_('COM_EASYSOCIAL_EVENTS_REQUEST_TO_ATTEND_THIS_EVENT'); ?>
        </a>
        <?php } ?>
    <?php } ?>
<?php } else { ?>

    <?php if ($guest->isPending()) { ?>
    <a class="btn btn-es btn-sm" href="javascript:void(0);"
        data-guest-action="withdraw"
        data-guest-withdraw>
        <?php echo JText::_('COM_EASYSOCIAL_EVENTS_GUEST_PENDING'); ?>
    </a>
    <?php } else { ?>

        <a href="javascript:void(0);"
            class="dropdown-toggle_ btn btn-es btn-sm<?php echo $defaultBtn; ?>"
            data-bs-toggle="dropdown"
            data-event-rsvp-button
        >
            <i class="fa fa-user-plus "></i>&nbsp;
            <span><?php echo $defaultBtnLabel; ?></span>
        </a>
        <ul class="dropdown-menu dropdown-arrow-topleft" data-event-button-container>
            <li class="">
                <a class="" href="javascript:void(0);"
                    data-guest-action="state"
                    data-guest-state="going">
                    <?php echo JText::_('COM_EASYSOCIAL_EVENTS_GUEST_GOING'); ?>
                </a>
            </li>

            <?php if ($event->getParams()->get('allowmaybe')) { ?>
            <li class="">
                <a class="" href="javascript:void(0);"
                    data-guest-action="state"
                    data-guest-state="maybe">
                    <?php echo JText::_('COM_EASYSOCIAL_EVENTS_GUEST_MAYBE'); ?>
                </a>
            </li>
            <?php } ?>

            <li class="">
                <a class="" href="javascript:void(0);"
                    data-guest-action="state"
                    data-guest-state="notgoing">
                    <?php echo JText::_('COM_EASYSOCIAL_EVENTS_GUEST_NOTGOING'); ?>
                </a>
            </li>
        </ul>
     <?php } ?>
<?php } ?>
</div>
