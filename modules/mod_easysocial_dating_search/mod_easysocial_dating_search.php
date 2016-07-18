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

// Include main engine
$file 	= JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';

jimport( 'joomla.filesystem.file' );

if( !JFile::exists( $file ) )
{
	return;
}

// Include the engine file.
require_once( $file );

// Check if Foundry exists
if( !FD::exists() )
{
    FD::language()->loadSite();
	echo JText::_( 'COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING' );
	return;
}

FD::language()->loadAdmin();

// Load up helper file
require_once( dirname( __FILE__ ) . '/helper.php' );

$config 	= FD::config();

$my 		= FD::user();

// Load up the module engine
$modules 	= FD::modules( 'mod_easysocial_dating_search' );

// We need these packages
$modules->addDependency( 'css' , 'javascript' );
$modules->loadScript('script.js');

// Get the layout to use.
$layout 	= $params->get( 'layout' , 'default' );
$suffix 	= $params->get( 'suffix' , '' );


// module setting
$withCover 	= $params->get( 'withCover' , 0 );
$limit 		= $params->get( 'total' , 6 );

//get the fields
$modFields = EasySocialModDatingSearchHelper::getFields($params);
if (! $modFields) {
    return;
}


$genderList = array();

$obj = new stdClass();
$obj->title = JText::_('--');
$obj->value = '';
$genderList[] = $obj;


// YES
$obj = new stdClass();
$obj->title = JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_MALE');
$obj->value = '1';
$genderList[] = $obj;

// NO
$obj = new stdClass();
$obj->title = JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_FEMALE');
$obj->value = '2';
$genderList[] = $obj;

$obj = new stdClass();
$obj->title = JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_GENDER_OTHERS');
$obj->value = '3';
$genderList[] = $obj;

$searchUnit = $config->get('general.location.proximity.unit','mile');


// Get values from posted data
$values                 = array();
$values[ 'criterias' ]  = JRequest::getVar( 'criterias' );
$values[ 'datakeys' ]   = JRequest::getVar( 'datakeys' );
$values[ 'operators' ]  = JRequest::getVar( 'operators' );
$values[ 'conditions' ] = JRequest::getVar( 'conditions' );

$modUserData = array();


    // lets do some clean up here.
for($i = 0; $i < count($values[ 'criterias' ]); $i++ ) {
    $criteria = $values[ 'criterias' ][$i];
    $condition = $values[ 'conditions' ][$i];
    $datakey = $values[ 'datakeys' ][$i];

    $field  = explode( '|', $criteria );

    $fieldCode  = $field[0];
    $fieldType  = $field[1];

    if ($fieldType == 'address' && $datakey == 'distance') {
        $addressData = explode('|', $condition);
        $modUserData[$fieldType]['distance'] = isset($addressData[0]) ? $addressData[0] : '';
        $modUserData[$fieldType]['latitude'] = isset($addressData[1]) ? $addressData[1] : '';
        $modUserData[$fieldType]['longitude'] = isset($addressData[2]) ? $addressData[2] : '';
        $modUserData[$fieldType]['address'] = isset($addressData[3]) ? $addressData[3] : '';
    }

    $modUserData[$fieldType]['condition'] = $condition;
}

// var_dump($values);



require( JModuleHelper::getLayoutPath( 'mod_easysocial_dating_search' , $layout ) );
