<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Helper class to generate Cookie Law for template
 *
 * @package     JSNTPLFramework
 * @subpackage  Template
 * @since       1.0.0
 */

// 		JSNTplTemplatePositionrender::renderPage(JURI::root() . 'index.php?tp=1&jsntpl_position=1');
// 		echo JSNTplTemplatePositionrender::getBody();

jimport('joomla.filesystem.file');
class JSNTplTemplatePositionrender
{
	private static $_contents = '';
	
	private static $_renderUrl = '';
	
	private static $_instance;
	
	private static $_isExternal = false;

	/**
	 * Return an instance of JSNTplTemplateCookielaw class.
	 *
	 * @return  JSNTplTemplateCookielaw
	 */
	public static function getInstance()
	{
		if (empty($url))
		{
			$url = JURI::root();
		}
		
		if ( ! isset(self::$instance))
		{
			self::$instance = new JSNTplTemplatePositionrender;
		}
	
		return self::$instance;
	}

	/**
	 * Set URL for get front-end content. Correct URL
	 *
	 * @param   string  $url  Link
	 *
	 * @return  void
	 */
	public static function setRenderUrl($url = '')
	{
		$uri = new JURI($url);
	
		if ($uri->getScheme() == '')
		{
			$scheme = 'http';
	
			if (@$_SERVER['HTTPS'])
			{
				$scheme = 'https';
			}
			
			$uri->setScheme($scheme);
		}
	
		@list($host, $port) = explode(':', $_SERVER['HTTP_HOST']);
	
		if ($uri->getHost() == '')
		{
			$uri->setHost($host);
		}
	
		if ($uri->getPort() == '')
		{
			$uri->setPort($port);
		}
	
		if (strtolower($uri->getHost()) != strtolower($host))
		{
			self::$_isExternal = true;
		}
		else
		{
			if (!$uri->hasVar('jsntpl_position'))
			{
				$uri->setVar('jsntpl_position', '1');
			}

			if (!$uri->hasVar('secret_key'))
			{
				$config 	= JFactory::getConfig();
				$secret 	= $config->get('secret');
				$uri->setVar('secret_key', md5($secret));
			}
			
			if ($uri->hasVar('Itemid') AND $uri->getVar('Itemid') == '')
			{
				$uri->delVar('Itemid');
			}
	
			self::$_renderUrl = $uri->toString();
		}

	}

	/**
	 * Check link render is internal/external.
	 *
	 * @return  boolean
	 */
	public static function isExternal()
	{
		return self::$_isExternal;
	}

	/**
	 * Using CURL get page source.
	 *
	 * @param   string  $url  Link to get content
	 *
	 * @return  string
	 */
	protected static function curlResponse($url)
	{
		$posts = array();
		$options = array(
				CURLOPT_RETURNTRANSFER	=> true,
				CURLOPT_HEADER			=> false,
				CURLOPT_FOLLOWLOCATION	=> true,
				CURLOPT_USERAGENT		=> "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13",
				CURLOPT_AUTOREFERER		=> true,
				CURLOPT_CONNECTTIMEOUT	=> 120,
				CURLOPT_TIMEOUT			=> 120,
				CURLOPT_MAXREDIRS		=> 10,
				CURLOPT_POSTFIELDS		=> $posts,
				CURLOPT_SSL_VERIFYPEER 	=> false
		);

		$ch = curl_init($url);
	
		curl_setopt_array($ch, $options);
	
		$contents	= curl_exec($ch);
		$err		= curl_errno($ch);
		$errmsg		= curl_error($ch);
		$header		= curl_getinfo($ch);

		curl_close($ch);
	
		if ($err > 0) {
			exit('cUrl error number: ' . $err);
		}
		
		$response = new stdClass;
		$response->contents			= $contents;
		$response->redirect_url		= $header['redirect_url'];

		if ($response->redirect_url != self::$_renderUrl AND trim($response->redirect_url) != '')
		{
			$old_uri		= new JURI(self::$_renderUrl);
			$redirect_uri	= new JURI($response->redirect_url);
	
			if ($old_uri->hasVar('tp') AND ! $redirect_uri->hasVar('tp'))
			{
				$redirect_uri->setVar('tp', 1);
			}
			
			if ($old_uri->hasVar('jsntpl_position') AND ! $redirect_uri->hasVar('jsntpl_position'))
			{
				$redirect_uri->setVar('jsntpl_position', 1);
			}
			
			if ($old_uri->hasVar('secret_key') AND ! $redirect_uri->hasVar('secret_key'))
			{
				$config 	= JFactory::getConfig();
				$secret 	= $config->get('secret');
				$redirect_uri->setVar('secret_key', md5($secret));
			}
								
			if ($old_uri->hasVar('Itemid') AND ! $redirect_uri->hasVar('Itemid'))
			{
				$redirect_uri->setVar('Itemid', $old_uri->getVar('Itemid'));
			}
	
			// Save redirect url
			self::$_renderUrl  = $redirect_uri->toString();
	
			$response	= self::curlResponse(self::$_renderUrl);
		}
	
		return $response;
	}
	
