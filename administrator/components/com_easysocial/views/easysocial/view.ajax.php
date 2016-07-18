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

FD::import( 'admin:/views/views' );

class EasySocialViewEasySocial extends EasySocialAdminView
{
	/**
	 * Displays the update version in a popbox modal
	 *
	 * @since	1.0
	 * @access	public
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function popboxUpdate()
	{
		$ajax 	= FD::ajax();

		$local 		= JRequest::getVar( 'local' );
		$online 	= JRequest::getVar( 'online' );

		$theme 		= FD::themes();
		$theme->set( 'local' , $local );
		$theme->set( 'online', $online );

		$contents 	= $theme->output( 'admin/easysocial/popbox.version.outdated' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Retrieves metadata about EasySocial
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getMetaData()
	{
		// Get the current version.
		$local = ES::getLocalVersion();
		$latest = ES::getOnlineVersion();
		$outdated = (version_compare($local, $latest)) === -1;

		$model = ES::model('News');
		$news = $model->getNews();

		if ($news->apps) {
			foreach ($news->apps as &$appItem) {
				$date = ES::date($appItem->updated);

				$appItem->lapsed = $date->toLapsed();
				$appItem->day = $date->format('d');
				$appItem->month = $date->format('M');
			}
		}

		$theme = FD::themes();
		$theme->set('items', $news->apps);
		$appNews = $theme->output('admin/news/apps');

		return $this->ajax->resolve($appNews, $local, $latest, $outdated);
	}

	/**
	 * Retrieves a list of countries
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCountries( $countries )
	{
		$ajax 	= FD::ajax();

		$result = array();
		foreach( $countries as $country )
		{
			$result[]	= $country->country;
		}

		// Get the table of list of countries
		$theme 		= FD::themes();
		$theme->set( 'countries'	, $countries );
		$content	= $theme->output( 'admin/easysocial/widget.map.table' );


		return $ajax->resolve( $result , $content );
	}

	/**
	 * Confirmation to purge cache
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function confirmPurgeCache()
	{
		$theme = ES::themes();
		$contents = $theme->output('admin/easysocial/dialog.purge.cache');

		return $this->ajax->resolve($contents);
	}
}
