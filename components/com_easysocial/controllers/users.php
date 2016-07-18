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

FD::import( 'site:/controllers/controller' );

class EasySocialControllerUsers extends EasySocialController
{


	/**
	 * Retrieves a list of users by sitewide search filter
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUsersByProfileFilter()
	{
		// Check for request forgeries
		FD::checkToken();

		$config = FD::config();
		$view = $this->getCurrentView();

		// Get the profile id
		$id = $this->input->get('id', 0, 'int');

		// fields filtering data
		$data = $this->input->getVar('data', null);


		$values 				= array();
		if (! is_null($data) && $data) {

			// data saved as json format. so we need to decode it.
			$dataFilter = FD::json()->decode( $data );

			$values['criterias'] 		= $dataFilter->{'criterias[]'};
			$values['datakeys'] 		= $dataFilter->{'datakeys[]'};
			$values['operators'] 		= $dataFilter->{'operators[]'};
			$values['conditions'] 		= $dataFilter->{'conditions[]'};
		} else {

			$values[ 'criterias' ] 	= $this->input->getVar( 'criterias' );
			$values[ 'datakeys' ] 	= $this->input->getVar( 'datakeys' );
			$values[ 'operators' ] 	= $this->input->getVar( 'operators' );
			$values[ 'conditions' ] = $this->input->getVar( 'conditions' );
		}

		$profile = FD::table('Profile');
		$profile->load($id);

		$options = array();

		$admin 		= $config->get( 'users.listings.admin' ) ? true : false;
		$options	= array('includeAdmin' => $admin );

		// setup the limit
		$limit 		= FD::themes()->getConfig()->get('userslimit');
		$options['limit']	= $limit;

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


		$searchOptions[ 'match' ] = 'and';
		$searchOptions[ 'avatarOnly' ] = false;
		if( $id ) {
			$searchOptions[ 'profile' ] = $id;
		}

		// Retrieve the users
		$model = FD::model('Users');
		$pagination  = null;

		$result = $model->getUsersByFilter('0', $options, $searchOptions);
		$pagination	= $model->getPagination();


		// Define those query strings here
		$pagination->setVar( 'Itemid'	, FRoute::getItemId( 'users' ) );
		$pagination->setVar( 'view'		, 'users' );
		$pagination->setVar( 'filter' , 'profiletype' );
		$pagination->setVar( 'id' , $profile->id );

		for($i = 0; $i < count($values[ 'criterias' ]); $i++ ) {

			$criteria = $values[ 'criterias' ][$i];
			$condition = $values[ 'conditions' ][$i];
			$datakey = $values[ 'datakeys' ][$i];
			$operator = $values[ 'operators' ][$i];

			$pagination->setVar( 'criterias['.$i.']' , $criteria );
			$pagination->setVar( 'datakeys['.$i.']' , $datakey );
			$pagination->setVar( 'operators['.$i.']' , $operator );
			$pagination->setVar( 'conditions['.$i.']' , $condition );
		}

		$users 		= array();

		// preload users.
		$arrIds = array();

		foreach ($result as $obj) {
			$arrIds[]	= FD::user( $obj->id );
		}

		if( $arrIds )
		{
			FD::user( $arrIds );
		}

		foreach( $result as $obj )
		{
			$users[]	= FD::user( $obj->id );
		}

		return $view->call(__FUNCTION__, $users, $profile, $data, $pagination);
	}


	/**
	 * Retrieves a list of users by sitewide search filter
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUsersByFilter()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the profile id
		$fid = $this->input->get('id', 0, 'int');
		$sort   = $this->input->get('sort', 'latest', 'word');

		$filter = FD::table('SearchFilter');
		$filter->load($fid);

		// Get the current view
		$view = $this->getCurrentView();

		$model 		= FD::model('Users');

		$options = array();

		// setup the limit
		$limit 		= FD::themes()->getConfig()->get('userslimit');
		$options['limit']	= $limit;

		$result		= $model->getUsersByFilter( $fid, $options );
		$pagination  = null;

		$pagination	= $model->getPagination();

		$displayOptions = $model->getDisplayOptions();

		// var_dump($displayOptions);


		// Define those query strings here
		$pagination->setVar( 'Itemid'	, FRoute::getItemId( 'users' ) );
		$pagination->setVar( 'view'		, 'users' );
		$pagination->setVar( 'filter' , 'search' );
		$pagination->setVar( 'id' , $fid );


		$users 		= array();

		// preload users.
		$arrIds = array();

		foreach ($result as $obj) {
			$arrIds[]	= FD::user( $obj->id );
		}

		if( $arrIds )
		{
			FD::user( $arrIds );
		}

		foreach( $result as $obj )
		{
			$users[]	= FD::user( $obj->id );
		}


		return $view->call(__FUNCTION__, $users, $filter, $pagination, $displayOptions);
	}



	/**
	 * Retrieves a list of users by specific profile
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUsersByProfile()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the profile id
		$id = $this->input->get('id', 0, 'int');
		$sort   = $this->input->get('sort', 'latest', 'word');


		$profile = FD::table('Profile');
		$profile->load($id);

		// Get the current view
		$view = $this->getCurrentView();

		$model 		= FD::model('Users');
		$options	= array('profile' => $id);

		if ($sort == 'alphabetical') {
			$options[ 'ordering' ]	= 'a.name';
			$options[ 'direction' ]	= 'ASC';
		} elseif($sort == 'latest') {
			$options[ 'ordering' ]	= 'a.id';
			$options[ 'direction' ]	= 'DESC';
		}

		// setup the limit
		$limit 		= FD::themes()->getConfig()->get('userslimit');
		$options['limit']	= $limit;

		// we only want published user.
		$options[ 'published' ]	= 1;

		// exclude users who blocked the current logged in user.
		$options['excludeblocked'] = 1;

		$config 	= FD::config();
		$options['includeAdmin'] = $config->get( 'users.listings.admin' ) ? true : false;

		// exclude current logged in user
		$my = ES::user();
		if ($my->id) {
			$options['exclusion'] = $my->id;
		}

		// $model = FD::model('Profiles');
		// $users = $model->getMembers($id, $options);

		$result		= $model->getUsers( $options );
		$pagination  = null;


		$pagination	= $model->getPagination();

		// Define those query strings here
		$pagination->setVar( 'Itemid'	, FRoute::getItemId( 'users' ) );
		$pagination->setVar( 'view'		, 'users' );
		$pagination->setVar( 'filter' , 'profiletype' );
		$pagination->setVar( 'id' , $id );


		$users 		= array();

		// preload users.
		$arrIds = array();

		foreach ($result as $obj) {
			$arrIds[]	= FD::user( $obj->id );
		}

		if( $arrIds )
		{
			FD::user( $arrIds );
		}

		foreach( $result as $obj )
		{
			$users[]	= FD::user( $obj->id );
		}


		return $view->call(__FUNCTION__, $users, $profile, $pagination);
	}

	/**
	 * Retrieves the list of users on the site.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getUsers()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current filter
		$filter = $this->input->get('filter', 'all', 'word');

		// Get the current sorting
		$sort = $this->input->get('sort', $this->config->get('users.listings.sorting'), 'word');
		$isSort = $this->input->get('isSort', false, 'bool');
		$showPagination = $this->input->get('showpagination', 0, 'default');

		$model = FD::model('Users');
		$options = array('exclusion' => $this->my->id);

		if ($sort == 'alphabetical') {
			$nameField = $this->config->get('users.displayName') == 'username' ? 'a.username' : 'a.name';

			$options['ordering'] = $nameField;
			$options['direction'] = 'ASC';
		} elseif($sort == 'latest') {

			$options['ordering'] = 'a.id';
			$options['direction'] = 'DESC';
		} elseif($sort == 'lastlogin') {

			$options['ordering'] = 'a.lastvisitDate';
			$options['direction'] = 'DESC';
		}

		if ($filter == 'online') {
			$options['login'] = true;
		}

		if ($filter == 'photos') {
			$options['picture']	= true;
		}

		// setup the limit
		$limit = FD::themes()->getConfig()->get('userslimit');
		$options['limit'] = $limit;

		// Determine if we should display admins
		$admin = $this->config->get('users.listings.admin') ? true : false;

		$options['includeAdmin'] = $admin;

		// we only want published user.
		$options['published'] = 1;

		// exclude users who blocked the current logged in user.
		$options['excludeblocked'] = 1;

		$result = $model->getUsers($options);
		$pagination = null;

		if ($showPagination) {
			$pagination	= $model->getPagination();

			// Define those query strings here
			$pagination->setVar('Itemid', FRoute::getItemId('users'));
			$pagination->setVar('view', 'users');
			$pagination->setVar('filter' , $filter);
			$pagination->setVar('sort', $sort);
		}

		$users = array();

		// preload users.
		$arrIds = array();

		foreach ($result as $obj) {
			$arrIds[] = FD::user($obj->id);
		}

		if ($arrIds) {
			FD::user($arrIds);
		}

		foreach ($result as $obj) {
			$users[] = FD::user($obj->id);
		}

		return $this->view->call(__FUNCTION__, $users, $isSort, $pagination);
	}
}