	protected static function getContents()
	{
		/** get contents of front-end page **/
		try
		{
			if (function_exists('curl_init'))
			{
				
				$response = self::curlResponse(self::$_renderUrl);
	
				// Parse front-end content
				list($head, $body) = explode('</head>', $response->contents);
				list($temp, $head) = explode('<head>', $head);
				list($body, $temp) = explode('</body>', $body);
	
				$head = preg_replace(
						array(
								'#<base\s+href="[^"]+"\s*/>#',
								'#<meta\s+[^>]+/>#',
								'#<title>[^\r\n]+</title>#'
						),
						'',
						$head
				);
	
				$body = preg_replace('#<body[^>]*>#', '', $body);
	
				self::$_contents = array(
						'head' => $head,
						'body' => $body
				);
			}
			else
			{
				throw new Exception(JText::_('JSN_EXTFW_ERROR_CURL_NOT_ENABLE'));
			}
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	/**
	 * Get html in <head> of front-end page.
	 *
	 * @return  array
	 */
	public static function getHeader()
	{

		return self::$_contents['head'];
	}
	
	/**
	 * Get html in <body> of front-end page.
	 *
	 * @return  string
	 */
	public static function getBody()
	{
		
		return self::$_contents['body'];
	}
	
	public static function renderPage($url = '')
	{
		self::setRenderUrl($url);
		
		
		if (!self::isExternal())
		{
			self::getContents();
		}
	}
	
	/**
	 * Only render positions and set data to joomla document.
	 *
	 * @return  void
	 */
	public static function renderEmptyModule()
	{
		$document = JFactory::getDocument();
		$positions	= self::getTemplatePositions();
		
		if ($positions == null)
		{
			/** if template not set load positions in index.php file **/
			@$positions = $this->_template->loadXMLPositions();
		}
	
		if (count($positions))
		{
			foreach ($positions AS $position)
			{
				if ($document->countModules($position->name))
				{
					$buffer  = JSNTplTemplateHelper::openTag('div', array('class' => "jsn-element-container_inner"));
					$buffer .= JSNTplTemplateHelper::openTag('div', array('class' => "jsn-position", 'id' => $position->name . '-jsnposition'));
					$buffer .= JSNTplTemplateHelper::openTag('p') . $position->name . JSNHtmlHelper::closeTag('p');
					$buffer .= JSNTplTemplateHelper::closeTag('div');
					$buffer .= JSNTplTemplateHelper::closeTag('div');
	
					$document->setBuffer($buffer, 'modules', $position->name);
				}
			}
		}
	}
	
	/**
	 *
	 * Only render empty component
	 */
	public static function renderEmptyComponent()
	{
		$document = JFactory::getDocument();
		
		$component = $document->getBuffer( 'component' );
		$component_buffer =  JSNTplTemplateHelper::openTag('div',  array('class'=>"jsn-component-container", 'id'=>"jsnrender-component"))
		.JSNTplTemplateHelper::openTag('p').$document->getTitle().JSNTplTemplateHelper::closeTag('p')
		.JSNTplTemplateHelper::closeTag('div');
		$document->setBuffer($component_buffer, 'component');
	}	
	/**
	 * Get includes in template
	 *
	 * @return  mixed
	 */
	public static function getTemplatePositions()
	{
		return self::defaultTemplate();
	}
	
	protected static function defaultTemplate()
	{
		$template = self::getDefaultTemplate();
		
		$client = JApplicationHelper::getClientInfo($template->client_id);
		$index_file_path = $client->path . '/templates/' . $template->element . '/index.php';
	
		if (file_exists($index_file_path))
		{
			$file_contents = JFile::read($index_file_path);
	
			if (preg_match_all('#<jdoc:include\ type="([^"]+)" (.*)\/>#iU', $file_contents, $matches))
			{
				$positions = array();
				$modules = $matches[2];
	
				foreach ($modules AS $module)
				{
					if ($module != "")
					{
						$params = explode(' ', $module);
						$position = new stdClass;
						$position->name = str_replace(array('name="', '"'), array('', ''), $params[0]);
						$position->params = array();
	
						if (count($params) > 1)
						{
							for ($i = 1; $i < count($params); $i++)
							{
								if ($params[$i] != '')
								{
									$tmp = explode('=', $params[$i]);		
									if (count($tmp) > 1)
									{
										$position->params[$tmp[0]] = str_replace('"', '', $tmp[1]);
									}
								}
							}
						}
	
							$positions[] = $position;
					}
				}
	
					return $positions;
			}
		}
	
		return null;
	}

	public static function getDefaultTemplate()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select("t.*");
		$query->from("#__extensions as t");
		$query->join("LEFT", "#__template_styles as s ON t.element = s.template");
		$query->where("s.client_id = 0 AND s.home = 1 AND t.type = 'template'");
		$db->setQuery($query);
	
		return $db->loadObject();
	}
	
	public static function enablePreviewMode()
	{
		$configs = array( 'template_positions_display' => 1 ); 
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("params");
		$query->from("#__extensions");
		$query->where("name = " . $db->quote( 'com_templates' ));
		$db->setQuery($query);
		$paramsString = $db->loadResult();
		
		if (!empty($paramsString))
		{
			$jParams =  new JRegistry();
			$jParams->loadObject(json_decode($paramsString));
			$params = $jParams->toArray();
			foreach($configs as $k => $val)
			{
				$params[$k] = (string) $val;
			}
		}
		else
		{
			$params = array();
			foreach($configs as $k => $val)
			{
				$params[$k] = (string) $val;
			}
		}	
			
		
		$query->clear();
		$query->select("extension_id");
		$query->from("#__extensions");
		$query->where("name = " . $db->quote('com_templates'));
		$db->setQuery($query);
		$extID = $db->loadResult();
		
		$query->clear();
		$query->update($db->quoteName('#__extensions'));
		$query->set($db->quoteName('params') . ' = ' . $db->quote((string) json_encode($params)));
		$query->where($db->quoteName('name') . ' = ' . $db->quote('com_templates'));
		$query->where($db->quoteName('extension_id') . ' = ' . $db->quote((int) $extID));
		$query->where($db->quoteName('type') . ' = ' . $db->quote('component'));
		$db->setQuery($query);
		$db->execute();
		
		return true;						
		
	}
}