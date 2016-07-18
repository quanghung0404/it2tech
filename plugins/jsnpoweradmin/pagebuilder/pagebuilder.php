<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN PowerAdmin support for com_content
 * @version $Id$
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.plugin.plugin' );
include_once JPATH_ROOT . '/administrator/components/com_poweradmin/extensions/extensions.php';

class plgJsnpoweradminPageBuilder extends plgJsnpoweradminExtensions
{
	public function onJSNPAPBReplaceContent($content)
	{
		//Check if the component is not installed.
		$isInstalledComponent = $this->isInstalledComponent();
		if ($isInstalledComponent != null && $isInstalledComponent == '1')
		{
			$apiPath = JPATH_ROOT . '/administrator/components/com_pagebuilder/libraries/joomlashine/api/api.php';

			if (file_exists($apiPath))
			{
				include_once $apiPath;
				$objJSNPageBuilderAPI = new JSNPageBuilderAPI;
				$content = $objJSNPageBuilderAPI->process($content);
			}
			else
			{
				//Backward compatible for JSN PageBuilder < 1.0.5
				preg_match_all('#\[(\[?)(pb_row)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)#', $content, $out);
				
				if (count($out[0]))
				{
					$elemenPath = JPATH_ROOT . '/administrator/components/com_pagebuilder/libraries/innotheme/shortcode/element.php';
					$childPath	= JPATH_ROOT . '/administrator/components/com_pagebuilder/libraries/innotheme/shortcode/child.php';
					$parentPath	= JPATH_ROOT . '/administrator/components/com_pagebuilder/libraries/innotheme/shortcode/parent.php';
					
					if (file_exists($elemenPath) && file_exists($childPath))
					{
						include_once $elemenPath;
						if (file_exists($parentPath))
						{
							include_once $parentPath;
						}
						include_once $childPath;				
			
						// Autoload all helper classes.
						JSN_Loader::register(JPATH_ROOT . '/administrator/components/com_pagebuilder' , 'JSNPagebuilder');
						
						// Autoload all shortcode
						JSN_Loader::register(JPATH_ROOT . '/administrator/components/com_pagebuilder/helpers/shortcode' , 'JSNPBShortcode');

						// Backward compatible for all JSN PageBuilder version =< 1.0.4  
						if (is_dir(JPATH_ROOT . '/administrator/components/com_pagebuilder/elements'))
						{	
							JSN_Loader::register(JPATH_ROOT . '/administrator/components/com_pagebuilder/elements/' , 'JSNPBShortcode');
						}
						else 
						{
							JSN_Loader::register(JPATH_ROOT . '/plugins/jsnpagebuilder/defaultelements/' , 'JSNPBShortcode');
						}
						
						global $JSNPbElements;
						$pcontent = '';
						
						$this->addScript();
						$JSNPbElements							= new JSNPagebuilderHelpersElements();
						$objJSNPagebuilderHelpersBuilder 		= new JSNPagebuilderHelpersBuilder();
						$objJSNPagebuilderHelpersShortcode   	= new JSNPagebuilderHelpersShortcode();
						$content								= $objJSNPagebuilderHelpersShortcode::removeAutop($content);
						
						
						$pcontent .= $objJSNPagebuilderHelpersBuilder->generateShortCode($content);
						$content = '<div id="jsnpa-pagebuilder-form-container" class="jsn-layout">' . $pcontent . '</div>';
					}
				}
			}
		}	
		
		return $content;
	}
	
	public function onJSNPAPBCheckLayout($componentName, $view, $layout)
	{
		$isInstalledComponent = $this->isInstalledComponent();
		if ($isInstalledComponent != null && $isInstalledComponent == '1')
		{
			$layoutPath = JPATH_ROOT . '/plugins/jsnpoweradmin/pagebuilder/views/' . $componentName . '/views/' . $view . '/' . $layout . '.php';
			
			if ( file_exists( $layoutPath ) )
			{
				return true;
			}
		}
		return false;
	}
	
	protected function isInstalledComponent($component = 'com_pagebuilder')
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
		->select('enabled')
		->from('#__extensions')
		->where($db->quoteName('type') . ' = ' . $db->quote('component'))
		->where($db->quoteName('element') . ' = ' . $db->quote($component));	
		$db->setQuery($query);
		$isEnabled = $db->loadResult();
		return $isEnabled;
	}
	
	//Backward compatible for JSN PageBuilder < 1.0.5
	protected function addScript()
	{
		$jscode = $this->_javascript();
		if ($jscode != '')
		{
			$document	= JFactory::getDocument();
			$document->addScriptDeclaration($jscode);
		}		
	}
	
	public function onJSNPAPBAddScript($content)
	{
		//Check if the component is not installed.
		$isInstalledComponent = $this->isInstalledComponent();
		if ($isInstalledComponent != null && $isInstalledComponent == '1')
		{
			$apiPath = JPATH_ROOT . '/administrator/components/com_pagebuilder/libraries/joomlashine/api/api.php';
		
			if (file_exists($apiPath))
			{	
				include_once $apiPath;
				$objJSNPageBuilderAPI = new JSNPageBuilderAPI;
				$pattern = $objJSNPageBuilderAPI->getShortcodeSyntax();

				preg_match_all($pattern, $content, $out);
					
				if (count($out[0]))
				{
					$jscode = $objJSNPageBuilderAPI->_javascript();
					return $jscode;
				}
			}
			else
			{
				//Backward compatible for JSN PageBuilder < 1.0.5
				preg_match_all('#\[(\[?)(pb_row)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)#', $content, $out);
					
				if (count($out[0]))
				{
					$jscode = $this->_javascript();
					return $jscode;	
					
				}
			}
		}	
		return '';
	}

	//Backward compatible for JSN PageBuilder < 1.0.5
	private function _javascript()
	{
		$jscode = '(function($){ 
					$.JSNPAPageBuilder = function(options) {
						this.options  			= $.extend({}, options);
					
						this.initialize = function ()
						{
							var self = this;
							this.wrapper = $("#jsnpa-pagebuilder-form-container");
							this.maxWidth = this.wrapper.width() ;
							self.updateColumnWidth(self, this.wrapper, this.maxWidth);
						};
						
						this.updateColumnWidth = function(self, wapper, maxWidth)
						{
							wapper.find(".jsn-row-container").each(function() {
				                var countColumn = $(this).find(".jsn-column-container").length;                
				                self.updateColumnSpanWidth(countColumn, maxWidth, $(this));
				            });
				        };
				
				        // Update span width of columns in each row
				        this.updateColumnSpanWidth = function(countColumn, totalWidth, parentForm) {
				            var seperateWidth = countColumn * 12;
				            var remainWidth = totalWidth - seperateWidth;
				            parentForm.find(".jsn-column-container").each(function () {
				                var selfSpan = $(this).find(".jsn-column-content").attr("data-column-class").replace("span","");
				                var columnWidth = parseInt(selfSpan)*remainWidth/12;
				                if(columnWidth >= totalWidth) columnWidth = totalWidth - 12;
				                $(this).find(".jsn-column").css("width", columnWidth + "px");
				            });
				        };
					}
					
					$(window).load(function ()
					{
						var JSNPAPageBuilder 	= new $.JSNPAPageBuilder();
						JSNPAPageBuilder.initialize();	
					});

					$(document).ready(function ()
					{
						var JSNPAPageBuilder 	= new $.JSNPAPageBuilder();
						JSNPAPageBuilder.initialize();	
					});
				})((typeof JoomlaShine != "undefined" && typeof JoomlaShine.jQuery != "undefined") ? JoomlaShine.jQuery : jQuery);';
		return $jscode;			
	}
}