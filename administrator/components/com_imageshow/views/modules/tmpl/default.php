<?php
/**
 * @version		$Id: default.php 16648 2012-10-03 10:15:24Z giangnd $
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
$client = $this->state->get('filter.client_id') ? 'administrator' : 'site';
$user = JFactory::getUser();
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_modules');
$saveOrder	= $listOrder == 'ordering';
?>
<form
	action="<?php echo JRoute::_('index.php?option=com_imageshow&controller=modules&tmpl=component'); ?>"
	method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
			</label> <input type="text" name="filter_search" id="filter_search"
				value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
				title="<?php echo htmlspecialchars(JText::_('COM_MODULES_MODULES_FILTER_SEARCH_DESC')); ?>" />
			<button type="submit">
			<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
			</button>
			<button type="button"
				onclick="document.id('filter_search').value='';this.form.submit();">
				<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
			</button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_state" class="inputbox"
				onchange="this.form.submit()">
				<option value="">
				<?php echo JText::_('JOPTION_SELECT_PUBLISHED');?>
				</option>
				<?php echo JHtml::_('select.options', ModulesHelper::getStateOptions(), 'value', 'text', $this->state->get('filter.state'));?>
			</select> <select name="filter_position" class="inputbox"
				onchange="this.form.submit()">
				<option value="">
				<?php echo JText::_('COM_MODULES_OPTION_SELECT_POSITION');?>
				</option>
				<?php echo JHtml::_('select.options', ModulesHelper::getPositions($this->state->get('filter.client_id')), 'value', 'text', $this->state->get('filter.position'));?>
			</select> <select name="filter_module" class="inputbox"
				onchange="this.form.submit()">
				<option value="">
				<?php echo JText::_('COM_MODULES_OPTION_SELECT_MODULE');?>
				</option>
				<?php echo JHtml::_('select.options', ModulesHelper::getModules($this->state->get('filter.client_id')), 'value', 'text', $this->state->get('filter.module'));?>
			</select> <select name="filter_access" class="inputbox"
				onchange="this.form.submit()">
				<option value="">
				<?php echo JText::_('JOPTION_SELECT_ACCESS');?>
				</option>
				<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
			</select>
		</div>
	</fieldset>
	<div class="clearbreak"></div>

	<table class="adminlist" id="modules-mgr">
		<thead>
			<tr>
				<th width="1%"><input type="checkbox" name="checkall-toggle"
					value="" onclick="checkAll(this)" />
				</th>
				<th class="title" width="60%"><?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'title', $listDirn, $listOrder); ?>
				</th>
				<th width="15%" class="left"><?php echo JHtml::_('grid.sort',  'COM_MODULES_HEADING_POSITION', 'position', $listDirn, $listOrder); ?>
				</th>
				<th width="10%" class="left"><?php echo JHtml::_('grid.sort', 'COM_MODULES_HEADING_MODULE', 'name', $listDirn, $listOrder); ?>
				</th>
				<th width="10%"><?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap"><?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10"><?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
		$ordering	= ($listOrder == 'ordering');
		$canCreate	= $user->authorise('core.create',		'com_modules');
		$canEdit	= $user->authorise('core.edit',			'com_modules');
		$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out==$user->get('id')|| $item->checked_out==0;
		$canChange	= $user->authorise('core.edit.state',	'com_modules') && $canCheckin;
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td><a class="poiter"
					onclick="if (window.parent) window.parent.selectModule_id('<?php echo $item->id?>', '<?php echo $this->escape($item->title); ?>', <?php echo JRequest::getCmd('seo'); ?>);">
					<?php echo $this->escape($item->title); ?> </a>
				</td>
				<td class="left"><?php echo $item->position; ?>
				</td>
				<td class="left"><?php echo $item->name;?>
				</td>
				<td class="center"><?php echo $this->escape($item->access_level); ?>
				</td>
				<td class="center"><?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" /> <input type="hidden"
			name="boxchecked" value="0" /> <input type="hidden"
			name="filter_order" value="<?php echo $listOrder; ?>" /> <input
			type="hidden" name="filter_order_Dir"
			value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
