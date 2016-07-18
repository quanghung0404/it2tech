<?php
/**
 * Plugin Link Helper File
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

require_once JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php';
require_once JPATH_ADMINISTRATOR . '/components/com_menus/models/menutypes.php';

/**
 * Plugin that cleans cache
 */
class HelperBetterPreviewLink extends PlgSystemBetterPreviewHelper
{
	var $_has_home = false;

	public function __construct(&$params)
	{
		$model = new MenusModelMenutypes;
		$model->getTypeOptions();
		$this->types = $model->getReverseLookup();

		parent::__construct($params);
	}

	public function convertLinks()
	{
		$links = $this->getLinks();

		$html = JResponse::getBody();

		if ($html == '')
		{
			return;
		}

		$this->prepareLinks($links);

		$title_class = 'betterpreview-dropdown dropdown pull-right visible-desktop visible-tablet';

		if (!$this->_has_home && count($links) < 2)
		{
			if ($this->params->display_title_link)
			{
				$title_link = '<div class="' . $title_class . '">'
					. '<a class="brand" href="' . JUri::root() . '" target="_blank">'
					. '\2'
					. '</a>'
					. '</div>';
			}

			if ($this->params->display_status_link)
			{
				$status_link = '<div class="betterpreview-dropdown">'
					. '<a href="' . JUri::root() . '" target="_blank">'
					. '\2'
					. '</a>'
					. '</div>';
			}
		}
		else
		{
			$mainurl     = 0;
			$main        = 0;
			$list_title  = array();
			$list_status = array();

			foreach ($links as $link)
			{
				if ($link->published)
				{
					if (!$mainurl)
					{
						$mainurl = $link->url;
						$main    = 1;
					}
					else
					{
						$main = 0;
					}
				}

				$item = array();

				$item[] = '<li>';

				if ($link->published)
				{
					$item[] = '<a href="' . $link->url . '" target="_blank" class="list-item"><span class="wrapper">';
				}
				else if (isset($link->error))
				{
					$item[] = '<span class="disabled list-item hasTooltip" data-placement="right" title="' . str_replace('"', '&quot;', $link->error) . '">';
				}
				else
				{
					$item[] = '<span class="disabled list-item">';
				}

				$item[] = '<table><tr>';

				$item[] = '<td>';
				if (!$link->published)
				{
					$item[] = '<span class="icon-not-ok"></span> ';
				}
				else if ($link->type)
				{
					$item[] = '<span class="icon-url"></span> ';
				}
				else
				{
					$item[] = '<span class="icon-home"></span> ';
				}

				$item[] = '<span class="nn_status_item_text">' . $link->name . '</span>';
				$item[] = '</td>';

				$item[] = '<td class="nn_status_td_right">';
				if ($this->params->show_link_type && $link->type)
				{
					$item['link_type'] = '<span class="label' . ($main ? ' label-success' : '') . '">' . strtolower($link->type) . '</span>';
				}

				if ($this->params->show_url_details)
				{
					if ($link->url && isset($link->nonsef))
					{
						$item['url_details'] = '<span class="icon-urlinfo"'
							. ' onclick="betterpreview_show_info(\'' . str_replace('"', '&quot;', $link->name . '\',\'' . $link->type . '\',\'' . $link->url . '\',\'' . $link->nonsef . '\',\'' . (isset($link->error) ? $link->error : '')) . '\');return false;"></span>';
					}
					else
					{
						$item['url_details'] = '<span class="icon-"></span>';
					}
				}
				$item[] = '</td>';

				$item[] = '</tr></table>';

				if ($link->published)
				{
					$item[] = '</span></a>';
				}
				else
				{
					$item[] = '</span>';
				}

				$item[] = '</li>';

				$list_title[] = implode('', $item);
				//unset($item['url_details']);
				//unset($item['link_type']);
				$list_status[] = implode('', $item);
			}

			if (!$mainurl)
			{
				// should really never happen
				$mainurl = JUri::root();
			}

			if ($this->params->display_title_link)
			{
				$title_link = '<div class="' . $title_class . '">'
					. '<a class="dropdown-toggle \1" href="' . $mainurl . '" target="_blank">'
					. '\2'
					. '<span class="icon-arrow-down-3"></span>'
					. '</a>'
					. '<ul class="dropdown-menu">'
					. implode('<li class="divider"></li>', $list_title)
					. '</ul>'
					. '</div>';
			}

			if ($this->params->display_status_link)
			{
				if ($this->params->reverse_status_link)
				{
					$list_status = array_reverse($list_status);
				}

				$status_link = '<div class="betterpreview-dropdown">'
					. '<a class="dropdown-toggle" href="' . $mainurl . '" target="_blank">'
					. '\2'
					. '<span class="icon-arrow-up-3"></span>'
					. '</a>'
					. '<ul class="dropdown-menu dropup-menu">'
					. implode('<li class="divider"></li>', $list_status)
					. '</ul>'
					. '</div>';
			}
		}

		if ($this->params->display_title_link)
		{
			$html = preg_replace(
				'#<a class="(brand visible-desktop visible-tablet)" [^>]*>(.*?)</a>#s',
				$title_link,
				$html
			);
		}

		if ($this->params->display_status_link)
		{
			$html = preg_replace(
				'#(<div class="btn-group">)<a [^>]*>(<i class="icon-share-alt"></i>.*?)</a>(</div>)#s',
				'\1' . $status_link . '\3',
				$html
			);
		}

		JResponse::setBody($html);
	}

