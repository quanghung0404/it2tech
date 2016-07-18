<?php
/**
 * Main Plugin File
 *
 * @package         Better Preview
 * @version         4.1.2PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * Plugin that cleans cache
 */
class PlgSystemBetterPreview extends JPlugin
{
	public function __construct(&$subject, $config)
	{
		$this->isadmin   = JFactory::getApplication()->isAdmin();
		$this->ispreview = JFactory::getApplication()->input->get('bp_preview');

		parent::__construct($subject, $config);
	}

	public function onAfterRoute()
	{
		// only in html
		if (JFactory::getDocument()->getType() != 'html')
		{
			return;
		}

		if (!$this->isadmin && JFactory::getApplication()->input->getInt('bp_generatesefs'))
		{
			include __DIR__ . '/helpers/generatesefs.php';

			return;
		}

		// only in admin or frontend preview pages
		if (!($this->isadmin || $this->ispreview))
		{
			return;
		}

		jimport('joomla.filesystem.file');

		if (JFile::exists(JPATH_PLUGINS . '/system/nnframework/helpers/protect.php'))
		{
			require_once JPATH_PLUGINS . '/system/nnframework/helpers/protect.php';
			// return if page should be protected
			if (NNProtect::isProtectedPage('betterpreview'))
			{
				return;
			}
		}

		// load the admin language file
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
		NNFrameworkFunctions::loadLanguage('plg_' . $this->_type . '_' . $this->_name);

		if (JFactory::getApplication()->input->get('bp_purgesefs'))
		{
			include __DIR__ . '/helpers/purgesefs.php';

			die();
		}

		if (JFactory::getApplication()->input->get('bp_preloader'))
		{
			include __DIR__ . '/helpers/preloader.php';

			return;
		}

		// return if NoNumber Framework plugin is not installed
		if (!JFile::exists(JPATH_PLUGINS . '/system/nnframework/nnframework.php'))
		{
			if (!$this->isadmin || JFactory::getApplication()->input->get('option') == 'com_login')
			{
				return;
			}

			$msg = JText::_('BP_NONUMBER_FRAMEWORK_NOT_INSTALLED')
				. ' ' . JText::sprintf('BP_EXTENSION_CAN_NOT_FUNCTION', JText::_('BETTER_PREVIEW'));
			$mq  = JFactory::getApplication()->getMessageQueue();

			foreach ($mq as $m)
			{
				if ($m['message'] != $msg)
				{
					continue;
				}

				$msg = '';
				break;
			}

			if ($msg)
			{
				JFactory::getApplication()->enqueueMessage($msg, 'error');
			}

			return;
		}

		// return if NoNumber Framework plugin is not enabled
		$nnframework = JPluginHelper::getPlugin('system', 'nnframework');
		if (!isset($nnframework->name))
		{
			if (!$this->isadmin || JFactory::getApplication()->input->get('option') == 'com_login')
			{
				return;
			}

			$msg = JText::_('BP_NONUMBER_FRAMEWORK_NOT_ENABLED');
			$msg .= ' ' . JText::sprintf('BP_EXTENSION_CAN_NOT_FUNCTION', JText::_('BETTER_PREVIEW'));
			$mq = JFactory::getApplication()->getMessageQueue();

			foreach ($mq as $m)
			{
				if ($m['message'] != $msg)
				{
					continue;
				}

				$msg = '';
				break;
			}

			if ($msg)
			{
				JFactory::getApplication()->enqueueMessage($msg, 'notice');
			}

			return;
		}

		// Load plugin parameters
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$parameters = NNParameters::getInstance();
		$params     = $parameters->getPluginParams($this->_name);

		if ($this->isadmin && !$params->display_title_link && !$params->display_status_link)
		{
			return;
		}

		$type = $this->ispreview ? 'preview' : 'link';

		// Include the Helpers
		require_once JPATH_PLUGINS . '/system/betterpreview/helper.php';
		require_once JPATH_PLUGINS . '/system/betterpreview/helpers/' . $type . '.php';

		if (!$class = PlgSystemBetterPreviewHelper::getHelperClass($type))
		{
			return false;
		}

		$this->helper = new $class($params);

		switch (true)
		{
			case ($this->ispreview):
				// Check for request forgeries.
				$this->helper->checkSession() or jexit(JText::_('JINVALID_TOKEN'));
				$this->helper->purgeCache();
				$this->helper->setLanguage();
				$this->helper->states();
				break;

			case ($this->isadmin) :
				JHtml::_('jquery.framework');
				JHtml::_('bootstrap.tooltip');
				JHtml::stylesheet('nnframework/style.min.css', false, true);
				JHtml::stylesheet('betterpreview/style.min.css', false, true);
				NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/betterpreview/js/script.min.js');
				NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/betterpreview/js/script.min.js');
				if ($params->show_url_details)
				{
					$script = "
						var betterpreview_texts = {
							'url': '" . addslashes(html_entity_decode(JText::_('NN_URL'))) . "',
							'nonsef': '" . addslashes(html_entity_decode(JText::_('BP_NON_SEF_URL'))) . "',
						};
					";
					JFactory::getDocument()->addScriptDeclaration($script);
				}
				break;
		}
	}

	public function onContentPrepare($context, &$article)
	{
		if (!isset($this->helper) || !$this->ispreview)
		{
			return;
		}

		$this->helper->restoreStates();
		$this->helper->renderPreview($article, $context);
	}

	public function onAfterRender()
	{
		if (!isset($this->helper))
		{
			return;
		}

		switch (true)
		{
			case ($this->ispreview):
				$this->helper->addMessages();
				break;

			case ($this->isadmin) :
				$this->helper->convertLinks();
				break;
		}
	}
}
