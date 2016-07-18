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
<div data-events-form-guests>
    <div class="fd-cf">
        <div class="btn-group btn-group-sm pull-left mb-15">
            <button type="button" class="btn btn-default ph-10" data-event-invite-guest>
                <i class="fa fa-user-plus"></i> <?php echo JText::_('COM_EASYSOCIAL_EVENTS_GUESTS_INVITE_GUEST'); ?>
            </button>
            <button type="button" class="btn btn-default ph-10" data-event-remove-guest>
                <i class="fa fa-user-remove"></i> <?php echo JText::_('COM_EASYSOCIAL_EVENTS_GUESTS_REMOVE_GUEST'); ?>
            </button>
            <button type="button" class="btn btn-default ph-10" data-event-approve-guest>
                <i class="fa fa-check "></i> <?php echo JText::_('COM_EASYSOCIAL_EVENTS_GUESTS_APPROVE_GUEST'); ?>
            </button>
            <button type="button" class="btn btn-default ph-10" data-event-promote-guest>
                <i class="fa fa-arrow-up-2 "></i> <?php echo JText::_('COM_EASYSOCIAL_EVENTS_GUESTS_PROMOTE_TO_ADMIN'); ?>
            </button>
            <button type="button" class="btn btn-default ph-10" data-event-demote-guest>
                <i class="fa fa-angle-down "></i> <?php echo JText::_('COM_EASYSOCIAL_EVENTS_GUESTS_REMOVE_ADMIN'); ?>
            </button>

        </div>
        <div class="form-group pull-right mt-0">
            <div><?php echo $this->html('filter.limit', $limit); ?></div>
        </div>
    </div>

    <div class="panel-table">
        <table class="app-table table table-eb table-striped">
            <thead>
                <tr>
                    <th width="1%" class="center">
                        <input type="checkbox" name="toggle" data-table-grid-checkall />
                    </th>

                    <th>
                        <?php echo $this->html('grid.sort', 'username', JText::_('COM_EASYSOCIAL_USERS_NAME'), $ordering, $direction); ?>
                    </th>

                    <th width="5%" class="center">
                        <?php echo $this->html('grid.sort', 'state', JText::_('COM_EASYSOCIAL_TABLE_COLUMN_STATUS'), $ordering, $direction); ?>
                    </th>

                    <th width="18%" class="center">
                        <?php echo $this->html('grid.sort', 'username', JText::_('COM_EASYSOCIAL_USERS_USERNAME'), $ordering, $direction); ?>
                    </th>

                    <th width="10%" class="center">
                        <?php echo $this->html('grid.sort', 'id', JText::_('COM_EASYSOCIAL_USERS_ID'), $ordering, $direction); ?>
                    </th>
                </tr>
            </thead>

            <tbody>
            <?php if (!empty($guests)) { ?>
                <?php $i = 0; ?>
                <?php foreach ($guests as $guest) { ?>
                    <?php $user = FD::user($guest->uid); ?>
                    <tr>
                        <td><?php echo $this->html('grid.id', $i, $guest->id); ?></td>

                        <td style="text-align: left;">
                            <span class="es-avatar es-avatar-rounded pull-left mr-15 ml-5">
                                <img src="<?php echo $user->getAvatar(SOCIAL_AVATAR_MEDIUM);?>" width="24" align="left" />
                            </span>

                            <a href="<?php echo FRoute::_('index.php?option=com_easysocial&view=users&layout=form&id=' . $user->id);?>"
                                data-user-insert
                                data-id="<?php echo $user->id;?>"
                                data-alias="<?php echo $user->getAlias();?>"
                                data-title="<?php echo $this->html('string.escape', $user->name);?>"
                                data-avatar="<?php echo $this->html('string.escape', $user->getAvatar(SOCIAL_AVATAR_MEDIUM));?>"
                            >
                                <?php echo $user->name;?>
                            </a>

                            <div class="fd-small">
                            <?php if ($guest->isOwner()) { ?>
                                <span class="fd-small label label-success"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_GUEST_OWNER'); ?></span>
                            <?php } ?>

                            <?php if (!$guest->isOwner() && $guest->isAdmin()) { ?>
                                <span class="label label-info"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_GUEST_ADMIN'); ?></span>
                            <?php } ?>
                            </div>
                        </td>

                        <td class="center">
                            <?php echo $this->html('grid.published', $guest, 'events', 'state', array('', '', 'approveGuests', '', ''), array('COM_EASYSOCIAL_EVENTS_GUEST_STATE_INVITED', 'COM_EASYSOCIAL_EVENTS_GUEST_STATE_GOING', 'COM_EASYSOCIAL_EVENTS_GUEST_STATE_PENDING', 'COM_EASYSOCIAL_EVENTS_GUEST_STATE_MAYBE', 'COM_EASYSOCIAL_EVENTS_GUEST_STATE_NOT_GOING'), array('invited', 'going', 'pending', 'maybe', 'notgoing')); ?>
                        </td>

                        <td class="center">
                            <span><?php echo $user->username;?></span>
                        </td>

                        <td class="center">
                            <?php echo $user->id;?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="5">
                        <div class="footer-pagination"><?php echo $pagination->getListFooter();?></div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
