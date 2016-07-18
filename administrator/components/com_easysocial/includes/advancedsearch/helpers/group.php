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
/**
 * DB layer for EasySocial.
 *
 * @since	1.0
 * @author	Sam <sam@stackideas.com>
 */
class SocialAdvancedSearchHelperGroup
{
	public $_total 		= null;
	public $_nextlimit 	= null;
	public $displayOptions = null;

	public function __construct()
	{
		$this->_total 		= 0;
		$this->_nextlimit 	= 0;
		$this->displayOptions = array();
	}


	/**
	 * Renders the datakeys html codes
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDataKeyHTML($options = array() , $selected = '')
	{
		// Determine if the field code and field type is provided
		$fieldCode	= isset( $options[ 'fieldCode' ] ) ? $options[ 'fieldCode' ] : null;
		$fieldType	= isset( $options[ 'fieldType' ] ) ? $options[ 'fieldType' ] : null;

		// Get the list of operators
		$keys	= $this->getDataKeys($fieldCode, $fieldType );

		$theme		= FD::themes();
		$theme->set( 'keys'	, $keys );
		$theme->set( 'selected' , $selected );

		$output 	= $theme->output( 'site/advancedsearch/group/default.datakey' );

		return $output;
	}



	/**
	 * Renders the operator html codes
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getOperatorHTML( $options = array() , $selected = '' )
	{
		// Determine if the field code and field type is provided
		$fieldCode	= isset( $options[ 'fieldCode' ] ) ? $options[ 'fieldCode' ] : null;
		$fieldType	= isset( $options[ 'fieldType' ] ) ? $options[ 'fieldType' ] : null;
		$fieldKey	= isset( $options[ 'fieldKey' ] ) ? $options[ 'fieldKey' ] : null;

		// var_dump($fieldKey);exit;

		// Get the list of operators
		$operators	= $this->getOperators( $fieldCode, $fieldType, $fieldKey );

		$theme		= FD::themes();
		$theme->set( 'operators'	, $operators );
		$theme->set( 'selected'		, $selected );

		$output 	= $theme->output( 'site/advancedsearch/group/default.operator' );

		return $output;
	}

	/**
	 * Renders the condition html codes
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getConditionHTML( $options = array() , $selected = '' )
	{
		// Determine if the field code and field type is provided
		$fieldCode	= isset( $options[ 'fieldCode' ] ) ? $options[ 'fieldCode' ] : null;
		$fieldType	= isset( $options[ 'fieldType' ] ) ? $options[ 'fieldType' ] : null;
		$fieldKey	= isset( $options[ 'fieldKey' ] ) ? $options[ 'fieldKey' ] : null;

		$operator 	= isset( $options[ 'fieldOperator' ] ) ? $options[ 'fieldOperator' ] : null;


		$fType = $fieldType;
		if ($fieldType == 'birthday') {
			if ($fieldKey) {
				$fType = $fieldType . '.' . $fieldKey;
			}
		}

		if ($fieldType == 'address') {
			if ($fieldKey && $fieldKey == 'distance') {
				$fType = $fieldType . '.' . $fieldKey;
			}
		}

		$condition 	= $this->getCondition( $operator, $fType );
		$show 		= ( $operator == 'blank' || $operator == 'notblank' ) ? false : true;

		$theme		= FD::themes();

		$fileName = 'default.condition';

		if (! in_array($condition->input, array('date','dates', 'joomla_lastlogin', 'joomla_joindate','age', 'ages', 'text', 'distance'))) {
			// lets get the options.
			$list = $this->getOptionList($fieldCode, $fieldType);

			// if the options is null, it means either the field do not have any options, or we couldn't find any options.
			// if that is the case, we will default it back to text.
			if (is_null($list)) {
				$condition->input = 'text';
			} else {
				$fileName .= '.list';
				$theme->set( 'list'			, $list );
			}
		}

		$theme->set( 'condition'	, $condition );
		$theme->set( 'selected'		, $selected );
		$theme->set( 'show'			, $show );

		$output 	= $theme->output( 'site/advancedsearch/group/' . $fileName );

		return $output;
	}

	/**
	 * get the options list for dropdown list fields.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array		An array of options
	 * @param	Array		An array of values
	 * @return	string		The html output for criterias
	 */
	public function getOptionList( $fieldCode, $fieldType )
	{
		$options = null;

		if ($fieldType=='boolean') {
			$options = array();

			// YES
			$obj = new stdClass();
			$obj->title = 'Yes';
			$obj->value = '1';
			$options[] = $obj;

			// NO
			$obj = new stdClass();
			$obj->title = 'No';
			$obj->value = '0';
			$options[] = $obj;

		} else if ($fieldType == 'country') {
			// load the country
			$file 		= JPATH_ADMINISTRATOR . '/components/com_easysocial/defaults/countries.json';
			$contents 	= JFile::read( $file );

			$json 		= FD::json();
			$countries 	= $json->decode( $contents );
			if($countries)
			{
				foreach($countries as $code => $title) {
					$obj = new stdClass();
					$obj->title = $title;
					// $obj->value = $code;
					$obj->value = $title;
					$options[] = $obj;
				}
			}

		} else if ($fieldType == 'gender') {

			$options = array();

			// YES
			$obj = new stdClass();
			$obj->title = JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_MALE');
			$obj->value = '1';
			$options[] = $obj;

			// NO
			$obj = new stdClass();
			$obj->title = JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_FEMALE');
			$obj->value = '2';
			$options[] = $obj;

			$obj = new stdClass();
			$obj->title = JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_GENDER_OTHERS');
			$obj->value = '3';
			$options[] = $obj;

		} else if ($fieldType == 'relationship') {

			// load up relationshop options.
			$file 		= JPATH_ROOT . '/media/com_easysocial/apps/fields/user/relationship/config/config.json';
			$contents 	= JFile::read( $file );

			$json 		= FD::json();
			$data 	= $json->decode( $contents );
			if ($data && isset($data->relationshiptype) && isset($data->relationshiptype->option)) {
				foreach ($data->relationshiptype->option as $item) {
					$obj = new stdClass();
					$obj->title = JText::_($item->label);
					$obj->value = $item->value;
					$options[] = $obj;
				}
			}

		} else {
			$model = FD::model('Search');
			$options = $model->getFieldOptionList($fieldCode, $fieldType);

			if (!$options) {
				$options = null;
			}
		}


		return $options;
	}

