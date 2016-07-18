<?php
/**
 * Plugin Helper File
 *
 * @package         Cache Cleaner
 * @version         4.2.3PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

NNFrameworkFunctions::loadLanguage('plg_system_cachecleaner');

class PlgSystemCacheCleanerHelper
{
	var $helpers = array();
	var $type = '';
	var $show_message = false;
	var $thirdparties = array('jre', 'jotcache', 'siteground', 'maxcdn', 'keycdn', 'cloudflare');

	public function __construct(&$params)
	{
		$params->size    = 0;
		$params->message = '';
		$params->error   = false;

		$this->params = $params;

		require_once __DIR__ . '/helpers/helpers.php';
		$this->helpers = PlgSystemCacheCleanerHelpers::getInstance($params);

		$this->type = $this->getCleanType();
	}

	function clean()
	{
		$this->purgeThirdPartyCacheByUrl();

		if (!$this->type)
		{
			return;
		}

		// Load language for messaging
		NNFrameworkFunctions::loadLanguage('mod_cachecleaner');

		$this->purgeCache();

		// only handle messages in html
		if (JFactory::getDocument()->getType() != 'html')
		{
			return false;
		}

		$error = $this->helpers->getParams()->error;
		if ($error)
		{
			$message = JText::_('CC_NOT_ALL_CACHE_COULD_BE_REMOVED');
			$message .= $this->helpers->getParams()->error !== true ? '<br />' . $this->helpers->getParams()->error : '';
		}
		else
		{
			$message = $this->helpers->getParams()->message ?: JText::_('CC_CACHE_CLEANED');

			if ($this->params->show_size && $this->helpers->getParams()->size)
			{
				$message .= ' (' . $this->helpers->get('cache')->getSize() . ')';
			}
		}

		if (JFactory::getApplication()->input->getInt('break'))
		{
			echo (!$error ? '+' : '') . str_replace('<br />', ' - ', $message);
			die;
		}

		if ($this->show_message && $message)
		{
			JFactory::getApplication()->enqueueMessage($message, ($error ? 'error' : 'message'));
		}
	}

	function getCleanType()
	{
		$cleancache = trim(JFactory::getApplication()->input->getString('cleancache'));

		// Clean via url
		if (!empty($cleancache))
		{
			// Return if on frontend and no secret url key is given
			if (JFactory::getApplication()->isSite() && $cleancache != $this->params->frontend_secret)
			{
				return '';
			}

			// Return if on login page
			if (JFactory::getApplication()->isAdmin() && JFactory::getUser()->get('guest'))
			{
				return '';
			}

			if (JFactory::getApplication()->input->getWord('src') == 'button')
			{
				return 'button';
			}

			$this->show_message = true;

			return 'clean';
		}

		// Clean via save task
		if ($this->passTask())
		{
			return 'save';
		}

		// Clean via interval
		if ($this->passInterval())
		{
			return 'interval';
		}

		return '';
	}

	function passTask()
	{
		if (!$task = JFactory::getApplication()->input->get('task'))
		{
			return false;
		}

		$task = explode('.', $task, 2);
		$task = isset($task['1']) ? $task['1'] : $task['0'];
		if (strpos($task, 'save') === 0)
		{
			$task = 'save';
		}

		$tasks = array_diff(array_map('trim', explode(',', $this->params->auto_save_tasks)), array(''));

		if (empty($tasks) || !in_array($task, $tasks))
		{
			return false;
		}

		if (JFactory::getApplication()->isAdmin() && $this->params->auto_save_admin)
		{
			$this->show_message = $this->params->auto_save_admin_msg;

			return true;
		}

		if (JFactory::getApplication()->isSite() && $this->params->auto_save_front)
		{
			$this->show_message = $this->params->auto_save_front_msg;

			return true;
		}

		return false;
	}

	function purgeCache()
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		// Joomla cache
		if ($this->passType('purge'))
		{
			$this->helpers->get('joomla')->purge();
		}

		// 3rd party cache
		$this->purgeThirdPartyCache();

		// Folders
		if ($this->passType('clean_tmp'))
		{
			$this->helpers->get('folders')->purge_tmp();
		}
		if ($this->passType('clean_folders'))
		{
			$this->helpers->get('folders')->purge_folders();
		}

		// Tables
		if ($this->passType('clean_tables'))
		{
			$this->helpers->get('tables')->purge();
		}

		// Purge OPcache
		if ($this->passType('purge_opcache'))
		{
			$this->helpers->get('joomla')->purgeOPcache();
		}

		// Purge expired cache
		if ($this->passType('purge'))
		{
			$this->helpers->get('joomla')->purgeExpired();
		}

		// Purge update cache
		if ($this->passType('purge_updates'))
		{
			$this->helpers->get('joomla')->purgeUpdates();
		}

		// Global check-in
		if ($this->passType('checkin'))
		{
			$this->helpers->get('joomla')->checkIn();
		}

		if ($this->passType('query_url') && !empty($this->params->query_url_selection))
		{
			$this->queryUrl();
		}

		$this->updateLog();
	}

	function passType($type)
	{
		if (empty($this->params->{$type}))
		{
			return false;
		}

		if ($this->params->{$type} == 2 && $this->type != 'button')
		{
			return false;
		}

		return true;
	}

	function purgeThirdPartyCache()
	{
		foreach ($this->thirdparties as $thirdparty)
		{
			if (!$this->passType('clean_' . $thirdparty))
			{
				continue;
			}

			$this->helpers->get($thirdparty)->purge();
		}
	}

	function purgeThirdPartyCacheByUrl()
	{
		foreach ($this->thirdparties as $thirdparty)
		{
			if (!JFactory::getApplication()->input->getInt('purge_' . $thirdparty))
			{
				continue;
			}

			$this->helpers->get($thirdparty)->purge();
		}
	}

	function queryUrl()
	{
		try
		{
			$http = JHttpFactory::getHttp()->get($this->params->query_url_selection, null, 5);
			if ($http->code != 200)
			{
				$this->params->error = JText::sprintf('CC_ERROR_QUERY_URL_FAILED', $this->params->query_url_selection);
			}
		}
		catch (RuntimeException $e)
		{
			$this->params->error = JText::sprintf('CC_ERROR_QUERY_URL_FAILED', $this->params->query_url_selection);
		}
	}

	function passInterval()
	{
		if (
			(JFactory::getApplication()->isAdmin() && !$this->params->auto_interval_admin)
			|| (JFactory::getApplication()->isSite() && $this->params->auto_interval_front)
		)
		{
			return false;
		}

		if (JFactory::getApplication()->isAdmin() && JFactory::getUser()->get('guest'))
		{
			return false;
		}

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$file_path = str_replace('//', '/', JPATH_SITE . '/' . str_replace('\\', '/', $this->params->log_path . '/'));
		if (!JFolder::exists($file_path))
		{
			$file_path = JPATH_PLUGINS . '/system/cachecleaner/';
		}
		$file = $file_path . '/cachecleaner_lastclean.log';

		$secs = JFactory::getApplication()->isSite() ? $this->params->auto_interval_front_secs : $this->params->auto_interval_admin_secs;

		// Return false if last clean is within interval
		if (
			JFile::exists($file)
			&& ($lastclean = JFile::read($file))
			&& $lastclean > (time() - $secs)
		)
		{
			return false;
		}

		$this->show_message = JFactory::getApplication()->isSite() ? $this->params->auto_interval_front_msg : $this->params->auto_interval_admin_msg;

		return true;
	}

	function updateLog()
	{
		// Write current time to text file

		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$file_path = str_replace('//', '/', JPATH_SITE . '/' . str_replace('\\', '/', $this->params->log_path . '/'));

		if (!JFolder::exists($file_path))
		{
			$file_path = JPATH_PLUGINS . '/system/cachecleaner/';
		}

		$time = time();
		JFile::write($file_path . 'cachecleaner_lastclean.log', $time);
	}
}
