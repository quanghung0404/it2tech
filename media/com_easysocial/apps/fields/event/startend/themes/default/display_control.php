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
<div class="col-md-6 es-field-event-startend-box" data-startend-box>
    <div class="es-field-box">
        <div class="es-field-box-header">
            <i class="fa fa-calendar"></i> <?php echo JText::_('FIELDS_EVENT_STARTEND_' . strtoupper($type) . '_DATETIME'); ?>
        </div>
        <p class="text-center es-field-event-startend-datetime" data-date data-date-utc="<?php echo $datetime->toSql(); ?>">
            <?php echo $datetime->toFormat($dateFormat); ?>
        </p>
        <?php if ($allowTimezone) { ?>
        <div class="text-center">
            <a href="javascript:void(0);" class="btn btn-es btn-block btn-xs" data-popbox data-popbox-position="bottom-right"><span data-timezone="<?php echo $timezone; ?>"><?php echo $timezone; ?></span> <i class="fa fa-arrow-down "></i></a>
        </div>

        <div style="display: none" data-popbox-content>
            <div class="text-center">
                <div class="es-timezone-block">
                    <select class="form-control input-sm es-timezone-select" data-timezone-select>
                        <option value="local"><?php echo JText::_('FIELDS_USER_DATETIME_LOCAL_TIMEZONE'); ?></option>
                        <option value="UTC" <?php if ($timezone === 'UTC') { ?>selected="selected"<?php } ?>>UTC</option>
                        <?php foreach ($timezones as $group => $zones) { ?>
                        <optgroup label="<?php echo $group; ?>">
                            <?php foreach ($zones as $zone) { ?>
                            <option value="<?php echo $zone; ?>" <?php if ($timezone === $zone) { ?>selected="selected"<?php } ?>><?php echo $zone; ?></option>
                            <?php } ?>
                        </optgroup>
                        <?php } ?>
                    </select>
                </div>


                <div class="es-timezone-block es-timezone-reset" data-timezone-reset>
                    <a href="javascript:void(0);"><?php echo JText::_('FIELDS_USER_DATETIME_TIMEZONE_RESET'); ?></a>
                </div>

                <div class="es-timezone-block es-timezone-my" data-timezone-local>
                    <a href="javascript:void(0);"><?php echo JText::_('FIELDS_USER_DATETIME_TIMEZONE_USE_LOCAL'); ?></a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
