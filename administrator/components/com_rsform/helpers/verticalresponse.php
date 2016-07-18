<?php
/**
 * @package    RSForm! Pro
 * @copyright  (c) 2007 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
?>
	<div id="rsfpverticalresponsediv">
		<table class="admintable">
			<tr>
				<td valign="top" align="left" width="30%">
					<table class="table table-bordered">
						<tr class="enable_vr">
							<td width="80" align="right" nowrap="nowrap" class="key">
								<label for="enable_verticalresponse"><?php echo JText::_('RSFP_VERTICALRESPONSE_ENABLE'); ?></label>
							</td>
							<td><?php echo RSFormProHelper::renderHTML('select.booleanlist', 'enable_verticalresponse', '', $row->enable_verticalresponse); ?></td>
						</tr>

						<tr class="vertical_response_fields">
							<td width="80" align="right" nowrap="nowrap" class="key">
								<label for="verticalresponse_update"><?php echo JText::_('RSFP_VERTICALRESPONSE_UPDATE_IF_EXISTS'); ?></label>
							</td>
							<td><?php echo RSFormProHelper::renderHTML('select.booleanlist', 'verticalresponse_update', '', $row->verticalresponse_update); ?></td>
						</tr>

						<tr class="vertical_response_fields">
							<td width="80" align="right" nowrap="nowrap" class="key">
								<label for="verticalresponse_list"><?php echo JText::_('RSFP_VERTICALRESPONSE_LISTS'); ?></label>
							</td>
							<td>
							<?php

							$response = contactList::get(
								ROOT_URL . 'lists',
								array('type' => 'basic')
							);

							$lists = array();

							foreach ($response['items'] as $item)
							{
								$lists[] = JHtml::_('select.option', $item['attributes']['id'], $item['attributes']['name']);
							}

							?>
							<select name="verticalresponse_list" id="verticalresponse_list">
								<?php echo JHtml::_('select.options',
									$lists,
									'value', 'text', RSFormProHelper::htmlEscape($row->verticalresponse_list));
								?>
							</select>

							</td>
						</tr>
						<?php
						foreach ($merge_vars as $merge_var => $title)
						{ ?>
							<tr class="vertical_response_fields">
								<td width="80" class="key" nowrap="nowrap" align="right">
									<label for="<?php echo 'verticalresponse_vars' . $merge_var ?>"><?php echo $title; ?></label></td>
								<td><?php echo JHtml::_('select.genericlist', $fields, 'verticalresponse_vars[' . $merge_var . ']', null, 'value', 'text', isset($row->vars[$merge_var]) ? $row->vars[$merge_var] : null); ?></td>
							</tr>
						<?php } ?>

					</table>
				</td>
			</tr>
		</table>
	</div>