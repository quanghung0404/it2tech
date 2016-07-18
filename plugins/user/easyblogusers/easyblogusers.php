<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

class plgUserEasyBlogUsers extends JPlugin
{
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Tests if EasyBlog exists
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';
			$exists = JFile::exists($file);

			if ($exists) {
				require_once($file);
			}
		}

		return $exists;
	}

	/**
	 * Triggered when saving a user in Joomla
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onUserAfterSave($data, $isNew, $result, $error)
	{
		if (! $this->exists()) {
			return false;
		}

	    //j.16
	    $this->updateSubscriptions( $data );

		$userId	= JArrayHelper::getValue( $data, 'id', 0, 'int' );

		// Process user subscription
		if ($userId && $result && isset($data['easyblog']) && (count($data['easyblog']))) {

			if (!empty($data['easyblog']['subscribe']) && $data['easyblog']['subscribe'] == '1') {

				$model = EB::model('Subscription');
				$exists = $model->isSiteSubscribedUser($userId , $data['email']);

				if ($exists) {
					// user found update the email address
					$model->updateSiteSubscriptionEmail($exists, $userId, $data['email']);
					return true;
				}

				$model->addSiteSubscription($data['email'], $userId, $data['name']);
				return true;
			}
		}
	}

	public function updateSubscriptions($user)
	{
	    $db = JFactory::getDBO();

	    if (is_object($user)) {
	        $user = get_object_vars($user);
	    }

	    if (!isset($user['id']) && empty($user['id'])) {
			return;
	    }

	    //update subscription tables.
	    $userId = $user['id'];
	    $userFullname = $user['name'];
	    $userEmail = $user['email'];

	    // user subscriptions
	    $query  = 'UPDATE `#__easyblog_subscriptions` SET';
		$query	.= ' `user_id` = ' . $db->Quote($userId);
		$query	.= ', `fullname` = ' . $db->Quote($userFullname);
		$query  .= ' WHERE `email` = ' . $db->Quote($userEmail);
		$query  .= ' AND `user_id` = ' . $db->Quote('0');
		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Invoked before deleting a user
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onUserBeforeDelete($user)
	{
		if (! $this->exists()) {
			return false;
		}


		$mainframe	= JFactory::getApplication();

	    if (is_object($user)) {
	        $user   = get_object_vars( $user );
	    }

		$userId     	= $user['id'];
		$newOwnerShip   = $this->_getnewOwnerShip( $userId );

		$this->ownerTransferCategory( $userId, $newOwnerShip );
		$this->ownerTransferTag( $userId, $newOwnerShip );
		$this->onwerTransferComment( $userId, $newOwnerShip );
		$this->ownerTransferPost( $userId, $newOwnerShip );

		$this->removeAssignedACLGroup( $userId );
		$this->removeAdsenseSetting( $userId );
		$this->removeFeedburnerSetting( $userId );
		$this->removeOAuthSetting( $userId );
		$this->removeFeaturedBlogger( $userId );
		$this->removeTeamBlogUser( $userId );
		$this->removeBloggerSubscription( $userId );
		$this->removeEasyBlogUser( $userId );


	}

	public function _getnewOwnerShip( $curUserId )
	{
	    $econfig     	= EasyBlogHelper::getConfig();

	    // this should get from backend. If backend not defined, get the default superadmin.
	    $user_id		= (EasyBlogHelper::getJoomlaVersion() >= '1.6') ? '42' : '62';

	    $newOwnerShip	= $econfig->get('main_orphanitem_ownership', $user_id);


	    /**
	     * we check if the tobe deleted user is the same user id as the saved user id in config.
	     * 		 if yes, we try to get a next SA id.
	     */

	    if( $curUserId == $newOwnerShip)
	    {
	        // this is no no a big no! try to get the next admin.
			if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
			{
	            $saUsersId  = EasyBlogHelper::getSAUsersIds();
	            if( count($saUsersId) > 0 )
	            {
					for($i = 0; $i < count($saUsersId); $i++)
					{
					    if( $saUsersId[$i] != $curUserId )
					    {
					        $newOwnerShip = $saUsersId[$i];
					        break;
					    }
					}
				}
			}
			else
			{
			    $newOwnerShip = $this->_getSuperAdminId( $curUserId );
			}
	    }


	    $newOwnerShip   = $this->_verifyOnwerShip($newOwnerShip);
	    return $newOwnerShip;
	}

	function _verifyOnwerShip( $newOwnerShip )
	{
	    $db = JFactory::getDBO();

	    $query  = 'SELECT `id` FROM `#__users` WHERE `id` = ' . $db->Quote($newOwnerShip);
	    $db->setQuery($query);
	    $result = $db->loadResult();

	    if(empty($result))
	    {
	        if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
	        {
	            $saUsersId  = EasyBlogHelper::getSAUsersIds();
	            $result     = $saUsersId[0];
	        }
	        else
	        {
	        	$result = $this->_getSuperAdminId();
	        }
	    }

	    return $result;
	}

	function _getSuperAdminId( $curUserId = '')
	{
		$db = JFactory::getDBO();

		$query  = 'SELECT `id` FROM `#__users`';
		$query  .= ' WHERE (LOWER( usertype ) = ' . $db->Quote('super administrator');
		$query  .= ' OR `gid` = ' . $db->Quote('25') . ')';

		if(! empty($curUserId) )
		{
		    $query  .= ' AND `id` != ' . $db->Quote( $curUserId );
		}

		$query  .= ' ORDER BY `id` ASC';
		$query  .= ' LIMIT 1';

		$db->setQuery($query);
		$result = $db->loadResult();

		$result = (empty($result)) ? '62' : $result;
		return $result;
	}

	function ownerTransferCategory( $userId, $newOwnerShip )
	{
	    $db = JFactory::getDBO();

	    $query  = 'UPDATE `#__easyblog_category`';
	    $query  .= ' SET `created_by` = ' . $db->Quote($newOwnerShip);
	    $query  .= ' WHERE `created_by` = ' . $db->Quote($userId);

		$db->setQuery( $query );
		$db->query();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}

	function ownerTransferTag( $userId, $newOwnerShip )
	{
	    $db = JFactory::getDBO();

	    $query  = 'UPDATE `#__easyblog_tag`';
	    $query  .= ' SET `created_by` = ' . $db->Quote($newOwnerShip);
	    $query  .= ' WHERE `created_by` = ' . $db->Quote($userId);

		$db->setQuery( $query );
		$db->query();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}

	function ownerTransferPost( $userId, $newOwnerShip )
	{
	    $db = JFactory::getDBO();

	    $query  = 'UPDATE `#__easyblog_post`';
	    $query  .= ' SET `created_by` = ' . $db->Quote($newOwnerShip);
	    $query  .= ' WHERE `created_by` = ' . $db->Quote($userId);

		$db->setQuery( $query );
		$db->query();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}

	function onwerTransferComment( $userId, $newOwnerShip )
	{
	    $db = JFactory::getDBO();

	    $query  = 'UPDATE `#__easyblog_comment`';
	    $query  .= ' SET `created_by` = ' . $db->Quote($newOwnerShip);
	    $query  .= ' WHERE `created_by` = ' . $db->Quote($userId);

		$db->setQuery( $query );
		$db->query();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}


	/**
	 * Remove assigned user acl group
	 */
	function removeAssignedACLGroup( $userId )
	{
	    $db = JFactory::getDBO();

	    $query  = 'DELETE FROM `#__easyblog_acl_group`';
	    $query  .= ' WHERE `content_id` = ' . $db->Quote($userId);
	    $query  .= ' AND `type` = ' . $db->Quote('assigned');

		$db->setQuery( $query );
		$db->query();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}

	function removeAdsenseSetting( $userId )
	{
	    $db = JFactory::getDBO();

	    $query  = 'DELETE FROM `#__easyblog_adsense`';
	    $query  .= ' WHERE `user_id` = ' . $db->Quote($userId);

		$db->setQuery( $query );
		$db->query();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}

	function removeFeedburnerSetting( $userId )
	{
	    $db = JFactory::getDBO();

	    $query  = 'DELETE FROM `#__easyblog_feedburner`';
	    $query  .= ' WHERE `userid` = ' . $db->Quote($userId);

		$db->setQuery( $query );
		$db->query();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}

	/**
	 * Since EasyBlog 2.0
	 */
	function removeOAuthSetting( $userId )
	{
	    $db = JFactory::getDBO();

		// removing oauth posts
	    $query  = 'DELETE FROM `#__easyblog_oauth_posts`';
	    $query  .= ' WHERE `oauth_id` IN (';
		$query  .= ' select `id` from `#__easyblog_oauth` where `user_id` = ' . $db->Quote( $userId );
		$query	.= ')';
		$db->setQuery( $query );
		$db->query();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		// removing oauth
	    $query  = 'DELETE FROM `#__easyblog_oauth`';
	    $query  .= ' WHERE `user_id` = ' . $db->Quote($userId);
		$db->setQuery( $query );
		$db->query();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}

	function removeFeaturedBlogger( $userId )
	{
	    $db = JFactory::getDBO();

	    $query  = 'DELETE FROM `#__easyblog_featured`';
	    $query  .= ' WHERE `content_id` = ' . $db->Quote($userId);
	    $query  .= ' AND `type` = ' . $db->Quote('blogger');

		$db->setQuery( $query );
		$db->query();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}

	function removeTeamBlogUser( $userId )
	{
	    $db = JFactory::getDBO();

	    $query  = 'DELETE FROM `#__easyblog_team_users`';
	    $query  .= ' WHERE `user_id` = ' . $db->Quote($userId);

		$db->setQuery( $query );
		$db->query();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}


	function removeBloggerSubscription( $userId )
	{
	    $db = JFactory::getDBO();

	    $query  = 'DELETE FROM `#__easyblog_subscriptions`';
	    $query  .= ' WHERE `uid` = ' . $db->Quote($userId);
	    $query .= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_BLOGGER);

		$db->setQuery( $query );
		$db->query();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}


	function removeEasyBlogUser( $userId )
	{
	    $db = JFactory::getDBO();

	    $query  = 'DELETE FROM `#__easyblog_users`';
	    $query  .= ' WHERE `id` = ' . $db->Quote($userId);

		$db->setQuery( $query );
		$db->query();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}

	/**
	 * Displays a subscribe to blog checkbox field.
	 *
	 * @since	3.7
	 * @access	public
	 */
	public function onContentPrepareData( $context , $data )
	{
		if (!$this->exists()) {
			return true;
		}

		// Check we are manipulating a valid form.
		if (!in_array($context, array('com_users.profile', 'com_users.user', 'com_users.registration', 'com_admin.profile'))) {
			return true;
		}

		return true;
	}


	/**
	 * Displays necessary fields for EasyBlog.
	 *
	 * @since	3.7
	 * @access	public
	 */
	public function onContentPrepareForm($form, $data)
	{
		if (!$this->exists()) {
			return true;
		}

		if (!($form instanceof JForm)) {
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return true;
		}

		if (!$this->params->get('show_subscribe', false)) {
			return true;
		}

		// Check we are manipulating a valid form.
		$name = $form->getName();
		if (!in_array($name, array('com_admin.profile', 'com_users.user', 'com_users.profile', 'com_users.registration'))) {
			return true;
		}

		JFactory::getLanguage()->load( 'plg_easyblogusers' , JPATH_ROOT . '/administrator/' );
		
		// Add the registration fields to the form.
		JForm::addFormPath( dirname(__FILE__) . '/profiles' );
		$state 	= $form->loadFile( 'easyblog', false);

		return true;
	}
}
