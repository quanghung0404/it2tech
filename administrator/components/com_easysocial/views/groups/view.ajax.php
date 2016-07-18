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

// Include the main views class
FD::import( 'admin:/views/views' );

class EasySocialViewGroups extends EasySocialAdminView
{
	/**
	 * Displays a dialog confirmation before deleting a group category
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function confirmDeleteCategory()
	{
		$ajax 	= FD::ajax();
		$theme 	= FD::themes();

		$contents	= $theme->output( 'admin/groups/dialog.delete.category' );
		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the new group creation form as we need the admin to select a category.
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function createDialog()
	{
		$ajax 	= FD::ajax();
		$theme	= FD::themes();

		// Get a list of categories
		$model	 = FD::model( 'GroupCategories' );
		$categories	= $model->getCategories( array( 'state' => SOCIAL_STATE_PUBLISHED, 'ordering' => 'ordering' ) );

		$theme->set( 'categories' , $categories );
		$contents 	= $theme->output( 'admin/groups/dialog.create.group' );

		return $ajax->resolve( $contents );
	}


	/**
	 * Allows caller to delete a category avatar
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmRemoveCategoryAvatar()
	{
		$ajax 	= FD::ajax();

		$theme 	= FD::themes();
		$id		= JRequest::getInt( 'id' );

		$theme->set( 'id' , $id );
		$contents 	= $theme->output( 'admin/groups/dialog.remove.category.avatar' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the owner switching form
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function switchOwner()
	{
		$ajax 	= FD::ajax();
		$theme	= FD::themes();

		$ids 	= JRequest::getVar( 'ids' );

		$theme->set( 'ids' , $ids );
		$contents 	= $theme->output( 'admin/groups/dialog.browse.users' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the owner switching form
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function confirmSwitchOwner()
	{
		$ajax 	= FD::ajax();
		$theme	= FD::themes();

		$ids 		= JRequest::getVar( 'id' );
		$userId 	= JRequest::getInt( 'userId' );
		$newOwner 	= FD::user( $userId );

		$theme->set( 'ids'		, $ids );
		$theme->set( 'user'		, $newOwner );

		$contents 	= $theme->output( 'admin/groups/dialog.switch.owner' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the new group creation form as we need the admin to select a category.
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function deleteConfirmation()
	{
		$ajax 	= FD::ajax();
		$theme	= FD::themes();

		$contents 	= $theme->output( 'admin/groups/dialog.delete.group' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Return the reformed data during save fields
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.1
	 * @access public
	 * @param  Array    $data The step and fields data
	 */
	public function saveFields( $data )
	{
		if( $data === false )
		{
			return FD::ajax()->reject( $this->getError() );
		}

		FD::ajax()->resolve( $data );
	}

	/**
	 * Allows caller to browse groups
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function browse()
	{
		$ajax 		= FD::ajax();
		$callback	= JRequest::getVar( 'jscallback' , '' );

		$theme 	= FD::themes();
		$theme->set( 'callback' , $callback );
		$content 	= $theme->output( 'admin/groups/dialog.browse' );

		return $ajax->resolve( $content );
	}

	/**
	 * Allows caller to browse a category via the internal dialog system
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function browseCategory()
	{
		$ajax 		= FD::ajax();
		$callback	= JRequest::getVar( 'jscallback' , '' );

		$theme 	= FD::themes();
		$theme->set( 'callback' , $callback );
		$content 	= $theme->output( 'admin/groups/dialog.browse.category' );

		return $ajax->resolve( $content );
	}

	/**
	 * Displays the reject dialog
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function rejectGroup()
	{
		$ajax 	= FD::ajax();

		// Get the group ids that should be rejected
		$ids 	= JRequest::getVar( 'ids' );
		$ids 	= FD::makeArray( $ids );

		$theme 	= FD::themes();
		$theme->set( 'ids' , $ids );
		$contents 	= $theme->output( 'admin/groups/dialog.reject' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the approve dialog
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function approveGroup()
	{
		$ajax 	= FD::ajax();

		// Get the group ids that should be rejected
		$ids 	= JRequest::getVar( 'ids' );
		$ids 	= FD::makeArray( $ids );

		$theme 	= FD::themes();
		$theme->set( 'ids' , $ids );
		$contents 	= $theme->output( 'admin/groups/dialog.approve' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Show user listing to add users into the group
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 */
	public function addMembers()
	{
		$ajax = FD::ajax();

		$theme = FD::themes();
		$contents = $theme->output('admin/groups/dialog.browse.addusers');
		return $ajax->resolve($contents);
	}

	/**
	 * Show confirmation to add users into the group
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 */
	public function confirmAddMembers()
	{
		$ajax = FD::ajax();

		$theme = FD::themes();

		$members = JRequest::getVar('members');

		$theme->set('members', $members);

		if (empty($members)) {
			$contents = $theme->output('admin/groups/dialog.empty.addusers');

			return $ajax->resolve($contents);
		}

		$userids = array();

		foreach ($members as $id => $member) {
			$userids[] = $id;
		}

		$theme->set('userids', FD::json()->encode($userids));

		$groupid = JRequest::getInt('groupid');

		$theme->set('groupid', $groupid);

		$contents = $theme->output('admin/groups/dialog.confirm.addusers');

		return $ajax->resolve($contents);
	}

	public function switchCategory()
	{
	    $theme = FD::themes();

	    $ids = $this->input->getVar('ids');

	    $theme->set('ids', $ids);

	    $categories = FD::model('GroupCategories')->getCategories(array('state' => SOCIAL_STATE_PUBLISHED, 'ordering' => 'ordering'));

	    $theme->set('categories', $categories);

	    $contents = $theme->output('admin/groups/dialog.switchCategory.browse');

	    return $this->ajax->resolve($contents);
	}
}
