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
<form class="form-horizontal" name="pollsForm" id="pollsForm" data-polls-form>
<div class="panels panel-default es-polls" data-polls-edit>

	<div class="h5 es-polls-question-title">
	    <input type="text" placeholder="<?php echo JText::_('COM_EASYSOCIAL_POLLS_SET_A_TITLE');?>" class="form-control input-sm" name="title" value="<?php echo $poll->title; ?>" data-polls-title />
	</div>

	<div class="panel-body pl-0 pr-0">
        <div class="data-field-multitextbox" data-field-multitextbox="" data-max="0">
            <ul data-polls-list class="fd-reset-list ui-sortable ml-0 mb-10">

                <?php foreach($items as $item) { ?>
                <li data-polls-item data-id="<?php echo $item->id; ?>" class="data-field-multitextbox-item">
                    <div class="media">
                        <div class="media-body">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control"
                                       data-field-multitextbox-input
                                       data-polls-item-text
                                       name="items[]"
                                       value="<?php echo $item->value;?>"
                                       placeholder="<?php echo JText::_('COM_EASYSOCIAL_POLLS_ENTER_POLL_ITEM');?>">

								<span class="input-group-btn" data-polls-delete-btn>
                                    <button class="btn btn-es btn-del" type="button" data-polls-item-delete>×</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
                <?php } ?>

                <li data-polls-item-copied class="data-field-multitextbox-item" style="display:none;">
                    <div class="media">
                        <div class="media-body">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" data-field-multitextbox-input="" name="copied" value="" placeholder="<?php echo JText::_('COM_EASYSOCIAL_POLLS_ENTER_POLL_ITEM');?>">
								<span class="input-group-btn" data-polls-delete-btn>
                                    <button class="btn btn-es btn-del" type="button" data-polls-item-delete="">×</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <a href="javascript:void(0);" data-polls-add><?php echo JText::_('COM_EASYSOCIAL_POLLS_ADD_ITEM');?></a>
        </div>

        <div class="es-checkbox mb-15">
            <input type="checkbox" id="multiple" name="multiple" data-polls-multiple <?php echo ($poll->multiple) ? ' checked="checked"' : ''; ?> />
            <label for="allow-multiple-selection" class="option">
            	<?php echo JText::_('COM_EASYSOCIAL_POLLS_ALLOW_MULTIPLE_ITEM'); ?>
            </label>
        </div>

        <div id="datetimepicker4" class="input-group" data-polls-expirydate="" data-value="<?php echo ($poll->expiry_date != '' && $poll->expiry_date != '00-00-00 00:00:00') ? ES::date($poll->expiry_date)->toFormat('Y-m-d H:i:s') : '';?>">
            <input type="text" class="form-control input-sm" placeholder="<?php echo JText::_('COM_EASYSOCIAL_POLLS_EXPIRED_DATE'); ?>" data-picker />
            <input type="hidden" data-datetime />
            <span class="input-group-addon" data-picker-toggle>
                <i class="fa fa-calendar"></i>
            </span>
        </div>

        <input type="hidden" name="element" data-polls-element value="<?php echo $element; ?>" />
        <input type="hidden" name="uid" data-polls-uid value="<?php echo $uid; ?>" />
        <input type="hidden" name="pollid" data-polls-id value="<?php echo $poll->id; ?>" />
        <input type="hidden" name="itemsremoved" data-polls-tobe-removed value="" />
	</div>

</div>
<div class="es-stream-editor-actions clearfix">
    <a href="javascript:void(0);" class="btn btn-es-danger btn-sm pull-left" data-stream-polls-edit-cancel><?php echo JText::_('COM_EASYSOCIAL_STREAM_POLLS_CANCEL_BUTTON');?></a>
    <a href="javascript:void(0);" class="btn btn-es-primary btn-sm pull-right" data-stream-polls-edit-update>
        <i class="fa fa-floppy-o"></i>&nbsp; <?php echo JText::_('COM_EASYSOCIAL_STREAM_POLLS_UPDATE_BUTTON');?>
    </a>
</div>
</form>
