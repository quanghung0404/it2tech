<?php
/**
 * @version    $Id: maintenance.php 16077 2012-09-17 02:30:25Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 *  Model maintenance class of ImageShow
 *
 * @package  JSN.ImageShow
 * @since    2.5
 *
 */

jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.utilities.simplexml');
jimport('joomla.filesystem.file');

class ImageShowModelMaintenance extends JSNConfigModel
{
	/**
	 * Contructor
	 */

	public function __construct()
	{
		parent::__construct();
	}
}

