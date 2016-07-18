<?php
/**
 * Popup include page
 * Displays a list with modules
 *
 * @package         Add to Menu
 * @version         4.0.4PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

$user = JFactory::getUser();
if (!JFactory::getApplication()->isSite() && $user->get('guest') || !$user->authorise('core.create', 'com_menus'))
{
	JError::raiseError(403, JText::_("ALERTNOTAUTH"));
}

$class = new AddToMenu;
$class->render();

class AddToMenu
{
	var $vars = array();
	var $extra = array();

	function render()
	{
		jimport('joomla.filesystem.folder');

		$this->vars = JFactory::getApplication()->input->getVar('vars');
		$option = $this->vars['option'];
		$comp_file = JFactory::getApplication()->input->getVar('comp');

		$folder = JPATH_ADMINISTRATOR . '/components/' . $option . '/addtomenu';
		if (!JFolder::exists($folder))
		{
			$folder = JPATH_ADMINISTRATOR . '/modules/mod_addtomenu/components/' . $option;
		}
		if (!JFolder::exists($folder))
		{
			return;
		}

		jimport('joomla.filesystem.file');

		require_once __DIR__ . '/helper.php';

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
		$func = new NNFrameworkFunctions;

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$parameters = NNParameters::getInstance();

		$template = '';

		foreach (JFolder::files($folder, '.xml') as $filename)
		{
			$file = $folder . '/' . $filename;

			$xml = $func->xmlToObject($file, 'params');
			if (isset($xml->required) && (empty($xml->required) || ModAddToMenu::checkRequiredFields($xml->required, $this->vars)))
			{
				$template = $xml;
				if (!isset($template->dbselect) || !is_object($template->dbselect))
				{
					$template->dbselect = new stdClass;
				}
				if (!isset($template->extras) || !is_object($template->extras))
				{
					$template->extras = new stdClass;
				}
				if (!isset($template->urlparams) || !is_object($template->urlparams))
				{
					$template->urlparams = new stdClass;
				}
				if (!isset($template->menuparams) || !is_object($template->menuparams))
				{
					$template->menuparams = new stdClass;
				}
				break;
			}
		}

		if (!$template)
		{
			return;
		}

		if ($option == 'com_categories')
		{
			$option = isset($this->vars['extension']) ? $this->vars['extension'] : 'com_content';
		}
		$lang = JFactory::getLanguage();
		$lang->load('mod_addtomenu', JPATH_ADMINISTRATOR);
		$lang->load('com_menus', JPATH_ADMINISTRATOR);
		$lang->load($option, JPATH_ADMINISTRATOR);
		$lang->load($option . '.sys', JPATH_ADMINISTRATOR);

		$insert = JFactory::getApplication()->input->getVar('insert');
		if ($insert)
		{
			$this->insertMenuItem($template, $parameters);
		}
		else
		{
			$this->renderHTML($template);
		}
	}

	function insertMenuItem(&$template, &$parameters)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$table = JTable::getInstance('menu');

		$table->title = JFactory::getApplication()->input->getVar('name', '');
		$table->alias = JFactory::getApplication()->input->getVar('alias', '');
		if (!strlen($table->alias))
		{
			$table->alias = $table->title;
		}
		$table->alias = $this->filterAlias($table->alias);

		$table->published = JFactory::getApplication()->input->getInt('published', 0);
		$menuitem = JFactory::getApplication()->input->getVar('menuitem', 'mainmenu::0');
		$menuitem = explode('::', $menuitem);
		$table->menutype = $menuitem['0'];
		$table->parent_id = isset($menuitem['1']) ? (int) $menuitem['1'] : 0;
		$table->access = JFactory::getApplication()->input->getInt('access', 1);
		$table->language = JFactory::getApplication()->input->getVar('language', '*');
		$table->template_style_id = JFactory::getApplication()->input->getVar('template_style_id', '*');
		$table->client_id = 0;

		$table->level = 1;
		$table->path = $table->alias;

		if ($table->parent_id)
		{
			$query->clear()
				->select('m.path, m.level')
				->from('#__menu AS m')
				->where('m.id = ' . (int) $table->parent_id);
			$db->setQuery($query);
			$parent = $db->loadObject();
			$table->level = (int) $parent->level;
			$table->level++;
			$table->path = $parent->path . '/' . $table->path;
		}

		$table->type = 'component';

		$query->clear()
			->select('e.extension_id')
			->from('#__extensions AS e')
			->where('e.type = ' . $db->quote('component'))
			->where('e.element = ' . $db->quote($template->urlparams->option));
		$db->setQuery($query);
		$table->component_id = $db->loadResult();

		$table->link = 'index.php?';
		$urlparams = array();
		foreach ($template->urlparams as $key => $val)
		{
			$val = $this->getVar($val);
			if (strlen($val))
			{
				$urlparams[] = $key . '=' . $val;
			}
		}
		$table->link .= implode('&', $urlparams);

		$menuparams = array();
		foreach ($template->menuparams as $key => $val)
		{
			$val = $this->getVar($val);
			if (strlen($val))
			{
				$menuparams[$key] = $val;
			}
		}

		$table->params = (object) $menuparams;

		$option = $this->vars['option'];
		$view = isset($this->vars['view']) ? $this->vars['view'] : (isset($this->vars['task']) ? $this->vars['task'] : '');
		// add default view settings
		$path = JPath::clean(JPATH_SITE . '/components/' . $option . '/views/' . $view . '/tmpl/default.xml');
		if (JFile::exists($path))
		{
			$xml = simplexml_load_file($path);
			$xml = $xml->xpath('fields[2]/fieldset/field');
			foreach ($xml as $param)
			{
				$name = trim((string) $param->attributes()->name);
				if ($name && (!isset($table->params->{$name}) || $table->params->{$name} == ''))
				{
					$table->params->{$name} = (string) $param->attributes()->default;
				}
			}
		}
		else
		{
			// add default component settings
			$path = JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $option . '/config.xml');
			if (JFile::exists($path))
			{
				$table->params = $parameters->getParams($table->params, $path);
			}
		}

		// add default menu settings
		$path = JPath::clean(JPATH_ADMINISTRATOR . '/components/com_menus/models/forms/item_component.xml');
		if (JFile::exists($path))
		{
			$table->params = $parameters->getParams($table->params, $path);
		}

		$table->params = json_encode($table->params);

		// Set the new location in the tree for the node.
		$table->setLocation($table->parent_id, 'last-child');
		$error = '';

		if (!$table->check())
		{
			$error = $table->getError();
		}

		if (!$table->store())
		{
			$error = $table->getError();
		}

		if ($error)
		{
			$error = str_replace('<br />', ' :: ', $table->getError());
			$error = explode('SQL=', $error);
			$error = trim($error['0']);

			if (strpos($error, 'Duplicate entry') !== false)
			{
				$error = JText::_('JLIB_DATABASE_ERROR_MENU_UNIQUE_ALIAS');
			}

			$error = JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error);

			JError::raiseWarning('1', $error);
			$this->renderHTML($template);
		}
		else
		{
			echo "<script>window.parent.addtomenu_setMessage( '" . JText::_('COM_MENUS_MENU_ITEM_SAVE_SUCCESS', true) . "', 1 );</script>\n";
		}
	}

	function renderHTML(&$template)
	{
		if (isset($template->dbselect->table))
		{
			if (!isset($template->dbselect->alias))
			{
				$template->dbselect->alias = $template->dbselect->name;
			}

			$db = JFactory::getDbo();

			$query = $db->getQuery(true)
				->select('t.' . $template->dbselect->name . ' AS name')
				->select('t.' . $template->dbselect->alias . ' AS alias');

			$order = isset($template->dbselect->order) ? 't.' . $template->dbselect->order : 't.' . $template->dbselect->name . ' ASC';
			$query->order($order);

			if (isset($template->dbselect->select))
			{
				foreach ($template->dbselect->select as $key => $val)
				{
					$query->select('t.' . $val . ' AS ' . $db->quote($key));
				}
			}

			$query->from($template->dbselect->table . ' AS t');

			if (isset($template->dbselect->join))
			{
				$on = array();
				foreach ($template->dbselect->join->on as $key => $val)
				{
					$on[] = ('j.' . $key . ' = ' . $db->quote($this->getVar($val)));
				}

				$query->join('left', $template->dbselect->join->table . ' AS j ON ' . implode('AND', $on));

				if (isset($template->dbselect->join->where))
				{
					foreach ($template->dbselect->join->where as $key => $val)
					{
						$query->where('j.' . $key . ' = ' . $db->quote($this->getVar($val)));
					}
				}

				if (isset($template->dbselect->join->select))
				{
					foreach ($template->dbselect->join->select as $key => $val)
					{
						$query->select('j.' . $val . ' AS ' . $db->quote($key));
					}
				}

				if (isset($template->dbselect->join->order))
				{
					$query->order('j.' . $template->dbselect->join->order);
				}
			}

			if (isset($template->dbselect->where))
			{
				foreach ($template->dbselect->where as $key => $val)
				{
					$query->where('t.' . $key . ' = ' . $db->quote($this->getVar($val)));
				}
			}

			$db->setQuery($query);
			$item = $db->loadObject();
		}
		else
		{
			$item = new stdClass;
			$item->name = JText::_($template->dbselect->name);

			$item->alias = isset($template->dbselect->alias) ? $template->dbselect->alias : $item->name;
		}

		$item->alias = $this->filterAlias($item->alias);

		foreach ($item as $key => $val)
		{
			if (!in_array($key, array('name', 'alias')))
			{
				$this->setVar($key, $val);
			}
		}

		$width = '100%';
		$elements = array();

		$elements[] = $this->el(
			'COM_MENUS_ITEM_FIELD_TITLE_LABEL',
			'name',
			'<input class="inputbox" type="text" name="name" id="name" style=width:' . $width . ';" maxlength="255" value="' . str_replace('"', '&quot;', $item->name) . '" />'
		);

		$elements[] = $this->el(
			'JFIELD_ALIAS_LABEL',
			'alias',
			'<input class="inputbox" type="text" name="alias" id="alias" style=width:' . $width . ';" maxlength="255" value="' . str_replace('"', '&quot;', $item->alias) . '" />'
		);

		$elements[] = $this->el(
			'JSTATUS',
			'published',
			'<fieldset id="published" class="radio btn-group">'
			. '<input type="radio" name="published" id="published1" value="1" checked="checked" />'
			. '<label for="published1">' . JText::_('JPUBLISHED') . '</label>'
			. '<input type="radio" name="published" id="published0" value="0" />'
			. '<label for="published0">' . JText::_('JUNPUBLISHED') . '</label>'
			. '</fieldset>'
		);

		$elements[] = $this->el(
			'COM_MENUS_ITEM_FIELD_PARENT_LABEL',
			'menuitem',
			$this->getMenuItems('menuitem', $width)
		);

		$parameters = NNParameters::getInstance();
		$params = $parameters->getModuleParams('addtomenu');

		if ($params->display_field_access)
		{
			$elements[] = $this->el(
				'JFIELD_ACCESS_LABEL',
				'access',
				'<select name="access" class="inputbox">
				' . JHtml::_('select.options', JHtml::_('access.assetgroups')) . '
			</select>'
			);
		}

		if ($params->display_field_language)
		{
			$elements[] = $this->el(
				'JFIELD_LANGUAGE_LABEL',
				'language',
				'<select name="language" class="inputbox">
				' . JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true)) . '
			</select>'
			);
		}

		if ($params->display_field_template_style)
		{
			$options = $this->getTemplateStyles();
			array_unshift($options, array(JHtml::_('select.option', 0, JText::_('JOPTION_USE_DEFAULT'))));

			$elements[] = $this->el(
				'COM_MENUS_ITEM_FIELD_TEMPLATE_LABEL',
				'templatestyle',
				JHtml::_(
					'select.groupedlist', $options, 'template_style_id',
					array('group.items' => null)
				)
			);
		}

		if (isset($template->extras->extra))
		{
			if (!is_array($template->extras->extra))
			{
				$template->extras->extra = array($template->extras->extra);
			}

			$extra_elements = array();
			foreach ($template->extras->extra as $element)
			{
				if ($element->type == 'toggler')
				{
					if (isset($element->param))
					{
						if (!isset($element->value))
						{
							$element->value = '';
						}

						$set_groups = explode('|', $element->param);
						$set_values = explode('|', $element->value);
						$ids = array();

						foreach ($set_groups as $i => $group)
						{
							$count = $i;
							if ($count >= count($set_values))
							{
								$count = 0;
							}
							$values = explode(',', $set_values[$count]);
							foreach ($values as $val)
							{
								$ids[] = $group . '.' . $val;
							}
						}

						$el = '</div></div><div id="' . rand(1000000, 9999999) . '___' . implode('___', $ids) . '" class="nntoggler nntoggler_horizontal"><div><div>';
					}
					else
					{
						$el = '</div></div></div>';
					}

					$extra_elements[] = $this->el(
						'',
						'',
						$el
					);
					continue;
				}

				if (!isset($element->name) || !isset($element->type))
				{
					continue;
				}

				if ($element->type == 'title')
				{
					$extra_elements[] = $this->el(
						'@spacer',
						'@spacer',
						JText::_($element->name)
					);
					continue;
				}

				if (!isset($element->param))
				{
					continue;
				}

				if ($element->name == '')
				{
					$element->name = $element->param;
				}

				if ($element->param == '')
				{
					$element->param = strtolower($element->name);
				}

				if (!isset($element->value))
				{
					$element->value = '';
				}

				if (!isset($element->default))
				{
					$element->default = '';
				}

				$style = '';
				if (isset($element->style))
				{
					$style = $element->style;
				}

				switch ($element->type)
				{
					case 'select':
					case 'list':
					case 'radio':
						$options = array();
						if (!isset($element->values))
						{
							$element->values = new stdClass;
							$element->values->{$element->value} = $element->value;
						}

						if (is_string($element->values))
						{
							$element->values = new stdClass;
							$element->values{$element->values} = $element->values;
						}

						foreach ($element->values->value as $value)
						{
							$value->value = is_string($value->value) ? $value->value : '';
							$options[] = JHtml::_('select.option', $value->value, JText::_($value->name), 'value', 'text');
						}
						break;

					case 'textarea':
					case 'hidden':
					case 'text':
						if (!empty($element->values) && !is_string($element->values))
						{
							foreach ($element->values as $value)
							{
								$element->value = $value;
								break;
							}
						}
						break;
				}

				switch ($element->type)
				{
					case 'select':
					case 'list':
						$el = JHtml::_('select.genericlist', $options, 'params[' . $element->param . ']', 'class="inputbox" style="' . $style . '"', 'value', 'text', $element->default, $element->param);
						break;
					case 'radio':
						$el = JHtml::_('select.radiolist', $options, 'params[' . $element->param . ']', 'class="inputbox" style="' . $style . '"', 'value', 'text', $element->default);
						// add breaks between each radio element
						$el = preg_replace('#(</label>)(\s*<input )#i', '\1<br />\2', $el);
						break;
					case 'textarea':
						$el = '<textarea style="width:' . $width . ';height:100px;' . $style . '" name="params[' . $element->param . ']">' . $element->value . '</textarea>';
						break;
					case 'hidden':
						$el = '<input type="hidden" style="' . $style . '" name="params[' . $element->param . ']" value="' . str_replace('"', '&quot;', $element->value) . '" />';
						break;
					case 'text':
					default:
						$el = '<input type="text" name="params[' . $element->param . ']" style="width:' . $width . ';' . $style . '" value="' . str_replace('"', '&quot;', $element->value) . '" />';
						break;
				}

				$el = trim(preg_replace('#^\s*<div class="controls">(.*)</div>\s*$#is', '\1', $el));
				$extra_elements[] = $this->el(
					$element->name,
					$element->param,
					$el
				);
			}

			if (!empty($extra_elements))
			{
				$elements[] = $this->el(
					'@spacer',
					'@spacer',
					'<strong>' . JText::_('ATM_EXTRA_OPTIONS') . '</strong>'
				);
				$elements = array_merge($elements, $extra_elements);
			}
		}

		$this->outputHTML($template, $elements);
	}

	function el($name, $id, $element)
	{
		return (object) array(
			'name'    => $name,
			'id'      => $id,
			'element' => $element,
		);
	}

	function getMenuItems($name, $width = '100%')
	{
		$db = JFactory::getDbo();

		// load the list of menu types
		$query = $db->getQuery(true)
			->select('m.menutype, m.title')
			->from('#__menu_types AS m')
			->order('m.id');
		$db->setQuery($query);
		$menuTypes = $db->loadObjectList();

		// load the list of menu items
		$query->clear()
			->select('m.id, m.parent_id, m.title, m.menutype, m.type, m.published')
			->from('#__menu AS m')
			->where('m.published != -2')
			->order('m.menutype, m.parent_id, m.lft');
		$db->setQuery($query);
		$menuItems = $db->loadObjectList();

		// establish the hierarchy of the menu
		// TODO: use node model
		$children = array();

		if ($menuItems)
		{
			// first pass - collect children
			foreach ($menuItems as $v)
			{
				$pt = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}

		// second pass - get an indent list of the items
		$list = JHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

		// assemble into menutype groups
		$groupedList = array();
		foreach ($list as $k => $v)
		{
			$groupedList[$v->menutype][] =& $list[$k];
		}

		// assemble menu items to the array
		$options = array();

		foreach ($menuTypes as $count => $type)
		{
			if ($count)
			{
				$options[] = JHtml::_('select.option', '-', '&nbsp;', 'value', 'text', true);
			}
			else
			{
				$selected = $type->menutype . '::0';
			}

			$options[] = JHtml::_('select.option', $type->menutype . '::0', '[ ' . $type->title . ' ]');

			if (isset($groupedList[$type->menutype]))
			{
				$n = count($groupedList[$type->menutype]);
				for ($i = 0; $i < $n; $i++)
				{
					$item =& $groupedList[$type->menutype][$i];

					// If menutype is changed but item is not saved yet, use the new type in the list
					if (JFactory::getApplication()->input->getString('option', '', 'get') == 'com_menus')
					{
						$currentItemArray = JFactory::getApplication()->input->get('cid', array(), 'array');
						$currentItemId = (int) $currentItemArray['0'];
						$currentItemType = JFactory::getApplication()->input->getString('type', $item->type, 'get');
						if ($currentItemId == $item->id && $currentItemType != $item->type)
						{
							$item->type = $currentItemType;
						}
					}

					if ($item->published == 0)
					{
						$item->treename .= ' (' . JText::_('Unpublished') . ')';
					}

					$options[] = JHtml::_('select.option', $type->menutype . '::' . $item->id, '&nbsp;&nbsp;&nbsp;' . $item->treename);
				}
			}
		}

		$attribs = 'class="inputbox" style=width:' . $width . ';"';
		$attribs .= ' size="' . ((count($options) > 10) ? 10 : count($options)) . '"';

		return JHtml::_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected);
	}

	function setVar($key, $val)
	{
		$this->extra[$key] = $val;
	}

	function getVar($var)
	{
		if ($var['0'] == '$')
		{
			$var = $this->getVal(substr($var, 1));
		}

		return $var;
	}

	function getVal($val)
	{
		$params = JFactory::getApplication()->input->getVar('params');

		if (isset($params[$val]))
		{
			$value = $params[$val];
		}
		else if (isset($this->extra[$val]))
		{
			$value = $this->extra[$val];
		}
		else if (isset($this->vars[$val]))
		{
			$value = $this->vars[$val];
		}
		else
		{
			$value = JFactory::getApplication()->input->getVar($val);
		}

		if (is_array($value))
		{
			$value = $value['0'];
		}

		return $value;
	}

	function filterAlias($alias)
	{
		$alias = JApplication::stringURLSafe($alias);

		if (trim(str_replace('-', '', $alias)) == '')
		{
			$alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}

		return $alias;
	}

	function outputHTML(&$template, &$elements)
	{
		JHtml::_('behavior.tooltip');
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

		JHtml::stylesheet('nnframework/style.min.css', false, true);
		NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/nnframework/js/script.min.js');
		NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/nnframework/js/toggler.min.js');

		$uri = JUri::getInstance();
		?>

		<div class="header">
			<div class="container-fluid">
				<h1 class="page-title">
					<?php echo JText::_('ADD_TO_MENU'); ?>:
					<?php echo JText::_($template->name); ?>
				</h1>
			</div>
		</div>

		<div class="subhead-collapse">
			<div class="subhead">
				<div class="container-fluid">
					<div id="container-collapse" class="container-collapse"></div>
					<div class="row-fluid">
						<div class="span12">
							<div class="btn-toolbar" id="toolbar">
								<div class="btn-group" id="toolbar-apply">
									<button href="#" onclick="document.getElementById('adminForm').submit();" class="btn btn-small btn-success">
										<span class="icon-apply icon-white"></span>
										<?php echo JText::_('JAPPLY') ?>
									</button>
								</div>
								<div class="btn-group" id="toolbar-cancel">
									<button href="#" onclick="window.parent.SqueezeBox.close();" class="btn btn-small">
										<span class="icon-cancel "></span>
										<?php echo JText::_('JCANCEL') ?>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="container-fluid container-main form-horizontal">
			<form action="<?php echo $uri->toString(); ?>" method="post" name="adminForm" id="adminForm">
				<input type="hidden" name="insert" value="1" />

				<?php foreach ($elements as $element) : ?>
					<div class="control-group">
						<?php if (!$element->name) : ?>
							<div class="controls"><?php echo $element->element; ?></div>
						<?php elseif ($element->name == '@spacer') : ?>
							<?php
						else : ?>
							<label id="<?php echo $element->id; ?>-lbl" for="<?php echo $element->id; ?>" class="control-label"><?php echo JText::_($element->name); ?></label>
							<div class="controls"><?php echo $element->element; ?></div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
				<?php foreach ($this->extra as $key => $val) : ?>
					<input type="hidden" name="<?php echo $key; ?>" value="<?php echo $val; ?>" />
				<?php endforeach; ?>
			</form>
		</div>
		<?php
	}

	protected function getTemplateStyles()
	{
		$groups = array();
		$lang = JFactory::getLanguage();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Build the query.
		$query->select('s.id, s.title, e.name as name, s.template')
			->from('#__template_styles as s')
			->where('s.client_id = 0')
			->order('template')
			->order('title')
			->join('LEFT', '#__extensions as e on e.element=s.template')
			->where('e.enabled = 1')
			->where($db->quoteName('e.type') . ' = ' . $db->quote('template'));

		$db->setQuery($query);
		$styles = $db->loadObjectList();

		if (empty($styles))
		{
			return $groups;
		}

		// Build the grouped list array.
		foreach ($styles as $style)
		{
			$template = $style->template;
			$lang->load('tpl_' . $template . '.sys', JPATH_SITE, null, false, true)
			|| $lang->load('tpl_' . $template . '.sys', JPATH_SITE . '/templates/' . $template, null, false, true);
			$name = JText::_($style->name);

			// Initialize the group if necessary.
			if (!isset($groups[$name]))
			{
				$groups[$name] = array();
			}

			$groups[$name][] = JHtml::_('select.option', $style->id, $style->title);
		}

		return $groups;
	}
}
