<?php
/**
 * @version    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Plugin button select slider in articles view
 *
 *
 * @since       1.6
 */
class plgButtonJSNEasySlider extends JPlugin
{

	/**
	 * Constructor
	 *
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Display the button
	 *
	 * @param   String  $name  Name button
	 *
	 * @return array A two element array of (imageName, textToInsert)
	 */
	function onDisplay($name)
	{
		$js = "
		  function jsnSelectSlider(objSlider, rand) {
				jInsertEditorText('{jsn_easyslider identity_id=' +  rand + ' slider_id=' + objSlider.val() + '/}', '" . $name . "');
				SqueezeBox.close();
		  }";
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
		$link = 'index.php?option=com_easyslider&amp;view=sliders&amp;layout=editors_layout&amp;tmpl=component';

		JHtml::_('behavior.modal');

		$button = new JObject;

		$button->set('modal', true);
		$button->set('class', 'btn');
		$button->set('link', $link);
		$button->set('text', JText::_('JSN_EASYSLIDER'));
		$button->set('name', 'picture');
		$button->set('options', "{handler: 'iframe', size: {x: 600, y: 250}}");

		return $button;
	}
}
