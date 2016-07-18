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

jimport('joomla.filesystem.file');

class K2ViewProfile extends SocialAppsView
{
	/**
	 * Checks if K2 exists on the site 
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function exists()
	{
		$k2File = JPATH_ROOT . '/components/com_k2/helpers/route.php';

		if (!JFile::exists($k2File)) {
			return false;
		}

		require_once($k2File);
	}

	/**
	 * Displays the application output in the canvas.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user id that is currently being viewed.
	 */
	public function display( $userId = null , $docType = null )
	{
		// Get the app params
		$params = $this->app->getParams();

		// Get the blog model
		$total = (int) $params->get( 'total' , $params->get( 'total' , 5 ) );

		// Retrieve a list of k2 items
		$model = $this->getModel( 'Items' );
		$items = $model->getItems( $userId , $total );

		$user = ES::user($userId);

		$this->format($items ,$params);

		$this->set('user', $user);
		$this->set('items', $items);

		echo parent::display('profile/default');
	}

	private function format(&$items, $params)
	{
		$this->exists();

		if (!$items) {
			return;
		}

		// Add K2's table path
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_k2/tables');

		foreach($items as $item) {
			$category = JTable::getInstance('K2Category', 'Table');
			$category->load($item->catid);

			$item->category = $category;
			$item->permalink = K2HelperRoute::getItemRoute( $item->id . ':' . $item->alias , $item->catid );
			$item->category->permalink = K2HelperRoute::getCategoryRoute( $category->id . ':' . $category->alias );
			$item->content = empty( $item->introtext ) ? $item->fulltext : $item->introtext;

			$titleLength = $params->get( 'title_length' );
			$contentLength = $params->get( 'content_length' );

			if ($titleLength) {
				$item->title = JString::substr($item->title, 0, $titleLength);
			}

			if ($contentLength) {
				$item->content = JString::substr(strip_tags($item->content), 0, $contentLength);
			}
		}
	}
}
