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

// Import main view
FD::import( 'site:/views/views' );

class EasySocialViewUsers extends EasySocialSiteView
{
	/**
	 * Displays a list of users on the site
	 *
	 * @access	public
	 * @param	string	The name of the template file to parse; automatically searches through the template paths.
	 * @return	null
	 */
	public function display($tpl = null)
	{
		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Retrieve the users model
		$model = FD::model('Users');
		$my = FD::user();

		$admin = $this->config->get('users.listings.admin') ? true : false;
		$options = array('includeAdmin' => $admin, 'exclusion' => $my->id);

		$limit = FD::themes()->getConfig()->get( 'userslimit' );
		$options['limit']	= $limit;

		$fid = 0;
		$filter = $this->input->get('filter', 'all', 'word');
		$sort = $this->input->get('sort', $this->config->get('users.listings.sorting'), 'word');

		// Do not display profile by default
		$profile = false;

		// Default title
		$title 	= JText::_('COM_EASYSOCIAL_PAGE_TITLE_USERS');

		// Set the sorting options
		if ($sort == 'alphabetical') {
			$nameField = $this->config->get('users.displayName') == 'username' ? 'a.username' : 'a.name';

			$options['ordering'] = $nameField;
			$options['direction'] = 'ASC';
		} else if($sort == 'latest') {
			$options[ 'ordering' ]	= 'a.id';
			$options[ 'direction' ]	= 'DESC';
		} elseif($sort == 'lastlogin') {
			$options[ 'ordering' ]	= 'a.lastvisitDate';
			$options[ 'direction' ]	= 'DESC';
		}

		$searchFilter = '';
		$displayOptions = '';

		if ($filter == 'search') {

			// search filter id
			$fid = $this->input->get('id', 0, 'int');

			$searchFilter = FD::table('SearchFilter');
			$searchFilter->load($fid);

			// Retrieve the users
			$result		= $model->getUsersByFilter($fid, $options);
			$pagination	= $model->getPagination();

			$displayOptions = $model->getDisplayOptions();

			// var_dump($displayOptions);exit;


			// let reset the page title here
			$title = $searchFilter->get('title');

		} else if ($filter == 'profiletype') {

			// Get the profile object
			$id = $this->input->get('id', 0, 'int');

			$profile = FD::table('Profile');
			$profile->load($id);

			if (!$id || !$profile->id) {
				return JError::raiseError(404, JText::_('COM_EASYSOCIAL_404_PROFILE_NOT_FOUND'));
			}

			$options[ 'profile' ]	= $id;

			// we only want published user.
			$options[ 'published' ]	= 1;

			// exclude users who blocked the current logged in user.
			$options['excludeblocked'] = 1;


			$values 				= array();
			$values[ 'criterias' ] 	= $this->input->getVar( 'criterias' );
			$values[ 'datakeys' ] 	= $this->input->getVar( 'datakeys' );
			$values[ 'operators' ] 	= $this->input->getVar( 'operators' );
			$values[ 'conditions' ] = $this->input->getVar( 'conditions' );


			if ($values[ 'criterias' ]) {

				// lets do some clean up here.
				for($i = 0; $i < count($values[ 'criterias' ]); $i++ ) {
					$criteria = $values[ 'criterias' ][$i];
					$condition = $values[ 'conditions' ][$i];
					$datakey = $values[ 'datakeys' ][$i];
					$operator = $values[ 'operators' ][$i];


					if (trim($condition)) {
						$searchOptions['criterias'][] = $criteria;
						$searchOptions['datakeys'][] = $datakey;
						$searchOptions['operators'][] = $operator;

						$field  = explode( '|', $criteria );

						$fieldCode 	= $field[0];
						$fieldType 	= $field[1];

						if ($fieldType == 'birthday') {
							// currently the value from form is in age format. we need to convert it into date time.
							$ages  = explode( '|', $condition );

							if (! isset($ages[1])) {
								// this happen when start has value and end has no value
								$ages[1] = $ages[0];
							}

							if ($ages[1] && !$ages[0]) {
								//this happen when start is empty and end has value
								$ages[0] = $ages[1];
							}

							$startdate = '';
							$enddate = '';

							$currentTimeStamp = FD::date()->toUnix();

							if ($ages[0] == $ages[1]) {
								$start = strtotime('-' . $ages[0] . ' years', $currentTimeStamp);

								$year = FD::date($start)->toFormat('Y');
								$startdate = $year . '-01-01 00:00:01';
								$enddate = FD::date($start)->toFormat('Y-m-d') . ' 23:59:59';
							} else {

								if ($ages[0]) {
									$start = strtotime('-' . $ages[0] . ' years', $currentTimeStamp);

									$year = FD::date($start)->toFormat('Y');
									$enddate = $year . '-12-31 23:59:59';
								}

								if ($ages[1]) {
									$end = strtotime('-' . $ages[1] . ' years', $currentTimeStamp);

									$year = FD::date($end)->toFormat('Y');
									$startdate = $year . '-01-01 00:00:01';
								}
							}

							$condition = $startdate . '|' . $enddate;
						}

						$searchOptions['conditions'][] = $condition;
					}

				}

				$searchOptions[ 'match' ] = 'and';
				$searchOptions[ 'avatarOnly' ] = false;
				if( $id ) {
					$searchOptions[ 'profile' ] = $id;
				}

				$result = $model->getUsersByFilter('0', $options, $searchOptions);

			} else {
				// Retrieve the users
				$result		= $model->getUsers($options);
			}

			$pagination	= $model->getPagination();

			// let reset the page title here
			$title = $profile->get('title');

		} else {
			if ($filter == 'online' || $filter == 'photos' || $filter = 'all') {

				// Need to exclude the current logged in user.
 				$option['exclusion'] = $my->id;

				if ($filter == 'online') {
					$title 	= JText::_('COM_EASYSOCIAL_PAGE_TITLE_USERS_ONLINE_USERS');

					$options['login'] = true;
				}

				if ($filter == 'photos') {
					$title = JText::_('COM_EASYSOCIAL_PAGE_TITLE_USERS_WITH_PHOTOS');
					$options['picture'] = true;
				}

				// we only want published user.
				$options[ 'published' ]	= 1;

				// exclude users who blocked the current logged in user.
				$options['excludeblocked'] = 1;

				// Retrieve the users
				$result		= $model->getUsers($options);
				$pagination	= $model->getPagination();
			}
		}

		// Set the page title
		FD::page()->title($title);

		// Set the page breadcrumb
		FD::page()->breadcrumb($title);

		// Retrieve a list of profile types on the site
		$profilesModel = FD::model('Profiles');
		$profiles = $profilesModel->getProfiles(array('state' => SOCIAL_STATE_PUBLISHED, 'includeAdmin' => $admin, 'excludeESAD' => true));

		if ( $filter != 'profiletype' && $filter != 'search') {
			// Define those query strings here
			$pagination->setVar('filter', $filter);
			$pagination->setVar('sort', $sort);
		}

		$userIds 	= array();
		$users 		= array();

		foreach ($result as $obj) {
			$userIds[] = $obj->id;
			$users[] = FD::user($obj->id);
		}

		// bind / set the fields_data into cache for later reference.
		// the requirement is to FD::user() first before you can call this setUserFieldsData();
		$model->setUserFieldsData($userIds);

		// get sitewide search filter
		$searchModel = FD::model( 'Search' );
		$searchFilters = $searchModel->getSiteWideFilters();

		$this->set('issearch', false);
		$this->set('profiles', $profiles);
		$this->set('activeProfile', $profile);
		$this->set('activeTitle', $title);
		$this->set('pagination', $pagination);
		$this->set('filter', $filter);
		$this->set('sort', $sort);
		$this->set('users', $users);
		$this->set('fid', $fid);
		$this->set('searchFilters', $searchFilters);
		$this->set('searchFilter', $searchFilter);
		$this->set('displayOptions', $displayOptions);

		echo parent::display( 'site/users/default' );
	}

