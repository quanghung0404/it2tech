<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldModuleType extends JFormField
{

	protected $type = 'ModuleType';

	protected function getInput()
	{
		JFactory::getLanguage()->load( 'mod_easyblogrelatedpost' , JPATH_ROOT );

		JHTML::_( 'behavior.modal' );

		EB::loadHeaders();

		ob_start();
		$output		= ob_get_contents();
		ob_end_clean();

		return $output;
	}
}
