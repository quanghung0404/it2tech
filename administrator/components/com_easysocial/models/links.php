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

class EasySocialModelLinks extends EasySocialModel
{
	private $data			= null;

	public function __construct( $config = array() )
	{
		parent::__construct( 'links' , $config );
	}

	/**
	 * Purges the URL cache from the site
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool	True on success, false otherwise.
	 */
	public function clear()
	{
		$db 	= FD::db();

		$sql 	= $db->sql();
		$sql->delete( '#__social_links' );

		$db->setQuery( $sql );
		return $db->Query();
	}

	/**
	 * Purges the URL cache from the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The number of days interval
	 * @return	bool	True on success, false otherwise.
	 */
	public function clearExpired( $interval )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();
		$date 	= FD::date();

		$query 	= 'DELETE FROM `#__social_links` WHERE DATE_ADD( `created` , INTERVAL ' . $interval . ' DAY) <= ' . $db->Quote( $date->toMySQL() );

		$sql->raw( $query );

		$db->setQuery( $sql );

		return $db->Query();
	}

	/**
	 * Retrieves a list of cached images
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getCachedImages($options = array())
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->select('#__social_links_images');

		if (isset($options['storage'])) {
			$sql->where('storage', $options['storage']);	
		}

		if (isset($options['exclusion']) && !empty($options['exclusion'])) {
			$sql->where('id', $options['exclusion'], 'NOT IN');
		}

		if (isset($options['limit'])) {
			$sql->limit($options['limit']);
		}

		$db->setQuery($sql);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$images = array();

		foreach ($result as $row) {
			$linkImage = FD::table('LinkImage');
			$linkImage->bind($row);

			$images[] = $linkImage;
		}
				
		return $images;
	}

	/**
	 * Retrieves the list of items which stored in Amazon
	 *
	 * @since	1.4.6
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLinkImagesStoredExternally($storageType = 'amazon')
	{
		// Get the number of files to process at a time
		$config = ES::config();
		$limit = $config->get('storage.amazon.limit', 10);

		$db = FD::db();
		$sql = $db->sql();
		$sql->select('#__social_links_images');
		$sql->where('storage', $storageType);
		$sql->limit($limit);

		$db->setQuery($sql);

		$result = $db->loadObjectList();

		return $result;
	}	
}