	public function prepareLinks(&$links)
	{
		$home_link = (object) array(
			'url'       => JUri::root(),
			'type'      => '',
			'name'      => 'Home',
			'published' => 1,
		);

		if (empty($links))
		{
			$links[] = $home_link;

			return;
		}

		foreach ($links as $link)
		{
			$this->prepareNonSefLink($link);
			$urls[$link->url] = $link->url;
		}

		$urls = $this->getUrlsFromCache($urls);

		foreach ($links as $i => $link)
		{
			$this->prepareSefLink($link, $urls);
		}

		if (!$this->_has_home)
		{
			$links[] = $home_link;
		}
	}

	public function prepareNonSefLink(&$link)
	{
		$lang              = isset($link->language) ? $link->language : '';
		$default_menu_item = JFactory::getApplication()->getMenu('site')->getDefault($lang);
		if (empty($default_menu_item))
		{
			$default_menu_item = JFactory::getApplication()->getMenu('site')->getDefault();
		}
		$default_menu_url = $default_menu_item->link . '&Itemid=' . $default_menu_item->id;

		if (!$this->params->use_home_menu_id && $link->url != $default_menu_url)
		{
			// Remove the home Itemid
			$link->url = preg_replace('#&(amp;)?Itemid=' . $default_menu_item->id . '$#', '', $link->url);
		}

		// Check if current item is the home menu item
		if ($link->published && in_array($link->url, array($default_menu_url, '')))
		{
			$this->_has_home = true;
			$link->home      = true;
			$link->name      = '<span class="icon-home"></span> ' . $link->name;
		}
	}

	public function prepareSefLink(&$link, &$urls)
	{
		$roots = array(JUri::root(), JUri::root(0), JUri::root(0) . '/', '/');

		$link->nonsef = $this->createURL($link->url);
		$url          = isset($urls[$link->url]) ? $urls[$link->url] : $link->url;
		$link->url    = $this->createURL($url);

		if ($link->published && in_array($url, $roots))
		{
			$this->_has_home = true;
			$link->name      = (isset($link->home) && $link->home) ? $link->name : '<span class="icon-home"></span> ' . $link->name;
		}
	}

