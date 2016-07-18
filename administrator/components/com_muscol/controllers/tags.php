<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TagsControllerTags extends TagsController
{

	function __construct()
	{
		parent::__construct();

	}

	function typeahead(){
		$mainframe = JFactory::getApplication();
		//$results = array('Beatles', 'Stones');
		//echo json_encode($results);
		//echo '{"tags":[{"tag":"Pisa"},{"tag":"Rome"}]}';
		$mainframe->close();
	}

}