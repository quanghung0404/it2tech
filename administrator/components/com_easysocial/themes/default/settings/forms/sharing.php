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
			$settings->renderSetting('Enable Sharing', 'sharing.enabled', 'boolean', array('help' => true))
		)
	),

	$settings->renderColumn(
		$settings->renderSection(
			$settings->renderHeader('Sharing Vendors'),
			$settings->renderSetting('Facebook', 'sharing.vendors.facebook', 'boolean'),
			$settings->renderSetting('Twitter', 'sharing.vendors.twitter', 'boolean'),
			$settings->renderSetting('Google', 'sharing.vendors.google', 'boolean'),
			$settings->renderSetting('Live', 'sharing.vendors.live', 'boolean'),
			$settings->renderSetting('LinkedIn', 'sharing.vendors.linkedin', 'boolean'),
			$settings->renderSetting('MySpace', 'sharing.vendors.myspace', 'boolean'),
			$settings->renderSetting('VK', 'sharing.vendors.vk', 'boolean'),
			$settings->renderSetting('StumbleUpon', 'sharing.vendors.stumbleupon', 'boolean'),
			$settings->renderSetting('Digg', 'sharing.vendors.digg', 'boolean'),
			$settings->renderSetting('Tumblr', 'sharing.vendors.tumblr', 'boolean'),
			$settings->renderSetting('Evernote', 'sharing.vendors.evernote', 'boolean'),
			$settings->renderSetting('Reddit', 'sharing.vendors.reddit', 'boolean'),
			$settings->renderSetting('Delicious', 'sharing.vendors.delicious', 'boolean')
		),

		$settings->renderSection(
			$settings->renderHeader('Email'),
			$settings->renderSetting('Enable Email', 'sharing.vendors.email', 'boolean', array('help' => true)),
			$settings->renderSetting('Limit per Hour', 'sharing.email.limit', 'input', array('help' => true, 'unit' => true, 'class' => 'form-control input-sm input-short text-center'))
		)
	)
);
