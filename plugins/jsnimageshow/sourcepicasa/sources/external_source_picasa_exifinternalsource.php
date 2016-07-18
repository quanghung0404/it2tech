<?php
/**
 * @version    $Id: imageshow.php 16609 2012-10-02 09:23:05Z haonv $
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

jimport('joomla.filesystem.file');
class JSNExternalSourcePicasaExifInternalSource
{
	function __construct()
	{
	}

	function renderData($exifData)
	{
		$tmpExifData = array();
		if (count($exifData))
		{
			if (isset($exifData['model']) && is_string($exifData['model']) && $exifData['model'] != '' && isset($exifData['make']) && is_string($exifData['make']) && $exifData['make'] != '')
			{
				$tmpExifData [] = @$exifData['make'].'/'.@$exifData['model'];
			}
			if (isset($exifData['exposure']) && is_string($exifData['exposure']) && $exifData['exposure'] != '')
			{
				$tmpExifData [] = '1/'.$this->convertDecimalToFraction($exifData['exposure']);
			}
			if (isset($exifData['fstop']) && is_string($exifData['fstop']) && $exifData['fstop'] != '')
			{
				$tmpExifData [] = 'f/'.$exifData['fstop'];
			}
			if (isset($exifData['focallength']) && is_string($exifData['focallength']) && $exifData['focallength'] != '')
			{
				$tmpExifData [] = (int) $exifData['focallength'].'mm';
			}
			if (isset($exifData['iso']) && is_string($exifData['iso']) && $exifData['iso'] != '')
			{
				$tmpExifData [] = 'ISO-'.(int) $exifData['iso'];
			}
			if (isset($exifData['flash']) && is_string($exifData['flash']) && $exifData['flash'] != '')
			{
				if (@$exifData['flash'] == 'true')
				{
					$tmpExifData [] = 'flash';
				}
				else
				{
					$tmpExifData [] = 'no flash';
				}
			}
			if (count($tmpExifData))
			{
				return implode(', ', $tmpExifData);
			}
			else
			{
				return '';
			}
		}
		return '';
	}

	function convertDecimalToFraction($decimal)
	{
		$data = 1/$decimal;
		return ceil($data);
	}
}