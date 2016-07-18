<?php
/**
 * @version    $Id: jsn_is_parameter.php 16077 2012-09-17 02:30:25Z giangnd $
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
 * Parameter Class
 *
 * @package  JSN.ImageShow
 * @since    2.5
 *
 */

class JSNISParameter
{
	private $_db = null;

	/**
	 * Contructor
	 *
	 */

	public function JSNISParameter()
	{
		if ($this->_db == null)
		{
			$this->_db = JFactory::getDBO();
		}
	}

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

		if (empty($instances['JSNISParameter']))
		{
			$instance	= new JSNISParameter;
			$instances['JSNISParameter'] = &$instance;
		}

		return $instances['JSNISParameter'];
	}

	/**
	 * Get parameters from parameters table
	 *
	 * @return a object
	 */

	public function getParameters()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		try
		{
			$query->select($db->quoteName('name') . ', ' . $db->quoteName('value'));
			$query->from($db->quoteName('#__jsn_imageshow_config'));
			$query->where($db->quoteName('name') . ' IN (' . $db->quote('show_quick_icons') . ', ' . $db->quote('enable_update_checking') . ', ' . $db->quote('number_of_images_on_loading') . ')');
			$db->setQuery($query);
			$result = $db->loadObjectList();
			$params = new stdClass;
			if (count($result))
			{
				foreach ($result as $item)
				{
					switch($item->name)
					{
						case 'show_quick_icons':
							$params->show_quick_icons = $item->value;
							break;
						case 'enable_update_checking':
							$params->enable_update_checking = $item->value;
							break;
						case 'number_of_images_on_loading':
							$params->number_of_images_on_loading = $item->value;
							break;
					}
				}
			}
			else
			{
				$params->show_quick_icons = '1';
				$params->enable_update_checking = '1';
				$params->number_of_images_on_loading = '30';
			}

			return $params;
		}
		catch (Exception $e)
		{
			$params = new stdClass;
			$params->show_quick_icons = '1';
			$params->enable_update_checking = '1';
			$params->number_of_images_on_loading = '30';
			return $params;
		}
	}

	/**
	 * Save parameters to parameters table
	 *
	 * @param   array  $post  a array of value
	 *
	 * @return void
	 */

	public function saveParameters($post)
	{
		$allow = false;
		// Check show_quick_icons
		$query = $this->_db->getQuery(true);
		$query->select($this->_db->quoteName('value'));
		$query->from($this->_db->quoteName('#__jsn_imageshow_config'));
		$query->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote('show_quick_icons'));
		$this->_db->setQuery($query);
		$resultCheck = $this->_db->loadObject();
		if (!empty($resultCheck))
		{
			$query->clear();
			$query->update($this->_db->quoteName('#__jsn_imageshow_config'));
			$query->set($this->_db->quoteName('value') . ' = ' . $this->_db->quote((int) $post['show_quick_icons']));
			$query->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote('show_quick_icons'));
			$this->_db->setQuery($query);
			if ($this->_db->query())
			$allow = true;
			else
			$allow = false;
		} else {
			$query->clear();
			$query->insert($this->_db->quoteName('#__jsn_imageshow_config'));
			$query->columns(array($this->_db->quoteName('name'), $this->_db->quoteName('value')));
			$query->values($this->_db->quote('show_quick_icons') . ', ' . $this->_db->quote((int) $post['show_quick_icons']));
			$this->_db->setQuery($query);
			if ($this->_db->query())
			$allow = true;
			else
			$allow = false;
		}

		// Check enable_update_checking
		$query = $this->_db->getQuery(true);
		$query->select($this->_db->quoteName('value'));
		$query->from($this->_db->quoteName('#__jsn_imageshow_config'));
		$query->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote('enable_update_checking'));
		$this->_db->setQuery($query);
		$resultCheck = $this->_db->loadObject();
		if (!empty($resultCheck))
		{
			$query->clear();
			$query->update($this->_db->quoteName('#__jsn_imageshow_config'));
			$query->set($this->_db->quoteName('value') . ' = ' . $this->_db->quote((int) $post['enable_update_checking']));
			$query->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote('enable_update_checking'));
			$this->_db->setQuery($query);
			if ($this->_db->query())
			$allow = true;
			else
			$allow = false;
		} else {
			$query->clear();
			$query->insert($this->_db->quoteName('#__jsn_imageshow_config'));
			$query->columns(array($this->_db->quoteName('name'), $this->_db->quoteName('value')));
			$query->values($this->_db->quote('enable_update_checking') . ', ' . $this->_db->quote((int) $post['enable_update_checking']));
			$this->_db->setQuery($query);
			if ($this->_db->query())
			$allow = true;
			else
			$allow = false;
		}

		// Check number_of_images_on_loading
		$query = $this->_db->getQuery(true);
		$query->select($this->_db->quoteName('value'));
		$query->from($this->_db->quoteName('#__jsn_imageshow_config'));
		$query->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote('number_of_images_on_loading'));
		$this->_db->setQuery($query);
		$resultCheck = $this->_db->loadObject();
		if (!empty($resultCheck))
		{
			$query->clear();
			$query->update($this->_db->quoteName('#__jsn_imageshow_config'));
			$query->set($this->_db->quoteName('value') . ' = ' . $this->_db->quote((int) $post['number_of_images_on_loading']));
			$query->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote('number_of_images_on_loading'));
			$this->_db->setQuery($query);
			if ($this->_db->query())
			$allow = true;
			else
			$allow = false;
		} else {
			$query->clear();
			$query->insert($this->_db->quoteName('#__jsn_imageshow_config'));
			$query->columns(array($this->_db->quoteName('name'), $this->_db->quoteName('value')));
			$query->values($this->_db->quote('number_of_images_on_loading') . ', ' . $this->_db->quote((int) $post['number_of_images_on_loading']));
			$this->_db->setQuery($query);
			if ($this->_db->query())
			$allow = true;
			else
			$allow = false;
		}

		// Check ask to review
		$query = $this->_db->getQuery(true);
		$query->select($this->_db->quoteName('value'));
		$query->from($this->_db->quoteName('#__jsn_imageshow_config'));
		$query->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote('review_popup'));
		$this->_db->setQuery($query);
		$resultCheck = $this->_db->loadObject();
		if (!empty($resultCheck))
		{
			$query->clear();
			$query->update($this->_db->quoteName('#__jsn_imageshow_config'));
			$query->set($this->_db->quoteName('value') . ' = ' . $this->_db->quote((int) $post['review_popup']));
			$query->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote('review_popup'));
			$this->_db->setQuery($query);
			if ($this->_db->query())
			$allow = true;
			else
			$allow = false;
		} else {
			$query->clear();
			$query->insert($this->_db->quoteName('#__jsn_imageshow_config'));
			$query->columns(array($this->_db->quoteName('name'), $this->_db->quoteName('value')));
			$query->values($this->_db->quote('review_popup') . ', ' . $this->_db->quote((int) $post['review_popup']));
			$this->_db->setQuery($query);
			if ($this->_db->query())
			$allow = true;
			else
			$allow = false;
		}

		return $allow;
	}
}
