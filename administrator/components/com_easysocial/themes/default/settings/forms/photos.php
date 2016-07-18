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

// Photo quality
$photoQuality	= array();

for($i = 0; $i <= 100; $i += 10)
{
	$message	= $i;
	$message	= $i == 0 ? JText::sprintf('COM_EASYSOCIAL_PHOTOS_SETTINGS_UPLOAD_QUALITY_LOW', $i) : $message;
	$message	= $i == 50 ? JText::sprintf('COM_EASYSOCIAL_PHOTOS_SETTINGS_UPLOAD_QUALITY_MEDIUM', $i) : $message;
	$message	= $i == 100 ? JText::sprintf('COM_EASYSOCIAL_PHOTOS_SETTINGS_UPLOAD_QUALITY_HIGH', $i) : $message;

	$photoQuality[]	= array('text' => $message, 'value' => $i);
}

echo $settings->renderTabs(array(
		'general' => $settings->renderPage(
						$settings->renderColumn(
							$settings->renderSection(
								$settings->renderHeader('General'),
								$settings->renderSetting('Enable Photos', 'photos.enabled', 'boolean', array('help' => true)),
								$settings->renderSetting('Photo Pagination', 'photos.pagination.photo', 'input', array('help' => true, 'class' => 'form-control input-sm input-short text-center', 'unit' => true)),
								$settings->renderSetting('Default Photo Popup', 'photos.popup.default', 'boolean', array('help' => true)),
								$settings->renderSetting('Import EXIF Data', 'photos.import.exif', 'boolean', array('help' => true)),
								$settings->renderSetting('Allow Downloads', 'photos.downloads', 'boolean', array('help' => true)),
								$settings->renderSetting('Allow View Original', 'photos.original', 'boolean', array('help' => true))
							)
						)
		),
		'layout' => $settings->renderPage(
						$settings->renderColumn(
							$settings->renderSection(
								$settings->renderHeader('Layout'),

								$settings->renderSetting('Size', 'photos.layout.size', 'list', array('options' =>
									array(
										array('text' => JText::_('COM_EASYSOCIAL_PHOTOS_SETTINGS_SIZE_LARGE'), 'value' => 'large'),
										array('text' => JText::_('COM_EASYSOCIAL_PHOTOS_SETTINGS_SIZE_MEDIUM'), 'value' => 'featured'),
										array('text' => JText::_('COM_EASYSOCIAL_PHOTOS_SETTINGS_SIZE_SMALL'), 'value' => 'thumbnail')
									), 'help' => true, 'info' => true, 'class' => 'form-control input-sm input-medium')),

								$settings->renderSetting('Pattern', 'photos.layout.pattern', 'list', array('options' =>
									array(
										array('text' => JText::_('COM_EASYSOCIAL_PHOTOS_SETTINGS_PATTERN_TILE'), 'value' => 'tile'),
										array('text' => JText::_('COM_EASYSOCIAL_PHOTOS_SETTINGS_PATTERN_FLOW'), 'value' => 'flow')
									), 'help' => true, 'info' => true, 'class' => 'form-control input-sm input-medium')),

								$settings->renderSetting('Aspect Ratio', 'photos.layout.ratio', 'list', array('options' =>
									array(
										array('text' => '4:3', 'value' => '4x3'),
										array('text' => '16:9', 'value' => '16x9'),
										array('text' => '1:1', 'value' => '1x1')
									), 'help' => true, 'info' => true, 'class' => 'form-control input-sm input-medium')),

								$settings->renderSetting('Resize Mode', 'photos.layout.mode', 'list', array('options' =>
									array(
										array('text' => JText::_('COM_EASYSOCIAL_PHOTOS_SETTINGS_RESIZE_MODE_STRETCH_TO_FILL'), 'value' => 'cover'),
										array('text' => JText::_('COM_EASYSOCIAL_PHOTOS_SETTINGS_RESIZE_MODE_STRETCH_TO_FIT'), 'value' => 'contain')
									), 'help' => true, 'info' => true, 'class' => 'form-control input-sm input-medium')),

								$settings->renderSetting('Resize Threshold', 'photos.layout.threshold', 'input', array('help' => true, 'class' => 'form-control input-sm input-short text-center', 'unit' => true, 'info' => true))
							)
						)
		),
		'uploader' => $settings->renderPage(
						$settings->renderColumn(
							$settings->renderSection(
								$settings->renderHeader('Uploader'),
								$settings->renderSetting('Photo Quality', 'photos.quality', 'list', array('options' => $photoQuality, 'help' => true, 'info' => true, 'class' => 'form-control input-sm input-medium')),
								$settings->renderSetting('Upload Limit', 'photos.uploader.maxsize', 'input', array('help' => true, 'class' => 'form-control input-sm input-short', 'unit' => true))
							)
						)
		)
	)
);