	/**
	 * Loads the criteria html codes
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array		An array of options
	 * @param	Array		An array of values
	 * @return	string		The html output for criterias
	 */
	public function getCriteriaHTML( $options = array(), $values = array() )
	{
		// Default values
		$criterias		= array();

		// Get the list of fields
		$fields 	= $this->getFields();

		// Set the default values for condition and operator
		$operatorHTML 	= '';
		$conditionHTML	= '';

		$isTemplate = isset( $options['isTemplate'] ) ? $options['isTemplate'] : false;

		// dump( $values );
		// Check if there are any values that need to be pre-populated
		if( isset( $values[ 'criterias' ] ) && !empty( $values[ 'criterias' ] ) )
		{
			$total 	= count( $values[ 'criterias' ] );

			for( $i = 0; $i < $total; $i++ )
			{
				$field 			= $values[ 'criterias' ][ $i ];

				if( empty( $field ) )
				{
					continue;
				}

				// Since the values are stored in CODE|TYPE, we need to get the correct values
				$data 			= explode( '|' , $field );
				$fieldCode		= $data[ 0 ];
				$fieldType 		= $data[ 1 ];

				// Get the operator base on the current index
				$datakey 		= isset($values[ 'datakeys' ][ $i ]) ? $values[ 'datakeys' ][ $i ] : '';

				// Get the operator base on the current index
				$operator 		= $values[ 'operators' ][ $i ];

				// Get the entered value base on the current index
				$value 			= $values[ 'conditions' ][ $i ];

				$criteria				= new stdClass();
				$criteria->fields		= $fields;
				$criteria->datakeys		= $this->getDataKeyHTML( array( 'fieldCode' => $fieldCode , 'fieldType' => $fieldType ) , $datakey );

				$fieldOptions = array( 'fieldCode' => $fieldCode , 'fieldType' => $fieldType );
				if ($datakey) {
					$fieldOptions['fieldKey'] = $datakey;
				}
				$criteria->operator		= $this->getOperatorHTML( $fieldOptions  , $operator );


				$fieldOptions['fieldOperator'] = $operator;
				$criteria->condition 	= $this->getConditionHTML( $fieldOptions , $value );

				$criteria->selected 	= $field;

				$criterias[]	= $criteria;
			}
		}
		else
		{
			$criteria 				= new stdClass();
			$criteria->fields 		= $fields;
			$criteria->datakeys		= $this->getDataKeyHTML();
			$criteria->operator		= $this->getOperatorHTML();
			$criteria->condition	= $this->getConditionHTML();
			$criteria->selected 	= '';

			$criterias[]	= $criteria;
		}

		$theme 		= FD::themes();

		$theme->set( 'criterias'	, $criterias );
		$theme->set( 'isTemplate'	, $isTemplate);

		return $theme->output( 'site/advancedsearch/group/default.criteria' );
	}

