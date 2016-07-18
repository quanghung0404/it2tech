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

FD::import( 'site:/views/views' );

class EasySocialViewSearch extends EasySocialSiteView
{
	/**
	 * Responsible to output the search layout.
	 *
	 * @access	public
	 * @return	null
	 *
	 */
	public function display($tpl = null)
	{
		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Get the current logged in user.
		$query = $this->input->get('q', '', 'default');
		$q = $query;

		// Get the search type
		$type = $this->input->get('type', '', 'cmd');

		// Get the search filter types
		$filters = $this->input->get('filtertypes', array(), 'array');

		// Load up the model
		$indexerModel = FD::model('Indexer');

		// Retrieve a list of supported types
		$allowedTypes = $indexerModel->getSupportedType();

		if (!in_array($type, $allowedTypes)) {
			$type = '';
		}

		// Options
		$data = null;
		$types = null;
		$count = 0;
		$next_limit = '';
		$limit = FD::themes()->getConfig()->get('search_limit');

		// Get the search model
		$model = FD::model('Search');
		$searchAdapter = FD::get('Search');

		// Determines if finder is enabled
		$isFinderEnabled = JComponentHelper::isEnabled('com_finder');

		if (!empty($query) && $isFinderEnabled) {

			jimport('joomla.application.component.model');

			$searchlib = JPATH_ROOT . '/components/com_finder/models/search.php';
			require_once($searchlib);

			if ($type) {
				JRequest::setVar('t', $type);
			} else if ($filters) {
				JRequest::setVar('t', $filters);
			}

			// Load up finder's model
			$finderModel = new FinderModelSearch();
			$state = $finderModel->getState();

			// Get the query
			// this line need to be here. so that the indexer can get the correct value
			$query = $finderModel->getQuery();

			// When there is no terms match, check if smart search suggested any terms or not. if yes, lets use it.
			if (!$query->terms) {

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
						$finderModel = new FinderModelSearch();
						$state = $finderModel->getState();

						// this line need to be here. so that the indexer can get the correct value
						$query = $finderModel->getQuery();
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
						$finderModel = new FinderModelSearch();
						$state = $finderModel->getState();

						// this line need to be here. so that the indexer can get the correct value
						$query = $finderModel->getQuery();
					}
				}
			}

			//reset the pagination state.
			$state->{'list.start'} 	= 0;
			$state->{'list.limit'} 	= $limit;

			$results = $finderModel->getResults();
			$count = $finderModel->getTotal();
			$pagination = $finderModel->getPagination();

			if ($results) {
				$data = $searchAdapter->format($results, $query);

				if (isset($data['excludeCnt'])) {
					$count = $count - $data['excludeCnt'];
					// now we remove this excludeCnt
					unset($data['excludeCnt']);
				}

				$query = $finderModel->getQuery();

				if (FD::isJoomla30()) {
					$pagination->{'pages.total'} = $pagination->pagesTotal;
				}

				if ($pagination->{'pages.total'} == 1) {
					$next_limit = '-1';
				} else {
					$next_limit = $pagination->limitstart + $pagination->limit;
				}
			}

			// @badge: search.create
			// Assign badge for the person that initiated the friend request.
			$badge 	= FD::badges();
			$badge->log( 'com_easysocial' , 'search.create' , $this->my->id , JText::_( 'COM_EASYSOCIAL_SEARCH_BADGE_SEARCHED_ITEM' ) );

