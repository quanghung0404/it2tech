<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id$
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
class JFormFieldJSNMultipleShowlist extends JFormField
{
	public $type = 'JSNMultipleShowlist';

	public function __construct($form = null)
	{
		parent::__construct($form);

	}

	protected function getInput()
	{
		$_app		= JFactory::getApplication('admin');
		$_input 	= $_app->input;

		$pathOnly = JURI::root(true);
		$pathRoot = JURI::root();

		$enabledCSS = ' jsn-disable';
		$menuid		= $_input->getInt('id', 0);
		$app 		= JFactory::getApplication();
		$db  		= JFactory::getDBO();
		$doc 		= JFactory::getDocument();

		$doc->addStyleSheet($pathOnly . '/administrator/components/com_imageshow/assets/css/imageshow.css');
		$doc->addStyleSheet($pathOnly . '/administrator/components/com_imageshow/assets/css/menu.galleries.collection.css');

		JSNHtmlAsset::addScript($pathOnly . '/media/jui/js/jquery.min.js');
		JSNHtmlAsset::addScript(JSN_URL_ASSETS . '/3rd-party/jquery-ui/js/jquery-ui-1.9.0.custom.min.js');
		$doc->addScript($pathOnly . '/administrator/components/com_imageshow/assets/js/joomlashine/window.js');
		JSNHtmlAsset::addScript($pathOnly . '/administrator/components/com_imageshow/assets/js/joomlashine/jquery.safe.element.js');

		$jsCode = "
			var baseUrl = '".JURI::root()."';
			var gIframeFunc = undefined;
			(function($){
				$(document).ready(function () {
					var wWidth  = $(window).width()*0.9;
					var wHeight = $(window).height()*0.8;
					$('.jsn-is-showlist-modal').click(function(event){
						event.preventDefault();
						var link = baseUrl+'administrator/'+$(this).attr('href')+'&tmpl=component';
						var save_button_lable = '".JText::_('JSN_IMAGESHOW_SAVE_AND_SELECT', true)."';
						var JSNISShowlistWindow = new $.JSNISUIWindow(link,{
								width: wWidth,
								height: wHeight,
								title: '".JText::_('JSN_IMAGESHOW_SHOWLIST_SETTINGS')."',
								scrollContent: true,
								buttons:
								[{
									text:save_button_lable,
									class: 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
									click: function (){
										if(typeof gIframeFunc != 'undefined')
										{
											gIframeFunc();
										}
										else
										{
											console.log('Iframe function not available')
										}
									}
								},
								{
									text: '".JText::_('JSN_IMAGESHOW_CANCEL', true)."',
									class: 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
									click: function (){
										$(this).dialog('close');
									}
								},
								]
						});
					});
				});
			})((typeof JoomlaShine != 'undefined' && typeof JoomlaShine.jQuery != 'undefined') ? JoomlaShine.jQuery : jQuery);
		  ";

		$doc->addScriptDeclaration($jsCode);

		$query	= $db->getQuery(true);
		$query->clear();
		$query->select('a.showlist_title AS text, a.showlist_id AS id');
		$query->from($db->quoteName('#__imageshow_showlist') . ' AS a');
		$query->where('a.published = ' . $db->quote(1));
		$query->order('a.ordering ASC');
		$db->setQuery($query);

		$results = $db->loadObjectList();

		$html  = "<div id='jsn-showlist-container'>";

		if (!$results)
		{
			$html 	.= '<span class="jsn-menu-alert-message">'.JText::_('JSN_DO_NOT_HAVE_ANY_SHOWLIST').'</span>';
		}
		else
		{
			$html .= JHTML::_('select.genericList', $results, $this->name, 'class="inputbox jsn-select-value'.$enabledCSS.'" multiple="multiple"', 'id', 'text', $this->value, $this->id);
		}

		$html 		.= "<a class=\"jsn-is-showlist-modal\" href=\"index.php?option=com_imageshow&controller=showlist&task=add\" rel='{\"action\": \"add\"}' title=\"".JText::_('CREATE_NEW_SHOWLIST')."\"><i class=\"jsn-icon16 jsn-icon-plus\" id = \"showlist-icon-add\"></i></a>";
		$html 		.= "</div>";
		return $html;
	}
}
?>