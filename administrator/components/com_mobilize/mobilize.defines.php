<?php
/**
 * 1.2.0     $Id: sample.defines.php 17400 2012-10-24 10:20:53Z cuongnm $
 * @package     JSNSample
 * @subpackage  item
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

define('JSN_MOBILIZE_DEPENDENCY', '[{"type":"template","dir":"templates\/jsn_mobilize","name":"jsn_mobilize","client":"site","publish":"0","lock":"1","title":"jsn_mobilize","params":"{\"mobilize\":\"mobilize\"}"},{"type":"plugin","folder":"system","name":"jsnframework","identified_name":"ext_framework","title":"JSN Extension Framework System Plugin","client":"site","publish":"1","lock":"1"},{"type":"plugin","folder":"system","dir":"plugins\/system\/jsnmobilize","name":"jsnmobilize","publish":"1","client":"site","lock":"1","title":"jsnmobilize","params":"{\"mobilize\":\"mobilize\"}"}]');

define('JSN_MOBILIZE_DEPENDENCY', '[{"type":"template","dir":"templates\/jsn_mobilize","name":"jsn_mobilize","client":"site","publish":"0","lock":"1","title":"jsn_mobilize","params":null},{"type":"plugin","folder":"system","name":"jsnframework","identified_name":"ext_framework","title":"JSN Extension Framework System Plugin","client":"site","publish":"1","lock":"1"},{"type":"plugin","folder":"system","dir":"plugins\/system\/jsnmobilize","name":"jsnmobilize","publish":"1","client":"site","lock":"1","title":"jsnmobilize","params":null}]');
// Only define below constant if product has multiple edition
define('JSN_MOBILIZE_EDITION', 'PRO_UNLIMITED');

// Define product identified name and version
define('JSN_MOBILIZE_IDENTIFIED_NAME',	'ext_mobilize');
define('JSN_MOBILIZE_VERSION',			'1.2.0');
// Define required Joomla version
define('JSN_MOBILIZE_REQUIRED_JOOMLA_VER', '3.0');

// Define some necessary links
define('JSN_MOBILIZE', 'com_mobilize');
define('JSN_MOBILIZE_INFO_LINK',		'http://www.joomlashine.com/joomla-extensions/jsn-mobilize.html');
define('JSN_MOBILIZE_DOC_LINK',		'http://www.joomlashine.com/joomla-extensions/jsn-mobilize-docs.zip');
define('JSN_MOBILIZE_REVIEW_LINK',	'http://www.joomlashine.com/joomla-extensions/jsn-mobilize-on-jed.html');
define('JSN_MOBILIZE_UPDATE_LINK',	'index.php?option=com_mobilize&view=update');
define('JSN_MOBILIZE_ASSETS_URL', JURI::root(true) . '/administrator/components/com_mobilize/assets/');
// If product has multiple edition, define upgrade link also
define('JSN_MOBILIZE_UPGRADE_LINK',	'index.php?option=com_mobilize&view=upgrade');

// If product is commercial, define buy link too
define('JSN_MOBILIZE_BUY_LINK',		'http://www.joomlashine.com/joomla-extensions/jsn-mobilize-buy-now.html');
