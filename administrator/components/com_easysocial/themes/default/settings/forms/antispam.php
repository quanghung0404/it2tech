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

echo $settings->renderTabs(array(
	'recaptcha'	=> $settings->renderPage(
		$settings->renderColumn(
			$settings->renderSection(
				$this->includeTemplate('admin/settings/forms/antispam.recaptcha.info')
			)
		),
		$settings->renderColumn(
			$settings->renderSection(
				$settings->renderHeader('Recaptcha'),
				$settings->renderSetting('Recaptcha Public Key', 'antispam.recaptcha.public', 'input', array('info' => true)),
				$settings->renderSetting('Recaptcha Private Key', 'antispam.recaptcha.private', 'input', array('info' => true))
			)
		)
	),
	'akismet'	=> $settings->renderPage(
		$settings->renderColumn(
			$settings->renderSection(
				$this->includeTemplate('admin/settings/forms/antispam.akismet.info')
			),
			$settings->renderSection(
				$settings->renderHeader('Akismet'),
				$settings->renderSetting('Akismet API Key', 'antispam.akismet.key', 'input', array('info' => true))
			)
		)
	),
	'mollom'	=> $settings->renderPage(
		$settings->renderColumn(
			$settings->renderSection(
				$this->includeTemplate('admin/settings/forms/antispam.mollom.info')
			),
			$settings->renderSection(
				$settings->renderHeader('Mollom'),
				$settings->renderSetting('Mollom Public Key', 'antispam.mollom.public', 'input', array('info' => true)),
				$settings->renderSetting('Mollom Private Key', 'antispam.mollom.private', 'input', array('info' => true)),
				$settings->renderSetting('Mollom Servers', 'antispam.mollom.server', 'input')
			)
		)
	)
));
