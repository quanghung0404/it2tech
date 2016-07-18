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
	function enableSalesforce(value)
	{
		document.getElementById('slsf_table').style.display = 'none';
		if (value == 1)
			document.getElementById('slsf_table').style.display = '';
	}

	function enableSalesforceDebug(value)
	{
		var todo = true;
		if (value == 1)
			todo = false;

		document.getElementById('slsf_debugEmail').disabled = todo;
	}

	function addSalesforceCustomField()
	{
		var table = document.getElementById('slsf_table');

		// ID
		var id = Math.floor(Math.random()*3+1);

		// API Name
		var x = table.insertRow(table.rows.length);
		x.id = 'slsf_api_name' + id;
		x.setAttribute('id', 'slsf_api_name' + id);

		var y = x.insertCell(0);
		y.setAttribute('width', 80);
		y.setAttribute('align', 'right');
		y.setAttribute('nowrap', 'nowrap');
		y.setAttribute('class', 'key');
		y.innerHTML = '<?php echo JText::_('RSFP_SLSF_CUSTOM_FIELD_API_NAME', true); ?>';

		var y = x.insertCell(1);
		var input = document.createElement('input');
		input.name = 'slsf_api_name[]';
		input.placeholder = 'ID';
		input.setAttribute('class', 'rs_inp rs_50');
		y.appendChild(input);

		var button = document.createElement('button');
		button.type = 'button';
		button.setAttribute('class', 'rs_button rs_right btn btn-danger');
		button.innerHTML = '<?php echo JText::_('RSFP_DELETE', true); ?>';
		jQuery(button).click(function() {
			deleteSalesforceCustomField(id);
		});
		y.appendChild(button);

		// Value
		var x = table.insertRow(table.rows.length);
		x.id = 'slsf_value' + id;
		x.setAttribute('id', 'slsf_value' + id);

		var y = x.insertCell(0);
		y.setAttribute('width', 80);
		y.setAttribute('align', 'right');
		y.setAttribute('nowrap', 'nowrap');
		y.setAttribute('class', 'key');
		y.innerHTML = '<?php echo JText::_('RSFP_SLSF_CUSTOM_FIELD_API_VALUE', true); ?>';

		var y = x.insertCell(1);
		var input = document.createElement('input');
		input.name = 'slsf_value[]';
		input.value = '';
		input.setAttribute('class', 'rs_inp rs_80');
		y.appendChild(input);
	}

	function deleteSalesforceCustomField(id)
	{
		document.getElementById('slsf_api_name' + id).parentNode.removeChild(document.getElementById('slsf_api_name' + id));
		document.getElementById('slsf_value' + id).parentNode.removeChild(document.getElementById('slsf_value' + id));
	}

</script>
<style type="text/css">
	#salesforcediv { overflow: auto; }
