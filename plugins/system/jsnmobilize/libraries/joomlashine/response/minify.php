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

// Import Joomla file library
jimport('joomla.filesystem.file');

/**
 * Class for minifying assets loaded in HTML response body.
 *
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @since       1.0.0
 */
class JSNResponseMinify
{
	/**
	 * Minify all stylesheets found in response body.
	 *
	 * @param   string   $path            Absolute path to the directory for storing minified stylesheet.
	 * @param   string   $responseBody    Generated HTML response body.
	 * @param   boolean  $setNewResponse  Set new response body directly or return as string.
	 *
	 * @return  mixed
	 */
	public static function css($path, $responseBody = '', $setNewResponse = true)
	{
		// Initialize reponse body
		!empty($responseBody) OR $responseBody = JResponse::getBody();

		// Get all stylesheets loaded in reponse body
		if (preg_match_all('#[\r\n\s\t]*<link([^>]+)? type=("|\')text/css("|\')([^>]+)?>#i', $responseBody, $matches, PREG_SET_ORDER))
		{
			// Group stylesheet by media type
			foreach ($matches AS $match)
			{
				if (strpos($match[0], ' href=') === false)
				{
					continue;
				}

				// Get stylesheet media type
				if (preg_match('/media=("|\')([^"\']+)("|\')/i', $match[0], $media))
				{
					$media = $media[2];
				}
				else
				{
					$media = 'all';
				}

				// Get link to stylesheet
				if (preg_match('/href=("|\')([^"\']+)("|\')/i', $match[0], $link))
				{
					// Store stylesheet info for minify later
					$stylesheets[$media][$match[0]] = $link[2];
				}
			}
		}

		// Check if minification file already exists
		isset($stylesheets) OR $stylesheets = array();

		foreach ($stylesheets AS $media => $files)
		{
			// Generate minification file path
			$minifiedFile = $path . DS . 'css_' . md5(json_encode($files)) . '.php';

			if (is_readable($minifiedFile))
			{
				// Generate replacement
				$replace = str_replace('\\', '/', str_replace(JPATH_ROOT, JURI::root(true), $minifiedFile));
				$replace = '<link media="' . $media . '" type="text/css" href="' . $replace . '" rel="stylesheet" />';

				// Update response body
				self::updateResponseBody($responseBody, $replace, array_keys($files));

				// Do not minify these stylesheets again
				unset($stylesheets[$media]);
			}
		}

		if (count($stylesheets))
		{
			// Read stylesheets
			foreach ($stylesheets AS $media => $files)
			{
				foreach ($files AS $tag => $file)
				{
					if (substr($file, 0, 1) == '/')
					{
						// Get absolute file path
						$file = str_replace(JURI::root(true), JPATH_ROOT, $file);

						if (!($content = JFile::read($file)))
						{
							// Convert to full file URI
							$file = JURI::root() . $file;
						}
					}

					// If remote file, get its content via HTTP client
					if (substr($file, 0, 5) == 'http:' OR substr($file, 0, 6) == 'https:')
					{
						$http = new JHttp;
						$content = $http->get($file);
						if (!empty($content->body))
						{
							$content = $content->body;
						}
					}
					//	$JVersion = new JVersion;
					//	$JVersion = $JVersion->getShortVersion();

					// Look for all relative path in file content
					if (preg_match_all('/url\(("|\')?([^"\'\)]+)("|\')?\)/i', $content, $matches, PREG_SET_ORDER))
					{
						foreach ($matches AS $match)
						{
							// Preset replacement
							$replace = (substr($file, 0, 5) == 'http:' OR substr($file, 0, 6) == 'https:')
								? dirname($file)
								: dirname(str_replace(JPATH_ROOT, JURI::root(true), $file));

							if (substr($match[2], 0, 1) == '.')
							{
								foreach (explode('/', $match[2]) AS $level)
								{
									if ($level == '..')
									{
										$replace = dirname($replace);
									}
									elseif ($level != '.')
									{
										$replace .= "/{$level}";
									}
								}
							}
							elseif (!preg_match('#^(https?://|/)#i', $match[2]))
							{
								$replace .= "/{$match[2]}";
							}

							// Replace relative path with absolute equivalent
							$content = str_replace($match[0], str_replace($match[2], $replace, $match[0]), $content);
						}
					}

					// Store file content for combine later
					$stylesheets[$media][$tag] = $content;
				}
			}

			// Combine files and set compress header for combined file
			$minified = self::process($path, 'css', $stylesheets, true);

			// Update response body
			foreach ($stylesheets AS $media => $files)
			{
				// Generate replacement
				$replace = str_replace('\\', '/', str_replace(JPATH_ROOT, JURI::root(true), $minified[$media]));
				$replace = '<link media="' . $media . '" type="text/css" href="' . $replace . '" rel="stylesheet" />';

				// Update response body
				self::updateResponseBody($responseBody, $replace, array_keys($files));
			}
		}

		// Set new response body directly or return it?
		return $setNewResponse ? JResponse::setBody($responseBody) : $responseBody;
	}

