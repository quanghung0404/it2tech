<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_jslanguages.php 15627 2012-08-29 08:57:27Z haonv $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
class JSNISJSLanguages
{
	//Array store all lang
	private $_langs;

	//Loaded lang to document
	private $_loaded;
	/**
	 *
	 * Get instance
	 *
	 * @param Array $params
	 */
	public static function getInstance()
	{
		static $jsnisinstances;
		if (!isset($instances))
		{
			$jsnisinstances = array();
		}

		if (empty($jsnisinstances['JSNISJSLanguages']))
		{
			$instance	= new JSNISJSLanguages();
			$instance->_langs = array();
			$instance->_loaded= false;
			$jsnisinstances['JSNISJSLanguages'] = &$instance;
			//Load lang file
			$lang = JFactory::getLanguage();
			$lang->load('com_imageshow');
		}

		return $jsnisinstances['JSNISJSLanguages'];
	}

	/**
	 *
	 * Add to array
	 *
	 * @param String $key
	 */
	public function addLang( $key )
	{
		if (!is_array($this->_langs))
		{
			$this->_langs = array();
		}

		if (!key_exists($key, $this->_langs))
		{
			$this->_langs[$key] = "JSNISLang.add('".$key."', '".JText::_($key, true)."');";
		}
	}
	/**
	 *
	 * Load all JS lang to array
	 *
	 * @return: String js command lines add lang
	 */
	public function loadLang()
	{
		$this->addLang('ON_UPPERCASE');
		$this->addLang('OFF_UPPERCASE');
		$this->addLang('SYNC_UPPERCASE');
		$this->addLang('SHOWLIST_IMAGE_LOAD_MORE_IMAGES');
		$this->addLang('SHOWLIST_IMAGE_LOADING');
		$this->addLang('SHOWLIST_NOTICE_IN_SYNC_MODE');
		$this->addLang('SHOWLIST_NOTICE_DRAG_AND_DROP');
		$this->addLang('SHOWLIST_NOTICE_IMAGES_ARE_SYNCED');
		$this->addLang('SHOWLIST_LOGIN');
		$this->addLang('SHOWLIST_DISALBE_SAVE_BUTTON');
		$this->addLang('SHOWLIST_EDIT_IMAGE_HEADER');
		if (!$this->_loaded)
		{
			$this->_loaded = true;
			return PHP_EOL.implode(PHP_EOL, $this->_langs).PHP_EOL;
		}

		return;
	}
}
