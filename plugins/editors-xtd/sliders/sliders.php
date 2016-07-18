<?php
/**
 * @package         Sliders
 * @version         5.1.11PRO
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * Button Plugin that places Editor Buttons
 */
class PlgButtonSliders extends JPlugin
{
	private $_alias = 'sliders';

	private $_init = false;
	private $_helper = null;

	/**
	 * Display the button
	 *
	 * @return array  A two element array of ( imageName, textToInsert )
	 */
	function onDisplay($name)
	{
		if (!$this->getHelper())
		{
			return;
		}

		return $this->_helper->render($name);
	}

	/*
	 * Below methods are general functions used in most of the NoNumber extensions
	 * The reason these are not placed in the NoNumber Framework files is that they also
	 * need to be used when the NoNumber Framework is not installed
	 */

	/**
	 * Create the helper object
	 *
	 * @return object The plugins helper object
	 */
	private function getHelper()
	{
		// Already initialized, so return
		if ($this->_init)
		{
			return $this->_helper;
		}

		$this->_init = true;

		if (!$this->isFrameworkEnabled())
		{
			return false;
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/protect.php';

		if (!NNProtect::isSystemPluginInstalled($this->_alias))
		{
			return false;
		}

		// Load plugin parameters
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$parameters = NNParameters::getInstance();
		$params     = $parameters->getPluginParams($this->_name);

		// allow in component?
		$disabled_components = is_array($params->disabled_components) ? $params->disabled_components : explode('|', $params->disabled_components);
		$params->disabled_components = array('com_acymailing');
		$params->disabled_components = array_merge($disabled_components, $params->disabled_components);
		if (in_array(JFactory::getApplication()->input->get('option'), $params->disabled_components))
		{
			return false;
		}

		// allow in frontend?
		if (!$params->enable_frontend && JFactory::getApplication()->isSite())
		{
			return false;
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/helper.php';
		$this->_helper = NNFrameworkHelper::getPluginHelper($this, $params);

		return $this->_helper;
	}

	/**
	 * Check if the NoNumber Framework is enabled
	 *
	 * @return bool
	 */
	private function isFrameworkEnabled()
	{
		// Return false if NoNumber Framework is not installed
		if (!$this->isFrameworkInstalled())
		{
			return false;
		}

		$nnframework = JPluginHelper::getPlugin('system', 'nnframework');

		return isset($nnframework->name);
	}

	/**
	 * Check if the NoNumber Framework is installed
	 *
	 * @return bool
	 */
	private function isFrameworkInstalled()
	{
		jimport('joomla.filesystem.file');

		return JFile::exists(JPATH_PLUGINS . '/system/nnframework/nnframework.php');
	}
}
