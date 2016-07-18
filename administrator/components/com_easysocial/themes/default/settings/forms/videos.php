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

$availableAudioBitrates = array('32k', '64k', '96k', '192k', '224k');
$audioBitrates = array();

foreach ($availableAudioBitrates as $audioBitrate) {
	$audioBitrates[] = $settings->makeOption($audioBitrate, $audioBitrate);
}

$availableSizes = array('1080', '720', '480');
$sizes = array();

foreach ($availableSizes as $size) {
	$sizes[] = $settings->makeOption($size, $size);
}

// Check if ffmpeg path really exists
$ffmpeg = $this->config->get('video.ffmpeg');
$displayUploadSettings = false;

if (JFile::exists($ffmpeg)) {
	$column = $settings->renderColumn(
				$settings->renderSection(
					$settings->renderHeader('Encoding', 'Encoding Info'),
					$settings->renderSetting('FFMpeg Path', 'video.ffmpeg', 'input', array('help' => true, 'info' => true, 'class' => 'form-control input-sm')),
					$settings->renderSetting('Delete Processed Videos', 'video.delete', 'boolean', array('help' => true, 'rowAttributes' => 'data-video-upload-settings')),
					$settings->renderSetting('Video Size', 'video.size', 'list', array('options' => $sizes, 'help' => true, 'rowAttributes' => 'data-video-upload-settings')),
					$settings->renderSetting('Encode Video After Upload', 'video.autoencode', 'boolean', array('help' => true, 'rowAttributes' => 'data-video-upload-settings')),
					$settings->renderSetting('Maximum Audio Bitrate', 'video.audiobitrate', 'list', array('help' => true, 'options' => $audioBitrates, 'rowAttributes' => 'data-video-upload-settings'))
				)
			);
} else {
	$column = $settings->renderColumn(
				$settings->renderSection(
					$settings->renderHeader('Encoding', 'Encoding Info'),
					$settings->renderSetting('FFMpeg Path', 'video.ffmpeg', 'input', array('help' => true, 'info' => true, 'class' => 'form-control input-sm')),
					$settings->renderSetting('Allow Video Uploads', 'info', 'text', array('text' => JText::_('COM_EASYSOCIAL_SETTINGS_VIDEOS_UPLOAD_ONLY_AVAILABLE_AFTER_FFMPEG_IS_AVAILABLE')))
				)
			);
}


echo $settings->renderTabs(array(
	'general' => $settings->renderPage(
		$settings->renderColumn(
			$settings->renderSection(
				$settings->renderHeader('General', 'General Info'),
				$settings->renderSetting('Enable Videos', 'video.enabled', 'boolean', array('help' => true)),
				$settings->renderSetting('Allow Video Uploads', 'video.uploads', 'boolean', array('help' => true, 'rowAttributes' => 'data-video-upload-settings')),
				$settings->renderSetting('Allow Video Embeds', 'video.embeds', 'boolean', array('help' => true, 'rowAttributes' => 'data-video-upload-settings')),
				$settings->renderSetting('Storage Path', 'video.storage.container', 'input', array('help' => true, 'info' => true, 'class' => 'form-control input-sm'))
			)
		), 
		$column
	),
	'layout' => $settings->renderPage(
		$settings->renderColumn(
			$settings->renderSection(
				$settings->renderHeader('Item Layout General', 'Item Layout General Info'),
				$settings->renderSetting('Display Recent Videos', 'video.layout.item.recent', 'boolean', array('help' => true)),
				$settings->renderSetting('Total Other Videos', 'video.layout.item.total', 'input', array('help' => true, 'unit' => true, 'class' => 'form-control input-sm input-short text-center')),
				$settings->renderSetting('Display Video Hits', 'video.layout.item.hits', 'boolean', array('help' => true)),
				$settings->renderSetting('Display Video Duration', 'video.layout.item.duration', 'boolean', array('help' => true)),
				$settings->renderSetting('Display Video Tags', 'video.layout.item.tags', 'boolean', array('help' => true))
			)
		)
	)
));
