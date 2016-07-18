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

class EasySocialModelSteps extends EasySocialModel
{
	private $data			= null;

	function __construct()
	{
		parent::__construct( 'steps' );
	}

	/**
	 * Creates a default workflow item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique id of the item.
	 * @param	string	The unique type of the item. (E.g: user , group etc)
	 * @return	SocialTableWorkflow
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function createDefaultStep( $uid , $type )
	{
		$step 				= FD::table( 'FieldStep' );

		// Use the default step title.
		$step->title 		= JText::_( 'COM_EASYSOCIAL_STEPS_DEFAULT_STEP' );

		// Set the default description.
		$step->description	= JText::_( 'COM_EASYSOCIAL_STEPS_DEFAULT_DESCRIPTION' );

		// Link the foreign keys.
		$step->uid 			= $uid;
		$step->type 		= $type;

		// Set the default state to be published since this step can never be unpublished.
		$step->state 	= SOCIAL_STATE_PUBLISHED;

		// The sequence will always be 1 since this is the first step created.
		$step->sequence	= 1;

		// Let's store this
		$state 			=	$step->store();

		if( !$state )
		{
			$this->setError( $step->getError() );
			return false;
		}

		return $step;
	}

	public function getFields( $profileId )
	{
		$db		= FD::db();
		$query  = 'SELECT d.* FROM ' . $db->nameQuote( '#__social_fields_steps' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__social_profiles' ) . ' AS b '
				. 'ON b.id=a.profile_id '
				. 'LEFT JOIN ' . $db->nameQuote( '#__social_profile_types_fields' ) . ' AS c '
				. 'ON c.profile_id=b.id '
				. 'LEFT JOIN ' . $db->nameQuote( '#__social_fields' ) . ' AS d '
				. 'ON d.id=c.field_id '
				. 'WHERE a.`id`=' . $db->Quote( $profileId );

		$db->setQuery( $query );

		return $db->loadObjectList();
	}

	public function getProfiles( $options = array() )
	{
		$query  = 'SELECT a.* FROM ' . $this->_db->nameQuote( '#__social_profiles' ) . ' AS a '
				. 'LEFT JOIN #__social_fields_steps AS b '
				. 'ON a.id=b.profile_id '
				. ' WHERE ';
		$sql    = array();


		if( is_array( $options ) )
		{
			foreach( $options as $key => $value )
			{
				$sql[]  = ' a.' . $this->_db->nameQuote( $key ) . '=' . $this->_db->Quote( $value );
			}
		}

		if( !empty( $sql ) )
		{
			$query	.= implode( ' AND ' , $sql );
		}

		$query  .= ' AND b.' . $this->_db->nameQuote( 'profile_id' ) . ' IS NULL';
		return $this->getData( $query );
	}

	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->pagination ) )
		{
			jimport('joomla.html.pagination');
			$this->pagination = new JPagination( $this->total , $this->getState('limitstart') , $this->getState('limit') );
		}

		return $this->pagination;
	}

	/**
	 * Retrieve steps for a specific workflow
	 *
	 * @since	1.0
	 * @access	public
	 * @param   int		The unique id.
	 * @param	string	The unique type.
	 */
	public function getSteps( $uid , $type = SOCIAL_TYPE_PROFILES , $mode = null )
	{
		$db 		= FD::db();
		$sql 		= $db->sql();

		$sql->select( '#__social_fields_steps' );
		$sql->where( 'uid' , $uid );
		$sql->where( 'type', $type );

		// Only filter by mode if it is frontend
		if( !empty( $mode ) )
		{
			$sql->where( 'visible_' . $mode , SOCIAL_STATE_PUBLISHED );
		}

		$sql->order( 'sequence' );

		$db->setQuery( $sql );

		$result 	= $db->loadObjectList();

		$steps 		= array();

		if( !$result )
		{
			return array();
		}

		foreach( $result as $row )
		{
			$step 	= FD::table( 'FieldStep' );
			$step->bind( $row );

			$steps[]    = $step;
		}

		return $steps;
	}

	/*
	 * Retreive custom field groups based on a specific step.
	 *
	 * @param   int     $stepId     The step id.
	 */
	public function getFieldsGroups( $stepId , $type = 'profiletype' )
	{
		$db		= FD::db();

		$query  = 'SELECT a.* '
				. 'FROM ' . $db->nameQuote( '#__social_fields_groups' ) . ' AS a '
				. 'WHERE a.' . $db->nameQuote( 'steps_id' ) . ' = ' . $db->Quote( $stepId ) . ' '
				. 'AND a.' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( SOCIAL_STATE_PUBLISHED );

		$db->setQuery( $query );

		$result		= $db->loadObjectList();

		if( !$result )
		{
			return $result;
		}

		$groups = array();

		foreach( $result as $row )
		{
			$group  = FD::table( 'FieldGroup' );
			$group->bind( $row );
			$groups[]   = $group;
		}
		return $groups;
	}
}
