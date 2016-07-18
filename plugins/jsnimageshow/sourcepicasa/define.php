<?php
/**
 * @version    $Id: imageshow.php 16609 2012-10-02 09:23:05Z haonv $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$jsnImageSourcePicasa = array(
	'name' => 'Picasa',
	'identified_name' => 'picasa',
	'type' => 'external',
	'description' => 'Picasa Description',
	'thumb' => 'plugins/jsnimageshow/sourcepicasa/assets/images/thumb-picasa.png',
	'sync'	=> true,
	'pagination' => true
);

define('JSN_IS_SOURCEPICASA', json_encode($jsnImageSourcePicasa));
