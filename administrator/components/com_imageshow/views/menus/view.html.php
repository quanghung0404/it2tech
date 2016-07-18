<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: view.html.php 13751 2012-07-04 01:40:48Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
require_once ('components' . DS . 'com_menus' . DS . 'views' . DS . 'items' . DS . 'view.html.php');
require_once (JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_imageshow'.DS . 'helpers'.DS.'menus.php');
require_once ('components' . DS . 'com_menus' . DS . 'models' . DS . 'items.php');
class ImageShowViewMenus extends MenusViewItems
{
	public function __construct($config = array ())
	{
		parent::__construct($config);
		$model = JModelLegacy::getInstance('Items', 'MenusModel');
		$this->_document = JFactory::getDocument();
		$this->_addAssets();
		$this->setModel($model, true);
	}

	private function _addAssets(){}
}