</style>
<table class="admintable">
	<tr>
		<td valign="top" align="left" width="1%">
			<table class="table table-bordered">
				<tr>
					<td colspan="2" align="center" class="center"><?php echo JHTML::image('administrator/components/com_rsform/assets/images/salesforce.png', 'salesforce.com'); ?></td>
				</tr>
				<tr>
					<td colspan="2"><div class="alert alert-info" style="width: 620px;"><?php echo JText::_('RSFP_SALESFORCE_DESC'); ?></div></td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_USE_INTEGRATION'); ?></td>
					<td nowrap="nowrap"><?php echo $lists['published']; ?></td>
				</tr>
			</table>
			<table id="slsf_table" class="table table-bordered" <?php echo !$row->slsf_published ? 'style="display: none;"' : ''; ?>>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_OID'); ?></td>
					<td>
						<input name="slsf_oid" id="slsf_oid" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_oid); ?>" class="rs_inp rs_80" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_DEBUG'); ?></td>
					<td><?php echo $lists['debug']; ?></td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_DEBUG_EMAIL'); ?></td>
					<td>
						<input name="slsf_debugEmail" id="slsf_debugEmail" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_debugEmail); ?>" class="rs_inp rs_80" <?php if (!$row->slsf_published || !$row->slsf_debug) { ?>disabled="disabled"<?php } ?> />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_CAMPAIGN_ID'); ?></td>
					<td>
						<input name="slsf_campaign_id" id="slsf_campaign_id" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_campaign_id); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_LEAD_SOURCE'); ?></td>
					<td>
						<input name="slsf_lead_source" id="slsf_lead_source" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_lead_source); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
						<br /><small><?php echo JText::_('RSFP_SLSF_LEAD_SOURCE_DESC'); ?></small>
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_DESCRIPTION'); ?></td>
					<td>
						<input name="slsf_description" id="slsf_description" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_description); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_SALUTATION'); ?></td>
					<td>
						<input name="slsf_salutation" id="slsf_salutation" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_salutation); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
						<br /><small><?php echo JText::_('RSFP_SLSF_SALUTATION_DESC'); ?></small>
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_FIRST_NAME'); ?></td>
					<td>
						<input name="slsf_first_name" id="slsf_first_name" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_first_name); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_LAST_NAME'); ?></td>
					<td>
						<input name="slsf_last_name" id="slsf_last_name" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_last_name); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_TITLE'); ?></td>
					<td>
						<input name="slsf_title" id="slsf_title" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_title); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_INDUSTRY'); ?></td>
					<td>
						<input name="slsf_industry" id="slsf_industry" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_industry); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_COMPANY'); ?></td>
					<td>
						<input name="slsf_company" id="slsf_company" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_company); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_REVENUE'); ?></td>
					<td>
						<input name="slsf_revenue" id="slsf_revenue" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_revenue); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_EMPLOYEES'); ?></td>
					<td>
						<input name="slsf_employees" id="slsf_employees" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_employees); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_WEBSITE'); ?></td>
					<td>
						<input name="slsf_website" id="slsf_website" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_website); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_EMAIL'); ?></td>
					<td>
						<input name="slsf_email" id="slsf_email" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_email); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_EMAIL_OPTOUT'); ?></td>
					<td>
						<input name="slsf_emailoptout" id="slsf_emailoptout" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_emailoptout); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_PHONE'); ?></td>
					<td>
						<input name="slsf_phone" id="slsf_phone" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_phone); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_DO_NOT_CALL'); ?></td>
					<td>
						<input name="slsf_donotcall" id="slsf_donotcall" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_donotcall); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_MOBILE'); ?></td>
					<td>
						<input name="slsf_mobile" id="slsf_mobile" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_mobile); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_FAX'); ?></td>
					<td>
						<input name="slsf_fax" id="slsf_fax" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_fax); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_FAX_OPTOUT'); ?></td>
					<td>
						<input name="slsf_faxoptout" id="slsf_faxoptout" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_faxoptout); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_STREET'); ?></td>
					<td>
						<input name="slsf_street" id="slsf_street" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_street); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_CITY'); ?></td>
					<td>
						<input name="slsf_city" id="slsf_city" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_city); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_STATE'); ?></td>
					<td>
						<input name="slsf_state" id="slsf_state" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_state); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_ZIP'); ?></td>
					<td>
						<input name="slsf_zip" id="slsf_zip" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_zip); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_COUNTRY'); ?></td>
					<td>
						<input name="slsf_country" id="slsf_country" value="<?php echo RSFormProHelper::htmlEscape($row->slsf_country); ?>" class="rs_inp rs_80"  data-delimiter=" " data-placeholders="display" />
					</td>
				</tr>
				<tr>
					<td colspan="2" class="key" align="center"><p align="center"><?php echo JText::_('RSFP_SLSF_CUSTOM_FIELDS'); ?> <button class="rs_button btn btn-primary" type="button" onclick="addSalesforceCustomField();"><?php echo JText::_('RSFP_SLSF_ADD_CUSTOM_FIELD'); ?></button></p></td>
				</tr>
				<?php foreach ($row->slsf_custom_fields as $i => $field) { ?>
					<tr id="slsf_api_name<?php echo $i; ?>">
						<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_CUSTOM_FIELD_API_NAME'); ?></td>
						<td><input name="slsf_api_name[]" value="<?php echo RSFormProHelper::htmlEscape($field->api_name); ?>" class="rs_inp rs_50" /><button class="rs_button rs_right btn btn-danger" type="button" onclick="deleteSalesforceCustomField(<?php echo $i; ?>)"><?php echo JText::_('DELETE'); ?></button></td>
					</tr>
					<tr id="slsf_value<?php echo $i; ?>">
						<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SLSF_CUSTOM_FIELD_API_VALUE'); ?></td>
						<td><input name="slsf_value[]" value="<?php echo RSFormProHelper::htmlEscape($field->value); ?>" class="rs_inp rs_80" /></td>
					</tr>
				<?php } ?>
			</table>
		</td>
		<td valign="top">
			&nbsp;
		</td>
	</tr>
</table>