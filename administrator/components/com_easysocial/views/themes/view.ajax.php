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

// Include main views file.
FD::import( 'admin:/views/views' );

class EasySocialViewThemes extends EasySocialAdminView
{
	public function compiler() {

		$ajax = FD::ajax();

		$location = JRequest::getCmd('location');
		$name     = JRequest::getCmd('name');
		$override = JRequest::getBool('override', false);

		// Get stylesheet
		$stylesheet = FD::stylesheet($location, $name, $override);
		$uuid = uniqid();

		$theme = FD::themes();
		$theme->set('uuid'      , $uuid);
		$theme->set('location'  , $location);
		$theme->set('name'      , $name);
		// TODO: Find a proper way to do this
		$theme->set('element'   , ucwords(str_ireplace('_', ' ', str_ireplace('mod_easysocial_', '', $name))));
		$theme->set('override'  , $override);
		$theme->set('stylesheet', $stylesheet);
		$theme->set('type'      , $stylesheet->type());
		$theme->set('manifest'  , $stylesheet->manifest());

		// Also pass in server memory limit.
		$memory_limit = ini_get('memory_limit');
		$memory_limit = FD::math()->convertBytes($memory_limit) / 1024 / 1024;
		$theme->set('memory_limit', $memory_limit);

		$html = $theme->output('admin/themes/compiler/form');

		$ajax->resolve($html);
		$ajax->send();
	}

	public function section() {

		$ajax = FD::ajax();

		// TODO: Exact section copy. Refactor.
		$location = JRequest::getCmd('location');
		$name     = JRequest::getCmd('name');
		$override = JRequest::getBool('override', false);
		$section  = JRequest::getVar('section');

		// Get stylesheet
		$stylesheet = FD::stylesheet($location, $name, $override);

		$theme = FD::themes();
		$theme->set('location'  , $location);
		$theme->set('name'      , $name);
		// TODO: Find a proper way to do this
		$theme->set('element'   , ucwords(str_ireplace('_', ' ', str_ireplace('mod_easysocial_', '', $name))));
		$theme->set('override'  , $override);
		$theme->set('section'   , $section);
		$theme->set('stylesheet', $stylesheet);

		// Also pass in server memory limit.
		$memory_limit = ini_get('memory_limit');
		$memory_limit = FD::math()->convertBytes($memory_limit) / 1024 / 1024;
		$theme->set('memory_limit', $memory_limit);

		$html = $theme->output('admin/themes/compiler/section');
		/* Exact section copy */

		$ajax->resolve($html);
		$ajax->send();
	}

	public function status($section, $stylesheet) {

		$status = $stylesheet->status($section);

		$theme = FD::themes();
		$theme->set('stylesheet', $stylesheet);
		$theme->set('section'   , $section);
		$theme->set('status'    , $status);

		$html = $theme->output('admin/themes/compiler/status');

		return $html;
	}

	public function imports($section, $stylesheet) {

		$imports = $stylesheet->imports($section);

		$theme = FD::themes();
		$theme->set('stylesheet', $stylesheet);
		$theme->set('section'   , $section);
		$theme->set('imports'   , $imports);

		$html = $theme->output('admin/themes/compiler/imports');

		return $html;
	}

	public function compile($section, $stylesheet, $task) {

		$ajax = FD::ajax();

		$failed  = $task->failed;

		$status  = $this->status($section, $stylesheet);
		$imports = $this->imports($section, $stylesheet);
		$task    = $task->toArray();

		if (!$failed) {
			$ajax->resolve(array(
				'status'  => $status,
				'imports' => $imports,
				'task'    => $task
			));
		} else {
			$ajax->reject(array(
				'task' => $task
			));
		}

		$ajax->send();
	}

	public function minify($section, $stylesheet, $task) {

		$ajax = FD::ajax();

		$failed  = $task->failed;

		$status  = $this->status($section, $stylesheet);
		$imports = $this->imports($section, $stylesheet);
		$task    = $task->toArray();

		if (!$failed) {
			$ajax->resolve(array(
				'status'  => $status,
				'imports' => $imports,
				'task'    => $task
			));
		} else {
			$ajax->reject(array(
				'task' => $task
			));
		}

		$ajax->send();
	}

	public function build($stylesheet, $task) {

		$ajax = FD::ajax();

		$failed  = $task->failed;

		$status = $stylesheet->status();

		$theme = FD::themes();
		$theme->set('stylesheet', $stylesheet);
		$theme->set('status'    , $status);

		$status = $theme->output('admin/themes/compiler/status');

		$task    = $task->toArray();

		if (!$failed) {
			$ajax->resolve(array(
				'status' => $status,
				'task'   => $task
			));
		} else {
			$ajax->reject(array(
				'task' => $task
			));
		}

		$ajax->send();
	}

	public function purge($task) {

		$ajax = FD::ajax();

		$log = $task->toArray();

		if ($task->failed) {
			$ajax->reject($log);
		} else {
			$ajax->resolve($log);
		}

		$ajax->send();
	}
}
