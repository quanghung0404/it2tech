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

class EasySocialModelSearchGroup extends EasySocialModel
{
	private $data			= null;
	private $types     		= null;
	private $next_limit    	= null;
	protected $total 			= null;

	function __construct()
	{
		parent::__construct( 'searchgroup' );
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


	    //process item limit
		$defaultLimit = $limit;

	    if( ! $options )
	    {
	    	return null;
	    }

	    $match = isset( $options[ 'match' ] ) ? $options[ 'match' ] : 'all';
	    // $sort = isset( $options[ 'sort' ] ) ? $options[ 'sort' ] : 'default';
	    $query = $this->buildAdvSearch( $match, $options );

	    // echo $query;exit;

	    if (! $query) {
	    	return array();
	    }

	    // this is for testing
	 //    $query = $this->buildAdvSearchTEST( $match, $options );
		// $cntQuery = str_replace( 'select distinct a.`id`', 'select count(1) as `CNT`', $query );

	    // this is the ori one.
		$cntQuery = str_replace( 'select distinct u.`id`', 'select count(distinct u.`id`) as `CNT`', $query );

		$sql->raw( $cntQuery );
	    $db->setQuery( $sql );
		$this->total = $db->loadResult();

		if(! $this->total )
		{
			// no need further processing
			return array();
		}

		// query sorting
		$query .= ' ORDER BY ' . $db->nameQuote('u.id') . ' DESC';

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

	    $groups = array();

	    if ($results) {

			for ($i = 0; $i < count( $results ); $i++) {
				$groups[] = FD::group($results[$i]);
			}

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


		return $groups;
	}

	// for debug purposes
	private function buildAdvSearchTEST( $match, $options )
	{
		$db     = FD::db();

		$query = 'select distinct a.`id`';
		$query .= ' from `#__social_clusters` as a';
		$query .= ' where a.cluster_type = ' . $db->Quote('group');

		return $query;
	}

	public function buildAdvSearch( $match, $options )
	{
		$config = FD::config();
	    $db     = FD::db();
	    $sql 	= $db->sql();

	    $userId = JFactory::getUser()->id;

	    $avatarOnly = $options['avatarOnly'];
	    $useProfileId = isset( $options['profile'] ) ? $options['profile'] : '';

	    if ($match == 'all') {
	    	$fieldTable = $this->buildAndConditionTable( $options );
		} else {
			$fieldTable = $this->buildORConditionTable( $options );
		}

		if (!$fieldTable) {
			return '';
		}

		$query = 'select distinct u.`id`';
		$query .= ' from ' . $db->nameQuote('#__social_clusters') . ' as u';
		$query .= ' inner join ' . $fieldTable . ' ON xf.uid = u.id';

		if (! ES::user()->isSiteAdmin() && $userId) {
			$query .= ' LEFT JOIN ' . $db->nameQuote('#__social_clusters_nodes') .' AS nodes ON ' . $db->nameQuote('u.id') . ' = ' . $db->nameQuote('nodes.cluster_id');
		}

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block
		    $query .= ' LEFT JOIN ' . $db->nameQuote( '#__social_block_users' ) . ' as bus';
		    $query .= ' ON u.' . $db->nameQuote( 'creator_uid' ) . ' = bus.' . $db->nameQuote( 'user_id' ) ;
		    $query .= ' AND bus.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( JFactory::getUser()->id ) ;
		}

		$query .= ' WHERE u.`state` = 1';
		$query .= ' AND u.`cluster_type` = ' . $db->Quote(SOCIAL_TYPE_GROUP);

		if (! ES::user()->isSiteAdmin()) {
			//cluster privacy ( cluster.type - open, closed or invite)
			if ($userId) {
				$query .= ' and (u.`type` IN (' . SOCIAL_GROUPS_PRIVATE_TYPE . ', ' . SOCIAL_GROUPS_PUBLIC_TYPE . ')';
				$query .= ' OR (u.`type` = ' . SOCIAL_GROUPS_INVITE_TYPE . ' AND nodes.`uid` = ' . $db->Quote($userId) . '))';
			} else {
				$query .= ' and u.`type` IN (' . SOCIAL_GROUPS_PRIVATE_TYPE . ', ' . SOCIAL_GROUPS_PUBLIC_TYPE . ')';
			}
		}

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block continue here
		    $query .= ' AND bus.' . $db->nameQuote( 'id' ) . ' IS NULL';
		}

		// echo $query;exit;

			// if ($useProfileId) {
		// 	$query .= ' and upm.`profile_id` = ' . $db->Quote($useProfileId) ;
		// }

		return $query;
	}

	private function buildAndConditionTable( $options )
	{
	    $db     = FD::db();

	    $queries = array();
		$filterCount = count( $options[ 'criterias' ] );

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

			} else if ($fieldType == 'title') {
				$query = $this->buildTitleSQL($criteria, $operator, $condition, $datakey);
				if ($query) {
					$queries[] = $query;
				}

			} else {

				$string = $this->buildConditionString( $criteria, $operator, $condition, $datakey );

				$query = 'select distinct a.`uid`';
				$query .= ' from `#__social_fields_data` as a';
				$query .= ' inner join `#__social_fields` as b on a.`field_id` = b.`id`';
				$query .= ' where a.`type` = ' . $db->Quote( SOCIAL_TYPE_GROUP );
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

	private function buildORConditionTable( $options )
	{
	    $db     = FD::db();

	    $viewer = FD::user()->id;

		$query = 'select a.`uid`';
		$query .= ' from `#__social_fields_data` as a';
		$query .= ' inner join `#__social_fields` as b on a.`field_id` = b.`id`';
		$query .= ' where a.`type` = ' . $db->Quote( SOCIAL_TYPE_GROUP );
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

			} else {

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

	private function buildTitleSQL($criteria, $operator, $condition, $datakey)
	{
		$db = FD::db();

		$query = 'select a.`id` as `uid`';
		$query .= ' FROM `#__social_clusters` as a';
		$query .= " where a.`cluster_type` = " . $db->Quote(SOCIAL_TYPE_GROUP);
		$query .= ' and a.`state` = 1';
		$query .= ' and a.`title` LIKE ' . $db->Quote( '%' . $condition . '%' );

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
			$query .= "		(select `uid`, `field_id`, `raw` as `lat` from `#__social_fields_data` where `type` = '".SOCIAL_TYPE_GROUP."' and `datakey` = 'latitude'";
			$query .= "			and cast(`raw` as decimal(10, 6)) between '$lat1' and '$lat2') as a";
			$query .= "			inner join (select `uid`, `field_id`, `raw` as `lng` from `#__social_fields_data` where `type` = '" . SOCIAL_TYPE_GROUP . "' and `datakey` = 'longitude'";
			$query .= " 			and cast(`raw` as decimal(10, 6)) between '$lon1' and '$lon2') as b on a.`uid` = b.`uid`) as x";
			$query .= " ) as geo";

			if ($operator == 'greater') {
				$query .= " where geo.`distance` > $dist";
			} else {
				$query .= " where geo.`distance` <= $dist";
			}

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

		$cond = '( b.`unique_key` = ' . $db->Quote( $fieldCode );

		if ($datakey) {
			$cond .= ' and a.`datakey` = ' . $db->Quote($datakey);
		}

		switch( $operator )
		{
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
