<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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
	public function loadmore($groupType, $results, $nextlimit, $displayOptions )
	{
		$ajax	= FD::ajax();

		$theme 		= FD::themes();

		$output 	= '';
		if( $results )
		{
			foreach( $results as $result )
			{
				if ($groupType == SOCIAL_FIELDS_GROUP_GROUP) {
					$output .= $theme->loadTemplate( 'site/advancedsearch/group/default.results.item' , array( 'group' => $result, 'displayOptions' => $displayOptions ) );
				} else {
					$output .= $theme->loadTemplate( 'site/advancedsearch/user/default.results.item' , array( 'user' => $result, 'displayOptions' => $displayOptions ) );
				}

			}
		}

		return $ajax->resolve( $output, $nextlimit );
	}


	public function getFilterResults( $fid, $data )
	{

		// Require user to be logged in
		FD::requireLogin();

		$ajax	= FD::ajax();

		$theme 		= FD::themes();

		$theme->set( 'criteriaHTML'		, $data['criteria'] );
		$theme->set( 'match'			, $data['match'] );
		$theme->set( 'avatarOnly'		, $data['avatarOnly'] );
		$theme->set( 'sort'				, $data['sort'] );
		$theme->set( 'displayOptions'	, $data['displayOptions'] );

		$theme->set( 'results'			, $data['results'] );
		$theme->set( 'total'			, $data['total'] );
		$theme->set( 'nextlimit'		, $data['nextlimit'] );

		$theme->set( 'fid'				, $fid );

		$contents	= $theme->output( 'site/advancedsearch/user/default.content' );

		return $ajax->resolve( $contents );

	}

	/**
	 * Displays the confirmation dialog to save filters
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmSaveFilter()
	{
		// Require user to be logged in
		FD::requireLogin();

		$ajax	= FD::ajax();

		$theme 		= FD::themes();
		$contents	= $theme->output( 'site/advancedsearch/user/dialog.filter.add' );

		return $ajax->resolve( $contents );
	}

	public function addFilter( $filter )
	{
		$ajax 	= FD::ajax();

		FD::requireLogin();

		$theme 		= FD::themes();

		$theme->set( 'filter'	, $filter );
		$theme->set( 'fid'		, '' );

		$content	= $theme->output( 'site/advancedsearch/user/sidebar.filter.item' );

		return $ajax->resolve( $content, JText::_( 'COM_EASYSOCIAL_ADVANCED_SEARCH_FILTER_SAVED' ) );
	}

	public function confirmFilterDelete()
	{
		$ajax 	= FD::ajax();

		$theme 		= FD::themes();
		$contents	= $theme->output( 'site/advancedsearch/user/dialog.filter.delete' );

		return $ajax->resolve( $contents );
	}

	public function getItems( $data, $last_type, $next_limit, $isloadmore = false, $ismini = false, $totalCnt = 0, $filters = array() )
	{
		// Load ajax lib
		$ajax	= FD::ajax();

		$showadvancedlink 	= JRequest::getBool( 'showadvancedlink', true );

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if ($error) {
			return $ajax->reject( $error );
		}

		$keywords 	= JRequest::getVar( 'q', '' );

		$theme 		= FD::get( 'Themes' );
		$theme->set( 'data' , $data );
		$theme->set( 'last_type' , $last_type );
		$theme->set( 'keywords', $keywords );
		$theme->set( 'total', $totalCnt );
		$theme->set( 'showadvancedlink', $showadvancedlink );
		$theme->set( 'filters', $filters );

		$next_type 		= '';
		$next_update 	= '';

		if ($data) {
			foreach ($data as $group => $items) {
				foreach ($items as $item) {
					$next_type = $item->utype;
				}
			}
		}

		$output = '';
		if ($isloadmore) {
			$theme->set( 'next_limit' , $next_limit );
			$output = $theme->output( 'site/search/default.list.ajax' );
			return $ajax->resolve( $output, $next_type, $next_limit );

		} else if ($ismini) {
			$output 	= $theme->output( 'site/search/default.list.mini' );
			return $ajax->resolve( $output );

		} else {
			$theme->set( 'next_limit' , $next_limit );
			$output 	= $theme->output( 'site/search/default.list' );
			return $ajax->resolve( $output );

		}

	}

	public function addNewCriteria( $criteria )
	{
		// Load ajax lib
		$ajax	= FD::ajax();

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		return $ajax->resolve( $criteria );
	}

	/**
	 * Sends the html codes for operator and conditions
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDataKeys($dataKeyHTML, $operatorHTML, $conditionHTML )
	{
		// Load ajax lib
		$ajax	= FD::ajax();

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		return $ajax->resolve( $dataKeyHTML, $operatorHTML, $conditionHTML );
	}

	/**
	 * Sends the html codes for operator and conditions
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getOperators( $operatorHTML, $conditionHTML )
	{
		// Load ajax lib
		$ajax	= FD::ajax();

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		return $ajax->resolve( $operatorHTML, $conditionHTML );
	}

	/**
	 * Sends the html codes for conditions
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getConditions( $conditionHTML )
	{
		// Load ajax lib
		$ajax	= FD::ajax();

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		return $ajax->resolve( $conditionHTML );
	}


	public function getActivities( $data, $nextlimit, $isloadmore = false )
	{
		// Load ajax lib
		$ajax	= FD::ajax();

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		$theme 		= FD::get( 'Themes' );
		$theme->set( 'activities' , $data );
		$theme->set( 'nextlimit' , $nextlimit );

		$output = '';
		if( $isloadmore )
		{
			if( $data )
			{
				foreach( $data as $activity ){
					$output .= $theme->loadTemplate( 'site/activities/default.activities.item' , array( 'activity' => $activity ) );
				}
			}

			return $ajax->resolve( $output, $nextlimit );
		}
		else
		{
			$output 	= $theme->output( 'site/activities/default.activities' );

			return $ajax->resolve( $output );
		}

	}

}
