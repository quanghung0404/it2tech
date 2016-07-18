<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
function enablevTiger(value) {
	document.getElementById('vt_table').style.display = value == 1 ? '' : 'none';
}
</script>

<table class="admintable table table-bordered">
	<tr>
		<td colspan="2" align="center" class="center"><?php echo JHTML::image('administrator/components/com_rsform/assets/images/vtiger.png', 'vtiger.com'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><div class="alert alert-info"><?php echo JText::_('RSFP_VTIGER_DESC'); ?></div></td>
	</tr>
	<tr>
		<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_VT_USE_INTEGRATION'); ?></td>
		<td nowrap="nowrap"><?php echo $lists['published']; ?></td>
	</tr>
</table>

<table class="admintable table table-bordered" id="vt_table" <?php echo !$row->vt_published ? 'style="display: none;"' : ''; ?>>
	<tr>
		<td colspan="3" align="left"><legend><?php echo JText::_('RSFP_VT_AUTHENTICATION'); ?></legend></td>
	</tr>
	<tr>
		<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_VT_ACCESSKEY'); ?></td>
		<td>
			<input name="vt_accesskey" id="vt_accesskey" value="<?php echo RSFormProHelper::htmlEscape($row->vt_accesskey); ?>" class="rs_inp rs_80" />
		</td>
	</tr>
	<tr>
		<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_VT_USERNAME'); ?></td>
		<td>
			<input name="vt_username" id="vt_username" value="<?php echo RSFormProHelper::htmlEscape($row->vt_username); ?>" class="rs_inp rs_80" />
		</td>
	</tr>
	<tr>
		<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_VT_HOSTNAME'); ?></td>
		<td>
			<input name="vt_hostname" id="vt_hostname" value="<?php echo RSFormProHelper::htmlEscape($row->vt_hostname); ?>" class="rs_inp rs_80" />
		</td>
	</tr>
	<tr>
		<td colspan="3" align="left"><legend><?php echo JText::_('RSFP_VT_FIELDS'); ?></legend></td>
	</tr>
	<?php if ($fields) { ?>
		<?php foreach ($fields as $f => $field) { ?>
		<?php if (!$field['editable'] || $field['type']['name'] == 'owner' || $field['type']['name'] == 'reference') continue; ?>
		<tr>
			<td width="80" align="right" nowrap="nowrap" class="key" valign="top">
				<?php echo RSFormProHelper::htmlEscape($field['label']); ?> <?php if ($field['mandatory']) { ?><b style="color: red;">(*)</b><?php } ?>
			</td>
			<td valign="top">
				<input name="vt_fields[<?php echo RSFormProHelper::htmlEscape($field['name']); ?>]" id="vt_fields<?php echo RSFormProHelper::htmlEscape($field['name']); ?>" value="<?php echo RSFormProHelper::htmlEscape(isset($row->vt_fields[$field['name']]) ? $row->vt_fields[$field['name']] : ''); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
			</td>
			<td valign="top">
				<?php echo RSFormProHelper::htmlEscape($field['type']['name']); ?>
				<?php if ($field['type']['name'] == 'picklist' || $field['type']['name'] == 'multipicklist') {
				$values = array();
				foreach ($field['type']['picklistValues'] as $value) {
					$values[] = RSFormProHelper::htmlEscape($value['value']);
				}
				?>
				<button type="button" onclick="jQuery('#vt_possible_values_<?php echo $f; ?>').toggle();"><?php echo JText::_('RSFP_VT_POSSIBLE_VALUES'); ?></button>
				<div id="vt_possible_values_<?php echo $f; ?>" style="display: none;">
					<p><?php echo implode('<br />', $values); ?></p>
				</div>
			<?php } ?>
			</td>
		</tr>
		<?php } ?>
	<?php } else { ?>
		<tr>
		<td colspan="3">
			<p><?php echo JText::_('RSFP_VT_NOT_CONFIGURED'); ?></p>
			<ol>
				<li><?php echo JText::_('RSFP_VT_NOT_CONFIGURE_PROVIDE_ACCESS_KEY'); ?></li>
				<li><?php echo JText::_('RSFP_VT_NOT_CONFIGURE_PROVIDE_USERNAME'); ?></li>
				<li><?php echo JText::_('RSFP_VT_NOT_CONFIGURE_PROVIDE_HOST_URL'); ?></li>
			</ol>
			<p><?php echo JText::_('RSFP_VT_ONCE_CONFIGURED'); ?></p>
		</td>
		</tr>
	<?php } ?>
</table>