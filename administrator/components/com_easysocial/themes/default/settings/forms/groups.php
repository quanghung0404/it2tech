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

$defaultDisplay = array(
	$settings->makeOption('Default Display Timeline', 'timeline'),
	$settings->makeOption('Default Display Info', 'info'),
	'help' => true,
	'class' => 'form-control input-sm'
);

echo $settings->renderTabs(array(
	'general'	=> $settings->renderPage(
						$settings->renderColumn(
							$settings->renderSection(
								$settings->renderHeader('General'),
								$settings->renderSetting('Enable Groups', 'groups.enabled', 'boolean', array('help' => true)),
								$settings->renderSetting('Allow Invite Non Friends', 'groups.invite.nonfriends', 'boolean', array('help' => true)),
								$settings->renderSetting('Default Display', 'groups.item.display', 'list', $defaultDisplay),
								$settings->renderSetting('Enable Hit Counter', 'groups.hits.display', 'boolean', array('help' => true))
							)
						)
					)
	)
);
