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
			$settings->renderHeader('LinkedIn Application Settings'),
			$settings->renderSetting('LinkedIn API Key', 'oauth.linkedin.app', 'input', array('help' => true, 'info' => true, 'class="input-full"')),
			$settings->renderSetting('LinkedIn Secret Key', 'oauth.linkedin.secret', 'input', array('help' => true, 'info' => true, 'class="input-full"')),
			$settings->renderSetting('LinkedIn Authentication', '', 'custom', FD::oauth('LinkedIn')->getLoginButton(3, 'config', FRoute::_('index.php?option=com_easysocial&view=settings&layout=closeOauthDialog&tmpl=component'), array('publish_stream')))
		)
	)
);
