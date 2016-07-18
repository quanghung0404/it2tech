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
<div class="eb-composer-viewport" data-scrolly="y">
    <div class="eb-composer-viewport-content" data-scrolly-viewport>
        <div class="eb-composer-artboard<?php echo !empty($post->image) ? " show-cover active" : ""; ?>" data-eb-composer-artboard>

            <?php echo $this->output('site/composer/page'); ?>
            <div class="eb-composer-artboard-viewport">
                <div class="eb-composer-toolbar-group row-table hidden" style="height: auto; position: fixed; top: 50px; left: 0; z-index: 5;">
                    <div class="eb-composer-toolbar-item is-button col-cell eb-document-add-cover-button <?php echo $post->hasImage() ? ' has-cover' : '';?>" data-eb-composer-meta-button data-id="cover">
                        <i class="fa fa-image"></i>
                        <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_ADD_COVER');?></span>
                        <span style="display: none;"><?php echo JText::_('COM_EASYBLOG_COMPOSER_COVER');?></span>
                    </div>

                    <?php if ($this->config->get('main_locations') && EB::location($this->config->get('location_service_provider'))->isSettingsComplete()) { ?>
                    <div class="eb-composer-toolbar-item is-button col-cell eb-document-add-location-button <?php echo $post->hasLocation() ? ' has-location' : '';?>" data-eb-composer-meta-button data-id="location">
                        <i class="fa fa-map-marker"></i>
                        <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_ADD_LOCATION');?></span>
                        <span style="display: none"><?php echo JText::_('COM_EASYBLOG_COMPOSER_LOCATION');?></span>
                    </div>
                    <?php } ?>
                </div>
                <?php echo $this->output('site/composer/document/artboard/cover'); ?>
                <?php echo $this->output('site/composer/document/artboard/location'); ?>
            </div>

        </div>
    </div>
</div>
