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
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="html-email">
    <tr>
    <td>
<?php
//	echo JText::_('COM_VIRTUEMART_CART_MAIL_VENDOR_TITLE').$this->vendor->vendor_name.'<br/>';
	echo JText::sprintf('COM_VIRTUEMART_MAIL_VENDOR_CONTENT',$this->vendor->vendor_store_name,$this->shopperName,$this->currency->priceDisplay($this->orderDetails['details']['BT']->order_total),$this->orderDetails['details']['BT']->order_number);

if(!empty($this->orderDetails['details']['BT']->customer_note)){
	echo '<br /><br />'.JText::sprintf('COM_VIRTUEMART_CART_MAIL_VENDOR_SHOPPER_QUESTION',$this->orderDetails['details']['BT']->customer_note).'<br />';
}

	?>
</td></tr></table>