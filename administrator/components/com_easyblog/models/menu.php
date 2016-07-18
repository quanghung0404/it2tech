<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/model.php');

class EasyBlogModelMenu extends EasyBlogAdminModel
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Retrieves all menus associated with EasyBlog
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAssociatedMenus()
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = 'AND ' . $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog%');

		$language = false;

		if (!$this->app->isAdmin()) {
        	$language = $this->app->getLanguageFilter();
        	$languageTag = JFactory::getLanguage()->getTag();
    	}

        // if ($language) {

        // 	$query[] = 'AND';
        // 	$query[] = '(';
        // 	$query[] = $db->qn('language') . '=' . $db->Quote($languageTag);
        // 	$query[] = 'OR';
        // 	$query[] = $db->qn('language') . '=' . $db->Quote('*');
        // 	$query[] = ')';
        // }

        $query = implode(' ', $query);

        $db->setQuery($query);

        $result = $db->loadObjectList();

        return $result;
	}

	/**
	 * Retrieves the link property of a given menu id
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMenuLink($itemId)
	{
		$db = EB::db();
		$query = array();
		$query[] = 'SELECT ' . $db->qn('link') . ' FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE ' . $db->qn('id') . '=' . $db->Quote($itemId);
		$query = implode(' ', $query);

		$db->setQuery($query);

		$link = $db->loadResult();

		return $link;
	}

	public function getMenuParamsById($menuId)
	{
		static $items = array();

		if (isset($items[$menuId])) {
			return $items[$menuId];
		}

		$db = EB::db();
		$query = array();
		$query[] = 'SELECT ' . $db->qn('params') . ' FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE ' . $db->qn('id') . '=' . $db->Quote($menuId);
		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadResult();

		$param = new JRegistry();

		if ($result) {
			$param = new JRegistry($result);
		}

		$items[$menuId] = $param;
		return $param;
	}

	public function getDefaultEntryXMLParams()
	{
		static $_cache = null;

		if (! $_cache) {

			$manifest = JPATH_ROOT . '/components/com_easyblog/views/entry/tmpl/default.xml';
			$fieldsets = EB::form()->getManifest($manifest);

			$obj = new stdClass();

			foreach($fieldsets as $fieldset) {
				foreach($fieldset->fields as $field) {
					$obj->{$field->attributes->name} = $field->attributes->default;
				}
			}

			$_cache = new JRegistry($obj);
		}

		return $_cache;
	}

	public function getDefaultXMLParams()
	{
		static $_cache = null;

		if (! $_cache) {

			$manifest = JPATH_ROOT . '/components/com_easyblog/views/latest/tmpl/default.xml';
			$fieldsets = EB::form()->getManifest($manifest);

			$obj = new stdClass();

			foreach($fieldsets as $fieldset) {
				foreach($fieldset->fields as $field) {
					$obj->{$field->attributes->name} = $field->attributes->default;
				}
			}

			$_cache = new JRegistry($obj);
		}

		return $_cache;
	}

	/**
	 * Custom menu params as this method will intelligently determine which menu params the menu should be inheriting from
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCustomMenuParams($menuId, JRegistry $params, $prefix)
	{
		static $items = array();
		$config = EB::config();
		$index = $menuId . $prefix;

		if (!isset($items[$index])) {

			// First we convert all the JRegistry parameters into an array
			// so that we can get the key / value mapping
			$properties = $params->toArray();

			// Normalize all the values as they shouldn't be containing -1.
			foreach ($properties as $key => $val) {

				// Inherit from settings > layout
				if ($val == '-1') {
					$params->set($key, $config->get($prefix . '_' . $key));
				}
			}

			$items[$index] = $params;
		}

		return $items[$index];
	}

	/**
	 * Retrieve menu items associated with a post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMenusByPostId($id)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT ' . $db->qn('id') . ' FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE';
		$query[] = '(';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=entry&id=' . $id . '%');
		$query[] = 'OR';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=entry&id=' . (int) $id . '%');
		$query[] = ')';
		$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = EBR::getLanguageQuery();
		$query[] = 'LIMIT 1';

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieve menu items associated with categories
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMenusByAllCategory()
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT ' . $db->qn('id') . ' FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE';
		$query[] = '(';
		$query[] = $db->qn('link') . '=' . $db->Quote('index.php?option=com_easyblog&view=categories');
		$query[] = 'OR';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=categories&limit%');
		$query[] = ')';
		$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = EBR::getLanguageQuery();
		$query[] = 'LIMIT 1';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieve menu items associated with categories
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMenusByCategoryId($id)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT ' . $db->qn('id') . ' FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE';
		$query[] = '(';
		$query[] = $db->qn('link') . '=' . $db->Quote('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $id);
		$query[] = 'OR';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $id . '&limit%');
		$query[] = 'OR';
		$query[] = $db->qn('link') . '=' . $db->Quote('index.php?option=com_easyblog&view=categories&layout=listings&id=' . (int) $id);
		$query[] = ')';
		$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = EBR::getLanguageQuery();
		$query[] = 'LIMIT 1';

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieve menu items associated with categories
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMenusByBloggerId($id)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT ' . $db->qn('id') . ' FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE';
		$query[] = '(';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $id . '%');
		$query[] = 'OR';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . (int) $id . '%');
		$query[] = ')';
		$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = EBR::getLanguageQuery();
		$query[] = 'LIMIT 1';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieve menu items associated with team blogs
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMenusByTagId($id)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT ' . $db->qn('id') . ' FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE';
		$query[] = '(';
		$query[] = $db->qn('link') . '=' . $db->Quote('index.php?option=com_easyblog&view=tags&layout=tag&id=' . $id);
		$query[] = 'OR';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=tags&layout=tag&id=' . $id . '&limit%');
		$query[] = 'OR';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=tags&layout=tag&id=' . (int) $id);
		$query[] = 'OR';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=tags&layout=tag&id=' . (int) $id . '&limit%');
		$query[] = ')';
		$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = EBR::getLanguageQuery();
		$query[] = 'LIMIT 1';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieve menu items associated with team blogs
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMenus($view, $layout = null)
	{
		$db = EB::db();

		$layout = is_null($layout) ? '' : '&layout=' . $layout;

		$query = array();
		$query[] = 'SELECT ' . $db->qn('id') . ' FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE';
		$query[] = $db->qn('link') . '=' . $db->Quote('index.php?option=com_easyblog&view=' . $view . $layout);
		$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = EBR::getLanguageQuery();
		$query[] = 'LIMIT 1';

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieve menu items associated with team blogs
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMenusByTeamId($id)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT ' . $db->qn('id') . ' FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE';
		$query[] = '(';
		$query[] = $db->qn('link') . '=' . $db->Quote('index.php?option=com_easyblog&view=teamblog&layout=listings&id=' . $id);
		$query[] = 'OR';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=teamblog&layout=listings&id=' . $id . '&limit%');
		$query[] = ')';
		$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = EBR::getLanguageQuery();
		$query[] = 'LIMIT 1';

		$query = implode(' ', $query);
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}
}
