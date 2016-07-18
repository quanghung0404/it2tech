<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
function rsfp_changeMcList(what)
{
	var value = what.value;
	
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';
	
	var params = new Array();
	params.push('list_id=' + escape(value));
	
	xml = buildXmlHttp();
	var url = 'index.php?option=com_rsform&task=plugin&plugin_task=get_merge_vars';
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
			var table = document.getElementById('merge_vars');
			while (table.rows.length >= 1)
				table.deleteRow(table.rows.length - 1);
		
			var result = xml.responseText.split("\n");
			for (var i=0; i<result.length; i+=2)
			{
				if (typeof result[i+1] == 'undefined') {
					continue;
				}
				var x = table.insertRow(table.rows.length);
				var y = x.insertCell(0);
				y.innerHTML = '(' + result[i] + ') ' + result[i+1];
				y.nowrap = true;
				y.align = 'right';
				var y = x.insertCell(1);
				
				var select = document.createElement('select');
				select.name = 'merge_vars[' + result[i] + ']';
				<?php foreach ($fields_array as $field) { ?>
				var option = document.createElement('option');
				option.text = option.value = '<?php echo $field; ?>';
				select.options.add(option);
				<?php } ?>
				y.appendChild(select);
			}
				
			document.getElementById('state').innerHTML='Status: ok';
			document.getElementById('state').style.color='';
		}
	}
	
	xml2 = buildXmlHttp();
	var url = 'index.php?option=com_rsform&task=plugin&plugin_task=get_interest_groups';
	xml2.open("POST", url, true);
	
	var params2 = new Array();
	params2.push('list_id=' + escape(value));
	
	params2 = params2.join('&');
	
	//Send the proper header information along with the request
	xml2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml2.setRequestHeader("Content-length", params2.length);
	xml2.setRequestHeader("Connection", "close");
	
	xml2.send(params2);
	xml2.onreadystatechange=function()
	{
		if (xml2.readyState==4)
		{
			var RSFP_MAILCHIMP_INTEREST_GROUP_DESC = '<?php echo JText::_('RSFP_MAILCHIMP_INTEREST_GROUP_DESC', true); ?>';
			var table = document.getElementById('interest_groups');
			while (table.rows.length >= 1)
				table.deleteRow(table.rows.length - 1);
		
			if (xml2.responseText.indexOf("\n") == -1)
				return;
			
			var result = xml2.responseText.split("\n");
			for (var i=0; i<result.length; i+=2)
			{
				var x = table.insertRow(table.rows.length);
				var y = x.insertCell(0);
				y.innerHTML = result[i];
				y.nowrap = true;
				y.align = 'right';
				var y = x.insertCell(1);
				
				var select = document.createElement('select');
				select.name = 'interest_groups[' + result[i] + ']';
				<?php foreach ($fields_array as $field) { ?>
				var option = document.createElement('option');
				option.text = option.value = '<?php echo $field; ?>';
				select.options.add(option);
				<?php } ?>
				y.appendChild(select);
				
				var x = table.insertRow(table.rows.length);
				var y = x.insertCell(0);
				y.colSpan = 2;
				y.innerHTML = RSFP_MAILCHIMP_INTEREST_GROUP_DESC.replace('%s', result[i+1]);
			}
				
			document.getElementById('state').innerHTML='Status: ok';
			document.getElementById('state').style.color='';
		}
	}
}

function rsfp_changeMcEmailType(what)
{	
	if (what.value == 'user')
		document.getElementById('mc_email_type_field').disabled = false;
	else
		document.getElementById('mc_email_type_field').disabled = true;
}

function rsfp_changeMcAction(what)
{	
	if (what.value == 2)
		document.getElementById('mc_action_field').disabled = false;
	else
		document.getElementById('mc_action_field').disabled = true;
}
</script>

