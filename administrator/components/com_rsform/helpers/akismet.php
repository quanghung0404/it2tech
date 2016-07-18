<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<table class="admintable table">
<tr>
	<td valign="top" align="left" width="30%">
		<table class="table table-bordered">
			<tr>
				<td colspan="2" align="center" class="center"><?php echo JHTML::image('administrator/components/com_rsform/assets/images/akismet.gif', 'Akismet'); ?></td>
			</tr>
			<tr>
				<td colspan="2"><div class="alert alert-info"><?php echo JText::_('RSFP_AKISMET_DESC'); ?></div></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_AKI_USE_INTEGRATION'); ?></td>
				<td><?php echo $lists['published']; ?></td>
			</tr>
			<tr>
				<td colspan="2" class="key" align="center"><p align="center"><?php echo JText::_('RSFP_AKI_MERGE_VARS'); ?></p></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_AKI_MERGE_VARS_DESC'); ?></td>
			</tr>
			<?php if (is_array($merge_vars)) { ?>
				<?php foreach ($merge_vars as $merge_var => $title) { ?>
				<tr>
					<td nowrap="nowrap" align="right"><?php echo $title; ?></td>
					<td><?php echo $lists['fields'][$merge_var]; ?></td>
				</tr>
				<?php } ?>
			<?php } ?>
		</table>
	</td>
	<td valign="top">
		&nbsp;
	</td>
</tr>
</table>