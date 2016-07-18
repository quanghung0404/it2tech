<?php
/**
* @package RSForm!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
function rsfp_showCcVars(val) {	
	if (val == 0)
		document.getElementById('merge_vars_cc').style.display = 'none';
	else
		document.getElementById('merge_vars_cc').style.display = '';
	return;
}

function rsfp_changeCcAction(what) {	
	if (what.value == 2)
		document.getElementById('ccontactcc_action_field').disabled = false;
	else
		document.getElementById('ccontactcc_action_field').disabled = true;
}
</script>

<table class="admintable">
<tr>
	<td valign="top" align="left" width="30%">
		<table class="table table-bordered">
			<tr>
				<td colspan="2" align="center" class="center"><?php echo JHTML::image('administrator/components/com_rsform/assets/images/constantcontact.gif', 'ConstantContact'); ?></td>
			</tr>
			<tr>
				<td colspan="2"><div class="alert alert-info"><?php echo JText::_('RSFP_CONSTANTCONTACT_DESC'); ?></div></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_CC_USE_INTEGRATION'); ?></td>
				<td><?php echo $lists['cc_published']; ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_CC_ACTION_DESC'); ?>"><?php echo JText::_('RSFP_CC_ACTION'); ?></span>
				</td>
				<td nowrap="nowrap"><?php echo $lists['cc_action']; ?> <?php echo $lists['cc_action_field']; ?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_CC_ACTION_WARNING'); ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_CC_LIST_ID'); ?></td>
				<td nowrap="nowrap"><?php echo $lists['cc_list_id']; ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_CC_UPDATE_CONTACT'); ?></td>
				<td nowrap="nowrap"><?php echo $lists['cc_update']; ?></td>
			</tr>
			<tr>
				<td colspan="2" class="key" align="center"><p align="center"><?php echo JText::_('RSFP_CC_UNSUBSCRIBE_OPTIONS'); ?></p></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_CC_DELETE_MEMBER_DESC'); ?>"><?php echo JText::_('RSFP_CC_DELETE_MEMBER'); ?></span>
				</td>
				<td><?php echo $lists['cc_delete_member']; ?></td>
			</tr>
			<tr>
				<td colspan="2" class="key" align="center"><p align="center"><?php echo JText::_('RSFP_CC_MERGE_VARS'); ?></p></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_CC_MERGE_VARS_DESC'); ?></td>
			</tr>
			<tbody id="merge_vars_cc">
			<?php if (is_array($merge_vars)) { ?>
				<?php foreach ($merge_vars as $merge_var => $title) { ?>
				<tr>
					<td nowrap="nowrap" align="right" class="key"><?php echo $title; ?></td>
					<td><?php echo $lists['cc_fields'][$merge_var]; ?></td>
				</tr>
				<?php } ?>
			<?php } ?>
			</tbody>
		</table>
	</td>
	<td valign="top">
		&nbsp;
	</td>
</tr>
</table>
<script type="text/javascript">
var cc_list = document.getElementById('ccontactcc_list_id');
var cc_list_selected = cc_list.options[cc_list.selectedIndex].value;
rsfp_showCcVars(cc_list_selected);
</script>