			// get types
			$types	= $searchAdapter->getTaxonomyTypes();
			$filterTypes = $types;
		} else {
			$filterTypes	= $searchAdapter->getTaxonomyTypes();
		}

		// merge the filters into filterTypes
		$searchAdapter->validateFilters($filterTypes, $filters);


		// Set the page title
		FD::page()->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_SEARCH'));

		// Set the page breadcrumb
		FD::page()->breadcrumb(JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_SEARCH'));

		$this->set('types', $types);
		$this->set('data', $data);
		$this->set('query', $q);
		$this->set('total', $count);
		$this->set('totalcount', $count);
		$this->set('next_limit', $next_limit);
		$this->set('filterTypes', $filterTypes);
		$this->set('filters', $filters);

		echo parent::display('site/search/default');
	}

	/**
	 * Displays the advanced search form
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function advanced( $tpl = null )
	{
		// Check for user profile completeness
		FD::checkCompleteProfile();

		$advGroups = array(SOCIAL_FIELDS_GROUP_GROUP, SOCIAL_FIELDS_GROUP_USER);

		// Get current logged in user.
		$my 			= FD::user();

		$config = FD::config();

		// What is this? - this is advanced search filter id.
		$fid 			= $this->input->get('fid', 0, 'int');

		// advanced search type. for now, it support user or group only.
		$groupType = $this->input->get('type', SOCIAL_FIELDS_GROUP_USER, 'default');
		$uid = $this->input->get('uid', 0, 'int');

		if (! in_array($groupType, $advGroups)) {
			// type not supported. redirect back to normal search page.
			$this->info->set('Search type not supported.', SOCIAL_MSG_ERROR);
			return $this->app->redirect('index.php?option=com_easysocial&view=search');
		}

		// setting page title and breadcrumb.
		$pageTitle = 'COM_EASYSOCIAL_PAGE_TITLE_ADVANCED_SEARCH';
		$breadcrumb = 'COM_EASYSOCIAL_PAGE_TITLE_ADVANCED_SEARCH';

		if ($groupType == SOCIAL_FIELDS_GROUP_GROUP) {
			$pageTitle = 'COM_EASYSOCIAL_PAGE_TITLE_ADVANCED_SEARCH_GROUP_MATCHES';
			$breadcrumb = 'COM_EASYSOCIAL_PAGE_TITLE_ADVANCED_SEARCH_GROUP_MATCHES';
		}


		// Set the page title
		FD::page()->title(JText::_($pageTitle));

		// Set the page breadcrumb
		FD::page()->breadcrumb(JText::_($breadcrumb));


		// Get filters
		$model 		= FD::model( 'Search' );
		$filters = array();
		if ($groupType == SOCIAL_FIELDS_GROUP_USER) {

			$filters 	= $model->getFilters( $my->id );
		}

		// Load up advanced search library
		$library 	= FD::get( 'AdvancedSearch', $groupType );

		// default values
		// Get values from posted data
		$match 		= JRequest::getVar( 'matchType', 'all' );
		$avatarOnly	= JRequest::getInt( 'avatarOnly', 0 );
		$onlineOnly	= JRequest::getInt( 'onlineOnly', 0 );
		$sort 		= JRequest::getVar( 'sort', $config->get('users.advancedsearch.sorting', 'default') );

		// Get values from posted data
		$values 				= array();
		$values[ 'criterias' ] 	= JRequest::getVar( 'criterias' );
		$values[ 'datakeys' ] 	= JRequest::getVar( 'datakeys' );
		$values[ 'operators' ] 	= JRequest::getVar( 'operators' );
		$values[ 'conditions' ] = JRequest::getVar( 'conditions' );
		$values[ 'match' ] 		= $match;
		$values[ 'avatarOnly' ] = $avatarOnly;
		$values[ 'onlineOnly' ] = $onlineOnly;
		$values[ 'sort' ] = $sort;

		// echo '<pre>';print_r( $values );echo '</pre>';exit;


		// Default values
		$results 		= null;
		$total 			= 0;
		$nextlimit 		= null;
		$criteriaHTML 	= '';


		if( $fid && empty( $values[ 'criterias' ] ) )
		{
			// we need to load the data from db and do the search based on the saved filter.
			$filter = FD::table( 'SearchFilter' );
			$filter->load( $fid );

			// data saved as json format. so we need to decode it.
			$dataFilter = FD::json()->decode( $filter->filter );

			// override with the one from db.
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


			$values['match'] 			= isset( $dataFilter->matchType ) ? $dataFilter->matchType : 'all';
			$values['avatarOnly']		= isset( $dataFilter->avatarOnly ) ? true : false;
			$values[ 'sort' ] 			= isset( $dataFilter->sort ) ? $dataFilter->sort : $config->get('users.advancedsearch.sorting', 'default');


			$match 		= $values['match'];
			$avatarOnly	= $values['avatarOnly'];
			$sort		= $values['sort'];

		}

		$displayOptions = array();

		// If there are criterias, we know the user is making a post request to search
		if( $values[ 'criterias' ] )
		{
			$results	= $library->search( $values );
			$displayOptions = $library->getDisplayOptions();
			$total 		= $library->getTotal();
			$nextlimit 	= $library->getNextLimit();
		}

		// Get search criteria output
		$criteriaHTML	= $library->getCriteriaHTML( array() , $values );

		if (! $criteriaHTML) {
			$criteriaHTML	= $library->getCriteriaHTML( array() );
		}

		$this->set( 'criteriaHTML'	, $criteriaHTML );
		$this->set( 'match'			, $match );
		$this->set( 'avatarOnly'	, $avatarOnly );
		$this->set( 'onlineOnly'	, $onlineOnly );
		$this->set( 'sort'	, $sort );
		$this->set( 'results'		, $results );
		$this->set( 'total'			, $total );
		$this->set( 'nextlimit'		, $nextlimit );
		$this->set( 'filters'		, $filters);
		$this->set( 'fid'			, $fid );
		$this->set( 'displayOptions', $displayOptions );

		$themefile = 'user/default';
		if ($groupType == SOCIAL_FIELDS_GROUP_GROUP) {

			$activeGroup = '';
			if ($uid) {
				$activeGroup = ES::group($uid);
			}
			$this->set( 'activeGroup', $activeGroup );
			$themefile = 'group/default';
		}

		echo parent::display( 'site/advancedsearch/' . $themefile );
	}
}
