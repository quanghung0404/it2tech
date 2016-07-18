<?php
/**
 * 2.1.3    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

define('JSN_EASYSLIDER_DEPENDENCY', '[{"type":"plugin","folder":"system","name":"easyslider","dir":"plugins\/system\/easyslider","client":"site","publish":"1","lock":"1","title":"JSN EasySlider System Plugin","params":null},{"type":"plugin","folder":"system","name":"jsnframework","identified_name":"ext_framework","client":"site","publish":"1","lock":"1","title":"JSN Extension Framework System Plugin"},{"type":"module","folder":"site","dir":"site\/modules\/mod_easyslider","name":"mod_easyslider","client":"site","publish":"1","lock":"1","title":"mod_easyslider","params":null},{"type":"plugin","folder":"editors-xtd","client":"site","dir":"plugins\/editors-xtd\/jsneasyslider","name":"jsneasyslider","publish":"1","lock":"1","title":"jsneasyslider","params":null},{"type":"plugin","folder":"content","client":"site","dir":"plugins\/content\/jsneasyslider","name":"jsneasyslider","publish":"1","lock":"1","title":"jsneasyslider","params":null}]');

// Define product identified name and version
define('JSN_EASYSLIDER_IDENTIFIED_NAME',	'ext_easyslider');
define('JSN_EASYSLIDER_VERSION',			'2.1.3');

// Define required Joomla version
define('JSN_EASYSLIDER_REQUIRED_JOOMLA_VER', '3.0');

// Only define below constant if product has multiple edition
 define('JSN_EASYSLIDER_EDITION', 'PRO UNLIMITED');

// Define some necessary links
define('JSN_EASYSLIDER_INFO_LINK',		'http://www.joomlashine.com/joomla-extensions/jsn-easyslider.html');
define('JSN_EASYSLIDER_DOC_LINK',		'http://www.joomlashine.com/joomla-extensions/jsn-easyslider-docs.zip');
define('JSN_EASYSLIDER_REVIEW_LINK',	'http://www.joomlashine.com/joomla-extensions/jsn-easyslider-on-jed.html');
define('JSN_EASYSLIDER_BUY_LINK', 'http://www.joomlashine.com/joomla-extensions/jsn-easyslider-joomla-slider-extension.html');
define('JSN_EASYSLIDER_UPDATE_LINK',	'index.php?option=com_easyslider&view=update');

// If product has multiple edition, define upgrade link also
define('JSN_EASYSLIDER_UPGRADE_LINK',	'index.php?option=com_easyslider&view=upgrade');

//define below constant

define('JSNES_ADMIN_PATH', JPATH_ROOT . '/administrator/components/com_easyslider/');
define('JSNES_ASSETS_URL', JURI::root(true) . '/administrator/components/com_easyslider/assets/');
define('JSNES_ASSETS_PATH', JSNES_ADMIN_PATH . 'assets/');
define('JSNES_PLG_SYSTEM_ASSETS_PATH', JPATH_ROOT . '/plugins/system/easyslider/assets/');
define('JSNES_PLG_SYSTEM_ASSETS_URL', JURI::root(true) . '/plugins/system/easyslider/assets/');
define('JSNES_PLG_JSNFRAMEWORK_SYSTEM_ASSETS_URL', JURI::root(true) . '/plugins/system/jsnframework/assets/');

define('JSNES_UPLOAD_IMAGES_PATH', JPATH_ROOT . '/images/jsn_easyslider/');
define('JSNES_UPLOAD_IMAGES_URL', '/images/jsn_easyslider/');

define('JSNES_COPY_IMAGES_PLUGIN_PATH', JPATH_ROOT . '/plugins/jsneasyslider/template/data/images/');
define('JSNES_COPY_IMAGES_PLUGIN_URL', '/plugins/jsneasyslider/template/data/images/');

define('JSNES_IMAGES_PATH', JPATH_ROOT . '/images');
define('JSNES_IMAGES_URL', '/images/');