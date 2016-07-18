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
<div class="row" data-field-reminder>
    <div class="col-xs-3">
        <div class="input-group">
            <input type="text" id="event_reminder"
                name="event_reminder"
                class="form-control input-sm text-center"
                autocomplete="off"
                value="<?php echo $value; ?>"
                placeholder="<?php echo JText::_('FIELDS_EVENT_UPCOMINGREMINDER_PLACEHOLDER'); ?>"
                data-input
                <?php echo $params->get('readonly') ? ' disabled="disabled"' : '';?>
            />
            <span class="input-group-addon"><?php echo JText::_('COM_EASYSOCIAL_DAYS');?></span>
        </div>
    </div>
</div>
