<?php
/**
 * Popup page
 * Displays a list with modules
 *
 * @package         Articles Anywhere
 * @version         4.1.5PRO
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
		&& !$user->authorise('core.create', 'com_content')
	)
)
{
	JError::raiseError(403, JText::_("ALERTNOTAUTH"));
}

require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
$parameters = NNParameters::getInstance();
$params     = $parameters->getPluginParams('articlesanywhere');

if (JFactory::getApplication()->isSite())
{
	if (!$params->enable_frontend)
	{
		JError::raiseError(403, JText::_("ALERTNOTAUTH"));
	}
}

$class = new PlgButtonArticlesAnywherePopup;
$class->render($params);

class PlgButtonArticlesAnywherePopup
{
	function render(&$params)
	{
		$app = JFactory::getApplication();

		// load the admin language file
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
		NNFrameworkFunctions::loadLanguage('plg_system_nnframework');
		NNFrameworkFunctions::loadLanguage('plg_editors-xtd_articlesanywhere');
		NNFrameworkFunctions::loadLanguage('plg_system_articlesanywhere');
		NNFrameworkFunctions::loadLanguage('com_content', JPATH_ADMINISTRATOR);

		JHtml::stylesheet('nnframework/style.min.css', false, true);

		require_once JPATH_ADMINISTRATOR . '/components/com_content/helpers/content.php';

		$content_type = JFactory::getApplication()->input->get('content_type', $params->content_type);
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
		$k2 = NNFrameworkFunctions::extensionInstalled('k2');

		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$filter = null;

		// Get some variables from the request
		$option           = 'articlesanywhere';
		$filter_order     = $app->getUserStateFromRequest($option . '_filter_order', 'filter_order', 'ordering', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest($option . '_filter_order_Dir', 'filter_order_Dir', '', 'word');
		$filter_featured  = $app->getUserStateFromRequest($option . '_filter_featured', 'filter_featured', '', 'int');
		$filter_category  = $app->getUserStateFromRequest($option . '_filter_category', 'filter_category', 0, 'int');
		$filter_author    = $app->getUserStateFromRequest($option . '_filter_author', 'filter_author', 0, 'int');
		$filter_state     = $app->getUserStateFromRequest($option . '_filter_state', 'filter_state', '', 'word');
		$filter_search    = $app->getUserStateFromRequest($option . '_filter_search', 'filter_search', '', 'string');
		$filter_search    = JString::strtolower($filter_search);

		$limit      = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest($option . '_limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$lists = array();

		// filter_search filter
		$lists['filter_search'] = $filter_search;

		// table ordering
		if ($k2 && $content_type == 'k2')
		{
			if ($filter_order == 'section' || $filter_order == 'frontpage')
			{
				$filter_order     = 'ordering';
				$filter_order_Dir = '';
			}
		}
		else
		{
			if ($filter_order == 'featured')
			{
				$filter_order     = 'ordering';
				$filter_order_Dir = '';
			}
		}

		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order']     = $filter_order;

		if ($k2 && $content_type == 'k2')
		{
			// load the k2 language file
			NNFrameworkFunctions::loadLanguage('com_k2', JPATH_ADMINISTRATOR);

			define('JPATH_COMPONENT', JPATH_ADMINISTRATOR . '/components/com_k2');
			define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_COMPONENT);

			JLoader::register('K2Controller', JPATH_COMPONENT . '/controllers/controller.php');
			JLoader::register('K2View', JPATH_COMPONENT . '/views/view.php');
			JLoader::register('K2Model', JPATH_COMPONENT . '/models/model.php');

			/* FILTERS */
			// featured filter
			$filter_featured_options[] = JHtml::_('select.option', -1, JText::_('- Select featured state -'));
			$filter_featured_options[] = JHtml::_('select.option', 1, JText::_('COM_CONTENT_FEATURED'));
			$filter_featured_options[] = JHtml::_('select.option', 0, JText::_('COM_CONTENT_UNFEATURED'));
			$lists['featured']         = JHtml::_('select.genericlist', $filter_featured_options, 'filter_featured', 'onchange="this.form.submit();"', 'value', 'text', $filter_featured);

			// get list of categories for dropdown filter
			require_once JPATH_COMPONENT . '/models/categories.php';
			$categoriesModel     = K2Model::getInstance('Categories', 'K2Model');
			$categories_option[] = JHtml::_('select.option', 0, JText::_('JOPTION_SELECT_CATEGORY'));
			$categories          = $categoriesModel->categoriesTree();
			$categories_options  = @array_merge($categories_option, $categories);
			$lists['categories'] = JHtml::_('select.genericlist', $categories_options, 'filter_category', 'onchange="this.form.submit();"', 'value', 'text', $filter_category);

			// get list of Authors for dropdown filter
			$query->clear()
				->select('c.created_by, u.name')
				->from('#__k2_items AS c')
				->join('LEFT', '#__users AS u ON u.id = c.created_by')
				->where('c.published != -1')
				->where('c.published != -2')
				->where('c.trash = 0')
				->group('u.id')
				->order('c.id DESC');
			$db->setQuery($query);
			$authors = $db->loadObjectList();
			array_unshift($authors, JHtml::_('select.option', '0', JText::_('JOPTION_SELECT_AUTHOR'), 'created_by', 'name'));
			$lists['authors'] = JHtml::_('select.genericlist', $authors, 'filter_author', 'class="inputbox" size="1" onchange="this.form.submit( );"', 'created_by', 'name', $filter_author);

			// state filter
			$filter_state_options[] = JHtml::_('select.option', -1, JText::_('JOPTION_SELECT_ACCESS'));
			$filter_state_options[] = JHtml::_('select.option', 1, JText::_('JPUBLISHED'));
			$filter_state_options[] = JHtml::_('select.option', 0, JText::_('JUNPUBLISHED'));
			$lists['state']         = JHtml::_('select.genericlist', $filter_state_options, 'filter_state', 'onchange="this.form.submit();"', 'value', 'text', $filter_state);

			/* ITEMS */
			$where   = array();
			$where[] = 'c.published != -2 AND c.trash = 0';

			if ($filter_search)
			{
				if (stripos($filter_search, 'id:') === 0)
				{
					$where[] = 'c.id = ' . (int) substr($filter_search, 3);
				}
				else
				{
					$cols = array('id', 'title', 'introtext', 'fulltext');
					$w    = array();
					foreach ($cols as $col)
					{
						$w[] = 'LOWER(c.' . $col . ') LIKE ' . $db->quote('%' . $db->escape($filter_search, true) . '%', false);
					}
					$where[] = '(' . implode(' OR ', $w) . ')';
				}
			}

			if ($filter_state && $filter_state > -1)
			{
				$where[] = 'c.published = ' . (int) $filter_state;
			}

			if ($filter_featured && $filter_featured > -1)
			{
				$where[] = 'c.featured = ' . (int) $filter_featured;
			}

			if ($filter_category && $filter_category > 0)
			{
				require_once JPATH_SITE . '/components/com_k2/models/itemlist.php';
				$model        = K2Model::getInstance('Itemlist', 'K2Model');
				$categories   = $model->getCategoryChildren($filter_category);
				$categories[] = $filter_category;
				$categories   = @array_unique($categories);
				$sql          = @implode(',', $categories);
				$where[]      = 'c.catid IN (' . $sql . ')';
			}

			if ($filter_author && $filter_author > 0)
			{
				$where[] = 'c.created_by=' . (int) $filter_author;
			}

			// Build the where clause of the content record query
			$where = implode(' AND ', $where);

			// Get the total number of records
			$query->clear()
				->select('COUNT(*)')
				->from('#__k2_items AS c')
				->join('LEFT', '#__k2_categories AS cc ON cc.id = c.catid')
				->where($where);
			$db->setQuery($query);
			$total = $db->loadResult();

			// Create the pagination object
			jimport('joomla.html.pagination');
			$page = new JPagination($total, $limitstart, $limit);

			if ($filter_order == 'ordering')
			{
				$order = 'category, ordering ' . $filter_order_Dir;
			}
			else
			{
				$order = $filter_order . ' ' . $filter_order_Dir . ', category, ordering';
			}

			$query->clear()
				->select('c.*, g.title AS accesslevel, cc.name AS category, v.name AS author')
				->select('w.name AS moderator, u.name AS editor')
				->from('#__k2_items AS c')
				->join('LEFT', '#__k2_categories AS cc ON cc.id = c.catid')
				->join('LEFT', '#__viewlevels AS g ON g.id = c.access')
				->join('LEFT', '#__users AS u ON u.id = c.checked_out')
				->join('LEFT', '#__users AS v ON v.id = c.created_by')
				->join('LEFT', '#__users AS w ON w.id = c.modified_by')
				->where($where)
				->order($order);

			$db->setQuery($query, $page->limitstart, $page->limit);
			$rows = $db->loadObjectList();

			// If there is a database query error, throw a HTTP 500 and exit
			if ($db->getErrorNum())
			{
				JError::raiseError(500, $db->stderr());

				return false;
			}
		}
		else
		{
			$options = JHtml::_('category.options', 'com_content');
			array_unshift($options, JHtml::_('select.option', '0', JText::_('JOPTION_SELECT_CATEGORY')));
			$lists['categories'] = JHtml::_('select.genericlist', $options, 'filter_category', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_category);
			//$lists['categories'] = JHtml::_( 'select.genericlist',  $categories, 'filter_category', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_category );

			// get list of Authors for dropdown filter
			$query->clear()
				->select('c.created_by, u.name')
				->from('#__content AS c')
				->join('LEFT', '#__users AS u ON u.id = c.created_by')
				->where('c.state != -1')
				->where('c.state != -2')
				->group('u.id')
				->order('u.id DESC');
			$db->setQuery($query);
			$options = $db->loadObjectList();
			array_unshift($options, JHtml::_('select.option', '0', JText::_('JOPTION_SELECT_AUTHOR'), 'created_by', 'name'));
			$lists['authors'] = JHtml::_('select.genericlist', $options, 'filter_author', 'class="inputbox" size="1" onchange="this.form.submit( );"', 'created_by', 'name', $filter_author);

			// state filter
			$lists['state'] = JHtml::_('grid.state', $filter_state, 'JPUBLISHED', 'JUNPUBLISHED', 'JARCHIVED');

			/* ITEMS */
			$where   = array();
			$where[] = 'c.state != -2';

			/*
			 * Add the filter specific information to the where clause
			 */
			// Category filter
			if ($filter_category > 0)
			{
				$where[] = 'c.catid = ' . (int) $filter_category;
			}
			// Author filter
			if ($filter_author > 0)
			{
				$where[] = 'c.created_by = ' . (int) $filter_author;
			}
			// Content state filter
			if ($filter_state)
			{
				if ($filter_state == 'P')
				{
					$where[] = 'c.state = 1';
				}
				else
				{
					if ($filter_state == 'U')
					{
						$where[] = 'c.state = 0';
					}
					else if ($filter_state == 'A')
					{
						$where[] = 'c.state = -1';
					}
					else
					{
						$where[] = 'c.state != -2';
					}
				}
			}
			// Keyword filter
			if ($filter_search)
			{
				if (stripos($filter_search, 'id:') === 0)
				{
					$where[] = 'c.id = ' . (int) substr($filter_search, 3);
				}
				else
				{
					$cols = array('id', 'title', 'introtext', 'fulltext');
					$w    = array();
					foreach ($cols as $col)
					{
						$w[] = 'LOWER(c.' . $col . ') LIKE ' . $db->quote('%' . $db->escape($filter_search, true) . '%', false);
					}
					$where[] = '(' . implode(' OR ', $w) . ')';
				}
			}

			// Build the where clause of the content record query
			$where = implode(' AND ', $where);

			// Get the total number of records
			$query->clear()
				->select('COUNT(*)')
				->from('#__content AS c')
				->join('LEFT', '#__categories AS cc ON cc.id = c.catid')
				->where($where);
			$db->setQuery($query);
			$total = $db->loadResult();

			// Create the pagination object
			jimport('joomla.html.pagination');
			$page = new JPagination($total, $limitstart, $limit);

			if ($filter_order == 'ordering')
			{
				$order = 'category, ordering ' . $filter_order_Dir;
			}
			else
			{
				$order = $filter_order . ' ' . $filter_order_Dir . ', category, ordering';
			}

			// Get the articles
			$query->clear()
				->select('c.*, c.state as published, g.title AS accesslevel, cc.title AS category')
				->select('u.name AS editor, f.content_id AS frontpage, v.name AS author')
				->from('#__content AS c')
				->join('LEFT', '#__categories AS cc ON cc.id = c.catid')
				->join('LEFT', '#__viewlevels AS g ON g.id = c.access')
				->join('LEFT', '#__users AS u ON u.id = c.checked_out')
				->join('LEFT', '#__users AS v ON v.id = c.created_by')
				->join('LEFT', '#__content_frontpage AS f ON f.content_id = c.id')
				->where($where)
				->order($order);
			$db->setQuery($query, $page->limitstart, $page->limit);
			$rows = $db->loadObjectList();

			// If there is a database query error, throw a HTTP 500 and exit
			if ($db->getErrorNum())
			{
				JError::raiseError(500, $db->stderr());

				return false;
			}
		}

		$this->outputHTML($params, $rows, $page, $lists, $k2);
	}

	function outputHTML(&$params, &$rows, &$page, &$lists, $k2 = 0)
	{
		JHtml::_('behavior.tooltip');
		JHtml::_('formbehavior.chosen', 'select');

		$plugin_tag = explode(',', $params->article_tag);
		$plugin_tag = trim($plugin_tag['0']);

		$content_type = 'core';
		$content_type = JFactory::getApplication()->input->get('content_type', $params->content_type);

		if (!empty($_POST))
		{
			foreach ($params as $key => $val)
			{
				if (array_key_exists($key, $_POST))
				{
					$params->$key = $_POST[$key];
				}
			}
		}
		?>
		<div class="header">
			<h1 class="page-title">
				<span class="icon-nonumber icon-articlesanywhere"></span>
				<?php echo JText::_('INSERT_ARTICLE'); ?>
			</h1>
		</div>

		<?php if (JFactory::getApplication()->isAdmin() && JFactory::getUser()->authorise('core.admin', 1)) : ?>
		<div class="subhead">
			<div class="container-fluid">
				<div class="btn-toolbar" id="toolbar">
					<div class="btn-wrapper" id="toolbar-options">
						<button
							onclick="window.open('index.php?option=com_plugins&filter_folder=system&filter_search=articles anywhere');"
							class="btn btn-small">
							<span class="icon-options"></span> <?php echo JText::_('JOPTIONS') ?>
						</button>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

		<div style="margin-bottom: 20px"></div>

		<div class="container-fluid container-main">
			<form action="" method="post" name="adminForm" id="adminForm">
				<div class="alert alert-info">
					<?php echo html_entity_decode(JText::_('AA_CLICK_ON_ONE_OF_THE_ARTICLE_LINKS'), ENT_COMPAT, 'UTF-8'); ?>
				</div>

				<div class="row-fluid form-vertical">
					<div class="span3 well well">
						<div class="control-group">
							<label id="data_title_enable-lbl" for="data_title_enable" class="control-label"
							       rel="tooltip" title="<?php echo JText::_('AA_TITLE_TAG_DESC'); ?>">
								<?php echo JText::_('JGLOBAL_TITLE'); ?>
							</label>

							<div class="controls">
								<fieldset id="data_title_enable" class="radio btn-group">
									<input type="radio" id="data_title_enable0" name="data_title_enable"
									       value="0" <?php echo !$params->data_title_enable ? 'checked="checked"' : ''; ?> />
									<label for="data_title_enable0"><?php echo JText::_('JNO'); ?></label>
									<input type="radio" id="data_title_enable1" name="data_title_enable"
									       value="1" <?php echo $params->data_title_enable ? 'checked="checked"' : ''; ?> />
									<label for="data_title_enable1"><?php echo JText::_('JYES'); ?></label>
								</fieldset>
							</div>
						</div>
					</div>

					<div class="span3 well">
						<div class="control-group">
							<label id="data_text_enable-lbl" for="data_text_enable" class="control-label" rel="tooltip"
							       title="<?php echo JText::_('AA_TEXT_TAG_DESC'); ?>">
								<?php echo JText::_('NN_CONTENT'); ?>
							</label>

							<div class="controls">
								<fieldset id="data_text_enable" class="radio btn-group">
									<input type="radio" id="data_text_enable0" name="data_text_enable"
									       value="0" <?php echo !$params->data_text_enable ? 'checked="checked"' : ''; ?>
									       onclick="toggleDivs();" onchange="toggleDivs();" />
									<label for="data_text_enable0"><?php echo JText::_('JNO'); ?></label>
									<input type="radio" id="data_text_enable1" name="data_text_enable"
									       value="1" <?php echo $params->data_text_enable ? 'checked="checked"' : ''; ?>
									       onclick="toggleDivs();" onchange="toggleDivs();" />
									<label for="data_text_enable1"><?php echo JText::_('JYES'); ?></label>
								</fieldset>
							</div>
						</div>

						<div rel="data_text_enable" class="toggle_div" style="display:none;">
							<div class="control-group">
								<label id="data_text_type-lbl" for="data_text_type" class="control-label" rel="tooltip"
								       title="<?php echo JText::_('AA_TEXT_TYPE_DESC'); ?>">
									<?php echo JText::_('AA_TEXT_TYPE'); ?>
								</label>

								<div class="controls">
									<select name="data_text_type">
										<option
											value="text"<?php echo $params->data_text_type == 'text' ? 'selected="selected"' : ''; ?>>
											<?php echo JText::_('AA_ALL_TEXT'); ?>
										</option>
										<option
											value="introtext"<?php echo $params->data_text_type == 'introtext' ? 'selected="selected"' : ''; ?>>
											<?php echo JText::_('AA_INTRO_TEXT'); ?>
										</option>
										<option
											value="fulltext"<?php echo $params->data_text_type == 'fulltext' ? 'selected="selected"' : ''; ?>>
											<?php echo JText::_('AA_FULL_TEXT'); ?>
										</option>
									</select>
								</div>
							</div>
							<div class="control-group">
								<label id="data_text_length-lbl" for="data_text_length" class="control-label"
								       rel="tooltip" title="<?php echo JText::_('AA_MAXIMUM_TEXT_LENGTH_DESC'); ?>">
									<?php echo JText::_('AA_MAXIMUM_TEXT_LENGTH'); ?>
								</label>

								<div class="controls">
									<input type="text" name="data_text_length" id="data_text_length"
									       value="<?php echo $params->data_text_length; ?>" size="4"
									       style="width:50px;text-align: right;" />
								</div>
							</div>
							<div class="control-group">
								<label id="data_text_strip-lbl" for="data_text_strip" class="control-label"
								       rel="tooltip" title="<?php echo JText::_('AA_STRIP_HTML_TAGS_DESC'); ?>">
									<?php echo JText::_('AA_STRIP_HTML_TAGS'); ?>
								</label>

								<div class="controls">
									<fieldset id="data_text_strip" class="radio btn-group">
										<input type="radio" id="data_text_strip0" name="data_text_strip"
										       value="0" <?php echo !$params->data_text_strip ? 'checked="checked"' : ''; ?> />
										<label for="data_text_strip0"><?php echo JText::_('JNO'); ?></label>
										<input type="radio" id="data_text_strip1" name="data_text_strip"
										       value="1" <?php echo $params->data_text_strip ? 'checked="checked"' : ''; ?> />
										<label for="data_text_strip1"><?php echo JText::_('JYES'); ?></label>
									</fieldset>
								</div>
							</div>
						</div>
					</div>

					<div class="span3 well">
						<div class="control-group">
							<label id="data_readmore_enable-lbl" for="data_readmore_enable" class="control-label"
							       rel="tooltip" title="<?php echo JText::_('AA_READMORE_TAG_DESC'); ?>">
								<?php echo JText::_('AA_READMORE_LINK'); ?>
							</label>

							<div class="controls">
								<fieldset id="data_readmore_enable" class="radio btn-group">
									<input type="radio" id="data_readmore_enable0" name="data_readmore_enable"
									       value="0" <?php echo !$params->data_readmore_enable ? 'checked="checked"' : ''; ?> />
									<label for="data_readmore_enable0"><?php echo JText::_('JNO'); ?></label>
									<input type="radio" id="data_readmore_enable1" name="data_readmore_enable"
									       value="1" <?php echo $params->data_readmore_enable ? 'checked="checked"' : ''; ?> />
									<label for="data_readmore_enable1"><?php echo JText::_('JYES'); ?></label>
								</fieldset>
							</div>
						</div>

						<div rel="data_readmore_enable" class="toggle_div" style="display:none;">
							<div class="control-group">
								<label id="data_readmore_text-lbl" for="data_readmore_text" class="control-label"
								       rel="tooltip" title="<?php echo JText::_('AA_READMORE_TEXT_DESC'); ?>">
									<?php echo JText::_('AA_READMORE_TEXT'); ?>
								</label>

								<div class="controls">
									<input type="text" name="data_readmore_text" id="data_readmore_text"
									       value="<?php echo $params->data_readmore_text; ?>" />
								</div>
							</div>
							<div class="control-group">
								<label id="data_readmore_class-lbl" for="data_readmore_class" class="control-label"
								       rel="tooltip" title="<?php echo JText::_('AA_CLASSNAME_DESC'); ?>">
									<?php echo JText::_('AA_CLASSNAME'); ?>
								</label>

								<div class="controls">
									<input type="text" name="data_readmore_class" id="data_readmore_class"
									       value="<?php echo $params->data_readmore_class; ?>" />
								</div>
							</div>
						</div>
					</div>

					<div class="span3 well">
						<div class="control-group">
							<label id="enable_div-lbl" for="enable_div-field" class="control-label" rel="tooltip"
							       title="<?php echo JText::_('AA_EMBED_IN_A_DIV_DESC'); ?>">
								<?php echo JText::_('AA_EMBED_IN_A_DIV'); ?>
							</label>

							<div class="controls">
								<fieldset id="enable_div" class="radio btn-group">
									<input type="radio" id="enable_div0" name="enable_div"
									       value="0" <?php echo !$params->div_enable ? 'checked="checked"' : ''; ?>
									       onclick="toggleDivs();" onchange="toggleDivs();" />
									<label for="enable_div0"><?php echo JText::_('JNO'); ?></label>
									<input type="radio" id="enable_div1" name="enable_div"
									       value="1" <?php echo $params->div_enable ? 'checked="checked"' : ''; ?>
									       onclick="toggleDivs();" onchange="toggleDivs();" />
									<label for="enable_div1"><?php echo JText::_('JYES'); ?></label>
								</fieldset>
							</div>
						</div>
						<div rel="enable_div" class="toggle_div" style="display:none;">
							<div class="control-group">
								<label id="div_width-lbl" for="div_width" class="control-label" rel="tooltip"
								       title="<?php echo JText::_('AA_WIDTH_DESC'); ?>">
									<?php echo JText::_('NN_WIDTH'); ?>
								</label>

								<div class="controls">
									<input type="text" class="text_area" name="div_width" id="div_width"
									       value="<?php echo $params->div_width; ?>" size="4"
									       style="width:50px;text-align: right;" />
								</div>
							</div>
							<div class="control-group">
								<label id="div_height-lbl" for="div_height" class="control-label" rel="tooltip"
								       title="<?php echo JText::_('AA_HEIGHT_DESC'); ?>">
									<?php echo JText::_('NN_HEIGHT'); ?>
								</label>

								<div class="controls">
									<input type="text" class="text_area" name="div_height" id="div_height"
									       value="<?php echo $params->div_height; ?>" size="4"
									       style="width:50px;text-align: right;" />
								</div>
							</div>
							<div class="control-group">
								<label id="div_float-lbl" for="div_float" class="control-label" rel="tooltip"
								       title="<?php echo JText::_('AA_ALIGNMENT_DESC'); ?>">
									<?php echo JText::_('AA_ALIGNMENT'); ?>
								</label>

								<div class="controls">
									<fieldset id="div_float" class="radio btn-group">
										<input type="radio" id="div_float0" name="div_float"
										       value="0" <?php echo !$params->div_float ? 'checked="checked"' : ''; ?> />
										<label for="div_float0"><?php echo JText::_('JNONE'); ?></label>
										<input type="radio" id="div_float1" name="div_float"
										       value="left" <?php echo $params->div_float == 'left' ? 'checked="checked"' : ''; ?> />
										<label for="div_float1"><?php echo JText::_('JGLOBAL_LEFT'); ?></label>
										<input type="radio" id="div_float2" name="div_float"
										       value="right" <?php echo $params->div_float == 'right' ? 'checked="checked"' : ''; ?> />
										<label for="div_float2"><?php echo JText::_('JGLOBAL_RIGHT'); ?></label>
									</fieldset>
								</div>
							</div>
							<div class="control-group">
								<label id="text_area-lbl" for="text_area" class="control-label" rel="tooltip"
								       title="<?php echo JText::_('AA_DIV_CLASSNAME_DESC'); ?>">
									<?php echo JText::_('AA_DIV_CLASSNAME'); ?>
								</label>

								<div class="controls">
									<input type="text" class="text_area" name="div_class" id="div_class"
									       value="<?php echo $params->div_class; ?>" />
								</div>
							</div>
						</div>
					</div>
				</div>

				<div style="clear:both;"></div>

				<?php if ($k2) : ?>
					<div class="form-horizontal well">
						<div class="control-group" style="margin-bottom: 0;">
							<label id="jform_content_type-lbl" for="jform_content_type" class="hasTip control-label"
							       title="<?php echo JText::_('AA_CONTENT_TYPE_DESC'); ?>"><?php echo JText::_('AA_CONTENT_TYPE'); ?></label>

							<div class="controls">
								<select id="content_type" name="content_type" onchange="form.submit()">
									<option
										value="core" <?php echo ($content_type == 'core') ? 'selected="selected"' : ''; ?>><?php echo JText::_('AA_CONTENT_TYPE_CORE'); ?></option>
									<option
										value="k2" <?php echo ($content_type == 'k2') ? 'selected="selected"' : ''; ?>><?php echo JText::_('AA_CONTENT_TYPE_K2'); ?></option>
								</select>
							</div>
						</div>
					</div>
				<?php endif; ?>
				<?php
				if ($k2 && $content_type == 'k2')
				{
					$this->outputTableK2($rows, $page, $lists);
				}
				else
				{
					$this->outputTableCore($rows, $page, $lists);
				}
				?>

				<input type="hidden" name="name"
				       value="<?php echo JFactory::getApplication()->input->getString('name', 'text'); ?>" />
				<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
			</form>
		</div>

		<?php
		// Tag character start and end
		list($tag_start, $tag_end) = explode('.', $params->tag_characters);
		// Data tag character start and end
		list($tag_data_start, $tag_data_end) = explode('.', $params->tag_characters_data);
		?>
		<script type="text/javascript">
			function articlesanywhere_jInsertEditorText(id) {
				(function($) {
					var t_start = '<?php echo addslashes($tag_start); ?>';
					var t_end = '<?php echo addslashes($tag_end); ?>';
					var td_start = '<?php echo addslashes($tag_data_start); ?>';
					var td_end = '<?php echo addslashes($tag_data_end); ?>';

					var str = '';

					if ($('input[name="data_title_enable"]:checked').val() == 1) {
						str += ' ' + td_start + 'title' + td_end;
					}

					if ($('input[name="data_text_enable"]:checked').val() == 1) {
						var tag = $('select[name="data_text_type"]').val();
						var text_length = parseInt($('input[name="data_text_length"]').val());
						if (text_length && text_length != 0) {
							tag += ':' + text_length;
						}
						if ($('input[name="data_text_strip"]:checked').val() == 1) {
							tag += ':strip';
						}
						str += ' ' + td_start + tag + td_end;
					}

					if ($('input[name="data_readmore_enable"]:checked').val() == 1) {
						var tag = 'readmore';
						var readmore_text = $('input[name="data_readmore_text"]').val();
						var readmore_class = $('input[name="data_readmore_class"]').val();
						if (readmore_text) {
							tag += ':' + readmore_text;
						}
						if (readmore_class && readmore_class != 'readon') {
							if (!readmore_text) {
								tag += ':';
							}
							tag += '|' + readmore_class;
						}
						str += ' ' + td_start + tag + td_end;
					}

					if ($('input[name="enable_div"]:checked').val() == 1) {
						var params = [];
						if ($('input[name="div_width"]').val()) {
							params[params.length] = 'width:' + $('input[name="div_width"]').val();
						}
						if ($('input[name="div_height"]').val()) {
							params[params.length] = 'height:' + $('input[name="div_height"]').val();
						}
						if ($('input[name="div_float"]:checked').val() != 0) {
							params[params.length] = 'float:' + $('input[name="div_float"]:checked').val();
						}
						if ($('input[name="div_class"]').val()) {
							params[params.length] = 'class:' + $('input[name="div_class"]').val();
						}
						str = t_start + ('div ' + params.join('|') ).trim() + t_end
							+ str.trim()
							+ t_start + '/div' + t_end;
					}

					str = t_start + '<?php echo $plugin_tag; ?> <?php echo $content_type == 'k2' ? 'k2:' : ''; ?>' + id + t_end + str.trim() + t_start + '/<?php echo $plugin_tag; ?>' + t_end;

					window.parent.jInsertEditorText(str, '<?php echo JFactory::getApplication()->input->getString('name', 'text'); ?>');
					window.parent.SqueezeBox.close();
				})(jQuery);
			}

			function initDivs() {
				(function($) {
					$('div.toggle_div').each(function(i, el) {
						$('input[name="' + $(el).attr('rel') + '"]').each(function(i, el) {
							$(el).click(function() {
								toggleDivs();
							});
						});
					});
					toggleDivs();
				})(jQuery);
			}

			function toggleDivs() {

				(function($) {
					$('div.toggle_div').each(function(i, el) {
						el = $(el);
						if ($('input[name="' + el.attr('rel') + '"]:checked').val() == 1) {
							el.slideDown();
						} else {
							el.slideUp();
						}
					});
				})(jQuery);
			}

			jQuery(document).ready(function() {
				initDivs();
			});
		</script>
		<?php
	}

	function outputTableK2(&$rows, &$page, &$lists)
	{
		$db   = JFactory::getDbo();
		$user = JFactory::getUser();
		?>
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search"
				       class="element-invisible"><?php echo JText::_('COM_BANNERS_SEARCH_IN_TITLE'); ?></label>
				<input type="text" name="filter_search" id="filter_search"
				       placeholder="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>"
				       value="<?php echo $lists['filter_search']; ?>"
				       title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left hidden-phone">
				<button class="btn" type="submit" rel="tooltip"
				        title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
					<span class="icon-search"></span></button>
				<button class="btn" type="button" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"
				        onclick="document.id('filter_search').value='';this.form.submit();">
					<span class="icon-remove"></span></button>
			</div>

			<div class="btn-group pull-right hidden-phone">
				<?php echo $lists['categories']; ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<?php echo $lists['authors']; ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<?php echo $lists['state']; ?>
			</div>
		</div>

		<table class="table table-striped">
			<thead>
			<tr>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th width="1%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', 'JSTATUS', 'published', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'title', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'JFIELD_ALIAS_LABEL', 'alias', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th class="title" width="8%" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort', 'JCATEGORY', 'category', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th width="10%" class="nowrap hidden-phone">
					<?php echo JHtml::_('grid.sort', 'JAUTHOR', 'author', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="13">
					<?php echo $page->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			foreach ($rows as $row)
			{
				if ($user->authorise('com_users', 'manage'))
				{
					if ($row->created_by_alias)
					{
						$author = $row->created_by_alias;
					}
					else
					{
						$author = $row->author;
					}
				}
				else
				{
					if ($row->created_by_alias)
					{
						$author = $row->created_by_alias;
					}
					else
					{
						$author = $row->author;
					}
				}
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td class="center">
						<?php echo '<label rel="tooltip" title="<strong>' . JText::_('AA_USE_ID_IN_TAG') . '</strong><br/>{article k2:' . $row->id . '}...{/article}"><a href="javascript:;" onclick="articlesanywhere_jInsertEditorText( \'' . $row->id . '\' );return false;">' . $row->id . '</a></label>'; ?>
					</td>
					<td class="center">
						<?php echo JHtml::_('jgrid.published', $row->published, $row->id, 'articles.', 0, 'cb', $row->publish_up, $row->publish_down); ?>
					</td>
					<td class="title">
						<?php echo '<label rel="tooltip" title="<strong>' . JText::_('AA_USE_TITLE_IN_TAG') . '</strong><br/>{article k2:' . htmlspecialchars($row->title, ENT_QUOTES, 'UTF-8') . '}...{/article}"><a href="javascript:;" onclick="articlesanywhere_jInsertEditorText( \'' . addslashes(htmlspecialchars($row->title, ENT_COMPAT, 'UTF-8')) . '\' );return false;">' . htmlspecialchars($row->title, ENT_QUOTES, 'UTF-8') . '</a></label>'; ?>
					</td>
					<td class="title">
						<?php echo '<label rel="tooltip" title="<strong>' . JText::_('AA_USE_ALIAS_IN_TAG') . '</strong><br/>{article k2:' . $row->alias . '}...{/article}"><a href="javascript:;" onclick="articlesanywhere_jInsertEditorText( \'' . $row->alias . '\' );return false;">' . $row->alias . '</a></label>'; ?>
					</td>
					<td>
						<?php echo $row->category; ?>
					</td>
					<td>
						<?php echo $author; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>
		<?php
	}

	function outputTableCore(&$rows, &$page, &$lists)
	{
		$db = JFactory::getDbo();
		?>
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search"
				       class="element-invisible"><?php echo JText::_('COM_BANNERS_SEARCH_IN_TITLE'); ?></label>
				<input type="text" name="filter_search" id="filter_search"
				       placeholder="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>"
				       value="<?php echo $lists['filter_search']; ?>"
				       title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left hidden-phone">
				<button class="btn" type="submit" rel="tooltip"
				        title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
					<span class="icon-search"></span></button>
				<button class="btn" type="button" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"
				        onclick="document.id('filter_search').value='';this.form.submit();">
					<span class="icon-remove"></span></button>
			</div>

			<div class="btn-group pull-right hidden-phone">
				<?php echo $lists['categories']; ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<?php echo $lists['authors']; ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<?php echo $lists['state']; ?>
			</div>
		</div>

		<table class="table table-striped">
			<thead>
			<tr>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th width="1%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', 'JSTATUS', 'published', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'title', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'JFIELD_ALIAS_LABEL', 'alias', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th width="10%" class="nowrap title">
					<?php echo JHtml::_('grid.sort', 'JCATEGORY', 'category', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th width="10%" class="nowrap hidden-phone">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_CREATED_BY', 'author', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="13">
					<?php echo $page->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			foreach ($rows as $row)
			{
				if ($row->created_by_alias)
				{
					$author = $row->created_by_alias;
				}
				else
				{
					$author = $row->created_by;
				}
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td class="center">
						<?php echo '<label rel="tooltip" title="<strong>' . JText::_('AA_USE_ID_IN_TAG') . '</strong><br/>{article ' . $row->id . '}...{/article}"><a href="javascript:;" onclick="articlesanywhere_jInsertEditorText( \'' . $row->id . '\' );return false;">' . $row->id . '</a></label>'; ?>
					</td>
					<td class="center">
						<?php echo JHtml::_('jgrid.published', $row->published, $row->id, 'articles.', 0, 'cb', $row->publish_up, $row->publish_down); ?>
					</td>
					<td class="title">
						<?php echo '<label rel="tooltip" title="<strong>' . JText::_('AA_USE_TITLE_IN_TAG') . '</strong><br/>{article ' . htmlspecialchars($row->title, ENT_QUOTES, 'UTF-8') . '}...{/article}"><a href="javascript:;" onclick="articlesanywhere_jInsertEditorText( \'' . addslashes(htmlspecialchars($row->title, ENT_COMPAT, 'UTF-8')) . '\' );return false;">' . htmlspecialchars($row->title, ENT_QUOTES, 'UTF-8') . '</a></label>'; ?>
					</td>
					<td class="title">
						<?php echo '<label rel="tooltip" title="<strong>' . JText::_('AA_USE_ALIAS_IN_TAG') . '</strong><br/>{article ' . $row->alias . '}...{/article}"><a href="javascript:;" onclick="articlesanywhere_jInsertEditorText( \'' . $row->alias . '\' );return false;">' . $row->alias . '</a></label>'; ?>
					</td>
					<td>
						<?php echo $row->category; ?>
					</td>
					<td class="hidden-phone">
						<?php echo $author; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>
		<?php
	}
}
