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
<div class="controls-description mb-15">
    <label class="radio" for="event_type_public">
        <input type="radio" checked="checked" value="1" id="event_type_public" name="event_type" <?php echo $value == 1 ? 'checked="checked"' : ''; ?> />
        <i class="fa fa-globe"></i> <?php echo JText::_('FIELDS_EVENT_TYPE_PUBLIC'); ?>
    </label>
    <div class="help-block fd-small mt-5">
        <?php echo JText::_('FIELDS_EVENT_TYPE_PUBLIC_DESC');?>
    </div>
</div>

<div class="controls-description mb-15">
    <label class="radio" for="event_type_private">
        <input type="radio" value="2" id="event_type_private" name="event_type" <?php echo $value == 2 ? 'checked="checked"' : ''; ?> />
        <i class="fa fa-user"></i> <?php echo JText::_('FIELDS_EVENT_TYPE_PRIVATE'); ?>
    </label>

    <div class="help-block fd-small mt-5">
        <?php echo JText::_('FIELDS_EVENT_TYPE_PRIVATE_DESC');?>
    </div>
</div>

<div class="controls-description mb-15">
    <label class="radio" for="event_type_invite">
        <input type="radio" value="3" id="event_type_invite" name="event_type" <?php echo $value == 3 ? 'checked="checked"' : ''; ?> />
        <i class="fa fa-lock"></i> <?php echo JText::_('FIELDS_EVENT_TYPE_INVITE_ONLY'); ?>
    </label>

    <div class="help-block fd-small mt-5">
        <?php echo JText::_('FIELDS_EVENT_TYPE_INVITE_ONLY_DESC');?>
    </div>
</div>
