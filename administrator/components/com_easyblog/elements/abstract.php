<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.html.html');
jimport('joomla.form.formfield');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

class EasyBlogFormField extends JFormField
{
	public function __construct()
	{
		EB::loadLanguages(JPATH_ADMINISTRATOR);
		
		// Load our own js library
		EB::init('admin');

		// $this->doc = JFactory::getDocument();
		// $this->doc->addStylesheet(rtrim(JURI::base(), '/') . '/components/com_easyblog/themes/default/css/elements.css');

		JHTML::_('behavior.modal');

		$this->app = JFactory::getApplication();
	}

	/**
	 * Abstract method that should be implemented on child classes
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	protected function getInput()
	{
	}
}
