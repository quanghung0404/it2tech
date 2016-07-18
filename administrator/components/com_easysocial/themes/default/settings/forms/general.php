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

	// Prepare all the options here
	$dstOptions = array();
	for($i = -4 ; $i <= 4; $i++) {
		$dstOptions[] = array('text' => $i . ' ' . JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_DAYLIGHT_SAVING_OFFSET_HOURS'), 'value' => $i);
	}

	$processEmailText = $settings->renderSettingText('Send Email on page load', 'info') . ' <a href="http://stackideas.com/docs/easysocial/administrators/cronjobs/cronjobs">' . $settings->renderSettingText('Send Email on page load', 'learn more') . '</a>';

	$cronUrl	= '';

	if($this->config->get('general.cron.key') )
	{
		$url 		= JURI::root() . 'index.php?option=com_easysocial&cron=true&phrase=' . $this->config->get('general.cron.key');

		$cronUrl	= '<br />' . JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_CRON_URL') . ':';

		$cronUrl 	.= '<input type="text" class="form-control input-sm" value="' . $url . '" />';
	}

	// Prepare all the options here
	$dstOptions = array();
	for($i = -4 ; $i <= 4; $i++) {
		$dstOptions[] = array('text' => $i . ' ' . JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_DAYLIGHT_SAVING_OFFSET_HOURS'), 'value' => $i);
	}

	ob_start(); ?>
	<p class="mt-20"><b><?php echo JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_ENVIRONMENT_PRODUCTION'); ?></b> - <?php echo JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_ENVIRONMENT_PRODUCTION_DESC'); ?></p>
	<p><b><?php echo JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_ENVIRONMENT_DEVELOPMENT'); ?></b> - <?php echo JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_ENVIRONMENT_DEVELOPMENT_DESC'); ?></p>
	<?php
	$environmentInfo = ob_get_contents();
	ob_end_clean();

	$envOptions = array(
		$settings->makeOption('Environment Development', 'development'),
		$settings->makeoption('Environment Production', 'static'),
		'help' => true,
		'info' => $environmentInfo,
		'class' => 'form-control input-sm'
	);

	ob_start(); ?>
	<p class="mt-20"><b><?php echo JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_COMPRESSION_COMPRESSED'); ?></b> - <?php echo JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_COMPRESSION_COMPRESSED_DESC'); ?></p>
	<p><b><?php echo JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_COMPRESSION_UNCOMPRESSED'); ?></b> - <?php echo JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_COMPRESSION_UNCOMPRESSED_DESC'); ?></p>
	<p><small><?php echo JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_COMPRESSION_UNCOMPRESSED_INFO'); ?></small></p>
	<?php
	$compressInfo = ob_get_contents();
	ob_end_clean();

	$compressOptions = array(
		$settings->makeOption('Compression Compressed', 'compressed'),
		$settings->makeoption('Compression Uncompressed', 'uncompressed'),
		'help' => true,
		'info' => $compressInfo,
		'class' => 'form-control input-sm'
	);

	ob_start(); ?>
	<p class="mt-20"><b><?php echo JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_COMPRESSION_COMPRESSED'); ?></b> - <?php echo JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_COMPRESSION_COMPRESSED_DESC'); ?></p>
	<p><b><?php echo JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_COMPRESSION_UNCOMPRESSED'); ?></b> - <?php echo JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_COMPRESSION_UNCOMPRESSED_DESC'); ?></p>
	<p><small><?php echo JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_COMPRESSION_UNCOMPRESSED_INFO'); ?></small></p>
	<?php
	$tempTableModeInfo = ob_get_contents();
	ob_end_clean();

	$temporatyTableOptions = array(
		$settings->makeOption('Memory Storage', 'memory'),
		$settings->makeoption('MyISAM Storage', 'myisam'),
		'help' => true,
		'info' => $tempTableModeInfo,
		'class' => 'form-control input-sm'
	);

