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
<div data-event-field-recurring class="form-horizonal">
    <div class="help-block">
        <select name="<?php echo $inputName; ?>[type]" data-recurring-type class="form-control input-sm es-recurring-select">
            <option value="none" <?php if (empty($value) || empty($value->type) || $value->type == 'none') { ?>selected="selected"<?php } ?>><?php echo JText::_('FIELD_EVENT_RECURRING_TYPE_NONE'); ?></option>
            <option value="daily" <?php if ($value->type == 'daily') { ?>selected="selected"<?php } ?>><?php echo JText::_('FIELD_EVENT_RECURRING_TYPE_DAILY'); ?></option>
            <?php /* Temporarily don't want weekly option
            <option value="weekly" <?php if ($value->type == 'weekly') { ?>selected="selected"<?php } ?>><?php echo JText::_('FIELD_EVENT_RECURRING_TYPE_WEEKLY'); ?></option>
            */ ?>
            <option value="monthly" <?php if ($value->type == 'monthly') { ?>selected="selected"<?php } ?>><?php echo JText::_('FIELD_EVENT_RECURRING_TYPE_MONTHLY'); ?></option>
            <option value="yearly" <?php if ($value->type == 'yearly') { ?>selected="selected"<?php } ?>><?php echo JText::_('FIELD_EVENT_RECURRING_TYPE_YEARLY'); ?></option>
        </select>
    </div>

    <div class="help-block" data-recurring-daily-block <?php if ($value->type !== 'daily') { ?>style="display: none;"<?php } ?>>

        <div class="es-recurring-daily-list">
            <?php foreach ($weekdays as $weekday) { ?>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="<?php echo $inputName; ?>[daily][]" value="<?php echo $weekday['key']; ?>" <?php if (!empty($value->daily) && in_array($weekday['key'], $value->daily)) { ?>checked="checked"<?php } ?> />
                        <?php echo $weekday['value']; ?>
                </label>
            </div>
            <?php } ?>
        </div>
    </div>

    <?php // Future purposes ?>
    <?php if (false) { ?>
    <div class="help-block">
        <label>Monthly (dummy)</label>
        <div class="radio">
            <label>
                <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked>
                Day of the month

            </label>
            <input type="number" class="form-control input-sm" placeholder="day" style="display:inline;width:80px;">
        </div>

        <div class="radio">
            <label>
                <input type="radio" name="optionsRadios" id="optionsRadios1" value="option2" checked>
                Last day of the month
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="optionsRadios" id="optionsRadios1" value="option2" checked>
            </label>
            <div class="form-inline">
                <div class="form-group ml-0 mr-0">
                    <select name="" id="" class="form-control input-sm">
                        <option value="">First</option>
                        <option value="">Second</option>
                        <option value="">Third</option>
                        <option value="">Fourth</option>
                    </select>
                </div>
                <div class="form-group ml-0 mr-0">
                    <select name="" id="" class="form-control input-sm">
                        <option value="">Sunday</option>
                    </select>

                </div>
            </div>
        </div>

    </div>
    <?php } ?>

    <div class="help-block" data-recurring-end-block <?php if (empty($value->type) || $value->type === 'none') { ?>style="display: none;"<?php } ?>>
        <div class="form-inline">
            <div class="form-group ml-0 mr-5">
                <label>
                    <?php echo JText::_('FIELD_EVENT_RECURRING_END'); ?>:
                </label>
            </div>
            <div class="form-group ml-0 mr-0">
                <div class="input-group input-group-sm">
                    <input class="form-control" type="text" data-recurring-end-picker />
                    <span class="input-group-addon" data-recurring-end-toggle="">
                        <i class="fa fa-calendar"></i>
                    </span>
                </div>
                <input type="hidden" name="<?php echo $inputName; ?>[end]" data-recurring-end-result value="<?php echo isset($value->end) ? $value->end : ''; ?>" />
            </div>
        </div>
    </div>

    <div class="help-block alert alert-info-2 mt-10" style="display: none;" data-recurring-schedule-loading-block>
        <div class="fd-loading"><span><?php echo JText::_('FIELD_EVENT_RECURRING_CHECKING'); ?></span></div>
    </div>

    <div class="help-block" data-recurring-summary-block style="display: none">
    </div>
</div>
