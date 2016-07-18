<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: imageshow.php 17428 2012-10-25 04:29:38Z dinhln $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
class plgButtonImageShow extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}


	/**
	 * Display the button
	 *
	 * @return
	 */
	function onDisplay($name)
	{
		$js = "
		function jSelectSyntax(objShowlist, objShowcase, objWidth, objHeight, objDimension) {
			var dimension  = '';
			var width  	   = '';
			var height     = '';
			if (objWidth.value != '')
			{
				if (objDimension.value == '%')
				{
					dimension = '%';
				}
				width = ' w='+objWidth.value + dimension;
			}
			if (objHeight.value != '')
			{
				height = ' h='+objHeight.value;
			}
			var syntax = '{imageshow sl=' + objShowlist.value + ' sc=' + objShowcase.value + width + height + ' /}';
			jInsertEditorText(syntax, '".$name."');
			SqueezeBox.close();
		}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		JHtml::_('behavior.modal');

		$link = 'index.php?option=com_imageshow&amp;task=modal&amp;tmpl=component&amp;'.JSession::getFormToken().'=1';
		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('class', 'btn');
		$button->set('text', JText::_('PLG_EDITOR_JSN_IMAGESHOW_BUTTON_IMAGESHOW'));
		$button->set('name', 'picture');
		$button->set('options', "{handler: 'iframe', size: {x: 550, y: 380}}");

		return $button;
	}
}
