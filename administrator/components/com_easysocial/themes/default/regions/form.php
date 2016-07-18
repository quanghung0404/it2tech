<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form id="adminForm" method="post" action="index.php" data-form>
    <div class="panel">
        <div class="wbody wbody-padding">
            <div class="form-group">
                <label class="col-md-3">
                    <?php echo JText::_('COM_EASYSOCIAL_REGIONS_REGION_TYPE'); ?>
                    <i data-placement="bottom"
                        data-title="<?php echo JText::_('COM_EASYSOCIAL_REGIONS_REGION_TYPE');?>"
                        data-content="<?php echo JText::_('COM_EASYSOCIAL_REGIONS_REGION_TYPE_DESC'); ?>"
                        data-es-provide="popover" class="fa fa-question-circle pull-right"></i>
                </label>
                <div class="col-md-9">
                    <select name="type" data-type>
                        <option value=""><?php echo JText::_('COM_EASYSOCIAL_REGIONS_FORM_CHOOSE_TYPE'); ?></option>
                        <option value="country" <?php if ($region->type === 'country') { ?>selected="selected"<?php } ?>><?php echo JText::_('COM_EASYSOCIAL_REGIONS_FORM_TYPE_COUNTRY'); ?></option>
                        <option value="state" <?php if ($region->type === 'state') { ?>selected="selected"<?php } ?> data-parent="country"><?php echo JText::_('COM_EASYSOCIAL_REGIONS_FORM_TYPE_STATE'); ?></option>
                    </select>
                </div>
            </div>

            <div class="form-group" <?php if (empty($region->parent_type)) { ?>style="display:none;"<?php } ?> data-parent-base>
                <label class="col-md-3">
                    <?php echo JText::_('COM_EASYSOCIAL_REGIONS_FORM_CHOOSE_PARENT'); ?>
                    <i data-placement="bottom"
                        data-title="<?php echo JText::_('COM_EASYSOCIAL_REGIONS_FORM_CHOOSE_PARENT');?>"
                        data-content="<?php echo JText::_('COM_EASYSOCIAL_REGIONS_FORM_CHOOSE_PARENT_DESC'); ?>"
                        data-es-provide="popover" class="fa fa-question-circle pull-right"></i>
                </label>
                <div class="col-md-9" data-parent-content <?php if (!empty($parents)) { ?>data-loaded="1"<?php } ?>>
                    <?php if (empty($parents)) { ?>
                    <i class="fd-loading"></i>
                    <?php } else { ?>
                    <select name="parent_uid" data-parent-uid>
                    <?php foreach ($parents as $parent) { ?>
                        <option value="<?php echo $parent->uid; ?>" <?php if ($parent->uid == $region->parent_uid) { ?>selected="selected"<?php } ?>><?php echo $parent->name; ?></option>
                    <?php } ?>
                    </select>
                    <?php } ?>
                </div>
            </div>

            <?php if (FD::get('multisites')->exists()) { ?>
            <div class="form-group">
                <label class="col-md-3">
                    <?php echo JText::_('COM_EASYSOCIAL_REGION_FORM_SITE_ID');?>
                    <i class="fa fa-question-circle pull-right"
                        <?php echo $this->html('bootstrap.popover', JText::_('COM_EASYSOCIAL_REGION_FORM_SITE_ID' ) , JText::_('COM_EASYSOCIAL_REGION_FORM_SITE_ID_DESCRIPTION'), 'bottom'); ?>
                    ></i>
                </label>
                <div class="col-md-9"><?php echo FD::get('multisites')->getForm('site_id', $region->site_id); ?></div>
            </div>
            <?php } ?>

            <div class="form-group">
                <label class="col-md-3">
                    <?php echo JText::_('COM_EASYSOCIAL_REGIONS_REGION_NAME'); ?>
                    <i data-placement="bottom"
                        data-title="<?php echo JText::_('COM_EASYSOCIAL_REGIONS_REGION_NAME');?>"
                        data-content="<?php echo JText::_('COM_EASYSOCIAL_REGIONS_REGION_NAME_DESC'); ?>"
                        data-es-provide="popover" class="fa fa-question-circle pull-right"></i>
                </label>
                <div class="col-md-9">
                    <input type="text" name="name" class="form-control input-sm" value="<?php echo $region->name; ?>" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3">
                    <?php echo JText::_('COM_EASYSOCIAL_REGIONS_REGION_CODE'); ?>
                    <i data-placement="bottom"
                        data-title="<?php echo JText::_('COM_EASYSOCIAL_REGIONS_REGION_CODE');?>"
                        data-content="<?php echo JText::_('COM_EASYSOCIAL_REGIONS_REGION_CODE_DESC'); ?>"
                        data-es-provide="popover" class="fa fa-question-circle pull-right"></i>
                </label>
                <div class="col-md-9">
                    <input type="text" name="code" class="form-control input-sm" value="<?php echo $region->code; ?>" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3">
                    <?php echo JText::_('COM_EASYSOCIAL_REGIONS_REGION_STATE'); ?>
                    <i data-placement="bottom"
                        data-title="<?php echo JText::_('COM_EASYSOCIAL_REGIONS_REGION_STATE');?>"
                        data-content="<?php echo JText::_('COM_EASYSOCIAL_REGIONS_REGION_STATE_DESC'); ?>"
                        data-es-provide="popover" class="fa fa-question-circle pull-right"></i>
                </label>
                <div class="col-md-9">
                    <?php echo $this->html('grid.boolean', 'state', $region->state, '', 'data-state'); ?>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="parent_type" value="<?php echo $region->parent_type; ?>" data-parent-type />

    <?php echo JHTML::_('form.token'); ?>
    <input type="hidden" name="id" value="<?php echo $region->id; ?>" />
    <input type="hidden" name="option" value="com_easysocial" />
    <input type="hidden" name="view" value="regions" />
    <input type="hidden" name="controller" value="regions" />
    <input type="hidden" name="task" value="store" />
</form>
