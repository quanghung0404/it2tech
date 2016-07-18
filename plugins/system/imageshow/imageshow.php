<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: imageshow.php 16164 2012-09-19 09:27:21Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );
class plgSystemImageShow extends JPlugin
{
	var $_user  		= null;
	var $_application 	= null;

	function plgSystemImageShow(& $subject, $config)
	{
		$this->_user = JFactory::getUser();
		$this->_application = JFactory::getApplication();
		parent::__construct($subject, $config);
		$this->JSNDisablePHPErrMsg();
	}

	function JSNDisablePHPErrMsg()
	{
		$option = JRequest::getVar('option');
		if($this->_application->isAdmin() && $option == 'com_imageshow')
		{
			if (function_exists('error_reporting'))
			{
				error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
			}
		}
	}

	function onAfterDispatch()
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root(true).'/components/com_imageshow/assets/css/style.css');
	}

	function onAfterRender()
	{
		$document = JFactory::getDocument();
		if ($document instanceOf JDocumentHTML)
		{
			$template = $document->template;
			$content  = JResponse::getBody();
			if ($this->_application->isAdmin() && $this->_user->id > 0)
			{
				preg_match('/<body([^>]+)>/is', $content, $matches);
				$pos = strpos(@$matches[0], 'jsn-master');
				if (!$pos)
				{
					if(preg_match('/<body([^>]*)class\s*=\s*"([^"]+)"([^>]*)>/is', $content))
					{
						$content = preg_replace('/<body([^>]*)class\s*=\s*"([^"]+)"([^>]*)>/is', '<body\\1 class="jsn-master tmpl-'.$template.' \\2" \\3>', $content);
					}
					else
					{
						$content = preg_replace('/<body([^>]+)>/is', '<body\\1 class="jsn-master tmpl-'.$template.'">', $content);
					}
				}
				JResponse::setBody($content);
			}
		}
	}
}
?>