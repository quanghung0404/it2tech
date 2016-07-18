<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class RatingsControllerRating extends RatingsController
{

	function __construct()
	{
		parent::__construct();

	}

	function remove()
	{
		$model = $this->getModel('rating');
		if(!$model->delete()) {
			$msg = JText::_( 'ERROR_DELETE_RATINGS' );
		} else {
			$msg = JText::_( 'RATINGS_DELETED' );
		}

		$this->setRedirect( 'index.php?option=com_muscol&controller=ratings', $msg );
	}

}