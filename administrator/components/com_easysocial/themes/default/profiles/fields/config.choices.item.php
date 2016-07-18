<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<li data-fields-config-param-choice class="mt-10" data-id="<?php echo $id; ?>">
	<a class="fields-config-param-choice" data-fields-config-param-choice-drag href="javascript:void(0);" data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_PARAMS_CHOICES_DRAG_CHOICE', true ); ?>" data-placement="top" data-es-provide="tooltip"><i class="icon-es-drag"></i></a>
	<input class="form-control input-sm" type="text" data-fields-config-param-choice-title value="<?php echo $title; ?>" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_PARAMS_CHOICES_TITLE', true ); ?>" />
	<input class="form-control input-sm" type="text" data-fields-config-param-choice-value value="<?php echo $value; ?>" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_PARAMS_CHOICES_VALUE', true ); ?>" />
	<input type="hidden" data-fields-config-param-choice-default value="<?php echo $default; ?>" />
	<a href="javascript:void(0);" data-fields-config-param-choice-add data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_PARAMS_CHOICES_ADD_CHOICE', true ); ?>" data-placement="top" data-es-provide="tooltip"><i class="es-state-plus"></i></a>
	<a href="javascript:void(0);" data-fields-config-param-choice-remove data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_PARAMS_CHOICES_REMOVE_CHOICE', true ); ?>" data-placement="top" data-es-provide="tooltip"><i class="es-state-minus"></i></a>
	<a href="javascript:void(0);" data-fields-config-param-choice-setdefault data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_PARAMS_CHOICES_TOGGLE_DEFAULT_CHOICE', true ); ?>" data-placement="top" data-es-provide="tooltip"><i data-fields-config-param-choice-defaulticon class="<?php echo empty( $default ) ? 'es-state-default' : 'es-state-featured'; ?>"></i></a>
</li>
