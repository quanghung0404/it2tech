<?php
/**
 * @version     $Id: changeposition.php 16460 2012-09-26 09:52:25Z hiepnv $
 * @package     JSN_Mobilize
 * @subpackage  item
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class JSNMobilizeControllerPosition extends JControllerForm
{
	/**
	 * 
	 * Redirect to position listing page
	 */
	public function selectPosition()
	{
		if(!function_exists('curl_version')){
			$msg 	=	 JText::_('JSN_MOBILIZE_ERROR_CURL_NOT_ENABLED');
			$this->setRedirect('index.php?option=com_mobilize&view=error&tmpl=component', $msg, 'error');
			$this->redirect();
		}
	}	
}
?>