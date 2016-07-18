<?php
/**
 * Main Component File
 * Used for the editor button (template xml)
 *
 * @package         Snippets
 * @version         4.1.4PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

$user = JFactory::getUser();
if ($user->get('guest')
	|| (
		!$user->authorise('core.edit', 'com_content')
		&& !$user->authorise('core.create', $asset)
	)
)
{
	JError::raiseError(403, JText::_("ALERTNOTAUTH"));
}

require_once JPATH_ADMINISTRATOR . '/components/com_snippets/helpers/helper.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
$parameters = NNParameters::getInstance();
$params     = $parameters->getComponentParams('snippets');

if (JFactory::getApplication()->isSite())
{
	if (!$params->enable_frontend)
	{
		JError::raiseError(403, JText::_("ALERTNOTAUTH"));
	}
}

$class = new PlgButtonSnippetsPopup;
$class->render($params);

class PlgButtonSnippetsPopup
{
	function render(&$params)
	{
		$app = JFactory::getApplication();

		// Initialize some variables
		$config = JComponentHelper::getParams('com_snippets');

		$filter_order     = $app->getUserStateFromRequest('filter_order', 'filter_order', 'ordering', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest('filter_order_Dir', 'filter_order_Dir', '', 'word');
		$filter_state     = $app->getUserStateFromRequest('filter_state', 'filter_state', $config->get('state', ''), 'word');
		$filter_search    = $app->getUserStateFromRequest('filter_search', 'filter_search', '', 'string');
		$filter_search    = JString::strtolower($filter_search);

		$limit      = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest('_limitstart', 'limitstart', 0, 'int');

		// lists
		$lists['order_Dir']     = $filter_order_Dir;
		$lists['order']         = $filter_order;
		$lists['filter_search'] = $filter_search;
		$lists['state']         = $filter_state;

		require_once JPATH_ADMINISTRATOR . '/components/com_snippets/models/list.php';
		$list = new SnippetsModelList;

		$list->setState('filter.state', $filter_state);
		$list->setState('filter.search', $filter_search);
		$list->setState('filter.limit', $limit);
		$list->setState('filter.limitstart', $limitstart);
		$list->setState('list.ordering', $filter_order);
		$list->setState('list.direction', $filter_order_Dir);

		$items = $list->getItems();

		$pageNav = $list->getPagination();

		$this->outputHTML($params, $items, $pageNav, $lists);
	}

	function outputHTML(&$params, &$rows, &$page, &$lists)
	{
		JHtml::_('behavior.tooltip');
		JHtml::_('formbehavior.chosen', 'select');

		$config = JComponentHelper::getParams('com_snippets');
		$tag    = $config->get('tag', 'snippet');
		// Tag character start and end
		list($tag_start, $tag_end) = explode('.', $config->get('tag_characters', '{.}'));

		// Load component language
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
		NNFrameworkFunctions::loadLanguage('plg_system_nnframework');
		NNFrameworkFunctions::loadLanguage('com_snippets');

		// Add scripts and styles
		$script = "
			function snippets_jInsertEditorText( id ) {
				f = document.getElementById( 'adminForm' );
				str = '" . $tag_start . $tag . " ' + id + '" . $tag_end . "';
				window.parent.jInsertEditorText( str, '" . JFactory::getApplication()->input->getString('name', 'text') . "' );
				window.parent.SqueezeBox.close();
			}
		";
		JFactory::getDocument()->addScriptDeclaration($script);
		?>
		<div class="header">
			<h1 CLASS="page-title">
				<span class="icon-nonumber icon-snippets"></span>
				<?php echo JText::_('INSERT_SNIPPET'); ?>
			</h1>
		</div>

		<div style="margin-bottom: 20px"></div>

		<div class="container-fluid container-main">
			<form action="" method="post" name="adminForm" id="adminForm">
				<div class="well well-small">
					<?php echo html_entity_decode(JText::_('SNP_CLICK_ON_ONE_OF_THE_SNIPPETS'), ENT_COMPAT, 'UTF-8'); ?>
				</div>

				<div style="clear:both;"></div>

				<div id="filter-bar" class="btn-toolbar">
					<div class="filter-search btn-group pull-left">
						<label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER'); ?></label>
						<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>"
						       value="<?php echo $lists['filter_search']; ?>" title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
					</div>
					<div class="btn-group pull-left hidden-phone">
						<button class="btn" type="submit" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
							<span class="icon-search"></span></button>
						<button class="btn" type="button" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"
						        onclick="document.id('filter_search').value='';this.form.submit();">
							<span class="icon-remove"></span></button>
					</div>

					<div class="btn-group pull-right hidden-phone">
						<?php echo $lists['state']; ?>
					</div>
				</div>

				<table class="table table-striped">
					<thead>
					<tr>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', 'JSTATUS', 'published', @$lists['order_Dir'], @$lists['order']); ?>
						</th>
						<th width="15%" class="title">
							<?php echo JHtml::_('grid.sort', 'SNP_SNIPPET_ID', 'alias', $lists['order_Dir'], $lists['order']); ?>
						</th>
						<th class="title">
							<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'name', $lists['order_Dir'], $lists['order']); ?>
						</th>
						<th class="title">
							<?php echo JHtml::_('grid.sort', 'JGLOBAL_DESCRIPTION', 'description', $lists['order_Dir'], $lists['order']); ?>
						</th>
						<th width="5%" class="nowrap center">
							<?php echo JText::_('NN_FRONTEND'); ?>
						</th>
						<th width="5%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $lists['order_Dir'], $lists['order']); ?>
						</th>
					</tr>
					</thead>
					<tfoot>
					<tr>
						<td colspan="7">
							<?php
							$url        = 'index.php?nn_qp=1&folder=plugins.editors-xtd.snippets&file=snippets.inc.php&name=' . JFactory::getApplication()->input->get('name', 'text');
							$pagination = $page->getListFooter();
							$pagination = preg_replace('#index\.php[^"\?]*\?([^"]*)#i', $url . '&\1', $pagination);
							echo $pagination;
							?>
						</td>
					</tr>
					</tfoot>

					<tbody>
					<?php
					$k = 0;
					for ($i = 0, $n = count($rows); $i < $n; $i++)
					{
						$item =& $rows[$i];

						if (!$item->button_enable || ($item->button_enable == 2 && JFactory::getApplication()->isSite()))
						{
							continue;
						}
						if ($item->button_enable == 1)
						{
							$enable_in_frontend = '<span class="btn btn-micro disabled" rel="tooltip" title="' . JText::_('NN_ENABLE_IN_FRONTEND') . '"><span class="icon-publish"></span></a>';
						}
						else
						{
							$enable_in_frontend = '<span class="btn btn-micro disabled" rel="tooltip" title="' . JText::_('NN_NOT') . ' ' . JText::_('NN_ENABLE_IN_FRONTEND') . '"><span class="icon-cancel"></span></a>';
						}
						?>
						<tr class="<?php echo "row$k"; ?>">
							<td class="center">
								<?php echo JHtml::_('jgrid.published', $item->published, $i, 'list.', 0); ?>
							</td>
							<td>
								<?php echo '<label class="hasTip" title="{' . htmlspecialchars($tag) . ' ' . htmlspecialchars($item->alias) . '}"><a href="javascript:;" onclick="snippets_jInsertEditorText( \'' . addslashes(htmlspecialchars($item->alias)) . '\' );return false;">' . htmlspecialchars($item->alias) . '</a></label>'; ?>
							</td>
							<td>
								<?php echo htmlspecialchars(str_replace(JUri::root(), '', $item->name)); ?>
							</td>
							<td>
								<?php
								$description = explode('---', $item->description);
								$descr       = trim($description['0']);
								if (isset($description['1']))
								{
									$descr = '<span class="hasTip" title="' . makeTooltipSafe(trim($descr) . '::' . trim($description['1'])) . '">' . ($descr) . '</span>';
								}
								echo $descr;
								?>
							</td>
							<td class="center">
								<?php echo $enable_in_frontend; ?>
							</td>
							<td class="center">
								<?php echo $item->id; ?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					}
					?>
					</tbody>
				</table>
				<input type="hidden" name="name" value="<?php echo JFactory::getApplication()->input->getString('name', 'text'); ?>" />
				<input type="hidden" name="client" value="<?php echo JFactory::getApplication()->getClientId(); ?>" />
				<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
			</form>
		</div>
		<?php
	}
}

function makeTooltipSafe($str)
{
	return str_replace(
		array('"', '::', "&lt;", "\n"),
		array('&quot;', '&#58;&#58;', "&amp;lt;", '<br />'),
		htmlentities(trim($str), ENT_QUOTES, 'UTF-8')
	);
}
