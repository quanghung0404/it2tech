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
<div class="product-fields clearafter">
	    <?php
	    $custom_title = null;
	    foreach ($this->product->customfieldsSorted[$this->position] as $field) {
	    	if ( $field->is_hidden ) //OSP http://forum.virtuemart.net/index.php?topic=99320.0
	    		continue;
			if ($field->display) {
	    ?><div class="product-field product-field-type-<?php echo $field->field_type ?>">
		    <?php if ($field->custom_title != $custom_title) { ?>
			    <div class="product-fields-title" ><?php echo JText::_($field->custom_title); ?></div>
			    <?php
			    if ($field->custom_tip)
				echo JHTML::tooltip($field->custom_tip, JText::_($field->custom_title), 'tooltip.png');
			}
			?>
	    	    <div class="product-field-display"><?php echo $field->display ?></div>
	    	    <div class="product-field-desc"><?php echo jText::_($field->custom_field_desc) ?></div>
	    	</div>
		    <?php
		    $custom_title = $field->custom_title;
			}
	    }
	    ?>
        </div>
