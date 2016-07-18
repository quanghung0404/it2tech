<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$timeformatOptions = array(
	$settings->makeOption('Display Time Format 12H', '12h'),
	$settings->makeOption('Display Time Format 24H', '24h'),
	'help' => true,
	'class' => 'form-control input-sm input-medium'
);
$defaultDisplay = array(
	$settings->makeOption('Default Display Timeline', 'timeline'),
	$settings->makeOption('Default Display Info', 'info'),
	'help' => true,
	'class' => 'form-control input-sm'
);

$startOfWeekOptions = array(
	$settings->makeOption('MON', 1, false),
	$settings->makeOption('TUE', 2, false),
	$settings->makeOption('WED', 3, false),
	$settings->makeOption('THU', 4, false),
	$settings->makeOption('FRI', 5, false),
	$settings->makeOption('SAT', 6, false),
	$settings->makeOption('SUN', 0, false),
);
echo $settings->renderPage(
	$settings->renderColumn(
		$settings->renderSection(
			$settings->renderHeader('General', 'General Desc'),
			$settings->renderSetting('Enable Events', 'events.enabled', 'boolean', array('help' => true)),
			$settings->renderSetting('Enable iCal Export', 'events.ical', 'boolean', array('help' => true)),
			$settings->renderSetting('Allow Invite Non Friends', 'events.invite.nonfriends', 'boolean', array('help' => true)),
			$settings->renderSetting('Recurring Limit', 'events.recurringlimit', 'input', array('help' => true, 'default' => 0, 'class' => 'input-sm')),
			$settings->renderSetting('Start Of Week', 'events.startofweek', 'list', $startOfWeekOptions)
		),
		$settings->renderSection(
			$settings->renderHeader('Stream', 'Stream Desc'),
			$settings->renderSetting('Creation Stream', 'events.stream.create', 'boolean', array('help' => true))
		)
	),
	$settings->renderColumn(
		$settings->renderSection(
			$settings->renderHeader("Layout", "Layout Desc"),
			$settings->renderSetting('Include Featured Event', 'events.listing.includefeatured', 'boolean', array('help' => true)),
			$settings->renderSetting('Include Group Event', 'events.listing.includegroup', 'boolean', array('help' => true)),
			$settings->renderSetting('Show End Date', 'events.showenddate', 'boolean', array('help' => true)),
			$settings->renderSetting('Display Time Format', 'events.timeformat', 'list', $timeformatOptions),
			$settings->renderSetting('Default Display', 'events.item.display', 'list', $defaultDisplay)
		)
	)
);
