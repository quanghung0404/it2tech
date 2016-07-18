<?php

/** 
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Component Helper
jimport('joomla.application.component.helper');

class MusColHelperRoute
{

	function getAlbumRoute($id, $itemid = 0)
	{
		//Create the link
		$link = 'index.php?option=com_muscol&view=album&id='. $id;

		if( $itemid ) {
			$link .= '&Itemid='.$itemid;
		};

		return $link;
	}

	function getArtistRoute($id, $layout = 0, $itemid = 0)
	{
		//Create the link
		$link = 'index.php?option=com_muscol&view=artist&id='. $id;

		if( $layout ) {
			$link .= '&layout='.$layout;
		}
		
		if( $itemid ) {
			$link .= '&Itemid='.$itemid;
		}

		return $link;
	}

	function getSongRoute($id, $itemid = 0)
	{
		//Create the link
		$link = 'index.php?option=com_muscol&view=song&id='. $id;

		if( $itemid ) {
			$link .= '&Itemid='.$itemid;
		};

		return $link;
	}

}
?>
