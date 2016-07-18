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

require_once(dirname(__FILE__) . '/controller.php');

class EasyBlogControllerCompiler extends EasyBlogController
{
	public function __construct($options = array())
	{
		parent::__construct($options);
	}

	/**
	 * Allows caller to compile scripts on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function compile()
	{
		if (!EB::isSiteAdmin()) {
			return JError::raiseError(JText::_('You had fun?'));
		}

		// See if the user wants to compile by specific sections
		$sections = $this->input->get('sections', array('admin', 'site', 'dashboard', 'composer'), 'word');

		// Should we be compiling and minifying the scripts?
		$minify = $this->input->get('minify', false, 'bool');

		$compiler = EB::compiler();


		$results = array();

		foreach ($sections as $section) {
			$result = $compiler->compile($section, $minify);

			$results[] = $result;
		}

		// XHR transport
		header('Content-type: text/x-json; UTF-8');
		echo json_encode($results);
		exit;

	}
}