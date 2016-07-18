<?php
/**
 * @version       1.0
 * @package       RSform!Pro 1.51.0
 * @copyright (C) 2007-2010 www.rsjoomla.com
 * @license       GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
?>
	<div id="rsfpgetresponsediv">
		<legend><?php echo JText::_('RSFP_GETRESPONSE') ?></legend>
		<table class="admintable">
			<tr>
				<td valign="top" align="left" width="30%">
					<table class="table table-bordered">
						<tr class="enable_gr">
							<td width="80" align="right" nowrap="nowrap" class="key">
								<label for="enable_getresponse"><?php echo JText::_('RSFP_GETRESPONSE_ENABLE'); ?></label>
							</td>
							<td><?php echo RSFormProHelper::renderHTML('select.booleanlist', 'enable_getresponse', '', $row->enable_getresponse); ?></td>
						</tr>

						<tr class="get_response_fields">
							<td width="80" align="right" nowrap="nowrap" class="key">
								<label for="getresponse_update"><?php echo JText::_('RSFP_GETRESPONSE_UPDATE_IF_EXISTS'); ?></label>
							</td>
							<td><?php echo RSFormProHelper::renderHTML('select.booleanlist', 'getresponse_update', '', $row->getresponse_update); ?></td>
						</tr>

						<tr class="get_response_fields">
							<td width="80" align="right" nowrap="nowrap" class="key">
								<label for="getresponse_list"><?php echo JText::_('RSFP_GETRESPONSE_LISTS'); ?></label>
							</td>
							<td>
								<?php
								$campaigns = $response->getCampaigns();
								$lists     = array();

								foreach ($campaigns as $item)
								{
									$lists[] = JHtml::_('select.option', $item->campaignId, $item->name);
								}

								?>
								<select name="getresponse_list" id="getresponse_list">
									<?php echo JHtml::_('select.options',
										$lists,
										'value', 'text', RSFormProHelper::htmlEscape($row->getresponse_list));
									?>
								</select>

							</td>
						</tr>
						<?php

						foreach ($merge_vars as $merge_var => $title)
						{ ?>
							<tr class="get_response_fields">
								<td width="80" class="key" nowrap="nowrap" align="right">
									<label for="<?php echo 'getresponse_vars' . $merge_var ?>"><?php echo $title; ?></label>
								</td>
								<td><?php echo JHtml::_('select.genericlist', $fields, 'getresponse_vars[' . $merge_var . ']', null, 'value', 'text', isset($row->vars[$merge_var]) ? $row->vars[$merge_var] : null); ?>

									<?php
									if (isset($custom_values[$merge_var]))
									{?>
										<div id="getresponse_possible_values_<?php echo $merge_var; ?>" class="getresponse_possible_values">
											<button class="btn btn-possible-values"><?php echo JText::_('RSFP_POSSIBLE_VALUES') ?></button>
											<p><small><?php echo nl2br(RSFormProHelper::htmlEscape(implode("\n", $custom_values[$merge_var]))); ?></small></p>
										</div>

									<?php } ?>

								</td>

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
			var $enable_vr = $(\'.enable_gr\');
			var $vr_default = $(\'.enable_gr input[type="radio"]:checked\').val();

			if ($vr_default == \'1\') {
				$(\'.get_response_fields\').show();
			} else {
				$(\'.get_response_fields\').hide();
			}

			$enable_vr.change(function () {
				var $val = $(\'.enable_gr input[type="radio"]:checked\').val();
				if ($val == \'1\') {
					$(\'.get_response_fields\').show();
				} else {
					$(\'.get_response_fields\').hide();
				}
			});

			var $possible_values = $(\'.btn-possible-values\');

			$possible_values.each(function(){
				$(this).click(function(e){
					e.preventDefault();
					$(this).next(\'p\').slideToggle(\'fast\');
				});
			});
		});
	');

RSFormProAssets::addStyleDeclaration('
	.getresponse_possible_values .btn-possible-values{
		display:block;
		clear:both;
		margin-bottom:20px;
	}
	.getresponse_possible_values p{
		display:none;
	}
');