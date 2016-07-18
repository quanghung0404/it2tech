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

<?php
if (VmConfig::get('oncheckout_show_steps', 1)) {
    echo '<div class="checkoutStep" id="checkoutStep2">' . JText::_('COM_VIRTUEMART_USER_FORM_CART_STEP2') . '</div>';
}
?>
<form method="post" id="userForm" name="chooseShipmentRate" action="<?php echo JRoute::_('index.php'); ?>" class="form-validate">
<?php

	echo "<h1>".JText::_('COM_VIRTUEMART_CART_SELECT_SHIPMENT')."</h1>";
	if($this->cart->getInCheckOut()){
		$buttonclass = 'button vm-button-correct';
	} else {
		$buttonclass = 'default';
	}
	?>
	<div class="buttonBar-right">

	        <button class="<?php echo $buttonclass ?>" type="submit" ><?php echo JText::_('COM_VIRTUEMART_SAVE'); ?></button>  &nbsp;
	<button class="<?php echo $buttonclass ?>" type="reset" onClick="window.location.href='<?php echo JRoute::_('index.php?option=com_virtuemart&view=cart'); ?>'" ><?php echo JText::_('COM_VIRTUEMART_CANCEL'); ?></button>
	</div>
<?php
    if ($this->found_shipment_method) {


	   echo "<fieldset>\n";
	// if only one Shipment , should be checked by default
	    foreach ($this->shipments_shipment_rates as $shipment_shipment_rates) {
		if (is_array($shipment_shipment_rates)) {
		    foreach ($shipment_shipment_rates as $shipment_shipment_rate) {
			echo $shipment_shipment_rate."<br />\n";
		    }
		}
	    }
	    echo "</fieldset>\n";
    } else {
	 echo "<h1>".$this->shipment_not_found_text."</h1>";
    }

    ?>

    <input type="hidden" name="option" value="com_virtuemart" />
    <input type="hidden" name="view" value="cart" />
    <input type="hidden" name="task" value="setshipment" />
    <input type="hidden" name="controller" value="cart" />
</form>
