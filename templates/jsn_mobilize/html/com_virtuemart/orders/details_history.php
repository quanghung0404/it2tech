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

<table width="100%" cellspacing="2" cellpadding="4" border="0">
	<tr align="left" class="sectiontableheader">
		<th align="left" ><?php echo JText::_('COM_VIRTUEMART_DATE') ?></th>
		<th align="left" ><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_STATUS') ?></th>
		<th align="left" ><?php echo JText::_('COM_VIRTUEMART_ORDER_COMMENT') ?></th>
	</tr>
<?php
	foreach($this->orderdetails['history'] as $_hist) {
		if (!$_hist->customer_notified) {
			continue;
		}
?>
		<tr valign="top">
			<td align="left">
				<?php echo $_hist->created_on; ?>
			</td>
			<td align="left" >
				<?php echo $this->orderstatuses[$_hist->order_status_code]; ?>
			</td>
			<td align="left" >
				<?php echo $_hist->comments; ?>
			</td>
		</tr>
<?php
	}
?>
</table>
