<?php
/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView'))require(JPATH_VM_SITE.DS.'helpers'.DS.'vmview.php');

/**
* View for the shopping cart
* @package VirtueMart
* @author Valérie Isaksen
*/
class VirtueMartViewPluginresponse extends VmView {



	public function display($tpl = null) {
		$mainframe = JFactory::getApplication();
		$pathway = $mainframe->getPathway();
		$document = JFactory::getDocument();
//       $paymentResponse = JRequest::getVar('paymentResponse', '');

      //Why do you we allow raw here?
//       $paymentResponseHtml = JRequest::getVar('paymentResponseHtml','','default','STRING',JREQUEST_ALLOWRAW);
		$layoutName = $this->getLayout();



		parent::display($tpl);
	}


}

//no closing tag