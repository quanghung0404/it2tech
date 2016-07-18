<?php
/**
* @package RSform!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access'); ?>

<table class="admintable">
<tr>
	<td valign="top" align="left" width="65%">
		<table class="table table-bordered">
			<tr>
				<td colspan="2" style="text-align: center;"><?php echo JHTML::image('administrator/components/com_rsform/assets/images/zohocrm.png', 'Zoho CRM'); ?></td>
			</tr>
			<tr>
				<td colspan="2"><div class="alert alert-info"><?php echo JText::_('PLG_SYSTEM_RSFPZOHOCRM_DESC'); ?></div></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_ZOHOCRM_USE_INTEGRATION'); ?></td>
				<td><?php echo $lists['zh_published']; ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_ZOHOCRM_SHOW_ZOHO_MESSAGES_DESC'); ?>"><?php echo JText::_('RSFP_ZOHOCRM_SHOW_ZOHO_MESSAGES'); ?></span>
				</td>
				<td><?php echo $lists['zh_debug']; ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_ZOHOCRM_TRIGGER_DESC'); ?>"><?php echo JText::_('RSFP_ZOHOCRM_TRIGGER'); ?></span>
				</td>
				<td nowrap="nowrap"><?php echo $lists['zh_trigger']; ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_ZOHOCRM_DUPLICATE_DESC'); ?>"><?php echo JText::_('RSFP_ZOHOCRM_DUPLICATE'); ?></span>
				</td>
				<td nowrap="nowrap"><?php echo $lists['zh_duplicates']; ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_ZOHOCRM_APPROVAL_DESC'); ?>"><?php echo JText::_('RSFP_ZOHOCRM_APPROVAL'); ?></span>
				</td>
				<td nowrap="nowrap"><?php echo $lists['zh_approval']; ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_ZOHOCRM_FORMAT_DESC'); ?>"><?php echo JText::_('RSFP_ZOHOCRM_FORMAT'); ?></span>
				</td>
				<td nowrap="nowrap"><?php echo $lists['zh_format']; ?></td>
			</tr>
			<tr>
				<td colspan="2" class="key" align="center"><p align="center"><?php echo JText::_('RSFP_ZOHOCRM_MERGE_VARS'); ?></p></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_ZOHOCRM_MERGE_VARS_DESC'); ?></td>
			</tr>
			<?php if (empty($zohoToken)) { ?>
			<tr>
				<td colspan="2" class="key" align="center" class="center"><strong><?php echo JText::_('RSFP_ZOHOCRM_EMPTY_TOKEN'); ?></strong></td>
			</tr>
			<?php } ?>
			<?php if (is_array($sections)) { ?>
			<?php foreach ($sections as $name => $section) { ?>
			<tbody>
			<tr>
				<td colspan="2" class="key" align="center" class="center"><strong><?php echo $name; ?></strong></td>
			</tr>
			<?php if (is_array($section)) { ?>
			<?php foreach ($section as $field) { ?>
			<tr>
				<td nowrap="nowrap" align="right" valign="top" class="key"><?php echo $field->dv; ?><?php echo $field->req === 'true' ? ' *' : ''; ?></td>
				<td><?php echo $lists['zh_fields'][$field->label]; ?>
				<?php if (!empty($field->val) && is_array($field->val)) { ?>
					<?php
					$fieldValues = array();
					foreach ($field->val as $value) {
						if (is_object($value) && isset($value->content)) {
							$value = $value->content;
						}
						
						// failsafe
						if (!is_object($value)) {
							$fieldValues[] = $value;
						}
					}
					?>
					<p><small><?php echo RSFormProHelper::htmlEscape(JText::sprintf('RSFP_ZOHOCRM_POSSIBLE_VALUES', implode(', ', $fieldValues))); ?></small></p>
				<?php } ?>
				</td>
			</tr>
			<?php } ?>
			<?php } ?>
			</tbody>
			<?php } ?>
			<?php } ?>
		</table>
	</td>
	<td valign="top">
		&nbsp;
	</td>
</tr>
</table>