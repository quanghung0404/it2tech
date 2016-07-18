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

// Include main view file.
FD::import( 'site:/views/views' );

class EasySocialViewAccount extends EasySocialSiteView
{
	/**
	 * Determines if the view should be visible on lockdown mode
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isLockDown()
	{
		$config = FD::config();
		$layout = $this->getLayout();

		// Allowed layouts on lockdown mode
		$allowed = array('auth');

		if (in_array($layout, $allowed)) {
			return false;
		}

		return true;
	}

	/**
	 * Allows remote user to authenticate via a normal http request and returns with an authentication code.
	 *
	 * @since	1.2.8
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function auth()
	{
		$username 	= JRequest::getVar('username');
		$password 	= JRequest::getVar('password');
		$data 		= array('username' => $username, 'password' => $password);

		$app 		= JFactory::getApplication();
		$state 		= $app->login($data);

		if ($state === false) {

			$this->set('code', 403);
			$this->set('message', JText::_('Invalid username or password provided'));

			return parent::display();
		}

		// Get the user's id based on the username.
		$model 	= FD::model('Users');
		$id 	= $model->getUserId('username', $username);

		if (!$id) {

			$this->set('code', 403);
			$this->set('message', JText::_('Unable to locate the user id with the given username.'));

			return parent::display();
		}

		$user 	= FD::user($id);

		// User logs in successfully. Generate an authentication code for the user
		$user->auth 	= md5($user->password . JFactory::getDate()->toSql());
		$user->store();

		$this->set('auth', $user->auth);
		$this->set('code', 200);
		$this->set('id', $user->id);

		return parent::display();
	}
}
