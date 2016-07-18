<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

jimport('joomla.application.component.model');

FD::import( 'admin:/includes/model' );

class EasySocialModelClusterCategory extends EasySocialModel
{
	public function __construct( $config = array() )
	{
		parent::__construct( 'clustercategory' , $config );
	}

	/**
	 * Inserts new access for a cluster category
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function insertAccess($categoryId, $type = 'create', $profiles = array())
	{
		$db 	= FD::db();

		// Delete all existing access type first
		$sql 	= $db->sql();
		$sql->delete( '#__social_clusters_categories_access');
		$sql->where('category_id', $categoryId);
		$sql->where('type', $type);

		$db->setQuery($sql);
		$db->Query();

		if (!$profiles) {
			return;
		}

		foreach($profiles as $id)
		{
			$sql->clear();
			$sql->insert('#__social_clusters_categories_access');
			$sql->values('category_id', $categoryId);
			$sql->values('type', $type);
			$sql->values('profile_id', $id);

			$db->setQuery($sql);
			$db->Query();
		}

		return true;
	}

	/**
	 * Determines if a profile is allowed to access to this category
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasAccess($categoryId, $type = 'create', $profileId)
	{
		$db 	= FD::db();

		// Check if the category has any access
		$sql 	= $db->sql();
		$sql->select('#__social_clusters_categories_access', 'a');
		$sql->column('count(1)');
		$sql->where('a.category_id', $categoryId);
		$sql->where('a.type', $type);

		$db->setQuery($sql);
		$exists = $db->loadResult();

		// If no access configured, return true always.
		if (!$exists) {
			return true;
		}

		// Delete all existing access type first
		$sql->clear();
		$sql->select('#__social_clusters_categories_access', 'a');
		$sql->where('a.category_id', $categoryId);
		$sql->where('a.profile_id', $profileId);
		$sql->where('a.type', $type);

		$db->setQuery($sql);
		$exists = $db->loadResult();

		return $exists;
	}

	/**
	 * Deletes all access related to a category
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteAccess($categoryId)
	{
		$db 	= FD::db();

		// Delete all existing access type first
		$sql 	= $db->sql();
		$sql->delete( '#__social_clusters_categories_access');
		$sql->where('category_id', $categoryId);

		$db->setQuery($sql);
		return $db->Query();
	}

	/**
	 * Retrieves a list of profile id's associated with the category
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAccess($categoryId, $type = 'create')
	{
		$db 	= FD::db();

		$sql 	= $db->sql();
		$sql->select('#__social_clusters_categories_access');
		$sql->column('profile_id');
		$sql->where('category_id', $categoryId);
		$sql->where('type', $type);

		$db->setQuery($sql);

		$ids 	= $db->loadColumn();

		return $ids;
	}

	public function preloadCategory($catIds)
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = "select * from `#__social_clusters_categories` where id in (" . implode(",", $catIds) . ")";
		$sql->raw($query);

		$db->setQuery($sql);

		$results = $db->loadObjectList();

		return $results;
	}
}
