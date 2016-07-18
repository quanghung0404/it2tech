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
				$settings->renderHeader('Notification Bar Settings'),
				$settings->renderSetting('Display Notification bar', 'toolbar.display', 'boolean', array('help' => true) )
			)
		)
	);

	/*
	// Page
	$syntax = array(

		// Column
		array(

			// Section
			array(
				'Date Settings',
				array('Enable Daylight Saving', 'general.dst'),
				array('Daylight Saving', 'general.dst_offset', 'list', $dstOptions)
			)
		),

		// Column
		array(

			// Section
			array(
				'System Settings',
				array('Environment', 'general.environment', 'list', $envOptions),
				array('Profiler', 'general.profile'),
				array('Logger', 'general.logger')
			)
		)
	);
	*/

	// echo call_user_func_array(array($settings, 'renderPage'), $syntax);
