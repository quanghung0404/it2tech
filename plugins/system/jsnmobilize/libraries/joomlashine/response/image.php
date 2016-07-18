<?php
/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla libraries
jimport('joomla.filesystem.file');
jimport('joomla.installer.helper');

/**
 * Class for mass resizing images loaded in HTML response body.
 *
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @since       1.0.0
 */
class JSNResponseImage
{
	/**
	 * Initialize all img tags to schedule image resizing via AJAX request.
	 *
	 * @param   string   $path            Absolute path to the directory for storing optimized image.
	 * @param   integer  $maxWidth        Maximum allowed image width.
	 * @param   string   $responseBody    Generated HTML response body.
	 * @param   boolean  $setNewResponse  Set new response body directly or return as string.
	 *
	 * @return  mixed
	 */
	public static function init($path, $maxWidth, $responseBody = '', $setNewResponse = true)
	{

		if (!JFolder::exists($path))
		{
			JFolder::create($path);
		}
		// Initialize reponse body
		!empty($responseBody) OR $responseBody = JResponse::getBody();

		// Get all img tags loaded in response body
		if (preg_match_all('#<img([^>]+)? src=("|\')([^"\']+)("|\')([^>]+)?>#i', $responseBody, $matches, PREG_SET_ORDER))
		{

			foreach ($matches AS $match)
			{
				// Image already optimized
				$replace = '';
				if (is_readable($imagePath = $path . DS . $maxWidth . DS . basename($match[3])))
				{
					// Get link to optimized image
					$link = JURI::root(true) . str_replace(
						str_replace(array('/', '\\'), array('/', '/'), JPATH_ROOT),
						'',
						str_replace(array('/', '\\'), array('/', '/'), $path . DS . $maxWidth . DS . basename($match[3]))
					);

					// Get image dimension
					$imageSize = @getimagesize($imagePath);

					// Generate replacement
					$replace = self::updateImageDimension(
						str_replace('src=' . $match[2] . $match[3] . $match[4], 'src=' . $match[2] . $link . $match[4], $match[0]),
						$imageSize[0],
						$imageSize[1],
						$match[2]
					);

					// Update response body
					$responseBody = str_replace($match[0], $replace, $responseBody);
				}
				// Image not optimized
				else
				{
					// Get image path
					$imagePath = str_replace(
						array(JURI::root(), JURI::root(true)),
						array(JPATH_ROOT, JPATH_ROOT),
						$match[3]
					);

					// Download remote image
					if (substr($imagePath, 0, 5) == 'http:' OR substr($imagePath, 0, 6) == 'https:')
					{

						if (!($tmp = JInstallerHelper::downloadPackage($imagePath)))
						{
							continue;
						}

						$imagePath = $path . DS . basename($tmp);


						// Move downloaded image to the directory for storing optimized image files
						JFile::move(JFactory::getConfig()->get('tmp_path') . DS . $tmp, $imagePath);

					}

					// Get image dimension
					$imageSize = @getimagesize($imagePath);

					if ($imageSize[0] > $maxWidth)
					{

						// Replace remote image with local one if necessary
						if (strpos($match[3], JURI::root()) !== 0 AND strpos($match[3], JURI::root(true)) !== 0)
						{

							// Generate replacement
							$tmpPath  = str_replace(JPATH_ROOT, JURI::root(true), $imagePath);
							$replace[3] = str_replace('\\', '/', $tmpPath);

							$replace[0] = str_replace($match[3], $replace[3], $match[0]);
							// Update response body

							$responseBody = str_replace($match[0], $replace[0], $responseBody);

							// Update matching result
							$match[0] = $replace[0];
							$match[3] = $replace[3];

						}

						// Update response body

						self::updateResponseBody($responseBody, $match, $path, $maxWidth, $imageSize);
					}
					elseif (strpos($match[0], 'width=') === false OR strpos($match[0], 'height=') === false)
					{
						// Generate replacement
						$replace = self::updateImageDimension($match[0], $imageSize[0], $imageSize[1], $match[2]);

						// Update response body
						$responseBody = str_replace($match[0], $replace, $responseBody);
					}
				}
			}
		}

		// Set new response body directly or return it?
		return $setNewResponse ? JResponse::setBody($responseBody) : $responseBody;
	}

	/**
	 * Set image dimension to img tag.
	 *
	 * @param   string   $imgTag  Original img tag.
	 * @param   integer  $width   Image width to set to img tag.
	 * @param   integer  $height  Image height to set to img tag.
	 * @param   string   $quote   Quotation mark (either " or ') to wrap around attribute value.
	 *
	 * @return  string
	 */
	protected static function updateImageDimension($imgTag, $width, $height, $quote = '"')
	{

		foreach (array('width' => $width, 'height' => $height) AS $attr => $value)
		{
			if (strpos($imgTag, "{$attr}=") === false)
			{
				$imgTag = str_replace('<img', "<img {$attr}=" . $quote . $value . $quote, $imgTag);
			}
			else
			{
				$imgTag = preg_replace("/{$attr}={$quote}[^\\{$quote}]*{$quote}/", "{$attr}=" . $quote . $value . $quote, $imgTag);
			}
		}

		return $imgTag;
	}

	/**
	 * Update HTML response body.
	 *
	 * @param   string   &$responseBody  Generated HTML response body.
	 * @param   string   $match          Matched img tag.
	 * @param   string   $path           Absolute path to the directory for storing optimized image.
	 * @param   integer  $maxWidth       Maximum allowed image width.
	 * @param   array    $imageSize      Dimension of original image: array(0 => width, 1 => height)
	 *
	 * @return  void
	 */
	protected static function updateResponseBody(&$responseBody, $match, $path, $maxWidth, $imageSize)
	{
		// Shorten destination path
		$path = str_replace(
			str_replace(array('/', '\\'), DS, JPATH_ROOT) . DS,
			'',
			str_replace(array('/', '\\'), DS, $path)
		);
		// Generate image optimization link
		$link = trim(JURI::root(), '/')
			. '/plugins/system/jsnmobilize/libraries/joomlashine/response/image/resizer.php?src='
			. $match[3] . '&width=' . $maxWidth . '&dest=' . $path . '&return=uri';

		// Calculate optimized image dimension
		$imageSize[1] = round(($maxWidth / $imageSize[0]) * $imageSize[1]);
		$imageSize[0] = $maxWidth;

		// Generate replacement
		$replace = str_replace(
			'src=' . $match[2] . $match[3] . $match[4],
			'_src=' . $match[2] . $link . $match[4],
			self::updateImageDimension($match[0], $imageSize[0], $imageSize[1], $match[2])
		);
		//var_dump($replace);
		// Manipulate attributes
		foreach (array('alt' => JText::_('JSN_MOBILIZE_IMAGE_LOADING'), 'class' => 'jsn-mobilize-image-loading') AS $attr => $value)
		{
			if (strpos($replace, "{$attr}=") === false)
			{
				$replace = str_replace('<img', "<img {$attr}=" . $match[2] . $value . $match[4], $replace);
			}
			else
			{
				$replace = str_replace("{$attr}=", "{$attr}=" . $match[2] . $value . $match[4] . "_{$attr}=", $replace);
			}
		}

		// Update response body
		$responseBody = str_replace($match[0], $replace, $responseBody);
	}
}