	public function getFields()
	{
		// load backend custom fields language strings.
		JFactory::getLanguage()->load( 'com_easysocial' , JPATH_ROOT . '/administrator' );


		static $fields = null;

		if(! $fields )
		{
			$db 	= FD::db();
			$sql 	= $db->sql();

			$query = 'select a.`unique_key`, a.`title`, b.`element`';
			$query .= ' from `#__social_fields` as a';
			$query .= ' inner join `#__social_fields_steps` as fs on a.`step_id` = fs.`id` and fs.`type` = ' . $db->Quote('clusters');
			$query .= ' inner join `#__social_clusters_categories` as p on fs.`uid` = p.`id` and p.`type` = ' . $db->Quote('group');
			$query .= ' inner join `#__social_apps` as b on a.`app_id` = b.`id` and b.`group` = ' . $db->Quote( 'group' );
			$query .= ' where a.`searchable` = ' . $db->Quote( '1' );
			$query .= ' and a.`state` = ' . $db->Quote( '1' );
			$query .= ' and a.`unique_key` != ' . $db->Quote( '' );
			$query .= ' and p.`state` = ' . $db->Quote('1');
			$query .= ' order by fs.`sequence`, a.`ordering`';

			$sql->raw( $query );
			$db->setQuery( $sql );
			$results = $db->loadObjectList();


			// manual grouping / distinct
			if( $results )
			{
				foreach( $results as $result )
				{
					$fields[ $result->unique_key ] = $result;
				}
			}
		}

		return $fields;
	}

