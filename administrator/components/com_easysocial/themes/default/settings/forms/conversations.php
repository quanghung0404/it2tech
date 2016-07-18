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
			$settings->renderSetting('Enable conversations', 'conversations.enabled', 'boolean', array('help' => true)),
			$settings->renderSetting('Enable group conversations', 'conversations.multiple', 'boolean', array('help' => true)),
			$settings->renderSetting('Allow compose to nonfriend users', 'conversations.nonfriend', 'boolean', array('help' => true))
		),
		$settings->renderSection(
			$settings->renderHeader('Pagination'),
			$settings->renderSetting('no recent conversations', 'conversations.pagination.toolbarlimit', 'input', array('class' => 'form-control input-sm input-short text-center', 'unit' => true, 'help' => true))
			// $settings->renderSetting('Conversations per page', 'conversations.pagination.limit', 'input', array('class' => 'form-control input-sm input-short text-center', 'unit' => true, 'help' => true))
		)
		//,$settings->renderSection(
		// 	$settings->renderHeader('Locations'),
		// 	$settings->renderSetting('Enable location', 'conversations.location', 'boolean', array('help' => true))
		//)
	),
	$settings->renderColumn(
		$settings->renderSection(
			$settings->renderHeader('Attachments'),
			$settings->renderSetting('Allow attachments', 'conversations.attachments.enabled', 'boolean', array('help' => true)),
			$settings->renderSetting('Attachment types', 'conversations.attachments.types', 'input', array('help' => true, 'class' => 'form-control input-sm')),
			$settings->renderSetting('Attachment max size', 'conversations.attachments.maxsize', 'input', array('help' => true, 'unit' => true, 'class' => 'form-control input-sm input-short text-center')),
			$settings->renderSetting('Storage path', 'conversations.attachments.storage', 'input', array('help' => true, 'class' => 'form-control input-sm'))
		)
	)
);
