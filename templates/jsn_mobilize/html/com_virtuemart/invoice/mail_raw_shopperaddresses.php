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
 echo "\n";
 echo JText::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'). "\n";
echo sprintf("%'-64.64s",'');
 echo "\n";
  foreach ($this->userfields['fields'] as $field) {
		if(!empty($field['value'])){
			echo $field['title'].': '.$this->escape($field['value'])."\n";
		}
	}
 echo "\n";
echo JText::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'). "\n";
echo sprintf("%'-64.64s",'');
 echo "\n";


	 foreach ($this->shipmentfields['fields'] as $field) {
		if(!empty($field['value'])){
			echo $field['title'].': '.$this->escape($field['value'])."\n";
		}
	}

 echo "\n";