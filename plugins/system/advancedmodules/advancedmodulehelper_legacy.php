<?php
/**
 * Plugin Module Helper File
 *
 * @package         Advanced Module Manager
 * @version         5.3.6PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

/*
 * ModuleHelper methods
 */

class PlgSystemAdvancedModuleHelper
{
	public function onRenderModule(&$module)
	{
		// do nothing if is not frontend
		if (!JFactory::getApplication()->isSite())
		{
			return false;
		}

		// return true if module is empty (this will empty the content)
		if ($this->isEmpty($module))
		{
			return true;
		}

		// Add pre and post html
		$this->addHTML($module);

		return false;
	}

	public function isEmpty(&$module)
	{
		$params = isset($module->adv_param) ? $module->adv_param : (isset($module->advancedparams) ? $module->advancedparams : null);

		// return false if hideempty is off in module params
		if (empty($params) || !isset($params->hideempty) || !$params->hideempty)
		{
			return false;
		}

		$config = $this->getConfig();

		// return false if show_hideempty is off in main config
		if (!$config->show_hideempty)
		{
			return false;
		}

		$content = trim($module->content);
		// return true if module is empty
		if ($content == '')
		{
			// return true will prevent the module from outputting html
			return true;
		}

		// remove html and hidden whitespace
		$content = str_replace(chr(194) . chr(160), ' ', $content);
		$content = str_replace(array('&nbsp;', '&#160;'), ' ', $content);
		// remove comment tags
		$content = preg_replace('#<\!--.*?-->#si', '', $content);
		// remove all closing tags
		$content = preg_replace('#</[^>]+>#si', '', $content);
		// remove tags to be ignored
		$tags = 'p|div|span|strong|b|em|i|ul|font|br|h[0-9]|fieldset|label|ul|ol|li|table|thead|tbody|tfoot|tr|th|td|form';
		$s    = '#<(' . $tags . ')([^a-z0-9>][^>]*)?>#si';
		if (@preg_match($s . 'u', $content))
		{
			$s .= 'u';
		}
		if (preg_match($s, $content))
		{
			$content = preg_replace($s, '', $content);
		}

		// return false if module is not empty
		if (trim($content) != '')
		{
			return false;
		}

		// return true will prevent the module from outputting html
		return true;
	}

	public function addHTML(&$module)
	{
		$params = isset($module->adv_param) ? $module->adv_param : (isset($module->advancedparams) ? $module->advancedparams : null);

		// do nothing if no advanced params are set
		if (empty($params))
		{
			return;
		}

		// prepend the pre html
		if (isset($params->pre_html) && $params->pre_html)
		{
			$module->content = $params->pre_html . $module->content;
		}

		// append the post html
		if (isset($params->post_html) && $params->post_html)
		{
			$module->content .= $params->post_html;
		}
	}

	public function onCreateModuleQuery(&$query)
	{
		// return if is not frontend
		if (!JFactory::getApplication()->isSite())
		{
			return;
		}

		foreach ($query as $type => $strings)
		{
			foreach ($strings as $i => $string)
			{
				if ($type == 'select')
				{
					$query->{$type}[$i] = str_replace(', mm.menuid', '', $string);
				}
				else if (strpos($string, 'mm.') !== false || strpos($string, 'm.publish_') !== false)
				{
					unset($query->{$type}[$i]);
				}
			}
		}
		$query->select[] = 'am.params as advancedparams, 0 as menuid, m.publish_up, m.publish_down';
		$query->join[]   = '#__advancedmodules as am ON am.moduleid = m.id';
		$query->order    = array('m.ordering, m.id');
	}