	/**
	 * Retrieves the default datakeys
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDataKeys( $fieldCode = null , $fieldType = null)
	{
		$return = array();

		if ($fieldType == 'address') {

			$return = array('address' => JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_KEY_LABEL_ADDRESS'), //full address
							'address1' => JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_KEY_LABEL_ADDRESS1'),
							'address2' => JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_KEY_LABEL_ADDRESS2'),
							'city' => JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_KEY_LABEL_CITY'),
							'state' => JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_KEY_LABEL_STATE'),
							'zip' => JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_KEY_LABEL_ZIP'),
							'country' => JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_KEY_LABEL_COUNTRY'),
							'distance' => JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_KEY_LABEL_DISTANCE')); // distance used to search latitude and longitude

		} else if ($fieldType == 'joomla_fullname') {

			$return = array('name' => JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_KEY_LABEL_NAME'), //full names
							'first' => JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_KEY_LABEL_FIRST'),
							'middle' => JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_KEY_LABEL_MIDDLE'),
							'last' => JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_KEY_LABEL_LAST'));

		} else if ($fieldType == 'birthday') {

			$return = array('date' => JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_KEY_LABEL_DATE'), //date search
							'age' => JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_KEY_LABEL_AGE')); // age search
		}

		return $return;
	}


	/**
	 * Retrieves the default operators
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getOperators( $fieldCode = null , $fieldType = null, $fieldKey = null )
	{
		$config = FD::config();
		$searchUnit = $config->get('general.location.proximity.unit','mile');

		$common = array(
					'equal' 		=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_IS_EQUAL_TO'),
					'notequal' 		=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_IS_NOT_EQUAL_TO'),
					'contain' 		=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_CONTAINS'),
					'notcontain' 	=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_DOES_NOT_CONTAIN'),
					'startwith' 	=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_STARTS_WITH'),
					'endwith' 		=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_ENDS_WITH')
					);

		// for address - distance
		$distance = array(
					'lessequal' 		=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_WITHIN_' . $searchUnit),
					'greater' 		=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_FURTHER_' . $searchUnit),
					);

		// for radio buttons, country, gender
		$option = array(
					'equal' 		=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_IS_EQUAL_TO'),
					'notequal' 		=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_IS_NOT_EQUAL_TO')
					);

		// for checkbox, multilist, multidropdown
		$multioption = array(
					'contain' 		=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_IS_EQUAL_TO'),
					'notequal' 		=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_IS_NOT_EQUAL_TO')
					);

		// for datetime and birthday
		$date = array(
					'equal' 		=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_IS_EQUAL_TO'),
					'notequal' 		=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_IS_NOT_EQUAL_TO'),
					'greater' 		=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_GREATER_THAN'),
					'greaterequal' 	=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_GREATER_THAN_OR_EQUAL_TO'),
					'less' 			=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_LESSER_THAN'),
					'lessequal' 	=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_LESSER_THAN_OR_EQUAL_TO'),
					'between' 		=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_IS_BETWEEN')
					);

		// for birthday - age
		$age = array(
					'equal' 		=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_IS_EQUAL_TO'),
					'less' 		=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_GREATER_THAN'), // when we searching for age greater than x, in date, it mean lesser than
					'lessequal' 	=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_GREATER_THAN_OR_EQUAL_TO'), // when we searching for age greater than x, in date, it mean lesser than
					'greater' 			=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_LESSER_THAN'),
					'greaterequal' 	=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_LESSER_THAN_OR_EQUAL_TO'),
					'between' 		=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_IS_BETWEEN')
					);

		$operators = array();

		switch( $fieldType )
		{
			case 'checkbox':
			case 'multilist':
			case 'multidropdown':
				$operators = $multioption;
				break;
			case 'relationship':
			case 'country':
			case 'gender':
			case 'dropdown':
			case 'boolean':
			// case 'joomla_timezone':
			// case 'joomla_user_editor':
			// case 'joomla_language':
				$operators = $option;
				break;

			case 'datetime':
			case 'joomla_lastlogin':
			case 'joomla_joindate':
				$operators = $date;
				break;

			case 'birthday':
				if ($fieldKey && $fieldKey == 'age') {
					$operators = $age;
				} else {
					$operators = $date;
				}
				break;

			case 'address':
				if ($fieldKey && $fieldKey == 'distance') {
					$operators = $distance;
				} else {
					$operators = $common;
				}
				break;

			case 'joomla_username':
			case 'joomla_email':
			case 'email':
				$operators = array('equal' 		=> JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_OPERATOR_IS_EQUAL_TO'));
				break;

			default:
				$operators = $common;
				break;
		}

		return $operators;
	}

	/**
	 * Retrieves a list of default search conditions
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCondition( $operator = null , $fieldType = null )
	{
		$condition = new stdClass();

		$condition->input 	= '';
		$condition->value 	= '';
		$condition->options = array();

		switch( $fieldType )
		{
			case 'datetime':
			case 'birthday':
			case 'joomla_lastlogin':
			case 'joomla_joindate':
			case 'birthday.date':
				$condition->input = 'date';

				if( $operator == 'between' )
				{
					$condition->input = 'dates';
				}
				break;
			case 'birthday.age':
				$condition->input = 'age';

				if( $operator == 'between' )
				{
					$condition->input = 'ages';
				}
				break;

			case 'address.distance':
				$condition->input = 'distance';

				break;

			case 'relationship':
			case 'gender':
				$condition->input = 'gender';
				break;

			case 'checkbox':
			case 'dropdown':
			case 'boolean':
			case 'country':
			case 'multilist':
			case 'multidropdown':
			// case 'joomla_timezone':
			// case 'joomla_user_editor':
			// case 'joomla_language':
				$condition->input = $fieldType;
				break;

			default:
				$condition->input = 'text';
				break;
		}

		return $condition;
	}

	public function search( $options = array() )
	{

		$limit = isset($options['limit']) ? $options[ 'limit' ] : FD::themes()->getConfig()->get( 'search_limit' );
		$nextlimit = isset($options['nextlimit']) ? $options[ 'nextlimit' ] : 0;

		// setup the display options.
		// $this->setDisplayOptions($options);

		$model = FD::model('SearchGroup');

		$results = $model->getAdvSearchItems($options, $nextlimit, $limit);
		$this->_total = $model->getCount();
		$this->_nextlimit = $model->getNextLimit();

		return $results;
	}

	public function setDisplayOptions($options)
	{

		// setup the display options.

		if (isset($options['criterias'])) {

			$criterias 	= is_string($options['criterias']) ? array($options['criterias']) : $options['criterias'];
			$datakeys 	= is_string($options['datakeys']) ? array($options['datakeys']) : $options['datakeys'];
			$conditions 	= is_string($options['conditions']) ? array($options['conditions']) : $options['conditions'];

			// var_dump($options);

			$totalC = count($criterias);

			for( $i = 0; $i < $totalC; $i++ )
			{
				$field 			= $criterias[ $i ];
				$datakey 		= isset($datakeys[ $i ]) ? $datakeys[ $i ] : '';
				$condition 		= $conditions[ $i ];

				// Since the values are stored in CODE|TYPE, we need to get the correct values
				$data = explode('|', $field);
				$fieldCode = isset($data[0]) ? $data[0] : '';
				$fieldType = isset($data[1]) ? $data[1] : '';

				// show gender
				if ($fieldType == 'gender') {
					$this->displayOptions['showGender'] = true;
					$this->displayOptions['GenderCode'] = $fieldCode;
				}

				// show last login date
				if ($fieldType == 'joomla_lastlogin') {
					$this->displayOptions['showLastLogin'] = true;
					$this->displayOptions['lastLoginCode'] = $fieldCode;
				}

				// show last login date
				if ($fieldType == 'joomla_joindate') {
					$this->displayOptions['showJoinDate'] = true;
					$this->displayOptions['joinDateCode'] = $fieldCode;
				}

				// show distance
				if ($fieldType == 'address' && $datakey == 'distance') {

					$inputdata = explode('|', $condition);

					$this->displayOptions['showDistance'] = true;
					$this->displayOptions['AddressCode'] = $fieldCode;

					$lat = isset($inputdata[1]) ? $inputdata[1] : 0;
					$lon = isset($inputdata[2]) ? $inputdata[2] : 0;

					if (!$lat && !$lon) {
						$my = FD::user();
						$address = $my->getFieldValue('ADDRESS');
						$lat = $address->value->latitude;
						$lon = $address->value->longitude;
					}

					$this->displayOptions['AddressLat'] = $lat;
					$this->displayOptions['AddressLon'] = $lon;
				}

			}
		}
	}

	public function getDisplayOptions()
	{
		return $this->displayOptions;
	}


	public function getTotal()
	{
		return $this->_total;
	}

	public function getNextLimit()
	{
		return $this->_nextlimit;
	}

}
