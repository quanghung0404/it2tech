<?php
/**
 * List View
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

jimport('joomla.application.component.view');

/**
 * List View
 */
class ContentTemplaterViewList extends JViewLegacy
{
	protected $enabled;
	protected $list;
	protected $pagination;
	protected $state;
	protected $config;
	protected $parameters;

	/**
	 * Display the view
	 *
	 */
	public function display($tpl = null)
	{
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$this->parameters = NNParameters::getInstance();

		$this->enabled       = ContentTemplaterHelper::isEnabled();
		$this->list          = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->config        = $this->parameters->getComponentParams('contenttemplater');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = ContentTemplaterHelper::getActions();

		$viewLayout = JFactory::getApplication()->input->get('layout', 'default');

		if ($viewLayout == 'import')
		{
			// Set document title
			JFactory::getDocument()->setTitle(JText::_('CONTENT_TEMPLATER') . ': ' . JText::_('NN_IMPORT_ITEMS'));
			// Set ToolBar title
			JToolbarHelper::title(JText::_('CONTENT_TEMPLATER') . ': ' . JText::_('NN_IMPORT_ITEMS'), 'contenttemplater icon-nonumber');
			// Set toolbar items for the page
			JToolBarHelper::back('JTOOLBAR_BACK', 'index.php?option=com_contenttemplater');
		}
		else
		{
			// Set document title
			JFactory::getDocument()->setTitle(JText::_('CONTENT_TEMPLATER') . ': ' . JText::_('NN_LIST'));
			// Set ToolBar title
			JToolbarHelper::title(JText::_('CONTENT_TEMPLATER') . ': ' . JText::_('NN_LIST'), 'contenttemplater icon-nonumber');
			// Set toolbar items for the page
			if ($canDo->get('core.create'))
			{
				JToolbarHelper::addNew('item.add');
			}
			if ($canDo->get('core.edit'))
			{
				JToolbarHelper::editList('item.edit');
			}
			if ($canDo->get('core.create'))
			{
				JToolbarHelper::custom('list.copy', 'copy', 'copy', 'JTOOLBAR_DUPLICATE', true);
			}
			if ($canDo->get('core.edit.state') && $state->get('filter.state') != 2)
			{
				JToolbarHelper::publish('list.publish', 'JTOOLBAR_PUBLISH', true);
				JToolbarHelper::unpublish('list.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			}
			if ($canDo->get('core.delete') && $state->get('filter.state') == -2)
			{
				JToolbarHelper::deleteList('', 'list.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			else if ($canDo->get('core.edit.state'))
			{
				JToolbarHelper::trash('list.trash');
			}
			if ($canDo->get('core.create'))
			{
				JToolbarHelper::custom('list.export', 'box-remove', 'box-remove', 'NN_EXPORT');
				JToolbarHelper::custom('list.import', 'box-add', 'box-add', 'NN_IMPORT', false);
			}
			if ($canDo->get('core.admin'))
			{
				JToolbarHelper::preferences('com_contenttemplater');
			}

			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_PUBLISHED'),
				'filter_state',
				JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived' => 0)), 'value', 'text', $this->state->get('filter.state'), true)
			);
		}
	}

	function maxlen($string = '', $maxlen = 60)
	{
		if (JString::strlen($string) > $maxlen)
		{
			$string = JString::substr($string, 0, $maxlen - 3) . '...';
		}

		return $string;
	}
}
