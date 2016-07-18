<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// requerim la vista de element pq sino no la trobara, ja que el model es albums i no element
require_once ( JPATH_COMPONENT.DS.'views'.DS.'element'.DS.'view.html.php' ) ;

class ElementControllerElement extends ElementController
{
	 
	 function element()
	{
		global $mainframe;
		
		$model	=$this->getModel( 'element' );
		$view	=$this->getView( 'element');
		
		$view->setModel( $model, true );
		//echo "holoa";$mainframe->close();
		$view->display();
	}
	 
	function __construct()
	{
		parent::__construct();

	}
  
}