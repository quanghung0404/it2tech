<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-composer-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_POST_SHOW_IMAGE'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        
        <div class="eb-composer-field text-center">
            <div class="row">
                <div class="col-lg-6 col-lg-offset-3">
                    <?php echo $this->html('grid.boolean', 'show_image', true, 'show_image', 'data-post-option-image'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="eb-composer-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_POST_SHOW_INTRO'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-field text-center">
            <div class="row">
                <div class="col-lg-6 col-lg-offset-3">
                    <?php echo $this->html('grid.boolean', 'show_intro', true, 'show_intro', 'data-post-option-intro'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="eb-composer-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_POST_SHOW_LINK'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-field text-center">
            <div class="row">
                <div class="col-lg-6 col-lg-offset-3">
                    <?php echo $this->html('grid.boolean', 'show_link', true, 'show_link', 'data-post-option-link'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

