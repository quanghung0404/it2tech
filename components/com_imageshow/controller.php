<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: controller.php 10345 2011-12-19 07:55:48Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access');
jimport('joomla.application.component.controller');
class ImageShowController extends JControllerLegacy {

	function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('add',  'display');
		$this->registerTask('edit', 'display');
		$this->registerTask('apply', 'save');
	}

	function display($cachable = false, $urlparams = false)
	{
		$view = JRequest::getCmd('view');
		switch($view)
		{
			case 'list':
				JRequest::setVar('layout', 'default');
				JRequest::setVar('view', 'list');
				JRequest::setVar('model', 'list');
				break;
			case 'show':
			default:
				JRequest::setVar('layout', 'default');
				JRequest::setVar('view', 'show');
				JRequest::setVar('model', 'show');
				break;
		}


		switch ($this->getTask())
		{
			case 'modal':
				JRequest::setVar('layout', 'default');
				JRequest::setVar('view', 'editorxtd');
				break;
			default:
				break;
		}

		parent::display();
	}
}
?>