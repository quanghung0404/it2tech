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

FD::import( 'admin:/includes/apps/apps' );

class SocialFieldsRelationshipstatus
{
	public function __construct()
	{
		parent::__construct();
	}

	/*
	 * During the initial installation, EasySocial will
	 * automatically call up this function.
	 *
	 * @return	string	Return a string so that EasySocial
	 */
	public function install()
	{
		/*
		 * Run something here if necessary
		 */
		 return true;
	}

	/*
	 * When there is an error, EasySocial will callback this function
	 *
	 * @param	string	$message	Error message
	 * @param	int		$code		Error codes
	 *
	 * @return	string	Return a string so that EasySocial
	 */
	public function error()
	{
		parent::error();
	}

	/*
	 * When installation is successful, this method will be called
	 *
	 * @param	string	$message	Error message
	 * @param	int		$code		Error codes
	 *
	 * @return	string	Return a string so that EasySocial
	 */
	public function success()
	{
		/*
		 * If you need to run anything after successful installations.
		 */
		parent::success();
	}

	/*
	 * Method will be invoked when an export is executed.
	 * Child must return the appropriate values to be exported.
	 */
	public function onExport()
	{
	}

	/*
	 * Method will be invoked when an import is executed.
	 * Child must return the appropriate values to be exported.
	 */
	public function onImport()
	{
	}

	/*
	 * Provide boolean status for the result
	 */
	public function onValidate( $value , $node )
	{
	    return true;
	}

	/*
	 * Responsible to output the html codes that is displayed to
	 * a user when they edit their profile.
	 *
	 * @param
	 */
	public function onEdit( &$post, &$user, $errors )
	{
	    JTable::addIncludePath( dirname( __FILE__ ) );
	    $relation   = JTable::getInstance( 'Relations' , 'SocialTable' );
		$relation->load( $person->get( 'id' ) );

		$this->set( 'relation' , $relation );

		return $this->display( 'fields.relationship_status.form' );
	}

	/*
	 * Save trigger which is called before really saving the object.
	 */
	public function onBeforeSave( $post , $person )
	{
		return true;
	}

	/*
	 * Save trigger which is called after really saving the object.
	 */
	public function onAfterSave( $post , $person )
	{
		$type   = $post[$this->element][ 'relation_type'];
	    $target	= $post[$this->element][ 'relation_with' ];

	    JTable::addIncludePath( dirname( __FILE__ ) );
	    $relation   = JTable::getInstance( 'Relations' , 'SocialTable' );
		$state      = $relation->load( $person->get( 'id' ) );

	    $relation->actor    = $person->get( 'id' );
	    $relation->target   = $target;
	    $relation->type     = $type;

	    $relation->store( $state );

		// @TODO: Send a notification to the target
	    return true;
	}

	/*
	 * Responsible to output the html codes that is displayed to
	 * a user when they edit their profile.
	 *
	 * @param
	 */
	public function onDisplay()
	{
		return $this->display( 'fields.relationship_status.html' );
	}
}
