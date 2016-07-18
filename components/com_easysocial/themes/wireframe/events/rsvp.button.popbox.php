<div class="mt-10"
    data-event-rsvp-button-wapper
    data-id="<?php echo $event->id; ?>"
    data-allowmaybe="<?php echo (int) $event->getParams()->get('allowmaybe'); ?>"
    data-allownotgoingguest="<?php echo (int) $guest->isOwner() || $event->getParams()->get('allownotgoingguest'); ?>"
    data-hidetext="0"
    data-ispopbox="1"
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
            class="btn btn-es btn-sm<?php echo $defaultBtn; ?>"
            data-popbox
            data-popbox-id="fd"
            data-popbox-component="es"
            data-popbox-type="responseButton"
            data-popbox-toggle="click"
            data-popbox-position="bottom-left"
            data-popbox-target=".rsvp-button"
            data-event-rsvp-button
        >
            <i class="fa fa-user-plus"></i>&nbsp;
            <span><?php echo $defaultBtnLabel; ?></span>
        </a>
        <div style="display: none" class="rsvp-button" data-popbox-content>
            <ul class="list-unstyled mt-5 ml-0" data-event-button-container>
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
        </div>
     <?php } ?>
<?php } ?>
</div>
