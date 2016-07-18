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
defined('_JEXEC') or die('');


echo $this->vendorAddress;
echo "\n";
echo "\n";
echo JText::sprintf ('COM_VIRTUEMART_MAIL_SHOPPER_NAME', $this->user->name);
echo "\n";
echo "\n";
if(!empty($this->mailbody)) {
	echo $this->mailbody;
} else {
	echo str_replace( "<br />", "\n", JText::sprintf('COM_VIRTUEMART_CART_NOTIFY_MAIL_RAW', $this->productName,$this->link) );
}

echo "\n";

// $uri    = JURI::getInstance();
// $prefix = $uri->toString(array('scheme', 'host', 'port'));
$link = JURI::root().'index.php?option=com_virtuemart';

echo "\n\n";
$link= JHTML::_('link', $link, $this->vendor->vendor_name) ;

//	echo JText::_('COM_VIRTUEMART_MAIL_VENDOR_TITLE').$this->vendor->vendor_name.'<br/>';
/* GENERAL FOOTER FOR ALL MAILS */
echo JText::_('COM_VIRTUEMART_MAIL_FOOTER' ) . $link;
echo "\n";
echo $this->vendor->vendor_name ."\n".$this->vendor->vendor_phone .' '.$this->vendor->vendor_store_name ."\n".strip_tags($this->vendor->vendor_store_desc)."\n".str_replace('<br />',"\n",$this->vendor->vendor_legal_info);

