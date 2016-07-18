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

$model 		= FD::model('Profiles');
$profiles	= $model->getItems();

$profilesList 	= array();

foreach($profiles as $profile)
{
	$profilesList[]	= $settings->makeOption($profile->title, $profile->id, false);
}

echo $settings->renderPage(
	$settings->renderColumn(
		$settings->renderSection(
			$settings->renderHeader('Twitter Application Settings'),
			$settings->renderSetting('Twitter Consumer Key', 'oauth.twitter.app', 'input', array('help' => true, 'info' => true, 'class="input-full"')),
			$settings->renderSetting('Twitter Consumer Secret', 'oauth.twitter.secret', 'input', array('help' => true, 'info' => true, 'class="input-full"')),

			$settings->renderSetting('Twitter Authentication', '', 'custom',
					array(
						'field' => FD::oauth('Twitter')->getLoginButton('/administrator/index.php?option=com_easysocial&controller=oauth&task=grant&uid=1&type=config&client=twitter&callback=' . urlencode(FRoute::_('index.php?option=com_easysocial&view=settings&layout=closeOauthDialog&tmpl=component')),
																					array('publish_stream'),
																					'popup'
																			),
						'rowAttributes'	=> array('data-oauth-twitter-button')
					)
			)
		)
	)
);
