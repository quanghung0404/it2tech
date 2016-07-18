<?php
/**
 * @version    $Id: jsn_is_profile.php 16077 2012-09-17 02:30:25Z giangnd $
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
 * Profile Class
 *
 * @package  JSN.ImageShow
 * @since    2.5
 *
 */

class JSNISProfile
{
	private $_db = null;

	/**
	 * Contructor
	 *
	 */

	public function JSNISProfile()
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

		if (empty($instances['JSNISProfile']))
		{
			$instance	= new JSNISProfile;
			$instances['JSNISProfile'] = &$instance;
		}

		return $instances['JSNISProfile'];
	}

	/**
	 * Get all profiles in JSN ImageShow
	 *
	 * @param   string  $title  The title of profile
	 * @param   string  $name   The name of profile
	 *
	 * @return array a array of objects
	 */

	public function getProfiles($title, $name)
	{
		JPluginHelper::importPlugin('jsnimageshow');
		$dispatcher = JDispatcher::getInstance();
		$agr 		= array(array('title' => $title, 'name' => $name));
		$queries 	= $dispatcher->trigger('onGetQueryProfile', $agr);

		if (count($queries))
		{
			$runQuery = array();

			foreach ($queries as $value)
			{
				if (!empty($value))
				{
					$runQuery[] = $value;
				}
			}

			$query = implode(' UNION ALL ', $runQuery);

			if (empty($query))
			{
				return array();
			}

			$this->_db->setQuery($query);
			$result = $this->_db->loadObjectList();

			if (!is_array($result))
			{
				$result = array();
			}

			$data = array();

			foreach ($result as $item)
			{
				$query = 'SELECT count(p.external_source_profile_id) as total, p.external_source_profile_id
						  FROM #__imageshow_source_profile p
						  INNER JOIN #__imageshow_external_source_' . $item->image_source_name . ' source
						  	ON source.external_source_id = p.external_source_id
						  INNER JOIN #__imageshow_showlist sl
						  	ON sl.image_source_profile_id = p.external_source_profile_id
		    			  WHERE
		    			  		sl.image_source_name = ' . $this->_db->quote($item->image_source_name) . '
		    			  	AND
		    			  		p.external_source_id = ' . (int) $item->external_source_id . '
	    			  		GROUP BY
	    			  			p.external_source_profile_id';

				$this->_db->setQuery($query);
				$result = $this->_db->loadObject();

				if ($result)
				{
					$item->totalshowlist 			  = $result->total;
					$item->external_source_profile_id = $result->external_source_profile_id;
				}
				else
				{
					$item->totalshowlist 			  = 0;
					$item->external_source_profile_id = null;
				}

				$data[] = $item;
			}

			return $data;
		}

		return array();
	}

	/**
	 * Delete a specifed profile
	 *
	 * @param   int     $sourceID    The ID of source
	 * @param   string  $sourceName  The name of source
	 *
	 * @return void
	 */

	public function deleteProfile($sourceID, $sourceName)
	{
		$query = 'SELECT sl.showlist_id FROM #__imageshow_source_profile p
				  INNER JOIN #__imageshow_showlist sl
				  	ON sl.image_source_profile_id = p.external_source_profile_id
				  WHERE
				  		p.external_source_id = ' . (int) $sourceID . '
				  	AND
				  		sl.image_source_name = ' . $this->_db->quote($sourceName);

		$this->_db->setQuery($query);

		$result = $this->_db->loadObjectList();

		if (count($result))
		{
			$showlistTable = JTable::getInstance('showlist', 'Table');

			foreach ($result as $showlist)
			{
				if ($showlistTable->load($showlist->showlist_id))
				{
					$imageSource = JSNISFactory::getSource($showlistTable->image_source_name, $showlistTable->image_source_type, $showlistTable->showlist_id);

					$imageSource->removeAllImages(array('showlist_id' => $showlistTable->showlist_id));
					$imageSource->_source['profileTable']->delete();

					$showlistTable->image_source_name = '';
					$showlistTable->image_source_type = '';
					$showlistTable->image_source_profile_id = 0;
					$showlistTable->store();
				}
			}
		}

		//remove source
		$query = 'DELETE FROM #__imageshow_external_source_' . $sourceName . '
				  WHERE external_source_id = ' . (int) $sourceID;

		$this->_db->setQuery($query);
		$this->_db->query();

	}

	/**
	 * Check whether the profile exists or not
	 *
	 * @param   string  $title           The title of profile
	 * @param   string  $source          The name of source
	 * @param   int     $ignoreSourceID  The source ids will be ignored
	 *
	 * @return boolean
	 */

	public function checkExternalProfileExist($title, $source, $ignoreSourceID = 0)
	{
		$condition = '';

		if ($ignoreSourceID)
		{
			$condition = ' AND external_source_id <> ' . (int) $ignoreSourceID . ' ';
		}

		$query = 'SELECT * FROM #__imageshow_external_source_' . $source . ' WHERE external_source_profile_title LIKE ' . $this->_db->quote($title) . $condition;
		$this->_db->setQuery($query);

		$result = $this->_db->loadResult();

		return ($result) ? true : false;
	}

	/**
	 * Get total of showlist item base on profile ID
	 *
	 * @param   int  $profileID  The ID of profile
	 *
	 * @return array
	 */

	public function countShowlistBaseOnProfileID($profileID)
	{
		$query = 'SELECT COUNT(showlist_id) FROM #__imageshow_showlist WHERE image_source_profile_id =' . (int) $profileID;
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Save parameter of a profile
	 *
	 * @param array $data a array of values
	 *
	 * @return bool
	 */

	public function saveProfileParameter($data)
	{
		$params		= json_decode($data['profile_parameter']);
		$params->number_of_images_on_loading = $data['number_of_images_on_loading'];
		$params 	= json_encode($params);
		$query 		= 'UPDATE #__extensions SET params = \'' . $params . '\' WHERE element = "' . $data['image_source'] . '" AND folder = "jsnimageshow"';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
}
