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

echo $settings->renderPage(
	$settings->renderColumn(
		$settings->renderSection(
			$settings->renderHeader('General'),
			$settings->renderSetting('Enable reporting', 'reports.enabled', 'boolean', array('help' => true)),
			$settings->renderSetting('Allow guests', 'reports.guests', 'boolean', array('help' => true))
			//,$settings->renderSetting('Maximum reports per IP address', 'reports.maxip', 'input', array('help' => true, 'unit' => true, 'class' => 'form-control input-sm input-short text-center'))
		)
	),
	$settings->renderColumn(
		$settings->renderSection(
			$settings->renderHeader('Notifications'),
			$settings->renderSetting('Notify moderators', 'reports.notifications.moderators', 'boolean', array('help' => true)),
			$settings->renderSetting('Custom Email addresses', 'reports.notifications.emails', 'input', array('class' => 'form-control input-sm', 'help' => true, 'placeholder' => true))
		)
	)
);
