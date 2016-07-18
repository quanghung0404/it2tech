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


echo JText::sprintf('COM_VIRTUEMART_CART_NOTIFY_MAIL_RAW', $this->productName,$this->url);


if(!empty($this->orderDetails['details']['BT']->customer_note)) {
	echo "\n" . JText::sprintf('COM_VIRTUEMART_CART_MAIL_VENDOR_SHOPPER_QUESTION', $this->orderDetails['details']['BT']->customer_note);
}
echo "\n";
