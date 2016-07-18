<?php
/**
 * @version     3.1.x
 * @package     Simple Image Gallery Pro
 * @author      JoomlaWorks - http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license     http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
if (version_compare(JVERSION, '1.6.0', 'ge'))
{
	jimport('joomla.html.parameter');
}

class plgContentJw_sigpro extends JPlugin
{

	// JoomlaWorks reference parameters
	var $plg_name = "jw_sigpro";
	var $plg_tag = "gallery";
	var $plg_copyrights_start = "\n\n<!-- JoomlaWorks \"Simple Image Gallery Pro\" Plugin (v3.1.0) starts here -->\n";
	var $plg_copyrights_end = "\n<!-- JoomlaWorks \"Simple Image Gallery Pro\" Plugin (v3.1.0) ends here -->\n\n";

	function __construct(&$subject, $params)
	{
		parent::__construct($subject, $params);

		// Define the DS constant under Joomla! 3.0
		if (!defined('DS'))
		{
			define('DS', DIRECTORY_SEPARATOR);
		}
	}

	// Joomla! 1.5
	function onPrepareContent(&$row, &$params, $page = 0)
	{
		$this->renderSIGP($row, $params, $page = 0);
	}

	// Joomla! 1.6
	function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		$this->renderSIGP($row, $params, $page = 0);
	}

	// The main function
	function renderSIGP(&$row, &$params, $page = 0)
	{

		// API
		jimport('joomla.filesystem.file');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();

		// Requests
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$layout = JRequest::getCmd('layout');
		$page = JRequest::getCmd('page');
		$secid = JRequest::getInt('secid');
		$catid = JRequest::getInt('catid');
		$itemid = JRequest::getInt('Itemid');
		if (!$itemid)
			$itemid = 999999;

		// Dev requests
		$sigplt = JRequest::getCmd('sigplt');
		$sigppe = JRequest::getCmd('sigppe');
		$sigpw = JRequest::getInt('sigpw');
		$sigph = JRequest::getInt('sigph');

		// Assign paths
		$sitePath = JPATH_SITE;
		$siteUrl = JURI::root(true);
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$pluginLivePath = $siteUrl.'/plugins/content/'.$this->plg_name.'/'.$this->plg_name;
			$defaultImagePath = 'images';
		}
		else
		{
			$pluginLivePath = $siteUrl.'/plugins/content/'.$this->plg_name;
			$defaultImagePath = 'images/stories';
		}

		// Check if plugin is enabled
		if (JPluginHelper::isEnabled('content', $this->plg_name) == false)
			return;

		// Bail out if the page format is not what we want
		$allowedFormats = array('', 'html', 'feed', 'json');
		if (!in_array(JRequest::getCmd('format'), $allowedFormats))
			return;

		// Simple performance check to determine whether plugin should process further
		if (JString::strpos($row->text, $this->plg_tag) === false)
			return;

		// expression to search for
		$regex = "#{".$this->plg_tag."}(.*?){/".$this->plg_tag."}#s";

		// Find all instances of the plugin and put them in $matches
		preg_match_all($regex, $row->text, $matches);

		// Number of plugins
		$count = count($matches[0]);

		// Plugin only processes if there are any instances of the plugin in the text
		if (!$count)
			return;

		// Load the plugin language file the proper way
		JPlugin::loadLanguage('plg_content_'.$this->plg_name, JPATH_ADMINISTRATOR);

		// Check for basic requirements
		if (!extension_loaded('gd') && !function_exists('gd_info'))
		{
			JError::raiseNotice('', JText::_('JW_SIGP_PLG_GD_MISSING_NOTICE'));
			return;
		}
		if (!is_writable($sitePath.DS.'cache'))
		{
			JError::raiseNotice('', JText::_('JW_SIGP_PLG_CACHE_FOLDER_UNWRITABLE'));
			return;
		}

		// Check if Simple Image Gallery is present and enabled and prompt to disable
		if (JPluginHelper::isEnabled('content', 'jw_simpleImageGallery') == true)
		{
			JError::raiseNotice('', JText::_('JW_SIGP_PLG_SIGFREE_NOTICE'));
			return;
		}

		// ----------------------------------- Get plugin parameters -----------------------------------

		// Get plugin info
		$plugin = JPluginHelper::getPlugin('content', $this->plg_name);

		// Control external parameters and set variable for controlling plugin layout within modules
		if (!$params)
			$params = class_exists('JParameter') ? new JParameter(null) : new JRegistry(null);
		if(!is_object($params))
			$params = class_exists('JParameter') ? new JParameter($params) : new JRegistry($params);
		$parsedInModule = $params->get('parsedInModule');

		$pluginParams = JComponentHelper::getParams('com_sigpro');

		$galleries_rootfolder = ($params->get('galleries_rootfolder')) ? $params->get('galleries_rootfolder') : $pluginParams->get('galleries_rootfolder', $defaultImagePath);
		$popup_engine = $pluginParams->get('popup_engine', 'jquery_fancybox');
		$jQueryHandling = $pluginParams->get('jQueryHandling', 'googlecdn');
		$jQueryRelease = $pluginParams->get('jQueryRelease', '1.11');
		$thb_template = $pluginParams->get('thb_template', 'Classic');
		$thb_width = $pluginParams->get('thb_width', 200);
		$thb_height = $pluginParams->get('thb_height', 160);
		$smartResize = $pluginParams->get('smartResize', 1);
		$jpg_quality = $pluginParams->get('jpg_quality', 80);
		$singlethumbmode = $pluginParams->get('singlethumbmode', 0);
		$sortorder = $pluginParams->get('sortorder', '0');
		$showcaptions = $pluginParams->get('showcaptions', 1);
		$wordLimit = $pluginParams->get('wordlimit', 0);
		$enabledownload = $pluginParams->get('enabledownload', 1);
		$loadmoduleposition = $pluginParams->get('loadmoduleposition', '');
		$flickrApiKey = $pluginParams->get('flickrApiKey', '82a76fbf755902903859df58d1dd5934');
		$flickrImageCount = $pluginParams->get('flickrImageCount', 20);
		$resizeSrcImage = (int) $pluginParams->get('resizeSrcImage', 0);
		$cache_expire_time = $pluginParams->get('cache_expire_time', 120) * 60; // Cache expiration time in minutes
		// Advanced
		$memoryLimit = (int)$pluginParams->get('memoryLimit');
		if ($memoryLimit) ini_set("memory_limit", $memoryLimit."M");
		$debugMode = $pluginParams->get('debugMode', 1);
		if ($debugMode == 0){
			error_reporting(0); // Turn off all error reporting
		}

		// Cleanups
		// Remove first and last slash if they exist
		if (substr($galleries_rootfolder, 0, 1) == '/')
			$galleries_rootfolder = substr($galleries_rootfolder, 1);
		if (substr($galleries_rootfolder, -1, 1) == '/')
			$galleries_rootfolder = substr($galleries_rootfolder, 0, -1);

		// Includes
		require_once (dirname(__FILE__).DS.$this->plg_name.DS.'includes'.DS.'helper.php');

		// Other assignments
		$transparent = $pluginLivePath.'/includes/images/transparent.gif';
		$downloadFile = $enabledownload ? $pluginLivePath.'/includes/download.php' : NULL;
		$modulePosition = $loadmoduleposition ? htmlentities('<div class="sigProModulePosition">'.SimpleImageGalleryProHelper::loadModulePosition($loadmoduleposition, -1).'</div>', ENT_QUOTES, 'utf-8') : NULL;

		// When used with K2 extra fields
		if (!isset($row->title))
			$row->title = '';

		// Variable cleanups for K2
		if (JRequest::getCmd('format') == 'raw')
		{
			$this->plg_copyrights_start = '';
			$this->plg_copyrights_end = '';
		}

		// ----------------------------------- Prepare the output -----------------------------------

		// Process plugin tags
		if (preg_match_all($regex, $row->text, $matches, PREG_PATTERN_ORDER) > 0)
		{

			// start the replace loop
			foreach ($matches[0] as $key => $match)
			{

				$tagcontent = preg_replace("/{.+?}/", "", $match);

				if (strpos($tagcontent, 'www.flickr.com') === false)
				{
					/* example tag: {gallery}folder:200:80:1:2:jquery_colorbox:Galleria{/gallery} */
					$tagparams = explode(':', $tagcontent);
					$galleryFolder = $tagparams[0];
					$gal_width = (array_key_exists(1, $tagparams) && $tagparams[1] != '') ? $tagparams[1] : $thb_width;
					$gal_height = (array_key_exists(2, $tagparams) && $tagparams[2] != '') ? $tagparams[2] : $thb_height;
					$gal_singlethumbmode = (array_key_exists(3, $tagparams) && $tagparams[3] != '') ? $tagparams[3] : $singlethumbmode;
					$gal_captions = (array_key_exists(4, $tagparams) && $tagparams[4] != '') ? $tagparams[4] : $showcaptions;
					$gal_engine = (array_key_exists(5, $tagparams) && $tagparams[5] != '') ? $tagparams[5] : $popup_engine;
					$gal_template = (array_key_exists(6, $tagparams) && $tagparams[6] != '') ? $tagparams[6] : $thb_template;

					// Backwards compatibility
					if ($gal_template == 'Default') $gal_template = 'Classic';

					// Dev assignments
					if($sigplt) $gal_template = $sigplt;
					if($sigppe) $gal_engine = $sigppe;
					if($sigpw) $gal_width = $sigpw;
					if($sigph) $gal_height = $sigph;

					// HTML & CSS assignments
					if ($gal_singlethumbmode)
						$singleThumbClass = ' singleThumbGallery';
					else
						$singleThumbClass = '';

					// Normalize the source image folder
					if(strpos($galleryFolder,'media/jw_sigpro/users')!==false){
						$srcimgfolder = substr($galleryFolder, 1);
					} else {
						$srcimgfolder = $galleries_rootfolder.'/'.$galleryFolder;
					}

					// Create a unique gallery ID
					$gal_id = substr(md5($key.$srcimgfolder), 1, 10);

					$flickrSetUrl = null;

					// Render the gallery
					$gallery = SimpleImageGalleryProHelper::renderGallery($srcimgfolder, $gal_width, $gal_height, $smartResize, $jpg_quality, $sortorder, $gal_captions, $row->title, $wordLimit, $cache_expire_time, $downloadFile, $gal_id, $resizeSrcImage);

					if (!$gallery)
					{
						JError::raiseNotice('', JText::_('JW_SIGP_PLG_GALLERY_RENDER_PROBLEM').' '.$srcimgfolder);
						continue;
					}

				}
				else
				{
					// Make sure we got PHP5
					if (version_compare(PHP_VERSION, '5.0.0', '<'))
					{
						JError::raiseNotice('', JText::_('JW_SIGP_PLG_PHP5_REQUIRED'));
						continue;
					}

					// Get the Flickr set
					/* example tag: {gallery}http://www.flickr.com/photos/joomlaworks/sets/72157626907305094/:20:200:80:1:2:jquery_colorbox:Galleria{/gallery} */

					if (substr($tagcontent, 0, 4) != 'http') $tagcontent = 'http://'.$tagcontent;
					$tempFlickrParams = explode('://', $tagcontent); // remove the protocol so it doesn't mess with the produced param array
					$flickrParams = explode(':', $tempFlickrParams[1]);
					$flickrSetUrl = 'https://'.$flickrParams[0]; // re-insert the protocol
					$gal_count = (array_key_exists(1, $flickrParams) && $flickrParams[1] != '') ? $flickrParams[1] : $flickrImageCount;
					$gal_width = (array_key_exists(2, $flickrParams) && $flickrParams[2] != '') ? $flickrParams[2] : $thb_width;
					$gal_height = (array_key_exists(3, $flickrParams) && $flickrParams[3] != '') ? $flickrParams[3] : $thb_height;
					$gal_singlethumbmode = (array_key_exists(4, $flickrParams) && $flickrParams[4] != '') ? $flickrParams[4] : $singlethumbmode;
					$gal_captions = (array_key_exists(5, $flickrParams) && $flickrParams[5] != '') ? $flickrParams[5] : $showcaptions;
					$gal_engine = (array_key_exists(6, $flickrParams) && $flickrParams[6] != '') ? $flickrParams[6] : $popup_engine;
					$gal_template = (array_key_exists(7, $flickrParams) && $flickrParams[7] != '') ? $flickrParams[7] : $thb_template;

					// Backwards compatibility
					if ($gal_template == 'Default') $gal_template = 'Classic';

					// Dev assignments
					if($sigplt) $gal_template = $sigplt;
					if($sigppe) $gal_engine = $sigppe;
					if($sigpw) $gal_width = $sigpw;
					if($sigph) $gal_height = $sigph;

					// Get Flickr required stuff
					$flickrRegex = "#flickr.com/photos/(.*?)/(sets|albums)/(.*)/?#s";

					if (preg_match_all($flickrRegex, $flickrSetUrl, $flickrMatches, PREG_PATTERN_ORDER) > 0)
					{
						$flickrUsername = $flickrMatches[1][0];
						$flickrSetId = $flickrMatches[3][0];
						if (substr($flickrSetId, -1)=='/')
							$flickrSetId = substr($flickrSetId, 0, -1);

						$flickrJson = 'https://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&photoset_id='.$flickrSetId.'&format=json&media=photos&per_page='.$gal_count.'&api_key='.$flickrApiKey.'&nojsoncallback=1&extras=date_upload,date_taken,owner_name,original_format,last_update,tags,o_dims,views,media,path_alias,url_sq,url_t,url_s,url_m,url_o';

						$getFlickrJson = SimpleImageGalleryProHelper::readFile($flickrJson, 'jw_sigpro');
						$getFlickrData = json_decode($getFlickrJson);
						if (is_null($getFlickrData))
							continue;

						$flickrSetTitle = $getFlickrData->photoset->title;

						// Initiate array to hold gallery
						$gallery = array();
						$galleryData = @$getFlickrData->photoset->photo;

						if (!count($galleryData))
						{
							JError::raiseNotice('', JText::_('JW_SIGP_PLG_FLICKR_PHOTOSET_NOT_AVAILABLE'));
							continue;
						}

						if(!is_array($galleryData))
						{
							$galleryData = array($galleryData);
						}

						foreach ($galleryData as $key => $photo)
						{

							$gallery[$key] = new stdClass;

							// Caption display
							if ($gal_captions == 2)
							{
								$gallery[$key]->captionTitle = $photo->title;
								if (!$photo->description)
									$photo->description = $photo->title;
								$gallery[$key]->captionDescription = $photo->description.' - '.JText::_('JW_SIGP_LABELS_11').' <a href="'.$flickrSetUrl.'">'.$flickrSetTitle.'</a> '.JText::_('JW_SIGP_LABELS_12').' <a target="_blank" href="http://www.flickr.com/photos/'.$flickrUsername.'">'.$flickrUsername.'</a>';
							}
							elseif ($gal_captions == 1)
							{
								$gallery[$key]->captionTitle = JText::_('JW_SIGP_LABELS_09');
								$gallery[$key]->captionDescription = JText::_('JW_SIGP_LABELS_10').' <a target="_blank" href="'.$flickrSetUrl.'">'.$flickrSetTitle.'</a> '.JText::_('JW_SIGP_LABELS_12').' <a target="_blank" href="http://www.flickr.com/photos/'.$flickrUsername.'">'.$flickrUsername.'</a>';
							}
							else
							{
								$gallery[$key]->captionTitle = '';
								$gallery[$key]->captionDescription = '';
							}

							$gallery[$key]->captionTitle = htmlentities(strip_tags($gallery[$key]->captionTitle), ENT_QUOTES, 'utf-8');
							if ($wordLimit)
							{
								$gallery[$key]->captionTitle = SimpleImageGalleryProHelper::wordLimit($gallery[$key]->captionTitle, $wordLimit);
							}

							$gallery[$key]->captionDescription = htmlentities($gallery[$key]->captionDescription, ENT_QUOTES, 'utf-8');

							if (!isset($photo->url_o))
								$photo->url_o = $photo->url_m;

							if ($downloadFile)
							{
								$gallery[$key]->downloadLink = SimpleImageGalleryProHelper::replaceHtml('<br /><a class="sigProDownloadLink" target="_blank" href="'.$photo->url_o.'" download>'.JText::_('JW_SIGP_LABELS_13').'</a>');
							}
							else
							{
								$gallery[$key]->downloadLink = '';
							}

							$tempFlickrFilename = array_slice(explode('/', substr($photo->url_m, 0, -4).'_b.jpg'), -1);
							$gallery[$key]->filename = $tempFlickrFilename[0];
							$gallery[$key]->sourceImageFilePath = substr($photo->url_m, 0, -4).'_b.jpg';
							if($resizeSrcImage){
								$gallery[$key]->sourceImageFilePath = '//ir0.mobify.com/'.$resizeSrcImage.'/'.$gallery[$key]->sourceImageFilePath;
							}
							$gallery[$key]->thumbImageFilePath = $photo->url_s;
							$gallery[$key]->width = $gal_width;
							$gallery[$key]->height = $gal_height;
						}

						// HTML & CSS assignments
						if ($gal_singlethumbmode)
							$singleThumbClass = ' singleThumbGallery';
						else
							$singleThumbClass = '';
						$gal_id = substr(md5($key.$flickrSetId), 1, 10);

					}
					else
					{
						JError::raiseNotice('', JText::_('JW_SIGP_PLG_GALLERY_RENDER_PROBLEM'));
						continue;
					}

				}

				// JS & CSS includes: Append head includes, but not when we're outputing raw content (like in K2)
				if (JRequest::getCmd('format') == '' || JRequest::getCmd('format') == 'html')
				{

					// Initiate variables
					$relName = '';
					$extraClass = '';
					$extraWrapperClass = '';
					$legacyHeadIncludes = '';
					$customLinkAttributes = '';

					$popupPath = "{$pluginLivePath}/includes/js/{$gal_engine}";
					$popupRequire = dirname(__FILE__).DS.$this->plg_name.DS.'includes'.DS.'js'.DS.$gal_engine.DS.'popup.php';

					if (file_exists($popupRequire) && is_readable($popupRequire))
					{
						require ($popupRequire);
					}

					// JS
					if (version_compare(JVERSION, '1.6.0', 'ge'))
					{
						JHtml::_('behavior.framework');
					}
					else
					{
						JHTML::_('behavior.mootools');
					}

					if (version_compare(JVERSION, '3.0.0', 'ge')){
						JHtml::_('jquery.framework');
					} else {
						if(strpos($jQueryHandling, '1.')!==false) $jQueryHandling = 'googlecdn'; // Fallback fix for SIGPro versions before 3.0.7
						if ($jQueryHandling){
							$jQueryMinorReleaseMapping = array(
								'1.7' => '1.7.2',
								'1.8' => '1.8.3',
								'1.9' => '1.9.1',
								'1.10' => '1.10.2',
								'1.11' => '1.11.3'
							);
							$jQueryMinorRelease = $jQueryMinorReleaseMapping[$jQueryRelease];
							$jQueryLocation = array(
								'local'		 	=> $pluginLivePath.'/includes/jquery/jquery-'.$jQueryMinorRelease.'.min.js',
								'jquerycdn' 	=> '//code.jquery.com/jquery-'.$jQueryMinorRelease.'.min.js',
								'googlecdn' 	=> '//ajax.googleapis.com/ajax/libs/jquery/'.$jQueryMinorRelease.'/jquery.min.js',
								'mscdn' 		=> '//ajax.aspnetcdn.com/ajax/jQuery/jquery-'.$jQueryMinorRelease.'.min.js',
								'yandexcdn' 	=> '//yastatic.net/jquery/'.$jQueryMinorRelease.'/jquery.min.js',
								'cdnjs'			=> '//cdnjs.cloudflare.com/ajax/libs/jquery/'.$jQueryMinorRelease.'/jquery.min.js',
								'jsdelivr' 		=> '//cdn.jsdelivr.net/jquery/'.$jQueryMinorRelease.'/jquery.min.js',
								'qihoo360' 		=> 'http://ajax.useso.com/ajax/libs/jquery/'.$jQueryMinorRelease.'/jquery.min.js'
							);
							$jQueryURL = $jQueryLocation[$jQueryHandling];
							$document->addScript($jQueryURL);
						}

					}

					if (count($scripts))
					{
						foreach ($scripts as $script)
						{
							if (substr($script, 0, 4) == 'http' || substr($script, 0, 2) == '//')
							{
								$document->addScript($script);
							}
							else
							{
								$document->addScript($popupPath.'/'.$script);
							}
						}
					}
					if (count($scriptDeclarations))
						foreach ($scriptDeclarations as $scriptDeclaration)
							$document->addScriptDeclaration($scriptDeclaration);

					// CSS
					if (count($stylesheets))
						foreach ($stylesheets as $stylesheet)
							$document->addStyleSheet($popupPath.'/'.$stylesheet);
					if (count($stylesheetDeclarations))
						foreach ($stylesheetDeclarations as $stylesheetDeclaration)
							$document->addStyleDeclaration($stylesheetDeclaration);

					// Other
					if ($legacyHeadIncludes)
						$document->addCustomTag($this->plg_copyrights_start.$legacyHeadIncludes.$this->plg_copyrights_end);

					if ($extraClass)
						$extraClass = ' '.$extraClass;

					if ($extraWrapperClass)
						$extraWrapperClass = ' '.$extraWrapperClass;

					if ($customLinkAttributes)
						$customLinkAttributes = ' '.$customLinkAttributes;

					$pluginCSS = SimpleImageGalleryProHelper::getTemplatePath($this->plg_name, 'css/template.css', $gal_template);
					$pluginCSS = $pluginCSS->http;
					$document->addStyleSheet($pluginCSS, 'text/css', 'screen');

					// Print CSS
					$document->addStyleSheet($pluginLivePath.'/includes/css/print.css', 'text/css', 'print');

					// Message to show when printing an article/item with a gallery
					$websiteURL = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https://".$_SERVER['HTTP_HOST'] : "http://".$_SERVER['HTTP_HOST'];
					$itemPrintURL = $websiteURL.$_SERVER['REQUEST_URI'];
					$itemPrintURL = explode("#", $itemPrintURL);
					$itemPrintURL = $itemPrintURL[0].'#sigProId'.$gal_id;
				}
				else
				{
					$itemPrintURL = false;
				}

				// Fetch the template
				ob_start();
				$templatePath = SimpleImageGalleryProHelper::getTemplatePath($this->plg_name, 'default.php', $gal_template);
				$templatePath = $templatePath->file;
				include ($templatePath);
				$getTemplate = $this->plg_copyrights_start.ob_get_contents().$this->plg_copyrights_end;
				ob_end_clean();

				// Output
				$plg_html = $getTemplate;

				// Do the replace
				$row->text = preg_replace("#{".$this->plg_tag."}".$tagcontent."{/".$this->plg_tag."}#s", $plg_html, $row->text);

			}// end foreach

			// Global head includes
			if (JRequest::getCmd('format') == '' || JRequest::getCmd('format') == 'html')
			{
				$document->addScript($pluginLivePath.'/includes/js/behaviour.js');
			}

		} // end if

	} // END FUNCTION

} // END CLASS
