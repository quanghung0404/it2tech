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
<div class="panels panel-default es-polls" data-polls>
	<form class="form-horizontal" name="pollsForm" id="pollsForm" data-polls-form>

        <div class="h5 es-polls-question-title">
            <!--Set a title for your poll-->
            <input type="text" placeholder="<?php echo JText::_('COM_EASYSOCIAL_POLLS_SET_A_TITLE');?>" class="form-control input-sm" name="title" value=""  data-polls-title />
        </div>

        <div class="panel-body pl-0 pr-0">
            <div class="data-field-multitextbox" data-field-multitextbox="" data-max="0">
                <ul data-polls-list class="fd-reset-list ui-sortable ml-0 mb-10">
                    <li data-polls-item class="data-field-multitextbox-item">
                        <div class="media">
                            <div class="media-body">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" data-field-multitextbox-input="" name="items[]" value="" data-polls-item-text placeholder="<?php echo JText::_('COM_EASYSOCIAL_POLLS_ENTER_POLL_ITEM');?>">
    								<span class="input-group-btn" data-polls-delete-btn>
                                        <button class="btn btn-es btn-del" type="button" data-polls-item-delete="">×</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </li>
                    
                    <li data-polls-item class="data-field-multitextbox-item">
                        <div class="media">
                            <div class="media-body">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" data-field-multitextbox-input="" name="items[]" value="" data-polls-item-text placeholder="<?php echo JText::_('COM_EASYSOCIAL_POLLS_ENTER_POLL_ITEM');?>">
    								<span class="input-group-btn" data-polls-delete-btn>
                                        <button class="btn btn-es btn-del" type="button" data-polls-item-delete="">×</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </li>
                    
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
                <input type="checkbox" id="allow-multiple-selection" name="multiple" data-polls-multiple>
                <label for="allow-multiple-selection" class="option">
                    <?php echo JText::_('COM_EASYSOCIAL_POLLS_ALLOW_MULTIPLE_ITEM'); ?>
                </label>
            </div>

            <div id="datetimepicker4" class="input-group" data-polls-expirydate="" data-value="">
                <input type="text" class="form-control input-sm" placeholder="<?php echo JText::_('COM_EASYSOCIAL_POLLS_EXPIRED_DATE'); ?>" data-picker />
                <input type="hidden" data-datetime />
                <span class="input-group-addon" data-picker-toggle>
                    <i class="fa fa-calendar"></i>
                </span>
            </div>
        </div>

        <input type="hidden" name="element" value="<?php echo $element; ?>" />
        <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
        <input type="hidden" name="group_source_id" value="<?php echo $cluster_id;?>" data-polls-sourceid />
	</form>
</div>
