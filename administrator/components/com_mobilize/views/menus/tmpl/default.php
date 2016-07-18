<?php
/**
 * @version    $Id$
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

if (JFactory::getApplication() -> isSite())
{
	JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}

// Load asset
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

if (JSNVersion::isJoomlaCompatible('3.0'))
{
	JHtml::_('behavior.tooltip');
}
else
{
	JHtml::_('behavior.framework');
}


$function = JFactory::getApplication() -> input -> getCmd('function', 'jQuery.jsnGetMenuItems');
$listOrder = $this -> escape($this -> state -> get('list.ordering'));
$listDirn = $this -> escape($this -> state -> get('list.direction'));
$actionForm = isset($_SERVER['QUERY_STRING']) ? 'index.php?' . $_SERVER['QUERY_STRING'] : '';
?>
<div class="jsn-page-list">
	<div class="jsn-bootstrap">
		<form class="form-inline form-menu" action="<?php echo JRoute::_($actionForm); ?>" method="post" name="adminForm" id="adminForm">
			<table class="table table-bordered table-striped table-popup">
				<thead>
				<tr>
					<th class="title">
						<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
					</th>
					<th width="5%">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
				</thead>
				<?php // Grid layout      ?>
				<tbody>
				<?php
				$originalOrders = array ();
				foreach ($this -> items as $i => $item) :

					?>
					<tr data-title="<?php echo $this -> escape($item -> title); ?>" data-id="<?php echo (int) $item -> id; ?>" class="jsnhover">
						<td>
							<?php echo $this -> escape($item -> title); ?>
						</td>
						<td>
							<?php echo $this -> escape($item -> access_level); ?>
						</td>

						<td>
							<select name="language" class="mobilize-language">
								<option value=''><?php echo JText::_('Chosen Language'); ?></option>
								<?php
								$options = JHtml::_('contentlanguage.existing');
								foreach($options as $option){
									$value = $option->value;
									$text = $option->text;
									echo $tmp ='<option value=' . $value . '>'. $text . '</option>';
								}
								?>
								<option value='all'><?php echo JText::_('All'); ?></option>
							</select>
						</td>
						<td class="nowrap">
								<span title="<?php echo sprintf('%d-%d', $item -> lft, $item -> rgt); ?>">
	<?php echo (int) $item -> id; ?></span>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot>
				<tr>
					<td class="jsn-pagination" colspan="15">
						<?php echo str_replace('Joomla.submitform();', 'document.adminForm.submit();', $this -> pagination -> getListFooter()); ?>
					</td>
				</tr>
				</tfoot>
			</table>
			<div>
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
				<input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</form>
	</div>
</div>
