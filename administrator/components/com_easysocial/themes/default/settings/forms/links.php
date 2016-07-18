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
			$settings->renderHeader('General', 'GENERAL_INFO'),
			$settings->renderSetting('CHECK_LINK_BEFORE_PARSING', 'links.parser.validate', 'boolean', array('help' => true))
		)
    ),
    $settings->renderColumn(
        $settings->renderSection(
            $settings->renderHeader('URL Caching', 'CACHING_INFO'),
            $settings->renderSetting('Cache Shared Images', 'links.cache.images', 'boolean', array('help' => true)),
            $settings->renderSetting('Cache Location', 'links.cache.location', 'input', array('help' => true, 'class' => 'form-control input-sm')),
            $settings->renderSetting('Automatically purge cached urls', 'general.url.purge', 'boolean', array('help' => true)),
            $settings->renderSetting('Purge interval', 'general.url.interval', 'input', array('help' => true, 'class' => 'form-control input-sm input-short text-center', 'unit' => true))
        )
	)
);
