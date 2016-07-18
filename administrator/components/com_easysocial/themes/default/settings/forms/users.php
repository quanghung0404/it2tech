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

	$nameDisplayOptions = array(
		$settings->makeOption('Username', 'username'),
		$settings->makeOption('Real Name', 'realname'),
		'help' => true,
		'class' => 'form-control input-sm'
	);

	$deleteOptions = array(
		$settings->makeOption('Delete Immediately And Notify Admin', 'delete'),
		$settings->makeOption('Unpublish Account And Notify Admin', 'unpublish'),
		'help' => true,
		'class' => 'form-control input-sm'
	);

	$startItem = array(
		$settings->makeOption('Me And Friends', 'me'),
		$settings->makeOption('Everyone', 'everyone'),
		$settings->makeOption('Following', 'following'),
		'help' => true,
		'class' => 'form-control input-sm'
	);

	$usersSorting = array(
		$settings->makeOption('Latest', 'latest'),
		$settings->makeOption('Alphabetically', 'alphabetical'),
		$settings->makeOption('LastLogin', 'lastlogin'),
		'help' => true,
		'class' => 'form-control input-sm'
	);

	$incompleteProfileActions = array(
		$settings->makeOption('Show Message on Site Wide', 'info'),
		$settings->makeOption('Show Message on Profile Page', 'infoprofile'),
		$settings->makeOption('Redirect to Edit Page', 'redirect'),
		'help' => true,
		'info' => true,
		'class' => 'form-control input-sm',
	);

	$logoutMenus 	= $this->html('form.menus', 'general.site.logout', $this->config->get('general.site.logout'));
	$loginMenus 	= $this->html('form.menus', 'general.site.login', $this->config->get('general.site.login'), array(JText::_('COM_EASYSOCIAL_USERS_SETTINGS_MENU_GROUP_CORE') => array(JHtml::_('select.option', 'null', JText::_('COM_EASYSOCIAL_USERS_SETTINGS_STAY_SAME_PAGE')))));

	$profileDefaultDisplay = array(
		$settings->makeOption('Profile Display Timeline', 'timeline'),
		$settings->makeOption('Profile Display About', 'about'),
		'help' => true,
		'class' => 'form-control input-sm'
	);

	$advansedSearchSorting = array(
		$settings->makeOption('Default', 'default'),
		$settings->makeOption('Recent logged in', 'lastvisitDate'),
		$settings->makeOption('Recent joined', 'registerDate'),
		'help' => true,
		'class' => 'form-control input-sm'
	);

	// unit must be in days
	$reminderDurationList = array(
		$settings->makeOption('2 weeks', '14'),
		$settings->makeOption('1 month', '30'),
		$settings->makeOption('2 months', '60'),
		$settings->makeOption('3 months', '90'),
		$settings->makeOption('6 months', '180'),
		'help' => false,
		'class' => 'form-control input-sm'
	);

	// Simple urls settings
	$simpleUrls = $settings->renderSetting('Allow Simple Urls', 'users.simpleUrl', 'boolean', array('help' => true));

	$jConfig = ES::jConfig();

	if (!$jConfig->getValue('sef_rewrite')) {
		$msg = JText::sprintf('COM_EASYSOCIAL_USERS_SETTINGS_ENABLE_MOD_REWRITE', JRoute::_('index.php?option=com_config'));
		$simpleUrls = $settings->renderSetting('Allow Simple Urls', 'users.simpleUrl', 'text', array('text' => $msg));
	}

	if (!JPluginHelper::isEnabled('system', 'easysocial')) {
		$msg = JText::sprintf('COM_EASYSOCIAL_USERS_SETTINGS_ENABLE_EASYSOCIAL_SYSTEM_PLUGIN', JRoute::_('index.php?option=com_config'));
		$simpleUrls = $settings->renderSetting('Allow Simple Urls', 'users.simpleUrl', 'text', array('text' => $msg));	
	}

