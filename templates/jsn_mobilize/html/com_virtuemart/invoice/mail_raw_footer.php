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
/* TODO Chnage the footer place in helper or assets ???*/
if (empty($this->vendor)) {
		$vendorModel = VmModel::getModel('vendor');
		$this->vendor = $vendorModel->getVendor();
}

// $uri    = JURI::getInstance();
// $prefix = $uri->toString(array('scheme', 'host', 'port'));

$link = JURI::root(). 'index.php?option=com_virtuemart' ;

echo "\n\n";
$link= JHTML::_('link', $link, $this->vendor->vendor_name) ;

//	echo JText::_('COM_VIRTUEMART_MAIL_VENDOR_TITLE').$this->vendor->vendor_name.'<br/>';
/* GENERAL FOOTER FOR ALL MAILS */
	echo JText::_('COM_VIRTUEMART_MAIL_FOOTER' ) . $link;
        echo "\n";
	echo $this->vendor->vendor_name ."\n".$this->vendor->vendor_phone .' '.$this->vendor->vendor_store_name ."\n".strip_tags($this->vendor->vendor_store_desc)."\n".str_replace('<br />',"\n",$this->vendor->vendor_legal_info);

