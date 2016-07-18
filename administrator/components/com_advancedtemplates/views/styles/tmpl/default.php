<?php
/**
 * @package         Advanced Template Manager
 * @version         1.6.4PRO
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

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
require_once JPATH_PLUGINS . '/system/nnframework/helpers/versions.php';

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user       = JFactory::getUser();
$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$showthumbs = $this->config->show_thumbs;
$showcolors = $this->config->show_color;
if ($showcolors)
{
	require_once JPATH_LIBRARIES . '/joomla/form/fields/color.php';
	$colorfield = new JFormFieldColor;
	$script     = "
		function setColor(id, el)
		{
			var f = document.getElementById('adminForm');
			f.setcolor.value = jQuery(el).val();
			listItemTask(id, 'styles.setcolor');
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
<form action="<?php echo JRoute::_('index.php?option=com_advancedtemplates'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php
		// Version check
		if ($this->config->show_update_notification)
		{
			echo NoNumberVersions::render('ADVANCED_TEMPLATE_MANAGER');
		}
		?>
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>"
				       value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip"
				       title="<?php echo JHtml::tooltipText('COM_TEMPLATES_STYLES_FILTER_SEARCH_DESC'); ?>" />
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
					<option value="">
						<?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
					<option value="asc"<?php echo $listDirn == 'asc' ? ' selected="selected"' : ''; ?>>
						<?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
					<option value="desc" <?php echo $listDirn == 'desc' ? ' selected="selected"' : ''; ?>>
						<?php echo JText::_('JGLOBAL_ORDER_DESCENDING'); ?></option>
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
				<?php echo JText::_('COM_TEMPLATES_MSG_MANAGE_NO_STYLES'); ?>
			</div>
		<?php else : ?>

			<?php $cols = 7; ?>
			<table class="table table-striped" id="styleList">
				<thead>
				<tr>
					<th width="5">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					<?php if ($showthumbs) : ?>
						<?php $cols++; ?>
						<th width="5">
							&#160;
						</th>
					<?php endif; ?>
					<?php if ($showcolors) : ?>
						<?php $cols++; ?>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', '<span class="icon-color"></span>', 'color', $listDirn, $listOrder, null, 'asc', 'ATP_COLOR'); ?>
						</th>
					<?php endif; ?>
					<th width="5">
						&#160;
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'COM_TEMPLATES_HEADING_STYLE', 'a.title', $listDirn, $listOrder); ?>
					</th>
					<th width="5%" class="nowrap">
						<?php echo JHtml::_('grid.sort', 'COM_TEMPLATES_HEADING_DEFAULT', 'a.home', $listDirn, $listOrder); ?>
					</th>
					<th width="5%" class="nowrap center hidden-phone">
						<?php echo JText::_('COM_TEMPLATES_HEADING_ASSIGNED'); ?>
					</th>
					<th width="10%" class="nowrap">
						<?php echo JHtml::_('grid.sort', 'JCLIENT', 'a.client_id', $listDirn, $listOrder); ?>
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
					$canCreate = $user->authorise('core.create', 'com_templates');
					$canEdit   = $user->authorise('core.edit', 'com_templates');
					$canChange = $user->authorise('core.edit.state', 'com_templates');
					?>
					<tr class="row<?php echo $i % 2; ?>">
						<td width="1%" class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<?php if ($showthumbs) : ?>
							<td class="center thumbnail-small">
								<?php echo JHtml::_('templates.thumb', $item->template, $item->client_id); ?>
							</td>
						<?php endif; ?>
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
						<td width="1%" class="center">
							<?php if ($this->preview && $item->client_id == '0') : ?>
								<a target="_blank" href="<?php echo JUri::root() . 'index.php?tp=1&templateStyle=' . (int) $item->id ?>" class="jgrid">
									<span class="icon-eye-open hasTooltip"
									      title="<?php echo JHtml::tooltipText(JText::_('COM_TEMPLATES_TEMPLATE_PREVIEW'), $item->title, 0); ?>"></span></a>
							<?php elseif ($item->client_id == '1') : ?>
								<span class="icon-eye-close disabled hasTooltip"
								      title="<?php echo JHtml::tooltipText('COM_TEMPLATES_TEMPLATE_NO_PREVIEW_ADMIN'); ?>"></span>
							<?php else: ?>
								<span class="icon-eye-close disabled hasTooltip"
								      title="<?php echo JHtml::tooltipText('COM_TEMPLATES_TEMPLATE_NO_PREVIEW'); ?>"></span>
							<?php endif; ?>
						</td>
						<td>
							<?php
							$title = $item->title;
							$title = str_ireplace($item->template . ' - ', '', $title);
							$title = '<span class="label label-info small">' . $item->template . '</span> ' . $this->escape($title);
							?>
							<?php if ($canEdit) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_advancedtemplates&task=style.edit&id=' . (int) $item->id); ?>">
									<?php echo $title; ?></a>
							<?php else : ?>
								<?php echo $title; ?>
							<?php endif; ?>
						</td>
						<td>
							<?php if ($item->home == '0' || $item->home == '1'): ?>
								<?php echo JHtml::_('jgrid.isdefault', $item->home != '0', $i, 'styles.', $canChange && $item->home != '1'); ?>
							<?php elseif ($canChange): ?>
								<a href="<?php echo JRoute::_('index.php?option=com_advancedtemplates&task=styles.unsetDefault&cid[]=' . $item->id . '&' . JSession::getFormToken() . '=1'); ?>">
									<?php echo JHtml::_('image', 'mod_languages/' . $item->image . '.gif', $item->language_title, array('title' => JText::sprintf('COM_TEMPLATES_GRID_UNSET_LANGUAGE', $item->language_title)), true); ?>
								</a>
							<?php else: ?>
								<?php echo JHtml::_('image', 'mod_languages/' . $item->image . '.gif', $item->language_title, array('title' => $item->language_title), true); ?>
							<?php endif; ?>
						</td>
						<td class="center hidden-phone">
							<?php if ($item->assigned > 0) : ?>
								<span class="icon-ok tip hasTooltip"
								      title="<?php echo JHtml::tooltipText(JText::plural('COM_TEMPLATES_ASSIGNED', $item->assigned), '', 0); ?>"></span>
							<?php else : ?>
								&#160;
							<?php endif; ?>
						</td>
						<td class="small">
							<?php echo $item->client_id == 0 ? JText::_('JSITE') : JText::_('JADMINISTRATOR'); ?>
						</td>
						<td class="hidden-phone">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<input type="hidden" name="setcolor" value="" />
		<?php echo JHtml::_('form.token'); ?>

		<?php if ($this->config->show_switch) : ?>
			<div style="text-align:right">
				<a href="<?php echo JRoute::_('index.php?option=com_templates&force=1'); ?>"><?php echo JText::_('ATP_SWITCH_TO_CORE'); ?></a>
			</div>
		<?php endif; ?>
		<?php

		// Copyright
		echo NoNumberVersions::getFooter('ADVANCED_TEMPLATE_MANAGER', $this->config->show_copyright);
		?>
	</div>
</form>
