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
<div data-group-events class="app-groups">

    <div class="es-filterbar row-table">
        <div class="col-cell filterbar-title"><?php echo JText::_('APP_GROUP_EVENTS_TITLE'); ?></div>

        <?php if ($group->canCreateEvent()) { ?>
        <div class="col-cell cell-tight">
            <a href="<?php echo FRoute::events(array('layout' => 'create', 'group_id' => $group->id));?>" class="btn btn-es-primary btn-sm pull-right">
                <?php echo JText::_('APP_GROUP_EVENTS_NEW_EVENT'); ?>
            </a>
        </div>
        <?php } ?>
    </div>

    <div class="app-contents-wrap" data-group-events-list>
        <?php echo $this->includeTemplate('site/events/default.list'); ?>
    </div>
</div>
