<?php
/**
 * Generate SEF URLs page
 * Converts URLs to frontend SEF
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

if (JFactory::getApplication()->isAdmin())
{
	die;
}

// need to set the user agent, to prevent breaking when debugging is switched on
$_SERVER['HTTP_USER_AGENT'] = '';

$helper = new BetterPreviewGenerateSefs;
$helper->render();

class BetterPreviewGenerateSefs
{
	var $params = null;
	var $db = null;

	public function render()
	{
		// Load plugin parameters
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$parameters   = NNParameters::getInstance();
		$this->params = $parameters->getPluginParams('betterpreview');

		$this->db = JFactory::getDbo();

		// log into frontend
		if (!JFactory::getUser()->id)
		{
			$this->logIn();
		}

		// Get a minimum of 50 urls to update
		$urls = $this->getUrlsToUpdate(50);

		if (empty($urls))
		{
			die('no sefs to update');
		}

		$this->insertSefRecords($urls);

		die('sefs updated');
	}

	private function logIn()
	{
		$query = $this->db->getQuery(true)
			->select('userid')
			->from('#__session')
			->where('session_id = ' . $this->db->quote(JFactory::getApplication()->input->get('session')))
			->where('client_id = 1')
			->where('guest = 0');
		$this->db->setQuery($query);
		$userid = $this->db->loadResult();

		$user = JFactory::getUser($userid);

		if ($user instanceof Exception)
		{
			return;
		}

		$session = JFactory::getSession();
		$session->set('user', $user);
		JFactory::getApplication()->checkSession();
	}

	private function getUrlsToUpdate($min = 50)
	{
		// get all outdated urls (older than timeout setting, maximum 500)
		$urls = $this->getOldUrls(500, $this->params->index_timeout . ' hours');

		if (count($urls) >= $min)
		{
			return $urls;
		}

		// Less than minimum number of urls found, so let's get more (older than an hour, maximum 50)
		$urls = $this->getOldUrls($min, '1 hour');

		if (count($urls) >= $min)
		{
			return $urls;
		}

		// still not much to do? lets also add/update some random menu urls
		$menuitems = $this->getRandomMenuUrls(($min - count($urls)));
		$urls      = array_merge($urls, $menuitems);

		return $urls;
	}

	private function getOldUrls($max, $min_age = '1 day')
	{
		$date  = JFactory::getDate('now - ' . $min_age);
		$query = $this->db->getQuery(true)
			->select('a.url')
			->from('#__betterpreview_sefs as a')
			->where('a.created < ' . $this->db->quote($date->toSql()))
			->order('a.created ASC');
		$this->db->setQuery($query, 0, $max);
		$urls = $this->db->loadColumn();

		if (!$urls)
		{
			$urls = array();
		}

		return $urls;
	}

	private function getRandomMenuUrls($max)
	{
		$query = $this->db->getQuery(true)
			->select('CONCAT(a.link, \'&Itemid=\', a.id)')
			->from('#__menu as a')
			->where('a.client_id = 0')
			->where('a.type != ' . $this->db->quote('alias'))
			->where('a.type != ' . $this->db->quote('url'))
			->where('a.link != ' . $this->db->quote(''))
			->order('RAND()');
		$this->db->setQuery($query, 0, $max);
		$menuitems = $this->db->loadColumn();

		if (empty($menuitems))
		{
			return array();
		}

		$query = $this->db->getQuery(true)
			->select('a.url')
			->from('#__betterpreview_sefs as a')
			->where('a.url IN (\'' . implode('\',\'', $menuitems) . '\')');
		$this->db->setQuery($query);
		$sefs = $this->db->loadColumn();

		if (!empty($sefs))
		{
			return $menuitems;
		}

		return array_diff($menuitems, $sefs);
	}

	private function deleteSefRecords($urls)
	{
		$query = $this->db->getQuery(true)
			->delete('#__betterpreview_sefs')
			->where($this->db->quoteName('url') . ' IN (\'' . implode('\',\'', $urls) . '\')');
		$this->db->setQuery($query);
		$this->db->execute();
	}

	private function insertSefRecords($urls)
	{
		// Delete urls from sef database so they will get renewed
		$this->deleteSefRecords($urls);

		$sefs = $this->getSefUrls($urls);

		if (empty($sefs))
		{
			return;
		}

		$date = JFactory::getDate();

		$query = $this->db->getQuery(true)
			->insert('#__betterpreview_sefs')
			->columns(array($this->db->quoteName('url'), $this->db->quoteName('sef'), $this->db->quoteName('created')));

		foreach ($sefs as $url => $sef)
		{
			$query->values($this->db->quote($url) . ',' . $this->db->quote($sef) . ',' . $this->db->quote($date->toSql()));
		}

		$this->db->setQuery($query);
		$this->db->execute();
	}

	private function getSefUrls($urls)
	{
		$sefs = array();
		foreach ($urls as $url)
		{
			if (!$sef = $this->getSefUrl($url))
			{
				continue;
			}

			$sefs[$url] = $sef;
		}

		return $sefs;
	}

	private function getSefUrl($url)
	{
		if (!$url)
		{
			return false;
		}

		if (substr($url, 0, 4) == 'http')
		{
			return $url;
		}

		$sef = JRoute::_($url);

		if ($sef == $url || $sef == '/' . $url)
		{
			return false;
		}

		return $sef;
	}
}
