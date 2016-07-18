<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: images_sources.php 8418 2011-09-22 08:18:02Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
abstract class JSNImagesSources
{
	public  $_error       = false;
	public  $_errorMsg;
	protected  $_data     = array();
	public $_source 	  = array();

	public function __construct($config = array())
	{
		$this->_db 		= JFactory::getDBO();
		$this->_source 	= $config;
	}

	public function getData() {
		return $this->_data;
	}

	public function getError($config = array()) {
		return $this->_error;
	}

	public function getErrorMsg() {
		return $this->_errorMsg;
	}

	abstract public function getCategories($config = array());

	abstract public function updateImages($config = array());

	abstract public function onSelectSource($config = array());

	abstract public function getProfileTitle();

	abstract public function loadImages($config = array());

	abstract public function addOriginalInfo();

	abstract function getImageSrc($config = array('image_big' => '', 'URL' => ''));

	abstract public function removeAllImages($config = array());

	abstract public function removeShowlist();
}
