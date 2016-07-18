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
defined ( '_JEXEC' ) or die ( 'Restricted access' );
?>

<div class="product-related-products">
	<h4><?php echo JText::_('COM_VIRTUEMART_RELATED_PRODUCTS'); ?></h4>
	<?php
    foreach ($this->product->customfieldsRelatedProducts as $field) {
	?>
	<div class="product-field product-field-type-<?php echo $field->field_type ?>"> <span class="product-field-display"><?php echo $field->display ?></span> </div>
	<?php } ?>
</div>
<div class="clear"></div>