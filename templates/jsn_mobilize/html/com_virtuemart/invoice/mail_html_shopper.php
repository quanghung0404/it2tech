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
    <td width="30%">
		<?php echo JText::_('COM_VIRTUEMART_MAIL_SHOPPER_YOUR_ORDER'); ?><br />
		<strong><?php echo $this->orderDetails['details']['BT']->order_number ?></strong>

	</td>
    <td width="30%">
		<?php echo JText::_('COM_VIRTUEMART_MAIL_SHOPPER_YOUR_PASSWORD'); ?><br />
		<strong><?php echo $this->orderDetails['details']['BT']->order_pass ?></strong>
	</td>
    <td width="40%">
    	<p>
 			<a class="default" title="<?php echo $this->vendor->vendor_store_name ?>" href="<?php echo JURI::root().'index.php?option=com_virtuemart&view=orders&layout=details&order_number='.$this->orderDetails['details']['BT']->order_number.'&order_pass='.$this->orderDetails['details']['BT']->order_pass; ?>">
			<?php echo JText::_('COM_VIRTUEMART_MAIL_SHOPPER_YOUR_ORDER_LINK'); ?></a>
		</p>
	</td>
  </tr>
  <tr>
    <td colspan="3"><p>
				<?php echo JText::sprintf('COM_VIRTUEMART_MAIL_SHOPPER_TOTAL_ORDER',$this->currency->priceDisplay($this->orderDetails['details']['BT']->order_total) ); ?></p></td>
  </tr>
  <td colspan="3"><p>
				<?php echo JText::sprintf('COM_VIRTUEMART_MAIL_ORDER_STATUS',JText::_($this->orderDetails['details']['BT']->order_status_name)) ; ?></p></td>
  </tr>
  <?php $nb=count($this->orderDetails['history']);
  if($this->orderDetails['history'][$nb-1]->customer_notified && !(empty($this->orderDetails['history'][$nb-1]->comments))) { ?>
  <tr>
    <td colspan="3">
		<?php echo  nl2br($this->orderDetails['history'][$nb-1]->comments); ?>
	</td>
  </tr>
  <?php } ?>
  <?php if(!empty($this->orderDetails['details']['BT']->customer_note)){ ?>
  <tr>
    <td colspan="3">
		<?php echo JText::sprintf('COM_VIRTUEMART_MAIL_SHOPPER_QUESTION',nl2br($this->orderDetails['details']['BT']->customer_note)) ?>

	</td>
  </tr>
  <?php } ?>
</table>
