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

$version 	= FD::getInstance( 'Version' )->getVersion();

if( $version >= '3.0' )
{
	class EasySocialModelMain extends JModelLegacy
	{
	}
}
else
{
	class EasySocialModelMain extends JModel
	{
	}
}

/**
 * Provides a set of helper methods to child classes.
 *
 * This class should not be instantiated.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class EasySocialModel extends EasySocialModelMain
{
	/**
	 * Total number of records.
	 * @var	int
	 */
	protected $total	= null;

	/**
	 * The database layer from Joomla.
	 * @var	JDatabase
	 */
	protected $db 		= null;

	/**
	 * The pagination object.
	 * @var SocialPagination
	 */
	protected $pagination	= null;

	/**
	 * The element name.
	 * @var string
	 */
	protected $element 	= null;
	protected $key 		= null;

	public function __construct($element, $config = array())
	{
		$this->db = FD::db();

		// Set the element
		$this->element = $element;

		// Set the key element for this model.
		$this->key = 'com_easysocial.' . $element;

		// We don't want to load any of the tables path because we use our own FD::table method.
		$options = array('table_path' => JPATH_ROOT . '/libraries/joomla/database/table');

		parent::__construct($options);
	}

	/**
	 * Initializes all the generic states from the form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function initStates()
	{
		$app 			= JFactory::getApplication();
		$config 		= FD::config();
		$jConfig 		= FD::jConfig();

		// Get the system defined limit
		$systemLimit 	= $jConfig->getValue( 'list_limit' );
		$systemLimit 	= $config->get( $this->element . '.limit' , $systemLimit );

		// Get the limit.
		$limit 			= $this->getUserStateFromRequest( 'limit' , $systemLimit , 'int' );

		// Get the limitstart.
		$limitstart 	= $this->getUserStateFromRequest( 'limitstart' , 0 );
		$limitstart 	= ( $limit != 0 ? ( floor( $limitstart / $limit ) * $limit ) : 0 );

		// Get the search
		$search 		= $this->getUserStateFromRequest( 'search' , '' );

		// Get the ordering
		$ordering 		= $this->getUserStateFromRequest( 'ordering' , 'id' );

		// Get the direction
		$direction 		= $this->getUserStateFromRequest( 'direction' , 'DESC' );

		$this->setState( 'direction'	, $direction );
		$this->setState( 'ordering'		, $ordering );
		$this->setState( 'search' 		, $search );
		$this->setState( 'limit'		, $limit );
		$this->setState( 'limitstart'	, $limitstart );
	}

	/**
	 * Get user's state from request
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUserStateFromRequest( $key , $default = '' , $type = 'none')
	{
		$app 	= JFactory::getApplication();

		// Try to get the key first. If the key is not in the request, then the userstate won't go back to empty
		if ($app->input->get($key, null, $type) == null) {
			$app->setUserState($this->key . '.' . $key, null);
		}

		$value	= $app->getUserStateFromRequest( $this->key . '.' . $key , $key , $default , $type );

		return $value;
	}

	/**
	 * Allows caller to pass in an array of data to normalize the data
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalize($data, $key, $default = null)
	{
		if (isset($data[$key])) {
			return $data[$key];
		}

		return $default;
	}

	public function setUserState($key, $value)
	{
		$app = JFactory::getApplication();

		return $app->setUserState($this->key . '.' . $key, $value);
	}

	public function getUserState($key, $default = null)
	{
		$app = JFactory::getApplication();

		return $app->getUserState($this->key . '.' . $key, $default);
	}

	/**
	 * Overrides parent's setState
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setState( $key , $value = null )
	{
		$namespace 	= $this->key . '.' . $key;

		parent::setState( $namespace , $value );
	}

	/**
	 * Retrieve a list of state items
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getState( $keyItem = null , $default = null )
	{
		$key 	= $this->key . '.' .$keyItem;

		$value 	= parent::getState( $key );

		return $value;
	}

	/**
	 * Returns the total number of items for the current query
	 *
	 * @since	1.0
	 * @access	public
	 * @return	int		The total number of records
	 */
	protected function getTotal()
	{
		return $this->total;
	}

	/**
	 * Sets the limit state
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setLimit( $limit = null )
	{
		if( is_null( $limit ) )
		{
			$jConfig 		= FD::jconfig();
			$systemLimit	= $jConfig->getValue( 'list_length' );
			$config = FD::config();

			$app 	= JFactory::getApplication();
			$limit 	= $app->getUserStateFromRequest( 'com_easysocial.' . $this->element . '.limit' , 'limit' , $config->get( $this->element . '.limit' , $systemLimit ) , 'int' );
		}

		$this->setState( 'limit' , $limit );

		return $this;
	}


	/**
	 * Returns the pagination object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialPagination
	 */
	public function getPagination( )
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->pagination ) )
		{
			$limitstart 		= (int) $this->getState( 'limitstart' );
			$limit 				= (int) $this->getState( 'limit' );
			$total 				= (int) $this->getState( 'total' );

			$this->pagination 	= FD::get( 'Pagination' , $total , $limitstart , $limit );
		}

		return $this->pagination;
	}

	/**
	 * Determines the total number of items based on the query given.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The SQL query.
	 * @param	bool		Uses outer count.
	 * @param	string		Any specific column to count against. (Optional)
	 * @return	int			The number of items.
	 *
	 * @author	Mark Lee	<mark@stackideas.com>
	 */
	protected function setTotal( $query , $wrapTemporary = false )
	{
		if( $wrapTemporary )
		{
			$query 	= 'SELECT COUNT(1) FROM (' . $query . ') AS zcount';
		}

		// Debug
		// echo str_ireplace( '#__' , 'jos_' , $query );exit;

		$this->db->setQuery( $query );

		$total		= (int) $this->db->loadResult();

		// Set the total items here.
		$this->setState( 'total' , $total );

		$this->total = $total;

		return $total;
	}

	/**
	 * Determines the total number of items based on the query given.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The SQL query.
	 * @param	bool		Uses outer count.
	 * @param	string		Any specific column to count against. (Optional)
	 * @return	int			The number of items.
	 *
	 * @author	Mark Lee	<mark@stackideas.com>
	 */
	protected function setTotalCount( $total )
	{
		// Set the total items here.
		$this->setState( 'total' , $total );
		$this->total = $total;
		return true;
	}

	/**
	 * If using the pagination query, child needs to use this method.
	 *
	 * @since	1.0
	 * @access	public
	 */
	protected function getData( $query , $debug = false )
	{
		// If enforced to use limit, we get the limitstart values from properties.
		$limit = $this->getState('limit');
		$limitstart 	= $this->getState( 'limitstart' );


		// Check if there's anything wrong with the limitstart because
		// User might be viewing on page 7 but switches a different view and it does not contain 7 pages.
		$total 			= $this->getTotal();

		if( $limitstart > $total )
		{
			$limitstart 	= 0;
			$this->setState( 'limitstart' , 0 );
		}

		$this->db->setQuery( $query , $limitstart , $limit );

		return $this->db->loadObjectList();
	}

	protected function getDataColumn( $query, $useLimit = true)
	{
		// If enforced to use limit, we get the limitstart values from properties.
		$limitstart = $useLimit ? $this->getState( 'limitstart' ) : 0;
		$limit 		= $useLimit ? $this->getState( 'limit' ) : 0;

		$this->db->setQuery( $query , $limitstart , $limit );

		return $this->db->loadColumn();
	}

	protected function bindTable($tableName, $result)
	{
		$binded = array();

		foreach ($result as $row) {
			$table = FD::table($tableName);
			$table->bind($row);

			$binded[] = $table;
		}

		return $binded;
	}
}
