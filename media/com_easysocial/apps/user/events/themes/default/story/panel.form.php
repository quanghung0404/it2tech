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
<div class="form-group">
    <input type="text" class="form-control input-sm" placeholder="<?php echo $titlePlaceholder; ?>" data-event-title />
</div>

<div class="form-group">
    <textarea name="description" id="description" class="input-sm form-control" placeholder="<?php echo $descriptionPlaceholder; ?>" data-event-description></textarea>
</div>

<div class="form-group" data-event-datetime-form data-yearfrom="<?php echo $yearfrom; ?>" data-yearto="<?php echo $yearto; ?>" data-allowtime="<?php echo $allowTime; ?>" data-allowtimezone="<?php echo $allowTimezone; ?>" data-dateformat="<?php echo $dateFormat; ?>" data-disallowpast="<?php echo $disallowPast; ?>" data-minutestepping="<?php echo $minuteStepping; ?>">
    <div class="row">
        <div class="col-md-6">
            <div id="datetimepicker4" class="input-group" data-event-datetime="start">
                <input type="text" class="form-control input-sm" placeholder="<?php echo JText::_('FIELDS_EVENT_STARTEND_START_DATETIME'); ?>" data-picker />
                <input type="hidden" data-datetime />
                <span class="input-group-addon" data-picker-toggle>
                    <i class="fa fa-calendar"></i>
                </span>
            </div>
        </div>

        <div class="col-md-6">
            <div id="datetimepicker4" class="input-group" data-event-datetime="end">
                <input type="text" class="form-control input-sm" placeholder="<?php echo JText::_('FIELDS_EVENT_STARTEND_END_DATETIME'); ?>" data-picker />
                <input type="hidden" data-datetime />
                <span class="input-group-addon" data-picker-toggle>
                    <i class="fa fa-calendar"></i>
                </span>
            </div>
        </div>
    </div>
</div>

<?php if ($allowTimezone) { ?>
<div class="form-group mt-10">
    <select class="form-control input-sm" data-event-timezone>
        <option value="UTC">UTC</option>

        <?php foreach ($timezones as $group => $zones) { ?>
            <optgroup label="<?php echo $group; ?>">
            <?php foreach ($zones as $zone) { ?>
                <option value="<?php echo $zone; ?>"><?php echo $zone; ?></option>
            <?php } ?>
            </optgroup>
        <?php } ?>
    </select>
    <?php } ?>
</div>
