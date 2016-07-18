<?php
/**
 * @version       1.0
 * @package       RSform!Pro 1.51.0
 * @copyright (C) 2007-2010 www.rsjoomla.com
 * @license       GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
?>
	<div id="rsfpcampaignmonitordiv">
		<table class="admintable">
			<tr>
				<td valign="top" align="left" width="30%">
					<table class="table table-bordered">
						<tr class="enable_cm">
							<td width="80" align="right" nowrap="nowrap" class="key">
								<label for="enable_campaignmonitor"><?php echo JText::_('RSFP_CAMPAIGNMONITOR_ENABLE'); ?></label>
							</td>
							<td><?php echo RSFormProHelper::renderHTML('select.booleanlist', 'enable_campaignmonitor', '', $row->enable_campaignmonitor); ?></td>
						</tr>
						<tr class="campaignmonitor_fields">
							<td width="80" align="right" nowrap="nowrap" class="key">
								<label for="campaignmonitor_update"><?php echo JText::_('RSFP_CAMPAIGNMONITOR_UPDATE_IF_EXISTS'); ?></label>
							</td>
							<td><?php echo RSFormProHelper::renderHTML('select.booleanlist', 'campaignmonitor_update', '', $row->campaignmonitor_update); ?></td>
						</tr>

						<tr class="campaignmonitor_fields">
							<td width="80" align="right" nowrap="nowrap" class="key">
								<label for="campaignmonitor_list"><?php echo JText::_('RSFP_CAMPAIGNMONITOR_LISTS'); ?></label>
							</td>
							<?php

							$response = new CS_REST_Clients($campaignmonitor_client, $auth);
							$response = $response->get_lists();

							if (!$response->was_successful())
							{
								throw new Exception(JText::sprintf('RSFP_CAMPAIGNMONITOR_ERROR', $response->response->Code, $response->response->Message));
							}
							$lists = [];

							foreach ($response->response as $list)
							{
								$lists[] = JHtml::_('select.option', $list->ListID, $list->Name);
							}
							?>
							<?php
							if (!empty($lists))
							{
								?>
								<td class="campaignmonitor_fields">
									<select name="campaignmonitor_list" id="campaignmonitor_list">
										<?php echo JHtml::_('select.options',
											$lists,
											'value', 'text', RSFormProHelper::htmlEscape($row->campaignmonitor_list));
										?>
									</select>
								</td>
								<?php
							}
							else
							{
								$url = JURI::current() . '?option=com_rsform&task=forms.edit&formId=' . $app->input->get('formId', '') . '&tabposition='. $app->input->get('tabposition', '') .'&tab='.  $app->input->get('tab', '') .'&plugin_task=defaultListCM&task=plugin';
								?>
								<td class="campaignmonitor_fields">
									<a class="btn btn-primary" href="<?php echo $url ?>"><?php echo JText::_('RSFP_CAMPAIGNMONITOR_CREATEDEFAULTLIST'); ?></a>
								</td>
								<?php
								throw new Exception(JText::_('RSFP_CAMPAIGNMONITOR_NOLIST'));
							} ?>
						</tr>

						<?php
						foreach ($merge_vars as $merge_var => $title)
						{ ?>
							<tr class="campaignmonitor_fields">
								<td width="80" class="key" nowrap="nowrap" align="right">
									<label for="<?php echo 'campaignmonitor_vars' . $merge_var ?>"><?php echo $title; ?></label></td>
								<td><?php echo JHtml::_('select.genericlist', $fields, 'campaignmonitor_vars[' . $merge_var . ']', null, 'value', 'text', isset($row->vars[$merge_var]) ? $row->vars[$merge_var] : null); ?></td>
							</tr>
						<?php } ?>
					</table>
				</td>
			</tr>
		</table>
	</div>
<?php

RSFormProAssets::addScriptDeclaration('
		jQuery(document).ready(function ($) {
			var $enable_cm = $(\'.enable_cm\');
			var $cm_default = $(\'.enable_cm input[type="radio"]:checked\').val();

			if ($cm_default == \'1\') {
				$(\'.campaignmonitor_fields\').show();
			} else {
				$(\'.campaignmonitor_fields\').hide();
			}

			$enable_cm.change(function () {
				var $val = $(\'.enable_cm input[type="radio"]:checked\').val();
				if ($val == \'1\') {
					$(\'.campaignmonitor_fields\').show();
				} else {
					$(\'.campaignmonitor_fields\').hide();
				}
			});
		});
	');