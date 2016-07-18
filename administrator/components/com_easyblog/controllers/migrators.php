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

require_once(JPATH_COMPONENT . '/controller.php');

class EasyBlogControllerMigrators extends EasyBlogController
{
	public function purge()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'migrator' );

		$layout = $this->input->get('layout', '', 'cmd');

		$db 	= EB::db();
		$sql = $db->sql();

		$mapping = array('joomla' => 'com_content',
						'wordpressjoomla' => 'com_wordpress',
						'wordpress' => 'xml_wordpress',
						'k2' => 'com_k2',
						'zoo' => 'com_zoo',
						'blogger' => 'xml_blogger'
					);

		$component = '';

		if ($layout) {
			//let map the layout with component.
			if (isset($mapping[$layout]) && $mapping[$layout]) {
				$component = $mapping[$layout];
			}
		}

		if ($component) {
			// delete only associated records from the component.
			$query = 'delete from ' . $db->nameQuote( '#__easyblog_migrate_content' ) . ' where ' . $db->nameQuote('component') . ' = ' . $db->Quote($component);
		} else {
			// truncate all
			$query 	= 'TRUNCATE TABLE ' . $db->nameQuote( '#__easyblog_migrate_content' );
		}

		$db->setQuery( $query );

		$db->Query();

		$link = 'index.php?option=com_easyblog&view=migrators';
		if ($layout) {
			$link .= '&layout=' . $layout;
		}

		if( $db->getError() )
		{
			JFactory::getApplication()->redirect( $link , JText::_( 'COM_EASYBLOG_PURGE_ERROR') , 'error' );
		}

		JFactory::getApplication()->redirect( $link , JText::_( 'COM_EASYBLOG_PURGE_SUCCESS' ) );
	}
}