	/**
	 * Minify all Javascript files found in response body.
	 *
	 * @param   string   $path            Absolute path to the directory for storing minified script.
	 * @param   string   $responseBody    Generated HTML response body.
	 * @param   boolean  $setNewResponse  Set new response body directly or return as string.
	 *
	 * @return  mixed
	 */
	public static function js($path, $responseBody = '', $setNewResponse = true)
	{
		// Initialize reponse body
		!empty($responseBody) OR $responseBody = JResponse::getBody();

		// Get all script files loaded in reponse body
		if (preg_match_all('#[\r\n\s\t]*<script([^>]+)? type=("|\')text/javascript("|\')([^>]+)?>([\s\t\r\n]+)?</script>#i', $responseBody, $matches, PREG_SET_ORDER))
		{
			foreach ($matches AS $match)
			{
				if (strpos($match[0], ' src=') === false)
				{
					continue;
				}

				// Get link to script file
				if (preg_match('/src=("|\')([^"\']+)("|\')/i', $match[0], $link))
				{
					// Store script file info for minify later
					$scripts[$match[0]] = $link[2];
				}
			}
		}

		// Check if minification file already exists
		isset($scripts) OR $scripts = array();

		if (count($scripts))
		{
			// Generate minification file path
			$minifiedFile = $path . DS . 'js_' . md5(json_encode($scripts)) . '.php';

			if (is_readable($minifiedFile))
			{
				// Generate replacement
				$replace = str_replace('\\', '/', str_replace(JPATH_ROOT, JURI::root(true), $minifiedFile));
				$replace = '<script type="text/javascript" src="' . $replace . '"></script>';

				// Update response body
				self::updateResponseBody($responseBody, $replace, array_keys($scripts));

				// Do not minify these stylesheets again
				unset($scripts);
			}
		}

		if (isset($scripts))
		{
			// Combine files and set compress header for combined file
			$minified = self::process($path, 'js', array(0 => $scripts));

			// Generate replacement
			$replace = str_replace('\\', '/', str_replace(JPATH_ROOT, JURI::root(true), $minified[0]));
			$replace = '<script type="text/javascript" src="' . $replace . '"></script>';

			// Update response body
			self::updateResponseBody($responseBody, $replace, array_keys($scripts));
		}

		// Set new response body directly or return it?
		return $setNewResponse ? JResponse::setBody($responseBody) : $responseBody;
	}