echo $settings->renderTabs(array(
		'general'	=> $settings->renderPage(
							$settings->renderColumn(
								$settings->renderSection(
									$settings->renderHeader('Lockdown Mode', 'Lockdown Mode Info'),
									$settings->renderSetting('Enable Lockdown Mode', 'general.site.lockdown.enabled', 'boolean', array('help' => true)),
									$settings->renderSetting('Allow Registrations in Lockdown Mode', 'general.site.lockdown.registration', 'boolean', array('help' => true))
								),
								$settings->renderSection(
									$settings->renderHeader('System Settings', 'System Settings Info'),
									$settings->renderSetting('Use Index For AJAX URLs', 'general.ajaxindex', 'boolean', array('help' => true, 'info' => true)),
									$settings->renderSetting('API Key', 'general.key', 'input', array('help' => true, 'class' => 'input-sm form-control')),
									$settings->renderSetting('Environment', 'general.environment', 'list', $envOptions),
									$settings->renderSetting('Javascript Compression', 'general.mode', 'list', $compressOptions),
									$settings->renderSetting('Inline Configuration', 'general.inline', 'boolean', array(
										'help' => true,
										'info' => '<p class="mt-20">' . JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_INLINE_CONFIGURATION_INFO') . '</p>'
									)),
									$settings->renderSetting('Profiler', 'general.profiler', 'boolean', array('help' => true))
								)
							),
							$settings->renderColumn(
								$settings->renderSection(
									$settings->renderHeader('CDN Settings'),
									$settings->renderSettingText('CDN Info'),
									$settings->renderSetting('Enable CDN', 'general.cdn.enabled', 'boolean', array('help' => true)),
									$settings->renderSetting('CDN Url', 'general.cdn.url', 'input', array('help' => true, 'class' => 'input-sm form-control')),
									$settings->renderSetting('Passive CDN', 'general.cdn.passive', 'boolean', array(
										'help' => true,
										'info' => '<p class="mt-20">' . JText::_('COM_EASYBLOG_SETTINGS_CDN_PASSIVE_CDN_INFO') . '</p>'
									))
								)
							)
							// $settings->renderColumn(
							// 	$settings->renderSection(
							// 		$settings->renderHeader('Optimization'),
							// 		$settings->renderSettingText('Optimization Info'),
							// 		$settings->renderSetting('Use Temporary Table', 'general.optimize.temporary', 'boolean', array(
							// 			'help' => true,
							// 			'info' => '<p class="mt-20">' . JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_USE_TEMPORARY_TABLE_INFO') . '</p>'
							// 		)),
							// 		$settings->renderSetting('Temporary Table Engine', 'general.optimize.temporary.engine', 'list', $temporatyTableOptions)
							// 	)
							// )
		),
		'friends' => $settings->renderPage(
						$settings->renderColumn(
							$settings->renderSection(
								$settings->renderHeader('Friend Lists', 'Friend Lists Help'),
								$settings->renderSetting('Enable Friend Lists', 'friends.list.enabled', 'boolean', array('help' => true)),
					            $settings->renderSetting('Allow Friend Invites', 'friends.invites.enabled', 'boolean', array('help' => true)),
								$settings->renderSetting('Show Empty Lists On Dashboard', 'friends.list.showEmpty', 'boolean', array('help' => true))
							)
						)
		),
		'followers' => $settings->renderPage(
							$settings->renderColumn(
								$settings->renderSection(
									$settings->renderHeader('General'),
									$settings->renderSetting('Enable Followers System', 'followers.enabled', 'boolean', array('help' => true))
								)
							)
		),
		'apps' => $settings->renderPage(
					$settings->renderColumn(
						$settings->renderSection(
							$settings->renderHeader('Terms and conditions', 'Terms and conditions help'),
							$settings->renderSetting('Application browser', 'apps.browser', 'boolean', array('help' => true)),
							$settings->renderSetting('Require acceptence of terms', 'apps.tnc.required', 'boolean', array('help' => true)),
							$settings->renderSetting('Terms and conditions message', 'apps.tnc.message', 'textarea', array('help' => true, 'class' => "form-control input-sm", 'translate' => true))
						)
					)
		),
		'emails' => $settings->renderPage(
						$settings->renderColumn(
								$settings->renderSection(
									$settings->renderHeader('Cronjob Settings', 'Cronjob Settings Info'),
									$settings->renderSetting('Enable Secure Cron Url', 'general.cron.secure', 'boolean', array('help' => true, 'info' => true)),
									$settings->renderSetting('Secure Cron Key', 'general.cron.key', 'input', array('help' => true, 'info' => true, 'custom' => $cronUrl, 'class' => 'input-sm')),
									$settings->renderSetting('Number Of Emails', 'general.cron.limit', 'input', array('help' => true, 'info' => true, 'class' => 'form-control input-sm input-short text-center', 'unit' => true))
								)
						),
						$settings->renderColumn(
								$settings->renderSection(
									$settings->renderHeader('Transporter Behaviour', 'Transporter Behaviour Info'),
									$settings->renderSetting('Send Email on page load', 'email.pageload', 'boolean', array('help' => true, 'info' => $processEmailText)),
									$settings->renderSetting('Sender name', 'email.sender.name', 'input', array('help' => true, 'class' => 'form-control input-sm', 'default' => $this->jConfig->getValue('fromname'))),
									$settings->renderSetting('Sender Email address', 'email.sender.email', 'input', array('help' => true,  'class' => 'form-control input-sm','default' => $this->jConfig->getValue('mailfrom'))),
									$settings->renderSetting('Reply to Email address', 'email.replyto', 'input', array('help' => true,  'class' => 'form-control input-sm','default' => $this->jConfig->getValue('mailfrom')))
								)
						)
		),
		'achievements' => $settings->renderPage(
								$settings->renderColumn(
									$settings->renderSection(
										$settings->renderHeader('General'),
										$settings->renderSetting('Enable Achievement System', 'badges.enabled', 'boolean', array('help' => true))
									)
								)
		),
		'points' => $settings->renderPage(
						$settings->renderColumn(
							$settings->renderSection(
								$settings->renderHeader('General', 'Points General Help'),
								$settings->renderSetting('Enable Points', 'points.enabled', 'boolean', array('help' => true))
							)
						)
		)
	)
);
