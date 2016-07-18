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
            $settings->renderHeader('Photo Storage Path'),
            $settings->renderSettingText('Photo Storage Path Desc'),
            $settings->renderSetting('Photos Storage Path', 'photos.storage.container', 'input', array('help' => true, 'class' => 'form-control input-sm', 'info' => true))
        )
	),
	$settings->renderColumn(
        $settings->renderSection(
            $settings->renderHeader('Avatar Storage Path'),
            $settings->renderSettingText('Avatar Storage Path Desc'),
            $settings->renderSetting('Avatars Storage Path', 'avatars.storage.container', 'input', array('help' => true, 'class' => 'form-control input-sm', 'info' => true))
        )
	)
);
