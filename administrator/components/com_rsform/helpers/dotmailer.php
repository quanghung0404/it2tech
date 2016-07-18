<?php
/**
* @package RSform!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
function rsfp_changeDmList(what)
{
	var value = what.value;
	
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';
	
	var params = new Array();
	params.push('list_id=' + escape(value));
	
	xml = buildXmlHttp();
	var url = 'index.php?option=com_rsform&task=plugin&plugin_task=get_merge_vars_dm';
	xml.open("POST", url, true);
	
	params = params.join('&');
	
	//Send the proper header information along with the request
	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml.setRequestHeader("Content-length", params.length);
	xml.setRequestHeader("Connection", "close");
	
	xml.send(params);
	xml.onreadystatechange=function()
	{
		if (xml.readyState==4)
		{
			var table = document.getElementById('merge_vars_dm');
			while (table.rows.length >= 1)
				table.deleteRow(table.rows.length - 1);
		
			var result = xml.responseText.split("\n");
			
			if (xml.responseText != '') {
				for (var i=0; i<result.length; i++) {
					var x = table.insertRow(table.rows.length);
					var y = x.insertCell(0);
					y.innerHTML = result[i];
					y.nowrap = true;
					y.align = 'right';
					y.className = 'key';
					var y = x.insertCell(1);
					
					var select = document.createElement('select');
					select.name = 'dotmailer[merge_vars][' + result[i] + ']';
					<?php foreach ($fields_array as $field) { ?>
					var option = document.createElement('option');
					option.text = option.value = '<?php echo $field; ?>';
					select.options.add(option);
					<?php } ?>
					y.appendChild(select);
				}
			}
				
			document.getElementById('state').innerHTML='Status: ok';
			document.getElementById('state').style.color='';
		}
	}
}

function rsfp_changeDmAction(what) {	
	if (what.value == 2)
		document.getElementById('dotmailerdm_action_field').disabled = false;
	else
		document.getElementById('dotmailerdm_action_field').disabled = true;
}

function rsfp_changeDmAudienceType(what) {	
	if (what.value == 'user')
		document.getElementById('dotmailerdm_audience_type_field').disabled = false;
	else
		document.getElementById('dotmailerdm_audience_type_field').disabled = true;
}

function rsfp_changeDmOptinType(what) {	
	if (what.value == 'user')
		document.getElementById('dotmailerdm_optin_type_field').disabled = false;
	else
		document.getElementById('dotmailerdm_optin_type_field').disabled = true;
}

function rsfp_changeDmEmailType(what) {	
	if (what.value == 'user')
		document.getElementById('dotmailerdm_email_type_field').disabled = false;
	else
		document.getElementById('dotmailerdm_email_type_field').disabled = true;
}
</script>

<table class="admintable">
<tr>
	<td valign="top" align="left" width="65%">
		<table class="table table-bordered">
			<tr>
				<td colspan="2" style="text-align: center;" class="center"><?php echo JHTML::image('administrator/components/com_rsform/assets/images/dotmailer-logo.png', 'DotMailer'); ?></td>
			</tr>
			<tr>
				<td colspan="2"><div class="alert alert-info"><?php echo JText::_('RSFP_DOTMAILER_DESC'); ?></div></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_DOTMAILER_USE_INTEGRATION'); ?></td>
				<td><?php echo $lists['dm_published']; ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_DOTMAILER_ACTION_DESC'); ?>"><?php echo JText::_('RSFP_DOTMAILER_ACTION'); ?></span>
				</td>
				<td nowrap="nowrap"><?php echo $lists['dm_action']; ?> <?php echo $lists['dm_action_field']; ?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_DOTMAILER_ACTION_WARNING'); ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_DOTMAILER_LIST_ID'); ?></td>
				<td nowrap="nowrap"><?php echo $lists['dm_list_id']; ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_DOTMAILER_AUDIENCE_TYPE_DESC'); ?>"><?php echo JText::_('RSFP_DOTMAILER_AUDIENCE_TYPE'); ?></span>
				</td>
				<td nowrap="nowrap"><?php echo $lists['dm_audience_type']; ?> <?php echo $lists['dm_audience_type_field']; ?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_DOTMAILER_AUDIENCE_TYPE_WARNING'); ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_DOTMAILER_OPTIN_TYPE_DESC'); ?>"><?php echo JText::_('RSFP_DOTMAILER_OPTIN_TYPE'); ?></span>
				</td>
				<td nowrap="nowrap"><?php echo $lists['dm_optin_type']; ?> <?php echo $lists['dm_optin_type_field']; ?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_DOTMAILER_OPTIN_TYPE_WARNING'); ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_DOTMAILER_EMAIL_TYPE_DESC'); ?>"><?php echo JText::_('RSFP_DOTMAILER_EMAIL_TYPE'); ?></span>
				</td>
				<td nowrap="nowrap"><?php echo $lists['dm_email_type']; ?> <?php echo $lists['dm_email_type_field']; ?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_DOTMAILER_EMAIL_TYPE_WARNING'); ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_DOTMAILER_EMAIL_DESC'); ?>"><?php echo JText::_('RSFP_DOTMAILER_EMAIL'); ?></span>
				</td>
				<td nowrap="nowrap"><?php echo $lists['dm_email']; ?></td>
			</tr>
			<tr>
				<td colspan="2" class="key" align="center"><p align="center"><?php echo JText::_('RSFP_DOTMAILER_MERGE_VARS'); ?></p></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_DOTMAILER_MERGE_VARS_DESC'); ?></td>
			</tr>
			<tbody id="merge_vars_dm">
			<?php if (is_array($merge_vars)) { ?>
				<?php foreach ($merge_vars as $merge_var) { ?>
				<tr>
					<td nowrap="nowrap" align="right" class="key"><?php echo $merge_var->name; ?></td>
					<td><?php echo $lists['dm_fields'][$merge_var->name]; ?></td>
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