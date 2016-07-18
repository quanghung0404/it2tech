<?php
/**
 * DB Replacer Controller
 *
 * @package         DB Replacer
 * @version         4.0.6PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * DB Replacer Default Controller
 */
class DBReplacerController extends JControllerLegacy
{
	/**
	 * Custom Constructor
	 */
	public function __construct($default = array())
	{
		parent::__construct($default);
	}

	/**
	 * Replace Method
	 * Set Redirection to the main administrator index
	 */
	function replace()
	{
		$this->doReplace();
		$this->display();
	}

	/**
	 * Replace Method
	 * Set Redirection to the main administrator index
	 */
	function doReplace()
	{
		$params          = new stdClass;
		$params->table   = JFactory::getApplication()->input->get('table');
		$params->columns = JFactory::getApplication()->input->get('columns', array(0), 'array');
		$params->search  = JRequest::getVar('search', '', 'default', 'none', 2);

		if (!$params->table || $params->search == '' || !is_array($params->columns) || empty($params->columns))
		{
			return;
		}

		// Get/Create the model
		if (!$model = $this->getModel(JFactory::getApplication()->input->get('view', 'default')))
		{
			return;
		}

		$params->replace = JRequest::getVar('replace', '', 'default', 'none', 2);
		$params->case    = JFactory::getApplication()->input->getInt('case', 0);
		$params->where = JFactory::getApplication()->input->getString('where', '');
		$params->regex = JFactory::getApplication()->input->getInt('regex', 0);
		$params->utf8  = JFactory::getApplication()->input->getInt('utf8', 0);
		$config        = JComponentHelper::getParams('com_dbreplacer');
		$params->max   = (int) $config->get('max_rows', '100');

		$model->replace($params);
	}

	/**
	 * Display Method
	 * Call the method and display the requested view
	 */
	function display($cachable = false, $urlparams = false)
	{
		$viewName   = JFactory::getApplication()->input->get('view', 'default');
		$viewLayout = JFactory::getApplication()->input->get('layout', 'default');

		if ($viewName == 'item')
		{
			// Hide the main menu
			JFactory::getApplication()->input->set('hidemainmenu', 1);
		}

		$view = $this->getView('default', JFactory::getDocument()->getType());

		// Get/Create the model
		if ($model = $this->getModel('default'))
		{
			// Push the model into the view ( as default )
			$view->setModel($model, true);
		}

		// Set the layout
		$view->setLayout($viewLayout);

		// Display the view
		$view->display();
	}

	/**
	 * Import Method
	 * Call the method and display the import view
	 */
	function import()
	{
		JFactory::getApplication()->input->set('layout', 'import');
		$this->display();
	}
}
