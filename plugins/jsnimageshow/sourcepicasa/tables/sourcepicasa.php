<?php
/**
 * @version    $Id: default_profile_picasa.php 16082 2012-09-17 03:13:08Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class TableSourcePicasa extends JTable
{
	var $external_source_id 			= null;
	var $external_source_profile_title 	= null;
	var $picasa_username 				= null;
	var $picasa_thumbnail_size 			= null;
	var $picasa_image_size 				= null;

	function __construct(&$db)
	{
		parent::__construct('#__imageshow_external_source_picasa', 'external_source_id', $db);
	}

	function store($updateNulls = false)
	{
		$query = 'SELECT * FROM #__imageshow_external_source_picasa WHERE external_source_id ='.(int)$this->external_source_id;
		$this->_db->setQuery($query);
		$current = $this->_db->loadObject();
		$updateThumbnailSize = false;
		$updateImageSize 	 = false;

		if ($current)
		{
			if ($this->picasa_thumbnail_size && $this->picasa_thumbnail_size != $current->picasa_thumbnail_size) {
				$updateThumbnailSize = $this->picasa_thumbnail_size;
			}

			if ($this->picasa_image_size && $this->picasa_image_size != $current->picasa_image_size) {
				$updateImageSize = $this->picasa_image_size;
			}
		}

		if (parent::store($updateNulls = false))
		{
			// if thumbnail size and image size changed, upate the images link
			if (isset($updateImageSize)) {
				$this->updateImageSize($this->external_source_id, $updateImageSize);
			}

			if (isset($updateThumbnailSize)) {
				$this->updateThumbnailSize($this->external_source_id, $updateThumbnailSize);
			}
		} else {
			return false;
		}
		return true;
	}

	function updateImageSize($externalSourceId, $updateImageSize = 1024)
	{
		if (!$updateImageSize || !$externalSourceId) return false;

		$objJSNShowlist = JSNISFactory::getObj('classes.jsn_is_showlist');
		$objJSNImages	= JSNISFactory::getObj('classes.jsn_is_images');
		$showlists 		= $objJSNShowlist->getListShowlistBySource($externalSourceId, 'picasa');

		foreach ($showlists as $showlist)
		{
			$images = $objJSNImages->getImagesByShowlistID($showlist->showlist_id);

			if ($images)
			{
				foreach ($images as $image)
				{
					$patt = '/\/s(\d)*\//';
					$imageBig = preg_replace($patt, '/s'.$updateImageSize.'/', $image->image_big);
					$query = 'UPDATE #__imageshow_images
							  SET image_big = '.$this->_db->quote($imageBig).'
							  WHERE showlist_id ='. (int)$showlist->showlist_id .'
							  AND image_id = '.$this->_db->quote($image->image_id).'
							  LIMIT 1';
					$this->_db->setQuery($query);
					$this->_db->query();
				}
			}
		}
	}

	function updateThumbnailSize($externalSourceId, $updateThumbnailSize = 144)
	{
		if (!$updateThumbnailSize || !$externalSourceId) return false;

		$objJSNShowlist = JSNISFactory::getObj('classes.jsn_is_showlist');
		$objJSNImages	= JSNISFactory::getObj('classes.jsn_is_images');
		$showlists 		= $objJSNShowlist->getListShowlistBySource($externalSourceId, 'picasa');

		foreach ($showlists as $showlist)
		{
			$images = $objJSNImages->getImagesByShowlistID($showlist->showlist_id);

			if ($images)
			{
				foreach ($images as $image)
				{
					$patt = '/\/s(\d)*\//';
					$imageSmall = preg_replace($patt, '/s'.$updateThumbnailSize.'/', $image->image_small);

					$query = 'UPDATE #__imageshow_images
							  SET image_small = '.$this->_db->quote($imageSmall).'
							  WHERE showlist_id ='. (int)$showlist->showlist_id .'
							  AND image_id = '.$this->_db->quote($image->image_id).'
							  LIMIT 1';
					$this->_db->setQuery($query);
					$this->_db->query();
				}
			}
		}
	}
}
?>