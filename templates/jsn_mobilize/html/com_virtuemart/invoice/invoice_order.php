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

<h1><?php echo JText::_('COM_VIRTUEMART_INVOICE').' '.$this->invoiceNumber; ?> </h1>


<table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
	<td class=""><?php echo JText::_('COM_VIRTUEMART_INVOICE_DATE') ?></td>
	<td align="left"><?php echo vmJsApi::date($this->invoiceDate, 'LC4', true); ?></td>
    </tr>
    <tr>
	<td ><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_NUMBER') ?></td>
	<td align="left"><strong>
	    <?php echo $this->orderDetails['details']['BT']->order_number; ?>
		</strong>
	</td>
    </tr>

    <tr>
	<td class=""><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_DATE') ?></td>
	<td align="left"><?php echo vmJsApi::date($this->orderDetails['details']['BT']->created_on, 'LC4', true); ?></td>
    </tr>
    <tr>
	<td class=""><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_STATUS') ?></td>
	<td align="left"><?php echo $this->orderstatuses[$this->orderDetails['details']['BT']->order_status]; ?></td>
    </tr>
    <tr>
	<td class=""><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIPMENT_LBL') ?></td>
	<td align="left"><?php
	    echo $this->orderDetails['shipmentName'];
	    ?></td>
    </tr>
    <tr>
	<td class=""><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL') ?></td>
	<td align="left"><?php echo $this->orderDetails['paymentName']; ?>
	</td>
    </tr>
<?php if ($this->orderDetails['details']['BT']->customer_note) { ?>
	 <tr>
    <td><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_CUSTOMER_NOTE') ?></td>
    <td valign="top" align="left" width="50%"><?php echo $this->orderDetails['details']['BT']->customer_note; ?></td>
</tr>
<?php } ?>

     <tr>
	<td class="orders-key"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?></strong></td>
	<td class="orders-key" align="left"><strong><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_total); ?></strong></td>
    </tr>

    <tr>
	<td colspan="2"></td>
    </tr>
    <tr>
	<td valign="top"><strong>
	    <?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_BILL_TO_LBL') ?></strong> <br/>
	    <table border="0"><?php
	    foreach ($this->userfields['fields'] as $field) {
		if (!empty($field['value'])) {
		    echo '<tr><td class="key">' . $field['title'] . '</td>'
		    . '<td>' . $field['value'] . '</td></tr>';
		}
	    }
	    ?></table>
	</td>
	<td valign="top" ><strong>
	    <?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIP_TO_LBL') ?></strong><br/>
	    <table border="0"><?php
	    foreach ($this->shipmentfields['fields'] as $field) {
		if (!empty($field['value'])) {
		    echo '<tr><td class="key">' . $field['title'] . '</td>'
		    . '<td>' . $field['value'] . '</td></tr>';
		}
	    }
	    ?></table>
	</td>
    </tr>
</table>
