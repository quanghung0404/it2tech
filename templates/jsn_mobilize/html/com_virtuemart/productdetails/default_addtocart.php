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
defined ('_JEXEC') or die('Restricted access');
?>
<div class="addtocart-area">

	<form method="post" class="product js-recalculate" action="<?php echo JRoute::_ ('index.php'); ?>">
		<?php // Product custom_fields
		if (!empty($this->product->customfieldsCart)) {
			?>
			<div class="product-fields clearafter">
				<?php foreach ($this->product->customfieldsCart as $field) { ?>
				<div class="product-field product-field-type-<?php echo $field->field_type ?>">
					<div class="product-fields-title-wrapper"><span class="product-fields-title"><strong><?php echo JText::_ ($field->custom_title) ?></strong></span>
					<?php if ($field->custom_tip) {
					echo JHTML::tooltip ($field->custom_tip, JText::_ ($field->custom_title), 'tooltip.png');
				} ?></div>
					<div class="product-field-desc"><?php echo $field->custom_field_desc ?></div>
					<div class="product-field-display"><?php echo $field->display ?></div>
				</div>
				<?php
			}
				?>
			</div>
			<?php
		}
		/* Product custom Childs
			 * to display a simple link use $field->virtuemart_product_id as link to child product_id
			 * custom_value is relation value to child
			 */

		if (!empty($this->product->customsChilds)) {
			?>
			<div class="product-fields">
				<?php foreach ($this->product->customsChilds as $field) { ?>
				<div class="product-field product-field-type-<?php echo $field->field->field_type ?>">
					<div class="product-fields-title"><strong><?php echo JText::_ ($field->field->custom_title) ?></strong></div>
					<div class="product-field-desc"><?php echo JText::_ ($field->field->custom_value) ?></div>
					<div class="product-field-display"><?php echo $field->display ?></div>

				</div>
				<?php } ?>
			</div>
			<?php } ?>

		<div class="addtocart-bar">

			<?php // Display the quantity box

			$stockhandle = VmConfig::get ('stockhandle', 'none');
			if (($stockhandle == 'disableit' or $stockhandle == 'disableadd') and ($this->product->product_in_stock - $this->product->product_ordered) < 1) {
				?>
				<a href="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id=' . $this->product->virtuemart_product_id); ?>" class="notify"><?php echo JText::_ ('COM_VIRTUEMART_CART_NOTIFY') ?></a>

				<?php } else { ?>
				<!-- <label for="quantity<?php echo $this->product->virtuemart_product_id; ?>" class="quantity_box"><?php echo JText::_ ('COM_VIRTUEMART_CART_QUANTITY'); ?>: </label> -->
				<span class="quantity-box">
		<input type="text" class="quantity-input js-recalculate" name="quantity[]" value="<?php if (isset($this->product->min_order_level) && (int)$this->product->min_order_level > 0) {
			echo $this->product->min_order_level;
		} else {
			echo '1';
		} ?>"/>
	    </span>
				<span class="quantity-controls js-recalculate">
		<input type="button" class="quantity-controls quantity-plus"/>
		<input type="button" class="quantity-controls quantity-minus"/>
	    </span>
				<?php // Display the quantity box END ?>

				<?php
				// Display the add to cart button
				?>
				<span class="addtocart-button">
		<?php echo shopFunctionsF::getAddToCartButton ($this->product->orderable); ?>
		</span>
				<?php } ?>

			<div class="clear"></div>
		</div>

		<?php // Display the add to cart button END  ?>
		<input type="hidden" class="pname" value="<?php echo $this->product->product_name ?>"/>
		<input type="hidden" name="option" value="com_virtuemart"/>
		<input type="hidden" name="view" value="cart"/>
		<noscript><input type="hidden" name="task" value="add"/></noscript>
		<input type="hidden" name="virtuemart_product_id[]" value="<?php echo $this->product->virtuemart_product_id ?>"/>
	</form>

	<div class="clear"></div>
</div>
