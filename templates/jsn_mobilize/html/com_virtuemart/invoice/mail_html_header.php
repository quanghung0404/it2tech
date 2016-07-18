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
/* TODO Change the header place in helper or assets ??? */
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="html-email">
	<tr>
		<td align="top">
			<img src="<?php  echo JURI::root () . $this->vendor->images[0]->file_url ?>" />
		</td>
		<td>
			<?php echo $this->vendorAddress; ?>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<strong><?php echo JText::sprintf ('COM_VIRTUEMART_MAIL_SHOPPER_NAME', $this->orderDetails['details']['BT']->title . ' ' . $this->orderDetails['details']['BT']->first_name . ' ' . $this->orderDetails['details']['BT']->last_name); ?></strong><br/>
		</td>
	</tr>
</table>