	public function onPrepareModuleList(&$modules)
	{
		// return if is not frontend
		if (!JFactory::getApplication()->isSite() || empty($modules))
		{
			return;
		}

		jimport('joomla.filesystem.file');

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$parameters = NNParameters::getInstance();

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/assignments.php';
		$assignments = new NNFrameworkAssignmentsHelper;

		$xmlfile_assignments = JPATH_ADMINISTRATOR . '/components/com_advancedmodules/assignments.xml';

		// set params for all loaded modules first
		// and make it an associated array (array id = module id)
		$new_modules = array();
		require_once JPATH_ADMINISTRATOR . '/components/com_advancedmodules/models/module.php';
		$model = new AdvancedModulesModelModule;
		foreach ($modules as $id => $module)
		{
			if (!isset($module->mirror_id))
			{
				$module->mirror_id = $this->getMirrorModuleIdById($id);
			}

			if (!isset($module->advancedparams))
			{
				$module->advancedparams = $this->getAdvancedParamsById($id);
			}

			$module->advancedparams = json_decode($module->advancedparams);
			if (is_null($module->advancedparams))
			{
				$module->advancedparams = new stdClass;
			}

			if (
				!isset($module->advancedparams->assignto_menuitems)
				|| isset($module->advancedparams->assignto_urls_selection_sef)
				|| (
					!is_array($module->advancedparams->assignto_menuitems)
					&& strpos($module->advancedparams->assignto_menuitems, '|') !== false
				)
			)
			{
				$module->advancedparams = (object) $model->initAssignments($module->id, $module);
			}

			$module->advancedparams   = $parameters->getParams($module->advancedparams, $xmlfile_assignments);
			$new_modules[$module->id] = $module;
		}
		$modules = $new_modules;
		unset($new_modules);

		foreach ($modules as $id => $module)
		{
			if ($module->advancedparams === 0)
			{
				continue;
			}

			$extraparams = array();
			for ($i = 1; $i <= 5; $i++)
			{
				$var               = 'extra' . $i;
				$extraparams[$var] = isset($module->advancedparams->{$var}) ? $module->advancedparams->{$var} : '';
			}
			if (!empty($extraparams))
			{
				if (empty($module->params))
				{
					$module->params = $extraparams;
				}
				else
				{
					$module->params = (array) json_decode($module->params, true);
					$module->params = array_merge($module->params, $extraparams);
				}
				$module->params = json_encode($module->params);
			}
			$module->reverse = 0;

			if (!isset($module->published))
			{
				$module->published = 0;
			}

			if ($module->published)
			{
				$this->setMirrorParams($module, $xmlfile_assignments);
				$this->removeDisabledAssignments($module->advancedparams);

				$ass  = $assignments->getAssignmentsFromParams($module->advancedparams);
				$pass = $assignments->passAll($ass, $module->advancedparams->match_method);

				if (!$pass)
				{
					$module->published = 0;
				}

				if ($module->reverse)
				{
					$module->published = $module->published ? 0 : 1;
				}
			}

			$modules[$id] = $module;
		}
	}

	private function removeDisabledAssignments(&$params)
	{
		$config = $this->getConfig();

		if (!$config->show_assignto_homepage)
		{
			$params->assignto_homepage = 0;
		}
		if (!$config->show_assignto_usergrouplevels)
		{
			$params->assignto_usergrouplevels = 0;
		}
		if (!$config->show_assignto_users)
		{
			$params->assignto_users = 0;
		}
		if (!$config->show_assignto_date)
		{
			$params->assignto_date = 0;
			$params->assignto_seasons = 0;
			$params->assignto_months  = 0;
			$params->assignto_days    = 0;
			$params->assignto_time    = 0;
		}
		if (!$config->show_assignto_languages)
		{
			$params->assignto_languages = 0;
		}
		if (!$config->show_assignto_ips)
		{
			$params->assignto_ips = 0;
		}
		if (!$config->show_assignto_geo)
		{
			$params->assignto_geocontinents  = 0;
			$params->assignto_geocountries   = 0;
			$params->assignto_georegions     = 0;
			$params->assignto_geopostalcodes = 0;
		}
		if (!$config->show_assignto_templates)
		{
			$params->assignto_templates = 0;
		}
		if (!$config->show_assignto_urls)
		{
			$params->assignto_urls = 0;
		}
		if (!$config->show_assignto_os)
		{
			$params->assignto_os = 0;
		}
		if (!$config->show_assignto_browsers)
		{
			$params->assignto_browsers = 0;
		}
		if (!$config->show_assignto_components)
		{
			$params->assignto_components = 0;
		}
		if (!$config->show_assignto_tags)
		{
			$params->show_assignto_tags = 0;
		}
		if (!$config->show_assignto_content)
		{
			$params->assignto_contentpagetypes = 0;
			$params->assignto_cats             = 0;
			$params->assignto_articles         = 0;
		}
		if (!$config->show_assignto_easyblog)
		{
			$params->assignto_easyblogpagetypes = 0;
			$params->assignto_easyblogcats      = 0;
			$params->assignto_easyblogtags      = 0;
			$params->assignto_easyblogitems     = 0;
		}
		if (!$config->show_assignto_flexicontent)
		{
			$params->assignto_flexicontentpagetypes = 0;
			$params->assignto_flexicontenttags      = 0;
			$params->assignto_flexicontenttypes     = 0;
		}
		if (!$config->show_assignto_form2content)
		{
			$params->assignto_form2contentprojects = 0;
		}
		if (!$config->show_assignto_k2)
		{
			$params->assignto_k2pagetypes = 0;
			$params->assignto_k2cats      = 0;
			$params->assignto_k2tags      = 0;
			$params->assignto_k2items     = 0;
		}
		if (!$config->show_assignto_zoo)
		{
			$params->assignto_zoopagetypes = 0;
			$params->assignto_zoocats      = 0;
			$params->assignto_zooitems     = 0;
		}
		if (!$config->show_assignto_akeebasubs)
		{
			$params->assignto_akeebasubspagetypes = 0;
			$params->assignto_akeebasubslevels    = 0;
		}
		if (!$config->show_assignto_hikashop)
		{
			$params->assignto_hikashoppagetypes = 0;
			$params->assignto_hikashopcats      = 0;
			$params->assignto_hikashopproducts  = 0;
		}
		if (!$config->show_assignto_mijoshop)
		{
			$params->assignto_mijoshoppagetypes = 0;
			$params->assignto_mijoshopcats      = 0;
			$params->assignto_mijoshopproducts  = 0;
		}
		if (!$config->show_assignto_redshop)
		{
			$params->assignto_redshoppagetypes = 0;
			$params->assignto_redshopcats      = 0;
			$params->assignto_redshopproducts  = 0;
		}
		if (!$config->show_assignto_virtuemart)
		{
			$params->assignto_virtuemartpagetypes = 0;
			$params->assignto_virtuemartcats      = 0;
			$params->assignto_virtuemartproducts  = 0;
		}
		if (!$config->show_assignto_cookieconfirm)
		{
			$params->assignto_cookieconfirm = 0;
		}
		if (!$config->show_assignto_php)
		{
			$params->assignto_php = 0;
		}
	}