	/**
	 * Default method for getting the links for a component view
	 * Based on searching for matching menu item links
	 *
	 * @return array
	 */
	public function getLinks()
	{
		$links = array();

		$uri = JUri::getInstance();
		$url = $uri->toString(array('query'));

		// find menu item based on current admin url
		if (!$url)
		{
			return $links;
		}

		$url     = 'index.php' . $url;
		$com_url = preg_replace('#&.*#', '', $url);

		// get all urls matching
		$this->q->clear()
			->select('a.id as id')
			->select('a.link as url')
			->from('#__menu as a')
			->where(
				'('
				. 'a.link = ' . $this->db->quote($com_url)
				. ' OR a.link LIKE ' . $this->db->quote($com_url . '&%')
				. ')'
			)
			->where('a.client_id = 0')
			->where('a.published = 1');
		if (JFactory::getApplication()->input->get('id'))
		{
			$this->q->where(
				'('
				. 'a.link LIKE ' . $this->db->quote('%&id=' . JFactory::getApplication()->input->get('id'))
				. ' OR a.link NOT LIKE ' . $this->db->quote('%&id=%')
				. ')'
			);
		}
		$this->db->setQuery($this->q);
		$items = $this->db->loadAssocList('id', 'url');

		if (empty($items))
		{
			return $items;
		}

		// search for exact url match
		$id = array_search($url, $items);

		// search for url match without layout edit/form
		if (!$id && ((!strpos($url, '&layout=edit') === false) || (!strpos($url, '&layout=form') === false)))
		{
			$url = preg_replace('#&layout=(?:edit|form)(&|$)#', '\1', $url);
			$id  = array_search($url, $items);
		}

		// search for url match drilling down to first url parameter
		while (!$id)
		{
			$url = preg_replace('#^(.*)&.*$#', '\1', $url);

			// search for exact url match with last url parameter stripped off
			if ($id = array_search($url, $items))
			{
				break;
			}

			// search for url starting with url with last url parameter stripped off
			// (disregarding urls with specific ids)
			foreach ($items as $itemid => $itemurl)
			{
				if (strpos($itemurl, $url) === 0 && strpos($itemurl, '&id=') === false)
				{
					$id = $itemid;
					break;
				}
			}
			if (strpos($url, '&') === false)
			{
				break;
			}
		}

		if ($id)
		{
			$this->q->clear()
				->select('a.id')
				->select('a.title as name')
				->select('a.link as url')
				->select('a.published as published')
				->select('a.language as language')
				->select('a.parent_id as parent')
				->from('#__menu as a')
				->where('a.id = ' . $id);
			$this->db->setQuery($this->q);
			$item       = $this->db->loadObject();
			$item->type = '';

			$parents = $this->getParents(
				$item,
				'menu',
				array('name' => 'title', 'parent' => 'parent_id', 'url' => 'link'),
				array(),
				1
			);

			$links = array_merge(array($item), $parents);

			foreach ($links as $i => $link)
			{
				if (isset($link->language) && $link->language)
				{
					if (strpos($link->url, '&lang=') == false && strpos($link->url, '?lang=') == false)
					{
						$links[$i]->url .= '&lang=' . $link->language;
					}
				}
				if (isset($link->published) && $link->published)
				{
					if (strpos($link->url, '&Itemid=') == false && strpos($link->url, '?Itemid=') == false)
					{
						$links[$i]->url .= '&Itemid=' . $link->id;
					}
				}
				if (isset($link->url) && preg_match('#option=([a-z0-9_]+)#', $link->url, $type))
				{
					$links[$i]->type = JText::_($type['1']);
				}
			}
		}

		return $links;
	}

	public function getUrlsFromCache($nonsefs)
	{
		if (empty($nonsefs))
		{
			return array();
		}

		$urls = $nonsefs;

		$sefs = $this->getUrlsFromDB($urls);

		// merge sef urls into url list
		$urls = array_merge($urls, $sefs);

		// remaining not-found urls
		$nonsefs = array_diff($urls, $sefs);

		if (empty($nonsefs))
		{
			return $urls;
		}

		$this->saveUrlsToDB($nonsefs);
		$sefs = $this->getUrlsFromDB($nonsefs);

		// merge sef urls into url list
		$urls = array_merge($urls, $sefs);

		return $urls;
	}

	public function getUrlsFromDB($urls)
	{
		$date = JFactory::getDate('now - ' . $this->params->index_timeout . ' hours');
		$this->q->clear()
			->select('a.url')
			->select('a.sef')
			->from('#__betterpreview_sefs as a')
			->where('a.url IN (\'' . implode('\',\'', $urls) . '\')')
			->where('a.created > ' . $this->db->quote($date->toSql()));
		$this->db->setQuery($this->q);
		$sef = $this->db->loadAssocList('url', 'sef');

		return $sef ? $sef : array();
	}

	public function saveUrlsToDB($urls)
	{
		// remove any records of these urls
		$this->q->clear()
			->delete('#__betterpreview_sefs')
			->where($this->db->quoteName('url') . ' IN (\'' . implode('\',\'', $urls) . '\')');
		$this->db->setQuery($this->q);
		$this->db->execute();

		// add empty url records that will be picked up in the generatesefs page
		$this->q->clear()
			->insert('#__betterpreview_sefs')
			->columns($this->db->quoteName('url'));
		foreach ($urls as $url)
		{
			$this->q->values($this->db->quote($url));
		}

		$this->db->setQuery($this->q);
		$this->db->execute();

		// get session id
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('session_id'))
			->from($db->quoteName('#__session'))
			->where($db->quoteName('userid') . ' = ' . $db->quote(JFactory::getUser()->id))
			->where($db->quoteName('client_id') . ' = 1')
			->order($db->quoteName('time') . ' DESC');
		$db->setQuery($query);
		$session = (string) $db->loadResult();

		// update db
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
		$func = new NNFrameworkFunctions;
		$func->getContents(JRoute::_(JUri::root() . 'index.php?tmpl=component&bp_generatesefs=1&session=' . $session));
	}

	public function createURL($url)
	{
		$root = JUri::root(0);

		if ($url && substr($url, 0, strlen($root)) == $root)
		{
			$url = substr($url, strlen($root));
		}

		if ($url && $url[0] == '/')
		{
			$url = substr($url, 1);
		}

		return JUri::root() . $url;
	}
}
