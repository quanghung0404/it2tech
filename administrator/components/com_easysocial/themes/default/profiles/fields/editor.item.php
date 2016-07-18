<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="form-group control-group-custom hover-panel" 
	data-fields-editor-page-item
	<?php if (isset($fieldid)) { ?>
	data-id="<?php echo $fieldid;?>"
	<?php } ?>
	data-appid="<?php echo $appid; ?>"
>
	<div data-fields-editor-page-item-handle class="item-handle"></div>

	<div class="custom-fields-control hover-panel-show">
		<a href="javascript:void(0);" class="pull-right btn btn-sm btn-es-danger" data-fields-editor-page-item-delete>
			<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_ITEM_DELETE' );?>
		</a>
		
		<a href="javascript:void(0);" class="mr-5 pull-right btn btn-sm btn-es" data-fields-editor-page-item-edit>
			<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_ITEM_EDIT' );?>
		</a>
		
		<a href="javascript:void(0);" class="mr-5 pull-right btn btn-sm btn-es" data-fields-editor-page-item-move>
			<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_ITEM_MOVE' );?>
		</a>

		<div class="pull- custom-label-app">
			<i class="<?php echo $app->getParams()->get( 'icon' , '' ) ? $app->getParams()->get( 'icon' ) : 'icon-field-' . $app->element;?>"></i>
			<span><?php echo $app->title; ?></span>
		</div>
	</div>

	<div data-fields-editor-page-item-content>
		<?php if ($app->id) { ?>
			<?php echo $output; ?>
		<?php } else { ?>
			<div class="alert alert-danger"><?php echo JText::_('COM_EASYSOCIAL_FIELDS_INVALID_APP'); ?></div>
		<?php } ?>
	</div>
</div>
