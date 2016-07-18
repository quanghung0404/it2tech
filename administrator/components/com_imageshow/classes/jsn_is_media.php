<?php
/**
 * @version    $Id: jsn_is_media.php 16077 2012-09-17 02:30:25Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Media Class
 *
 * @package  JSN.ImageShow
 * @since    2.5
 *
 */

class JSNISMedia
{
	/**
	 * The instance of JDocumnent class
	 *
	 * @var    object
	 */

	private $_document;

	/**
	 * Constructor
	 */

	public function __construct()
	{
		$this->_document = JFactory::getDocument();
	}

	/**
	 * Get Instance
	 *
	 * @return Ambigous <JSNMedia>
	 */

	public static function getInstance()
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}
		if (empty($instances['JSNISMedia']))
		{
			$instance	= new JSNMedia;
			$instances['JSNISMedia'] = &$instance;
		}

		return $instances['JSNISMedia'];
	}

	/**
	 * Queue store script file to array
	 *
	 * @param   string  $path  The path of file.
	 *
	 * @return	void
	 */

	public function addScript( $path )
	{
		$objUtils	  		= JSNISFactory::getObj('classes.jsn_is_utils');
		$currentVersion		= $objUtils->getVersion();
		$path				.= '?v=' . $currentVersion;
		$this->_document->addScript($path);
	}

	/**
	 * Queue store script file to array
	 *
	 * @param   string  $path  The path of file.
	 *
	 * @return	void
	 */

	public function addStyleSheet($path)
	{
		$objUtils	  		= JSNISFactory::getObj('classes.jsn_is_utils');
		$currentVersion		= $objUtils->getVersion();
		$path				.= '?v=' . $currentVersion;
		$this->_document->addStyleSheet($path);
	}

	/**
	 * Queue store script file to array
	 *
	 * @param   string  $str  The string added to page head.
	 *
	 * @return	void
	 */

	public function addStyleDeclaration($str)
	{
		$this->_document->addStyleDeclaration($str);
	}

	/**
	 * Queue store script file to array
	 *
	 * @param   string  $str  The string added to page head.
	 *
	 * @return	void
	 */

	public function addScriptDeclaration($str)
	{
		$this->_document->addScriptDeclaration($str);
	}
}
