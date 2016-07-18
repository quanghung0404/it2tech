<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  TPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import necessary Joomla libraries
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Javascript Compression engine
 *
 * @package     TPLFramework
 * @subpackage  Plugin
 * @since       1.0.0
 */
abstract class JSNMobilizeCompressJs
{
	/**
	 * Method to parse all link to css files from the html markup
	 * and compress it
	 *
	 * @param   string  $htmlMarkup  HTML Content to response to browser
	 * @return  void
	 */
	public static function compress($htmlMarkup)
	{
		// Get object for working with URI
		$uri = JUri::getInstance();

		// Generate link prefix if current scheme is HTTPS
		$prefix = '';

		if ($uri->getScheme() == 'https')
		{
			$prefix = $uri->toString(array('scheme', 'host', 'port'));
		}

		// Initialize variables
		$groupIndex = 0;
		$groupType = 'default';
		$groupFiles = array();
		$compress = array();

		// Sometime, script file need to be stored in the original location and file name
		$document = JFactory::getDocument();
		$leaveAlone = preg_split('/[\r\n]+/', $document->params->get('compressionExclude'));

		// We already know some files must be excluded from compression
		$leaveAlone[] = 'modal.js';
		$leaveAlone[] = 'tiny_mce.js';
		$leaveAlone[] = 'tinymce.min.js';

		// Parse script tags
		foreach (explode('>', $htmlMarkup[0]) as $line)
		{
			$attributes = JSNMobilizeCompressHelper::parseAttributes($line);

			// Set default group
			$attributes['group'] = 'default';

			// Skip if not have attibute src
			if (!isset($attributes['src']))
			{
				continue;
			}

			// Add to result list if this is external file
			if (!($isInternal = JUri::isInternal($attributes['src'])) OR strpos($attributes['src'], '//') === 0)
			{
				// Add collected files to compress list
				if (!empty($groupFiles))
				{
					$compress[] = array(
						'files' => $groupFiles[$groupIndex],
						'group' => $groupType
					);

					$groupFiles = array();
				}

				$compress[] = array('src' => $attributes['src']);

				continue;
			}

			// Add to result list if this is dynamic generation content
			$questionPos = false;

			if (($questionPos = strpos($attributes['src'], '?')) !== false)
			{
				$isDynamic = (substr($attributes['src'], $questionPos - 4, 4) == '.php');
				$path = JSNMobilizeCompressHelper::getFilePath(substr($attributes['src'], 0, $questionPos));

				// Check if this is a dynamic generation content
				if (!$isDynamic AND JUri::isInternal($attributes['src']))
				{
					$isDynamic = !is_file($path);
				}

				if ($isDynamic)
				{
					// Add collected files to compress list
					if (!empty($groupFiles))
					{
						$compress[] = array(
							'files' => $groupFiles[$groupIndex],
							'group' => $groupType
						);

						$groupFiles = array();
					}

					$compress[] = array('src' => $attributes['src']);

					continue;
				}
			}

			// Check if reserving script file name is required
			$scriptName = basename($questionPos !== false ? $path : $attributes['src']);

			if (in_array($scriptName, $leaveAlone))
			{
				$attributes['group'] = 'reserve|' . $scriptName;
			}

			// Create new compression group if reserving script file name is required
			if ($attributes['group'] != $groupType)
			{
				// Add collected files to compress list
				if (isset($groupFiles[$groupIndex]) AND !empty($groupFiles[$groupIndex]))
				{
					$compress[] = array(
						'files' => $groupFiles[$groupIndex],
						'group' => $groupType
					);
				}

				// Increase index number of the group
				$groupIndex++;
				$groupType = $attributes['group'];
			}

			// Initial group
			if (!isset($groupFiles[$groupIndex]))
			{
				$groupFiles[$groupIndex] = array();
			}

			$src = $attributes['src'];
			$queryStringIndex = strpos($src, '?');

			if ($queryStringIndex !== false)
			{
				$src = substr($src, 0, $queryStringIndex);
			}

			// Add file to the group
			$groupFiles[$groupIndex][] = $src;
		}

		// Add collected files to result list
		if (isset($groupFiles[$groupIndex]) AND !empty($groupFiles[$groupIndex]))
		{
			$compress[] = array(
				'files' => $groupFiles[$groupIndex],
				'group' => $groupType
			);
		}

		// Initial compress result
		$compressResult = array();

		// Get template details
		$templateName = JFactory::getApplication()->getTemplate();
		$cacheDirectory = 'cache';
		// Generate path to store compressed files
		if (!preg_match('#^(/|\\|[a-z]:)#i', $cacheDirectory))
		{
			$compressPath = JPATH_ROOT . '/' . rtrim($cacheDirectory, '\\/');
		}
		else
		{
			$compressPath = rtrim($cacheDirectory, '\\/');
		}

		$compressPath = $compressPath . '/' . $templateName . '/';

		// Create directory if not exists
		if (!is_dir($compressPath))
		{
			JFolder::create($compressPath);
		}

		// Loop to each compress element to compress file
		foreach ($compress AS $group)
		{
			// Ignore compress when group is a external file
			if (isset($group['src']))
			{
				$compressResult[] = sprintf('<script src="%s" type="text/javascript"></script>', $group['src']);

				continue;
			}

			// Check if reserving script file name is required
			if (isset($group['group']) AND preg_match('/^reserve\|(.+)$/', $group['group']))
			{
				$compressResult[] = sprintf('<script src="%s" type="text/javascript"></script>', $group['files'][0]);

				continue;
			}

			// Generate compress file name
			$compressFile = md5(implode('', $group['files'])) . '.js';
			$lastModified = 0;
			$splittedFiles = array();

			// Check last modified time for each file in the group
			foreach ($group['files'] AS $file)
			{
				$path = JSNMobilizeCompressHelper::getFilePath($file);
				$lastModified = (is_file($path) && filemtime($path) > $lastModified) ? filemtime($path) : $lastModified;
			}

			// Compress group when expired
			if (!is_file($compressPath . $compressFile) OR filemtime($compressPath . $compressFile) < $lastModified)
			{
				// Preset compression buffer
				$buffer = '';

				// Preset some variables to hold compression status
				$processedFiles = array();
				$maxFileSize = 1024 * (int) $document->params->get('maxCompressionSize');
				$currentSize = 0;

				// Read content of each file and write it to the cache file
				foreach ($group['files'] AS $file)
				{
					$filePath = JSNMobilizeCompressHelper::getFilePath($file);

					// Skip when cannot access to file
					if (!is_file($filePath) OR !is_readable($filePath))
					{
						continue;
					}

					// Prepend path to source file
					$source = ($currentSize == 0 ? '' : "\n\n")
						. '/* FILE: ' . str_replace(str_replace('\\', '/', JPATH_ROOT), '', str_replace('\\', '/', $filePath)) . ' */'
						. "\n" . JFile::read($filePath);

					// Get length of processed content
					$length = strlen($source);

					if ($length > $maxFileSize OR ($currentSize + $length) > $maxFileSize)
					{
						// Write buffer to cache file
						JFile::write($compressPath . $compressFile, $buffer);

						// Rename created cache file
						if ($currentSize > 0)
						{
							$newFileName = md5(implode('', $processedFiles)) . '.js';
							JFile::move($compressPath . $compressFile, $compressPath . $newFileName);

							// Store splitted file URL for later reference
							$splittedFiles[] = $prefix . str_replace(str_replace('\\', '/', JPATH_ROOT), JUri::root(true), str_replace('\\', '/', $compressPath)) . $newFileName;
						}

						// Reset compression buffer
						$buffer = '';

						// Reset current file size
						$currentSize = $length;

						$processedFiles = array($filePath);
					}
					else
					{
						// Update current file size
						$currentSize += $length;

						$processedFiles[] = $filePath;
					}

					// Append processed content to buffer
					$buffer .= $source . ";\n";
				}

				// Write buffer to cache file
				JFile::write($compressPath . $compressFile, $buffer);

				// Prepend splitted compress files into trackable compress file
				if (count($splittedFiles))
				{
					for ($n = count($splittedFiles), $i = $n - 1; $i >= 0; $i--)
					{
						JSNMobilizeCompressHelper::prependIntoFile("// Include: {$splittedFiles[$i]}" . ($i + 1 < $n ? "\n" : "\n\n"), $compressPath . $compressFile);
					}
				}
			}
			else
			{
				// Read compressed file for list of splitted file
				$include = JFile::read($compressPath . $compressFile);
				$include = substr($include, 0, strpos($include, "\n\n"));

				// Parse splitted compress file
				foreach (explode("\n", $include) AS $line)
				{
					if (strpos($line, '// Include: ') === 0)
					{
						$splittedFiles[] = str_replace('// Include: ', '', $line);
					}
				}
			}

			// Load splitted compress file
			if (count($splittedFiles))
			{
				foreach ($splittedFiles AS $file)
				{
					$compressResult[] = sprintf('<script src="%s" type="text/javascript"></script>', $file);
				}
			}

			// Add compressed file to the compress result list
			$compressUrl = str_replace(str_replace('\\', '/', JPATH_ROOT), JUri::root(true), str_replace('\\', '/', $compressPath)) . $compressFile;
			$compressResult[] = sprintf('<script src="%s" type="text/javascript"></script>', $prefix . $compressUrl);
		}

		return implode("\r\n", $compressResult);
	}
}
