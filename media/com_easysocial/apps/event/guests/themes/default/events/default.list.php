<?php
/**
* @package        EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license        GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if (!empty($guests)) { ?>
<ul class="fd-reset-list app-contents-list">
    <?php foreach ($guests as $guest) { ?>

    <li class="member-item
        <?php if ($guest->isOwner()) { ?>is-owner<?php } ?>
        <?php if ($guest->isStrictlyAdmin()) { ?>is-admin<?php } ?>
        <?php if ($guest->isGoing()) { ?>is-going<?php } ?>
        <?php if ($guest->isMaybe()) { ?>is-maybe<?php } ?>
        <?php if ($guest->isNotGoing()) { ?>is-not-going<?php } ?>
        <?php if ($guest->isPending()) { ?>is-pending<?php } ?>
        "
        data-event-guest-item
        data-guest-id="<?php echo $guest->id; ?>"
    >
        <?php if ($myGuest->uid !== $guest->uid && !$guest->isOwner() && ($this->my->isSiteAdmin() || $myGuest->isOwner() || $myGuest->isAdmin())) { ?>
        <div class="pull-right btn-group">
            <a class="dropdown-toggle_ loginLink btn btn-es btn-dropdown" data-bs-toggle="dropdown" href="javascript:void(0);">
                <i class="icon-es-dropdown"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-user messageDropDown">
                <?php if (($myGuest->isOwner() || $this->my->isSiteAdmin()) && $guest->isStrictlyAdmin()) { ?>
                <li>
                    <a href="javascript:void(0);" data-guest-demote><?php echo JText::_('APP_EVENT_GUESTS_REVOKE_ADMIN'); ?></a>
                </li>
                <?php } ?>

                <?php if (!$guest->isAdmin() && $guest->isGuest()) { ?>
                <li>
                    <a href="javascript:void(0);" data-guest-promote><?php echo JText::_('APP_EVENT_GUESTS_MAKE_ADMIN'); ?></a>
                </li>
                <?php } ?>

                <?php if ($myGuest->isOwner() || $this->my->isSiteAdmin() || ($myGuest->isAdmin() && !$guest->isAdmin())) { ?>
                <li>
                    <a href="javascript:void(0);" data-guest-remove><?php echo JText::_('APP_EVENT_GUESTS_REMOVE_FROM_EVENT'); ?></a>
                </li>
                <?php } ?>

                <?php if ($guest->isPending()) { ?>
                <li>
                    <a href="javascript:void(0);" data-guest-approve><?php echo JText::_('APP_EVENT_GUESTS_APPROVE_REQUEST'); ?></a>
                </li>
                <li>
                    <a href="javascript:void(0);" data-guest-reject><?php echo JText::_('APP_EVENT_GUESTS_REJECT_REQUEST'); ?></a>
                </li>
                <?php } ?>
            </ul>
        </div>
        <?php } ?>

        <?php echo $this->loadTemplate('site/avatar/default', array('user' => ES::user($guest->uid))); ?>
        <h5>
            <?php echo $this->html('html.user', $guest->uid, false); ?>
        
            <span class="label label-primary label-owner"><?php echo JText::_('APP_EVENT_GUESTS_OWNER'); ?></span>

            <span class="label label-danger label-admin"><?php echo JText::_('APP_EVENT_GUESTS_ADMIN'); ?></span>

            <span class="label label-success label-going"><?php echo JText::_('APP_EVENT_GUESTS_GOING'); ?></span>

            <span class="label label-warning label-not-going"><?php echo JText::_('APP_EVENT_GUESTS_NOT_GOING'); ?></span>

            <span class="label label-info label-maybe"><?php echo JText::_('APP_EVENT_GUESTS_MAYBE'); ?></span>

            <span class="label label-warning label-pending"><?php echo JText::_('APP_EVENT_GUESTS_PENDING'); ?></span>

            <span class="label label-warning label-pending-invitation"><?php echo JText::_('APP_EVENT_GUESTS_INVITED'); ?></span>
        </h5> 
    </li>
    <?php } ?>

</ul>
<?php } ?>
<div class="empty empty-hero">
    <i class="fa fa-users"></i>
    <?php echo JText::_('APP_EVENT_GUESTS_EMPTY'); ?>
</div>
