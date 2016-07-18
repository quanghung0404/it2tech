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
<table class="html-email" cellspacing="0" cellpadding="0" border="0" width="100%">  <tr  >
	<th width="50%">
	    <?php echo JText::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'); ?>
	</th>
	<th width="50%" >
	    <?php echo JText::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); ?>
	</th>
    </tr>
    <tr>
	<td valign="top" width="50%">

	    <?php

	    foreach ($this->userfields['fields'] as $field) {
		if (!empty($field['value'])) {
			?><!-- span class="titles"><?php echo $field['title'] ?></span -->
	    	    <span class="values vm2<?php echo '-' . $field['name'] ?>" ><?php echo $this->escape($field['value']) ?></span>
			<?php if ($field['name'] != 'title' and $field['name'] != 'first_name' and $field['name'] != 'middle_name' and $field['name'] != 'zip') { ?>
			    <br class="clear" />
			    <?php
			}
		    }
		 
	    }
	    ?>

	</td>
	<td valign="top" width="50%">
	    <?php
	    foreach ($this->shipmentfields['fields'] as $field) {

		if (!empty($field['value'])) {
			    ?><!-- span class="titles"><?php echo $field['title'] ?></span -->
			    <span class="values vm2<?php echo '-' . $field['name'] ?>" ><?php echo $this->escape($field['value']) ?></span>
			    <?php if ($field['name'] != 'title' and $field['name'] != 'first_name' and $field['name'] != 'middle_name' and $field['name'] != 'zip') { ?>
		    	    <br class="clear" />
				<?php
			    }
			}
	    }

	    ?>
	</td>
    </tr>
</table>

