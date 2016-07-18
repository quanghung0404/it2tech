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

class EasySocialControllerSearch extends EasySocialController
{


	/**
	 * Search within EasySocial.
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function query()
	{
		// Check for request forgeries!
		FD::checkToken();

		// Get the query
		$query = $this->input->get('q', '', 'default');

		// Get the search type
		$type = $this->input->get('type', '', 'cmd');

		// Get the search filter types
		$filters = $this->input->get('filtertypes', array(), 'array');

		$options = array('q' => urlencode($query));

		if ($type) {
			$options['type'] = $type;
		}


		if ($filters) {
			for($i = 0; $i < count($filters); $i++) {
				$options['filtertypes[' . $i . ']'] = $filters[$i];
			}
		}

		$url 	= FRoute::search($options, false);

		$this->app->redirect($url);
	}


	/**
	 * get activity logs.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getItems()
	{
		// Check for request forgeries!
		FD::checkToken();

		// search controller do not need to check islogin.

		// Get the current view
		$view 			= $this->getCurrentView();

		// Get current logged in user
		$my 			= FD::user();

		// 7:EasyBlog
		// $type 			= JRequest::getInt( 'type', 0 );
		// $keywords 		= JRequest::getVar( 'q', '' );
		// $next_limit 	= JRequest::getVar( 'next_limit', '' );
		// $last_type 		= JRequest::getVar( 'last_type', '' );
		// $isloadmore 	= JRequest::getVar( 'isloadmore', false );
		// $ismini 		= JRequest::getVar( 'mini', false );


		$keywords = $this->input->get('q', '', 'default');
		$next_limit = $this->input->get('next_limit', '', 'default');
		$last_type = $this->input->get('last_type', '', 'default');
		$isloadmore = $this->input->get('loadmore', false, 'bool');
		$ismini = $this->input->get('mini', false, 'bool');

		$filters = $this->input->get('filtertypes', array(), 'array');


		$highlight = $ismini ? false : true;

		$limit 			= ( $ismini ) ? FD::themes()->getConfig()->get( 'search_toolbarlimit' ) : FD::themes()->getConfig()->get( 'search_limit' );

		// @badge: search.create
		// Assign badge for the person that initiated the friend request.
		if( ! $isloadmore ) {
			$badge 	= FD::badges();
			$badge->log( 'com_easysocial' , 'search.create' , $my->id , JText::_( 'COM_EASYSOCIAL_SEARCH_BADGE_SEARCHED_ITEM' ) );
		}

		$results = array();
		$pagination = null;
		$count = 0;

		$data = array();

		$isFinderEnabled = JComponentHelper::isEnabled('com_finder');


		if ($isFinderEnabled) {

			jimport( 'joomla.application.component.model' );

			$searchAdapter = FD::get( 'Search' );

			$path 	= JPATH_ROOT . '/components/com_finder/models/search.php';
			require_once( $path );

			$jModel = new FinderModelSearch();
			$state = $jModel->getState();

			$query = $jModel->getQuery(); // this line need to be here. so that the indexer can get the correct value

			if (!$query->terms) {
				// if there is no terms match. lets check if smart search suggested any terms or not. if yes, lets use it.

				// before we can include this file, we need to supress the notice error of this key FINDER_PATH_INDEXER due to the way this key defined in /com_finder/models/search.php
				$suggestlib = JPATH_ROOT . '/components/com_finder/models/suggestions.php';
				@require_once($suggestlib);

				$suggestion = '';
				$suggestionModel = new FinderModelSuggestions();
				$suggestedItems = $suggestionModel->getItems();

				if ($suggestedItems) {
					// we need to get the shortest terms to search
					$curLength = JString::strlen($suggestedItems[0]);
					$suggestion = $suggestedItems[0];

					for($i = 1; $i < count($suggestedItems); $i++) {
						$iLen = JString::strlen($suggestedItems[$i]);

						if ($iLen < $curLength) {
							$curLength = $iLen;
							$suggestion = $suggestedItems[$i];
						}
					}

					if ($suggestion) {

						$app = JFactory::getApplication();
						$input = $app->input;
						$input->request->set('q', $suggestion);

						// Load up the new model
						$jModel = new FinderModelSearch();
						$state = $jModel->getState();

						// this line need to be here. so that the indexer can get the correct value
						$query = $jModel->getQuery();
					}
				}

				if (!$suggestion && isset($query->included) && count($query->included) > 0) {

					foreach($query->included as $item) {
						if (isset($item->suggestion) && !empty($item->suggestion)) {
							$suggestion = $item->suggestion;
						}
					}

					if ($suggestion) {
						$app = JFactory::getApplication();
						$input = $app->input;
						$input->request->set('q', $suggestion);

						// Load up the new model
						$jModel = new FinderModelSearch();
						$state = $jModel->getState();

						// this line need to be here. so that the indexer can get the correct value
						$query = $jModel->getQuery();
					}
				}
			}

			//reset the pagination state.
			$state->{'list.start'} = $next_limit;
			$state->{'list.limit'} = $limit;

			if ($filters) {
				$typeArr = array();

				foreach($filters as $filter) {

					// 7-EasyBlog
					$typeAlias = explode('-', $filter);
					$typeAlias = $typeAlias[1];

					$typeArr['Type'][$typeAlias] = (int) $filter;


					//debug code here.
					//17258-EasySocial.Photos
					//17268-EasySocial.Videos
					// $typeArr['Type'] = array( 'EasySocial.Photos' => '17258', 'EasySocial.Videos' => '17268' );
				}

				$query->filters = $typeArr;
			}

			$results 	= $jModel->getResults();

			$count 		= $jModel->getTotal();
			$pagination = $jModel->getPagination();

			if( FD::isJoomla30() )
			{
				$pagination->{'pages.total'} = $pagination->pagesTotal;
				$pagination->{'pages.current'} = $pagination->pagesCurrent;
			}

			if( $results )
			{
				$data = $searchAdapter->format( $results, $query, $highlight );

				if (isset($data['excludeCnt'])) {
					$count = $count - $data['excludeCnt'];
					// now we remove this excludeCnt
					unset($data['excludeCnt']);
				}

				if ($pagination->{'pages.total'} == 1 || $pagination->{'pages.total'} == $pagination->{'pages.current'}) {
					$next_limit = '-1';
				}
				else
				{
					$next_limit = $pagination->limitstart + $pagination->limit;
				}
			}


		}

		return $view->call( __FUNCTION__, $data, $last_type, $next_limit, $isloadmore, $ismini, $count, $filters );

	}


	public function deleteFilter()
	{
		// Check for request forgeries.
		FD::checkToken();

		// In order to access the dashboard apps, user must be logged in.
		FD::requireLogin();

		$view 	= FD::view( 'Search' , false );
		$my 	= FD::user();

		$id 	= JRequest::getInt( 'fid', 0 );

		if(! $id )
		{
			FD::getInstance( 'Info' )->set( JText::_( 'Invalid filter id - ' . $id ) , 'error' );
			$view->setError( JText::_( 'Invalid filter id.' ) );
			return $view->call( __FUNCTION__ );
		}


		$filter = FD::table( 'SearchFilter' );

		// make sure the user is the filter owner before we delete.
		$filter->load( array( 'id' => $id, 'uid' => $my->id, 'element' => 'user' ) );

		if(! $filter->id )
		{
			FD::getInstance( 'Info' )->set( JText::_( 'Filter not found - ' . $id ) , 'error' );
			$view->setError( JText::_( 'Filter not found. Action aborted.' ) );
			return $view->call( __FUNCTION__ );
		}

		$filter->delete();

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_DELETED' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ );
	}

	// this method is called from the dialog to quickly add new filter based on the viewing hashtag.
	public function addFilter()
	{
		// Check for request forgeries.
		FD::checkToken();

		// In order to access the dashboard apps, user must be logged in.
		FD::requireLogin();

		$my 	= FD::user();

		$view 	= FD::view( 'Search' , false );

		$title   	= JRequest::getVar( 'title' );
		$sitewide   = JRequest::getVar( 'sitewide', '0' );
		$data   	= JRequest::getVar( 'data' );

		$filter = FD::table( 'SearchFilter' );

		$filter->title 		= $title;
		$filter->uid   		= $my->id;
		$filter->element 	= SOCIAL_TYPE_USER;
		$filter->created_by = $my->id;
		$filter->filter 	= $data; // as as json string.
		$filter->created 	= FD::date()->toMySQL();
		$filter->sitewide 	= ($my->isSiteAdmin() && $sitewide) ? 1 : 0;

		$filter->store();

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_ADVANCED_SEARCH_FILTER_SAVED' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__, $filter );
	}

	/**
	 * Allows caller to retrieve saved search results
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function getFilterResults()
	{
		// Check for request forgeries.
		FD::checkToken();

		// In order to access the dashboard apps, user must be logged in.
		FD::requireLogin();
		$showNew = false;

		$config = FD::config();

		$view 	= FD::view( 'Search' , false );
		$fid 	= JRequest::getVar( 'fid', '' );
		$fname  = '';

		$data['criteria'] 			= '';
		$data['match']				= 'all';
		$data['avatarOnly']			= 0;
		$data['sort']				= $config->get('users.advancedsearch.sorting', 'default');
		$data['total'] 				= 0;
		$data['results']			= null;
		$data['nextlimit']			= null;

		$library 	= FD::get( 'AdvancedSearch' );
		// this is doing new search
		$options = array();
		$options[ 'showPlus' ] 	= true;

		$displayOptions = array();

		if( $fid )
		{
			// lets get the criteria from db.
			$filter = FD::table( 'SearchFilter' );
			$filter->load( $fid );

			$fname = $filter->title;

			// data saved as json format. so we need to decode it.
			//
			// var_dump( $filter->filter  );
			$dataFilter = FD::json()->decode( $filter->filter );

			$values = array();
			$values['criterias'] 		= isset( $dataFilter->{'criterias[]'} ) ? $dataFilter->{'criterias[]'} : '';
			$values['datakeys'] 		= isset( $dataFilter->{'datakeys[]'} ) ? $dataFilter->{'datakeys[]'} : '';
			$values['operators'] 		= isset( $dataFilter->{'operators[]'} ) ? $dataFilter->{'operators[]'} : '';
			$values['conditions'] 		= isset( $dataFilter->{'conditions[]'} ) ? $dataFilter->{'conditions[]'} : '';

			// we need check if the item passed in is array or not. if not, make it an array.
			if( ! is_array( $values['criterias'] ) )
			{
				$values['criterias'] = array( $values['criterias'] );
			}

			if( ! is_array( $values['datakeys'] ) )
			{
				$values['datakeys'] = array( $values['datakeys'] );
			}


			if( ! is_array( $values['operators'] ) )
			{
				$values['operators'] = array( $values['operators'] );
			}

			if( ! is_array( $values['conditions'] ) )
			{
				$values['conditions'] = array( $values['conditions'] );
			}

			// perform search
			$values['match'] 			= isset( $dataFilter->matchType ) ? $dataFilter->matchType : 'all';
			$values['avatarOnly']		= isset( $dataFilter->avatarOnly ) ? true : false;
			$values['sort']				= isset( $dataFilter->sort ) ? $dataFilter->sort : $config->get('users.advancedsearch.sorting', 'default');


			$results 	= null;
			$total 		= 0;
			$nextlimit 	= null;

			if( $values['criterias'] )
			{
				$results = $library->search( $values );
				$displayOptions = $library->getDisplayOptions();
				$total 		= $library->getTotal();
				$nextlimit 	= $library->getNextLimit();
			}

			$criteriaHTML	= $library->getCriteriaHTML( $options, $values );

			if (! $criteriaHTML) {
				// this is doing new search
				$showNew = true;
			}

			$data['criteria'] 		= $criteriaHTML;
			$data['match']			= $values['match'];
			$data['avatarOnly']		= $values['avatarOnly'];
			$data['sort']			= $values['sort'];
			$data['total'] 			= $total;
			$data['results']		= $results;
			$data['nextlimit']		= $nextlimit;
		}
		else
		{
			$showNew = true;
		}

		$data['displayOptions']	= $displayOptions;

		if($showNew) {
			$criteriaHTML 		= $library->getCriteriaHTML( $options );
			$data['criteria'] 	= $criteriaHTML;
		}

		return $view->call( __FUNCTION__, $fid, $data );

	}

	/**
	 * Responsible to display more results from the advanced search
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function loadmore()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();
		$config = FD::config();

		// Get the data from request
		$data 			= JRequest::getVar( 'data', '' );
		$nextlimit 		= JRequest::getVar( 'nextlimit', '' );

		// data saved as json format. so we need to decode it.
		$filter 	= FD::json()->decode($data);

		$groupType = isset($filter->type) ? $filter->type : SOCIAL_FIELDS_GROUP_USER;

		// Load up advanced search library
		$library 	= FD::get( 'AdvancedSearch', $groupType );

		// Get the values
		$values 	= array();
		$values['criterias']	= $filter->{'criterias[]'};
		$values['operators']	= $filter->{'operators[]'};
		$values['conditions']	= $filter->{'conditions[]'};
		$values['datakeys']	= $filter->{'datakeys[]'};

		// perform search
		$values['match'] 		= $filter->matchType;
		$values['avatarOnly']	= isset($filter->avatarOnly) ? true : false;
		$values['onlineOnly']	= isset($filter->onlineOnly) ? true : false;
		$values['sort']			= isset($filter->sort) ? $filter->sort : $config->get('users.advancedsearch.sorting', 'default');
		$values['nextlimit'] 	= $nextlimit;

		$results 	= null;
		$total 		= 0;
		$nextlimit 	= null;
		$displayOptions = array();

		if ($values['criterias']) {
			$results 	= $library->search($values);
			$displayOptions = $library->getDisplayOptions();
			$total 		= $library->getTotal();
			$nextlimit 	= $library->getNextLimit();
		}

		return $view->call( __FUNCTION__, $groupType, $results, $nextlimit, $displayOptions );
	}


	/**
	 * Allows caller to get a list of datakeys for the field
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function getDataKeys()
	{
		$fldsWithKeys = array('address','joomla_fullname');

		// Check for request forgeries.
		FD::checkToken();

		$key 			= JRequest::getVar( 'key' );
		$element 		= JRequest::getVar( 'element' );
		$datakey 		= JRequest::getVar( 'datakey', '' );

		// Get the current view
		$view	= $this->getCurrentView();

		// Set the default options
		$options				= array();
		$options[ 'fieldCode' ] = $key;
		$options[ 'fieldType' ] = $element;

		// Load up advanced search library
		$library		= FD::get( 'AdvancedSearch' );

		// Get the datakey's html codes
		$dataKeysHTML = $library->getDataKeyHTML( $options );


		// Get the operator's html codes
		$operatorHTML	= $library->getOperatorHTML( $options );

		// now we get the default condition
		$options[ 'fieldOperator' ] = 'equal';
		$conditionHTML	= $library->getConditionHTML( $options );

		return $view->call( __FUNCTION__, $dataKeysHTML, $operatorHTML, $conditionHTML );
	}


	/**
	 * Allows caller to get a list of operators for advanced search
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function getOperators()
	{
		// Check for request forgeries.
		FD::checkToken();

		$key 			= JRequest::getVar( 'key' );
		$element 		= JRequest::getVar( 'element' );
		$datakey 		= JRequest::getVar( 'datakey', '' );

		// Get the current view
		$view	= $this->getCurrentView();

		// Set the default options
		$options				= array();
		$options[ 'fieldCode' ] = $key;
		$options[ 'fieldType' ] = $element;

		if ($datakey) {
			$options[ 'fieldKey' ] = $datakey;
		}

		// Load up advanced search library
		$library		= FD::get( 'AdvancedSearch' );

		// Get the operator's html codes
		$operatorHTML	= $library->getOperatorHTML( $options );

		// now we get the default condition
		$options[ 'fieldOperator' ] = 'equal';
		$conditionHTML	= $library->getConditionHTML( $options );

		return $view->call( __FUNCTION__, $operatorHTML, $conditionHTML );
	}


	/**
	 * Allows caller to get a list of conditions for advanced search
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function getConditions()
	{
		// Check for request forgeries.
		FD::checkToken();

		$element 		= JRequest::getVar( 'element' );
		$operator 		= JRequest::getVar( 'operator' );
		$datakey 		= JRequest::getVar( 'datakey', '' );


		// Get the current view
		$view 	= $this->getCurrentView();

		$options 					= array();
		$options[ 'fieldType' ]		= $element;
		$options[ 'fieldOperator' ] = $operator;

		if ($datakey) {
			$options[ 'fieldKey' ] = $datakey;
		}


		$library 		= FD::get( 'AdvancedSearch' );
		$conditionHTML	= $library->getConditionHTML( $options );

		return $view->call( __FUNCTION__, $conditionHTML );
	}

}
