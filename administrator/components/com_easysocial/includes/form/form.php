<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

/**
 * Our own internal form system
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialForm
{
	/**
	 * The form data
	 * @var obj
	 */
	private $form	= null;

	/**
	 * Creates a copy of it self and return to the caller.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialParameter
	 *
	 */
	public static function factory()
	{
		return new self();
	}

	/**
	 * Loads the form data
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function load( $data = null )
	{
		$this->form		= FD::makeObject( $data );
	}

	/**
	 * Bind the params
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bind( $params = null )
	{
		if( is_object( $params ) )
		{
			$this->params 	= $params;

			return;
		}

		if( is_file( $params ) )
		{
			$params 	= JFile::read( $params );
		}

		$this->params 	= FD::registry( $params );
	}


	/**
	 * Renders the form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 *
	 */
	public function render($tabs = false, $sidebarTabs = false, $active = '', $prefix = '', $processActiveTab = true)
	{
		// Load our custom theme.
		$theme 		= FD::get('Themes');

		$type 		= $tabs ? 'default.tabs' : 'default';

		foreach ($this->form as &$form) {
			foreach ($form->fields as &$field) {
				// Ensure that the default property exists
				if (!isset($field->default)) {
					$field->default 	= '';
				}

				// Ensure that the suffix exists
				if (!isset($field->suffix)) {
					$field->suffix 		= '';
				}

				$field->inputName	= $prefix ? $prefix . '[' . $field->name . ']' : $field->name;

				// Translate suffix if neccessary
				$field->suffix 	= JText::_($field->suffix);

				// Custom renderer based on type
				// Need to support apps renderer as well
				// apps:/path/to/file or apps:/[group]/[element]/renderer/[nameInCamelCase]
				// class SocialFormRenderer[NameInCamelCase]

				$rendererType = $field->type;
				$file = dirname(__FILE__) . '/renderer/' . strtolower($rendererType) . '.php';

				// Check for :
				if (strpos($field->type, ':') !== false) {

					list($protocol, $path) = explode(':', $field->type);

					$segments = explode('/', $path);

					$rendererType = array_pop($segments);

					$base = defined('SOCIAL_' . strtoupper($protocol)) ? constant('SOCIAL_' . strtoupper($protocol)) : SOCIAL_ADMIN;

					$file = $base . $path . '.php';
				}

				if (JFile::exists($file)) {

					require_once($file);

					$className 	= 'SocialFormRenderer' . ucfirst($rendererType);

					if (class_exists($className)) {
						$renderer	= new $className;

						$renderer->render($field, $this->params);
					}
				}
			}
		}

		// Replacements for invalid keys
		$invalidKeys	= array(' ', ',', '&', '.', '*', "'");

		// Generate unique id so multiple tabs form on a single page would not conflict
		$uid 	= uniqid();

		$theme->set('invalidKeys'	, $invalidKeys);
		$theme->set('uid'			, $uid);
		$theme->set('processActiveTab', $processActiveTab);
		$theme->set('prefix'		, $prefix);
		$theme->set('active'		, $active);
		$theme->set('sidebarTabs'	, $sidebarTabs);
		$theme->set('tabs'			, $tabs);
		$theme->set('params'		, $this->params);
		$theme->set('forms'		, $this->form);

		$template = 'admin/forms/' . $type;

		$contents = $theme->output($template);

		return $contents;
	}
}