	/**
	 * Displays a list of users on the site from dating search module
	 *
	 * @access	public
	 * @param	string	The name of the template file to parse; automatically searches through the template paths.
	 * @return	null
	 */
	public function search($tpl = null)
	{

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Retrieve the users model
		$model 	= FD::model('Users');
		$my 	= FD::user();

		$config = FD::config();
		$admin 		= $config->get( 'users.listings.admin' ) ? true : false;
		$options	= array('includeAdmin' => $admin );

		$limit 		= FD::themes()->getConfig()->get( 'userslimit' );
		$options[ 'limit' ]	= $limit;

		// Default title
		$title 	= JText::_('COM_EASYSOCIAL_PAGE_TITLE_USERS');

		$post = JRequest::get('POSTS');

		// echo '<pre>';print_r( $post );echo '</pre>';


		// Get values from posted data
		$values 				= array();
		$values[ 'criterias' ] 	= JRequest::getVar( 'criterias' );
		$values[ 'datakeys' ] 	= JRequest::getVar( 'datakeys' );
		$values[ 'operators' ] 	= JRequest::getVar( 'operators' );
		$values[ 'conditions' ] = JRequest::getVar( 'conditions' );

		$avatarOnly = JRequest::getVar( 'avatarOnly', false );
		$onlineOnly = JRequest::getVar( 'onlineOnly', false );

		// echo '<pre>';print_r( $values );echo '</pre>';

		$searchOptions = array();

		// lets do some clean up here.
		for($i = 0; $i < count($values[ 'criterias' ]); $i++ ) {
			$criteria = $values[ 'criterias' ][$i];
			$condition = $values[ 'conditions' ][$i];
			$datakey = $values[ 'datakeys' ][$i];
			$operator = $values[ 'operators' ][$i];


			if (trim($condition)) {
				$searchOptions['criterias'][] = $criteria;
				$searchOptions['datakeys'][] = $datakey;
				$searchOptions['operators'][] = $operator;

				$field  = explode( '|', $criteria );

				$fieldCode 	= $field[0];
				$fieldType 	= $field[1];

				if ($fieldType == 'birthday') {
					// currently the value from form is in age format. we need to convert it into date time.
					$ages  = explode( '|', $condition );

					if (! isset($ages[1])) {
						// this happen when start has value and end has no value
						$ages[1] = $ages[0];
					}

					if ($ages[1] && !$ages[0]) {
						//this happen when start is empty and end has value
						$ages[0] = $ages[1];
					}

					$startdate = '';
					$enddate = '';

					$currentTimeStamp = FD::date()->toUnix();

					if ($ages[0] == $ages[1]) {
						$start = strtotime('-' . $ages[0] . ' years', $currentTimeStamp);

						$year = FD::date($start)->toFormat('Y');
						$startdate = $year . '-01-01 00:00:01';
						$enddate = FD::date($start)->toFormat('Y-m-d') . ' 23:59:59';
					} else {

						if ($ages[0]) {
							$start = strtotime('-' . $ages[0] . ' years', $currentTimeStamp);

							$year = FD::date($start)->toFormat('Y');
							$enddate = $year . '-12-31 23:59:59';
						}

						if ($ages[1]) {
							$end = strtotime('-' . $ages[1] . ' years', $currentTimeStamp);

							$year = FD::date($end)->toFormat('Y');
							$startdate = $year . '-01-01 00:00:01';
						}
					}

					$condition = $startdate . '|' . $enddate;
				}

				$searchOptions['conditions'][] = $condition;
			}

		}

		$pagination = null;
		$result = null;
		$users = array();

		if ($searchOptions) {
			$searchOptions[ 'match' ] = 'all';
			$searchOptions[ 'avatarOnly' ] = $avatarOnly;
			$searchOptions[ 'onlineOnly' ] = $onlineOnly;

			// Retrieve the users
			$result		= $model->getUsersByFilter('0', $options, $searchOptions);
			// $result		= array();
			$pagination	= $model->getPagination();

			$pagination->setVar( 'Itemid'	, FRoute::getItemId( 'users' ) );
			$pagination->setVar( 'view'		, 'users' );
			$pagination->setVar( 'layout'	, 'search' );
			$pagination->setVar( 'filter' , 'search' );

			if ($avatarOnly) {
				$pagination->setVar( 'avatarOnly' , $avatarOnly );
			}

			if ($onlineOnly) {
				$pagination->setVar( 'onlineOnly' , $onlineOnly );
			}


			for($i = 0; $i < count($values[ 'criterias' ]); $i++ ) {

				$criteria = $values[ 'criterias' ][$i];
				$condition = $values[ 'conditions' ][$i];
				$datakey = $values[ 'datakeys' ][$i];
				$operator = $values[ 'operators' ][$i];

				$field  = explode( '|', $criteria );

				$fieldCode 	= $field[0];
				$fieldType 	= $field[1];

				$pagination->setVar( 'criterias['.$i.']' , $criteria );
				$pagination->setVar( 'datakeys['.$i.']' , $datakey );
				$pagination->setVar( 'operators['.$i.']' , $operator );
				$pagination->setVar( 'conditions['.$i.']' , $condition );
			}

			if ($result) {
				foreach ($result as $obj) {
					$users[]	= FD::user($obj->id);
				}
			}
		}

		// $displayOptions = '';
		$displayOptions = $model->getDisplayOptions();

		// Set the page title
		FD::page()->title($title);

		// Set the page breadcrumb
		FD::page()->breadcrumb($title);

		$filter = 'search';
		$sort = '';

		$this->set('issearch', true);
		$this->set('profiles', '');
		$this->set('activeProfile', '');
		$this->set('profile', '');
		$this->set('activeTitle', $title);
		$this->set('pagination', $pagination);
		$this->set('filter', $filter);
		$this->set('sort', $sort);
		$this->set('users', $users);
		$this->set('fid', '');
		$this->set('searchFilters', '');
		$this->set('searchFilter', '');
		$this->set('displayOptions', $displayOptions);


		echo parent::display( 'site/users/default' );

	}
}
