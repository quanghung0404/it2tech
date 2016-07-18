<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: articles.php 13756 2012-07-04 03:12:38Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.controller');
class ImageShowControllerArticles extends JControllerLegacy
{
	function __construct($config = array())
	{
		parent::__construct($config);
	}

	function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('layout', 'default');
		JRequest::setVar('view', 'articles');
		$return = JRequest::getVar('return', '');
		if($return){
			$this->setRedirect($return);
		}
		parent::display();
	}
}
?>