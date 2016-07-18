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

// We need the router
require_once( JPATH_ROOT . '/components/com_content/helpers/route.php' );

/**
 * Files app view for groups
 *
 * @since	1.0
 * @access	public
 */
class FilesViewGroups extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user id that is currently being viewed.
	 */
	public function display( $groupId = null , $docType = null )
	{
		$group		= FD::group( $groupId );

		// Only allow group members access here.
		if( !$group->isMember() )
		{
			return $this->redirect( $group->getPermalink( false ) );
		}

		// Load up the explorer library.
		$explorer 	= FD::explorer( $group->id , SOCIAL_TYPE_GROUP );

		// Get total number of files that are already uploaded in the group
		$model 		= FD::model( 'Files' );
		$total 		= (int) $model->getTotalFiles( $group->id , SOCIAL_TYPE_GROUP );

		$access			= $group->getAccess();
		$allowUpload	= $access->get( 'files.max' ) == 0 || $total < $access->get( 'files.max' ) ? true : false;
		$uploadLimit 	= $access->get( 'files.maxsize' );

		$this->set( 'uploadLimit'	, $uploadLimit );
		$this->set( 'allowUpload'	, $allowUpload );
		$this->set( 'explorer'		, $explorer );
		$this->set( 'group'			, $group );

		echo parent::display( 'groups/default' );
	}
}
