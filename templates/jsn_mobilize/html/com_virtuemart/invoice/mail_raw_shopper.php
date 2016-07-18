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
echo strip_tags(JText::_('COM_VIRTUEMART_MAIL_SHOPPER_YOUR_ORDER')) . "\n" . "\n";
echo strip_tags(JText::sprintf('COM_VIRTUEMART_MAIL_SHOPPER_SUMMARY', $this->vendor->vendor_store_name)) . "\n" . "\n";

echo JText::sprintf('COM_VIRTUEMART_MAIL_SHOPPER_CONTENT', $this->shopperName, $this->vendor->vendor_store_name, $this->orderDetails['details']['BT']->order_total, $this->orderDetails['details']['BT']->order_number, $this->orderDetails['details']['BT']->order_pass, $this->orderDetails['details']['BT']->created_on) . "\n" . "\n";

echo "\n" . strip_tags(JText::sprintf('COM_VIRTUEMART_MAIL_ORDER_STATUS', $this->orderDetails['details']['BT']->order_status_name));

echo "\n\n";
$nb = count($this->orderDetails['history']);
if ($this->orderDetails['history'][$nb - 1]->customer_notified && !(empty($this->orderDetails['history'][$nb - 1]->comments))) {
    echo $this->orderDetails['history'][$nb - 1]->comments;
}
    echo "\n\n";

    echo "\n\n";
    echo JText::_('COM_VIRTUEMART_MAIL_SHOPPER_YOUR_ORDER_LINK') . ' : ' . JURI::root() . 'index.php?option=com_virtuemart&view=orders&layout=details&order_number=' . $this->orderDetails['details']['BT']->order_number . '&order_pass=' . $this->orderDetails['details']['BT']->order_pass . "\n";

    if (!empty($this->orderDetails['details']['BT']->customer_note)) {
	echo "\n" . strip_tags(JText::sprintf('COM_VIRTUEMART_MAIL_SHOPPER_QUESTION', $this->orderDetails['details']['BT']->customer_note));
    }
    echo "\n\n";

//TODO if silent registration logindata
//TODO if Paymentmethod needs Bank account data of vendor
//We may wish to integrate later a kind of signature