	/**
	 * Combine files and set compress header for combined file.
	 *
	 * Below is an example of <b>$files</b> parameter to minify some stylesheets:
	 *
	 * <pre>array(
	 *     'screen' => array('screen1.css', 'screen2.css'),
	 *     'print' => array('print1.css', 'print2.css')
	 * )</pre>
	 *
	 * And the return value after execution this method is:
	 *
	 * <pre>array(
	 *     'screen' => 'absolute/path/to/combined/file',
	 *     'print' => 'absolute/path/to/combined/file'
	 * )</pre>
	 *
	 * For combine Javascript files, the <b>$files</b> parameter look like below:
	 *
	 * <pre>array(
	 *     0 => array('script1.js', 'script2.js', 'script3.js', 'script4.js')
	 * )</pre>
	 *
	 * @param   string   $path       Absolute path to the directory for storing minified file.
	 * @param   string   $type       Either 'css' or 'js'.
	 * @param   array    $list       Array of files to combine.
	 * @param   boolean  $isContent  Set to true if $files contains file content instead of file path.
	 *
	 * @return  array
	 */
	protected static function process($path, $type, $list, $isContent = false)
	{
		// Combine file content
		foreach ($list AS $media => $files)
		{
			foreach ($files AS $file)
			{
				isset($minified[$media]) OR $minified[$media] = '';

				if ($isContent)
				{
					$tmp = $file;
				}
				else
				{
					// Read file content
					if (substr($file, 0, 1) == '/')
					{
						// Get absolute file path
						$file = str_replace(JURI::root(true), JPATH_ROOT, $file);

						if (!($tmp = JFile::read($file)))
						{
							// Convert to full file URI
							$file = JURI::root() . $file;
						}
					}

					// If remote file, get its content via HTTP client
					if (substr($file, 0, 5) == 'http:' OR substr($file, 0, 6) == 'https:')
					{
						$http = new JHttp;
						$tmp = $http->get($file);
						if (!empty($tmp->body))
						{
							$tmp = $tmp->body;
						}
					}
				}

				$minified[$media] .= "\n{$tmp}";
			}
		}

		// Save combined file
		foreach ($list AS $media => $files)
		{
			// Generate minification file path
			$minifiedFile = $path . DS . $type . '_' . md5(json_encode($files)) . '.php';

			// Add header to compress asset file
			$minified[$media] = '<?php
// Initialize ob_gzhandler function to send and compress data
ob_start("ob_gzhandler");

// Send the requisite header information and character set
header("content-type: ' . ($type == 'js' ? 'text/javascript' : 'text/css') . '; charset: UTF-8");

// Check cached credentials and reprocess accordingly
header("cache-control: must-revalidate");

// Set variable for duration of cached content
$offset = 365 * 60 * 60;

// Set variable specifying format of expiration header
$expire = "expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";

// Send cache expiration header to the client broswer
header($expire);
?>
' . self::cleanCode($minified[$media], $type);

			// Write minification file
			JFile::write($minifiedFile, $minified[$media]);

			// Store path to minification file
			$minified[$media] = $minifiedFile;
		}

		return $minified;
	}

	/**
	 * Clean Javascript or CSS source code.
	 *
	 * @param   string  $code  The source code to minify.
	 * @param   string  $type  The type of code, 'css' or 'js'.
	 *
	 * @return  string  The minified code.
	 */
	protected static function cleanCode($code, $type)
	{
		// Initialize variable
		$minified = $code;

		// Replace multiple new-line with just one
		$minified = preg_replace('/[\r\n]{2,99}/', "\n", $minified);

		// Replace multiple white-space with just one
		$minified = preg_replace('/[^\S\n]{2,99}/', ' ', $minified);

		// Minify CSS code
		if ($type == 'css')
		{
			// Remove all comments
			$minified = preg_replace('#[\s\t\r\n]+/\*(.|[\r\n])*?\*/#', '', $minified);

			// Remove unnecessary white-space/new-line
			$minified = preg_replace('/\s+(\{|\})/', '\1', $minified);
			$minified = preg_replace('/([\{:;])\s+/', '\1', $minified);

			// Remove unnecessary semi-colon
			$minified = str_replace(';}/', '}', $minified);

			// Remove all remaining unnecessary white-space/new-line
			$minified = preg_replace('/\n\s*/', '', $minified);
		}

		return $minified;
	}

	/**
	 * Update HTML response body.
	 *
	 * @param   string  &$responseBody  Generated HTML response body.
	 * @param   string  $replacement    Tag to inject into response body.
	 * @param   array   $tags           Array of tags to remove from response body.
	 *
	 * @return  void
	 */
	protected static function updateResponseBody(&$responseBody, $replacement, $tags)
	{
		// Initialize variable
		$first = true;

		// Do replacement
		foreach ((array) $tags AS $tag)
		{
			if ($first)
			{
				// Replace old tag with new tag
				$responseBody = str_replace($tag, "\n\t{$replacement}", $responseBody);

				$first = false;
			}
			else
			{
				// Remove old tag from response body
				$responseBody = str_replace($tag, '', $responseBody);
			}
		}
	}
}
