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

class EasySocialModelTasks extends EasySocialModel
{
	public function __construct( $config = array() )
	{
		parent::__construct( 'tasks' , $config );
	}

	/**
	 * Deletes all tasks from a milestone
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int		The milestone id
	 * @return	boolean
	 */
	public function deleteTasks( $milestoneId )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->delete( '#__social_tasks' );
		$sql->where( 'milestone_id' , $milestoneId );

		$db->setQuery( $sql );

		return $db->Query();
	}

	/**
	 * Retrieves a list of tasks from a milestone
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int		The milestone id
	 * @return
	 */
	public function getTasks( $milestoneId  , $options = array() )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_tasks' );
		$sql->where( 'milestone_id' , $milestoneId );

		// Determines if we should only fetch open tasks
		$open 	= isset( $options[ 'open' ] ) ? $options[ 'open' ] : false;
		$closed = isset( $options[ 'closed' ] ) ? $options[ 'closed' ] : false;

		if( $open )
		{
			$sql->where( 'state' , SOCIAL_STATE_PUBLISHED );
		}

		if( $closed )
		{
			$sql->where( 'state' , 2 );
		}

		$sql->order( 'created' , 'DESC' );

		$db->setQuery( $sql );

		$rows 	= $db->loadObjectList();

		if( !$rows )
		{
			return $rows;
		}

		$tasks 	= array();

		foreach( $rows as $row )
		{
			$task 	= FD::table( 'Task' );
			$task->bind( $row );

			$tasks[]	= $task;
		}

		return $tasks;
	}

	/**
	 * Retrieves a list of milestones for a node
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMilestones( $uid , $type , $options = array() )
	{
		$db 	= FD::db();
		$query 	= $db->sql();

		$query->select( '#__social_tasks_milestones', 'a' );
		$query->column('a.*');

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    $query->leftjoin( '#__social_block_users' , 'bus');
		    $query->on( 'a.owner_id' , 'bus.user_id' );
		    $query->on( 'bus.target_id', JFactory::getUser()->id );
		    $query->isnull('bus.id');
		}

		$query->where( 'a.uid' , $uid );
		$query->where( 'a.type', $type );

		// Should we fetch completed milestones?
		$completed 	= isset( $options[ 'completed' ] ) ? $options[ 'completed' ] : false;

		if( !$completed )
		{
			$query->where( 'a.state' , SOCIAL_STATE_PUBLISHED );
		}

		$db->setQuery( $query );

		$rows 	= $db->loadObjectList();

		if( !$rows )
		{
			return $rows;
		}

		$milestones 	= array();

		foreach( $rows as $row )
		{
			$milestone 	= FD::table( 'Milestone' );
			$milestone->bind( $row );

			$milestones[]	= $milestone;
		}

		return $milestones;
	}

	/**
	 * Retrieves the total number of tasks a milestone has
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int 	The milestone id
	 * @return	int
	 */
	public function getTotalTasks( $milestoneId , $options = array() )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_tasks' );
		$sql->column( 'COUNT(1)' );
		$sql->where( 'milestone_id' , $milestoneId );

		$open 	= isset( $options[ 'open' ] ) ? $options[ 'open' ] : false;
		$closed = isset( $options[ 'closed' ] ) ? $options[ 'closed' ] : false;

		if( $open )
		{
			$sql->where( 'state' , SOCIAL_TASK_UNRESOLVED );
		}

		if( $closed )
		{
			$sql->where( 'state' , SOCIAL_TASK_RESOLVED );
		}

		$db->setQuery( $sql );
		$total 	= $db->loadResult();

		return $total;
	}

	/**
	 * Retrieves a list of tasks created by a particular user.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int		$userId		The user's / creator's id.
	 *
	 * @return	Array				A list of notes item.
	 */
	public function getItems( $userId )
	{
		$db = FD::db();

		// Get sql helper.
		$query = $db->sql();

		// Select the table.
		$query->select('#__social_tasks');

		// Build the where.
		$query->where('user_id' , $userId);

		// Execute the query.
		$db->setQuery($query);

		// Get the result.
		$tasks = $db->loadObjectList();

		return $tasks;
	}
}
