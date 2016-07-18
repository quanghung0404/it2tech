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

jimport('joomla.application.component.model');

FD::import( 'admin:/includes/model' );
FD::import( 'admin:/includes/privacy/option' );

class EasySocialModelSearch extends EasySocialModel
{
	private $data			= null;
	private $types     		= null;
	private $next_limit    	= null;
	protected $total 			= null;

	function __construct()
	{
		parent::__construct( 'search' );
	}

	public function getTypes()
	{
		$db = FD::db();

		if(! $this->types )
		{
			// get utypes from queries
			$typeQuery = 'select distinct ' . $db->nameQuote( 'utype' ) . ' FROM ' . $db->nameQuote( '#__social_indexer' );
			$db->setQuery( $typeQuery );
			$types = $db->loadObjectList();

			$this->types = $types;
		}

		return $this->types;
	}

	public function verifyFieldsData( $keywords, $userId )
	{
		// return variable
		$content 		= '';

		// get customfields.
		$fieldsLib		= FD::fields();
		$fieldModel  	= FD::model( 'Fields' );
		$fieldsResult 	= array();

		$options = array();
		$options['data'] 		= true;
		$options['dataId'] 		= $userId;
		$options['dataType'] 	= SOCIAL_TYPE_USER;
		$options['searchable'] 	= 1;

		//todo: get customfields.
		$fields = $fieldModel->getCustomFields( $options );

		if( count( $fields ) > 0 )
		{
			//foreach( $fields as $item )
			foreach( $fields as $field )
			{
				$userFieldData  = isset( $field->data ) ? $field->data : '';

				$args 			= array( $userId, $keywords, $userFieldData );
				$f 				= array( &$field );

				$dataResult 	= $fieldsLib->trigger( 'onIndexerSearch' , SOCIAL_FIELDS_GROUP_USER , $f , $args );

				if( $dataResult !== false && count( $dataResult ) > 0 )
					$fieldsResult[]  	= $dataResult[0];
			}

			$contentSnapshot = array();

			$totalReturnFields = count( $fieldsResult );
			$invalidCnt        = 0;

			if( $fieldsResult )
			{
				// we need to go through each one to see if any of the result returned is a false or not.
				// false mean, the user canot view the fields.
				// this also mean, the user canot view the searched item.

				foreach( $fieldsResult as $fr )
				{
					if( $fr == -1 )
					{
						$invalidCnt++;
					}
					else if( !empty( $fr ) )
					{
						$contentSnapshot[] = $fr;
					}
				}

				if( $invalidCnt == $totalReturnFields )
				{
					return -1;
				}
			}

			if( $contentSnapshot )
			{
				$content = implode( '<br />', $contentSnapshot );
			}

		}

		return $content;
	}

	public function getFilters( $uid, $element = SOCIAL_TYPE_USER )
	{
	    $db     = FD::db();
	    $sql 	= $db->sql();

	    if( !$uid )
	    {
	    	return null;
	    }

	    $query = 'select a.* from `#__social_search_filter` as a';
	    $query .= ' where a.`element` = ' . $db->Quote( $element );
	    $query .= ' and a.uid = ' . $db->Quote( $uid );

	    $sql->raw( $query );
	    $db->setQuery( $sql );

	    $results = $db->loadObjectList();

	    $filters = array();
	    if( $results )
	    {
	    	foreach( $results as $row )
	    	{
	    		$tbl = FD::table( 'SearchFilter' );
	    		$tbl->bind( $row );

	    		$filters[] = $tbl;
	    	}
	    }

	    return $filters;
	}

	public function getSiteWideFilters( $element = SOCIAL_TYPE_USER )
	{
	    $db     = FD::db();
	    $sql 	= $db->sql();

	    $query = 'select a.* from `#__social_search_filter` as a';
	    $query .= ' where a.`element` = ' . $db->Quote( $element );
	    $query .= ' and a.`sitewide` = 1';

	    $sql->raw( $query );
	    $db->setQuery( $sql );

	    $results = $db->loadObjectList();

	    $filters = array();
	    if( $results )
	    {
	    	foreach( $results as $row )
	    	{
	    		$tbl = FD::table( 'SearchFilter' );
	    		$tbl->bind( $row );

	    		$filters[] = $tbl;
	    	}
	    }

	    return $filters;
	}


	public function getFieldOptionList( $uniqueKey, $element )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$query = "select distinct c.`title`, c.`value`";
		$query .= " from `#__social_fields` as a";
		$query .= " inner join `#__social_fields_options` as c";
		$query .= " on a.`id` = c.`parent_id`";
		$query .= " where a.`unique_key` = '$uniqueKey'";
		$query .= " and c.`key` = 'items'";
		$query .= " and c.`value` is not null";
		$query .= " order by c.`ordering`";

		$sql->raw($query);
		$db->setQuery($sql);

