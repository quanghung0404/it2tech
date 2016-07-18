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
$profiles	= $model->getProfiles();

$profilesList 	= array();

if($profiles)
{
	$profilesList[]		= $settings->makeOption(JText::_('COM_EASYSOCIAL_FACEBOOK_SETTINGS_FACEBOOK_SELECT_A_PROFILE'), '', false);

	foreach($profiles as $profile)
	{
		$profilesList[]	= $settings->makeOption($profile->title, $profile->id, false);
	}
}


// Registration types
$registrationTypes 	= array($settings->makeOption(JText::_('COM_EASYSOCIAL_FACEBOOK_SETTINGS_SIMPLIFIED'), 'simplified', false), $settings->makeOption(JText::_('COM_EASYSOCIAL_FACEBOOK_SETTINGS_NORMAL'), 'normal', false), 'help' => true);
$sourceCoast 		= '<div class="clearfix mt-20"><img width="128" style="margin-left: 20px;margin-right:25px; float: left;" align="left" src="' . JURI::base() . 'components/com_easysocial/themes/default/images/sourcecoast.png' . '" /><div style="overflow:hidden;">' . JText::_('COM_EASYSOCIAL_FACEBOOK_SETTINGS_JFBCONNECT_INFO') . '<br /><br /><a href="http://www.shareasale.com/r.cfm?B=495360&U=614082&M=46720&urllink=" class="btn btn-es-primary btn-sm" target="_blank">' . JText::_('COM_EASYSOCIAL_GET_JFBCONNECT_NOW') . '</a></div></div>';

$facebookUsername = array(
	$settings->makeOption('Facebook Username Use Email', 'email'),
	$settings->makeOption('Facebook Username Use Full Name', 'name')
);

echo $settings->renderPage(
	$settings->renderColumn(
		$settings->renderSection(
			$settings->renderHeader('Facebook Application Settings'),
			$settings->renderSetting('Facebook App ID', 'oauth.facebook.app', 'input', array('help' => true, 'info' => true, 'class' => 'form-control input-sm', 'attributes' => array('data-oauth-facebook-id'))),
			$settings->renderSetting('Facebook App Secret', 'oauth.facebook.secret', 'input', array('help' => true, 'info' => true, 'class' => 'form-control input-sm', 'attributes' => array('data-oauth-facebook-secret')))
			// $settings->renderSetting('Facebook Authentication', '', 'custom',
			// 		array(
			// 			'field' => FD::oauth('Facebook')->getLoginButton('/administrator/index.php?option=com_easysocial&controller=oauth&task=grant&uid=1&type=config&client=facebook&callback=' . urlencode(FRoute::_('index.php?option=com_easysocial&view=settings&layout=closeOauthDialog&tmpl=component')),
			// 																		array('publish_stream'),
			// 																		'popup'
			// 																),
			// 			'rowAttributes'	=> array('data-oauth-facebook-button')
			// 		)
			//)
		),
		$settings->renderSection(
			$settings->renderHeader('Facebook Opengraph Settings'),
			$settings->renderSetting('Add Opengraph Tags', 'oauth.facebook.opengraph.enabled', 'boolean', array('help' => true, 'info' => true))
		),
		$settings->renderSection(
			$settings->renderHeader('JFBConnect Integrations'),
			$settings->renderSettingText($sourceCoast, '', false),
			$settings->renderSetting('Use JFBConnect Buttons', 'oauth.facebook.jfbconnect.enabled', 'boolean', array('help' => true, 'info' => true))
		)
	),
	$settings->renderColumn(
		$settings->renderSection(
			$settings->renderHeader('Facebook Registration Settings'),
			$settings->renderSetting('Facebook Allow Registration', 'oauth.facebook.registration.enabled', 'boolean', array('help' => true)),
			$settings->renderSetting('Facebook Registration Type', 'oauth.facebook.registration.type', 'list', $registrationTypes),
			$settings->renderSetting('Facebook Profile Type', 'oauth.facebook.profile', 'list', array('options' => $profilesList, 'help' => true)),
			$settings->renderSetting('Facebook Import Avatar', 'oauth.facebook.registration.avatar', 'boolean', array('help' => true)),
			$settings->renderSetting('Facebook Import Cover', 'oauth.facebook.registration.cover', 'boolean', array('help' => true)),
			$settings->renderSetting('Facebook Allow Push', 'oauth.facebook.push', 'boolean', array('help' => true)),
			$settings->renderSetting('Facebook Username', 'oauth.facebook.username', 'list', array('options' => $facebookUsername, 'help' => true, 'info' => true))
			//,
			// $settings->renderSetting('Facebook Allow Pull', 'oauth.facebook.pull', 'boolean', array('help' => true)),
			// $settings->renderSetting('Facebook Import Total Timeline Posts', 'oauth.facebook.registration.totalTimeline', 'input', array('help' => true, 'class' => 'input-mini center'))
		)
	)
);
