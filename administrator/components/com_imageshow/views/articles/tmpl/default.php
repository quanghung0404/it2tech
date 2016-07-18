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
$lang->load('com_content');
require_once JPATH_ROOT . '/components/com_content/helpers/route.php';
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
$function  = JRequest::getCmd('function', 'jSelectArticle');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$JVersion = new JVersion;
$JVersion = $JVersion->getShortVersion();
?>
<div class="jsn-page-list">
	<div class="jsn-bootstrap">
		<form class="form-inline"
			action="<?php echo JRoute::_('index.php?option=com_imageshow&controller=articles&tmpl=component&function=' . $function . '&' . JSession::getFormToken() . '=1'); ?>"
			method="post" name="adminForm" id="adminForm">
			<div class="jsn-fieldset-filter">
				<fieldset>
					<div class="pull-left jsn-fieldset-search">
						<label class="control-label" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
						</label> <input type="text" name="<?php echo (version_compare($JVersion, '3.2', '>=') ? 'filter[search]' : 'filter_search');?>"
							class="input-xlarge" id="filter_search"
							value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
							size="30"
							title="<?php echo JText::_('Search title or alias. Prefix with ID: to search for an article ID.'); ?>" />
						<button class="btn" type="submit">
						<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
						</button>
						<button class="btn" type="button"
							onclick="document.id('filter_search').value='';this.form.submit();">
							<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
						</button>
					</div>
					<div class="pull-right jsn-fieldset-select">
						<select name="<?php echo (version_compare($JVersion, '3.2', '>=') ? 'filter[category_id]' : 'filter_category_id');?>" class="inputbox"
							onchange="this.form.submit()">
							<option value="">
							<?php echo JText::_('JOPTION_SELECT_CATEGORY'); ?>
							</option>
							<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_content'), 'value', 'text', $this->state->get('filter.category_id')); ?>
						</select>
					</div>
					<div class="clearbreak"></div>
				</fieldset>
			</div>
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th class="title"><?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th class="center" width="15%"><?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
						</th>
						<th class="center" width="15%"><?php echo JHtml::_('grid.sort', 'JCATEGORY', 'a.catid', $listDirn, $listOrder); ?>
						</th>
						<th class="center" width="5%"><?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
						</th>
						<th class="center" width="5%"><?php echo JHtml::_('grid.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="center nowrap"><?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
					<tr
						onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>', '<?php echo $this->escape($item->catid); ?>', null, '<?php echo $this->escape(ContentHelperRoute::getArticleRoute($item->id)); ?>');">
						<td><?php echo $this->escape($item->title); ?>
						</td>
						<td class="center"><?php echo $this->escape($item->access_level); ?>
						</td>
						<td class="center"><?php echo $this->escape($item->category_title); ?>
						</td>
						<td class="center"><?php if ($item->language == '*'): ?> <?php echo JText::alt('JALL', 'language'); ?>
						<?php else: ?> <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
						<?php endif; ?>
						</td>
						<td class="center nowrap"><?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
						</td>
						<td class="center"><?php echo (int) $item->id; ?>
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
					name="return"
					value="index.php?option=com_imageshow&controller=image&task=linkpopup&tmpl=component&tab=article" />
					<?php echo JHtml::_('form.token'); ?>
			</div>
		</form>
	</div>
</div>
