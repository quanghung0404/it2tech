<?php
/**
 * @version    $Id: jsn_is_language.php 16077 2012-09-17 02:30:25Z giangnd $
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
 * language Class
 *
 * @package  JSN.ImageShow
 * @since    2.5
 *
 */

class JSNISLanguage
{
	private $_pluginLanguages 			= null;

	private $_adminSourcePath 			= '';

	private $_siteSourcePath 			= '';

	private $_adminPath 				= '';

	private $_sitePath 					= '';

	/**
	 * Signleton pattern
	 *
	 * @return a instance
	 */

	public static function getInstance()
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (empty($instances['JSNISLanguage']))
		{
			$instance	= new JSNISLanguage;
			$instances['JSNISLanguage'] = &$instance;
		}

		return $instances['JSNISLanguage'];
	}

	/**
	 * Contructor
	 */

	public function JSNISLanguage()
	{
		$this->_adminSourcePath 			= JPATH_COMPONENT_ADMINISTRATOR . DS . 'language' . DS . 'admin';
		$this->_siteSourcePath 				= JPATH_COMPONENT_ADMINISTRATOR . DS . 'language' . DS . 'site';
		$this->_adminPath 					= JPATH_ADMINISTRATOR . DS . 'language';
		$this->_sitePath 					= JPATH_SITE . DS . 'language';
		$this->_pluginLanguages				= $this->getPluginLanguages();
	}

	/**
	 * Install language one or many languages
	 *
	 * @param   array   $langs  the array of languages need to be installed
	 * @param   string  $area   the area to install language
	 *
	 * @return void
	 */

	public function install($langs, $area)
	{
		if ($area == 'site')
		{
			if (count($langs))
			{
				foreach ($langs as $lang)
				{
					$files = glob($this->_siteSourcePath . DS . $lang . DS . "{$lang}.*.ini");

					foreach ($files as $file)
					{
						copy($file, $this->_sitePath . DS . $lang . DS . basename($file));
					}
				}
			}

			//Install lang for sources and themes
			if (isset($this->_pluginLanguages['site']))
			{
				if (count($langs))
				{
					foreach ($langs as $lang)
					{
						foreach ($this->_pluginLanguages['site'] as $plugin)
						{
							$files = glob($plugin . DS . "{$lang}.*.ini");

							foreach ($files as $file)
							{
								copy($file, $this->_sitePath . DS . $lang . DS . basename($file));
							}
						}
					}
				}
			}
		}
		else
		{
			if (count($langs))
			{
				foreach ($langs as $lang)
				{
					$files = glob($this->_adminSourcePath . DS . $lang . DS . "{$lang}.*.ini");

					foreach ($files as $file)
					{
						copy($file, $this->_adminPath . DS . $lang . DS . basename($file));
					}
				}
			}

			//Install lang for sources and themes
			if (isset($this->_pluginLanguages['admin']))
			{
				if (count($langs))
				{
					foreach ($langs as $lang)
					{
						foreach ($this->_pluginLanguages['admin'] as $plugin)
						{
							$files = glob($plugin . DS . "{$lang}.*.ini");

							foreach ($files as $file)
							{
								copy($file, $this->_adminPath . DS . $lang . DS . basename($file));
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Get all supported languages of JSN ImageShow plugin, here is: theme and source
	 *
	 * @return array
	 */

	public function getPluginLanguages()
	{
		JPluginHelper::importPlugin('jsnimageshow');
		$dispatcher 	= JDispatcher::getInstance();
		$plugins 		= $dispatcher->trigger('getLanguageJSNPlugin');

		$languages		= array();
		foreach ($plugins as $plugin)
		{
			foreach ($plugin as $position => $language)
			{
				$languages [$position][$language['files'][0]] = (string) $language['path'][0];
			}
		}
		return $languages;
	}

	/**
	 * Check whether the SEO of website is enable or not
	 *
	 * @return string
	 */

	public function getFilterLangSystem()
	{
		$app 			= JFactory::getApplication();
		$router 		= $app->getRouter();
		$modeSef 		= ($router->getMode() == JROUTER_MODE_SEF) ? true : false;
		$languageFilter = $app->getLanguageFilter();
		$uri 			= JFactory::getURI();
		$langCode		= JLanguageHelper::getLanguages('lang_code');
		$langDefault	= JComponentHelper::getParams('com_languages')->get('site', 'en-GB');

		$realPath = 'index.php?';

		if ($languageFilter)
		{
			if (isset($langCode[$langDefault]))
			{
				if ($modeSef)
				{
					$realPath = '';
					$realPath .= JFactory::getConfig()->get('sef_rewrite') ? '' : 'index.php/';
					$realPath .= $langCode[$langDefault]->sef . '/?';
				}
				else
				{
					$realPath = 'index.php?lang=' . $uri->getVar('lang') . '%26';
				}
			}
		}

		return $realPath;
	}
}
