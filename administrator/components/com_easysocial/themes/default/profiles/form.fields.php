<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="profileFieldForm" data-id="<?php echo $id; ?>">
    <div class="row row-sm">
        <div class="col-lg-3 col-md-12">
            <div class="profile-notice row-table">
                <div class="col-cell" style="vertical-align: top; padding: 2px 0 0; width: 25px;"><i class="fa fa-info-circle"></i></div>

                <div class="col-cell">
                    <?php if (isset($formType)) { ?>
                        <?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_INFO_' . $formType);?>
                    <?php } else { ?>
                        <?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_INFO');?>
                    <?php } ?>
                </div>
            </div>

            <ul class="stepList profile-fields-flow" data-fields-step>
                <?php $pageNumber = 0; ?>

                <?php foreach ($steps as $step) { ?>
                    <?php $pageNumber++; ?>
                    <?php echo $this->includeTemplate('admin/profiles/fields/step.item', array('step' => $step, 'pageNumber' => $pageNumber)); ?>
                <?php } ?>

                <li data-fields-step-add class="adding">
                    <a href="javascript:void(0);" class="step-add">
                        <i class="ies-plus ies-small"></i>
                        <?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_ADD_NEW_STEP');?>
                    </a>
                </li>
            </ul>
        </div>

        <div class="col-lg-5 col-sm-7">
            <div class="profile-fields" data-fields-wrap>
                <div class="profile-field-step tab-content" data-fields-editor>
                    <?php $pageNumber = 0; ?>

                    <?php foreach ($steps as $step) { ?>
                        <?php $pageNumber++; ?>
                        <?php echo $this->includeTemplate('admin/profiles/fields/editor.page', array('step' => $step, 'pageNumber' => $pageNumber)); ?>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-sm-5" style="position: relative">
            <div style="position: absolute; top: 0;">
                <div class="profile-field-browser" data-fields-browser>
                    <div >
                        <h4><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_SELECT_FIELDS');?></h4>
                        <div><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_SELECT_FIELDS_INFO');?></div>
                        <hr />

                        <div class="fields-mandatory">
                            <div class="browserGroup" data-fields-browser-group-mandatory style="<?php echo !$coreAppsRemain ? 'display:none;' : '';?>">
                                <h4><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_MANDATORY_FIELDS'); ?></h4>
                                <?php echo $this->includeTemplate('admin/profiles/fields/browser.list', array('core' => 1)); ?>
                            </div>
                        </div>

                        <div class="fields-unique">
                            <div class="browserGroup" data-fields-browser-group-unique style="<?php echo !$uniqueAppsRemain ? 'display:none;' : '';?>">
                                <h4><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_UNIQUE_FIELDS'); ?></h4>
                                <?php echo $this->includeTemplate('admin/profiles/fields/browser.list', array('core' => 0, 'unique' => 1)); ?>
                            </div>
                        </div>

                        <div class="fields-standard">
                            <div class="browserGroup" data-fields-browser-group-standard>
                                <h4><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_STANDARD_FIELDS'); ?></h4>
                                <?php echo $this->includeTemplate('admin/profiles/fields/browser.list', array('core' => 0, 'unique' => 0)); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>







	

	

	<input type="hidden" name="fields" value="" data-fields-save />
</div>
