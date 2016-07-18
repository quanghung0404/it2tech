<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
EasySocial
.require()
.script('apps/fields/event/startend/content').done(function($) {
    $('[data-field-<?php echo $field->id; ?>]').addController('EasySocial.Controller.Field.Event.Startend', {
        requiredEnd: <?php echo $params->get('require_enddate') ? 1 : 0; ?>,
        dateFormat: '<?php echo $dateFormat; ?>',
        allowTime: <?php echo $params->get('allow_time') ? 1 : 0; ?>,
        allowTimezone: <?php echo $params->get('allow_timezone') ? 1 : 0; ?>,
        yearfrom: '<?php echo $params->get('yearfrom'); ?>',
        yearto: '<?php echo $params->get('yearto'); ?>',
        disallowPast: <?php echo $params->get('disallow_past') ? 1 : 0; ?>,
        minuteStepping: <?php echo $params->get('minute_stepping', 15); ?>,
        defaultStart: '<?php echo $params->get('default_start', 'nexthour'); ?>',
        allday: <?php echo $allday ? 1 : 0; ?>,
        calendarLanguage: '<?php echo $params->get('calendar_language', 'english'); ?>',
        dow: <?php echo $this->config->get('events.startofweek', 0); ?>
    });
});
