<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

if (isset($id) && $id) {
	// Get the category alias
    $user = EB::user($id);

	// Add the view to the list of titles
	$title[] = JString::ucwords(JText::_('COM_EASYBLOG_SH404_ROUTER_' . strtoupper($view)));
	$title[] = ucfirst($user->getAlias());

	shRemoveFromGETVarsList('view');
	shRemoveFromGETVarsList('layout');
	shRemoveFromGETVarsList('id');
}


if (!isset($id)) {
    // Add the view to the list of titles
    $title[] = JString::ucwords(JText::_('COM_EASYBLOG_SH404_ROUTER_' . strtoupper($view)));

    shRemoveFromGETVarsList('view');
}