	private function setMirrorParams(&$module, $xmlfile_assignments)
	{
		if (empty($module->mirror_id))
		{
			return;
		}

		$parameters = NNParameters::getInstance();

		$mirror_id = $module->mirror_id < 0 ? $module->mirror_id * -1 : $module->mirror_id;

		$count = 0;
		while ($count++ < 10)
		{
			if (!$test_mirrorid = $this->getMirrorModuleIdById($mirror_id))
			{
				break;
			}

			$mirror_id = $test_mirrorid;
		}

		if (empty($mirror_id))
		{
			return;
		}

		$module->reverse = $mirror_id < 0;

		if ($mirror_id == $module->id)
		{
			$empty         = new stdClass;
			$mirror_params = $parameters->getParams($empty, $xmlfile_assignments);
		}
		else
		{
			if (isset($modules[$mirror_id]))
			{
				if (!isset($modules[$mirror_id]->advancedparams))
				{
					$modules[$mirror_id]->advancedparams = $this->getAdvancedParamsById($mirror_id);
					$modules[$mirror_id]->advancedparams = $parameters->getParams($modules[$mirror_id]->adv_param, $xmlfile_assignments);
				}
				$mirror_params = $modules[$mirror_id]->advancedparams;
			}
			else
			{
				$mirror_params = $this->getAdvancedParamsById($mirror_id);
				$mirror_params = $parameters->getParams($mirror_params, $xmlfile_assignments);
			}
		}

		// Keep the advanced settings that shouldn't be mirrored
		$settings_to_keep = array(
			'hideempty', 'color',
			'pre_html', 'post_html',
			'extra1', 'extra2', 'extra3', 'extra4', 'extra5'
		);

		foreach ($settings_to_keep as $key)
		{
			if (!isset($module->advancedparams->$key))
			{
				continue;
			}

			$mirror_params->$key = $module->advancedparams->$key;
		}

		$module->advancedparams = $mirror_params;
	}

	/**
	 * Function that gets the config settings
	 *
	 * @return    Object
	 */
	private function getConfig()
	{
		static $instance;

		if (is_object($instance))
		{
			return $instance;
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$parameters = NNParameters::getInstance();
		$instance   = $parameters->getComponentParams('advancedmodules');

		return $instance;
	}

	private function getMirrorModuleIdById($id)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.mirror_id')
			->from('#__advancedmodules AS a')
			->where('a.moduleid = ' . (int) $id);
		$db->setQuery($query);

		return $db->loadResult();
	}

	private function getAdvancedParamsById($id)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.params')
			->from('#__advancedmodules AS a')
			->where('a.moduleid = ' . (int) $id);
		$db->setQuery($query);

		return $db->loadResult();
	}
}
