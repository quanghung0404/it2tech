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
defined('_JEXEC') or die('Restricted access');

echo strip_tags(JText::sprintf('COM_VIRTUEMART_ORDER_PRINT_TOTAL', $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_total))) . "\n";
echo sprintf("%'-64.64s", '') . "\n";
echo JText::_('COM_VIRTUEMART_ORDER_ITEM') . "\n";
foreach ($this->orderDetails['items'] as $item) {
    echo "\n";
    echo $item->product_quantity . ' X ' . $item->order_item_name . ' (' . strtoupper(JText::_('COM_VIRTUEMART_SKU')) . $item->order_item_sku . ')' . "\n";
    if (!empty($item->product_attribute)) {
	if (!class_exists('VirtueMartModelCustomfields'))
	    require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'customfields.php');
	$product_attribute = VirtueMartModelCustomfields::CustomsFieldOrderDisplay($item, 'FE');
	echo "\n" . $product_attribute . "\n";
    }
    if (!empty($item->product_basePriceWithTax) && $item->product_basePriceWithTax != $item->product_final_price) {
	echo $item->product_basePriceWithTax . "\n";
    }

    echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') . $item->product_final_price;
    if (VmConfig::get('show_tax')) {
	echo ' (' . JText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_TAX') . ':' . $this->currency->priceDisplay($item->product_tax) . ')' . "\n";
    }
    echo "\n";
}
echo sprintf("%'-64.64s", '');
echo "\n";

// Coupon
if (!empty($this->orderDetails['details']['BT']->coupon_code)) {
    echo JText::_('COM_VIRTUEMART_COUPON_DISCOUNT') . ':' . $this->orderDetails['details']['BT']->coupon_code . ' ' . JText::_('COM_VIRTUEMART_PRICE') . ':' . $this->currency->priceDisplay($this->orderDetails['details']['BT']->coupon_discount);
    echo "\n";
}



foreach ($this->orderDetails['calc_rules'] as $rule) {
    if ($rule->calc_kind == 'DBTaxRulesBill') {
	echo $rule->calc_rule_name . $this->currency->priceDisplay($rule->calc_amount) . "\n";
    } elseif ($rule->calc_kind == 'taxRulesBill') {
	echo $rule->calc_rule_name . ' ' . $this->currency->priceDisplay($rule->calc_amount) . "\n";
    } elseif ($rule->calc_kind == 'DATaxRulesBill') {
	echo $rule->calc_rule_name . ' ' . $this->currency->priceDisplay($rule->calc_amount) . "\n";
    }
}


echo strtoupper(JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING')) . ' (' . strip_tags(str_replace("<br />", "\n", $this->orderDetails['shipmentName'])) . ' ) ' . "\n";
echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') . ' : ' . $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_shipment);
if (VmConfig::get('show_tax')) {
    echo ' (' . JText::_('COM_VIRTUEMART_ORDER_PRINT_TAX') . ' : ' . $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_shipment_tax) . ')';
}
echo "\n";
echo strtoupper(JText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT')) . ' (' . strip_tags(str_replace("<br />", "\n", $this->orderDetails['paymentName'])) . ' ) ' . "\n";
echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') . ':' . $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_payment);
if (VmConfig::get('show_tax')) {
    echo ' (' . JText::_('COM_VIRTUEMART_ORDER_PRINT_TAX') . ' : ' . $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_payment_tax) . ')';
}
echo "\n";

echo sprintf("%'-64.64s", '') . "\n";
// total order
echo JText::_('COM_VIRTUEMART_MAIL_SUBTOTAL_DISCOUNT_AMOUNT') . ' : ' . $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_billDiscountAmount) . "\n";

echo strtoupper(JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL')) . ' : ' . $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_total) . "\n";
if (VmConfig::get('show_tax')) {
    echo ' (' . JText::_('COM_VIRTUEMART_ORDER_PRINT_TAX') . ' : ' . $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_billTaxAmount) . ')' . "\n";
}
echo "\n";

