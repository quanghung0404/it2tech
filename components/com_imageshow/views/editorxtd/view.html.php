<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id$
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');
class ImageShowViewEditorXTD extends JViewLegacy
{
	public function display($tpl = null)
	{
		JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));

		$user		= JFactory::getUser();
		$userId		= $user->get('id');

		if (!$userId)
		{
			// Build error object
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
		}
		JHTML::_('behavior.framework', false);
		$this->_document = JFactory::getDocument();
		$this->_document->addStyleSheet(JURI::root(true) . '/components/com_imageshow/assets/css/imageshow.css');
		parent::display($tpl);
	}
}