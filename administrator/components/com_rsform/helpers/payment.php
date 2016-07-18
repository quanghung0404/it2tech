<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<style type="text/css">
#paymentdiv { overflow: auto; }
</style>
<table class="admintable">
<tr>
	<td valign="top" align="left" width="1%">
		<table class="table table-bordered">
			<tr>
				<td colspan="2"><div class="alert alert-info" style="width: 620px;"><?php echo JText::_('RSFP_PAYMENT_EMAIL_SETTINGS_DESC'); ?></div></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="hasTip key" title="<?php echo JText::_('RSFP_PAYMENT_USER_EMAIL_DESC'); ?>"><?php echo JText::_('RSFP_PAYMENT_USER_EMAIL'); ?></td>
				<td><?php echo $lists['UserEmail']; ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="hasTip key" title="<?php echo JText::_('RSFP_PAYMENT_ADMIN_EMAIL_DESC'); ?>"><?php echo JText::_('RSFP_PAYMENT_ADMIN_EMAIL'); ?></td>
				<td><?php echo $lists['AdminEmail']; ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="hasTip key" title="<?php echo JText::_('RSFP_PAYMENT_ADDITIONAL_EMAILS_DESC'); ?>"><?php echo JText::_('RSFP_PAYMENT_ADDITIONAL_EMAILS'); ?></td>
				<td><?php echo $lists['AdditionalEmails']; ?></td>
			</tr>
		</table>
	</td>
	<td valign="top">
		&nbsp;
	</td>
</tr>
</table>