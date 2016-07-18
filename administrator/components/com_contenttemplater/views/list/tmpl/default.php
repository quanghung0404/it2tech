<?php
/**
 * List View Template: Default
 *
 * @package         Content Templater
 * @version         5.1.6PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

JHtml::stylesheet('nnframework/style.min.css', false, true);
JHtml::stylesheet('contenttemplater/style.min.css', false, true);

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$ordering  = ($listOrder == 'a.ordering');

$editor = JFactory::getEditor();

$user       = JFactory::getUser();
$canCreate  = $user->authorise('core.create', 'com_contenttemplater');
$canEdit    = $user->authorise('core.edit', 'com_contenttemplater');
$canChange  = $user->authorise('core.edit.state', 'com_contenttemplater');
$canCheckin = $user->authorise('core.manage', 'com_checkin');
$saveOrder  = ($listOrder == 'a.ordering');
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_contenttemplater&task=list.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'itemList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$cols = 8;

// Version check
require_once JPATH_PLUGINS . '/system/nnframework/helpers/versions.php';
if ($this->config->show_update_notification)
{
	echo NoNumberVersions::render('CONTENT_TEMPLATER');
}
?>
	<form action="<?php echo JRoute::_('index.php?option=com_contenttemplater&view=list'); ?>" method="post" name="adminForm" id="adminForm">
		<?php
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>

		<table class="table table-striped" id="itemList">
			<thead>
			<tr>
				<th width="1%" class="nowrap center hidden-phone">
					<?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
				</th>
				<th width="1%" class="hidden-phone">
					<?php echo JHtml::_('grid.checkall'); ?>
				</th>
				<th width="1%" class="nowrap center">
					<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<th class="title">
					<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th class="title hidden-phone">
					<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="nowrap center hidden-phone">
					<?php echo JText::_('NN_FRONTEND'); ?>
				</th>
				<th width="5%" class="nowrap center hidden-phone">
					<?php echo JText::_('CT_AUTO_LOAD'); ?>
				</th>
				<th width="1%" class="nowrap center hidden-phone">
					<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
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
			<?php if (empty($this->list)): ?>
				<tr>
					<td colspan="<?php echo $cols; ?>">
						<?php echo JText::_('NN_NO_ITEMS_FOUND'); ?>
					</td>
				</tr>
			<?php else: ?>
				<?php foreach ($this->list as $i => $item) :
					$canCheckinItem = ($canCheckin || $item->checked_out == 0 || $item->checked_out == $user->get('id'));
					$canChangeItem  = ($canChange && $canCheckinItem);

					if ($item->button_enable_in_frontend)
					{
						$enable_in_frontend = '<span class="btn btn-micro disabled" rel="tooltip" title="' . JText::_('NN_ENABLE_IN_FRONTEND') . '"><span class="icon-publish"></span></a>';
					}
					else
					{
						$enable_in_frontend = '<span class="btn btn-micro disabled" rel="tooltip" title="' . JText::_('NN_NOT') . ' ' . JText::_('NN_ENABLE_IN_FRONTEND') . '"><span class="icon-cancel"></span></a>';
					}
					if ($item->load_enabled)
					{
						$is_default = '<span class="btn btn-micro disabled" rel="tooltip" title="' . JText::_('JDEFAULT') . '"><span class="icon-publish"></span></a>';
					}
					else
					{
						$is_default = '<span class="btn btn-micro disabled" rel="tooltip" title="' . JText::_('NN_NOT') . ' ' . JText::_('JDEFAULT') . '"><span class="icon-cancel"></span></a>';
					}
					?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="ct">
						<td class="order nowrap center hidden-phone">
							<?php if ($canChange) :
								$disableClassName = '';
								$disabledLabel    = '';
								if (!$saveOrder) :
									$disabledLabel    = JText::_('JORDERINGDISABLED');
									$disableClassName = 'inactive tip-top';
								endif; ?>
								<span class="sortable-handler <?php echo $disableClassName ?>" rel="tooltip" title="<?php echo $disabledLabel ?>">
									<span class="icon-menu"></span>
								</span>
								<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>"
								       class="width-20 text-area-order" />
							<?php else : ?>
								<span class="sortable-handler inactive">
									<span class="icon-menu"></span>
								</span>
							<?php endif; ?>
						</td>
						<td class="center hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center center">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'list.', $canChangeItem); ?>
						</td>
						<td>
							<?php if ($item->checked_out) : ?>
								<?php echo JHtml::_('jgrid.checkedout', $i, $editor, $item->checked_out_time, 'list.', $canCheckin); ?>
							<?php endif; ?>
							<?php if ($canEdit) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_contenttemplater&task=item.edit&id=' . $item->id); ?>">
									<?php echo $this->escape(str_replace(JUri::root(), '', $item->name)); ?></a>
							<?php else : ?>
								<?php echo $this->escape(str_replace(JUri::root(), '', $item->name)); ?>
							<?php endif; ?>
						</td>
						<td class="hidden-phone">
							<?php
							$description = explode('---', $item->description);
							$descr       = nl2br($this->escape(trim($description['0'])));
							if (isset($description['1']))
							{
								$descr = '<span rel="tooltip" title="' . makeTooltipSafe(trim($description['1'])) . '">' . $descr . '</span>';
							}
							echo $descr;
							?>
						</td>
						<td class="center hidden-phone">
							<?php echo $enable_in_frontend; ?>
						</td>
						<td class="center hidden-phone">
							<?php echo $is_default; ?>
						</td>
						<td class="center hidden-phone">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</form>

<?php

// Copyright
echo NoNumberVersions::getFooter('CONTENT_TEMPLATER', $this->config->show_copyright);

function makeTooltipSafe($str)
{
	return str_replace(
		array('"', '::', "&lt;", "\n"),
		array('&quot;', '&#58;&#58;', "&amp;lt;", '<br />'),
		htmlentities(trim($str), ENT_QUOTES, 'UTF-8')
	);
}
