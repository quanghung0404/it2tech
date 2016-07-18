<?php
/**
 * Plugin Helper File
 *
 * @package         Advanced Module Manager
 * @version         5.3.6PRO-revPRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

class PlgSystemAdvancedModulesHelper
{
	var $params = null;
	var $use_legacy = false;

	function __construct(&$params)
	{
		$this->params = $params;
	}

	public function loadModuleHelper()
	{
		if (!$this->moduleHelperNeedsLegacy())
		{
			return;
		}

		$this->use_legacy = true;

		// No need to load the JModuleHelper again
		if ($this->moduleHelperModuleHelperIsLoaded())
		{
			return;
		}

		require_once JPATH_PLUGINS . '/system/advancedmodules/modulehelper_legacy.php';
	}

	private function moduleHelperNeedsLegacy()
	{
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/versions.php';

		// Return true if old JModuleHelper will be loaded by one of the following extensions
		if (
			(JPluginHelper::isEnabled('system', 't3') && version_compare(NoNumberVersions::getPluginXMLVersion('t3'), '2.4.6', '<'))
			|| (JPluginHelper::isEnabled('system', 'helix') && version_compare(NoNumberVersions::getPluginXMLVersion('helix'), '2.1.9', '<'))
			|| (JPluginHelper::isEnabled('system', 'jsntplframework') && version_compare(NoNumberVersions::getPluginXMLVersion('jsntplframework'), '2.3.4', '<'))
			|| (JPluginHelper::isEnabled('system', 'magebridge') && version_compare(NoNumberVersions::getPluginXMLVersion('magebridge'), '1.9.5295', '<'))
			|| (JPluginHelper::isEnabled('system', 'metamod'))
		)
		{
			return true;
		}

		return false;
	}

	private function moduleHelperModuleHelperIsLoaded()
	{
		$classes = get_declared_classes();
		if (!in_array('JModuleHelper', $classes) && !in_array('jmodulehelper', $classes))
		{
			return false;
		}

		return true;
	}

	public function registerEvents()
	{
		if ($this->use_legacy)
		{
			require_once JPATH_PLUGINS . '/system/advancedmodules/advancedmodulehelper_legacy.php';
			$class = new PlgSystemAdvancedModuleHelper;

			JFactory::getApplication()->registerEvent('onRenderModule', array($class, 'onRenderModule'));
			JFactory::getApplication()->registerEvent('onCreateModuleQuery', array($class, 'onCreateModuleQuery'));
			JFactory::getApplication()->registerEvent('onPrepareModuleList', array($class, 'onPrepareModuleList'));

			return;
		}

		require_once JPATH_PLUGINS . '/system/advancedmodules/advancedmodulehelper.php';
		$class = new PlgSystemAdvancedModuleHelper;

		JFactory::getApplication()->registerEvent('onRenderModule', array($class, 'onRenderModule'));
		JFactory::getApplication()->registerEvent('onPrepareModuleList', array($class, 'onPrepareModuleList'));
	}

	public function replaceLinks()
	{
		if (JFactory::getApplication()->isAdmin() && JFactory::getApplication()->input->get('option') == 'com_modules')
		{
			$this->replaceLinksInCoreModuleManager();

			return;
		}

		$body = JResponse::getBody();

		$this->replaceLinksModules($body);

		if (!JFactory::getApplication()->isAdmin())
		{
			$this->replaceLinksInFrontend($body);
		}

		JResponse::setBody($body);
	}

	private function replaceLinksModules(&$string)
	{
		$string = preg_replace('#(\?option=com_)(modules[^a-z-_])#', '\1advanced\2', $string);
		$string = str_replace(array('?option=com_advancedmodules&force=1', '?option=com_advancedmodules&amp;force=1'), '?option=com_modules', $string);
	}

	private function replaceLinksInFrontend(&$string)
	{
		if (strpos($string, 'jmodediturl=') === false)
		{
			return;
		}

		$url = 'index.php?option=com_advancedmodules&view=edit&task=edit';

		if (JFactory::getUser()->authorise('core.manage', 'com_modules') && $this->params->use_admin_from_frontend)
		{
			$url = 'administrator/index.php?option=com_advancedmodules&task=module.edit';
		}

		$string = preg_replace(
			'#(jmodediturl="[^"]*)index.php\?option=com_config&controller=config.display.modules#',
			'\1' . $url,
			$string
		);
	}

	private function replaceLinksInCoreModuleManager()
	{
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

		NNFrameworkFunctions::loadLanguage('com_advancedmodules');

		$body = JResponse::getBody();

		$url = 'index.php?option=com_advancedmodules';
		if (JFactory::getApplication()->input->get('view') == 'module')
		{
			$url .= '&task=module.edit&id=' . (int) JFactory::getApplication()->input->get('id');
		}

		$link = '<a style="float:right;" href="' . JRoute::_($url) . '">' . JText::_('AMM_SWITCH_TO_ADVANCED_MODULE_MANAGER') . '</a><div style="clear:both;"></div>';
		$body = preg_replace('#(</div>\s*</form>\s*(<\!--.*?-->\s*)*</div>)#', $link . '\1', $body);

		JResponse::setBody($body);
	}
}
