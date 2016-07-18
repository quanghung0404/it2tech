<?php
/**
 * @package         Advanced Module Manager
 * @version         5.3.6PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

require_once JPATH_PLUGINS . '/system/nnframework/helpers/versions.php';

$client    = $this->state->get('filter.client_id') ? 'administrator' : 'site';
$user      = JFactory::getUser();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$trashed   = $this->state->get('filter.state') == -2 ? true : false;
$canOrder  = $user->authorise('core.edit.state', 'com_modules');
$saveOrder = $listOrder == 'ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_advancedmodules&task=modules.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'moduleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$showcolors = ($client == 'site' && $this->config->show_color);
if ($showcolors)
{
	require_once JPATH_LIBRARIES . '/joomla/form/fields/color.php';
	$colorfield = new JFormFieldColor;
	$script     = "
		function setColor(id, el)
		{
			var f = document.getElementById('adminForm');
			f.setcolor.value = jQuery(el).val();
			listItemTask(id, 'modules.setcolor');
		}
	";
	JFactory::getDocument()->addScriptDeclaration($script);
}

$sortFields = $this->getSortFields();

$script = "
	Joomla.orderTable = function()
	{
		table = document.getElementById('sortTable');
		direction = document.getElementById('directionTable');
		order = table.options[table.selectedIndex].value;
		if (order != '" . $listOrder . "')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	};
";
JFactory::getDocument()->addScriptDeclaration($script);

JHtml::stylesheet('nnframework/style.min.css', false, true);
?>
<form action="<?php echo JRoute::_('index.php?option=com_advancedmodules'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php
		// Version check
		if ($this->config->show_update_notification)
		{
			echo NoNumberVersions::render('ADVANCED_MODULE_MANAGER');
		}
		?>
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>"
				       value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip"
				       title="<?php echo JHtml::tooltipText('COM_MODULES_MODULES_FILTER_SEARCH_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
					<span class="icon-search"></span></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>"
				        onclick="document.getElementById('filter_search').value='';this.form.submit();">
					<span class="icon-remove"></span></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
					<option value="asc" <?php if ($listDirn == 'asc')
					{
						echo 'selected="selected"';
					} ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
					<option value="desc" <?php if ($listDirn == 'desc')
					{
						echo 'selected="selected"';
					} ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING'); ?></option>
				</select>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY'); ?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
				</select>
			</div>
		</div>
		<div class="clear"></div>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('COM_MODULES_MSG_MANAGE_NO_MODULES'); ?>
			</div>
		<?php else : ?>
			<?php $cols = 10; ?>
			<table class="table table-striped nn_tablelist" id="moduleList">
				<thead>
				<tr>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', '<span class="icon-menu-2"></span>', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>
					<th width="1%" class="hidden-phone">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					<th width="1%" class="nowrap center" style="min-width:55px">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
					</th>
					<?php if ($showcolors) : ?>
						<?php $cols++; ?>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', '<span class="icon-color"></span>', 'color', $listDirn, $listOrder, null, 'asc', 'AMM_COLOR'); ?>
						</th>
					<?php endif; ?>
					<th class="title">
						<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
					</th>
					<?php if ($this->config->show_note == 3) : ?>
						<?php $cols++; ?>
						<th class="title">
							<?php echo JHtml::_('grid.sort', 'JFIELD_NOTE_LABEL', 'a.note', $listDirn, $listOrder); ?>
						</th>
					<?php endif; ?>
					<th width="15%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort', 'COM_MODULES_HEADING_POSITION', 'position', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort', 'COM_MODULES_HEADING_MODULE', 'name', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort', 'NN_MENU_ITEMS', 'pages', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
					</th>
					<th width="5%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
				</thead>
				<tfoot>
				<tr>
					<td colspan="<?php echo $cols; ?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
				</tfoot>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
					$ordering   = ($listOrder == 'ordering');
					$canCreate  = $user->authorise('core.create', 'com_modules');
					$canEdit    = $user->authorise('core.edit', 'com_modules.module.' . $item->id);
					$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
					$canChange  = $user->authorise('core.edit.state', 'com_modules.module.' . $item->id) && $canCheckin;
					?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->position; ?>">
						<td class="order nowrap center hidden-phone">
							<?php
							$iconClass = '';
							if (!$canChange)
							{
								$iconClass = ' inactive';
							}
							elseif (!$saveOrder)
							{
								$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
							}
							?>
							<span class="sortable-handler<?php echo $iconClass ?>">
									<span class="icon-menu"></span>
								</span>
							<?php if ($canChange && $saveOrder) : ?>
								<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>"
								       class="width-20 text-area-order" />
							<?php endif; ?>
						</td>
						<td class="center hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center">
							<div class="btn-group">
								<?php echo JHtml::_('jgrid.published', $item->published, $i, 'modules.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
								<?php
								// Create dropdown items
								JHtml::_('actionsdropdown.duplicate', 'cb' . $i, 'modules');

								$action = $trashed ? 'untrash' : 'trash';
								JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'modules');

								// Render dropdown list
								echo JHtml::_('actionsdropdown.render', $this->escape($item->title));
								?>
							</div>
						</td>
						<?php if ($showcolors) : ?>
							<td class="center inlist">
								<?php
								$color          = (isset($item->params->color) && $item->params->color) ? $color = str_replace('##', '#', $item->params->color) : 'none';
								$element        = new SimpleXMLElement(
									'<field
											name="color_' . $i . '"
											type="color"
											control="simple"
											default=""
											colors="' . (isset($this->config->main_colors) ? $this->config->main_colors : '') . '"
											split="4"
											onchange="setColor(\'cb' . $i . '\', this)"
											/>'
								);
								$element->value = $color;
								$colorfield->setup($element, $color);
								echo $colorfield->__get('input');
								?>
							</td>
						<?php endif; ?>
						<td class="has-context">
							<div class="pull-left">
								<?php if ($item->checked_out) : ?>
									<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'modules.', $canCheckin); ?>
								<?php endif; ?>
								<?php
								$title   = $this->escape($item->title);
								$tooltip = '<strong>' . JText::_('AMM_EDIT_MODULE') . '</strong><br />' . htmlspecialchars($title);
								if (!empty($item->note) && $this->config->show_note == 1)
								{
									$tooltip .= '<br /><em>' . htmlspecialchars(JText::sprintf('JGLOBAL_LIST_NOTE', $this->escape($item->note))) . '</em>';
								}
								$title = '<span rel="tooltip" title="' . $tooltip . '">' . $title . '</span>';
								?>
								<?php if ($canEdit) : ?>
									<a href="<?php echo JRoute::_('index.php?option=com_advancedmodules&task=module.edit&id=' . (int) $item->id); ?>">
										<?php echo $title; ?></a>
								<?php else : ?>
									<?php echo $title; ?>
								<?php endif; ?>
								<?php if (!empty($item->note) && $this->config->show_note == 2) : ?>
									<div class="small">
										<?php echo JText::sprintf('JGLOBAL_LIST_NOTE', $this->escape($item->note)); ?>
									</div>
								<?php endif; ?>
							</div>
						</td>
						<?php if ($this->config->show_note == 3) : ?>
							<td class="has-context">
								<?php echo $this->escape($item->note); ?>
							</td>
						<?php endif; ?>
						<td class="small hidden-phone">
							<?php if ($item->position) : ?>
								<span class="label label-info">
										<?php echo $item->position; ?>
									</span>
							<?php else : ?>
								<span class="label">
										<?php echo JText::_('JNONE'); ?>
									</span>
							<?php endif; ?>
						</td>
						<td class="small hidden-phone">
							<?php echo $item->name; ?>
						</td>
						<td class="small hidden-phone">
							<?php echo $item->pages; ?>
						</td>

						<td class="small hidden-phone">
							<?php echo $this->escape($item->access_level); ?>
						</td>
						<td class="small hidden-phone">
							<?php
							if ($item->language == '')
							{
								echo JText::_('JDEFAULT');
							}
							elseif ($item->language == '*')
							{
								$advanced_params = json_decode($item->advancedparams);

								if (!empty($advanced_params->assignto_languages) && $advanced_params->assignto_languages == 2)
								{
									$text = JText::_('COM_MODULES_ASSIGNED_VARIES_EXCEPT');
								}
								else if (!empty($advanced_params->assignto_languages) && !empty($advanced_params->assignto_languages_selection))
								{
									$text = JText::_('COM_MODULES_ASSIGNED_VARIES_ONLY');
								}
								else
								{
									$text = JText::alt('JALL', 'language');
								}

								echo $text;
							}
							else
							{
								$language_params = json_decode($item->language_params);
								echo !empty($language_params->name) ? $this->escape($language_params->name) : JText::_('JUNDEFINED');
							}
							?>
						</td>
						<td class="hidden-phone">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<?php // Load the batch processing form. ?>
		<?php if ($user->authorise('core.create', 'com_modules')
			&& $user->authorise('core.edit', 'com_modules')
			&& $user->authorise('core.edit.state', 'com_modules')
		) : ?>
			<?php echo JHtml::_(
				'bootstrap.renderModal',
				'collapseModal',
				array(
					'title'  => JText::_('COM_MODULES_BATCH_OPTIONS'),
					'footer' => $this->loadTemplate('batch_footer'),
				),
				$this->loadTemplate('batch_body')
			); ?>
		<?php endif; ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<input type="hidden" name="setcolor" value="" />
		<?php echo JHtml::_('form.token'); ?>

		<?php if ($this->config->show_switch) : ?>
			<div style="text-align:right">
				<a href="<?php echo JRoute::_('index.php?option=com_modules&force=1'); ?>"><?php echo JText::_('AMM_SWITCH_TO_CORE'); ?></a>
			</div>
		<?php endif; ?>
		<?php

		// Copyright
		echo NoNumberVersions::getFooter('ADVANCED_MODULE_MANAGER', $this->config->show_copyright);
		?>
	</div>
</form>
