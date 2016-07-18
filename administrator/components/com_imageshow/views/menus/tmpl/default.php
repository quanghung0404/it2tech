<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: default.php 14064 2012-07-16 10:45:41Z thangbh $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
if (JFactory::getApplication()->isSite())
{
	JRequest::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}
$lang = JFactory::getLanguage();
$lang->load('com_menus');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
$function  = JRequest::getCmd('function', 'jsnGetMenuItems');
$user		= JFactory::getUser();
$app		 = JFactory::getApplication();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$JVersion = new JVersion;
$JVersion = $JVersion->getShortVersion();
?>
<div class="jsn-page-list">
	<div class="jsn-bootstrap">
		<form class="form-inline"
			action="<?php echo JRoute::_('index.php?option=com_imageshow&controller=menus&tmpl=component&function=' . $function . '&' . JSession::getFormToken() . '=1'); ?>"
			method="post" name="adminForm" id="adminForm">
			<div class="jsn-fieldset-filter">
				<fieldset>
					<div class="pull-left jsn-fieldset-search">
						<label class="control-label" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
						</label> <input type="text" name="<?php echo (version_compare($JVersion, '3.2', '>=') ? 'filter[search]' : 'filter_search');?>"
							class="input-xlarge" id="filter_search"
							value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
							title="" />
						<button class="btn" type="submit">
						<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
						</button>
						<button class="btn" type="button"
							onclick="document.id('filter_search').value='';this.form.submit();">
							<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
						</button>
					</div>
					<div class="pull-right jsn-fieldset-select">
						<select name="menutype" class="inputbox"
							onchange="this.form.submit()">
							<?php echo JHtml::_('select.options', JHtml::_('menu.menus'), 'value', 'text', $this->state->get('filter.menutype')); ?>
						</select>
					</div>
				</fieldset>
			</div>
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th class="title"><?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="center"><?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="center"><?php echo JText::_('JGRID_HEADING_MENU_ITEM_TYPE'); ?>
						</th>
						<th width="5%" class="center"><?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="center nowrap"><?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<?php // Grid layout    ?>
				<tbody>
				<?php
				$originalOrders = array ();
				foreach ($this->items as $i => $item) :

				$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
				$canChange  = $user->authorise('core.edit.state', 'com_menus') && $canCheckin;
				?>
					<tr
						onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('<?php echo $item->id; ?>', '<?php echo $item->title; ?>', null,'<?php echo $this->escape($item->link) . "&Itemid=" . $item->id; ?>');">
						<td><?php echo $this->escape($item->title); ?>
						</td>
						<td class="center"><?php echo $this->escape($item->access_level); ?>
						</td>
						<td class="nowrap"><span
							title="<?php echo isset($item->item_type_desc) ? htmlspecialchars($this->escape($item->item_type_desc), ENT_COMPAT, 'UTF-8') : ''; ?>">
							<?php echo $this->escape($item->item_type); ?> </span>
						</td>
						<td class="center"><?php if ($item->language == ''): ?> <?php echo JText::_('JDEFAULT'); ?>
						<?php elseif ($item->language == '*'): ?> <?php echo JText::alt('JALL', 'language'); ?>
						<?php else: ?> <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
						<?php endif; ?>
						</td>
						<td class="center nowrap"><span
							title="<?php echo sprintf('%d-%d', $item->lft, $item->rgt); ?>">
							<?php echo (int) $item->id; ?> </span>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<td class="jsn-pagination" colspan="15"><?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
			</table>
			<div>
				<input type="hidden" name="task" value="" /> <input type="hidden"
					name="boxchecked" value="0" /> <input type="hidden"
					name="filter_order" value="<?php echo $listOrder; ?>" /> <input
					type="hidden" name="filter_order_Dir"
					value="<?php echo $listDirn; ?>" /> <input type="hidden"
					name="original_order_values"
					value="<?php echo implode($originalOrders, ','); ?>" /> <input
					type="hidden" name="return"
					value="index.php?option=com_imageshow&controller=image&task=linkpopup&tmpl=component&tab=menu" />
					<?php echo JHtml::_('form.token'); ?>
			</div>
		</form>
	</div>
</div>
