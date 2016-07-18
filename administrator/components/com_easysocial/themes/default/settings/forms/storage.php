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


$storageServices = array(
	$settings->makeOption('Local Server', 'joomla'),
	$settings->makeOption('Amazon S3', 'amazon'),
	'help' => true,
	'class' => 'form-control input-sm'
);

$amazonRegion = array(
	$settings->makeOption('US Standard', 'us'),
	$settings->makeOption('US West Oregon', 'us-west-2'),
	$settings->makeOption('US West Northern California', 'us-west-1'),
	$settings->makeOption('EU Frankfurt', 'eu-central-1'),
	$settings->makeOption('EU Ireland', 'eu-west-1'),
	$settings->makeOption('Asia Pacific Singapore', 'ap-southeast-1'),
	$settings->makeOption('Asia Pacific Sydney', 'ap-southeast-2'),
	$settings->makeOption('Asia Pacific Tokyo', 'ap-northeast-1'),
	$settings->makeOption('South America Sau Paulo', 'sa-east-1')
);

$amazonClass = array(
	$settings->makeOption('Standard storage', 'standard'),
	$settings->makeOption('Reduced redundancy', 'reduced')
);

echo $settings->renderTabs(array(
	'general' => $settings->renderPage(
		$settings->renderColumn(
			$settings->renderSection(
				$settings->renderHeader('Storage', 'Storage Info'),
				$settings->renderSetting('Avatars', 'storage.avatars', 'list', $storageServices),
				$settings->renderSetting('Files', 'storage.files', 'list', $storageServices),
				$settings->renderSetting('Photos', 'storage.photos', 'list', $storageServices),
				$settings->renderSetting('Videos', 'storage.videos', 'list', $storageServices),
				$settings->renderSetting('Images From Links', 'storage.links', 'list', $storageServices)
			)
		)
	),
	'amazon' => $settings->renderPage(
		$settings->renderColumn(
			$settings->renderSection(
				$settings->renderHeader('Amazon S3', 'Amazon S3 Info'),
				$settings->renderSetting('Amazon access key', 'storage.amazon.access', 'input', array('class' => 'form-control input-sm', 'help' => true)),
				$settings->renderSetting('Amazon secret key', 'storage.amazon.secret', 'input', array('class' => 'form-control input-sm', 'help' => true)),
				$settings->renderSetting('Amazon bucket path', 'storage.amazon.bucket', 'input', array('help' => true, 'class' => 'form-control input-sm', 'unit' => true)),
				$settings->renderSetting('Amazon SSL', 'storage.amazon.ssl', 'boolean', array('help' => true)),
				$settings->renderSetting('Delete Files After Upload', 'storage.amazon.delete', 'boolean', array('help' => true)),
				$settings->renderSetting('Amazon transfer limit', 'storage.amazon.limit', 'input', array('class' => 'form-control input-sm', 'help' => true, 'class' => 'form-control input-sm input-short text-center', 'unit' => true)),
				$settings->renderSetting('Amazon storage region', 'storage.amazon.region', 'list', array('class' => 'form-control input-sm', 'options' => $amazonRegion, 'help' => true)),
				$settings->renderSetting('Amazon storage class', 'storage.amazon.class', 'list', array('class' => 'form-control input-sm', 'options' => $amazonClass, 'help' => true, 'info' => true))
			)
		)
	)
	//, 'rackspace' => $settings->renderPage(
	// 	$settings->renderColumn(
	// 		$settings->renderSection($this->includeTemplate('admin/settings/forms/storage.rackspace.info')),
	// 		$settings->renderSection(
	// 			$settings->renderHeader('Rackspace'),
	// 			$settings->renderSetting('Rackspace username', 'storage.rackspace.username', 'input', array('class' => 'form-control input-sm', 'help' => true)),
	// 			$settings->renderSetting('Rackspace access key', 'storage.rackspace.api', 'input', array('class' => 'form-control input-sm', 'help' => true)),
	// 			$settings->renderSetting('Rackspace CDN URL', 'storage.rackspace.url', 'input', array('class' => 'form-control input-sm', 'help' => true)),
	// 			$settings->renderSetting('Rackspace container', 'storage.rackspace.container', 'input', array('class' => 'form-control input-sm', 'help' => true))
	// 		)
	// 	)
	//)
));