<table class="admintable">
<tr>
	<td valign="top" align="left" width="30%">
		<table class="table table-bordered">
			<tr>
				<td colspan="2" class="center"><?php echo JHTML::image('administrator/components/com_rsform/assets/images/mailchimp.png', 'MailChimp'); ?></td>
			</tr>
			<tr>
				<td colspan="2"><div class="alert alert-info"><?php echo JText::_('RSFP_MAILCHIMP_DESC'); ?></div></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_MAILCHIMP_USE_INTEGRATION'); ?></td>
				<td><?php echo $lists['published']; ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_MAILCHIMP_ACTION_DESC'); ?>"><?php echo JText::_('RSFP_MAILCHIMP_ACTION'); ?></span>
				</td>
				<td nowrap="nowrap"><?php echo $lists['mc_action']; ?> <?php echo $lists['mc_action_field']; ?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_MAILCHIMP_ACTION_WARNING'); ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_MAILCHIMP_LIST_ID'); ?></td>
				<td nowrap="nowrap"><?php echo $lists['mc_list_id']; ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_MAILCHIMP_EMAIL_TYPE_DESC'); ?>"><?php echo JText::_('RSFP_MAILCHIMP_EMAIL_TYPE'); ?></span>
				</td>
				<td nowrap="nowrap"><?php echo $lists['mc_email_type']; ?> <?php echo $lists['mc_email_type_field']; ?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_MAILCHIMP_EMAIL_TYPE_WARNING'); ?></td>
			</tr>
			<tr>
				<td colspan="2" class="key" align="center"><p align="center"><?php echo JText::_('RSFP_MAILCHIMP_SUBSCRIBE_OPTIONS'); ?></p></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_MAILCHIMP_DOUBLE_OPTIN_DESC'); ?>"><?php echo JText::_('RSFP_MAILCHIMP_DOUBLE_OPTIN'); ?></span>
				</td>
				<td><?php echo $lists['mc_double_optin']; ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_MAILCHIMP_UPDATE_EXISTING_DESC'); ?>"><?php echo JText::_('RSFP_MAILCHIMP_UPDATE_EXISTING'); ?></span>
				</td>
				<td><?php echo $lists['mc_update_existing']; ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_MAILCHIMP_REPLACE_INTERESTS_DESC'); ?>"><?php echo JText::_('RSFP_MAILCHIMP_REPLACE_INTERESTS'); ?></span>
				</td>
				<td><?php echo $lists['mc_replace_interests']; ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_MAILCHIMP_SEND_WELCOME_DESC'); ?>"><?php echo JText::_('RSFP_MAILCHIMP_SEND_WELCOME'); ?></span>
				</td>
				<td><?php echo $lists['mc_send_welcome']; ?></td>
			</tr>
			<tr>
				<td colspan="2" class="key" align="center"><p align="center"><?php echo JText::_('RSFP_MAILCHIMP_UNSUBSCRIBE_OPTIONS'); ?></p></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_MAILCHIMP_DELETE_MEMBER_DESC'); ?>"><?php echo JText::_('RSFP_MAILCHIMP_DELETE_MEMBER'); ?></span>
				</td>
				<td><?php echo $lists['mc_delete_member']; ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_MAILCHIMP_SEND_GOODBYE_DESC'); ?>"><?php echo JText::_('RSFP_MAILCHIMP_SEND_GOODBYE'); ?></span>
				</td>
				<td><?php echo $lists['mc_send_goodbye']; ?></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<span class="hasTip" title="<?php echo JText::_('RSFP_MAILCHIMP_SEND_NOTIFY_DESC'); ?>"><?php echo JText::_('RSFP_MAILCHIMP_SEND_NOTIFY'); ?></span>
				</td>
				<td><?php echo $lists['mc_send_notify']; ?></td>
			</tr>
			<tr>
				<td colspan="2" class="key" align="center"><p align="center"><?php echo JText::_('RSFP_MAILCHIMP_MERGE_VARS'); ?></p></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_MAILCHIMP_MERGE_VARS_DESC'); ?></td>
			</tr>
			<tbody id="merge_vars">
			<?php if (is_array($merge_vars)) { ?>
				<?php foreach ($merge_vars as $merge_var) { ?>
				<tr>
					<td nowrap="nowrap" align="right">(<?php echo $merge_var['tag']; ?>) <?php echo $merge_var['name']; ?></td>
					<td><?php echo $lists['fields'][$merge_var['tag']]; ?></td>
				</tr>
				<?php } ?>
			<?php } ?>
			</tbody>
			<tr>
				<td colspan="2" class="key" align="center"><p align="center"><?php echo JText::_('RSFP_MAILCHIMP_INTERESTS'); ?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_MAILCHIMP_INTERESTS_DESC'); ?></td>
			</tr>
			<tbody id="interest_groups">
			<?php if (is_array($interest_groups)) { ?>
				<?php foreach ($interest_groups as $interest_group) { ?>
				<tr>
					<td nowrap="nowrap" align="right"><?php echo $interest_group['name']; ?></td>
					<td><?php echo $lists['fields_groups'][$interest_group['id']]; ?></td>
				</tr>
				<tr>
					<td colspan="2"><?php echo JText::sprintf('RSFP_MAILCHIMP_INTEREST_GROUP_DESC', $lists['field_groups_desc'][$interest_group['id']]); ?></td>
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