echo $settings->renderTabs(array(
		'display' => $settings->renderPage(
						$settings->renderColumn(
							$settings->renderSection(
								$settings->renderHeader('Display Options', 'Display Options Info'),
								$settings->renderSetting('Display name format', 'users.displayName', 'list', $nameDisplayOptions),
								$settings->renderSetting('Permalink format', 'users.aliasName', 'list', $nameDisplayOptions),
								$settings->renderSetting('Display about profile', 'users.display.profiletype', 'boolean', array('help' => true)),
								$settings->renderSetting('Profile Default Display', 'users.profile.display', 'list', $profileDefaultDisplay),
								$simpleUrls
							)
						),
						$settings->renderColumn(
							$settings->renderSection(
								$settings->renderHeader('Avatar Options', 'Avatar Options Info'),
								$settings->renderSetting('Allow Webcam Avatar', 'users.avatarWebcam', 'boolean', array('help' => true))
							),
							$settings->renderSection(
								$settings->renderHeader('Profile'),
								$settings->renderSetting('Check for Profile Completion', 'user.completeprofile.required', 'boolean', array('help' => true)),
								$settings->renderSetting('Include Optional Field', 'user.completeprofile.strict', 'boolean', array('help' => true)),
								$settings->renderSetting('Action on Incomplete Profile', 'user.completeprofile.action', 'list', $incompleteProfileActions)
							)
						)
					),
		'authentication' => $settings->renderPage(
								$settings->renderColumn(
									$settings->renderSection(
										$settings->renderHeader('Authentication', 'Authentication Info'),
										$settings->renderSetting('Allow Login With Email', 'general.site.loginemail', 'boolean', array('help' => true, 'info' => true)),
										$settings->renderSetting('Use Email as Username', 'registrations.emailasusername', 'boolean', array('help' => true)),
										$settings->renderSetting('Login Redirection', 'general.site.login', 'custom', array('help' => true, 'field' => $loginMenus, 'class' => 'form-control input-sm')),
										$settings->renderSetting('Logout Redirection', 'general.site.logout', 'custom', array('help' => true, 'field' => $logoutMenus, 'class' => 'form-control input-sm')),
										$settings->renderSetting('Enable 2 Factor Authentication', 'general.site.twofactor', 'boolean', array('help' => true))
									)
								)
		),
		'workflow' => $settings->renderPage(
						$settings->renderColumn(
							$settings->renderSection(
								$settings->renderHeader('Dashboard Behavior'),
								$settings->renderSetting('Default Start Item', 'users.dashboard.start', 'list', $startItem)
							),
							$settings->renderSection(
								$settings->renderHeader('Activity Stream'),
								$settings->renderSetting('Add Friend', 'users.stream.friend', 'boolean', array('help' => true)),
								$settings->renderSetting('Following User', 'users.stream.following', 'boolean', array('help' => true)),
								$settings->renderSetting('Edit Profile', 'users.stream.profile', 'boolean', array('help' => true))
							),
							$settings->renderSection(
								$settings->renderHeader('Reminder'),
								$settings->renderSetting('Reminder for inactive users', 'users.reminder.enabled', 'boolean', array('help' => true)),
								$settings->renderSetting('Duration for inactivity', 'users.reminder.duration', 'list', $reminderDurationList)
							),

							$settings->renderSection(
								$settings->renderHeader('Advanced Search'),
								$settings->renderSetting('Sorting', 'users.advancedsearch.sorting', 'list', $advansedSearchSorting)
							)
						),
						$settings->renderColumn(
							$settings->renderSection(
								$settings->renderHeader('User Listings'),
								$settings->renderSetting('Include Site Administrators', 'users.listings.admin', 'boolean', array('help' => true)),
								$settings->renderSetting('Default Sorting Method', 'users.listings.sorting', 'list', $usersSorting),
								$settings->renderSetting('Allow admin to view ESAD users', 'users.listings.esadadmin', 'boolean', array('help' => true))
							),
							$settings->renderSection(
								$settings->renderHeader('Leaderboard Listings'),
								$settings->renderSetting('Include Site Administrators in Leaderboard', 'leaderboard.listings.admin', 'boolean', array('help' => true))
							),
							$settings->renderSection(
								$settings->renderHeader('User Account'),
								$settings->renderSetting('Account Deletion Workflow', 'users.deleteLogic', 'list', $deleteOptions),
								$settings->renderSetting('Allow User Blocking', 'users.blocking.enabled', 'boolean', array('help' => true))
							),
							$settings->renderSection(
								$settings->renderHeader('User Indexing'),
								$settings->renderSetting('Name indexing format', 'users.indexer.name', 'list', $nameDisplayOptions),
								$settings->renderSetting('Index Email', 'users.indexer.email', 'boolean', array('help' => true)),
								$settings->renderSetting('privacy validation', 'users.indexer.privacy', 'boolean', array('help' => true))
							)
						)
		)
	)
);