		$result = $db->loadObjectList();
		return $result;
	}

	public function getAdvSearchItems( $options, $next_limit = null, $limit = 0 )
	{
	    $db     	= FD::db();
	    $sql 		= $db->sql();

	    $my     	= FD::user();
	    $config 	= FD::config();
		$privacy 	= FD::privacy( $my->id );


	    //process item limit
		$defaultLimit = $limit;

	    if( ! $options )
	    {
	    	return null;
	    }

	    $match = isset( $options[ 'match' ] ) ? $options[ 'match' ] : 'all';
	    $sort = isset( $options[ 'sort' ] ) ? $options[ 'sort' ] : 'default';
	    $query = $this->buildAdvSearch( $match, $options );

	    if (! $query) {
	    	return array();
	    }

	    // this is for testing
	    // $query = $this->buildAdvSearchTEST( $match, $options );
		// $cntQuery = str_replace( 'select distinct a.`id`', 'select count(1) as `CNT`', $query );


	    // this is the ori one.
		$cntQuery = str_replace( 'select u.`id`', 'select count(1) as `CNT`', $query );

		$sql->raw( $cntQuery );
	    $db->setQuery( $sql );
		$this->total = $db->loadResult();

		if(! $this->total )
		{
			// no need further processing
			return array();
		}

		// query sorting
		if ($sort != 'default') {
			$query .= ' ORDER BY ' . $db->nameQuote('u.' . $sort) . ' DESC';
		}

		// echo $query;exit;

		// this mainQuery shouldnt contain the limit for later use in data filling.
		$mainQuery = $query;

	    if( is_null( $next_limit ) )
	    {
	    	$query .= ' LIMIT ' . $limit;
	    	$next_limit = $limit;
	    }
	    else
	    {
	    	$query .= ' LIMIT ' . $next_limit . ',' . $limit;
	    	$next_limit = $next_limit + $limit;
	    }

	    $sql->clear();
	    $sql->raw( $query );
	    $db->setQuery( $sql );

	    $results = $db->loadColumn();

	    $users = array();
	    if( $results )
	    {
	    	$filtered = array();

			for( $i = 0; $i < count( $results ); $i++  )
			{
				$userId 			= $results[ $i ];

				$privacykeys = 'profiles.search';
				$addItem = false;

				if( $config->get( 'users.indexer.privacy' ) )
				{
					$addItem = $privacy->validate( $privacykeys, $userId );
				}
				else
				{
					$addItem = true;
				}

				if( $addItem )
				{
					$filtered[] = $userId;
				}
			}

			if( count( $filtered ) < $defaultLimit && $next_limit > 0)
			{
				$this->fillAdvSearchData( $defaultLimit, $next_limit, $filtered, $mainQuery);
			}


			$users 	= FD::user( $filtered );

			// lets cache the user's customfield data.
			$userModel 	= FD::model('Users');
			$userModel->setUserFieldsData($filtered);

			if( $next_limit >= $this->total )
			{
				$next_limit = '-1';
			}
	    }
	    else
	    {
	    	$next_limit = '-1';
	    }

		//setting next limit for loadmore
		$this->next_limit = $next_limit;


		return $users;
	}


	public function fillAdvSearchData( $defaultLimit, $next_limit, &$filtered, $query )
	{
		$db 		= FD::db();

		$my     	= FD::user();
		$privacy 	= FD::privacy( $my->id );
		$config 	= FD::config();
		$cnt 		= 0;

		$tryLimit 	= 2;

		do{
			$startLimit = $next_limit;

			if( $next_limit == '-1' )
				return;

			$queryLimit = ' LIMIT ' . $next_limit . ',' . $defaultLimit;
			$nextQuery = $query . $queryLimit;

			$db->setQuery( $nextQuery );

			$results = $db->loadColumn();

			if( count( $results ) > 0 )
			{
				for( $i = 0; $i < count( $results ); $i++ )
				{
					$userId 		= $results[$i];
					$privacykeys 	= 'profiles.search';

					$addItem = false;
					if( $config->get( 'users.indexer.privacy' ) )
					{
						$addItem = $privacy->validate( $privacykeys, $userId );
					}
					else
					{
						$addItem = true;
					}

					if( $addItem )
					{
						$filtered[] = $userId;
						$next_limit = $next_limit + 1;

						if( count( $filtered ) == $defaultLimit )
						{
							break;
						}

					}
				}

				$cnt = count( $filtered );
			}
			else
			{
				$next_limit = '-1';
			}

			$tryLimit--;

		} while ( ( $cnt < $defaultLimit && $next_limit != '-1' ) && $tryLimit > 0 );


	}


	// for debug purposes
	private function buildAdvSearchTEST( $match, $options )
	{
		$query = 'select distinct a.`id`';
		$query .= ' from `#__users` as a';

		return $query;
	}

	public function buildAdvSearch( $match, $options )
	{
		$config = FD::config();
	    $db     = FD::db();
	    $sql 	= $db->sql();

	    $avatarOnly = $options['avatarOnly'];
	    $onlineOnly = isset($options['onlineOnly']) ? $options['onlineOnly'] : false;
	    $useProfileId = isset( $options['profile'] ) ? $options['profile'] : '';


	    if (is_string($options['criterias'])) {
	    	$options['criterias'] = array($options['criterias']);
	    	$options['conditions'] = array($options['conditions']);
	    	$options['datakeys'] = array($options['datakeys']);
	    	$options['operators'] = array($options['operators']);
	    }

	    $tmps = array();

	    // data clean up.
		for($i = 0; $i < count($options['criterias']); $i++ ) {

			$criteria = $options[ 'criterias' ][$i];
			$condition = $options[ 'conditions' ][$i];
			$datakey = $options[ 'datakeys' ][$i];
			$operator = $options[ 'operators' ][$i];

			$condition = trim($condition);

			if ($condition && $condition != '|') {
				$tmps['criterias'][] = $criteria;
				$tmps['datakeys'][] = $datakey;
				$tmps['operators'][] = $operator;
				$tmps['conditions'][] = $condition;
			}
		}

		$options['criterias'] = $tmps['criterias'];
		$options['conditions'] = $tmps['conditions'];
		$options['datakeys'] = $tmps['datakeys'];
		$options['operators'] = $tmps['operators'];

	    if ($match == 'all') {
	    	$fieldTable = $this->buildAndConditionTable( $options );
		} else {
			$fieldTable = $this->buildORConditionTable( $options );
		}

		if (!$fieldTable) {
			return '';
		}

		// echo $fieldTable;

		$query = 'select u.`id`';
		$query .= ' from `#__users` as u';
		$query .= ' inner join ' . $fieldTable . ' ON xf.uid = u.id';

		// exclude esad users
		$query .= ' INNER JOIN `#__social_profiles_maps` as upm on u.`id` = upm.`user_id`';
		$query .= ' INNER JOIN `#__social_profiles` as up on upm.`profile_id` = up.`id` and up.`community_access` = 1';


		if ($avatarOnly) {
			$query .= ' inner join `#__social_albums` as c on u.id = c.uid and c.`type` = ' . $db->Quote( SOCIAL_TYPE_USER ) . ' and c.`core` = ' . $db->Quote( '1' ) ;
		}

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block
		    $query .= ' LEFT JOIN ' . $db->nameQuote( '#__social_block_users' ) . ' as bus';
		    $query .= ' ON u.' . $db->nameQuote( 'id' ) . ' = bus.' . $db->nameQuote( 'user_id' ) ;
		    $query .= ' AND bus.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( JFactory::getUser()->id ) ;
		}

		$query .= ' WHERE u.`block` = 0';

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block continue here
		    $query .= ' AND bus.' . $db->nameQuote( 'id' ) . ' IS NULL';
		}

		if ($useProfileId) {
			$query .= ' and upm.`profile_id` = ' . $db->Quote($useProfileId) ;
		}


		if ($onlineOnly) {
			$query .= ' and EXISTS( ';
			$query .= 'SELECT ' . $db->nameQuote('userid') . ' FROM ' . $db->nameQuote('#__session') . ' AS us WHERE ' . $db->nameQuote( 'userid' ) . ' = u.' . $db->nameQuote( 'id' );
			$query .= ' AND `client_id` = ' . $db->Quote( 0 );
			$query .= ')';
		}

		// echo $query;exit;

		return $query;
	}

	private function buildAndConditionTable( $options )
	{
	    $db     = FD::db();

	    $queries = array();
		$filterCount = count( $options[ 'criterias' ] );

		// var_dump($options);exit;

		// current viewing user.
		$viewer = FD::user()->id;

		for ($i = 0; $i < $filterCount; $i++)
		{
			$criteria 	= is_string($options['criterias']) ? $options['criterias'] : $options['criterias'][$i];

			if (empty($criteria)) {
				continue;
			}

			$datakey 	= '';
			if (is_string($options['datakeys'])) {
				$datakey = $options['datakeys'];
			} else if (isset($options['datakeys'][$i])) {
				$datakey = $options['datakeys'][$i];
			}

			$operator 	= is_string($options['operators']) ? $options['operators'] : $options['operators'][$i];
			$condition 	= is_string($options['conditions']) ? $options['conditions'] : $options['conditions'][$i];

			$field  	= explode( '|', $criteria );

			$fieldCode 	= $field[0];
			$fieldType 	= $field[1];

			if ($fieldType == 'address' && $datakey == 'distance') {
				$query = $this->buildAddressDistanceSQL($criteria, $operator, $condition, $datakey);
				if ($query) {
					$queries[] = $query;
				}

			} else if ($fieldType == 'joomla_username') {

				$query = $this->buildUsernameSQL($criteria, $operator, $condition, $datakey);
				if ($query) {
					$queries[] = $query;
				}

			} else if ($fieldType == 'joomla_lastlogin' || $fieldType == 'joomla_joindate') {

				$query = $this->buildJoomlaDatesSQL($criteria, $operator, $condition, $datakey);

				if ($query) {
					$queries[] = $query;
				}

			} else {

				// need to check here if this is a birthday age or not.
				if ($fieldType == 'birthday' && $datakey == 'age') {
					$ages = explode('|', $condition);
					$ageInDates = $this->convertAgeToDate($ages, $operator);
					$condition = implode('|', $ageInDates);

					// if operator == equal, we need to make it to between.
					if ($operator == 'equal') {
						$operator = 'between';
					}
				}

				if ($fieldType == 'birthday' && $datakey == 'date') {
					$dates = explode('|', $condition);

					if (count($dates) == 2) {
						$dstart = $dates[0];
						$dend = $dates[1];

						$dstart = explode(' ', $dstart);
						$dend = explode(' ', $dend);

						$dstart = $dstart[0] . ' 00:00:00';
						$dend = $dend[0] . ' 23:59:59';

						$condition = $dstart . '|' . $dend;
					}

					if (count($dates) == 1) {
						$dstart = $dates[0];

						$dstart = explode(' ', $dstart);
						$dstart = $dstart[0] . ' 00:00:00';

						$condition = $dstart;
					}
				}

				//now we need to reset the datakey as birthday field always search based on 'date' datakey
				if($fieldType == 'birthday') {
					$datakey = 'date';
				}

				$string = $this->buildConditionString( $criteria, $operator, $condition, $datakey );

				$query = 'select distinct a.`uid`';
				$query .= ' from `#__social_fields_data` as a';
				$query .= ' inner join `#__social_fields` as b on a.`field_id` = b.`id`';
				$query .= ' left join `#__social_privacy_items` as pi on a.`field_id` = pi.`uid`';
				$query .= '		and a.`uid` = pi.`user_id` and pi.`type` = ' . $db->Quote(SOCIAL_TYPE_FIELD);
				$query .= ' where a.`type` = ' . $db->Quote( SOCIAL_TYPE_USER );

				// privacy here.
				$query .= ' AND (';

				//public
				$query .= ' (pi.`value` = ' . $db->Quote( SOCIAL_PRIVACY_PUBLIC ) . ') OR (pi.`value` IS NULL) OR ';

				//member
				$query .= ' ( (pi.`value` = ' . $db->Quote(SOCIAL_PRIVACY_MEMBER) . ') AND (' . $viewer . ' > 0 ) ) OR ';

				//friends of friends
				$query .= ' ( (pi.`value` = ' . $db->Quote(SOCIAL_PRIVACY_FRIENDS_OF_FRIEND) . ') AND ( (' . $this->generateMutualFriendSQL( $viewer, 'pi.`user_id`' ) . ') > 0 ) ) OR ';

				//friends of friends
				$query .= ' ( (pi.`value` = ' . $db->Quote(SOCIAL_PRIVACY_FRIENDS_OF_FRIEND) . ') AND ( (' . $this->generateIsFriendSQL( 'pi.`user_id`', $viewer ) . ') > 0 ) ) OR ';

				//friends
				$query .= ' ( (pi.`value` = ' . $db->Quote(SOCIAL_PRIVACY_FRIEND) . ') AND ( (' . $this->generateIsFriendSQL( 'pi.`user_id`', $viewer ) . ') > 0 ) ) OR ';

				//only me
				$query .= ' ( (pi.`value` = ' . $db->Quote(SOCIAL_PRIVACY_ONLY_ME) . ') AND ( pi.`user_id` = ' . $viewer . ' ) ) OR ';

				//viewer items
				$query .= ' (pi.`user_id` = ' . $viewer . ')';

				// privacy ended here
				$query .= ')';

				$query .= ' AND ';
				$query .= $string;

				$queries[] = $query;
			}

		}

		if (!$queries) {
			return '';
		}

		$union = ( count($queries) > 1 ) ? implode( ') UNION ALL (' , $queries) : $queries[0];
		$union = '(' . $union . ')';

		$groupCnt = $filterCount - 1;

		// here is the key to filter users ( by using group by ) which 'meet' all the conditions.
		$query = '( select * from (' . $union . ') as x group by x.`uid` having ( count(x.`uid`)  > ' . $groupCnt . ' ) ) as xf';

		return $query;
	}

	private function generateMutualFriendSQL( $source, $target )
	{
		$query = '';

		$query = "select count(1) from (";
		$query .= "	select af1.`actor_id` as `fid` from `#__social_friends` as af1 where af1.`target_id` = $source and af1.`state` = 1";
		$query .= "		union ";
		$query .= "	select af2.`target_id` as `fid`  from `#__social_friends` as af2 where af2.`actor_id` = $source and af2.`state` = 1";
		$query .= " ) as x";
		$query .= " where exists (";
		$query .= "	select bf1.`actor_id` from `#__social_friends` as bf1 where bf1.`target_id` = $target and bf1.`actor_id` = x.`fid` and bf1.`state` = 1";
		$query .= " 	union ";
		$query .= "	select bf2.`target_id` from #__social_friends as bf2 where bf2.`actor_id` = $target and bf2.`target_id` = x.`fid`  and bf2.`state` = 1";
		$query .= " )";

		return $query;
	}

	private function generateIsFriendSQL( $source, $target )
	{
		$query = "select count(1) from `#__social_friends` where ( `actor_id` = $source and `target_id` = $target) OR (`target_id` = $source and `actor_id` = $target) and `state` = 1";

		return $query;
	}

	private function convertAgeToDate($ages, $operator)
	{
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

		$dates = array();

		// year 24
		// startdate : 1990-01-01 00:00:01
		// enddate : 1990-08-21 23:59:59

		switch($operator) {
			case 'equal':
			case 'between':
				$dates[0] = $startdate;
				$dates[1] = $enddate;
				break;
			case 'greater':
				$dates[0] = $startdate; // greater mean younger, in this case, younger than 24, so we use startdate. raw > 1990-01-01
				break;
			case 'greaterequal':
				$dates[0] = $enddate; // same as greater but it include the 24 year it itself. so we use the enddate raw >= 1990-08-21
				break;
			case 'less':
				$dates[0] = $enddate; // less mean older, in this case, older than 24, so we use enddate. raw < 1990-08-21
				break;
			case 'lessequal':
				$dates[0] = $startdate; // same is less but it include the 24 year as well. so we use the start date. raw <= 1990-01-01
				break;

		}


		return $dates;
	}

	private function buildORConditionTable( $options )
	{
	    $db     = FD::db();

	    $viewer = FD::user()->id;

		$query = 'select a.`uid`';
		$query .= ' from `#__social_fields_data` as a';
		$query .= ' inner join `#__social_fields` as b on a.`field_id` = b.`id`';
		$query .= ' left join `#__social_privacy_items` as pi on a.`field_id` = pi.`uid`';
		$query .= '		and a.`uid` = pi.`user_id` and pi.`type` = ' . $db->Quote(SOCIAL_TYPE_FIELD);
		$query .= ' where a.`type` = ' . $db->Quote( SOCIAL_TYPE_USER );

		// privacy here.
		$query .= ' AND (';

		//public
		$query .= ' (pi.`value` = ' . $db->Quote( SOCIAL_PRIVACY_PUBLIC ) . ') OR (pi.`value` IS NULL) OR ';

		//member
		$query .= ' ( (pi.`value` = ' . $db->Quote(SOCIAL_PRIVACY_MEMBER) . ') AND (' . $viewer . ' > 0 ) ) OR ';

		//friends of friends
		$query .= ' ( (pi.`value` = ' . $db->Quote(SOCIAL_PRIVACY_FRIENDS_OF_FRIEND) . ') AND ( (' . $this->generateMutualFriendSQL( $viewer, 'pi.`user_id`' ) . ') > 0 ) ) OR ';

		//friends of friends
		$query .= ' ( (pi.`value` = ' . $db->Quote(SOCIAL_PRIVACY_FRIENDS_OF_FRIEND) . ') AND ( (' . $this->generateIsFriendSQL( 'pi.`user_id`', $viewer ) . ') > 0 ) ) OR ';

		//friends
		$query .= ' ( (pi.`value` = ' . $db->Quote(SOCIAL_PRIVACY_FRIEND) . ') AND ( (' . $this->generateIsFriendSQL( 'pi.`user_id`', $viewer ) . ') > 0 ) ) OR ';

		//only me
		$query .= ' ( (pi.`value` = ' . $db->Quote(SOCIAL_PRIVACY_ONLY_ME) . ') AND ( pi.`user_id` = ' . $viewer . ' ) ) OR ';

		//viewer items
		$query .= ' (pi.`user_id` = ' . $viewer . ')';

		// privacy ended here
		$query .= ')';

		$query .= ' and (';

	    $queries = array();
	    $oQueries = array();
		$filterCount = count( $options[ 'criterias' ] );

		for ($i = 0; $i < $filterCount; $i++) {
			$criteria 	= is_string($options['criterias']) ? $options['criterias'] : $options['criterias'][$i];

			if (empty($criteria)) {
				continue;
			}

			$datakey 	= '';
			if (is_string($options['datakeys'])) {
				$datakey = $options['datakeys'];
			} else if (isset($options['datakeys'][$i])) {
				$datakey = $options['datakeys'][$i];
			}
			$operator 	= is_string($options['operators']) ? $options['operators'] : $options['operators'][$i];
			$condition 	= is_string($options['conditions']) ? $options['conditions'] : $options['conditions'][$i];

			$field  	= explode( '|', $criteria );

			$fieldCode 	= $field[0];
			$fieldType 	= $field[1];

			if ($fieldType == 'address' && $datakey == 'distance') {
				$aQuery = $this->buildAddressDistanceSQL($criteria, $operator, $condition, $datakey);

				if ($aQuery) {
					$queries[] = $aQuery;
				}

			} else if ($fieldType == 'joomla_username') {

				$aQuery = $this->buildUsernameSQL($criteria, $operator, $condition, $datakey);
				if ($aQuery) {
					$queries[] = $aQuery;
				}

			} else if ($fieldType == 'joomla_lastlogin' || $fieldType == 'joomla_joindate') {

				$aQuery = $this->buildJoomlaDatesSQL($criteria, $operator, $condition, $datakey);
				if ($aQuery) {
					$queries[] = $aQuery;
				}

			} else {

				// need to check here if this is a birthday age or not.
				if ($fieldType == 'birthday' && $datakey == 'age') {
					$ages = explode('|', $condition);
					$ageInDates = $this->convertAgeToDate($ages, $operator);
					$condition = implode('|', $ageInDates);

					// if operator == equal, we need to make it to between.
					if ($operator == 'equal') {
						$operator = 'between';
					}
				}

				if ($fieldType == 'birthday' && $datakey == 'date') {
					$dates = explode('|', $condition);

					if (count($dates) == 2) {
						$dstart = $dates[0];
						$dend = $dates[1];

						$dstart = explode(' ', $dstart);
						$dend = explode(' ', $dend);

						$dstart = $dstart[0] . ' 00:00:00';
						$dend = $dend[0] . ' 23:59:59';

						$condition = $dstart . '|' . $dend;
					}

					if (count($dates) == 1) {
						$dstart = $dates[0];

						$dstart = explode(' ', $dstart);
						$dstart = $dstart[0] . ' 00:00:00';

						$condition = $dstart;
					}
				}

				//now we need to reset the datakey as birthday field always search based on 'date' datakey
				if($fieldType == 'birthday') {
					$datakey = 'date';
				}

				$string = $this->buildConditionString( $criteria, $operator, $condition, $datakey );

				// echo $string;
				$oQueries[] = $string;
			}
		}

		$or = '';
		if ($oQueries) {
			$or = ( count($oQueries) > 1 ) ? implode( ' OR ' , $oQueries) : $oQueries[0];
		}

		$query .= $or;
		$query .= ' )';

		if ($queries || $oQueries) {
			$union = '';

			if ($queries) {
				$union = ( count($queries) > 1 ) ? implode( ' UNION ' , $queries) : $queries[0];
			}

			if (count($oQueries) == 0 && $union) {
				// this mean the search only has one condition and this condition is based on the address distance
				$query = $union;
			} else if($oQueries && $union) {
				$query .= ' UNION ' . $union;
			}
		} else {
			return '';
		}

		$result = '( select distinct * from (' . $query . ') as x ) as xf';
		return $result;
	}

	private function buildUsernameSQL($criteria, $operator, $condition, $datakey)
	{
		$db = FD::db();

		$query = 'select a.`id` as `uid`';
		$query .= ' FROM `#__users` as a';
		$query .= ' where a.`username` = ' . $db->Quote($condition);
		$query .= ' and a.`block` = 0';

		return $query;
	}

	private function buildJoomlaDatesSQL($criteria, $operator, $condition, $datakey)
	{
		$db = FD::db();

		$field  	= explode( '|', $criteria );

		$fieldCode 	= $field[0];
		$fieldType 	= $field[1];

		$column = ($fieldType == 'joomla_lastlogin') ? 'a.lastvisitDate': 'a.registerDate';


		$query = 'select a.`id` as `uid`';
		$query .= ' FROM `#__users` as a';
		$query .= ' where a.`block` = 0';


		$tzOffset = FD::date()->getOffset();

		if ($operator != 'between') {
			// we need to use jdate or else the offset will be messed up.
			$inputDate = new JDate($condition, $tzOffset);
			$condition = $inputDate->toSql();
		}

		$cond = '';
		switch( $operator )
		{
			case 'notequal':
				$cond .= ' and ' . $db->nameQuote($column) . ' != ' . $db->Quote( $condition );
				break;

			case 'greater':
				$cond .= ' and ' . $db->nameQuote($column) . ' > ' . $db->Quote( $condition );
				break;

			case 'greaterequal':
				$cond .= ' and ' . $db->nameQuote($column) . ' >= ' . $db->Quote( $condition );
				break;

			case 'less':
				$cond .= ' and ' . $db->nameQuote($column) . ' < ' . $db->Quote( $condition );
				break;

			case 'lessequal':
				$cond .= ' and ' . $db->nameQuote($column) . ' <= ' . $db->Quote( $condition );
				break;

			case 'between':
				$dates = explode( '|', $condition );

				$inputDate1 = new JDate($dates[0], $tzOffset);
				$dates[0] = $inputDate1->toSql();

				$inputDate2 = new JDate($dates[1], $tzOffset);
				$dates[1] = $inputDate2->toSql();

				$cond .= ' and (' . $db->nameQuote($column) . ' >= ' . $db->Quote( $dates[0] ) . ' and ' . $db->nameQuote($column) . ' <= ' . $db->Quote( $dates[1] ) . ')';
				break;

			case 'equal':
			default:
				$cond .= ' and ' . $db->nameQuote($column) . ' = ' . $db->Quote( $condition );
				break;
		}

		$query .= $cond;

		return $query;
	}

	private function buildAddressDistanceSQL($criteria, $operator, $condition, $datakey)
	{
		$db = FD::db();
		$config = FD::config();
		$searchUnit = $config->get('general.location.proximity.unit','mile');

        $unit['mile'] = 69;
        $unit['km'] = 111;
        $radius['mile'] = 3959;
        $radius['km'] = 6371;

        $query = '';
	    $fieldCode 	= '';
	    $fieldType 	= '';
	    $viewer = FD::user()->id;

		$conditions = explode( '|', $condition);
		$distance = isset($conditions[0]) && $conditions[0] ? $conditions[0] : '';

		$mylat = isset($conditions[1]) && $conditions[1] ? $conditions[1] : '';
		$mylon = isset($conditions[2]) && $conditions[2] ? $conditions[2] : '';

		if (!$mylat && !$mylon) {
			// lets get the lat and lon from current logged in user address
			$my = FD::user();
			$address = $my->getFieldValue('ADDRESS');
			$mylat = $address->value->latitude;
			$mylon = $address->value->longitude;

			// var_dump($address->value->latitude, $address->value->longitude);
		}

		// $mylat = '3.2287897';
		// $mylon = '101.6402272';

		// var_dump($mylat, $mylon);


		if ($distance && $mylat && $mylon) {
    		// $mylat = $address->data['latitude'];
    		// $mylon = $address->data['longitude'];

	        $dist = (int) $distance; // 5 miles
	        $lon1 = $mylon - $dist / abs( cos(deg2rad($mylat) ) * $unit[$searchUnit]);
	        $lon2 = $mylon + $dist / abs( cos(deg2rad($mylat) ) * $unit[$searchUnit]);
	        $lat1 = $mylat - ($dist / $unit[$searchUnit]);
	        $lat2 = $mylat + ($dist / $unit[$searchUnit]);

			$query = " select distinct geo.`uid` from (";
			$query .= " SELECT uid, field_id, ( $radius[$searchUnit] * acos( cos( radians($mylat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($mylon) ) + sin( radians($mylat) ) * sin( radians( lat ) ) ) ) AS distance";
			$query .= " FROM (select a.`uid`, a.field_id, a.`lat`, b.`lng` from";
			$query .= "		(select `uid`, `field_id`, `raw` as `lat` from `#__social_fields_data` where `type` = '".SOCIAL_TYPE_USER."' and `datakey` = 'latitude'";
			$query .= "			and cast(`raw` as decimal(10, 6)) between '$lat1' and '$lat2') as a";
			$query .= "			inner join (select `uid`, `field_id`, `raw` as `lng` from `#__social_fields_data` where `type` = '" . SOCIAL_TYPE_USER . "' and `datakey` = 'longitude'";
			$query .= " 			and cast(`raw` as decimal(10, 6)) between '$lon1' and '$lon2') as b on a.`uid` = b.`uid`) as x";
			$query .= " ) as geo";

			$query .= " left join `#__social_privacy_items` as pi on geo.`field_id` = pi.`uid`";
			$query .= "		and geo.`uid` = pi.`user_id` and pi.`type` = '" . SOCIAL_TYPE_FIELD . "'";

			if ($operator == 'greater') {
				$query .= " where geo.`distance` > $dist";
			} else {
				$query .= " where geo.`distance` <= $dist";
			}

			// privacy here.
			$query .= " AND (";

			//public
			$query .= " (pi.`value` = " . $db->Quote( SOCIAL_PRIVACY_PUBLIC ) . ") OR (pi.`value` IS NULL) OR ";

			//member
			$query .= " ( (pi.`value` = " . $db->Quote(SOCIAL_PRIVACY_MEMBER) . ") AND ($viewer > 0 ) ) OR ";

			//friends of friends
			$query .= " ( (pi.`value` = " . $db->Quote(SOCIAL_PRIVACY_FRIENDS_OF_FRIEND) . ") AND ( (" . $this->generateMutualFriendSQL( $viewer, 'pi.`user_id`' ) . ") > 0 ) ) OR ";

			//friends of friends
			$query .= " ( (pi.`value` = " . $db->Quote(SOCIAL_PRIVACY_FRIENDS_OF_FRIEND) . ") AND ( (" . $this->generateIsFriendSQL( 'pi.`user_id`', $viewer ) . ") > 0 ) ) OR ";

			//friends
			$query .= " ( (pi.`value` = " . $db->Quote(SOCIAL_PRIVACY_FRIEND) . ") AND ( (" . $this->generateIsFriendSQL( 'pi.`user_id`', $viewer ) . ") > 0 ) ) OR ";

			//only me
			$query .= " ( (pi.`value` = " . $db->Quote(SOCIAL_PRIVACY_ONLY_ME) . ") AND ( pi.`user_id` = '$viewer') ) OR ";

			//viewer items
			$query .= " (pi.`user_id` = '$viewer')";

			// privacy ended here
			$query .= ")";

		}

		return $query;
	}


	private function buildConditionString( $criteria, $operator, $condition, $datakey = '' )
	{
	    $db     = FD::db();

	    $fieldCode 	= '';
	    $fieldType 	= '';

	    if( !empty( $criteria ) )
	    {
			$field  	= explode( '|', $criteria );

			$fieldCode 	= $field[0];
			$fieldType 	= $field[1];
	    }

	    // special handling on gender to allow multi value search
	    if ($fieldType == 'gender' && strpos($condition, '|') !== false) {
	    	$condition = explode('|', $condition);
	    	$operator = 'in';
	    }

		$cond = '( b.`unique_key` = ' . $db->Quote( $fieldCode );

		if ($datakey) {
			$cond .= ' and a.`datakey` = ' . $db->Quote($datakey);
		}

		switch( $operator )
		{
			case 'in':

				if (is_array($condition)) {
					$tmp = '';
					foreach($condition as $c) {
						$tmp .= ($tmp) ? ',' . $db->Quote($c) : $db->Quote($c);
					}

					$condition = $tmp;
				} else {
					$condition = $db->Quote($condition);
				}

				$cond .= ' and a.`raw` IN (' . $condition . ')';
				break;


			case 'notequal':
				$cond .= ' and a.`raw` != ' . $db->Quote( $condition );
				break;

			case 'contain':
				$condition = str_replace( ' ', '%', $condition );
				$cond .= ' and a.`raw` LIKE ' . $db->Quote( '%' . $condition . '%' );
				break;

			case 'notcontain':
				$condition = str_replace( ' ', '%', $condition );
				$cond .= ' and a.`raw` NOT LIKE ' . $db->Quote( '%' . $condition . '%' );
				break;

			case 'startwith':
				$cond .= ' and a.`raw` LIKE ' . $db->Quote( $condition . '%' );
				break;

			case 'endwith':
				$cond .= ' and a.`raw` LIKE ' . $db->Quote( '%' . $condition );
				break;

			case 'blank':
				$cond .= ' and (a.`raw` = ' . $db->Quote( '' ) . ' OR a.`raw` IS NULL)';
				break;

			case 'notblank':
				$cond .= ' and a.`raw` != ' . $db->Quote( '' ) . ' and a.`raw` IS NOT NULL';
				break;

			case 'greater':
				$cond .= ' and a.`raw` > ' . $db->Quote( $condition );
				break;

			case 'greaterequal':
				$cond .= ' and a.`raw` >= ' . $db->Quote( $condition );
				break;

			case 'less':
				$cond .= ' and a.`raw` < ' . $db->Quote( $condition );
				break;

			case 'lessequal':
				$cond .= ' and a.`raw` <= ' . $db->Quote( $condition );
				break;

			case 'between':
				$dates = explode( '|', $condition );
				$cond .= ' and a.`raw` >= ' . $db->Quote( $dates[0] ) . ' and a.`raw` <= ' . $db->Quote( $dates[1] );
				break;

			case 'equal':
			default:
				$cond .= ' and a.`raw` = ' . $db->Quote( $condition );
				break;

		}

		$cond .= ')';

		return $cond;
	}


	public function getItems( $keywords, $type = '', $next_limit = null, $limit = 0 )
	{
	    $db     = FD::db();
	    $sql 	= $db->sql();
	    $my     = FD::user();
	    $config = FD::config();

	    $coreType = array( SOCIAL_INDEXER_TYPE_USERS, SOCIAL_INDEXER_TYPE_PHOTOS, SOCIAL_INDEXER_TYPE_LISTS,  SOCIAL_INDEXER_TYPE_GROUPS, SOCIAL_INDEXER_TYPE_EVENTS );

		if( empty( $keywords ) )
			return;

		$where		= array();
		$wheres		= array();
		$words		= explode( ' ', $keywords );

		if( count( $words ) > 1 )
		{
			$tmp = array();
			$cnt = count( $words ) - 1;
			for( $i = 0; $i < $cnt; $i++ )
			{
				$tmp[] = $words[ $i ] . ' ' . $words[ $i + 1 ];
			}

			$words	= $tmp;
		}

		foreach ($words as $word)
		{
			$word		= $db->Quote( '%'.$db->escape( $word, true ).'%', false );

			// $where[]	= 'a.`title` LIKE ' . $word;
			$where[]	= 'a.`content` LIKE ' . $word;


			$wheres[] 	= implode( ' OR ', $where );
		}
		$where	= ' (' . implode( ') OR (' , $wheres ) . ')';


		$mainQuery = array();


		//process item limit
		$defaultLimit = $limit;

		$queryLimit = '';
		if( $next_limit )
		{
			$queryLimit = ' LIMIT ' . $next_limit . ', ' . $defaultLimit;
			$next_limit = $next_limit + $defaultLimit;
		}
		else
		{
			$queryLimit = ' LIMIT ' . $defaultLimit;
			$next_limit = $defaultLimit;
		}


		// users
		$query = 'select a.* FROM `#__social_indexer` as a';
		$query .= ' inner join `#__users` as u ON a.`uid` = u.`id` and u.`block` = ' . $db->Quote( '0' );
		$query .= ' where `utype` = ' . $db->Quote( SOCIAL_INDEXER_TYPE_USERS );
		$query .= ' and (' . $where . ')';
		if( $type == '' || $type == SOCIAL_INDEXER_TYPE_USERS)
			$mainQuery[] = $query;


		if( $my->id )
		{
			// own photos
			$query = 'select a.* FROM `#__social_indexer` as a';
			$query .= ' where `utype` = ' . $db->Quote( SOCIAL_INDEXER_TYPE_PHOTOS );
			$query .= ' and `ucreator` = ' . $db->Quote( $my->id );
			$query .= ' and (' . $where . ')';
			if( $type == '' || $type == SOCIAL_INDEXER_TYPE_PHOTOS)
				$mainQuery[] = $query;


			// own friend list
			$query = 'select a.* FROM `#__social_indexer` as a';
			$query .= ' where `utype` = ' . $db->Quote( SOCIAL_INDEXER_TYPE_LISTS );
			$query .= ' and `ucreator` = ' . $db->Quote( $my->id );
			$query .= ' and (' . $where . ')';
			if( $type == '' || $type == SOCIAL_INDEXER_TYPE_LISTS)
				$mainQuery[] = $query;
		}

		// groups
		$query = 'select a.* FROM `#__social_indexer` as a';
		$query .= ' where `utype` = ' . $db->Quote( SOCIAL_INDEXER_TYPE_GROUPS );
		$query .= ' and (' . $where . ')';
		if( $type == '' || $type == SOCIAL_INDEXER_TYPE_GROUPS)
			$mainQuery[] = $query;

		// events
		$query = 'select a.* FROM `#__social_indexer` as a';
		$query .= ' where `utype` = ' . $db->Quote( SOCIAL_INDEXER_TYPE_EVENTS );
		$query .= ' and (' . $where . ')';
		if( $type == '' || $type == SOCIAL_INDEXER_TYPE_EVENTS)
			$mainQuery[] = $query;

		// others
		$query = 'select a.* FROM `#__social_indexer` as a';
		if( $type && !in_array( $type, $coreType) )
		{
			$query .= ' where `utype` = ' . $db->Quote( $type );
		}
		else
		{
			$query .= ' where `utype` NOT IN (' . $db->Quote( SOCIAL_INDEXER_TYPE_USERS ) . ',' . $db->Quote( SOCIAL_INDEXER_TYPE_PHOTOS ) . ',' . $db->Quote( SOCIAL_INDEXER_TYPE_LISTS ) . ',' . $db->Quote( SOCIAL_INDEXER_TYPE_GROUPS )  . ')';
		}
		$query .= ' and (' . $where . ')';
		if( $type == '' || ( $type != SOCIAL_INDEXER_TYPE_USERS && $type != SOCIAL_INDEXER_TYPE_PHOTOS && $type != SOCIAL_INDEXER_TYPE_LISTS && $type != SOCIAL_INDEXER_TYPE_GROUPS ) )
			$mainQuery[] = $query;





		if( ! $mainQuery )
		{
			// this mean the user is a guest and trying to click on the photos / friend list filtering.
			$this->next_limit = '-1';
			return array();
		}


		$mainQuery = '(' . implode( ') UNION (', $mainQuery ) . ')';
		$mainQuery = 'select * FROM ( ' . $mainQuery . ' ) as x';

		// query for total count.
		$cntQuery = 'select COUNT(1) FROM ( ' . $mainQuery . ' ) as x';


		// continue
		$mainQuery .= ' order by x.`utype` desc, x.`last_update` desc';

		// limit
		$query = $mainQuery . $queryLimit;

		// getting items count.
		$sql->clear();
		$sql->raw( $cntQuery );

		$db->setQuery( $sql );

		$this->total = $db->loadResult();

		$sql->clear();
		$sql->raw( $query );

		// echo $sql;exit;

		$db->setQuery( $sql );
		$result = $db->loadObjectList();

		$filtered 	= array();
		$privacy 	= FD::privacy( $my->id );

		if( count( $result ) > 0 )
		{
			if( count( $result ) < $defaultLimit )
			{
				//this mean the resultset is the last batch
				$next_limit = '-1';
			}

			//foreach( $result as $item )
			for( $i = 0; $i < count( $result ); $i++  )
			{
				$item 			=& $result[ $i ];

				$privacy_key 	= ( $item->utype == SOCIAL_INDEXER_TYPE_USERS ) ? 'profiles' : $item->utype;
				$privacy_rule 	= ( $item->utype == SOCIAL_INDEXER_TYPE_USERS ) ? 'search' : 'view';

				$keys = $privacy_key . '.' . $privacy_rule;

				$addItem = true;

				if( $keys == 'profiles.search' )
				{
					if( $config->get( 'users.indexer.privacy' ) )
					{
						$addItem = $privacy->validate( $keys, $item->ucreator );
					}
					else
					{
						$addItem = true;
					}
				}
				else if( $item->utype != SOCIAL_INDEXER_TYPE_GROUPS )
				{
					$addItem = $privacy->validate( $keys, $item->uid, $item->utype, $item->ucreator );
				}

				// if this item is a user type, the content might be from fields. let check the fields privacy.
				if( $addItem && $item->utype == SOCIAL_INDEXER_TYPE_USERS && $config->get( 'users.indexer.privacy' ) )
				{
					$smallText  = $this->verifyFieldsData( $keywords, $item->uid );

					if( $smallText === false )
					{
						// when this is false, meean the user canot view the result which is returned by the fields.
						$addItem = false;
					}
					else
					{
						$item->description = $smallText;
					}
				}

				if( $addItem )
				{
					$filtered[] = $item;
				}
			}

			if( count( $filtered ) < $defaultLimit && $next_limit > 0)
			{
				$this->fillData( $defaultLimit, $next_limit, $filtered, $mainQuery);
			}

		}
		else
		{
			$next_limit = '-1';
		}


		//setting next limit for loadmore
		$this->next_limit = $next_limit;

		// we need to adjust the total item count here due to privacy checking
		if( $this->total > count( $filtered ) && !$filtered )
		{
			$this->total = 0;
		}

		// var_dump( $groups );
		// exit;

		// echo $query;

		$groups 	= array();
		if( count( $filtered ) > 0 )
		{
			foreach( $filtered as $item )
			{
				$groups[$item->utype][] = $item;
			}
		}

		return $groups;
	}

	public function fillData( $defaultLimit, $next_limit, &$filtered, $query )
	{
		$db 		= FD::db();

		$my     	= FD::user();
		$privacy 	= FD::privacy( $my->id );
		$config 	= FD::config();
		$cnt 		= 0;

		$tryLimit 	= 2;

		do{
			$startLimit = $next_limit;

			if( $next_limit == '-1' )
				return;

			$queryLimit = ' LIMIT ' . $next_limit . ',' . $defaultLimit;
			$nextQuery = $query . $queryLimit;

			$db->setQuery( $nextQuery );

			$result = $db->loadObjectList();

			if( count( $result ) > 0 )
			{
				foreach( $result as $item )
				{
					$privacy_key 	= ( $item->utype == SOCIAL_INDEXER_TYPE_USERS ) ? 'profiles' : $item->utype;
					$privacy_rule 	= ( $item->utype == SOCIAL_INDEXER_TYPE_USERS ) ? 'search' : 'view';

					$keys = $privacy_key . '.' . $privacy_rule;

					$addItem = false;

					if( $keys == 'profiles.search' )
					{
						if( $config->get( 'users.indexer.privacy' ) )
						{
							$addItem = $privacy->validate( $keys, $item->ucreator );
						}
						else
						{
							$addItem = true;
						}
					}
					else
					{
						$addItem = $privacy->validate( $keys, $item->uid, $item->utype, $item->ucreator );
					}

					if( $addItem )
					{
						$filtered[] = $item;
						$next_limit = $next_limit + 1;

						if( count( $filtered ) == $defaultLimit )
						{
							break;
						}

					}
				}

				$cnt = count( $filtered );
			}
			else
			{
				$next_limit = '-1';
			}

			$tryLimit--;

		} while ( ( $cnt < $defaultLimit && $next_limit != '-1' ) && $tryLimit > 0 );


	}

	public function getCount()
	{
		return empty ( $this->total ) ? '0' : $this->total ;
	}




	public function getNextLimit()
	{
		return $this->next_limit;
	}

	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->pagination ) )
		{
			jimport('joomla.html.pagination');
			$this->pagination = new JPagination( $this->total , $this->getState('limitstart') , $this->getState('limit') );
		}

		return $this->pagination;
	}
}
