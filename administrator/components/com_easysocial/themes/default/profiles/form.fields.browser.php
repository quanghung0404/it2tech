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
<div class="profile-field-browser" data-fields-browser>
    <div class="pa-10">
        <div >
            <h4><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_SELECT_FIELDS');?></h4>
            <em><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_SELECT_FIELDS_INFO');?></em>
            <hr/>
            <div>
                <div class="browserGroup" data-fields-browser-group-mandatory <?php echo !$coreAppsRemain ? 'style="display: none;"' : ''; ?>>
                    <h4><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_MANDATORY_FIELDS'); ?></h4>
                    <?php echo $this->includeTemplate('admin/profiles/form.fields.browser.list', array('core' => 1)); ?>
                </div>
            </div>
            <div>
                <div class="browserGroup" data-fields-browser-group-unique <?php echo !$uniqueAppsRemain ? 'style="display: none;"' : ''; ?>>
                    <h4><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_UNIQUE_FIELDS'); ?></h4>
                    <?php echo $this->includeTemplate('admin/profiles/form.fields.browser.list', array('core' => 0, 'unique' => 1)); ?>
                </div>
            </div>
            <div>
                <div class="browserGroup" data-fields-browser-group-standard>
                    <h4><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_STANDARD_FIELDS'); ?></h4>
                    <?php echo $this->includeTemplate('admin/profiles/form.fields.browser.list', array('core' => 0, 'unique' => 0)); ?>
                </div>
            </div>
        </div>
    </div>
</div>
