<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class SocialAjax extends EasySocial
{
	private $commands = array();
	static $instance = null;

	public function addCommand($type, &$data)
	{
		$this->commands[] = array(
			'type' => $type,
			'data' =>& $data
		);

		return $this;
	}

	/**
	 * Creates a copy of it self and return to the caller.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialProfiler
	 *
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Resolve a given POSIX path.
	 *
	 * <code>
	 * <?php
	 * // This would translate to administrator/components/com_easysocial/controllers/fields.php
	 * FD::resolve( 'ajax:/admin/controllers/fields/renderSample' );
	 *
	 * // This would translate to components/com_easysocial/controllers/dashboard.php
	 * FD::resolve( 'ajax:/site/controllers/dashboard/someMethod' );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The posix path to lookup for.
	 * @return	string		The translated path
	 */
	public static function resolveNamespace($namespace)
	{
		// Get the request
		$input = JFactory::getApplication()->input;

		$parts = explode('/', $namespace);

		// Determine the location of the namespace
		$location = $parts[0];

		// Remove the location from parts.
		array_shift($parts);

		// Remove the method from the namespace
		$method = array_pop($parts);

		// Get the absolute path of the initial location
		$path = $location == 'admin' ? SOCIAL_ADMIN : SOCIAL_SITE;

		// Determine if this is a view or controller.
		if ($location == 'site' || $location == 'admin') {
				
			$glued = implode('/', $parts);

			if ($parts[0] == 'controllers') {
				$path = $path . '/' . $glued . '.php';
			} else {
				$path = $path . '/' . $glued . '/view.ajax.php';
			}
		}

		// If the location is meant for apps, we need to determine the correct path now.
		if ($location == 'apps') {

			// Whether this is a "user", "group", "event" app.
			$group = $parts[0];

			// The element of the app.
			$element = $parts[1];

			// Whether this request is made for controllers or views
			$type = $parts[2];

			// Don't know what this is
			$typeFile = $parts[3];

			// E.g: apps:/user/tasks/views/viewName/functionName
			if ($type == 'views') {
				$path = SOCIAL_APPS . '/' . $group . '/' . $element . '/views/' . $typeFile . '/view.ajax.php';
			}

			// E.g: apps:/user/tasks/controllers/tasks/functionName
			if ($type == 'controllers') {
				// Import dependencies.
				ES::import('admin:/includes/apps/dependencies');

				$path = SOCIAL_APPS . '/' . $group . '/' . $element . '/controllers/' . $typeFile . '.php';
			}
		}

		// If the location is meant for custom fields, we need to determine the correct path
		if ($location == 'fields') {
			
			// This is the field group. E.g: users , groups etc.
			$group = $parts[0];

			// This is the field element.
			$element = $parts[1];

			$path = SOCIAL_FIELDS . '/' . $group . '/' . $element . '/ajax.php';
		}

		// Get the arguments from the query string if there is any.
		$args = $input->get('args', '', 'default');

		// Check that the file exists.
		jimport('joomla.filesystem.file');
		
		$ajax = ES::ajax();

		if (!JFile::exists($path)) {
			$ajax->reject(JText::sprintf('The file %1s does not exist.', $namespace));
			return $ajax->send();
		}

		// Include the path.
		include_once($path);

		// Get the adapter to process.
		$adapter = self::getAdapter($location);
		$adapter->execute($namespace, $parts, $args, $method);

		// Terminate the output.
		$ajax->send();

		return $path;
	}

	/**
	 * Retrieves an ajax adapter so that it knows how to resolve the calls
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function getAdapter($location)
	{
		$file = __DIR__ . '/adapters/' . strtolower($location) . '.php';

		require_once($file);

		$className = 'SocialAjaxAdapter' . ucfirst($location);
		$adapter = new $className();

		return $adapter;
	}

	/* This will handle all ajax commands e.g. success/fail/script */
	public function __call($method, $args)
	{
		$this->addCommand($method, $args);

		return $this;
	}

	public function EasySocial($selector=null)
	{
		$chain = array();

		$this->addCommand('script', $chain);

		// Because we need to maintain the variable to be passed by reference,
		// we need to use an array instead as arguments.
		$js = FD::get( array( 'Javascript' , true ) , array( &$chain ) );

		if (isset($selector))
		{
			$js->EasySocial($selector);
		}
		else
		{
			$js->EasySocial;
		}

		return $js;
	}

	public function send()
	{
		header('Content-type: text/x-json; UTF-8');

		$json 		= FD::json();
		$callback 	= JRequest::getVar( 'callback' , '' );

		// Isolate PHP errors and send it as a notify command.
		$error_reporting = ob_get_contents();
		if (strlen(trim($error_reporting))) {
			$this->notify($error_reporting, 'debug');
		}

		ob_clean();

		// Process jsonp requests if necessary.
		if( $callback )
		{
			header('Content-type: application/javascript; UTF-8');
			echo $callback . '(' . $json->encode( $this->commands ) . ');';
			exit;
		}

		$transport = JRequest::getVar('transport');

		if ($transport=="iframe") {
			header('Content-type: text/html; UTF-8');
			echo '<textarea data-type="application/json" data-status="200" data-statusText="OK">' . $json->encode( $this->commands ) . '</textarea>';
			exit;
		}

		echo $json->encode( $this->commands );
		exit;
	}

	/**
	 * Processes an ajax call that is passed to the server. It is smart enough to decide which
	 * file would be responsible to keep these codes.
	 *
	 * @since	1.0
	 * @access	public
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function listen()
	{
		// Do not proceed if the request is not in ajax format.
		if ($this->doc->getType() != 'ajax') {
			return;
		}

		// Namespace format should be POSIX format.
		$namespace = $this->input->get('namespace', '', 'default');

		// Split the namespace
		$parts = explode(':/', $namespace);

		// Detect if the user passed in a protocol.
		$hasProtocol = count($parts) > 1;

		if (!$hasProtocol) {
			$namespace = 'ajax:/' . $namespace;
		}

		return ES::resolve($namespace);
	}
}
