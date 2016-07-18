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
<div class="row">
    <?php echo $this->loadTemplate('fields/event/startend/display_control', array(
        'type' => 'start',
        'datetime' => $startDatetime,
        'allowTimezone' => $params->get('allow_timezone'),
        'timezone' => $params->get('allow_timezone') ? $timezone : '',
        'dateFormat' => $dateFormat,
        'timezones' => $params->get('allow_timezone') ? $timezones : ''));
    ?>

    <?php if ($endDatetime) { ?>
    <?php echo $this->loadTemplate('fields/event/startend/display_control', array(
        'type' => 'end',
        'datetime' => $endDatetime,
        'allowTimezone' => $params->get('allow_timezone'),
        'timezone' => $params->get('allow_timezone') ? $timezone : '',
        'dateFormat' => $dateFormat,
        'timezones' => $params->get('allow_timezone') ? $timezones : ''));
    ?>
    <?php } ?>
</div>
