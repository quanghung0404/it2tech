<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2015 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Load stylesheet
JHtml::_('stylesheet', 'mod_rsform_feedback/style.css', array(), true);

// Define parameters
$open_in		= $params->get('open-in', 'same');
$modal			= $open_in == 'modal';
$new			= $open_in == 'new';
$sfx 			= $params->get('moduleclass_sfx');
$position 		= $params->get('position', 'left');
$bg_color 		= $params->get('bg-color', '#FFFFFF');
$border_color 	= $params->get('border-color', '#000000');
$text_color		= $params->get('text-color', '#000000');
$font_size		= $params->get('font-size', 14);
$text 			= htmlentities($params->get('string'), ENT_COMPAT, 'utf-8');
$Itemid			= $params->get('itemid');

// Build form URL
$parameters = array(
	'option' 	=> 'com_rsform',
	'formId'	=> $params->get('formId'),
);

if ($modal) {
	$parameters['tmpl'] = 'component';
}

if ($Itemid) {
	$parameters['Itemid'] = $Itemid;
}

$form_url = 'index.php?'.http_build_query($parameters);

// Setup the link attributes
$attribs = array(
	'class' => 'feedback-text'
);

if ($modal) {
	$attribs['class'] .= ' feedback-modal';
	$attribs['rel']	= htmlentities(json_encode(array(
		'handler' => 'iframe',
		'size' => array(
			'x' => (int) $params->get('modal_x', 660),
			'y' => (int) $params->get('modal_y', 475)
		)
	)));
}

if ($new) {
	$attribs['target'] = '_blank';
}

require JModuleHelper::getLayoutPath('mod_rsform_feedback');