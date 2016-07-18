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
<div data-field-startend>
    <div class="row">
        <div class="col-md-6">
            <div id="datetimepicker4" class="input-group input-group-sm" data-event-start>
                <input type="text" class="form-control" placeholder="<?php echo JText::_('FIELDS_EVENT_STARTEND_START_DATETIME'); ?>" data-picker />
                <input type="hidden" name="startDatetime" value="<?php echo $startDatetime; ?>" data-datetime />
                <span class="input-group-addon" data-picker-toggle>
                    <i class="fa fa-calendar"></i>
                </span>

            </div>
        </div>

        <div class="col-md-6">
            <div id="datetimepicker4" class="input-group input-group-sm" data-event-end>
                <input type="text" class="form-control" placeholder="<?php echo JText::_('FIELDS_EVENT_STARTEND_END_DATETIME' . (!$params->get('require_end') ? '_OPTIONAL' : '')); ?>" data-picker />
                <input type="hidden" name="endDatetime" value="<?php echo $endDatetime; ?>" data-datetime />
                <span class="input-group-addon" data-picker-toggle>
                    <i class="fa fa-calendar"></i>
                </span>
            </div>
        </div>
    </div>

    <?php if ($params->get('allow_timezone')) { ?>
    <div class="mt-10">
        <select class="form-control" name="startendTimezone" data-event-timezone>
            <option value="UTC" <?php if ($timezone == 'UTC') { ?>selected="selected"<?php } ?>>UTC</option>

            <?php foreach ($timezones as $group => $zones) { ?>
                <optgroup label="<?php echo $group; ?>">
                <?php foreach ($zones as $zone) { ?>
                    <option value="<?php echo $zone; ?>" <?php if ($timezone == $zone) { ?>selected="selected"<?php } ?>><?php echo $zone; ?></option>
                <?php } ?>
                </optgroup>
            <?php } ?>
        </select>
    </div>
    <?php } ?>
</div>
