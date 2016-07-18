<?php
/**
 * @package         Sourcerer
 * @version         5.2.2PRO
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * Plugin that replaces Sourcerer code with its HTML / CSS / JavaScript / PHP equivalent
 */
class PlgSystemSourcerer extends JPlugin
{
	public function __construct(&$subject, $config)
	{
		$this->_pass = 0;
		parent::__construct($subject, $config);
	}

	public function onAfterRoute()
	{
		$this->_pass = 0;

		jimport('joomla.filesystem.file');
		if (JFile::exists(JPATH_PLUGINS . '/system/nnframework/helpers/protect.php'))
		{
			require_once JPATH_PLUGINS . '/system/nnframework/helpers/protect.php';
			// return if page should be protected
			if (NNProtect::isProtectedPage('', 1))
			{
				return;
			}
		}

		// load the admin language file
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
		NNFrameworkFunctions::loadLanguage('plg_' . $this->_type . '_' . $this->_name);

		// return if NoNumber Framework plugin is not installed
		if (!JFile::exists(JPATH_PLUGINS . '/system/nnframework/nnframework.php'))
		{
			if (JFactory::getApplication()->isAdmin() && JFactory::getApplication()->input->get('option') != 'com_login')
			{
				$msg = JText::_('SRC_NONUMBER_FRAMEWORK_NOT_INSTALLED')
					. ' ' . JText::sprintf('SRC_EXTENSION_CAN_NOT_FUNCTION', JText::_('SOURCERER'));
				$mq  = JFactory::getApplication()->getMessageQueue();
				foreach ($mq as $m)
				{
					if ($m['message'] == $msg)
					{
						$msg = '';
						break;
					}
				}
				if ($msg)
				{
					JFactory::getApplication()->enqueueMessage($msg, 'error');
				}
			}

			return;
		}

		if (JFile::exists(JPATH_PLUGINS . '/system/nnframework/helpers/protect.php'))
		{
			require_once JPATH_PLUGINS . '/system/nnframework/helpers/protect.php';
			// return if current page is an admin page
			if (NNProtect::isAdmin())
			{
				return;
			}
		}
		else if (JFactory::getApplication()->isAdmin())
		{
			return;
		}

		// load the site language file
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
		NNFrameworkFunctions::loadLanguage('plg_' . $this->_type . '_' . $this->_name, JPATH_SITE);

		// Load plugin parameters
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$parameters = NNParameters::getInstance();
		$params     = $parameters->getPluginParams($this->_name);

		// Include the Helper
		require_once JPATH_PLUGINS . '/' . $this->_type . '/' . $this->_name . '/helper.php';
		$class         = get_class($this) . 'Helper';
		$this->_helper = new $class ($params);

		$this->_pass = 1;
	}

	public function onContentPrepare($context, &$article)
	{
		if ($this->_pass)
		{
			$this->_helper->onContentPrepare($article, $context);
		}
	}

	public function onAfterDispatch()
	{
		if ($this->_pass)
		{
			$this->_helper->onAfterDispatch();
		}
	}

	public function onAfterRender()
	{
		if ($this->_pass)
		{
			$this->_helper->onAfterRender();
		}
	}
}
